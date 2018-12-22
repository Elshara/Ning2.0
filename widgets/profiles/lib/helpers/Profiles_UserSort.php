<?php

/**
 * Logic for sorting User objects.
 */
abstract class Profiles_UserSort {

    /**
     * Retrieves the sort with the given name.
     *
     * @param string $name  Name of the sort: mostRecent, alphabetical, random
     */
    public static function get($name) {
        if (! self::$nameToSortMap[$name]) {
            $className = 'Profiles_' . ucfirst($name) . 'UserSort';
            self::$nameToSortMap[$name] = new $className;
        }
        return self::$nameToSortMap[$name];
    }

    /** Mapping of sort name to singleton sort instance */
    public static $nameToSortMap = array();

    /**
     * Returns users matching the given criteria.
     *
     * @param $filters array An array of filters keyed by attribute name k. Each array element is either:
     *              'v' to filter on k = v
     *              array('op','v') to filter on k op v
     *              array('op','v','type') to filter on k op v type
     * @param integer $begin optional result set start. Defaults to 0
     * @param integer $end   optional result set end.   Defaults to 10
     * @param mixed $caching optional caching control information:
     *                       true: cache, use default max age and no additional invalidation keys
     *                       integer: cache, use provided integer as max age and no invalidation keys
     *                       array: cache, use optional 'maxAge' key as max age
     *                                     use optional 'keys' key as invalidation keys
     * @param $helper Profiles_FriendListHelper  facade for services used by the friend/list action, making it easier to test.
     * @return array A two element array: 'users' => the requested users
     *                                    'numUsers' => the total number of users that match
     */
     public abstract function findUsers($filters, $begin, $end, $caching, $helper);

    /**
     * Returns a brief description of this sort, suitable for display
     *
     * @return string  The sort description
     */
    public abstract function getDisplayText();

    /**
     * Returns a title for the page.
     *
     * @param $numUsers integer  total number of Users
     * @return string  the plain-text title
     */
    public abstract function getPageTitle($numUsers);

    /**
     * Returns a short string identifying this sort.
     *
     * @return string  e.g., mostRecent
     */
    public function getId() {
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        return XG_LangHelper::lcfirst(preg_replace('@Profiles_([a-z]+)UserSort@ui', '\\1', get_class($this)));
    }

}

/**
 * Logic for sorting User objects, newest first.
 */
class Profiles_MostRecentUserSort extends Profiles_UserSort {

    /**
     * Returns users matching the given criteria.
     */
     public function findUsers($filters, $begin, $end, $caching, $helper) {
         return $helper->findUsers($filters, $begin, $end, 'createdDate', 'desc', $caching);
     }

    /**
     * Returns a brief description of this sort, suitable for display
     *
     * @return string  The sort description
     */
    public function getDisplayText() {
        return xg_text('RECENTLY_ADDED');
    }

    /**
     * Returns a title for the page.
     *
     * @param $numUsers integer  total number of Users
     * @return string  the plain-text title
     */
     public function getPageTitle($numUsers) {
		 return xg_text('RECENTLY_ADDED');
     }

}

/**
 * Logic for sorting User objects alphabetically.
 */
class Profiles_AlphabeticalUserSort extends Profiles_UserSort {

    /**
     * Returns users matching the given criteria.
     */
     public function findUsers($filters, $begin, $end, $caching, $helper) {
         return $helper->findUsers($filters, $begin, $end, 'my.fullName', 'asc', $caching);
     }

    /**
     * Returns a brief description of this sort, suitable for display
     *
     * @return string  The sort description
     */
    public function getDisplayText() {
        return xg_text('ALPHABETICAL');
    }

    /**
     * Returns a title for the page.
     *
     * @param $numUsers integer  total number of Users
     * @return string  the plain-text title
     */
     public function getPageTitle($numUsers) {
		 return xg_text('MEMBERS');
     }

}

/**
 * Logic for sorting User objects randomly.
 */
class Profiles_RandomUserSort extends Profiles_UserSort {

    /**
     * Returns users matching the given criteria.
     */
    public function findUsers($filters, $begin, $end, $caching, $helper) {
        //TODO: implement sort descriptors like in photos/videos [ywh 2008-05-13]
        return $helper->findUsers($filters, $begin, $end, 'random()', null, false);
    }

    /**
     * Returns a brief description of this sort, suitable for display
     *
     * @return string  The sort description
     */
    public function getDisplayText() {
        return xg_text('RANDOM');
    }

    /**
     * Returns a title for the page.
     *
     * @param $numUsers integer  total number of Users
     * @return string  the plain-text title
     */
     public function getPageTitle($numUsers) {
         return xg_text('RANDOM_MEMBERS');
     }

}

/**
 * Logic for sorting User objects, most recently featured first.
 */
class Profiles_MostRecentlyFeaturedUserSort extends Profiles_UserSort {

    /**
     * Returns users matching the given criteria.
     */
     public function findUsers($filters, $begin, $end, $caching, $helper) {
         XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
         return $helper->findUsers($filters, $begin, $end, 'my.' . XG_PromotionHelper::attributeName(), 'desc', $caching);
     }

    /**
     * Returns a brief description of this sort, suitable for display
     *
     * @return string  The sort description
     */
    public function getDisplayText() {
        return xg_text('LATEST_FEATURED');
    }

    /**
     * Returns a title for the page.
     *
     * @param $numUsers integer  total number of Users
     * @return string  the plain-text title
     */
     public function getPageTitle($numUsers) {
         return xg_text('FEATURED_MEMBERS');
     }

}
