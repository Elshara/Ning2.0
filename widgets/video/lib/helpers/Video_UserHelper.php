<?php

/**
 * Common code for saving and querying User objects.
 */
class Video_UserHelper {
    /** Constant for sorting by most active. */
    const SORT_ORDER_MOSTACTIVE  = 'mostActive';
    /** Constant for sorting by most recent. */
    const SORT_ORDER_MOSTRECENT   = 'mostRecent';
    /** Constant for alphabetical sorting. */
    const SORT_ORDER_ALPHABETICAL = 'alphabetical';

    /**
     * Returns the descriptors of the known sorting orders.
     *
     * @return The descriptors keyed by the internal name, containing:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     */
    public static function getKnownSortingOrders() {
        return array(self::SORT_ORDER_MOSTACTIVE   => self::getMostActiveSortingOrder(),
                     self::SORT_ORDER_MOSTRECENT   => self::getMostRecentSortingOrder(),
                     self::SORT_ORDER_ALPHABETICAL => self::getAlphabeticalSortingOrder());
    }

    /**
     * Returns the descriptor of the most-active sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     */
    public static function getMostActiveSortingOrder() {
        return array('name'      => xg_text('MOST_ACTIVE'),
                     'code'      => self::SORT_ORDER_MOSTACTIVE,
                     'attribute' => 'my->' . self::attributeName('activityCount'),
                     'direction' => 'desc',
                     'type'      => XN_Attribute::NUMBER);
    }

    /**
     * Returns the descriptor of the most-recent sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     */
    public static function getMostRecentSortingOrder() {
        return array('name'      => xg_text('MOST_RECENT'),
                     'code'      => self::SORT_ORDER_MOSTRECENT,
                     'attribute' => 'createdDate',
                     'direction' => 'desc',
                     'type'      => XN_Attribute::DATE);
    }

    /**
     * Returns the descriptor of the alphabetical sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     */
    public static function getAlphabeticalSortingOrder() {
        return array('name'      => xg_text('ALPHABETICAL'),
                     'code'      => self::SORT_ORDER_ALPHABETICAL,
                     'attribute' => 'my->lowercaseScreenName',
                     'direction' => 'asc',
                     'type'      => XN_Attribute::STRING);
    }

    /**
     * Determines whether the other user is a friend of the current user.
     *
     * @param profile    The profile of the current user
     * @param screenName The screen name of the other user
     * @return true iff the other user is a friend
     */
    public static function isFriend($profile, $screenName) {
        if ($profile->isLoggedIn()) {
            // The trick is to query for the user object and restrict the query
            // via a friends query against the owner of the user object (which is
            // the Ning user)
            $query = XN_Query::create('Content')
                             ->filter('type', '=', 'User')
                             ->filter('owner')
                             ->filter('title', 'eic', $screenName)
                             ->filter('contributorName', 'in', XN_Query::FRIENDS());
            return count($query->execute()) > 0;
        } else {
            return false;
        }
    }

    /**
     * Determines the friend status for the given user list.
     *
     * @param profile The profile of the current user
     * @param users   The user objects
     * @return An array screen name => status string (contact | friend | pending | requested |
     *         groupie | blocked | not-friend)
     */
    public static function getFriendStatusFor($profile, $users) {
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        return XG_ContactHelper::getFriendStatusFor($profile->screenName, $users);
    }

    /**
     * Returns n friends of the person with the given screenName.
     *
     * @param $screenName username of the person
     * @param $n max number of friends to retrieve
     * @param $numFriends output for the total number of friends found
     * @return an array of User XN_Content objects
     */
    public static function getFriends($screenName, $n, &$numFriends = null) {
        $query = XN_Query::create('Content')
                       ->filter('type', '=', 'User')
                       ->filter('owner')
                       ->filter('contributorName', 'in', XN_Query::FRIENDS($screenName))
                       ->filter('my->duplicate', '<>', 'Y')
                       ->order('my->' . self::attributeName('activityCount'), 'desc', XN_Attribute::NUMBER)
                       ->end($n)
                       ->alwaysReturnTotalCount(true);
        // Don't add activity filter to friends query (BAZ-273) [Jon Aquino 2006-11-28]
        Video_PrivacyHelper::addBlockedFilter($query);
        $friends = $query->execute();
        $numFriends = $query->getTotalCount();
        if (isset($_GET['test_friend_count'])) {
            $firstFriend = $friends[0];
            $numFriends = $_GET['test_friend_count'];
            $friends = array();
            for ($i = 0; $i < min($n, $numFriends); $i++) {
                $friends[] = $firstFriend;
            }
        }
        return $friends;
    }

