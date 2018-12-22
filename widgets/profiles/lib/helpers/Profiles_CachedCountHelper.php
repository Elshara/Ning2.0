<?php

/**
 * Caches the counts of sent/received friend requests, friends across all networks, and friends on the
 * current network - all of which can be expensive to compute. When a count changes, we increment/decrement
 * the cached value rather than invalidating it.
 *
 * @see BAZ-9810
 */
class Profiles_CachedCountHelper {

    /** Name for the number of friend requests sent by a user. */
    const SENT_FRIEND_REQUEST_COUNT = 'sentFriendRequestCount';

    /** Name for the number of friend requests received by a user. */
    const RECEIVED_FRIEND_REQUEST_COUNT = 'receivedFriendRequestCount';

    /** Name for the number of friends of a user on the current network. */
    const NUMBER_OF_FRIENDS_ON_NETWORK = 'numberOfFriendsOnNetwork';

    /** Name for the number of friends of a user on all networks. */
    const NUMBER_OF_FRIENDS_ACROSS_NING = 'numberOfFriendsAcrossNing';

    /** TTL for each count's cache, in seconds. */
    const MAX_AGE = 1800;

    private $readOnlyProperties = array(

        /** Whether the helper is currently building a cache entry. */
        'buildingCache' => FALSE,

    );

    /** Singleton instance of this helper. */
    protected static $instance = NULL;

    /**
     * Returns the singleton instance of this helper.
     *
     * @return Profiles_CachedCountHelper  the singleton
     */
    public static function instance() {
        if (is_null(self::$instance)) { self::$instance = new Profiles_CachedCountHelper(); }
        return self::$instance;
    }

