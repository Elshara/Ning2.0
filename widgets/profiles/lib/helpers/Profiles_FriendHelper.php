<?php
/**
 * Useful functions for working with friends and friend requests.
 *
 * @see Confluence: In-Network Messages and Friend Requests v1
 */
class Profiles_FriendHelper {

    /** Maximum number of friend requests you may send on this network. */
    const SENT_FRIEND_REQUEST_LIMIT = 100;

    /** Maximum number of friends you may have on this network. */
    const FRIEND_LIMIT = 5000;

    /**
     * Kill switch for the friend-limit check (BAZ-9788).
     * FALSE prevents the call to the expensive numberOfFriendsOnNetwork() function.
     */
    const CHECKING_FRIEND_LIMIT = TRUE;

    /** Whether to log expensive queries to assist with QA for BAZ-9810 */
    const BAZ_9810_LOGGING_ENABLED = FALSE;

    /** Singleton instance of this helper. */
    protected static $instance = NULL;

    /**
     * Returns the singleton instance of this helper.
     *
     * @return Profiles_FriendHelper  the singleton
     */
    public static function instance() {
        if (is_null(self::$instance)) { self::$instance = new Profiles_FriendHelper(); }
        return self::$instance;
        // This helper contains instance methods rather than static methods.
        // Instance methods are easier to test than static methods;
        // in particular, we can use SimpleTest's generatePartial() function
        // to mock the instance methods called by the instance method under test.
        // See Profiles_FriendHelperTest->testSetContactStatus() for an example.  [Jon Aquino 2008-08-06]
    }