    /**
     * Gets the most active users.
     *
     * @param n The number of users to return
     * @param $numActiveUsers output for the total number of active users found
     * @return The users with the most active user first
     */
    public static function getMostActiveUsers($n = 7, &$numActiveUsers) {
        return User::getMostActiveUsersForCurrentWidget($n, $numActiveUsers);
    }

    /**
     * Returns an array of sorted users.
     *
     * @param filters An array of filters to limit the returned users:
     *                'searchFor' => Text to search for
     *                'friendsOf' => Screen name of person whose friends to return
     * @param sort    The sort descriptor as returned by getKnownSortingOrders()
     * @param begin   The number of the first user to return
     * @param end     The number of the user after the last user to return
     * @return An array 'users' => the users, 'numUsers' => the total number of users that match the query
     */
    public static function getSortedUsers($filters = null, $sort, $begin = 0, $end = 100) {
        $query = XN_Query::create('Content')
                         ->filter('type', '=', 'User')
                         ->filter('owner')
                         ->filter('my->duplicate', '<>', 'Y');

        if ($filters) {
            if ($filters['searchFor']) {
                $query->filter('my->searchText', 'likeic', $filters['searchFor']);
            }
            if ($filters['friendsOf']) {
                $query->filter('contributorName', 'in', XN_Query::FRIENDS($filters['friendsOf']));
            }
        }

        // Don't add activity filter to friends query (BAZ-273) [Jon Aquino 2006-11-28]
        if (! $filters || ! $filters['friendsOf']) {
            self::addActivityFilter($query, false);
        }
        Video_PrivacyHelper::addBlockedFilter($query);
        $query->order($sort['attribute'], $sort['direction'], $sort['type']);
        if (isset($_GET['test_user_count'])) { $usersPerPage = $end - $begin; $begin = 0; $end = 1; }
        $query->begin($begin);
        $query->end($end);
        $query->alwaysReturnTotalCount(true);

        /* Only cache the query if:
         * - there's no friendsOf filter
         * AND
         * - we can cache order N queries if there's a searchFor filter
         */
        // TODO: Allow caching for the friends filter, but use XG_QueryHelper::setMaxAgeForFriendsQuery [Jon Aquino 2008-09-17]
        if ((! $filters['friendsOf']) && ((! $filters['searchFor']) || XG_Cache::cacheOrderN())) {
            $query = XG_Query::create($query);
            $query->setCaching(XG_Cache::key('type','User'));
        }

        $users    = $query->execute();
        $numUsers = $query->getTotalCount();
        if (isset($_GET['test_user_count']) && count($users) > 0) {
            $user = $users[0];
            $numUsers = $_GET['test_user_count'];
            $users = array();
            for ($i = 0; $i < min($usersPerPage, $numUsers); $i++) {
                $users[] = $user;
            }
        }
        return array('users' => $users, 'numUsers' => $numUsers);
    }

    /**
     * Filters out people who have not yet contributed content.
     * @see VID-805 "A person shouldn't appear on the app until they contribute their first piece of content"
     */
    public static function addActivityFilter($query, $canFilterOutAppOwner = true) {
        if ($canFilterOutAppOwner) {
            $query->filter('my->' . self::attributeName('activityCount'), '>', 0, XN_Attribute::NUMBER);
        } else {
            $query->filter(XN_Filter::any(XN_Filter('my->' . self::attributeName('activityCount'), '>', 0, XN_Attribute::NUMBER), XN_Filter('title','=',XN_Application::load()->ownerName)));
        }
    }

    // do we need this? called by VideoController::_before [ywh 2008-07-22]
    public static function autoCreateAppOwnerUserObject() {
        $currentProfile = XN_Profile::current();
        if (XG_SecurityHelper::userIsOwner($currentProfile) && ! self::load($currentProfile)) { self::loadOrCreate($currentProfile)->save(); }
    }

    public static function loadOrCreate($profileOrScreenName) {
        return self::load($profileOrScreenName, true);
    }

