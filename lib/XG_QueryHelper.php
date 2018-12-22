<?php

/**
 * Useful functions for working with queries.
 */
class XG_QueryHelper {

    /**
     * Adds a filter to the given query, restricting it to objects whose title and description combined contain
     * all of the given keywords
     *
     * @param $query XN_Query  The query to apply the filter to
     * @param $keywords string  Space-delimited set of keywords to search on (only the first 5 are considered)
     * @param $useSearch boolean Whether this is a search query or not (not == Content query)
     */
    public static function addSearchFilter($query, $keywords, $useSearch = false) {
        if ($useSearch) {
            $query->filter('fulltext','like',$keywords);
        }
        else {
            foreach (explode(' ', preg_replace('/\s+/u', ' ', trim($keywords)), 5) as $keyword) {
                $query->filter('fulltext', 'likeic', $keyword);
            }
        }
    }

    /**
     * Filters out objects marked as deleted. For example, Groups are hidden rather than deleted,
     * in case someone wants to resurrect a group.
     *
     * @param $query XN_Query  The query to apply the filter to
     */
    public static function addDeletedFilter($query, $isSearch = false) {
        $query->filter('my.deleted', $isSearch ? '!like' : '<>', 'Y');
    }

    /**
     * Filters out objects marked for exclusion from public search results. For example, invisible Groups,
     * Groups marked as deleted, and discussions in private groups should be excluded from public searches.
     * "Public searches" are searches that the general public can do; these include searches from the Ningbar,
     * the Ning system pages, the main Forum, and forums on public groups. On the other hand, searches from
     * forums on private groups are considered "private searches".
     *
     * @param $query XN_Query  The query to apply the filter to
     */
    public static function addExcludeFromPublicSearchFilter($query, $isSearch = false) {
        $query->filter('my.excludeFromPublicSearch', $isSearch ? '!like' : '<>', 'Y');
    }

    /**
     * Filters out objects whose given attribute has one of the given values.
     * This function may be inefficient - avoid using it if possible.
     *
     * @param $query XN_Query  the query to apply the filter to
     * @param $attributeName string  the name of the attribute
     * @param $values array  the values to filter out
     */
    public static function addNotInFilter($query, $attributeName, $values) {
        if (count($values) == 0) { return; }
        foreach ($values as $value) {
            $query->filter($attributeName, '<>', $value);
        }
    }

    /**
     * Which method to use for doing search queries. "content" is the old way,
     * using a Content query. "search" is the new way, using a Search query.
     * Initialized the first time getSearchMethod() is called.
     *
     * @see BAZ-1697
     */
    protected static $_searchMethod = null;

    /**
     * Which method to use for doing search queries. "content" is the old way,
     * using a Content query. "search" is the new way, using a Search query.
     * Defaults to "search", can be adjusted in individual networks with the
     * <searchMethod/> configuration variable in the admin widget
     *
     * @return string
     */
    public static function getSearchMethod() {
        if (is_null(self::$_searchMethod)) {
            /* Set the default */
            self::$_searchMethod = 'search';
            try {
                /* Override the default if there's something set in the
                 * admin widget */
                $adminWidget = W_Cache::getWidget('admin');
                if (mb_strlen($adminWidget->config['searchMethod'])) {
                    self::$_searchMethod = $adminWidget->config['searchMethod'];
                }
            } catch (Exception $e) {
                /* Don't change anything from the default if there's no
                 * admin widget */
            }
        }
        return self::$_searchMethod;
    }

    /**
     * Return an array of content objects corresponding to the content returned in the
     * search results. The returned array preserves the order of the search results.
     *
     * @param $searchResults array An array of XN_SearchResult objects
     * @param $splitTypes boolean optional Whether to chunk up the XN_SearchResults by
     *   type and do separate content queries for each group of types. If you know that
     *   there are only a few (< 5) different types in $searchResults, you can set this
     *   to false
     * @return array An array of XN_Content objects, one for each search result
    */
    public static function contentFromSearchResults($searchResults, $splitTypes = true) {
        $ids = array();
        /* If we're not splitting the types up, just do one query to get all of the content
         * and return it */
        if (! $splitTypes) {
            foreach ($searchResults as $k => $searchResult) {
                $ids[$k] = $searchResult->id;
            }
            try {
                $content = XG_Cache::content($ids);
            } catch (Exception $e) {
                $content = array();
            }
            return $content;
        }

        /* Otherwise, do the type-splitting */
        /* How many types per query do we want to allow? */
        $maxTypesPerQuery = 5;

        foreach ($searchResults as $searchResult) {
            $ids[$searchResult->type][$searchResult->id] = $searchResult->id;
        }
        $content = array();
        $unorderedContent = array();
        $numberOfTypes = count($ids);
        /* Each chunk consists of all of the IDs for up to $maxTypesPerQuery types */
        $idChunks = array();
        $typeCount = 0;
        $chunkIndex = 0;
        foreach ($ids as $type => $actualIds) {
            $typeCount++;
            if ($typeCount > $maxTypesPerQuery) {
                $typeCount = 1;
                $chunkIndex++;
            }
            if (isset($idChunks[$chunkIndex])) {
                $idChunks[$chunkIndex] = array_merge($idChunks[$chunkIndex], $actualIds);
            } else {
                $idChunks[$chunkIndex] = $actualIds;
            }
        }
        /* The IDs have now been split up into appropriate chunks, so we can load them
         * with one query per chunk */
        foreach ($idChunks as $chunk) {
            try {
                $newContent = XG_Cache::content($chunk);
                $unorderedContent = array_merge($unorderedContent, $newContent);
            } catch (Exception $e) {
            }
        }
        /* Make sure we return the content objects ordered as they were ordered in the
         * search query */
        foreach ($searchResults as $searchResult) {
            if ($unorderedContent[$searchResult->id]) {
                $content[] = $unorderedContent[$searchResult->id];
            }
        }
        return $content;
    }