    /**
     * Queries the given range of friends who are members of this network.
     *
     * @param $start integer  the start index (inclusive)
     * @param $end integer  the end index (exclusive)
     * @return array  the XN_Profiles and the total count
     */
    public function getFriends($start, $end) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        return array_values(Index_MessageHelper::instance()->friendsOnNetwork(XN_Profile::current()->screenName, $start, $end));
    }

    /**
     * Queries the given range of friend requests sent by the current user.
     * The friend requests are an array of arrays, each keyed by screenName and date.
     *
     * @param $start integer  the start index (inclusive)
     * @param $end integer  the end index (exclusive)
     * @return array  the friend requests and the total count
     */
    public function getSentFriendRequests($start, $end) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_SentFriendRequestUpdater.php');
        if (Profiles_SentFriendRequestUpdater::instance()->isRunning()) { return array(array(), 0); }
        return $this->getFriendRequests(XN_Profile::FRIEND_PENDING, $start, $end);
    }

    /**
     * Queries the given range of friend requests received by the current user.
     * The friend requests are an array of arrays, each keyed by screenName and date.
     *
     * @param $start integer  the start index (inclusive)
     * @param $end integer  the end index (exclusive)
     * @return array  the friend requests and the total count
     */
    public function getReceivedFriendRequests($start, $end) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ReceivedFriendRequestUpdater.php');
        if (Profiles_ReceivedFriendRequestUpdater::instance()->isRunning()) { return array(array(), 0); }
        return $this->getFriendRequests(XN_Profile::GROUPIE, $start, $end);
    }

    /**
     * Returns the number of friend requests sent by the current user.
     *
     * @return integer  the friend request count
     */
    public function getSentFriendRequestCount() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_SentFriendRequestUpdater.php');
        if (Profiles_SentFriendRequestUpdater::instance()->isRunning()) { return 0; }
        $friendRequests = $this->getFriendRequests(XN_Profile::FRIEND_PENDING, 0, 1);
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
        if (! Profiles_CachedCountHelper::instance()->buildingCache) {
            Profiles_CachedCountHelper::instance()->put(Profiles_CachedCountHelper::SENT_FRIEND_REQUEST_COUNT, XN_Profile::current()->screenName, $friendRequests[1]);
        }
        return $friendRequests[1];
    }

    /**
     * Returns the number of friend requests received by the current user.
     *
     * @return integer  the friend request count
     */
    public function getReceivedFriendRequestCount() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ReceivedFriendRequestUpdater.php');
        if (Profiles_ReceivedFriendRequestUpdater::instance()->isRunning()) { return 0; }
        $friendRequests = $this->getFriendRequests(XN_Profile::GROUPIE, 0, 1);
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
        if (! Profiles_CachedCountHelper::instance()->buildingCache) {
            Profiles_CachedCountHelper::instance()->put(Profiles_CachedCountHelper::RECEIVED_FRIEND_REQUEST_COUNT, XN_Profile::current()->screenName, $friendRequests[1]);
        }
        return $friendRequests[1];
    }

    /**
     * Queries the given range of friend requests sent or received by the current user.
     * The friend requests are an array of arrays, each keyed by screenName and date.
     *
     * @param $relationship string  XN_Profile::FRIEND_PENDING for requests sent; XN_Profile::GROUPIE for requests received
     * @param $start integer  the start index (inclusive)
     * @param $end integer  the end index (exclusive)
     * @return array  the friend requests and the total count
     */
    protected function getFriendRequests($relationship, $start, $end) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_NetworkSpecificFriendRequestHelper.php');
        $friendRequests = Profiles_NetworkSpecificFriendRequestHelper::instance()->getFriendRequests(XN_Profile::current()->screenName, $relationship);
        return array(array_slice($friendRequests, $start, $end), count($friendRequests));
    }

    /**
     * Creates a friend request from the current user to the given recipient.
     *
     * @param $screenName string  screen name of the person to send the friend request to
     * @param $message string  optional plain-text message body
     */
    public function createFriendRequest($screenName, $message) {
        if ($this->getContactStatus($screenName) == XN_Profile::FRIEND) { return; } // Just in case: Prevent unnecessary notifications [Jon Aquino 2008-06-13]
        $this->setContactStatus($screenName, XN_Profile::FRIEND, XN_Profile::NOT_FRIEND);
        $message = mb_substr(trim($message), 0, FriendRequestMessage::MAX_MESSAGE_LENGTH);
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        XG_App::includeFileOnce('/lib/XG_MessageHelper.php');
        Index_MessageHelper::sendNotification(NULL, $message, $screenName, XG_Message_Notification::EVENT_FRIEND_REQUEST);
        FriendRequestMessage::setMessage($screenName, XN_Profile::current()->screenName, $message);
    }

    /**
     * Deletes the friend request from the current user to the given recipients.
     *
     * @param $screenNames array  screen names of people that friend requests were sent to
     */
    public function withdrawFriendRequests($screenNames) {
        $this->setContactStatus($screenNames, XN_Profile::NOT_FRIEND, XN_Profile::FRIEND_PENDING);
        FriendRequestMessage::deleteMessages($screenNames, array(XN_Profile::current()->screenName));
    }

    /**
     * Friends the given senders to the current user
     *
     * @param $screenNames array  screen names of people who sent friend requests
     * @param $logActivity boolean  whether to record the new friendships in the activity log
     * @param $friendCount integer  total number of new friendships to log
     */
    public function acceptFriendRequests($screenNames, $logActivity = TRUE, $friendCount = NULL) {
        $this->setContactStatus($screenNames, XN_Profile::FRIEND, XN_Profile::GROUPIE);
        FriendRequestMessage::deleteMessages(array(XN_Profile::current()->screenName), $screenNames);
        if ($logActivity) { $this->logFriendships($screenNames, $friendCount); }
    }

    /**
     * Records the new friendships in the activity log.
     *
     * @param $screenNames array  screen names of the current user's new friends
     * @param $friendCount integer  total number of new friendships to log
     */
    private function logFriendships($screenNames, $friendCount) {
        $results = User::find(array('title' => array('in', $screenNames), 'my->activityFriendships' => array('!=', 'N')), 0, 5);
        $memberScreenNames = User::screenNames($results['users']);
        if (! $memberScreenNames) { return; }
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        $item = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_CONNECTION, XG_ActivityHelper::SUBCATEGORY_FRIEND, XN_Profile::current()->screenName . ',' . implode(',', $memberScreenNames));
        if ($item) {
            $item->my->xg_profiles_friendCount = $friendCount;
            $item->save();
        }
    }

    /**
     * Deletes the friend request from the given senders to the current user
     *
     * @param $screenNames array  screen names of people who sent friend requests
     */
    public function ignoreFriendRequests($screenNames) {
        $this->setContactStatus($screenNames, XN_Profile::NOT_FRIEND, XN_Profile::GROUPIE);
        FriendRequestMessage::deleteMessages(array(XN_Profile::current()->screenName), $screenNames);
    }

    /**
     * Deletes all friend requests sent.
     */
    public function withdrawAllFriendRequests() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_SentFriendRequestUpdater.php');
        Profiles_SentFriendRequestUpdater::instance()->withdrawAll();
    }

    /**
     * Friends the current user to all who have sent her friend requests
     */
    public function acceptAllFriendRequests() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ReceivedFriendRequestUpdater.php');
        Profiles_ReceivedFriendRequestUpdater::instance()->acceptAll();
    }

    /**
     * Deletes all friend requests received.
     */
    public function ignoreAllFriendRequests() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ReceivedFriendRequestUpdater.php');
        Profiles_ReceivedFriendRequestUpdater::instance()->ignoreAll();
    }

    /**
     * Blocks a person from sending you messages on this network and deletes all messages associated with the user!
     *
     * @param $screenName string  screen name of the person to block
     */
    public function blockMessagesAndDeleteFriendRequests($screenName) {
        self::blockMessages($screenName);
        FriendRequestMessage::deleteMessages(array($screenName), array(XN_Profile::current()->screenName));
    }

    /**
     * Blocks a person from sending you messages on this network.
     *
     * @param $screenName string  screen name of the person to block
     */
    public function blockMessages($screenName) {
        $current = XN_Profile::current();
        $sender = XN_Profile::load($screenName);
        BlockedContactList::blockSender($current->screenName, array($sender->screenName, $sender->email));
    }

    /**
     * Unblocks a person - allowing them to send you messages on this network.
     *
     * @param $screenName string  screen name of the person to block
     */
    public function unblockMessages($screenName) {
        $current = XN_Profile::current();
        $sender = XN_Profile::load($screenName);
        BlockedContactList::unblockSender($current->screenName, array($sender->screenName, $sender->email));
    }

    /** Number of screen names above which setContactStatus will not invalidate the friend and friend-request caches */
    const SET_CONTACT_STATUS_INVALIDATION_THRESHOLD = 3;

    /**
     * Alters the relationship between the current user and another user.
     *
     * @param $screenNames string|array  one or several screen names
     * @param $newRelationship string  XN_Profile::FRIEND, XN_Profile::NOT_FRIEND
     * @param $oldRelationship string  XN_Profile::FRIEND, XN_Profile::NOT_FRIEND, XN_Profile::FRIEND_PENDING, XN_Profile::GROUPIE
     */
    public function setContactStatus($screenNames, $newRelationship, $oldRelationship) {
        $screenNames = array_unique(is_array($screenNames) ? $screenNames : array($screenNames));
        $this->setContactStatusProper($screenNames, $newRelationship);
        $this->invalidateCachesForCurrentUserAnd($screenNames, $newRelationship, $oldRelationship);
    }

    /**
     * Invalidates the friend and friend request caches for the current user and
     * the given users.
     *
     * @param $screenNames array  other users to invalidate
     * @param $newRelationship string  relationship of the current user to the other users: XN_Profile::FRIEND, XN_Profile::NOT_FRIEND
     * @param $oldRelationship string  best guess as to the initial relationship: XN_Profile::FRIEND, XN_Profile::NOT_FRIEND, XN_Profile::FRIEND_PENDING, XN_Profile::GROUPIE
     */
    public function invalidateCachesForCurrentUserAnd($screenNames, $newRelationship, $oldRelationship) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_NetworkSpecificFriendRequestHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        Profiles_NetworkSpecificFriendRequestHelper::instance()->invalidateFriendRequestsCache(XN_Profile::current()->screenName);
        Index_MessageHelper::instance()->invalidateFriendsOnNetworkCache(XN_Profile::current()->screenName);
        // If the number of screen names exceeds the threshold, just rely on the cache TTLs
        // (FRIEND_REQUESTS_CACHE_MAX_AGE and FRIENDS_ON_NETWORK_CACHE_MAX_AGE). [Jon Aquino 2008-08-09]
        if (count($screenNames) <= XG_App::constant('Profiles_FriendHelper::SET_CONTACT_STATUS_INVALIDATION_THRESHOLD')) {
            foreach ($screenNames as $screenName) {
                Profiles_NetworkSpecificFriendRequestHelper::instance()->invalidateFriendRequestsCache($screenName);
                Index_MessageHelper::instance()->invalidateFriendsOnNetworkCache($screenName);
            }
        }
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
        Profiles_CachedCountHelper::instance()->updateCounts($screenNames, $newRelationship, $oldRelationship);
    }

    /**
     * Alters the relationship between the current user and the specified user(s).
     *
     * @param $screenNames string|array  one or several screen names
     * @param $relationship string  XN_Profile::FRIEND, XN_Profile::NOT_FRIEND
     */
    protected function setContactStatusProper($screenNames, $relationship) {
        // TODO: Remove the next line [Jon Aquino 2008-09-30]
        if (is_array($screenNames) && count($screenNames) == 1) { $screenNames = reset($screenNames); } // Workaround to make this work on XNA, which doesn't yet have 6.13.2 [Jon Aquino 2008-09-02]
        XN_Profile::current()->setContactStatus($screenNames, $relationship); // Ignore result (BAZ-10574) [Jon Aquino 2008-09-30]
    }

    /**
     * Returns the relationship between the current user and the specified user
     *
     * @param $screenName string  screen name of the other user
     * @return string  XN_Profile::FRIEND, XN_Profile::NOT_FRIEND, or XN_Profile::BLOCK
     */
    public function getContactStatus($screenName) {
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        return XG_ContactHelper::getFriendStatusFor(XN_Profile::current()->screenName, $screenName);
    }

    /**
     * Whether the current user will have sent too many friend requests on this network
     * if the given number of friend requests are sent.
     *
     * @param $numberOfNewFriendRequests integer  the number of friend requests that will be sent
     * @return boolean  whether the sent friend request limit has been exceeded
     */
    public function willSentFriendRequestLimitBeExceeded($numberOfNewFriendRequests) {
        if (XG_SecurityHelper::userIsAdmin()) { return false; }
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
        return ($numberOfNewFriendRequests + Profiles_CachedCountHelper::instance()->getApproximateSentFriendRequestCount()) > XG_App::constant('Profiles_FriendHelper::SENT_FRIEND_REQUEST_LIMIT');
    }

    /**
     * Whether the current user will have too many friends on this network if the given
     * number of friends is added.
     *
     * @param $numberOfNewFriends integer  the number of friends that will be added
     * @return boolean  whether the limit on the number of friends has been exceeded
     */
    public function willFriendLimitBeExceeded($numberOfNewFriends) {
        if (XG_SecurityHelper::userIsAdmin()) { return false; }
        if (! XG_App::constant('Profiles_FriendHelper::CHECKING_FRIEND_LIMIT')) { return false; }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
        // Check numberOfFriendsAcrossNing first, as it is less expensive than numberOfFriendsOnNetwork [Jon Aquino 2008-08-20]
        return ($numberOfNewFriends + Profiles_CachedCountHelper::instance()->getApproximateNumberOfFriendsAcrossNingFor(XN_Profile::current()->screenName)) > XG_App::constant('Profiles_FriendHelper::FRIEND_LIMIT')
                && ($numberOfNewFriends + Profiles_CachedCountHelper::instance()->getApproximateNumberOfFriendsOnNetworkFor(XN_Profile::current()->screenName)) > XG_App::constant('Profiles_FriendHelper::FRIEND_LIMIT');
    }

}
