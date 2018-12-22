<?php

// This functionality gets its own class, to make it easier to unit-test. [Jon Aquino 2007-08-24]

/**
 * Sends an email to all members of a group.
 */
class Groups_BroadcastHelper {

    /** Label for all group-broadcast profile sets */
    const GROUP_BROADCAST_LABEL = 'xg_group_broadcast';

    /**
     * Sends a message to all members of a group.
     * The return values may be inaccurate, but the second return value
     * (the number of emails remaining to be sent)
     * will definitely be 0 when the broadcast is complete.
     *
     * @param $group XN_Content|W_Content  the Group object
     * @param $subject string  subject for the email
     * @param $body string  body text for the email
     * @param $counter integer  zero-based counter, incremented on each call to this function during a broadcast
     * @return array  the number of emails sent, and the number of emails remaining to be sent
     */
    public function broadcast($group, $subject, $body, $counter) {
        // If the group's broadcast Profile Set does not yet exist, create it now and
        // add the group's members to it. If counter > 1, we are iterating over the users
        // to build the Profile Set. [Jon Aquino 2007-08-24]
        if ($counter > 1 || ! $this->loadProfileSet($group)) {
            $profileSet = $this->loadOrCreateProfileSet($group);
            $changed = 0;
            $start = $counter * $this->userUpgradeLimit();
            $end = $start + $this->userUpgradeLimit();
            $userInfo = $this->findUsers($group, $start, $end);
            foreach ($userInfo['users'] as $user) {
                //  If the user has site broadcast messages turned on, add him to the alias
                if ($this->acceptingBroadcastsHook($user)) {
                    $profileSet->addMembers($user->contributorName);
                }
                $changed++;
            }
            $contentRemaining = max(0, ($userInfo['numUsers'] - $end));
        } else {
            //  No need to iterate - just send the message
            $changed = 1;
            $contentRemaining = 0;
        }
        // If we've finished building the alias, send the message
        if ($contentRemaining == 0) { $this->broadcastProper($group, $subject, $body); }
        return array($changed, $contentRemaining);
    }

    // Protected functions below are called by unit tests [Jon Aquino 2007-08-24]

    /**
     * Sends the message.
     *
     * @param $group XN_Content|W_Content  the Group object
     * @param $subject string  subject for the email
     * @param $body string  body text for the email
     */
    protected function broadcastProper($group, $subject, $body) {
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $msg = new XG_Message_Group_Broadcast(trim($subject), trim($body), XN_Profile::current(), $group);
        $msg->send(self::profileSetId($group->id) . '@lists');
        //  BAZ-7299 Send the message to whoever sent the group message too (the default notification excludes the sender)
        $msg->send(XN_Profile::current()->screenName, XG_Message::siteReturnAddress());
    }

    /**
     * Returns whether the user has opted into group broadcasts.
     *
     * @param XN_Content|W_Content User  the User object
     * @return boolean  whether they have chosen to receive group-broadcast email
     */
    protected function acceptingBroadcastsHook($user) {
        return self::acceptingBroadcasts($user);
    }

    /**
     * Returns whether the user has opted into group broadcasts.
     *
     * @param XN_Content|W_Content User  the User object
     * @return boolean  whether they have chosen to receive group-broadcast email
     */
    public static function acceptingBroadcasts($user) {
        if ($user->my->emailGroupBroadcastPref) {
            return $user->my->emailGroupBroadcastPref != 'N';
        } else {
            return $user->my->emailNewMessagePref != 'N';
        }
    }

     /**
     * Queries users.
     *
     * @param $group XN_Content|W_Content  the Group object
     * @param $start integer  zero-based inclusive start index
     * @param $end integer  zero-based exclusive end index
     * @return array A two element array: 'users' => the requested users
     *         'numUsers' => the total number of users that match
     */
    protected function findUsers($group, $start, $end) {
        return User::find(array('my.' . XG_App::widgetAttributeName(W_Cache::getWidget('groups'), 'groups') => array('likeic', $group->id)), $start, $end, null, null, true);
    }

    /**
     * Returns the broadcast profile set for the given group.
     *
     * @param $group XN_Content|W_Content  the Group object
     * @return XN_ProfileSet|boolean  the profile set, or false if it does not exist
     */
    protected function loadProfileSet($group) {
        return XN_ProfileSet::load(self::profileSetId($group->id));
    }

    /**
     * Returns the broadcast profile set for the given group, creating one
     * if necessary.
     *
     * @param $group XN_Content|W_Content|string  the Group object or ID
     * @return XN_ProfileSet  the profile set
     */
    public function loadOrCreateProfileSet($group) {
        $groupId = (is_string($group) ? $group : $group->id);
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        return XN_ProfileSet::loadOrCreate(self::profileSetId($groupId),
                array(Index_NotificationHelper::groupLabel($groupId), self::GROUP_BROADCAST_LABEL));
    }

    /**
     * Returns the Profile Set ID for broadcast notifications for the given Group.
     *
     * @param $groupId string  the ID of the Group object
     * @return string  the alias
     */
    public static function profileSetId($groupId) {
        return 'xg_group_broadcast_' . strtr($groupId, ':', '_');
    }

    /**
     * Returns the number of users to upgrade on each iteration.
     *
     * @return integer  the number of users to process at a time
     */
    protected function userUpgradeLimit() {
        return 40;
    }

}
