<?php

/**
 * Utility functions for working with messages sent to friends.
 * NOTE: A huge section of the code here is copied over to OpenSocial_MessageHelper.  We need to ensure that the two logics are in sync.
 */
class Index_MessageHelper {

    /**
     * Kill switch for the "Friends on this Network" checkbox on the QuickAdd Share dialog (BAZ-9788).
     * FALSE prevents the call to the expensive numberOfFriendsOnNetwork() function.
     */
    const QUICKADD_SHARE_FRIENDS_ON_NETWORK_CHECKBOX_DISPLAYED = TRUE;

    /** Name of the set of all friends in the FriendList. */
    const ALL_FRIENDS = 'ALL_FRIENDS';

    /** Name of the set of friends who are members of the current network. */
    const FRIENDS_ON_NETWORK = 'FRIENDS_ON_NETWORK';

    /** Whether to enable the query that counts the number of friends on the current network (BAZ-9000). */
    const NUMBER_OF_FRIENDS_ON_NETWORK_QUERY_ENABLED = TRUE;

    /** Whether to enable the query that counts the number of friends across all networks (BAZ-9000). */
    const NUMBER_OF_FRIENDS_ACROSS_NING_QUERY_ENABLED = TRUE;

    /** Number of seconds to cache the current user's friends on this network. */
    const FRIENDS_ON_NETWORK_CACHE_MAX_AGE = 1800;

    /**
     * Sends the message to the specified users
     *
     * @param $subject string  the subject line
     * @param $message string  the message text
     * @param $screenNamesAndEmailAddresses array  usernames and email addresses of the recipients
     * @param $destinationFolder string  recipient's folder to deliver the mail at - default is INBOX
     */
    public static function send($subject, $message, $screenNamesAndEmailAddresses, $destinationFolder = Profiles_MessageHelper::FOLDER_NAME_INBOX) {
        XG_Cache::profiles($screenNamesAndEmailAddresses); // Preload with a single query [Jon Aquino 2008-09-04]
        self::postToMessagingEndpoint($subject, $message, $screenNamesAndEmailAddresses, $destinationFolder);
        $notificationFailureCount = 0;
        foreach ($screenNamesAndEmailAddresses as $screenNameOrEmailAddress) {
            try {
                XG_App::includeFileOnce('/lib/XG_MessageHelper.php');
                self::sendNotification($subject, $message, $screenNameOrEmailAddress, XG_Message_Notification::EVENT_USER_MESSAGE);
            } catch (Exception $e) {
                $notificationFailureCount++;
            }
        }
        if ($notificationFailureCount == count($screenNamesAndEmailAddresses)) { throw new Exception('Could not send notifications (359595798)'); }
    }

    /**
     * Sends the message to the specified users
     *
     * @param $subject string  the subject line
     * @param $message string  the message text
     * @param $screenNamesAndEmailAddresses array  usernames and email addresses of the recipients
     * @param $destinationFolder string  recipient's folder to deliver the mail at - default is INBOX
     */
    private static function postToMessagingEndpoint($subject, $message, $screenNamesAndEmailAddresses, $destinationFolder = Profiles_MessageHelper::FOLDER_NAME_INBOX) {
    	if (is_null($destinationFolder)) $destinationFolder = Profiles_MessageHelper::FOLDER_NAME_INBOX;
        $recipients = array();
        $displayNames = array();
        foreach ($screenNamesAndEmailAddresses as $screenNameOrEmailAddress) {
            $profile = XG_Cache::profiles($screenNameOrEmailAddress);
            $current = XN_Profile::current();
            // Blocked from sending messages?
            if (BlockedContactList::isSenderBlocked($profile ? $profile->screenName : $screenNameOrEmailAddress, array($current->screenName, $current->email)))
                continue;
            $recipients[] = $screenNameOrEmailAddress;
            $displayNames[$screenNameOrEmailAddress] = $profile ? XG_UserHelper::getFullName($profile) : $screenNameOrEmailAddress;
        }
        if (count($recipients) > 0) {  // if no recipients, then no need to post
            $message = XN_Message::create(array('recipients' => $recipients, 'displayNames' => $displayNames, 'subject' => $subject, 'body' => $message, 'destinationFolder' => $destinationFolder));
            $result = $message->save();
            if (is_array($result)) { throw new Exception(reset($result)); }
        }
    }

