<?php

/**
 * Facade for services used by the friend/list action, making it easier to test.
 */
class Profiles_FriendListHelper {

    /**
     * Returns which method to use for doing search queries. "content" is the old way,
     * using a Content query. "search" is the new way, using a Search query.
     *
     * @return string  "search" or "content"
     * @see BAZ-1697
     */
    public function getSearchMethod() {
        return XG_QueryHelper::getSearchMethod();
    }

    /**
     * Query for users
     *
     * @param $filters array An array of filters keyed by attribute name k. Each array element is either:
     *              'v' to filter on k = v
     *              array('op','v') to filter on k op v
     *              array('op','v','type') to filter on k op v type
     * @param integer $begin optional result set start. Defaults to 0
     * @param integer $end   optional result set end.   Defaults to 10
     * @param mixed $order  optional field to order on. Defaults to null.
     *         null: no order specified
     *         string: the property to sort on
     *         array(name, type): sort on the named property with the given type, e.g., XN_Attribute::NUMBER
     * @param string $dir    optional ordering direction Defaults to null if $order is not specified, asc if order is specified
     * @param mixed $caching optional caching control information:
     *                       true: cache, use default max age and no additional invalidation keys
     *                       integer: cache, use provided integer as max age and no invalidation keys
     *                       array: cache, use optional 'maxAge' key as max age
     *                                     use optional 'keys' key as invalidation keys
     * @return array A two element array: 'users' => the requested users
     *                                    'numUsers' => the total number of users that match
     */
     public static function findUsers($filters, $begin = 0, $end = 10, $order = null, $dir = null, $caching = null) {
         //TODO: implement sort descriptors like in photos/videos [ywh 2008-05-13]
         return User::find($filters, $begin, $end, $order, $dir, $caching);
     }

    /**
     * Constructs a new XN_Query object for the given subject.
     *
     * @param $subject string specifies the subject of the query
     * @return XN_Query the XN_Query object
     */
    public function createQuery($subject) {
        return XN_Query::create($subject);
    }

    /**
     * Returns the content object with the given IDs.
     *
     * @param $ids array  content IDs
     * @return array  XN_Content objects
     */
    public function content($ids) {
        return XG_Cache::content($ids);
    }

}
