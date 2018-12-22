<?php

/**
 * Common code for saving and querying Photo objects
 */
class Photo_PhotoHelper {
    /** Constant for sorting by most popular. */
    const SORT_ORDER_MOSTPOPULAR  = 'mostPopular';
    /** Constant for sorting by highest rating. */
    const SORT_ORDER_HIGHESTRATED = 'highestRated';
    /** Constant for sorting by most recent. */
    const SORT_ORDER_MOSTRECENT   = 'mostRecent';
    /** Constant for sorting by most favorited. */
    const SORT_ORDER_MOSTFAVORITED   = 'mostFavorited';
    /** Constant for random ordering. */
    const SORT_ORDER_RANDOM = 'random';
    /** Cache key for number of photos to approve */
    const PHOTO_APPROVAL_COUNT_CHANGED = 'PHOTO_APPROVAL_COUNT_CHANGED';

    /** Cache key for all photos rss feed */
    const PHOTO_RSS = 'PHOTO_RSS';
    /** Cache key for tag rss feeds, to be concatenated with the tag as in PHOTO_RSS_TAG_wombat */
    const PHOTO_RSS_TAG = 'PHOTO_RSS_TAG_';
    /** Cache key for user x photos rss feeds, to be concatenated with the username as in PHOTO_RSS_CONTRIBUTOR_NingDev */
    const PHOTO_RSS_CONTRIBUTOR = 'PHOTO_RSS_CONTRIBUTOR_';
    /** Cache key for user x favorite photos rss feeds, to be concatenated with the username as in PHOTO_RSS_FAVORITES_NingDev */
    const PHOTO_RSS_FAVORITES = 'PHOTO_RSS_FAVORITES_';
    /** Cache key for photos of album x rss feeds, to be concatenated with the album id as in PHOTO_RSS_ALBUM_12345 */
    const PHOTO_RSS_ALBUM = 'PHOTO_RSS_ALBUM_';