    /**
     * Sends an email to the recipient, notifying her that she has a new message.
     *
     * @param $subject string  the subject line
     * @param $message string  the message text
     * @param $screenNameOrEmailAddress string  username or email address of the recipient
     * @param $event string  the event type, e.g., XG_Message_Notification::EVENT_USER_MESSAGE
     */
    public static function sendNotification($subject, $message, $screenNameOrEmailAddress, $event) {
		XG_Browser::execInEmailContext(array(__CLASS__,'_sendNotification'), $subject, $message, $screenNameOrEmailAddress, $event);
    }

	// callback for sending notifications
	public static function _sendNotification ($subject, $message, $screenNameOrEmailAddress, $event) { # void
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $message = XG_Message_Notification::create($event, array(
                'profile' => XN_Profile::current(),
                'subject' => $subject,
                'body' => W_Cache::getWidget('main')->privateConfig['noMessageInNotification'] ? null : $message));
        $message->send($screenNameOrEmailAddress);
    }


    /**
     * Returns how many friends the user has on this network, i.e., not across Ning.
     * Banned and pending members are excluded.
     *
     * @param $screenName string  the user's screen-name
     * @return integer  the friend count
     */
    public static function numberOfFriendsOnNetwork($screenName) {
        if (! XG_App::constant('Index_MessageHelper::NUMBER_OF_FRIENDS_ON_NETWORK_QUERY_ENABLED')) { return 0; }
        $friendData = self::instance()->friendsOnNetwork($screenName, 0, 1);
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
        if (! Profiles_CachedCountHelper::instance()->buildingCache) {
            Profiles_CachedCountHelper::instance()->put(Profiles_CachedCountHelper::NUMBER_OF_FRIENDS_ON_NETWORK, $screenName, $friendData['numFriends']);
        }
        return $friendData['numFriends'];
    }

    /**
     * Returns how many friends the user has from all Ning networks
     *
     * @param $screenName string  the user's screen-name
     * @return integer  the friend count
     */
    public static function numberOfFriendsAcrossNing($screenName) {
        if (! XG_App::constant('Index_MessageHelper::NUMBER_OF_FRIENDS_ACROSS_NING_QUERY_ENABLED')) { return 0; }
        $friendData = self::instance()->friendsAcrossNing($screenName, 0, 1);
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
        if (! Profiles_CachedCountHelper::instance()->buildingCache) {
            Profiles_CachedCountHelper::instance()->put(Profiles_CachedCountHelper::NUMBER_OF_FRIENDS_ACROSS_NING, $screenName, $friendData['numFriends']);
        }
        return $friendData['numFriends'];
    }

