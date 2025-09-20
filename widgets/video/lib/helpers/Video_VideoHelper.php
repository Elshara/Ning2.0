<?php

/**
 * Common code for saving and querying Video objects
 */
class Video_VideoHelper {
    /** Constant for sorting by most popular. */
    const SORT_ORDER_MOSTPOPULAR  = 'mostPopular';
    /** Constant for sorting by highest rating. */
    const SORT_ORDER_HIGHESTRATED = 'highestRated';
    /** Constant for sorting by most recent. */
    const SORT_ORDER_MOSTRECENT   = 'mostRecent';
    /** Constant for sorting by most favorited. */
    const SORT_ORDER_MOSTFAVORITED   = 'mostFavorited';
    /** Constant for sorting by promoted. */
    const SORT_ORDER_PROMOTED   = 'promoted';
    /** Constant for random sorting order. */
    const SORT_ORDER_RANDOM = 'random';
    /** Cache key for number of videos to approve */
    const VIDEO_APPROVAL_COUNT_CHANGED = 'VIDEO_APPROVAL_COUNT_CHANGED';

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
        return array(self::SORT_ORDER_MOSTRECENT   => self::getMostRecentSortingOrder(),
                     self::SORT_ORDER_HIGHESTRATED => self::getHighestRatedSortingOrder(),
                     self::SORT_ORDER_MOSTPOPULAR  => self::getMostPopularSortingOrder(),
                     self::SORT_ORDER_RANDOM => self::getRandomSortingOrder());
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
					 'mainPageTitleKey' => 'MOST_POPULAR_VIDEOS');
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
					 'mainPageTitleKey' => 'TOP_RATED_VIDEOS');
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
					 'mainPageTitleKey' => 'LATEST_VIDEOS');
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
     */
    public static function getMostFavoritedSortingOrder() {
        return array('name'      => xg_text('MOST_FAVORITED'),
                     'attribute'   => 'my->favoritedCount',
                     'alias'     => self::SORT_ORDER_MOSTFAVORITED,
                     'direction' => 'desc',
                     'type'      => XN_Attribute::NUMBER);
    }

    /**
     * Returns the descriptor of the random sorting order.
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
    public static function getRandomSortingOrder() {
        return array('name' => xg_text('RANDOM'),
                     'alias'     => self::SORT_ORDER_RANDOM,
					 'mainPageTitleKey' => 'RANDOM_VIDEOS');
    }

    /**
     * Returns the descriptor of the promoted sorting order.
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
    public static function getPromotedSortingOrder() {
        return array('name' => xg_text('FEATURED'),
                     'alias'     => self::SORT_ORDER_PROMOTED,
					 'mainPageTitleKey' => 'PROMOTED_VIDEOS');
    }

    public static function query($profile, $page, $pageSize, $sort=null, $addVisibilityFilter=true) {
        $query = XN_Query::create('Content');
        if ($addVisibilityFilter) {
            Video_SecurityHelper::addVisibilityFilter($profile, $query);
            Video_SecurityHelper::addApprovedFilter($profile, $query);
        }
        Video_SecurityHelper::addConversionCompleteFilter($query);
        $query->filter('type', '=', 'Video');
        $query->filter('owner');
        if ($sort) {
            $query->order($sort['attribute'], $sort['direction'], $sort['type']);
        }
        $page = $page ? $page : 1;
        $start = ($page-1) * $pageSize;
        $query->begin($start);
        $query->end($start + $pageSize);
        $query->alwaysReturnTotalCount(true);
        if (!XN_Profile::current()->isLoggedIn()) {
            $query = XG_Query::create($query);
            $query->setCaching(XG_Cache::key('type','Video'));
        }
        return $query;
    }

    public static function embedCount($embedCode) {
        return max(preg_match_all('/<\s*\bobject\b/u', mb_strtolower($embedCode), $matches),
                   preg_match_all('/<\s*\bembed\b/u', mb_strtolower($embedCode), $matches));
    }

    /**
     * Returns the given number of promoted videos.
     *
     * @param $n max number of videos to return
     * @param $returnTuple boolean specifies whether an array of data and totalCount should be returned rather than just the data
     * @param $begin The number of the first video to return
     * @return an array of XN_Content objects of type Video or an array of content objects and the totalCount of objects
     */
    public static function getPromotedVideos($n, $returnTuple = false, $begin = 0) {
        return self::getPromotedVideosProper($n, true, $returnTuple, $begin);
    }

    /**
     * Returns the given number of recent videos.
     *
     * @param $n max number of videos to return;
     * @param $promotedOrUnpromoted whether to return promoted or unpromoted videos
     * @param $returnTuple boolean specifies whether an array of data and totalCount should be returned rather than just the data
     * @param $begin The number of the first video to return
     * @return an array of XN_Content objects of type Video or an array of content objects and the totalCount of objects
     */
    private static function getPromotedVideosProper($n, $promotedOrUnpromoted, $returnTuple = false, $begin = 0) {
        if ($n == 0) { return array(); }
        $query = XN_Query::create('Content');
        $query->filter('type', '=', 'Video');
        $query->filter('owner');
        $query->begin($begin);
        $query->end($n + $begin);
        if ($returnTuple) {
            $query->alwaysReturnTotalCount(true);
        }

        // When displaying promoted videos, we only display videos viewable
        // by everyone (BAZ-6710)
        if ($promotedOrUnpromoted) {
            $query->filter('my.visibility','=','all');
        } else {
            Video_SecurityHelper::addVisibilityFilter(XN_Profile::current(), $query);
        }
        Video_SecurityHelper::addApprovedFilter(XN_Profile::current(), $query);
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if($promotedOrUnpromoted){
            if (! XG_PromotionHelper::areQueriesEnabled()) { return $returnTuple ? array('videos' => array(), 'numVideos' => 0) : array(); }
            XG_PromotionHelper::addPromotedFilterToQuery($query);
            $query->order('my->' . XG_PromotionHelper::attributeName(), 'desc', XN_Attribute::DATE);
        } else {
            XG_PromotionHelper::addUnpromotedFilterToQuery($query);
            $query->order('createdDate', 'desc', XN_Attribute::DATE);
        }

        // BAZ-6710: cache this and only expire when promotion changes
        if ($promotedOrUnpromoted) {
            $query = XG_Query::create($query);
            $query->setCaching(XG_CacheExpiryHelper::promotedObjectsChangedCondition('Video'));
        } else if (!XN_Profile::current()->isLoggedIn()) {
            $query = XG_Query::create($query);
            $query->setCaching(XG_Cache::key('type','Video'));
        }

        if ($returnTuple) {
            $videos = $query->execute();
            $numVideos = $query->getTotalCount();
            return array('videos' => $videos, 'numVideos' => $numVideos);
        }
        return $query->execute();
    }

    /**
     * Returns an array of sorted videos.
     *
     * @param profile The XN_Profile object of the user for whom the videos are queried for
     * @param filters An array of filters to limit the returned videos:
     *                'contributor'      => The contributor of the video
     *                'locationRequired' => Whether the videos need to have a location
     *                'friends'          => Whether to return videos from friends only
     *                'tag'              => A tag that the videos must have (case insensitive)
     * @param sort    The sort descriptor as returned by getKnownSortingOrders()
     * @param begin   The number of the first video to return
     * @param end     The number of the video after the last video to return
     * @return An array 'videos' => the videos, 'numVideos' => the total number of videos that match the query
     */
    public static function getSortedVideos($profile, $filters, $sort, $begin = 0, $end = 100) {
        $query = XN_Query::create('Content')
                         ->filter('type', '=', 'Video')
                         ->filter('owner');
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        Video_SecurityHelper::addVisibilityFilter($profile, $query);
        $needApprovedFilter = true;

        /* Perhaps cache if the user is not logged in */
        $shouldCache = (! XN_Profile::current()->isLoggedIn()); // Should this not be $profile? [Jon Aquino 2008-09-19]

        $filters = $filters ? $filters : array();
        $includeUnconverted = !empty($filters['includeUnconvertedVideos']);
        if (!$includeUnconverted) {
            Video_SecurityHelper::addConversionCompleteFilter($query);
        }
        if ($filters) {
            /* Only cache if the current user is not logged in AND the friends
             * filter is not in use.
             *
             * Only cache if order N queries are allowed if any of the following
             * filters are in use: contributor, tag
             */
             // TODO: Allow caching for friends queries, but use XG_QueryHelper::setMaxAgeForFriendsQuery [Jon Aquino 2008-09-17]
             if (!empty($filters['friends'])) {
                 $shouldCache = false;
             }
             if (((isset($filters['contributor']) && $filters['contributor'] !== '') || (isset($filters['tag']) && $filters['tag'] !== '')) &&
                 (! XG_Cache::cacheOrderN())) {
                $shouldCache = false;
             }


            if (!empty($filters['contributor'])) {
                $query->filter('contributorName', 'eic', $filters['contributor']);
                // we don't want the approved filter for the "My Videos"
                if ($profile->screenName == $filters['contributor']) {
                    $needApprovedFilter = false;
                }
            } else if (!empty($filters['friends']) && XN_Profile::current()->isLoggedIn()) {  // Should this not be $profile? [Jon Aquino 2008-09-19]
                $query->filter('contributorName', 'in', XN_Query::FRIENDS());
            }
            $location = $filters['location'] ?? '';
            if (mb_strlen($location)) {
                $query->filter('my->location', 'eic', $location);
            }
            if (!empty($filters['locationRequired'])) {
                $query->filter('my->lat', '<>', null);
                $query->filter('my->lat', '<>', '');
                $query->filter('my->lng', '<>', null);
                $query->filter('my->lng', '<>', '');
            }
            if (!empty($filters['tag'])) {
                $query->filter('tag->value', 'eic', $filters['tag']);
            }
            $searchTerms = $filters['searchTerms'] ?? '';
            if (mb_strlen($searchTerms)) {
                XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
                XG_QueryHelper::addSearchFilter($query, $searchTerms);
            }
        }
        if ($needApprovedFilter) {
            Video_SecurityHelper::addApprovedFilter($profile, $query);
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
        if (isset($_GET['test_video_count'])) { $videosPerPage = $end - $begin; $begin = 0; $end = 1; }
        $query->begin($begin);
        $query->end($end);
        $query->alwaysReturnTotalCount(true);

        if ($shouldCache) {
            $query = XG_Query::create($query);
            $query->setCaching(XG_Cache::key('type','Video'));
        }
        $videos    = $query->execute();
        $numVideos = $query->getTotalCount();
        if (isset($_GET['test_video_count'])) { $numVideos = $_GET['test_video_count']; }
        if (isset($_GET['test_video_count']) && count($videos) > 0) {
            $video = $videos[0];
            $videos = array();
            for ($i = 0; $i < min($videosPerPage, $numVideos); $i++) {
                $videos[] = $video;
            }
        }
        return array('videos' => $videos, 'numVideos' => $numVideos);
    }

    /**
     * Returns specific videos.
     *
     * @param profile The XN_Profile object of the user for whom the videos are queried for
     * @param ids     The array of ids of the videos to return
     * @param $ids sort    The sort descriptor as returned by getKnownSortingOrders(), or null to use the order given by
     * @param begin   The number of the first video to return
     * @param end     The number of the video after the last video to return
     * @return The videos
     */
    public static function getSpecificVideos($profile, $ids, $sort, $begin = 0, $end = 100) {
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        if ($_GET['test_video_count'] === '0') { return array('videos' => array(), 'numVideos' => 0); }

        if ($_GET['test_ids']) { var_dump($ids); }
        if (count($ids) > 0) {
            $query = XN_Query::create('Content')
                             ->filter('type', '=', 'Video')
                             ->filter('id', 'in', $ids)
                             ->filter('owner');
            Video_SecurityHelper::addVisibilityFilter($profile, $query);
            Video_SecurityHelper::addApprovedFilter($profile, $query);
            Video_SecurityHelper::addConversionCompleteFilter($query);
            if ($sort) {
                if ($sort['alias'] == self::SORT_ORDER_RANDOM) {
                    $query->order('random()');
                } else {
                    if ($sort['attribute'] == 'my->favoritedCount') {
                        $query->filter('my->favoritedCount', '>', 0);
                    }
                    $query->order($sort['attribute'], $sort['direction'], $sort['type']);
                }
            $query->begin($begin);
            $query->end($end);
            }
            $query->alwaysReturnTotalCount(true);
            if (XG_Cache::cacheOrderN() && (!XN_Profile::current()->isLoggedIn())) {
                $query = XG_Query::create($query);
                $query->setCaching(XG_Cache::key('type','Video'));
            }
            $videos = $query->execute();
            if (! $sort) {
                $idsAndObjects = array();
                foreach ($videos as $video) {
                    $idsAndObjects[$video->id] = $video;
                }
                $videos = array();
                foreach($ids as $id) {
                    if ($idsAndObjects[$id]) {
                        $videos[] = $idsAndObjects[$id];
                    }
                }
                $videos = array_slice($videos, $begin, $end-$begin);
            }
            $numVideos = $query->getTotalCount();
            return array('videos' => $videos, 'numVideos' => $numVideos);
        } else {
            return array('videos' => array(), 'numVideos' => 0);
        }
    }

    /**
     * Returns a set of videos related to the current video.
     *
     * @param profile The XN_Profile object of the user for whom the videos are queried for
     * @param $video XN_Content|W_Content the Video to find related vids for
     * @param $max numeric the number of objects to retrieve
     *
     * defaults to returning the latest videos if it can't find any that are related. Tries to match the first given tag with searchText
     * if it exists and if the title is longer than 12 characters; otherwise does a match based on the title.
     * @return An array 'videos' => the videos, 'title' => the title of the related videos section; 'Related Videos' or 'Latest Videos'
     */

     // TODO: redo this to turn the title into an array, and do OR matches against each term in descneding order of term length
     public static function getRelatedVideos($profile, $video, $max = 5) {
         $title = xg_html('RELATED_VIDEOS');
         $query = self::query($profile, 1, $max, null);
         $query->filter('id', '<>', $video->id);
         if ($video->my->topTags && mb_strlen($video->title) > 12) {
             $tags = XN_Tag::parseTagString(trim($video->my->topTags));
             $query->filter('my->searchText', 'likeic', $tags[0]);
         } else {
             $query->filter('my->searchText', 'likeic', $video->title);
         }
         $results = $query->execute();
         if (!$results) {
             $title = xg_html('LATEST_VIDEOS');
             $query = self::query($profile, 1, $max, self::getMostRecentSortingOrder())
                 ->filter('id', '<>', $video->id);
             $results = $query->execute();
         }
         return array('videos'=>$results,'title'=>$title);
     }


    /**
     * Deletes a video (but not its comments).
     *
     * @param $video XN_Content|W_Content the Video to delete
     * @param $saveUser boolean whether to save the User object after adjusting its counts.
     *         Set to false if you are deleting several videos and will call save on the User object yourself.
     * @return the number of objects deleted (i.e. the video and any ancillary objects)
     */
    public static function delete($video, $saveUser = true) {
		$user = $video->contributorName ? Video_UserHelper::load($video->contributorName) : NULL;
        $numObjectsDeleted = 0;
        if ($video->my->sourceVideoAttachment) {
            XN_Content::delete(XG_Cache::content($video->my->sourceVideoAttachment));
            $numObjectsDeleted++;
        }
        if ($video->my->videoAttachment) {
            XN_Content::delete(XG_Cache::content($video->my->videoAttachment));
            $numObjectsDeleted++;
        }
        if ($video->my->previewFrame) {
            XN_Content::delete(XG_Cache::content($video->my->previewFrame));
            $numObjectsDeleted++;
        }
        // If the video was moderated and not yet approved, invalidate the approval-link cache
        if ($video->my->approved == 'N') {
            W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
        }
        try {
            XN_Content::delete($video);
            $numObjectsDeleted++;
        } catch (Exception $e) {
            // nothing - do not increment $numObjectsDeleted
        }
		if (!is_null($user)) {
			Video_VideoHelper::updateVideoCount($user, $saveUser);
        }
        return $numObjectsDeleted;
    }

    /**
     * Deletes videos and their ancillary objects.
     *
     * @param $videos array The Video objects to delete.
     * @param $limit integer maximum number of content objects to remove (approximate).
     * @return the number of objects deleted
     */
    public static function deleteVideos($videos, $limit) {
        if (count($videos) > $limit) { $videos = array_slice($videos, 0, $limit); }
        if (count($videos) < 1) { return 0; }

        $users = XG_UserHelper::uniqueContributorUserObjects($videos);
        $numObjectsDeleted = 0;
        foreach ($videos as $video) {
            $numObjectsDeleted += self::delete($video, false);
        }
        // save the unique users once, instead of once per deletion
        foreach ($users as $user) {
            $user->save();
        }
        return $numObjectsDeleted;
    }

    /**
     * Loads a video.
     *
     * @param id The id of the video
     * @return The video object if it exists, or null
     */
    public static function load($id, $useWContent=TRUE, $useCache = TRUE) {
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Video_ContentHelper.php');
        return Video_ContentHelper::findByID('Video', $id, $useWContent, $useCache);
    }

    public static function previewFrameDimensions($video) {
        if ($video->my->previewFrameWidth) { return array ($video->my->previewFrameWidth, $video->my->previewFrameHeight); }
        $previewFrame = Video_ContentHelper::findById('VideoPreviewFrame', $video->my->previewFrame);
        $previewFrameDimensions = $previewFrame->imageDimensions('data');
        $video->my->previewFrameWidth = $previewFrameDimensions[0];
        $video->my->previewFrameHeight = $previewFrameDimensions[1];
        $video->save();
        return $previewFrameDimensions;
    }



    public static function previewFrameWidth($video) {
        $previewFrameDimensions = self::previewFrameDimensions($video);
        return $previewFrameDimensions[0];
    }



    public static function previewFrameHeight($video) {
        $previewFrameDimensions = self::previewFrameDimensions($video);
        return $previewFrameDimensions[1];
    }


    /**
     * Return the preview frame dimensions that would fit a box of $boxWidth x $boxHeight,
     * use $upscale=true if you want to also upscale to fit, the default is to downscale only
     */
    public static function previewFrameDimensionsScaled($video, $boxWidth, $boxHeight, $upscale = false) {
        XG_App::includeFileOnce('/lib/XG_ImageHelper.php');
        $originalSize = self::previewFrameDimensions($video);
        list ($w, $h) = $originalSize;
        return XG_ImageHelper::getDimensionsScaled($w, $h, $boxWidth, $boxHeight, $upscale);
    }

    public static function fillExtent($width, $height, $minWidth, $minHeight) {
        $scaledHeight = $height * $minWidth / $width;
        if ($scaledHeight >= $minHeight) { return array($minWidth, intval($scaledHeight)); }
        $scaledWidth = $width * $minHeight / $height;
        return array(intval($scaledWidth), $minHeight);
    }


    public static function previewFrameUrl($video) {
        return self::previewFrameUrlProper($video, 'previewFrame', 'previewFrameUrl');
    }

    public static function previewFrameUrlProper($video, $previewFrameAttributeName, $previewFrameUrlAttributeName) {
        if ($video->my->$previewFrameUrlAttributeName) { return $video->my->$previewFrameUrlAttributeName; }
        if ($video->my->$previewFrameAttributeName) {
            try{
                W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_ContentHelper.php');
                $previewFrame = XG_Cache::content($video->my->$previewFrameAttributeName);
                $previewFrameUrl = $previewFrame->fileUrl('data');
            }catch(Exception $e){
                error_log('no previewFrame: '.$e->getMessage());
            }
        }
        if ($previewFrame && !$previewFrameUrl) {
            $widget = W_Cache::current('W_Widget');
            $previewFrameUrl = xg_cdn($widget->buildResourceUrl('gfx/placeholders/150_generic.gif'));
            $video->my->$previewFrameUrlAttributeName = $previewFrameUrl;
            $video->save();
        }
        if ($previewFrame && $previewFrame->isPrivate) {
            // Private URLs change  [Jon Aquino 2006-07-26]
            return $previewFrameUrl;
        }
        return $previewFrameUrl;
    }



    public static function embedPreviewFrameUrlAndMimeType($embedCode) {
        $include = 'xn-app://SnazzyApps/thumbnailUrl.php';
        if (! file_exists($include)) {
            // App core may be down  [Jon Aquino 2006-08-28]
            return null;
        }
        XG_App::includeFileOnce($include, false); /* No prefix, full path supplied */
        return thumbnail_url_and_mime_type($embedCode, $app);
    }



    public static function videoAttachmentUrl($video) {
        if ($video->my->videoAttachmentUrl) { return $video->my->videoAttachmentUrl; }
        $videoAttachment = Video_ContentHelper::findById('VideoAttachment', $video->my->videoAttachment);
        $videoAttachmentUrl = $videoAttachment->fileUrl('data');
        if ($videoAttachment->isPrivate) {
            // Private URLs change  [Jon Aquino 2006-07-26]
            return $videoAttachmentUrl;
        }
        $video->my->videoAttachmentUrl = $videoAttachmentUrl;
        $video->save();
        return $videoAttachmentUrl;
    }



    public static function conversionFailed($video, $reason) {
        $video->my->reasonForConversionFailure = $reason;
        $video->my->conversionStatus = 'failed';
        Video_LogHelper::log('Conversion of ' . $video->id . ' failed.');
        Video_LogHelper::log($reason);
    }



    public static function visibilityDescription($value) {
        if ($value == 'all') { return xg_text('ANYONE'); }
        if ($value == 'friends') { return xg_text('JUST_MY_FRIENDS'); }
        if ($value == 'me') { return xg_text('JUST_ME'); }
        throw new Exception('Shouldn\'t get here');
    }

    /**
     * Returns the given person's tags for the video.
     *
     * @return a comma-delimited list of tags, double-quoted when they contain commas, semicolons, or spaces
     */
    public static function getTagStringForUser($profile, $video) {
        return $profile->isLoggedIn() ? XG_TagHelper::getTagStringForObjectAndUser($video->id, $profile->screenName) : '';
    }



    public static function setVisibility($video, $visibility) {
        $video->my->visibility = $visibility;
        // Make the video and its supporting objects private to ensure they don't appear in the pivot and search results.  [Jon Aquino 2006-07-31]
        self::updatePrivacy($video);
    }

    private static function updatePrivacy($video) {
        $video->isPrivate = XG_App::contentIsPrivate() || $video->my->visibility != 'all' || Video_VideoHelper::isAwaitingApproval($video);
        if ($video->isPrivate) {
            // URLs for private objects are not constant  [Jon Aquino 2006-07-31]
            $video->my->previewFrameUrl = null;
            $video->my->videoAttachmentUrl = null;
        }
        if ($video->my->videoAttachment) {
            $videoAttachment = Video_ContentHelper::findById('VideoAttachment', $video->my->videoAttachment);
            if ($videoAttachment->isPrivate != $video->isPrivate) {
                $videoAttachment->isPrivate = $video->isPrivate;
                $videoAttachment->save();
            }
        }
        if ($video->my->sourceVideoAttachment) {
            $sourceVideoAttachment = Video_ContentHelper::findById('VideoAttachment', $video->my->sourceVideoAttachment);
            if ($sourceVideoAttachment->isPrivate != $video->isPrivate) {
                $sourceVideoAttachment->isPrivate = $video->isPrivate;
                $sourceVideoAttachment->save();
            }
        }
        if ($video->my->previewFrame) {
            $previewFrame = Video_ContentHelper::findById('VideoPreviewFrame', $video->my->previewFrame);
            if ($previewFrame->isPrivate != $video->isPrivate) {
                $previewFrame->isPrivate = $video->isPrivate;
                $previewFrame->save();
            }
        }
    }

    public static function setApproved($video, $approved) {
        $video->my->approved = $approved;
        self::updatePrivacy($video);
        // Invalidate the approval-link cache
        W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
    }

    public static function isAwaitingApproval($video) {
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        return Video_SecurityHelper::isApprovalRequired() && ($video->my->approved == 'N');
    }

    public static function completedVideosWithLocations($videos) {
        $videosWithLocation = array();
        foreach ($videos as $video) {
            // Check that status is complete (VID-455)  [Jon Aquino 2006-08-24]
            if ($video->my->conversionStatus != null && $video->my->conversionStatus != 'complete') { continue; }
            if ($video->my->lat && (mb_strlen($video->my->lat) > 0) &&
                $video->my->lng && (mb_strlen($video->my->lng) > 0)) {
                    $videosWithLocation[] = $video;
            }
        }
        return $videosWithLocation;
    }


    /**
     * Sets the embedCode's wmode to opaque, so action dialogs and other divs
     * can appear above the player.
     */
    public static function opaqueEmbedCode($embedCode) {
        return preg_replace('/<\/object>/u', '<param name="wmode" value="opaque" /></object>', preg_replace('/<embed /ui', '<embed wmode="opaque" ', $embedCode));
    }

    public static function disableScriptAccess($embedCode) {
        return
            preg_replace('/<\/object>/u', '<param name="allowScriptAccess" value="never" /></object>',
                preg_replace('/<embed /ui', '<embed allowScriptAccess="never" ',
                    preg_replace('/(allowscriptaccess)/ui', 'x-${1}', $embedCode)));
    }

    public static function thumbnailUrl($video, $thumbWidth, $thumbHeight=null) {
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_HtmlHelper.php');
        $widget = W_Cache::getWidget('video');
        if ($video->my->previewFrame && ! $_GET['test_embed_code']) {
            $url = Video_HtmlHelper::addParamToUrl(Video_VideoHelper::previewFrameUrl($video), 'width', $thumbWidth, false);
            if($thumbHeight) Video_HtmlHelper::addParamToUrl(Video_VideoHelper::previewFrameUrl($video), 'height', $thumbHeight, false);
            return $url;
        }
        if ($_GET['test_embed_code']) { $video->my->embedCode = $_GET['test_embed_code']; }
        if (preg_match('/youtube.com/u', $video->my->embedCode)) {
            return xg_cdn($widget->buildResourceUrl('gfx/placeholders/' . $thumbWidth . '_youtube.gif'));
        }
        if (preg_match('/google.com/u', $video->my->embedCode)) {
            return xg_cdn($widget->buildResourceUrl('gfx/placeholders/' . $thumbWidth . '_google.gif'));
        }
        if (preg_match('/ifilm.com/u', $video->my->embedCode)) {
            return xg_cdn($widget->buildResourceUrl('gfx/placeholders/' . $thumbWidth . '_ifilm.gif'));
        }
        return xg_cdn($widget->buildResourceUrl('gfx/placeholders/' . $thumbWidth . '_generic.gif'));
    }

    /**
     * Returns whether the filename's extension is that of a recognized video format.
     *
     * @param $filename string  the video's filename
     * @return boolean  whether the extension indicates that the file is a video
     */
    public static function hasVideoExtension($filename) {
        // Extension list from http://www.fileinfo.net/filetypes/video  [Jon Aquino 2006-12-11]
        $extensions = array('.3g2', '.3gp', '.3gp2', '.3gpp', '.3mm', '.60d', '.ajp', '.asf', '.asx', '.avi', '.avs', '.bik', '.bix', '.box', '.byu', '.cvc', '.dce', '.dif', '.dir', '.divx', '.dv', '.dvr-ms', '.dxr', '.eye', '.fla', '.flc', '.fli', '.flv', '.flx', '.gl', '.grasp', '.gvi', '.gvp', '.ifo', '.imovieproject', '.ivf', '.ivs', '.izz', '.lsf', '.lsx', '.m1v', '.m2v', '.m4e', '.m4u', '.m4v', '.mjp', '.mkv', '.moov', '.mov', '.movie', '.mp4', '.mpe', '.mpeg', '.mpg', '.mpv2', '.mswmm', '.mvb', '.mvc', '.nvc', '.ogm', '.omf', '.prproj', '.prx', '.qt', '.qtch', '.rm', '.rmvb', '.rp', '.rts', '.rts', '.scm', '.smil', '.smv', '.spl', '.ssm', '.svi', '.swf', '.tivo', '.vfw', '.vid', '.viewlet', '.viv', '.vivo', '.vob', '.vro', '.wm', '.wmd', '.wmv', '.wmx', '.wvx');
        foreach ($extensions as $extension) {
            if (preg_match('@\\' . $extension . '$@ui', $filename)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Populates the xg_video_videoCount field on the User object.
     *
     * @param $user XN_Content|W_Content  the User object to update
     * @param $save boolean  whether to save the User object
     */
    public static function updateVideoCount($user, $save = TRUE) {
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        if (! XG_LockHelper::lock('update-video-count-' . $user->title, 0)) { return; }
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_UserHelper.php');
        $videoData = Video_VideoHelper::getSortedVideos(Video_UserHelper::createAnonymousProfile(), array('contributor' => $user->title), NULL , 0, 1);
        $user->my->set('xg_video_videoCount', $videoData['numVideos'], XN_Attribute::NUMBER);
        if ($save) { $user->save(); }
    }

}
