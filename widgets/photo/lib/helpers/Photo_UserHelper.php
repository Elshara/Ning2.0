<?php

/**
 * Common code for saving and querying User objects.
 */
class Photo_UserHelper {
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
     *         code      => The code used to index this order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     */
    public static function getMostActiveSortingOrder() {
        return array('name'      => xg_text('MOST_ACTIVE'),
                     'code'      => self::SORT_ORDER_MOSTACTIVE,
                     'attribute' => 'my->' . Photo_UserHelper::attributeName('activityCount'),
                     'direction' => 'desc',
                     'type'      => XN_Attribute::NUMBER);
    }

    /**
     * Returns the descriptor of the most-recent sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         code      => The code used to index this order
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
     *         code      => The code used to index this order
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
     * Loads a user object from the content store. If there is no user object yet for
     * the specified user, a new one will be created and stored.
     */
    public static function loadOrCreate($profileOrScreenName) {
        return self::load($profileOrScreenName, true);
    }

    public static function load($profileOrScreenName, $createIfNecessary = false) {
        $user = User::load($profileOrScreenName, $createIfNecessary);
        if ($user && is_null(self::get($user, 'photoCount'))) {
            self::set($user, 'photoCount', 0, XN_Attribute::NUMBER);
            self::set($user, 'albumCount', 0, XN_Attribute::NUMBER);
            self::set($user, 'commentCount', 0, XN_Attribute::NUMBER);
            self::set($user, 'activityCount', 0, XN_Attribute::NUMBER);
            self::set($user, 'ratingCount', 0, XN_Attribute::NUMBER);
        }
        return $user;
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
     * @param usersOrScreenNames   The user objects
     * @return An array screen name => status string (contact | friend | pending | requested |
     *         groupie | blocked | not-friend)
     */
    public static function getFriendStatusFor($profile, $usersOrScreenNames) {
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        return XG_ContactHelper::getFriendStatusFor($profile->screenName, $usersOrScreenNames);
    }

    /**
     * Determines the most active friends of the current user.
     *
     * @param profile    The profile of the current user
     * @param numFriends The number of friends to return
     * @return The friends
     */
    public static function getFriends($screenName, $numFriends) {
        if ($_GET['test_friend_count'] === '0') { return array(); }
        $query = XN_Query::create('Content')
                         ->filter('type', '=', 'User')
                         ->filter('owner')
                         ->filter('contributorName', 'in', XN_Query::FRIENDS($screenName))
                         ->order('my->' . Photo_UserHelper::attributeName('activityCount'), 'desc', XN_Attribute::NUMBER)
                         ->end($numFriends);
        // Don't add activity filter to friends query (BAZ-273) [Jon Aquino 2006-11-28]
        Photo_PrivacyHelper::addBlockedFilter($query);
        return $query->execute();
    }

    /**
     * Gets the most active users.
     *
     * @param numUsers The number of users to return
     * @return The users with the most active user first
     */
    public static function getMostActiveUsers($numUsers = 7) {
        return User::getMostActiveUsersForCurrentWidget($numUsers);
    }

    /**
     * Returns an array of sorted users.
     *
     * @param filters An array of filters to limit the returned users:
     *                'searchFor' => Text to search for
     *                'friends'   => Whether to return friends only
     * @param sort    The sort descriptor as returned by getKnownSortingOrders()
     * @param begin   The number of the first user to return
     * @param end     The number of the user after the last user to return
     * @return An array 'users' => the users, 'numUsers' => the total number of users that match the query
     */
    public static function getSortedUsers($filters = null, $sort, $begin = 0, $end = 100) {
        $query = XN_Query::create('Content')
                         ->filter('type', '=', 'User')
                         ->filter('owner');

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
        Photo_PrivacyHelper::addBlockedFilter($query);
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
     * @see PHO-621 "A person shouldn't appear on the app until they contribute their first piece of content"
     */
    public static function addActivityFilter($query, $canFilterOutAppOwner = true) {
        if ($canFilterOutAppOwner) {
            $query->filter('my->' . Photo_UserHelper::attributeName('activityCount'), '>', 0, XN_Attribute::NUMBER);
        } else {
            $query->filter(XN_Filter::any(XN_Filter('my->' . Photo_UserHelper::attributeName('activityCount'), '>', 0, XN_Attribute::NUMBER), XN_Filter('title','=',XN_Application::load()->ownerName)));
        }
    }

    // do we need this? called by PhotoController::_before [ywh 2008-07-22]
    public static function autoCreateAppOwnerUserObject() {
        $currentProfile = XN_Profile::current();
        if (XG_SecurityHelper::userIsOwner($currentProfile) && ! self::load($currentProfile)) { self::loadOrCreate($currentProfile)->save(); }
    }

    /**
     * Returns the rating that the user applied to the indicated photo (if any).
     *
     * @param photoId The id of the photo
     * @param The rating or null if the user has not rated the photo
     */
    public static function getRating($user, $photoId) {
        return Photo_ContentHelper::value($photoId, $user, Photo_UserHelper::attributeName('ratings'));
    }

    /**
     * Adds or updates the rating of the user for the indicated photo.
     *
     * @param photoId The id of the photo
     * @param rating  The rating value
     */
    public static function setRating($user, $photoId, $rating) {
        Photo_ContentHelper::add($photoId, $user, Photo_UserHelper::attributeName('ratings'), $rating);
        self::set($user, 'ratingCount', Photo_ContentHelper::count($user, Photo_UserHelper::attributeName('ratings')), XN_Attribute::NUMBER);
        self::updateActivityCount($user);
    }

    /**
     * Adds the indicated photo as a favorite of this user.
     *
     * @param photoId The id of the photo
     */
    public static function addFavorite($user, $photoId) {
        // Limit of 100 according to the spec. This lets us use the id list as an "in" filter, as
        // in-filters allow a maximum of 100 items.  [Jon Aquino 2006-07-22]
        Photo_ContentHelper::add($photoId, $user, Photo_UserHelper::attributeName('favorites'), NULL, 100);
    }

    /**
     * Removes the indicated photo as a favorite of this user.
     *
     * @param photoId The id of the photo
     */
    public static function removeFavorite($user, $photoId) {
        Photo_ContentHelper::remove($photoId, $user, Photo_UserHelper::attributeName('favorites'));
    }

    /**
     * Determines whether the indicated photo is a favorite of this user.
     *
     * @param photoId The id of the photo
     * @return true if the photo is a favorite
     */
    public static function hasFavorite($user, $photoId) {
        return Photo_ContentHelper::has($photoId, $user, Photo_UserHelper::attributeName('favorites'));
    }

    /**
     * Registers at the user that he or she added a number of photos.
     *
     * @return  The User object
     */
    public static function addPhotos($user) {
        // TODO: Rename to photosAdded [Jon Aquino 2008-09-27]
        Photo_PhotoHelper::updatePhotoCount($user, FALSE);
        self::set($user, 'lastUploadOn', date('c'), XN_Attribute::DATE);
        self::updateActivityCount($user);
        return $user;
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

    /**
     * Updates the activity value for this user.
     */
    public static function updateActivityCount($user) {
        // In Bazel Photos, activity excludes comments in ratings - you need to contribute
        // a photo to appear on the Popular Contributors page [Jon Aquino 2006-12-04]
        self::set($user, 'activityCount', self::get($user, 'photoCount'), XN_Attribute::NUMBER);
    }

    public static function get($user, $name) {
        return $user->my->raw(self::attributeName($name));
    }

    public static function set($user, $name, $value, $type = XN_Attribute::STRING) {
        $user->my->set(self::attributeName($name), $value, $type);
    }

    public static function attributeName($attributeName) {
        if (in_array($attributeName, array('defaultVisibility', 'addCommentPermission', 'emailActivityPref', 'emailModeratedPref'))) {
            // These attributes are shared with the Video and Blog widgets [Jon Aquino 2006-12-01]
            return $attributeName;
        }
        return XG_App::widgetAttributeName(W_Cache::current('W_Widget'), $attributeName);
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