    /**
     * Returns how many friends the user has in the given group
     *
     * @param $screenName string  the user's screen-name
     * @param $groupId string  content ID of the Group
     * @return integer  the friend count
     */
    public static function numberOfFriendsInGroup($screenName, $groupId) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_GroupMembershipFilter.php');
        $query = XN_Query::create('Content')->filter('contributorName', 'in', XN_Query::FRIENDS($screenName))->begin(0)->end(1);
        // TODO: Change XG_GroupHelper::currentGroupId() to $groupId [Jon Aquino 2008-03-25]
        Groups_GroupMembershipFilter::get('mostRecent')->execute($query, XG_GroupHelper::currentGroupId());
        return $query->getTotalCount();
    }

    /**
     * Returns data to use as JSON output for the friendData actions,
     * for friends on this network, i.e., not across Ning.
     * Banned and pending members are excluded.
     *
     * @param $start integer  inclusive start index
     * @param $end integer  exclusive end index
     * @return array  "screenNames", and "friends" (each with screenName, fullName and thumbnailUrl)
     */
    public static function dataForFriendsOnNetwork($start, $end) {
        // BAZ-5655 "Compose Message: Friend list should only contain friends on the current network" [Jon Aquino 2007-12-31]
        return self::friendData($start, $end, array(self::instance(), 'friendsOnNetwork'));
    }

    /**
     * Returns data to use as JSON output for the friendData actions,
     * for friends from all Ning networks.
     *
     * @param $start integer  inclusive start index
     * @param $end integer  exclusive end index
     * @return array  "screenNames", and "friends" (each with screenName, fullName and thumbnailUrl)
     */
    public static function dataForFriendsAcrossNing($start, $end) {
        return self::friendData($start, $end, array(self::instance(), 'friendsAcrossNing'));
    }

    /**
     * Returns data to use as JSON output for the friendData actions.
     *
     * @param $start integer  inclusive start index
     * @param $end integer  exclusive end index
     * @param $friendDataFunction callback  the function that retrieves the friend data
     * @return array  "screenNames", and "friends" (each with screenName, fullName, thumbnailUrl, and isMember)
     */
    private static function friendData($start, $end, $friendDataFunction) {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        XG_SecurityHelper::redirectIfNotMember();
        // TODO: Rename $friendData, as another $friendData is created below [Jon Aquino 2008-01-03]
        $friendData = call_user_func($friendDataFunction, XN_Profile::current()->screenName, 0, 1);
        $indexes = XG_LangHelper::indexes($start, min($friendData['numFriends'], $end), 100);
        $friends = array();
        $screenNames = array();
        if ($friendData['numFriends'] > 0) {
            for ($i = 0; $i < count($indexes) - 1; $i++) {
                $friendData = call_user_func($friendDataFunction, XN_Profile::current()->screenName, $indexes[$i], $indexes[$i+1]);
                foreach ($friendData['profiles'] as $profile) {
                    // $fullName may be null for old Ning profiles [Jon Aquino 2007-12-28]
                    $fullName = XG_UserHelper::getFullName($profile);
                    $fullName = $fullName ? $fullName : $profile->screenName;
                    $friends[] = array(
                            'screenName' => $profile->screenName,
                            'fullName' => $fullName,
                            'thumbnailUrl' => XG_UserHelper::getThumbnailUrl($profile, 32, 32),
                            'isMember' => User::isMember($profile));
                    $screenNames[] = $profile->screenName;
                }
            }
        }
        // Some fullNames may be out of order, so re-sort [Jon Aquino 2007-12-28]
        usort($friends, create_function('$a, $b', 'return strcmp(mb_strtolower($a["fullName"]), mb_strtolower($b["fullName"]));'));
        return array('friends' => $friends, 'screenNames' => $screenNames);
    }

    /**
     * Returns the user's friends on this network, i.e., not across Ning.
     * Banned and pending members are excluded.
     *
     * @param $screenName string  the user's screen-name
     * @param $begin integer  inclusive start index
     * @param $end integer  exclusive end index
     * @return array  "profiles" and "numFriends"
     */
    public function friendsOnNetwork($screenName, $begin, $end) {
        $result = $this->friendsOnNetworkProper($screenName, $begin, $end);
        $profiles = XG_Cache::profiles($result['screenNames']);
        // Check isMember explicitly because cached results may be out of date for recently banned members (BAZ-9707) [Jon Aquino 2008-09-09]
        $numFriendsAdjustment = 0;
        foreach ($profiles as $screenName => $profile) {
            if (! User::isMember($profile)) {
                unset($profiles[$screenName]);
                --$numFriendsAdjustment;
            }
        }
        return array('profiles' => $profiles, 'numFriends' => $result['numFriends'] + $numFriendsAdjustment);
    }

    /**
     * Returns the user's friends on this network, i.e., not across Ning.
     * Banned and pending members are excluded.
     *
     * @param $screenName string  the user's screen-name
     * @param $begin integer  inclusive start index
     * @param $end integer  exclusive end index
     * @return array  "screenNames" and "numFriends"
     */
    protected function friendsOnNetworkProper($screenName, $begin, $end) {
        // Cache expensive friend queries (BAZ-9491) [Jon Aquino 2008-09-01]
        // Don't cache if $begin > 0. $begin can vary unpredictably depending on the position
        // of the scrollbar in FriendList.js [Jon Aquino 2008-09-01]
        if ($begin > 0) { return $this->friendsOnNetworkProperProper($screenName, $begin, $end); }
        XG_App::includeFileOnce('/lib/XG_CacheHelper.php');
        return XG_CacheHelper::instance()->get(
            "friends-on-network-$screenName-$begin-$end",
            array('friends-on-network', $this->friendsOnNetworkCacheLabel($screenName)),
            XG_App::constant('Index_MessageHelper::FRIENDS_ON_NETWORK_CACHE_MAX_AGE'),
            array($this, 'friendsOnNetworkProperProper'), array($screenName, $begin, $end));
    }

    /**
     * Returns the user's friends on this network, i.e., not across Ning.
     * Banned and pending members are excluded.
     *
     * @param $screenName string  the user's screen-name
     * @param $begin integer  inclusive start index
     * @param $end integer  exclusive end index
     * @return array  "screenNames" and "numFriends"
     */
    public function friendsOnNetworkProperProper($screenName, $begin, $end) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        if (XG_App::constant('Profiles_FriendHelper::BAZ_9810_LOGGING_ENABLED')) { error_log("BAZ-9810: Executing query for friends on the network (current user: " . XN_Profile::current()->screenName . ") ($screenName, $begin, $end)"); }
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        // Set step size to 50 rather than 100, to avoid out-of-memory errors with large User objects [Jon Aquino 2007-12-31]
        $indexes = XG_LangHelper::indexes($begin, $end, 50);
        $users = array();
        $numFriends = 0;
        for ($i = 0; $i < count($indexes) - 1; $i++) {
            $result = User::find(array('contributorName' => array('in', XN_Query::FRIENDS($screenName))), $indexes[$i], $indexes[$i+1], 'my->fullName', 'asc', false);
            $users = array_merge($users, $result['users']);
            $numFriends = $result['numUsers'];
        }
        return array('screenNames' => User::screenNames($users), 'numFriends' => $numFriends);
    }

    /**
     * Clears the cached data for the friends on this network.
     *
     * @param $screenName string  the user for whom to invalidate the data
     */
    public function invalidateFriendsOnNetworkCache($screenName) {
        XN_Cache::invalidate($this->friendsOnNetworkCacheLabel($screenName));
    }

    /**
     * Label for caches of the user's friends on this network
     *
     * @param $screenName string  the user's screen name
     */
    protected function friendsOnNetworkCacheLabel($screenName) {
        return 'friends-on-network-' . $screenName;
    }

    /**
     * Returns the user's friends across Ning.
     *
     * @param $screenName string  the user's screen-name
     * @param $begin integer  inclusive start index
     * @param $end integer  exclusive end index
     * @return array  "profiles" and "numFriends"
     */
    public function friendsAcrossNing($screenName, $begin, $end) {
        // Contact-query returns at most 100 at a time [Jon Aquino 2007-12-31]
        if ($end - $begin > 100) { throw new Exception('end - begin exceeds 100 (1523451174)'); }
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        if (XG_App::constant('Profiles_FriendHelper::BAZ_9810_LOGGING_ENABLED')) { error_log("BAZ-9810: Executing query for friends across all networks (current user: " . XN_Profile::current()->screenName . ") ($screenName, $begin, $end)"); }
        $query = XN_Query::create('Contact')
                ->filter('owner', '=', $screenName)
                ->filter('relationship', '=', 'friend')
                ->begin($begin)
                ->end($end)
                ->order('name', 'asc')
                ->alwaysReturnTotalCount(true);
        $contacts = $query->execute();
        return array('profiles' => XG_Cache::profiles($contacts), 'numFriends' => $query->getTotalCount());
    }

    /**
     * Filters out members who have opted out of messages, group invitations,
     * and Share This messages sent to All Friends.
     *
     * @param $screenNames array  screen names, which may include non-members
     * @return array  screen names without those of Users who have opted out
     */
    public function removeAllFriendsOptOuts($screenNames) {
        $optOutScreenNames = array();
        // Batches of 50, to avoid out-of-memory errors with large User objects  [Jon Aquino 2008-01-01]
        foreach (array_chunk($screenNames, 50) as $screenNamesChunk) {
            foreach (User::loadMultiple($screenNamesChunk) as $user) {
                if (! self::acceptingMessagesSentToAllFriends($user)) {
                    $optOutScreenNames[$user->title] = $user->title;
                }
            }
        }
        $filteredScreenNames = array();
        foreach ($screenNames as $screenName) {
            if (! $optOutScreenNames[$screenName]) { $filteredScreenNames[] = $screenName; }
        }
        return $filteredScreenNames;
    }

    /**
     * Returns whether the User has not opted out of messages, group invitations, and
     * Share This messages sent to All Friends.
     *
     * @param $user XN_Content|W_Content  the User object to examine
     * @return boolean  whether the user is accepting messages sent to All Friends
     */
    protected static function acceptingMessagesSentToAllFriends($user) {
        return $user->my->emailAllFriendsPref ? $user->my->emailAllFriendsPref != 'N' : $user->my->emailNewMessagePref != 'N';
    }

    /** Singleton instance of this helper. */
    protected static $instance = NULL;

    /**
     * Returns the singleton instance of this helper.
     *
     * @return Index_MessageHelper  the singleton
     */
    public static function instance() {
        if (is_null(self::$instance)) { self::$instance = new Index_MessageHelper(); }
        return self::$instance;
    }

    /**
     * Creates a contact list from the given POST variables.
     *
     * @param $contactList string  JSON array of contacts, each an array with keys "name" and "emailAddress"
     * @param $friendStart integer  (if contactList is not specified) inclusive start index for a friend query
     * @param $friendEnd integer  (if contactList is not specified) exclusive end index for a friend query
     * @param $friendSet string  (if contactList is not specified) Index_MessageHelper::ALL_FRIENDS (default) or Index_MessageHelper::FRIENDS_ON_NETWORK
     * @param $screenNamesExcluded  (if contactList is not specified) list of screenNames to exclude from mailing
     * @return array  an array of contacts, each being an array with keys "name" and "emailAddress"
     */
    public function createContactList($post) {
        if (! array_key_exists('friendEnd', $post)) {
            $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
            return $json->decode($post['contactList']);
        }
        // this should provide backwards compatability for existing async jobs that do not use the exclude list
        $screenNamesExcluded = $post['screenNamesExcluded'] ? explode(',', $post['screenNamesExcluded']) : array();
        $friendDataFunction = $post['friendSet'] == self::FRIENDS_ON_NETWORK ? 'friendsOnNetwork' : 'friendsAcrossNing';
        $friendData = $this->$friendDataFunction(XN_Profile::current()->screenName, $post['friendStart'], $post['friendEnd']);
        $screenNames = array_diff_key(User::screenNames($friendData['profiles']), array_flip($screenNamesExcluded));
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationFormHelper.php');
        return Index_InvitationFormHelper::screenNamesToContactList($this->removeAllFriendsOptOuts($screenNames));
    }

}