    /** Cache key for all photos slideshow feed, to be used with the size as in PHOTO_SLIDESHOW_SMALL or PHOTO_SLIDESHOW_MINI */
    const PHOTO_SLIDESHOW = 'PHOTO_SLIDESHOW_';
    /** Cache key for user x photos slideshow feeds, to be used with the size and username as in PHOTO_SLIDESHOW_CONTRIBUTOR_SMALL_NingDev */
    const PHOTO_SLIDESHOW_CONTRIBUTOR = 'PHOTO_SLIDESHOW_CONTRIBUTOR_';
    /** Cache key for user x favorite photos slideshow feeds, to be used with the size and username as in PHOTO_SLIDESHOW_FAVORITES_SMALL_NingDev */
    const PHOTO_SLIDESHOW_FAVORITES = 'PHOTO_SLIDESHOW_FAVORITES_';
    /** Cache key for photos of album x slideshow feeds, to be used with the size and album id as in PHOTO_SLIDESHOW_ALBUM_SMALL_12345 */
    const PHOTO_SLIDESHOW_ALBUM = 'PHOTO_SLIDESHOW_ALBUM_';
    /** Cache key for tag slideshow feeds, to be used with the size and tag as in PHOTO_SLIDESHOW_TAG_MINI_wombatfood */
    const PHOTO_SLIDESHOW_TAG = 'PHOTO_SLIDESHOW_TAG_';

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
        return array(
                     self::SORT_ORDER_MOSTRECENT   => self::getMostRecentSortingOrder(),
                     self::SORT_ORDER_HIGHESTRATED => self::getHighestRatedSortingOrder(),
                     self::SORT_ORDER_MOSTPOPULAR  => self::getMostPopularSortingOrder(),
                     self::SORT_ORDER_RANDOM  => self::getRandomSortingOrder());
    }

    /**
     * Returns the descriptor of the most-popular sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getMostPopularSortingOrder() {
        return array('name'      => xg_text('MOST_POPULAR'),
                     'attribute'   => 'my->popularityCount',
                     'alias'     => self::SORT_ORDER_MOSTPOPULAR,
                     'direction' => 'desc',
                     'type'      => XN_Attribute::NUMBER,
                     'mainPageTitleKey' => 'MOST_POPULAR_PHOTOS');
    }

    /**
     * Returns the descriptor of the highest-rated sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getHighestRatedSortingOrder() {
        return array('name'      => xg_text('TOP_RATED'),
                     'attribute'   => 'my->ratingAverage',
                     'alias'     => self::SORT_ORDER_HIGHESTRATED,
                     'direction' => 'desc',
                     'type'      => XN_Attribute::NUMBER,
                     'mainPageTitleKey' => 'TOP_RATED_PHOTOS');
    }

    /**
     * Returns the descriptor of the most-recent sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getMostRecentSortingOrder() {
        return array('name'      => xg_text('LATEST'),
                     'attribute'   => 'createdDate',
                     'alias'     => self::SORT_ORDER_MOSTRECENT,
                     'direction' => 'desc',
                     'type'      => XN_Attribute::DATE,
                     'mainPageTitleKey' => 'LATEST_PHOTOS');
    }

    /**
     * Returns the descriptor of the most-favorited sorting order.
     * Note that this sort order is not included in the known sorting
     * orders as we do not offer it in the sort drop down.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getMostFavoritedSortingOrder() {
        return array('name'      => xg_text('MOST_FAVORITED'),
                     'attribute'   => 'my->favoritedCount',
                     'alias'     => self::SORT_ORDER_MOSTFAVORITED,
                     'direction' => 'desc',
                     'type'      => XN_Attribute::NUMBER,
                     'mainPageTitleKey' => null);
    }

    /**
     * Returns the descriptor of the random sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getRandomSortingOrder() {
        // Sort by createdDate. We will randomize the results using shuffle().
        // TODO: Change the sort metadata from arrays to strategy objects.
        // Then we can put the shuffling code in the strategy objects. [Jon Aquino 2008-02-12]
        return array('name'      => xg_text('RANDOM'),
                     'attribute'   => 'createdDate',
                     'alias'     => self::SORT_ORDER_RANDOM,
                     'direction' => 'desc',
                     'type'      => XN_Attribute::DATE,
                     'mainPageTitleKey' => 'RANDOM_PHOTOS');
    }

    /**
     * Creates a new photo.
     *
     * @return The new photo object
     */
    public static function create() {
        $photo = W_Content::create('Photo');
        self::initialize($photo);
        return $photo;
    }

    /**
     * Sets the initial attribute values for a Photo object
     *
     * $photo XN_Content|W_Content a newly created Photo object
     */
    public static function initialize($photo) {
        $photo->my->mozzle = W_Cache::current('W_Widget')->dir;
        $photo->my->visibility      = 'all';
        $photo->my->rotation        = 0;
        $photo->my->ratingCount     = 0;
        $photo->my->ratingAverage   = 0;
        $photo->my->viewCount       = 0;
        $photo->my->favoritedCount  = 0;
        $photo->my->popularityCount = 0;
    }

    /**
     * Loads a photo.
     *
     * @param id The id of the photo
     * @return The photo object if it exists, or null
     */
    public static function load($id, $useWContent=TRUE, $useCache = TRUE) {
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        return Photo_ContentHelper::findByID('Photo', $id, $useWContent, $useCache);
    }

    public static function adjacentPhoto($profile, $comparison, $photo) {
        $adjacentPhotos = self::adjacentPhotos($profile, $comparison, $photo, NULL);
        return count($adjacentPhotos) > 0 ? $adjacentPhotos[0] : NULL;
    }

    /**
     * Returns the n next (or previous) photos.
     *
     * @param $profile XN_Profile  the current user
     * @param $comparison string  < or >, for previous or next
     * @param $photo XN_Content|W_Content  the photo that the comparison is relative to
     * @param $idToFilterOut string  optional ID of a content object to exclude from the results
     * @param $begin integer  start index for the query; typically 0
     * @param $end integer  the number of adjacent photos to retrieve
     * @param $context string  name of the context: album, user, location, featured
     * @param $photoIds string  content IDs for Photos in an album
     */
    public static function adjacentPhotos($profile, $comparison, $photo, $idToFilterOut=NULL, $begin=0, $end = 2, $context = 'user', $photoIds = null) {
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_Context.php');
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'Photo');
        Photo_SecurityHelper::addVisibilityFilter($profile, $query);
        Photo_SecurityHelper::addApprovedFilter($profile, $query);
        // Filter original object out of the query results. Workaround for NING-3012  [Jon Aquino 2006-07-11]
        $query->filter('id', '<>', $photo->id);
        if ($idToFilterOut) { $query->filter('id', '<>', $idToFilterOut); }
        if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
        if ($context && ! Photo_Context::get($context)->filterQueryByContext($query, $comparison, $photo, $begin, $end, $photoIds)) { return array(); }
        /* Only cache if the user is not logged in and we want to cache order N
         * queries (@see BAZ-2969). If the user is logged in, then addVisibilityFilter
         * adds as FRIENDS() filter */
        // TODO: Allow caching when logged in, but use XG_QueryHelper::setMaxAgeForFriendsQuery [Jon Aquino 2008-09-17]
        if (XG_Cache::cacheOrderN() && (!XN_Profile::current()->isLoggedIn())) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Photo'));
        }
        return $query->execute();
    }

    /**
     * Return the next photo from this photo for this user in this context
     *
     * @param $profile XN_Profile the user
     * @param $photo XN_Content|W_Content the photo that we're comparing against
     * @param $context string name of the context: album, user, location, featured
     * @param $photoIds string content IDS for Photos in an album
     *
     * @see adjacentPhotos()
     *
     * @todo Long-term, refactor this code into a PhotoCollection object that
     *       has access to all of the Photos in the given collectino you are
     *       accessing
     */
    public static function getNextPhoto($profile, $photo, $context, $photoIds) {
        $return = reset(Photo_PhotoHelper::adjacentPhotos($profile, '<', $photo,NULL,0,1, $context, $photoIds));
        return $return;
    }

    /**
     * Return the previous photo from this photo for this user in this context
     *
     * @param $profile XN_Profile the user
     * @param $photo XN_Content|W_Content the photo that we're comparing against
     * @param $context string name of the context: album, user, location, featured
     * @param $photoIds string content IDS for Photos in an album
     *
     * @see adjacentPhotos()
     *
     * @todo Long-term, refactor this code into a PhotoCollection object that
     *       has access to all of the Photos in the given collectino you are
     *       accessing
     */
    public static function getPreviousPhoto($profile, $photo, $context, $photoIds) {
        $return = reset(Photo_PhotoHelper::adjacentPhotos($profile, '>', $photo,NULL,0,1, $context, $photoIds));
        return $return;
    }

    /**
     * Returns the given number of promoted photos.
     *
     * @param $n max number of photos to return
     * @param $sort sorting order
     * @return an array of XN_Content objects of type Photo
     */
    public static function getPromotedPhotos($n, $sort = null) {
        return self::getPromotedPhotosProper($n, $sort, true);
    }

    /**
     * Returns the given number of recent photos.
     *
     * @param $n max number of photos to return;
     * @param $sort sorting order
     * @param $promotedOrUnpromoted whether to return promoted or unpromoted photos
     * @return an array of XN_Content objects of type Photo
     */
    private static function getPromotedPhotosProper($n, $sort, $promotedOrUnpromoted) {
        if ($n == 0) { return array(); }
        $query = XN_Query::create('Content');
        $query->filter('type', '=', 'Photo');
        $query->filter('owner');
        $query->end($n);

        // BAZ-6710: only include public photos in promoted list
        if ($promotedOrUnpromoted) {
            $query->filter('my.visibility','=','all');
        } else {
            Photo_SecurityHelper::addVisibilityFilter(XN_Profile::current(), $query);
        }
        Photo_SecurityHelper::addApprovedFilter(XN_Profile::current(), $query);
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if($promotedOrUnpromoted){
            if (! XG_PromotionHelper::areQueriesEnabled()) { return array(); }
            XG_PromotionHelper::addPromotedFilterToQuery($query);
            if ($sort && $sort['alias'] == self::SORT_ORDER_RANDOM) {
                $query->order('random()');
            } else {
                $query->order('my->' . XG_PromotionHelper::attributeName(), 'desc', XN_Attribute::DATE);
            }
        } else {
            XG_PromotionHelper::addUnpromotedFilterToQuery($query);
            if ($sort && $sort['alias'] == self::SORT_ORDER_RANDOM) {
                $query->order('random()');
            } else {
                $query->order('createdDate', 'desc', XN_Attribute::DATE);
            }
        }

        // BAZ-6710: when displaying promoted, only invalidate the cache when
        // promotion changes
        if ($promotedOrUnpromoted) {
            $query = XG_Query::create($query);
            $query->setCaching(XG_CacheExpiryHelper::promotedObjectsChangedCondition('Photo'));
        } else if (!XN_Profile::current()->isLoggedIn()) {
            /* Only cache if the user is not logged in. If the user is logged in, then addVisibilityFilter
             * adds as FRIENDS() filter */
            // TODO: Allow caching when logged in, but use XG_QueryHelper::setMaxAgeForFriendsQuery [Jon Aquino 2008-09-17]
            // TODO: Set this explicitly using a $cache parameter [Jon Aquino 2008-02-06]
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Photo'));
        }

        return $query->execute();
    }

    /**
     * Returns an array of sorted photos.
     *
     * @param profile               The XN_Profile object of the user for whom the photos are queried for
     * @param filters               An array of filters to limit the returned photos:
     *                              'contributor'      => The contributor of the photo
     *                              'locationRequired' => Whether the photos need to have a location
     *                              'friends'          => Whether to return photos from friends only
     *                              'tag'              => A tag that the photos must have (case insensitive)
     *                              'tags'             => An array of tags of which the photos must have at least one (case insensitive)
     *                              'forApproval'      => Photos requiring Owner Approval
     *                              'ignoredIds'       => Ids of photos that should not be returned
     *                              'promoted'         => true to return only promoted photos, false to return only unpromoted photos
     *                              'location' => The location field (case-insensitive)
     *                              'searchTerms' => String of keywords to search on (using a Content query, not a Search query)
     *
     * @param $ids sort                  The sort descriptor as returned by getKnownSortingOrders(), or null to use the order given by
     * @param begin                 The number of the first photo to return
     * @param end                   The number of the photo after the last photo to return
     * @param needApprovalFilter    Whether a photo must have been approved (true), must not have been approved (false), or do not supply
     *                              this arg to get intelligent default based on $profile and $filters.
     * @return                      An array 'photos' => the photos, 'numPhotos' => the total number of photos that match the query
     */
    public static function getSortedPhotos($profile, $filters, $sort, $begin = 0, $end = 100, $needApprovedFilter = null) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (! XG_PromotionHelper::areQueriesEnabled() && $filters['promoted']) { return array('photos' => array(), 'numPhotos' => 0); }
        $query = self::createQueryForSortedPhotos($profile, $filters, $sort, $begin, $end, $needApprovedFilter);
        return array('photos' => $query->execute(), 'numPhotos' => $query->getTotalCount());
    }

    /**
     * Returns an array of sorted photos.
     *
     * @param profile               The XN_Profile object of the user for whom the photos are queried for
     * @param filters               An array of filters to limit the returned photos:
     *                              'contributor'      => The contributor of the photo
     *                              'locationRequired' => Whether the photos need to have a location
     *                              'friends'          => Whether to return photos from friends only
     *                              'tag'              => A tag that the photos must have (case insensitive)
     *                              'tags'             => An array of tags of which the photos must have at least one (case insensitive)
     *                              'forApproval'      => Photos requiring Owner Approval
     *                              'ignoredIds'       => Ids of photos that should not be returned
     *                              'promoted'         => true to return only promoted photos, false to return only unpromoted photos
     *                              'location' => The location field (case-insensitive)
     *                              'searchTerms' => String of keywords to search on (using a Content query, not a Search query)
     *
     * @param sort                  The sort descriptor as returned by getKnownSortingOrders(), or null to use the order given by $ids
     * @param begin                 The number of the first photo to return
     * @param end                   The number of the photo after the last photo to return
     * @param needApprovalFilter    Whether a photo must have been approved (true), must not have been approved (false), or do not supply
     *                              this arg to get intelligent default based on $profile and $filters.
     * @return XN_Query|XG_Query  A query to execute
     */
    protected static function createQueryForSortedPhotos($profile, $filters, $sort, $begin = 0, $end = 100, $needApprovedFilter = null) {
        // TODO: Refactor this large, complex function. [Jon Aquino 2008-02-13]
        // TODO: Change the sort metadata from arrays to strategy objects.
        // Then we can put the shuffling code in the strategy objects. [Jon Aquino 2008-02-12]
        $query = XN_Query::create('Content')
                         ->filter('type', '=', 'Photo')
                         ->filter('owner');
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        Photo_SecurityHelper::addVisibilityFilter($profile, $query);
        // If $needApprovedFilter is null then the client programmer is asking for the default behavior which
        // is to require it for all cases except photos for approval or "My Photos".
        if ($needApprovedFilter === null) {
            $needApprovedFilter = true;
            // we don't want the approved filter for the "My Photos" unless explicitly asked for.
            if ($filters && $filters['contributor'] && $profile->screenName == $filters['contributor']) {
                $needApprovedFilter = false;
            } else if ($filters && $filters['forApproval']) {
                $needApprovedFilter = false;
            }
        }
        $shouldCache = TRUE;
        if ($filters) {
            /*
             * Only cache if order N queries are allowed if any of the following
             * filters are in use: contributor, tag, ignoredIds
             */
             if ((isset($filters['contributor']) || isset($filters['tag']) || mb_strlen($filters['searchTerms']) ||
                 isset($filters['ignoredIds'])) && (! XG_Cache::cacheOrderN())) {
                $shouldCache = false;
             }
            if (isset($filters['promoted'])) {
                XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
                if ($filters['promoted']) {
                    XG_PromotionHelper::addPromotedFilterToQuery($query);
                    if (! $sort) {
                        $query->order('my->' . XG_PromotionHelper::attributeName(), 'desc', XN_Attribute::DATE);
                    }
                }
                else {
                    XG_PromotionHelper::addUnpromotedFilterToQuery($query);
                }
            }
            if ($filters['contributor']) {
                $query->filter('contributorName', 'eic', $filters['contributor']);
            } else if ($filters['friends']) {
                $query->filter('contributorName', 'in', XN_Query::FRIENDS());
            }
            if (mb_strlen($filters['location'])) {
                $query->filter('my->location', 'eic', $filters['location']);
            }
            if ($filters['locationRequired']) {
                $query->filter('my->lat', '<>', null);
                $query->filter('my->lat', '<>', '');
                $query->filter('my->lng', '<>', null);
                $query->filter('my->lng', '<>', '');
            }
            if (mb_strlen($filters['searchTerms'])) {
                XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
                XG_QueryHelper::addSearchFilter($query, $filters['searchTerms']);
            }
            if ($filters['tag']) {
                $query->filter('tag->value', 'eic', $filters['tag']);
            } else if ($filters['tags']) {
                $query->filter('tag->value', 'in', $filters['tags']);
            }
            if ($filters['ignoredIds']) {
                foreach ($filters['ignoredIds'] as $ignoredId) {
                    $query->filter('id', '<>', $ignoredId);
                }
            }
            if ($filters['forApproval']) {
                $query->filter('my->approved', '=', 'N');
            }
        }
        if ($needApprovedFilter) {
            Photo_SecurityHelper::addApprovedFilter($profile, $query);
        }
        if ($sort) {
            if ($sort['alias'] == self::SORT_ORDER_RANDOM) {
                $query->order('random()');
            } else {
                if ($sort['attribute'] == 'my->favoritedCount') {
                    $query->filter('my->favoritedCount', '>', 0);
                }
                $query->order($sort['attribute'], $sort['direction'], $sort['type']);
            }
        }
        $query->begin($begin);
        $query->end($end);
        $query->alwaysReturnTotalCount(true);
        if ($shouldCache) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Photo'));
            if (isset($filters['friends'])) {
                XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
                XG_QueryHelper::setMaxAgeForFriendsQuery($query);
            }
        }
        return $query;
    }

    /**
     * Returns specific photos.
     *
     * @param profile           The XN_Profile object of the user for whom the photos are queried for
     *                          If null, then visibility/privacy won't be checked
     * @param ids               The array of ids of the photos to return
     * @param sort              The sort descriptor as returned by getKnownSortingOrders()
     * @param begin             The number of the first photo to return
     * @param end               The number of the photo after the last photo to return
     * @param ignoreApproval    Allows the Approval check to be overridden for owner post-upload updates
     * @return An array 'photos' => the photos, 'numPhotos' => the total number of photos that match the query
     */
    public static function getSpecificPhotos($profile, $ids, $sort = null, $begin = 0, $end = 100, $ignoreApproval = false) {
        $query = XN_Query::create('Content');
            if ($profile) {
                W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
                Photo_SecurityHelper::addVisibilityFilter($profile, $query);
                if(!$ignoreApproval) {
                    Photo_SecurityHelper::addApprovedFilter($profile, $query);
                }
            }
        return self::getSpecificPhotosProper($query, $ids, $sort, $begin, $end);
    }

    /**
     * Returns specific photos.
     *
     * @param $query XG_Query|XN_Query  a partially initialized query
     * @param ids               The array of ids of the photos to return
     * @param sort              The sort descriptor as returned by getKnownSortingOrders()
     * @param begin             The number of the first photo to return
     * @param end               The number of the photo after the last photo to return
     * @return An array 'photos' => the photos, 'numPhotos' => the total number of photos that match the query
     */
    public static function getSpecificPhotosProper($query, $ids, $sort = null, $begin = 0, $end = 100) {
        if (count($ids) > 0) {
            $query->filter('type', '=', 'Photo');
            $query->filter('id', 'in', $ids);
            $query->filter('owner');
            if ($sort) {
                if ($sort['alias'] == self::SORT_ORDER_RANDOM) {
                    $query->order('random()');
                } else {
                    if ($sort['attribute'] == 'my->favoritedCount') {
                        $query->filter('my->favoritedCount', '>', 0);
                    }
                    $query->order($sort['attribute'], $sort['direction'], $sort['type']);
                }
            }
            if (isset($_GET['test_photo_count'])) { $photosPerPage = $end - $begin; $begin = 0; $end = 1; }

            //[Fabricio-2006-Nov-29] to sort albums and favorites by the order
            //of $ids it is necessary to get all results first (limited to 100 for the moment)
            //and slice after see PHO-500, PHO-651 :(

            if (($sort != null)){
                $query->begin($begin);
                $query->end($end);
            }
            $query->alwaysReturnTotalCount(true);
            $photos    = $query->execute();

            // If $sort is null, arrange $photos in the order of $ids
            if (is_null($sort)) {
                $idsAndObjects = array();
                foreach ($photos as $photo) {
                    $idsAndObjects[$photo->id] = $photo;
                }
                $photos = array();
                foreach ($ids as $id) {
                    if ($idsAndObjects[$id]) {
                        $photos[] = $idsAndObjects[$id];
                    }
                }
                if (count($ids) > $end - $begin) {
                    $photoSlice = array();
                    $counter = 0;
                    foreach ($photos as $photo) {
                        if (($counter < $end) && ($counter >= $begin)) {
                            $photoSlice[] = $photo;
                        }
                        $counter++;
                    }
                    $photos = $photoSlice;
                }
            }
            $numPhotos = $query->getTotalCount();
            if (isset($_GET['test_photo_count']) && count($photos) > 0) {
                $photo = $photos[0];
                $numPhotos = $_GET['test_photo_count'];
                $photos = array();
                for ($i = 0; $i < min($photosPerPage, $numPhotos); $i++) {
                    $photos[] = $photo;
                }
            }
            return array('photos' => $photos, 'numPhotos' => $numPhotos);
        } else {
            return array('photos' => array(), 'numPhotos' => 0);
        }
    }

    public static function getPendingPhotoCount() {
        if (Photo_SecurityHelper::isApprovalRequired()) {
            $query = XG_Query::create('Content')
                             ->filter('type', '=', 'Photo')
                             ->filter('owner')
                             ->filter('my->approved', '=', 'N')
                             ->end(1)
                             ->alwaysReturnTotalCount(true);

            $query->addCaching(XG_Cache::key('type','Photo'));
            $query->execute();
            return $query->getTotalCount();
        } else {
            return 0;
        }
    }

    public static function appContainsPhotos() {
        $query = XG_Query::create('Content')
                         ->filter('type', '=', 'Photo')
                         ->filter('owner')
                         ->end(1);
        $query->addCaching(XG_Cache::key('type','Photo'));
        return (count($query->execute()) > 0);
    }

    /**
     * Delete the specified photo updating the contributor's photo count and optionally
     * saving the user object.
     *
     * @param XN_Content(Photo) photo  The photo to delete
     * @param boolean saveUser  Whether to save the user object after update
     *
     * @returns integer  Number of photos removed
     */
    public static function delete($photo, $saveUser = true) {
        $user = Photo_UserHelper::load($photo->contributorName);
        if (($photo->my->approved !== 'N') && ! is_null($user)) {
            // We only increment photoCount for approved photos so don't decrement when deleting unapproved photo.
	        Photo_PhotoHelper::updatePhotoCount($user, $saveUser);
        }
        // If the photo was to-be-moderated, clear the approval-link cache
        if ($photo->my->approved == 'N') {
            W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
        }
        try {
            XN_Content::delete($photo);
            return 1;
        } catch (Exception $e) {
            // exception during delete
            return 0;
        }
    }

    /**
     * Deletes photos and their ancillary objects.
     *
     * @param $videos array The Video objects to delete.
     * @param $limit integer maximum number of content objects to remove (approximate).
     * @return the number of objects deleted
     */
    public static function deletePhotos($photos, $limit) {
        if (count($photos) > $limit) { $photos = array_slice($photos, 0, $limit); }
        if (count($photos) < 1) { return 0; }

        $users = XG_UserHelper::uniqueContributorUserObjects($photos);
        $numObjectsDeleted = 0;
        foreach ($photos as $photo) {
            $numObjectsDeleted += self::delete($photo, false);
        }
        // save the unique users once, instead of once per deletion
        foreach ($users as $user) {
            $user->save();
        }
        return $numObjectsDeleted;
    }

    /**
     * Returns the given person's tags for the photo.
     *
     * @return a comma-delimited list of tags, double-quoted when they contain commas, semicolons, or spaces
     */
    public static function getTagStringForUser($profile, $photo) {
        return $profile->isLoggedIn() ? XG_TagHelper::getTagStringForObjectAndUser($photo->id, $profile->screenName) : '';
    }

    public static function isAwaitingApproval($photo) {
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        return Photo_SecurityHelper::isApprovalRequired() && ($photo->my->approved == 'N');
    }

    /**
     * Returns a friendly description of the given visibility.
     *
     * @param visibility The visibility
     * @return The visibility description
     */
    public static function getVisibilityDescription($visibility) {
        switch ($visibility) {
            case 'friends':
                return xg_text('ONLY_MY_FRIENDS');
            case 'me':
                return xg_text('JUST_ME');
            default:
                return xg_text('ANYBODY');
        }
    }

    /**
     * Returns the visibilities and their friendly descriptions.
     *
     * @return An array visibility token => visibility description
     */
    public static function getVisibilityDescriptions() {
        return array('all'     => xg_text('ANYBODY'),
                     'friends' => xg_text('ONLY_MY_FRIENDS'),
                     'me'      => xg_text('JUST_ME'));
    }

    public static function getVisibilityText($photo, $profile) {
        $contributorName = $photo->contributorName;
        $myPhoto = ($contributorName == $profile->screenName);
        switch ($photo->my->visibility) {
            case 'friends':
                return $myPhoto ? xg_text('ONLY_MY_FRIENDS_CAN_SEE_PHOTO') : xg_text('ONLY_XS_FRIENDS_CAN_SEE_PHOTO', Photo_FullNameHelper::fullName($contributorName));
            case 'me':
                return $myPhoto ? xg_text('ONLY_I_CAN_SEE_PHOTO') : xg_text('ONLY_X_CAN_SEE_PHOTO', Photo_FullNameHelper::fullName($contributorName));
            default:
                return xg_text('EVERYONE_CAN_SEE_PHOTO');
        }
    }

    /**
     * Determines the MIME type and whether it is an type that this widget accepts.
     *
     * @param $mimeType string  the MIME type given in the POST variables, if any
     * @param $filename string  the filename of the uploaded file
     * @return string  the MIME type, or null if it is not a MIME type that this widget accepts
     */
    public static function imageMimeType($mimeType, $filename) {
        if (preg_match('@image@u', $mimeType)) {
            // Remove whitespace and the trailing ; that sometimes shows up in the
            // core-provided mime type
            return rtrim(trim($mimeType), ';');
        // If no mimetype was provided, guess from the filename [ David Sklar 2006-09-28 ]
        } elseif (preg_match('@\.jpe?g$@ui', $filename)) {
            return 'image/jpeg';
        } elseif (preg_match('@\.gif$@ui', $filename)) {
            return 'image/gif';
        } elseif (preg_match('@\.bmp$@ui', $filename)) {
            return 'image/bmp';
        } elseif (preg_match('@\.png$@ui', $filename)) {
            return 'image/png';
        } else {
            return null;
        }
    }

    /**
     * Creates a photo, sends notifications, and logs activity.
     * Before calling this method, check that imageMimeType() does not return null.
     *
     * @param $postVariableName string  The name of the file field containing the uploaded file.
     * @param $title string  title for the photo
     * @param $description string  description for the photo
     * @param $visibility string  access control: "all", "friends", or "me"
     * @param $tags string  comma-delimited list of tags, double-quoted when they contain commas, semicolons, or spaces
     */
    public static function upload($postVariableName, $title, $description, $visibility, $tags) {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        self::uploadProper(array(
                'postVariableName' => $postVariableName,
                'title' => $title,
                'description' => $description,
                'visibility' => $visibility,
                'tags' => $tags,
                'photoPhotoHelper' => new Photo_PhotoHelper(),
                'photoSecurityHelper' => new Photo_SecurityHelper(),
                'photoUserHelper' => new Photo_UserHelper(),
                'photoMessagingHelper' => new Photo_MessagingHelper(),
                'tagHelper' => new XG_TagHelper,
                'xgActivityHelper' => new XG_ActivityHelper()));
    }

    /**
     * Creates a photo, sends notifications, and logs activity.
     * Allows mock helpers to be passed in for unit testing.
     * Before calling this method, check that imageMimeType() does not return null.
     *
     * @param $args array
     *         postVariableName - name of the file field containing the uploaded file.
     *         title - title for the photo;
     *         description - description for the photo;
     *         visibility - access control: "all", "friends", or "me"
     *         tags - comma-delimited list of tags, double-quoted when they contain commas, semicolons, or spaces
     *         photoPhotoHelper - Photo_PhotoHelper or mock object
     *         photoSecurityHelper - Photo_SecurityHelper or mock object
     *         photoUserHelper - Photo_UserHelper or mock object
     *         photoMessagingHelper - Photo_MessagingHelper or mock object
     *         xgActivityHelper - XG_ActivityHelper or mock object
     */
    protected static function uploadProper($args) {
        $mimeType = self::imageMimeType($_POST[$args['postVariableName'] . ':type'], $_POST[$args['postVariableName']]);
        $approved = !Photo_SecurityHelper::failed($args['photoSecurityHelper']->checkCurrentUserIsAdmin(XN_Profile::current())) || !$args['photoSecurityHelper']->isApprovalRequired() ? 'Y' : 'N';
        $photo = $args['photoPhotoHelper']->create();
        $photo->set('data', $_POST[$args['postVariableName']], XN_Attribute::UPLOADEDFILE);
        $title = $args['title'] ? $args['title'] : xg_text('PHOTO_UPLOADED_ON_X', xg_date(xg_text('F_J_Y')));
        $photo->setTitle($title);
        $photo->setDescription($args['description']);
        $photo->setApproved($approved);
        $photo->my->mimeType = $mimeType;
        $user = $args['photoUserHelper']->load(XN_Profile::current());
        $photo->setVisibility($args['visibility']);
        $args['tagHelper']->updateTagsAndSave($photo, $args['tags']);
        self::logPhotoCreationProper($photo, $args['xgActivityHelper']);
        if ($approved == 'N') {
            $args['photoMessagingHelper']->photosAwaitingApproval(array($photo), $user->title);
        } else {
            $args['photoUserHelper']->addPhotos($user, 1);
        }
        $user->save();
    }

    public static function logPhotoCreation($photo, $save = true) {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        self::logPhotoCreationProper($photo, new XG_ActivityHelper(), $save);
    }

    protected static function logPhotoCreationProper($photo, $xgActivityHelper, $save = true) {
        if($photo->my->approved == 'Y' && !$photo->my->newContentLogItem && $photo->contributorName) {
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            $logItem = $xgActivityHelper->logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_PHOTO, $photo->contributorName, array($photo));
            $photo->my->newContentLogItem = $logItem->id;
            if ($save) {
                $photo->save();
            }
        }
    }

    /**
     * Takes an array of Photo objects and groups the objects by contributorName.
     * Returns an array of arrays; the first array is indexed by unique
     * contributorName and the second array contains the associated Photo objects
     * sorted by their original sort order.
     *
     * @param $photos   array of Photo  The input array of Photo objects
     * @return          array           An array of Photo objects grouped by contributor
     */
    public static function groupPhotosByContributor($photos) {
        $byContributor = array();
        foreach ($photos as $photo) {
            if (! array_key_exists($photo->contributorName, $byContributor)) {
                $byContributor[$photo->contributorName] = array();
            }
            array_push($byContributor[$photo->contributorName], $photo);
        }
        return $byContributor;
    }

    /**
     * Populates the xg_photo_photoCount field on the User object.
     *
     * @param $user XN_Content|W_Content  the User object to update
     * @param $save boolean  whether to save the User object
     */
    public static function updatePhotoCount($user, $save = TRUE) {
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        if (! XG_LockHelper::lock('update-photo-count-' . $user->title, 0)) { return; }
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $photoData = Photo_PhotoHelper::getSortedPhotos(Photo_UserHelper::createAnonymousProfile(), array('contributor' => $user->title), NULL , 0, 1, TRUE);
        $user->my->set('xg_photo_photoCount', $photoData['numPhotos'], XN_Attribute::NUMBER);
        if ($save) { $user->save(); }
    }
}
