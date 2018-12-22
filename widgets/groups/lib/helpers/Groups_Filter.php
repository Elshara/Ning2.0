<?php

/**
 * A query filter that filters and sorts Groups.
 * This is the well-known "Strategy" pattern: different objects
 * represent different algorithms (in this case, filtering/sorting).
 *
 * Usage: Groups_Filter::get($filterName)->execute($query, $username)
 */
abstract class Groups_Filter {

    /**
     * Retrieves the filter with the given name.
     *
     * @param $name string  Name of the filter: mostRecent, mostPopular
     */
    public static function get($name) {
        if (! self::$nameToFilterMap[$name]) {
            $className = 'Groups_' . ucfirst($name) . 'Filter';
            self::$nameToFilterMap[$name] = new $className;
        }
        return self::$nameToFilterMap[$name];
    }

    /** Mapping of filter name to singleton filter instance */
    public static $nameToFilterMap = array();

    /**
     * Filters and sorts Groups. Takes care of filtering the
     * owner, type, mozzle and contributorName; alwaysReturnTotalCount is turned on.
     * If you pass in an XG_Query without invalidation keys specified, basic type-invalidation
     * keys will be added automatically.
     *
     * @param $query XN_Query|XG_Query  The query to filter
     * @param $username string  Username to filter on (optional)
     * @return array  Group objects
     */
    public abstract function execute($query, $username = NULL);

    /**
     * Returns a brief description of this filter, suitable for display
     *
     * @param $username string  Username that will be filtered on (optional)
     * @return string  The filter description
     */
    public abstract function getDisplayText($username);

    /**
     * Adds the owner filter (to restrict the query to the current app),
     * mozzle filter, and username filter; turns on alwaysReturnTotalCount.
     *
     * @param $query XN_Query|XG_Query  The query to modify
     * @param $username string  Username to filter on (optional)
     * @param $excludeUnapproved boolean Whether to exclude unapproved groups; optional
     */
    protected function addBasicFilters($query, $username, $excludeUnapproved = true) {
        $query->filter('owner');
        $query->filter('my.mozzle', '=', 'groups');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        XG_QueryHelper::addDeletedFilter($query);
        if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
        if ($excludeUnapproved) {
            $query->filter('my->approved', '<>', 'N');
        }
        $query->alwaysReturnTotalCount(TRUE);
        // use my->username instead of contributorName (BAZ-10094) [ywh 2008-09-17]
        if ($username) { $query->filter('my->username', 'eic', $username); }
    }
    /**
     * Returns whether the query is an XG_Query with no invalidation keys specified.
     *
     * @return boolean  whether invalidation keys need to be added
     */
    protected static function needsInvalidationKeys($query) {
        return $query instanceof XG_Query && count($query->getCaching()) == 0;
    }
}

/**
 * A query filter for Search queries (instead of content queries)
 */
class Groups_SearchFilter extends Groups_Filter {
    public function execute($query, $username = NULL) {
        $query->filter('my.mozzle', 'like', 'groups');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        XG_QueryHelper::addDeletedFilter($query, true);
        if (defined('UNIT_TESTING')) { $query->filter('my.test', 'like', 'Y'); }
        $query->alwaysReturnTotalCount(TRUE);
        if ($username) { $query->filter('contributorName', 'like', $username); }
        return $query->execute();
    }

    public function getDisplayText($username) {
        return '';
    }
}

/**
 * A query filter that returns groups requiring moderation.
 */
class Groups_ModerationFilter extends Groups_Filter {
    public function execute($query, $username = NULL) {
        if ($username) {
            $query->filter('contributorName', '=', $username);
        }
        $this->addBasicFilters($query, $username, false);
        $query->filter('type','=','Group');
        $query->filter('my->approved', '=', 'N');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Group')); }
        $query->order('createdDate', 'asc', XN_Attribute::DATE);
        return Group::addToCache($query->execute());
    }
    public function getDisplayText($username) {
        return '';
    }
}

/**
 * A query filter that filters in promoted Groups, sorted most recent first.
 */
class Groups_PromotedFilter extends Groups_Filter {
    public function execute($query, $username = NULL) {
        if ($username) { throw new Exception('Assertion failed'); }
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Group');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Group')); }
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_PromotionHelper::addPromotedFilterToQuery($query);
        $query->order('my->' . XG_PromotionHelper::attributeName(), 'desc', XN_Attribute::DATE);
        return Group::addToCache($query->execute());
    }
    public function getDisplayText($username) {
        return xg_text('FEATURED');
    }
}


/**
 * A query filter that filters Group objects based on the greatest activity score.
 */
class Groups_MostActiveFilter extends Groups_Filter {
    public function execute($query, $username = NULL) {
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Group');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Group')); }
        $widget = W_Cache::current('W_Widget');
        // BAZ-6953: Can only sort by one attribute, so the best "Most Active" can do is sort by activityScore
        // @todo reenable primary sort after the content store allows sorting by multiple attributes
        //$query->order('my->lastActivityOn', 'desc', XN_Attribute::DATE);
        $query->order('my->activityScore', 'desc', XN_Attribute::NUMBER);
        return Group::addToCache($query->execute());
    }
    public function getDisplayText($username) {
        return xg_text('MOST_ACTIVE');
    }
}

/**
 * A query filter that filters Group objects based on the greatest number of members.
 */
class Groups_MostPopularFilter extends Groups_Filter {
    public function execute($query, $username = NULL) {
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Group');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Group')); }
        $widget = W_Cache::current('W_Widget');
        $query->order('my->memberCount', 'desc', XN_Attribute::NUMBER);
        return Group::addToCache($query->execute());
    }
    public function getDisplayText($username) {
        return xg_text('MOST_MEMBERS');
    }
}



/**
 * A query filter that filters Group objects based on the latest activity date.
 */
class Groups_LatestActivityFilter extends Groups_Filter {
    public function execute($query, $username = NULL) {
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Group');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Group')); }
        $widget = W_Cache::current('W_Widget');
        $query->order('my->lastActivityOn', 'desc', XN_Attribute::DATE);
        return Group::addToCache($query->execute());
    }
    public function getDisplayText($username) {
        return xg_text('LATEST_ACTIVITY');
    }
}

/**
 * A query filter that filters Group objects based on the date of group creation.
 */
class Groups_MostRecentFilter extends Groups_Filter {
    public function execute($query, $username = NULL) {
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Group');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Group')); }
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        return Group::addToCache($query->execute());
    }
    public function getDisplayText($username) {
        return xg_text('LATEST');
    }
}


/**
 * A query filter that filters in Groups that the specified person has joined, sorted most recently joined first.
 */
class Groups_JoinedFilter extends Groups_Filter {
    public function execute($query, $username = NULL) {
        if (! $username) { throw new Exception('Assertion failed (601725633)'); }
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','GroupMembership');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'GroupMembership')); }
        $query->filter('my.status', 'in', array('member', 'admin'));
        $idsOfGroupsMarkedAsDeleted = array_intersect(Group::idsOfGroupsMarkedAsDeleted(), Group::groupIds(User::load($username)));
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        XG_QueryHelper::addNotInFilter($query, 'my.groupId', $idsOfGroupsMarkedAsDeleted);
        if (XN_Profile::current()->screenName != $username) { $query->filter('my.groupPrivacy', '=', 'public'); }
        $query->order('my.dateJoined', 'desc', XN_Attribute::DATE);
        return Group::groupsForObjects($query->execute());
    }
    public function getDisplayText($username) {
        return 'NOT YET IMPLEMENTED';
    }
}
