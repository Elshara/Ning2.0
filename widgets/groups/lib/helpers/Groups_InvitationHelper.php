<?php

/**
 * Utility functions for working with group invitations
 */
class Groups_InvitationHelper {

    /**
     *  See XG_MessageHelper::getDefaultMessageParts() for details.
     *
     *  @param      $group	object	Group
     *  @return     hash
     */
    public function getMessageParts($group) {
        $messageParts = XG_MessageHelper::getDefaultMessageParts();
        if (XN_Profile::current()->screenName == $group->contributorName) {
            $messageParts[xg_html('GROUP_DESCRIPTION')] = $group->description;
            $messageParts[xg_html('GROUP_TITLE')] = $group->title;
        }
        return $messageParts;
    }

    /**
     * Invites someone to the group.
     *
     * @param $group XN_Content|W_Content  the Group object
     * @param $emailAddress string  the e-mail address of the invitee
     */
    public static function sendGroupInvitation($group, $emailAddress) {
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $invitation = Index_InvitationHelper::createInvitation($emailAddress, null, self::groupInvitationLabel($group->id));
        $profile = XG_Cache::profiles($emailAddress);
        $user = User::load($profile);
        $message = new XG_Message_Group_Invitation(array(
                'subject' => xg_text('COME_JOIN_ME_ON_X_ON_Y', $group->title, XN_Application::load()->name),
                'body' => null,
                'url' => XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id, Index_InvitationHelper::KEY => $invitation->id))));
        $message->send($emailAddress, XN_Profile::current()->screenName, $group);
        self::addGroupInviting($user, $group->id, XN_Profile::current()->screenName);
        $user->save();
    }

    /**
     * Returns metadata for the Groups to which the user has been invited.
     * Each Group is represented by:
     *     id - content ID of the Group
     *     inviter - screen name of the user who sent the invitation
     *
     * @param XN_Content|W_Content  the User object
     * @return array  JSON array of the Groups that the specified person has been invited to
     */
    private static function getMetadataForGroupsInviting($user) {
        $groupsMetadataJson = $user->my->groupsInvitedTo;
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        return $groupsMetadataJson ? $json->decode($groupsMetadataJson) : array();
    }

    /**
     * Adds metadata for a Group to which the user has been invited.
     *
     * @param XN_Content|W_Content  the User object to which to add the Group metadata
     * @param $groupId string  content ID of the Group
     * @param $inviter string  screen name of the user who sent the invitation
     */
    public static function addGroupInviting($user, $groupId, $inviter) {
        self::removeGroupInviting($user, $groupId);
        $newMetadataForGroupsInvitingUser = self::getMetadataForGroupsInviting($user);
        array_unshift($newMetadataForGroupsInvitingUser, array('id' => $groupId, 'inviter' => $inviter));
        self::setMetadataForGroupsInviting($user, $newMetadataForGroupsInvitingUser);
    }

    /**
     * Removes metadata for a Group to which the user has been invited.
     *
     * @param XN_Content|W_Content  the User object from which to remove the Group metadata
     * @param $groupId string  content ID of the Group
     */
    public static function removeGroupInviting($user, $groupId) {
        $newMetadataForGroupsInvitingUser = array();
        foreach (self::getMetadataForGroupsInviting($user) as $groupMetadata) {
            if ($groupMetadata['id'] != $groupId) { $newMetadataForGroupsInvitingUser[] = $groupMetadata; }
        }
        self::setMetadataForGroupsInviting($user, $newMetadataForGroupsInvitingUser);
    }

    /**
     * Sets the metadata for the Groups to which the user has been invited.
     * Each Group is represented by:
     *     id - content ID of the Group
     *     inviter - screen name of the user who sent the invitation
     *
     * @param XN_Content|W_Content  the User object
     * @param $metadata array  JSON array of the Groups that the specified person has been invited to
     */
    private static function setMetadataForGroupsInviting($user, $metadata) {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $user->my->groupsInvitedTo = $json->encode($metadata);
    }

    /**
     * Returns metadata for the unused invitation for the given invitee.
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $invitee string Screen name or email of the invited
     * @return array  null if the person does not have an unused invitation; otherwise an array keyed by:
     *     inviter - Ning username of the person who sent the invitation
     */
    public static function getUnusedGroupInvitation($group, $invitee) {
        foreach (self::getMetadataForGroupsInviting(User::load($invitee)) as $groupMetadata) {
            if ($groupMetadata['id'] == $group->id) { return array('inviter' => $groupMetadata['inviter']); }
        }
        return null;
    }

    /**
     * Returns metadata for unused invitations for the specified group.
     *
     * @param $groupId string  content ID of the Group
     * @param $begin integer  0-based start index (inclusive)
     * @param $end integer  0-based end index (exclusive)
     * @param $totalCount integer  output for the total number of unused invitations for the group
     * @param $profiles array  output for the invitee and inviter profiles, keyed by screen name
     * @return array  for each invitation, an array keyed by:
     *     displayName - recipient's display name
     *     screenName - recipient's Ning username, if any
     *     emailAddress - recipient's email address
     *     id - identifier for the invitation
     *     date - date on which the invitation was sent
     *     inviter - Ning username of the person who sent the invitation
     */
    public static function getUnusedGroupInvitations($groupId, $begin, $end, &$totalCount, &$profiles) {
        $totalCount = 0;
        $profiles = array();
        $query = XN_Query::create('Invitation')
                ->filter('label', '=', self::groupInvitationLabel($groupId))
                ->begin($begin)
                ->end($end)
                ->order('createdDate', 'desc')
                ->alwaysReturnTotalCount(true);
        $invitations = $query->execute();
        $screenNamesAndEmailAddresses = array();
        foreach ($invitations as $invitation) {
            $screenNamesAndEmailAddresses[] = $invitation->inviter;
            $screenNamesAndEmailAddresses[] = $invitation->recipient;
        }
        $totalCount = $query->getTotalCount();
        $profiles = XG_Cache::profiles($screenNamesAndEmailAddresses);
        return Index_InvitationHelper::metadataForInvitations($invitations);
    }


    /**
     * Sets the state of the invitation: sent, accepted, blocked
     * (marked as no longer valid, either before or after acceptance).
     * Does nothing if $invitee is null or an empty string.
     *
     * @param $group XN_Content|W_Content  the Group object
     * @param $invitee string  recipient's email address
     * @param $status string  the new status: accepted, declined, blocked
     */
    public static function setGroupInvitationStatus($group, $invitee, $status) {
        if (! $invitee) { return; }
        if (! in_array($status, array('accepted', 'declined', 'blocked'))) { throw new Exception('Assertion failed (843909533)'); }
        Index_InvitationHelper::deleteInvitations($invitee, self::groupInvitationLabel($group->id), $status == 'accepted');
        $profile = XG_Cache::profiles($invitee);
        if (! $profile) { return; }
        $user= User::load($profile);
        if (! $user) { return; }
        self::removeGroupInviting($user, $group->id);
        $user->save();
    }

    /**
     * Returns the XN_Invitation label for the given Group.
     *
     * @param string $groupId  the content ID for the Group object
     * @return string  the label for invitations for the Group
     */
    public static function groupInvitationLabel($groupId) {
        if (! is_string($groupId)) { xg_echo_and_throw('Assertion failed (1296105079)', true); }
        return 'group-invitation-' . $groupId;
    }

    /**
     * Returns the Group associated with the given invitation.
     *
     * @param $label  the invitation label
     * @return XN_Content|W_Content  the associated Group, or null if there is none
     */
    private static function group($label) {
        $groupId = self::groupId($label);
        return $groupId ? Group::load($groupId) : null;
    }

    /**
     * Extracts the group ID from the label.
     *
     * @param $label  the invitation label
     * @return  the ID of the associated Group, or null if there is none
     */
    protected static function groupId($label) {
        return preg_match('@group-[^-]+-(.*)@u', $label, $matches) ? $matches[1] : null;
    }

    /**
     * Called when an invitation is consumed.
     *
     * @param $invitation  the invitation to consume
     */
    public static function onConsume($invitation) {
        if (self::groupId($invitation->label) && $user = User::load(XN_Profile::current())) {
            self::addGroupInviting($user, self::groupId($invitation->label), $invitation->inviter);
            $user->save();
        }
    }

    /**
     * Called when an invitation is being re-sent.
     *
     * @param $invitation  the invitation to re-send
     */
    public static function onResend($invitation) {
        $group = self::group($invitation->label);
        if ($group) {
            $message = new XG_Message_Group_Invitation(array(
                    'subject' => xg_text('COME_JOIN_ME_ON_X_ON_Y', $group->title, XN_Application::load()->name),
                    'url' => XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id, Index_InvitationHelper::KEY => $invitation->id))));
            $message->send($invitation->recipient, $invitation->inviter, $group);
        }
    }

}

XN_Event::listen('invitation/consume/after', array('Groups_InvitationHelper', 'onConsume'));
XN_Event::listen('invitation/resend', array('Groups_InvitationHelper', 'onResend'));
