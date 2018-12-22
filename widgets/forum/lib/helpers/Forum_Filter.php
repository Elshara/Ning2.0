<?php

/**
 * A query filter that filters and sorts Topics and Comments.
 * This is the well-known "Strategy" pattern: different objects
 * represent different algorithms (in this case, filtering/sorting).
 *
 * Usage: Forum_Filter::get($filterName)->execute($query, $username)
 */
abstract class Forum_Filter {

    /**
     * Retrieves the filter with the given name.
     *
     * @param $name string  Name of the filter: mostRecent, mostRecentDiscussions, mostRecentlyUpdatedDiscussions, mostPopularDiscussions, promoted, discussionsStarted, discussionsAddedTo
     */
    public static function get($name) {
        if (! self::$nameToFilterMap[$name]) {
            $className = 'Forum_' . ucfirst($name) . 'Filter';
            self::$nameToFilterMap[$name] = new $className;
        }
        return self::$nameToFilterMap[$name];
    }

    /** Mapping of filter name to singleton filter instance */
    public static $nameToFilterMap = array();

    /**
     * Filters and sorts Topics and Comments. Takes care of filtering the
     * owner, type, mozzle and contributorName; alwaysReturnTotalCount is turned on.
     * If you pass in an XG_Query without invalidation keys specified, basic type-invalidation
     * keys will be added automatically.
     *
     * @param $query XN_Query|XG_Query  The query to filter
     * @param $username string  Username to filter on (optional)
     * @return array  Topic and Comment objects
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
        $widget = W_Cache::current('W_Widget');
        $query->filter('owner');
        $query->filter('my.mozzle', '=', $widget->dir);
        $query->filter('my.' . XG_App::widgetAttributeName($widget, 'deleted'), '=', null);
        if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
        if ($widget->privateConfig['allCategoriesDeletedOn']) { $query->filter('createdDate', '>', $widget->privateConfig['allCategoriesDeletedOn'], XN_Attribute::DATE); }
        $query->alwaysReturnTotalCount(TRUE);
        XG_GroupHelper::addGroupFilter($query);
        if ($username) { $query->filter('contributorName', 'eic', $username); }
    }
    /**
     * Returns whether the query is an XG_Query with no invalidation keys specified.
     *
     */
    protected static function needsInvalidationKeys($query) {
        return $query instanceof XG_Query && count($query->getCaching()) == 0;
    }
}



/**
 * A query filter that filters in Topics and Comments, sorted most recent first.
 */
abstract class Forum_AbstractMostRecentFilter extends Forum_Filter {
    /** Whether to return Topics and Comments, or just Topics. */
    private $includeComments;
    /** The attribute to sort on: createdDate or updatedDate */
    private $sortAttribute;
    /**
     * Constructor.
     *
     * @param $includeComments boolean  Whether to return Topics and Comments, or just Topics.
     * @param $sortAttribute string  The attribute to sort on: createdDate, updatedDate, my.lastEntryDate, etc.
     */
    public function __construct($includeComments = FALSE, $sortAttribute = 'createdDate') {
        $this->includeComments = $includeComments;
        $this->sortAttribute = $sortAttribute;
    }
    public function execute($query, $username = NULL) {
        $this->addBasicFilters($query, $username);
        if ($this->includeComments) {
            $query->filter(XN_Filter::any(XN_Filter('type','=','Topic'), XN_Filter('type','=','Comment')));
            if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Topic'), XG_Cache::key('type', 'Comment')); }
        } else {
            $query->filter('type','=','Topic');
            if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Topic')); }
        }
        $query->order($this->sortAttribute, 'desc', XN_Attribute::DATE);
        return $query->execute();
    }
    public function getDisplayText($username) {
        return xg_text('MOST_RECENT');
    }
}

/**
 * A query filter for Search queries (instead of content queries)
 */
class Forum_SearchFilter extends Forum_Filter {
    public function execute($query, $username = NULL) {
        $widget = W_Cache::current('W_Widget');
        $query->filter('my.mozzle', 'like', $widget->dir);
        $query->filter('my.' . XG_App::widgetAttributeName($widget, 'deleted'), '!like', 'Y');
        if (defined('UNIT_TESTING')) { $query->filter('my.test', 'like', 'Y'); }
        if ($widget->privateConfig['allCategoriesDeletedOn']) {
            // This will force a fallback to the content search
            // Forum_TopicController::prepareListAction() checks for this text to decide what to do
            throw new XN_Exception("Date range searching currently unsupported (BAZ-2459)");
            // $query->filter('createdDate', '>', $widget->privateConfig['allCategoriesDeletedOn'], XN_Attribute::DATE); }
        }
        $query->alwaysReturnTotalCount(TRUE);
        XG_GroupHelper::addGroupSearchFilter($query);
        if ($username) { $query->filter('contributorName', 'like', $username); }
        $query->filter(XN_Filter::any(XN_Filter('type','like','Topic'), XN_Filter('type','like','Comment')));
        /* no caching on search queries */
        return $query->execute();
    }

