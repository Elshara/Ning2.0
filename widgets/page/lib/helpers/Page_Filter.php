<?php

/**
 * A query filter that filters and sorts pages and Comments.
 * This is the well-known "Strategy" pattern: different objects
 * represent different algorithms (in this case, filtering and sorting).
 */
abstract class Page_Filter {

    /**
     * Retrieves the filter with the given name.
     *
     * @param $name string  Name of the filter: mostRecent, mostPopular, discussionsStarted, discussionsAddedTo
     */
    public static function get($name) {
        if (! self::$nameToFilterMap[$name]) {
            $className = 'Page_' . ucfirst($name) . 'Filter';
            self::$nameToFilterMap[$name] = new $className;
        }
        return self::$nameToFilterMap[$name];
    }

    /** Mapping of filter name to singleton filter instance */
    public static $nameToFilterMap = array();

    /**
     * Filters and sorts pages and Comments. Takes care of filtering the
     * owner, type, mozzle and contributorName; alwaysReturnTotalCount is turned on.
     *
     * @param $query XN_Query|XG_Query  The query to filter
     * @param $username string  Username to filter on (optional)
     * @return array  page and Comment objects
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
     */
    protected function addBasicFilters($query, $username) {
        $query->filter('owner');
        $query->filter('my.mozzle', '=', W_Cache::current('W_Widget')->dir);
        $query->alwaysReturnTotalCount(TRUE);
        if ($username) { $query->filter('contributorName', 'eic', $username); }
    }
}



/**
 * A query filter that filters in pages and Comments, sorted most recent first.
 */
class Page_MostRecentFilter extends Page_Filter {
    public function execute($query, $username = NULL) {
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Page');
        if ($query instanceof XG_Query) { $query->addCaching(XG_Cache::key('type', 'Page')); }
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        return $query->execute();
    }
    public function getDisplayText($username) {
        return xg_text('MOST_RECENT');
    }
}



/**
 * A query filter that filters in pages and Comments, sorted most popular first.
 */
class Page_MostPopularFilter extends Page_Filter {
    public function execute($query, $username = NULL) {
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Page');
        if ($query instanceof XG_Query) { $query->addCaching(XG_Cache::key('type', 'Page')); }
        $widget = W_Cache::current('W_Widget');
        $query->order('my->viewCount', 'desc', XN_Attribute::NUMBER);
        return $query->execute();
    }
    public function getDisplayText($username) {
        return xg_text('MOST_POPULAR');
    }
}



/**
 * A query filter that filters in pages started by the specified person, sorted most recent first.
 */
class Page_DiscussionsStartedFilter extends Page_Filter {
    public function execute($query, $username = NULL) {
        if (! $username) { throw new Exception('Assertion failed'); }
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Page');
        if ($query instanceof XG_Query) { $query->addCaching(XG_Cache::key('type', 'Page')); }
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        return $query->execute();
    }
    public function getDisplayText($username) {
        return $username == XN_Profile::current()->screenName ? xg_text('DISCUSSIONS_I_STARTED') : xg_text('DISCUSSIONS_X_STARTED', XG_UserHelper::getFullName(XG_Cache::profiles($username)));
    }
}



/**
 * A query filter that filters in pages commented on by the specified person, sorted most recent first.
 * Does not necessarily include pages started - just pages commented on.
 */
class Page_DiscussionsAddedToFilter extends Page_Filter {
    public function execute($query, $username = NULL) {
        if (! $username) { throw new Exception('Assertion failed'); }
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','PageCommenterLink');
        if ($query instanceof XG_Query) { $query->addCaching(XG_Cache::key('type', 'PageCommenterLink')); }
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        $pageCommenterLinks = $query->execute();
        $pageIds = array();
        foreach ($pageCommenterLinks as $pageCommenterLink) {
            $pageIds[] = $pageCommenterLink->my->pageId;
        }
        return self::pages($pageIds);
    }
    public function getDisplayText($username) {
        return $username == XN_Profile::current()->screenName ? xg_text('DISCUSSIONS_I_REPLIED_TO') : xg_text('DISCUSSIONS_X_REPLIED_TO', XG_UserHelper::getFullName(XG_Cache::profiles($username)));
    }
    /**
     * Retrieves the pages with the given IDs, sorted most recent first.
     *
     * @param $ids array  Content-object IDs
     * @return array  page objects
     */
    private static function pages($ids) {
        $query = XN_Query::create('Content');
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'Page'));
        }
        $query->filter('owner');
        $query->filter('type', '=', 'Page');
        $query->filter('id', 'in', $ids);
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        return $query->execute();
    }
}

