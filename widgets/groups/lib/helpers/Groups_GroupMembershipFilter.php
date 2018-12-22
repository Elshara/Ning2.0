<?php

/**
 * A query filter that filters and sorts GroupMemberships.
 * This is the well-known "Strategy" pattern: different objects
 * represent different algorithms (in this case, filtering/sorting).
 *
 * Usage: Groups_GroupMembershipFilter::get($filterName)->execute($query, $groupId)
 */
abstract class Groups_GroupMembershipFilter {

    /**
     * Retrieves the filter with the given name.
     *
     * @param $name string  Name of the filter: mostRecent, mostPopular, plain
     */
    public static function get($name) {
        if (! self::$nameToFilterMap[$name]) {
            $className = 'Groups_' . ucfirst($name) . 'GroupMembershipFilter';
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
     * @param $groupId string  Content ID of the group
     * @return array  Group objects
     */
    public abstract function execute($query, $groupId);

    /**
     * Same as execute, but returns XN_Profiles.
     *
     * @param $query XN_Query|XG_Query  The query to filter
     * @param $groupId string  Content ID of the group
     * @return array  XN_Profiles
     */
    public function profiles($query, $groupId) {
        return GroupMembership::profiles($this->execute($query, $groupId));
    }

    /**
     * Adds the owner filter (to restrict the query to the current app), and
     * mozzle filter; turns on alwaysReturnTotalCount.
     *
     * @param $query XN_Query|XG_Query  The query to modify
     * @param $groupId string  Content ID of the group
     */
    protected function addBasicFilters($query, $groupId) {
        $query->filter('owner');
        $query->filter('my.mozzle', '=', 'groups');
        if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
        $query->alwaysReturnTotalCount(TRUE);
        $query->filter('type','=','GroupMembership');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(GroupMembership::groupMembershipChangedInvalidationKey($groupId)); }
        $query->filter('my.groupId', '=', $groupId);
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
 * A query filter that filters in members, sorted most active first.
 */
class Groups_MostActiveGroupMembershipFilter extends Groups_GroupMembershipFilter {
    public function execute($query, $groupId) {
        $this->addBasicFilters($query, $groupId);
        $query->filter('my.status', 'in', array('member', 'admin'));
        $query->order('my.activityCount', 'desc', XN_Attribute::NUMBER);
        return GroupMembership::addToCache($query->execute());
    }
}



/**
 * A query filter that filters in members, sorted by date joined.
 */
class Groups_MostRecentGroupMembershipFilter extends Groups_GroupMembershipFilter {
    public function execute($query, $groupId) {
        $this->addBasicFilters($query, $groupId);
        $query->filter('my.status', 'in', array('member', 'admin'));
        $query->order('my.dateJoined', 'desc', XN_Attribute::DATE);
        return GroupMembership::addToCache($query->execute());
    }
}


/**
 * A query filter that filters in members, using the sort order of the query supplied.
 */
class Groups_UnsortedGroupMembershipFilter extends Groups_GroupMembershipFilter {
    public function execute($query, $groupId) {
        $this->addBasicFilters($query, $groupId);
        $query->filter('my.status', 'in', array('member', 'admin'));
        return GroupMembership::addToCache($query->execute());
    }
}



/**
 * A query filter that filters in admin GroupMemberships
 */
class Groups_AdminGroupMembershipFilter extends Groups_GroupMembershipFilter {
    public function execute($query, $groupId) {
        $this->addBasicFilters($query, $groupId);
        $query->filter('my.status', '=', 'admin');
        return GroupMembership::addToCache($query->execute());
    }
}



/**
 * A query filter that filters in banned GroupMemberships
 */
class Groups_BannedGroupMembershipFilter extends Groups_GroupMembershipFilter {
    public function execute($query, $groupId) {
        $this->addBasicFilters($query, $groupId);
        $query->filter('my.status', '=', 'banned');
        $query->order('my.dateBanned', 'desc', XN_Attribute::DATE);
        return GroupMembership::addToCache($query->execute());
    }
}