    /**
     * Apply the specified filters to the specified query.
     *
     * @param $query XN_Query A Content, Invitation or other query.
     * @param $filters array An array of filters keyed by attribute name k. Each array element is either:
     *              'v' to filter on k = v
     *              array('op','v') to filter on k op v
     *              array('op','v','type') to filter on k op v type
     * @return XN_Query $query with the filters appllied.
     */
    public static function applyFilters($query, $filters) {
        foreach ($filters as $filterKey => $filterValue) {
            if ($filterValue instanceof XN_Query_InternalType_FilterClause) {
                $query->filter($filterValue);
            }
            else if (! is_array($filterValue)) {
                $query->filter($filterKey,'=',$filterValue);
            } else {
                $args = $filterValue;
                // If each element of $args is itself an array, that means there are multiple
                // filters to apply to this $filterKey
                if (is_array($args[0])) {
                    foreach ($args as $subArgs) {
                        array_unshift($subArgs, $filterKey);
                        call_user_func_array(array($query, 'filter'), $subArgs);
                    }
                } else {
                    array_unshift($args, $filterKey);
                    call_user_func_array(array($query, 'filter'), $args);
                }
            }
        }
        return $query;
    }

    /**
     * Determine the appropriate 'sort by' field, sort order and sort field type from a formatted string and optional
     * list of attribute names (the function contains the most common as defaults).
     *
     * @param   $s  String      In the format FIELD_DIRECTION where FIELD is 'name' OR 'status' OR 'date'
     *                          and DIRECTION is 'a' for ascending or 'd' for descending.
     * @param   $fields   array Override default attributes for each type by supplying them in this optional
     *                          array.  Example: array('date' => 'my->joinedDate').
     * @return  array           array[0] = atrribute to order by, array[1] = direction ('asc' or 'desc'),
     *                          array[2] = XN_Attribute type of the sort field
     */
    public static function sortOrder($s, $fields=array()) {
        if (! isset($fields['date'])) {
            $fields['date'] = array('createdDate', XN_Attribute::DATE);
        }
        if (! isset($fields['status'])) {
            $fields['status'] = array('my->memberStatus', XN_Attribute::NUMBER);
        }
        if (! isset($fields['name'])) {
            $fields['name'] = array('my->fullName', XN_Attribute::STRING);
        }
        if (! isset($fields['default'])) {
            $fields['default'] = $fields['date'];
        }
        if (! $s || mb_strlen($s) < 3) {
            return array($fields['default'][0], 'asc', $fields['default'][1]);
        }
        $field = mb_substr($s, 0, mb_strlen($s) - 2);
        if (isset($fields[$field])) {
            list($by, $type) = $fields[$field];
        } else {
            throw new Exception('Unknown sort by field: ' . $field);
        }
        $direction = (mb_substr($s, -1, 1) === 'd' ? 'desc' : 'asc');
        return array($by, $direction, $type);
    }

    const EXECUTE_AS_NEEDED_CHUNK_SIZE = 100;

    /**
     * Executes the query as many times as necessary to reach the desired end index.
     *
     * @param $query XN_Query  the query to execute
     * @param $end integer  the exclusive end index; allowed to be greater than 100
     */
    public static function executeAsNeeded($query, $end) {
        $totalCount = NULL;
        $resultArrays = array();
        do {
            $query->begin($start = count($resultArrays) == 0 ? 0 : $query->getResultTo());
            $query->end(min($start + XG_App::constant('XG_QueryHelper::EXECUTE_AS_NEEDED_CHUNK_SIZE'), $end));
            $query->alwaysReturnTotalCount(is_null($totalCount));
            $resultArrays[] = $query->execute();
            if (is_null($totalCount)) { $totalCount = $query->getTotalCount(); }
        } while ($query->getResultTo() < min($totalCount, $end));
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        return XG_LangHelper::arrayFlatten($resultArrays);
    }

    /**
     * Sets an acceptable TTL for the cache on a query that has a FRIENDS() filter.
     *
     * @param $query XG_Query  the cached query to modify
     */
    public static function setMaxAgeForFriendsQuery($query) {
        $query->maxAge(300);
    }
}
