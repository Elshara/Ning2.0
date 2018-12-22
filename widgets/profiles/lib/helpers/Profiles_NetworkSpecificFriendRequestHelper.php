<?php

/**
 * Useful functions for working with network-specific friend requests.
 */
class Profiles_NetworkSpecificFriendRequestHelper {

    /** Number of seconds to cache the friend requests. */
    const FRIEND_REQUESTS_CACHE_MAX_AGE = 1800;

    // The current implementation is brute force and expensive. See BAZ-8867. [Jon Aquino 2008-08-08]

    // TODO: A future core release will let us filter friend requests by network of origin.
    // We might be able to eliminate this helper at that point. Note that the behavior is a little different
    // (this helper does not strictly filter by network of origin), so we would need to run the change by Product. [Jon Aquino 2008-09-23]

    /** Singleton instance of this helper. */
    private static $instance = NULL;

    /**
     * Returns the singleton instance of this helper.
     *
     * @return Profiles_NetworkSpecificFriendRequestHelper  the singleton
     */
    public static function instance() {
        if (is_null(self::$instance)) { self::$instance = new Profiles_NetworkSpecificFriendRequestHelper(); }
        return self::$instance;
    }

    /** Number of Ning-wide friend requests to check. */
    const FRIEND_REQUEST_LIMIT = 1000;

    /**
     * Returns all friend requests for the current user, filtered according to
     * whether the person is a member of the current network. Newest friend requests
     * appear first. The result is cached for 30 minutes.
     *
     * @param $screenName string  the user's username
     * @param $relationship string  XN_Profile::GROUPIE for sent friend requests; XN_Profile::FRIEND_PENDING for received friend requests
     * @return array  array of arrays, each keyed by screenName and date
     */
    public function getFriendRequests($screenName, $relationship) {
        static $friendRequests = array();
        $key = $screenName . ', ' . $relationship;
        if (is_null($friendRequests[$key])) {
            $friendRequests[$key] = $this->getFriendRequestsProper($screenName, $relationship);
        }
        return $friendRequests[$key];
    }

    /**
     * Returns all friend requests for the current user, filtered according to
     * whether the person is a member of the current network. Newest friend requests
     * appear first. The result is cached for 30 minutes.
     *
     * @param $screenName string  the user's username
     * @param $relationship string  XN_Profile::GROUPIE for sent friend requests; XN_Profile::FRIEND_PENDING for received friend requests
     * @return array  array of arrays, each keyed by screenName and date
     */
    protected function getFriendRequestsProper($screenName, $relationship) {
        XG_App::includeFileOnce('/lib/XG_CacheHelper.php');
        return XG_CacheHelper::instance()->get(
            $this->friendRequestsCacheId($screenName, $relationship),
            $this->friendRequestsCacheLabel($screenName),
            XG_App::constant('Profiles_NetworkSpecificFriendRequestHelper::FRIEND_REQUESTS_CACHE_MAX_AGE'),
            array($this, 'getFriendRequestsProperProper'), array($screenName, $relationship));
    }

    /**
     * Cache ID for the given user's friend requests
     *
     * @param $screenName string  the user's screen name
     * @param $relationship string  XN_Profile::GROUPIE for sent friend requests; XN_Profile::FRIEND_PENDING for received friend requests
     */
    protected function friendRequestsCacheId($screenName, $relationship) {
        return "friend-requests-$screenName-$relationship";
    }

    /**
     * Cache label for the given user's friend requests
     *
     * @param $screenName string  the user's screen name
     */
    protected function friendRequestsCacheLabel($screenName) {
        return "friend-requests-$screenName";
    }

    /**
     * Clears the cache of the given user's friend requests
     *
     * @param $screenName string  the user for whom to invalidate the counts
     */
    public function invalidateFriendRequestsCache($screenName) {
        XN_Cache::invalidate($this->friendRequestsCacheLabel($screenName));
    }

    /**
     * Returns all friend requests for the given user, filtered according to
     * whether the person is a member of the current network. Newest friend requests
     * appear first. The result is not cached.
     *
     * @param $screenName string  the user's username
     * @param $relationship string  XN_Profile::GROUPIE for sent friend requests; XN_Profile::FRIEND_PENDING for received friend requests
     * @return array  array of arrays, each keyed by screenName and date
     */
    public function getFriendRequestsProperProper($screenName, $relationship) {
        // This function must be public so that it can be called by XG_CacheHelper::instance()->get() [Jon Aquino 2008-09-15]
        $friendRequests = array();
        foreach ($this->getFriendRequestContacts($screenName, $relationship) as $contact) {
            $friendRequests[] = array('screenName' => $contact->screenName, 'date' => $contact->updatedDate);
        }
        return $friendRequests;
    }

    /**
     * Returns all friend requests for the given user, filtered according to
     * whether the person is a member of the current network. Newest friend requests
     * appear first. The result is not cached.
     *
     * @param $screenName string  the user's username
     * @param $relationship string  XN_Profile::GROUPIE for sent friend requests; XN_Profile::FRIEND_PENDING for received friend requests
     * @return array  XN_Contact objects
     */
    public function getFriendRequestContacts($screenName, $relationship) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        if (XG_App::constant('Profiles_FriendHelper::BAZ_9810_LOGGING_ENABLED')) { error_log("BAZ-9810: Executing query for friend requests on the network (current user: " . $screenName . ") ($relationship)"); }
        $query = $this->createContactQuery();
        $query->filter('owner', '=', $screenName);
        $query->filter('relationship', '=', $relationship);
        $query->order('updatedDate', 'desc');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $contacts = XG_QueryHelper::executeAsNeeded($query, XG_App::constant('Profiles_NetworkSpecificFriendRequestHelper::FRIEND_REQUEST_LIMIT'));
        $users = $this->loadUsers(User::screenNames($contacts));
        $filteredContacts = array();
        foreach ($contacts as $contact) {
            if ($users[$contact->screenName] && User::isMember($users[$contact->screenName])) {
                $filteredContacts[] = $contact;
            }
        }
        return $filteredContacts;
    }

    /**
     * Creates a query for Contact objects.
     *
     * @return XN_Query  the query to use for friend requests.
     */
    protected function createContactQuery() {
        return XN_Query::create('Contact');
    }

    /**
     * Recursively converts the given mixed-type arguments (objects and screen names) into User objects.
     *
     * @see User::loadMultiple()
     */
    protected function loadUsers() {
        $args = func_get_args();
        return User::loadMultiple($args);
    }

}