    public function getDisplayText($username) {
        return '';
    }
}

/**
 * A query filter that filters in Topics and Comments, sorted most recent first.
 */
class Forum_MostRecentFilter extends Forum_AbstractMostRecentFilter {
    public function __construct() {
        parent::__construct(true);
    }
}



/**
 * A query filter that filters in Topics (but not Comments), sorted most recent first.
 */
class Forum_MostRecentDiscussionsFilter extends Forum_AbstractMostRecentFilter {
    public function __construct() {
        parent::__construct(false);
    }
    public function getDisplayText($username) {
        return xg_text('NEWEST_DISCUSSIONS');
    }
}



/**
 * A query filter that filters in Topics (but not Comments), sorted by the lastEntryDate.
 * This is the creation date of the last comment, or if there are no comments, the creation date of the topic.
 */
class Forum_MostRecentlyUpdatedDiscussionsFilter extends Forum_AbstractMostRecentFilter {
    public function __construct() {
        parent::__construct(false, 'my.lastEntryDate');
    }
    public function getDisplayText($username) {
        return xg_text('LATEST_ACTIVITY');
    }
}


/**
 * A query filter that filters in Topics, sorted most popular first.
 */
class Forum_MostPopularDiscussionsFilter extends Forum_Filter {
    public function execute($query, $username = NULL) {
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Topic');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Topic')); }
        $widget = W_Cache::current('W_Widget');
        $query->filter('my->' . XG_App::widgetAttributeName($widget, 'commentCount'), '<>', NULL);
        $query->order('my->' . XG_App::widgetAttributeName($widget, 'commentCount'), 'desc', XN_Attribute::NUMBER);
        return $query->execute();
    }
    public function getDisplayText($username) {
        return xg_text('MOST_POPULAR');
    }
}



/**
 * A query filter that filters in promoted Topics and Comments, sorted most recent first.
 */
class Forum_PromotedFilter extends Forum_Filter {
    public function execute($query, $username = NULL) {
        if ($username) { throw new Exception('Assertion failed'); }
        $this->addBasicFilters($query, $username);
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_PromotionHelper::addPromotedFilterToQuery($query);
        $query->filter('type','=','Topic');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Topic')); }
        $query->order('my->' . XG_PromotionHelper::attributeName(), 'desc', XN_Attribute::DATE);
        return $query->execute();
    }
    public function getDisplayText($username) {
        return xg_text('FEATURED');
    }
}



/**
 * A query filter that filters in Topics started by the specified person, sorted most recent first.
 */
class Forum_DiscussionsStartedFilter extends Forum_Filter {
    public function execute($query, $username = NULL) {
        if (! $username) { throw new Exception('Assertion failed'); }
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Topic');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Topic')); }
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        return $query->execute();
    }
    public function getDisplayText($username) {
        return xg_text('DISCUSSIONS_STARTED');
    }
}


/**
 * A query filter that filters comments (replies) started by the specified person, sorted most recent first.
 */
class Forum_RecentRepliesMadeFilter extends Forum_Filter {
    public function execute($query, $username = NULL) {
        if (! $username) { throw new Exception('Assertion failed'); }
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','Comment');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'Comment')); }
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        return $query->execute();
    }
    public function getDisplayText($username) {
return xg_text('DISCUSSIONS_REPLIED_TO');
    }
}

/**
 * A query filter that filters in Topics commented on by the specified person, sorted most recent first.
 * Does not necessarily include Topics started - just Topics commented on. NB: not in use as of 3.3
 */
class Forum_DiscussionsAddedToFilter extends Forum_Filter {
    public function execute($query, $username = NULL) {
        if (! $username) { throw new Exception('Assertion failed'); }
        $this->addBasicFilters($query, $username);
        $query->filter('type','=','TopicCommenterLink');
        if (self::needsInvalidationKeys($query)) { $query->addCaching(XG_Cache::key('type', 'TopicCommenterLink')); }
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        $topicCommenterLinks = $query->execute();
        $topicIds = array();
        foreach ($topicCommenterLinks as $topicCommenterLink) {
            $topicIds[] = $topicCommenterLink->my->topicId;
        }
        return self::topics($topicIds);
    }
    public function getDisplayText($username) {
        return $username == XN_Profile::current()->screenName ? xg_text('DISCUSSIONS_I_REPLIED_TO') : xg_text('DISCUSSIONS_X_REPLIED_TO', XG_UserHelper::getFullName(XG_Cache::profiles($username)));
    }
    
    /**
     * Retrieves the topics with the given IDs, sorted most recent first.
     *
     * @param $ids array  Content-object IDs
     * @return array  Topic objects
     */
    private static function topics($ids) {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'Topic');
        $query->filter('id', 'in', $ids);
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        XG_GroupHelper::addGroupFilter($query);
        return $query->execute();
    }
}