    /**
     * Returns the number of friend requests sent by the current user; may not be accurate.
     *
     * @return integer  the friend request count
     * @see Profiles_FriendHelper::getSentFriendRequestCount()
     */
    public function getApproximateSentFriendRequestCount() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        return $this->get(self::SENT_FRIEND_REQUEST_COUNT, XN_Profile::current()->screenName, array(Profiles_FriendHelper::instance(), 'getSentFriendRequestCount'));
    }

    /**
     * Returns the number of friend requests received by the current user; may not be accurate.
     *
     * @return integer  the friend request count
     * @see Profiles_FriendHelper::getReceivedFriendRequestCount()
     */
    public function getApproximateReceivedFriendRequestCount() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        return $this->get(self::RECEIVED_FRIEND_REQUEST_COUNT, XN_Profile::current()->screenName, array(Profiles_FriendHelper::instance(), 'getReceivedFriendRequestCount'));
    }

    /**
     * Returns the number of friends of the given user on the current network; may not be accurate.
     *
     * @param $screenName string  the username
     * @return integer  the friend count
     * @see Index_MessageHelper::numberOfFriendsOnNetwork()
     */
    public function getApproximateNumberOfFriendsOnNetworkFor($screenName) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        return $this->get(self::NUMBER_OF_FRIENDS_ON_NETWORK, $screenName, array('Index_MessageHelper', 'numberOfFriendsOnNetwork'), array($screenName));
    }

    /**
     * Returns the number of friends of the given user on all networks; may not be accurate.
     *
     * @param $screenName string  the username
     * @return integer  the friend count
     * @see Index_MessageHelper::numberOfFriendsAcrossNing()
     */
    public function getApproximateNumberOfFriendsAcrossNingFor($screenName) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        return $this->get(self::NUMBER_OF_FRIENDS_ACROSS_NING, $screenName, array('Index_MessageHelper', 'numberOfFriendsAcrossNing'), array($screenName));
    }

    /**
     * Returns the given count for the given user.
     *
     * @param $countName string  the name of the count, e.g., SENT_FRIEND_REQUEST_COUNT
     * @param $screenName string  the username
     * @param $buildCallback callback  public function to build the contents of the cache entry - typically an expensive operation
     * @param $buildCallbackArgs array  arguments to pass to the buildCallback
     * @return integer  the count
     */
    private function get($countName, $screenName, $buildCallback, $buildCallbackArgs = array()) {
        XG_App::includeFileOnce('/lib/XG_CacheHelper.php');
        $this->readOnlyProperties['buildingCache'] = TRUE;
        $count = XG_CacheHelper::instance()->get($this->cacheId($countName, $screenName), $countName, XG_App::constant('Profiles_CachedCountHelper::MAX_AGE'), $buildCallback, $buildCallbackArgs);
        $this->readOnlyProperties['buildingCache'] = FALSE;
        return $count;
    }

    /**
     * Returns the cache ID for the given count and the given user.
     *
     * @param $countName string  the name of the count, e.g., SENT_FRIEND_REQUEST_COUNT
     * @param $screenName string  the username
     */
    protected function cacheId($countName, $screenName) {
        return $countName . '-' . $screenName;
    }

    /**
     * Sets the given count for the given user
     *.
     * @param $countName string  the name of the count, e.g., SENT_FRIEND_REQUEST_COUNT
     * @param $screenName string  the username
     * @param $count integer  the new value for the count
     */
    public function put($countName, $screenName, $count) {
        XG_App::includeFileOnce('/lib/XG_CacheHelper.php');
        XG_CacheHelper::put($this->cacheId($countName, $screenName), $countName, XG_App::constant('Profiles_CachedCountHelper::MAX_AGE'), $count);
    }

    /**
     * Updates the counts based on the given change in relationship to the current user.
     *
     * @param $otherScreenNames array  the other users whose relationship to the current user has changed
     * @param $newRelationship string  relationship of the current user to the other users: XN_Profile::FRIEND, XN_Profile::NOT_FRIEND
     * @param $oldRelationship string  best guess as to the initial relationship: XN_Profile::FRIEND, XN_Profile::NOT_FRIEND, XN_Profile::FRIEND_PENDING, XN_Profile::GROUPIE
     */
    public function updateCounts($otherScreenNames, $newRelationship, $oldRelationship) {
        XG_App::includeFileOnce('/lib/XG_CacheHelper.php');
        foreach ($this->updateCountsProper($otherScreenNames, $newRelationship, $oldRelationship, XG_App::constant('Profiles_CachedCountHelper::MAX_USERS_FOR_UPDATE')) as $putArgs) {
            call_user_func_array(array('XG_CacheHelper', 'put'), $putArgs);
        }
    }

    /** Above this threshold, updateCounts() will update the counts for the current user only. */
    const MAX_USERS_FOR_UPDATE = 3;

    /**
     * Updates the counts based on the given change in relationship to the current user.
     *
     * @param $otherScreenNames array  the other users whose relationship to the current user has changed
     * @param $newRelationship string  relationship of the current user to the other users: XN_Profile::FRIEND, XN_Profile::NOT_FRIEND
     * @param $oldRelationship string  best guess as to the initial relationship: XN_Profile::FRIEND, XN_Profile::NOT_FRIEND, XN_Profile::FRIEND_PENDING, XN_Profile::GROUPIE
     * @param $maxUsersForUpdate integer  above this threshold, we will update the counts for the current user only.
     * @return array  arguments for XG_CacheHelper::instance()->put()
     */
    protected function updateCountsProper($otherScreenNames, $newRelationship, $oldRelationship, $maxOtherUsersToUpdate) {
        if ($newRelationship == XN_Profile::FRIEND && $oldRelationship == XN_Profile::NOT_FRIEND) {
            // Sending friend requests [Jon Aquino 2008-09-13]
            return $this->updateCountsProperProper($otherScreenNames, $maxOtherUsersToUpdate, array(+count($otherScreenNames), 0, 0, 0), array(0, +1, 0, 0));
        } elseif ($newRelationship == XN_Profile::FRIEND && $oldRelationship == XN_Profile::GROUPIE) {
            // Accepting received friend requests [Jon Aquino 2008-09-13]
            return $this->updateCountsProperProper($otherScreenNames, $maxOtherUsersToUpdate, array(0, -count($otherScreenNames), +count($otherScreenNames), +count($otherScreenNames)), array(-1, 0, 1, 1));
        } elseif ($newRelationship == XN_Profile::NOT_FRIEND && $oldRelationship == XN_Profile::FRIEND) {
            // Defriending [Jon Aquino 2008-09-13]
            return $this->updateCountsProperProper($otherScreenNames, $maxOtherUsersToUpdate, array(0, 0, -count($otherScreenNames), -count($otherScreenNames)), array(0, 0, -1, -1));
        } elseif ($newRelationship == XN_Profile::NOT_FRIEND && $oldRelationship == XN_Profile::FRIEND_PENDING) {
            // Withdrawing sent friend requests [Jon Aquino 2008-09-13]
            return $this->updateCountsProperProper($otherScreenNames, $maxOtherUsersToUpdate, array(-count($otherScreenNames), 0, 0, 0), array(0, -1, 0, 0));
        } elseif ($newRelationship == XN_Profile::NOT_FRIEND && $oldRelationship == XN_Profile::GROUPIE) {
            // Ignoring received friend requests [Jon Aquino 2008-09-13]
            return $this->updateCountsProperProper($otherScreenNames, $maxOtherUsersToUpdate, array(0, -count($otherScreenNames), 0, 0), array(-1, 0, 0, 0));
        } else {
            error_log('Unrecognized transition: ' . $newRelationship . ' <= ' . $oldRelationship . ' (1133633623)');
            return array();
        }
    }

    /**
     * Updates the counts based on the given change in relationship to the current user.
     *
     * @param $otherScreenNames array  the other users whose relationship to the current user has changed
     * @param $maxUsersForUpdate integer  above this threshold, we will update the counts for the current user only.
     * @param $currentUserDeltas array  changes in sentFriendRequestCount, receivedFriendRequestCount, numberOfFriendsOnNetwork, and numberOfFriendsAcrossNing for the current user
     * @param $otherUserDeltas array  changes in sentFriendRequestCount, receivedFriendRequestCount, numberOfFriendsOnNetwork, and numberOfFriendsAcrossNing for each of the other users
     * @return array  arguments for XG_CacheHelper::instance()->put()
     */
    protected function updateCountsProperProper($otherScreenNames, $maxOtherUsersToUpdate, $currentUserDeltas, $otherUserDeltas) {
        $result = array();
        $countNames = array(self::SENT_FRIEND_REQUEST_COUNT, self::RECEIVED_FRIEND_REQUEST_COUNT, self::NUMBER_OF_FRIENDS_ON_NETWORK, self::NUMBER_OF_FRIENDS_ACROSS_NING);
        foreach ($countNames as $i => $countName) {
            if ($currentUserDeltas[$i] == 0) { continue; }
            $count = $this->getProper($countName, XN_Profile::current()->screenName);
            if (is_null($count)) { continue; }
            $result[] = array($this->cacheId($countName, XN_Profile::current()->screenName), array($countName), XG_App::constant('Profiles_CachedCountHelper::MAX_AGE'), max(0, $count + $currentUserDeltas[$i]));
        }
        if (count($otherScreenNames) > $maxOtherUsersToUpdate) { return $result; }
        foreach ($otherScreenNames as $otherScreenName) {
            foreach ($countNames as $i => $countName) {
                if ($otherUserDeltas[$i] == 0) { continue; }
                $count = $this->getProper($countName, $otherScreenName);
                if (! mb_strlen($count)) { continue; }
                $result[] = array($this->cacheId($countName, $otherScreenName), array($countName), XG_App::constant('Profiles_CachedCountHelper::MAX_AGE'), max(0, $count + $otherUserDeltas[$i]));
            }
        }
        return $result;
    }

    /**
     * Returns the given count for the given user, or null if it does not exist in the cache.
     *.
     * @param $countName string  the name of the count, e.g., SENT_FRIEND_REQUEST_COUNT
     * @param $screenName string  the username
     * @return integer  the count, or null
     */
    protected function getProper($countName, $screenName) {
        XG_App::includeFileOnce('/lib/XG_CacheHelper.php');
        return XG_CacheHelper::getProper($this->cacheId($countName, $screenName));
    }

    /**
     * Provides write access to properties of the profile
     *
     * @param $name string property name
     * @param $value mixed property value
     */
    public function __set($name, $value) {
        throw new Exception('Assertion failed (1769927641)');
    }

    /**
     * Provides read access to the property of the given name simulated as a
     * public instance variable accessed through the '->' operator.
     *
     * @param $name string property name
     * @return mixed
     */
    public function __get($name) {
        if (! array_key_exists($name, $this->readOnlyProperties)) { throw new Exception('Unknown property: ' . $name . ' (732688229)'); }
        return $this->readOnlyProperties[$name];
    }

}