    public static function load($profileOrScreenName, $createIfNecessary = false) {
        $user = User::load($profileOrScreenName, $createIfNecessary);
        if ($user && is_null(self::get($user, 'videoCount'))) {
            self::set($user, 'videoCount', 0, XN_Attribute::NUMBER);
            self::set($user, 'commentCount', 0, XN_Attribute::NUMBER);
            self::set($user, 'activityCount', 0, XN_Attribute::NUMBER);
            self::set($user, 'ratingCount', 0, XN_Attribute::NUMBER);
        }
        return $user;
    }

    /**
     * Retrieves the given widget-specific attribute on the given User object,
     * for the current widget.
     *
     * @param $user XN_Content|W_Content  The User object
     * @param $name string  Name of the attribute
     * @return string|integer  Value of the attribute
     */
    public static function get($user, $name) {
        return User::getWidgetAttribute($user, $name);
    }

    /**
     * Sets a widget-specific attribute on the given User object,
     * for the current widget.
     *
     * @param $user XN_Content|W_Content  The User object
     * @param $name string  Name of the attribute
     * @param $value string|integer Value of the attribute
     * @param $type XN_Attribute::STRING (default), XN_Attribute::NUMBER, or XN_Attribute::DATE
     */
    public static function set($user, $name, $value, $type = XN_Attribute::STRING) {
        User::setWidgetAttribute($user, $name, $value, $type);
    }

    /**
     * Returns an appropriately prefixed attribute name for User objects,
     * for the current widget.
     * @param $name string  The unprefixed attribute name
     * @return string  The prefixed attribute name
     */
    public static function attributeName($attributeName) {
        return User::widgetAttributeName($attributeName);
    }

    public static function getRating($user, $videoId) {
        return Video_ContentHelper::value($videoId, $user, self::attributeName('ratings'));
    }



    public static function setRating($user, $videoId, $rating) {
        Video_ContentHelper::add($videoId, $user, self::attributeName('ratings'), $rating);
        self::set($user, 'ratingCount', Video_ContentHelper::count($user, self::attributeName('ratings')), XN_Attribute::NUMBER);
        self::updateActivityCount($user);
    }



    public static function hasFavorite($user, $videoId) {
        return Video_ContentHelper::has($videoId, $user, self::attributeName('favorites'));
    }


    public static function setCommentCount($user, $commentCount) {
        self::set($user, 'commentCount', max(0, $commentCount), XN_Attribute::NUMBER);
        self::updateActivityCount($user);
    }


    /**
     * Registers that the user has made a new comment.
     */
    public static function addComment($user) {
        self::set($user, 'commentCount', self::get($user, 'commentCount') + 1, XN_Attribute::NUMBER);
        self::updateActivityCount($user);
    }

    /**
     * Registers that one comment of the user has been deleted.
     */
    public static function removeComment($user) {
        if (self::get($user, 'commentCount') > 0) {
            self::set($user, 'commentCount', self::get($user, 'commentCount') - 1, XN_Attribute::NUMBER);
        }
    }


    public static function updateActivityCount($user) {
        // In Bazel Videos, activity excludes comments in ratings - you need to contribute
        // a video to appear on the Popular Contributors page [Jon Aquino 2006-12-04]
        self::set($user, 'activityCount', Video_UserHelper::get($user, 'videoCount'), XN_Attribute::NUMBER);
    }



    public static function addFavorite($user, $videoId) {
        // Limit of 100 according to the spec. This lets us use the id list as an "in" filter, as in-filters allow a maximum of 100 items.  [Jon Aquino 2006-07-22]
        Video_ContentHelper::add($videoId, $user, self::attributeName('favorites'), NULL, 100);
    }


    public static function removeFavorite($user, $videoId) {
        Video_ContentHelper::remove($videoId, $user, self::attributeName('favorites'));
    }


    public static function watchLater($user, $videoId) {
        Video_ContentHelper::add($videoId, $user, self::attributeName('watchLaterList'), NULL, 100);
    }


    public static function willWatchLater($user, $videoId) {
        return Video_ContentHelper::has($videoId, $user, self::attributeName('watchLaterList'));
    }

    public static function willNotWatchLater($user, $videoId) {
        return Video_ContentHelper::remove($videoId, $user, self::attributeName('watchLaterList'));
    }

    /**
     * Returns a profile object representing a person who is not signed in.
     *
     * @return XN_Profile  the profile object
     */
    public static function createAnonymousProfile() {
        return XN_Profile::create(null, null);
    }
}
