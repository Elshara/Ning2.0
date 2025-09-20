<?php

/**
 * Dispatches requests pertaining to videos.
 */
class Video_VideoController extends W_Controller {
    /** The number of video thumbs on a two column view. */
    const NUM_THUMBS_ONECOLUMNVIEW = 10; //10; // 5 rows with 2 columns
    /** The number of video thumbs on a two column view. */
    const NUM_THUMBS_TWOCOLUMNVIEW = 20; // 10 rows with 2 columns
    /** The number of comments on the detail view. */
    const NUM_COMMENTS_DETAILVIEW  = 10;
    const NUM_THUMBS_RSS = 5;
    const APPROVAL_PAGE_SIZE = 6;
    /** The width in pixels of an embed that turns off the 'related videos' strip on the right side */
    const EMBED_RELATED_THRESHHOLD_WIDTH = 540;
    /** Invalidation condition indicating that the video-player logo has been updated. */
    const PLAYER_LOGO_CHANGED = 'PLAYER_LOGO_CHANGED';

    public function action_overridePrivacy($action) {
        $rssParam = $_GET['rss'] ?? '';
        $isRss = $rssParam === 'yes';

        return
            // Player feeds implement their own privacy mechanism [Jon Aquino 2006-12-09]
            $action == 'showPlayerConfig' ||
            $action == 'showFacebookPlayerConfig' ||
            // Called by the video transcoder [Jon Aquino 2006-10-24]
            $action == 'conversionUpdated' ||

            (! XG_App::appIsPrivate() && $action == 'rss') ||
            (! XG_App::appIsPrivate() && $action == 'listFavorites' && $isRss) ||
            (! XG_App::appIsPrivate() && $action == 'listForContributor' && $isRss) ||
            (! XG_App::appIsPrivate() && $action == 'listTagged' && $isRss);
    }

    protected function _before() {
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_LogHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_JsonHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_ColorHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_AppearanceHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_MessagingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_GeocodingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_TrackingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_PrivacyHelper.php');
        XG_App::includeFileOnce('/lib/XG_MapHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        Video_PrivacyHelper::checkMembership();
        Video_TrackingHelper::insertHeader();
        Video_HttpHelper::trimGetAndPostValues();
        Video_AppearanceHelper::updateFilesIfNecessary();
        Video_UserHelper::autoCreateAppOwnerUserObject();

        $this->pageSize = self::NUM_THUMBS_TWOCOLUMNVIEW;
        $this->sorts    = Video_VideoHelper::getKnownSortingOrders();
        $this->sort     = $this->getSortDescriptor();
    }

    /**
     * Returns the sort order description (one of the $this->sorts items) for the sort order
     * specified in the $_GET array.
     *
     * @param _GET['sort'] The current sort order
     * @return The sort descriptor, one of the $this->sorts items
     */
    private function getSortDescriptor() {
        $requested = $_GET['sort'] ?? null;

        if ($requested !== null && isset($this->sorts[$requested])) {
            return $this->sorts[$requested];
        }

        return $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT];
    }

    // @todo Move these queries out of the controller and into, for example, Video or VideoHelper.
    // Is there a reason they are actions instead of normal functions? [Jon Aquino 2007-06-26]

    public function action_getMostRecentVideo() {
       $query = Video_VideoHelper::query(null, 1, 1, $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT], false);
       $query->filter('my->approved', '=', 'Y');
       return $query->uniqueResult();
    }

    public function action_getMostRecentLocalVideo($screenName = NULL) {
       $query = Video_VideoHelper::query(null, 1, 1, $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT], false);
       $query->filter('my->approved', '=', 'Y');
       $query->filter('my->videoAttachment', '!=', NULL);
       if ($screenName) {
           $query->filter('contributorName', 'eic', $screenName);
       }
       return $query->uniqueResult();
    }

    public function action_getMostRecentPromotedVideo() {
       XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
       if (! XG_PromotionHelper::areQueriesEnabled()) { return null; }
       $query = Video_VideoHelper::query(null, 1, 1, $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT], false);
       XG_PromotionHelper::addPromotedFilterToQuery($query);
       $query->filter('my->approved', '=', 'Y');
       return $query->uniqueResult();
    }

    public function action_getMostRecentPromotedLocalVideo($screenName = NULL) {
       XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
       if (! XG_PromotionHelper::areQueriesEnabled()) { return null; }
       $query = Video_VideoHelper::query(null, 1, 1, $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT], false);
       XG_PromotionHelper::addPromotedFilterToQuery($query);
       $query->filter('my->approved', '=', 'Y');
       $query->filter('my->videoAttachment', '!=', NULL);
       if ($screenName) {
           $query->filter('contributorName', 'eic', $screenName);
       }
       return $query->uniqueResult();
    }

    public function action_getHighestRatedVideo() {
       $query = Video_VideoHelper::query(null, 1, 1, $this->sorts[Video_VideoHelper::SORT_ORDER_HIGHESTRATED], false);
       $query->filter('my->approved', '=', 'Y');
       return $query->uniqueResult();
    }

    public function action_getHighestRatedLocalVideo($screenName = NULL) {
       $query = Video_VideoHelper::query(null, 1, 1, $this->sorts[Video_VideoHelper::SORT_ORDER_HIGHESTRATED], false);
       $query->filter('my->approved', '=', 'Y');
       $query->filter('my->videoAttachment', '!=', NULL);
       if ($screenName) {
           $query->filter('contributorName', 'eic', $screenName);
       }
       return $query->uniqueResult();
    }

    public function action_index() {
        $this->forwardTo('list');
    }

    public function action_list() {
        $searchTerms = trim((string) ($_GET['q'] ?? ''));
        if ($searchTerms !== '') { return $this->forwardTo('search'); }
        if (! $this->_user->isLoggedIn()) {
            $this->setCaching(array(md5(XG_HttpHelper::currentUrl())), 300);
        }
        $this->handleSortingAndPagination(null, self::NUM_THUMBS_TWOCOLUMNVIEW);
        self::handleSortingAndPagination(null, self::NUM_THUMBS_TWOCOLUMNVIEW);
        Video_FullNameHelper::initialize($this->videos);
        if ($this->page == 1) {
            $this->featuredVideos = Video_VideoHelper::getPromotedVideos(6);
            if (count($this->featuredVideos) == 6) {
                $this->showViewAllFeaturedUrl = true;
                array_pop($this->featuredVideos);
                $this->preLoadVideoPreviewFrames($this->featuredVideos);
            }
        }
        $this->pageTitle = xg_text('VIDEOS');
        $this->title = xg_text('ALL_VIDEOS');
        $this->showFacebookMeta = array_key_exists('from', $_GET) && ($_GET['from'] === 'fb');
        if ($searchTerms !== '') { $this->title = xg_text('SEARCH_RESULTS'); }
    }

    /**
     * Displays all videos for the given search terms.
     */
    public function action_search() {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $searchTerms = trim((string) ($_GET['q'] ?? ''));
        if (XG_QueryHelper::getSearchMethod() == 'search') {
            try {
                $this->pageSize = self::NUM_THUMBS_TWOCOLUMNVIEW;
                $pageValue = $_GET['page'] ?? null;
                $pageNumber = is_numeric($pageValue) ? max(1, (int) $pageValue) : 1;
                $begin = XG_PaginationHelper::computeStart($pageNumber, $this->pageSize);
                $query = XN_Query::create('Search');
                $query->filter('type', 'like', 'Video');
                $query->begin($begin);
                $query->end($begin + $this->pageSize);
                $query->alwaysReturnTotalCount(true);
                XG_QueryHelper::addSearchFilter($query, $searchTerms, true);
                XG_QueryHelper::addExcludeFromPublicSearchFilter($query, true);
                $this->videos = XG_QueryHelper::contentFromSearchResults($query->execute(), false);
                $this->numVideos = $query->getTotalCount();
            } catch (Exception $e) {
                // According to David Sklar, the search core may throw an exception
                // while searchability is being added to an app without search. [Jon Aquino 2008-02-13]
                $filters = ($searchTerms !== '') ? array('searchTerms' => $searchTerms) : null;
                $this->handleSortingAndPagination($filters, 20);
            }
        } else {
            $filters = ($searchTerms !== '') ? array('searchTerms' => $searchTerms) : null;
            $this->handleSortingAndPagination($filters, 20);
                self::handleSortingAndPagination($filters, 20);
            }
        } else {
            $filters = ($searchTerms !== '') ? array('searchTerms' => $searchTerms) : null;
            self::handleSortingAndPagination($filters, 20);
        }
        Video_FullNameHelper::initialize($this->videos);
    }

    public function action_listFeatured() {
        if (! $this->_user->isLoggedIn()) {
            $this->setCaching(array(md5(XG_HttpHelper::currentUrl())), 300);
        }
        $this->sort = Video_VideoHelper::getPromotedSortingOrder();
        $this->handleSortingAndPagination();
        $this->pageTitle = xg_text('VIDEOS');
        $this->title = xg_text('FEATURED_VIDEOS');
    }

    /**
     * Displays all videos for a given location.
     */
    public function action_listForLocation() {
        $this->location = trim((string) ($_GET['location'] ?? ''));
        $this->pageSize = self::NUM_THUMBS_TWOCOLUMNVIEW;
        $filters = $this->location !== '' ? array('location' => $this->location) : null;
        $this->handleSortingAndPagination($filters, $this->pageSize);
        self::handleSortingAndPagination($filters, $this->pageSize);
        Video_FullNameHelper::initialize($this->videos);
        $rssParam = $_GET['rss'] ?? '';
        if ($rssParam === 'yes') {
            header("Content-Type: text/xml");
            $this->setCaching(array('video-video-rss-' . md5(XG_HttpHelper::currentUrl())), 1800);
            if (!empty($_GET['test_caching'])) { var_dump('Not cached'); }
            $this->title = xg_text('VIDEOS_FOR_LOCATION', $this->location);
            $this->link = 'http://' . $_SERVER['HTTP_HOST'];
            $this->render('rss');
            return;
        }
    }

    public function action_gfx() {
        Video_LogHelper::log('debug: ' . Video_HttpHelper::currentUrl());
        Video_LogHelper::log('referrer: ' . $_SERVER['HTTP_REFERER']);
        Video_LogHelper::log('screen name: ' . $this->_user->screenName);
    }


    public function action_listForApproval() {
        $this->pageSize = self::APPROVAL_PAGE_SIZE;
        if ($this->error = Video_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->bodyId = 'videos-to-approve';
        if (Video_SecurityHelper::isApprovalRequired()) {
            $pageValue = $_GET['page'] ?? null;
            $pageNumber = is_numeric($pageValue) ? max(1, (int) $pageValue) : 1;
            $this->query = Video_VideoHelper::query($this->_user, $pageNumber, $this->pageSize, $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT], false);
            $this->query->filter('my->approved', '=', 'N');
            $this->videos = $this->query->execute();
            if (count($this->videos) == 0 && $this->query->getTotalCount() > 0) {
                $this->redirectTo('listForApproval');
                return;
            }
        } else {
            $this->videos = array();
        }
        Video_FullNameHelper::initialize($this->videos);
    }



    public function action_rss() {
        $this->setCaching(array('video-video-rss-' . md5(XG_HttpHelper::currentUrl())), 1800);
        if (!empty($_GET['test_caching'])) { var_dump('Not cached'); }
        $query = Video_VideoHelper::query($this->_user, 1, self::NUM_THUMBS_RSS, $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT]);
        $this->videos = $query->execute();
        $this->title = xg_text('LATEST_VIDEOS');
        foreach ($this->videos as $video) {
            $thumbnail = '';
            if ($video->my->previewFrame) {
                ob_start(); ?>
                <a href="<?php echo $this->_buildUrl('video', 'show') . '?id=' . $video->id ?>">
                    <img src="<?php echo xnhtmlentities(Video_HtmlHelper::addParamToUrl(Video_VideoHelper::previewFrameUrl($video), 'width', 320, false)) ?>" width="320" alt="<%= xg_html('THUMBNAIL') %>" />
                </a>
                <?php
                $thumbnail = trim(ob_get_contents()) . '<br />';
                ob_end_clean();
            }
            $video->description = $thumbnail . xg_nl2br($video->description);
        }
        header('Content-Type: application/atom+xml');
    }


    public function action_listTagged() {
        $this->tag = trim((string) ($_GET['tag'] ?? ''));
        if ($this->tag === '') {
            $this->error = xg_text('NO_TAG_WAS_SPECIFIED');
            $this->render('error', 'index');
            return;
        }
        $rssParam = $_GET['rss'] ?? '';
        $isRss = $rssParam === 'yes';
        if ($isRss) {
            header("Content-Type: text/xml");
        }
        if ($isRss) { $this->sort = Video_VideoHelper::getMostRecentSortingOrder(); }
        $this->handleSortingAndPagination(array('tag' => $this->tag), $isRss ? self::NUM_THUMBS_RSS : self::NUM_THUMBS_TWOCOLUMNVIEW);
        self::handleSortingAndPagination(array('tag' => $this->tag), $isRss ? self::NUM_THUMBS_RSS : self::NUM_THUMBS_TWOCOLUMNVIEW);
        $this->rssTitle = xg_text('ALL_VIDEOS_TAGGED_X_X', $this->tag, XN_Application::load()->name);
        if ($isRss) {
            $this->title = $this->rssTitle;
            $this->link = $this->_buildUrl('video', 'listTagged', array('tag' => $this->tag));
            $this->render('rss');
            return;
        }
        $this->pageUrl = $this->_buildUrl('video', 'listTagged', array('tag' => $this->tag));
        Video_FullNameHelper::initialize($this->videos);
    }

    /**
     * Expected GET parameters:
     *     uploaded - whether to notify the user that her videos were successfully uploaded
     */
    public function action_listForContributor() {
        $requestedScreenName = trim((string) ($_GET['screenName'] ?? ''));
        if ($requestedScreenName !== '' && User::isMember($requestedScreenName)) {
            $this->user = Video_UserHelper::load($requestedScreenName);
        } else {
            XG_SecurityHelper::redirectIfNotMember();
            $this->user = Video_UserHelper::load($this->_user);
        }
        if (! $this->user) { throw new Exception(); }
        if ($requestedScreenName === '') {
            // Redirect; otherwise Bloglines bookmarklet will hit sign-in page when looking for RSS autodiscovery elements  [Jon Aquino 2006-09-29]
            $this->redirectTo('listForContributor', 'video', array('screenName' => $this->user->title));
            return;
        }
        $rssParam = $_GET['rss'] ?? '';
        $isRss = $rssParam === 'yes';
        if ($isRss) {
            header("Content-Type: text/xml");
            $this->setCaching(array('video-video-listForContributor-' . md5(XG_HttpHelper::currentUrl())), 1800);
            if (!empty($_GET['test_caching'])) { var_dump('Not cached'); }
        }
        $this->myOwnVideos = ($this->user->title == $this->_user->screenName);
        $this->title = $this->myOwnVideos ? xg_text('MY_VIDEOS') :
                            xg_text('XS_VIDEOS', Video_FullNameHelper::fullName($this->user->title));
        $this->pageTitle = $this->myOwnVideos ? xg_text('MY_VIDEOS') :
                            xg_text('XS_VIDEOS', Video_FullNameHelper::fullName($this->user->title));
        $this->pageUrl = $this->_buildUrl('video', 'listForContributor');
        $this->pageUrl = XG_HttpHelper::addParameter($this->pageUrl,'screenName',xnhtmlentities($this->user->title));

        if ($this->myOwnVideos || $isRss) {
            $requestedSort = $_GET['sort'] ?? null;
            $this->sort = ($requestedSort !== null && $requestedSort !== '') ? $this->sort : $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT];
        }//force merge conflict

        $this->handleSortingAndPagination(array('contributor'              => $this->user->title,
                                               'includeUnconvertedVideos' => $this->myOwnVideos),
                                         $isRss ? self::NUM_THUMBS_RSS : self::NUM_THUMBS_TWOCOLUMNVIEW);

        if ($isRss) {
            Video_FullNameHelper::initialize(array_merge($this->videos, array($this->user)));
            $this->title = xg_text('XS_VIDEOS_X',  Video_FullNameHelper::fullName($this->user->title), XN_Application::load()->name);
            $this->link = $this->_buildUrl('video', 'listForContributor', array('screenName' => $this->user->title));
            $this->render('rss');
            return;
        }

        $this->friends = Video_UserHelper::getFriends($this->user->title, 7, $numFriends);
        Video_FullNameHelper::initialize(array_merge($this->videos, array($this->user), $this->friends));
        if (!empty($_GET['uploaded'])) {
            if (XG_SecurityHelper::userIsAdmin(XN_Profile::current()) || !Video_SecurityHelper::isApprovalRequired()) {
                $this->uploadMessage = xg_text('VIDEOS_SUCCESSFULLY_UPLOADED');
            } else {
                $this->uploadMessage = xg_text('VIDEOS_UPLOADED_AWAITING', XN_Application::load()->name);
            }
        }
    }

    public function action_listFriends() {
        XG_SecurityHelper::redirectIfNotMember();
        $this->pageUrl = $this->_buildUrl('video', 'listFriends');
        $this->handleSortingAndPagination(array('friends' => true));
        $this->friends = Video_UserHelper::getFriends($this->_user->screenName, 7, $numFriends);
        $this->numFriends = $numFriends;
        Video_FullNameHelper::initialize(array_merge($this->friends, $this->videos));
    }

    /**
     * Handles pagination and sorting for the list actions.
     *
     * @param filters    The filters for selecting the videos
     *                   (see Video_VideoHelper::getSortedVideos)
     * @param numPerPage The number of thumbs per page
     */
    private function handleSortingAndPagination($filters = null, $numPerPage = self::NUM_THUMBS_TWOCOLUMNVIEW, $beginOffset = 0) {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $pageValue = $_GET['page'] ?? null;
        $pageNumber = is_numeric($pageValue) ? max(1, (int) $pageValue) : 1;
        $begin = XG_PaginationHelper::computeStart($pageNumber, $numPerPage);
        $begin = max(0, $begin + $beginOffset);
        if ($this->sort['alias'] == Video_VideoHelper::SORT_ORDER_PROMOTED) {
            $videosData = Video_VideoHelper::getPromotedVideos($numPerPage,true,$begin);
        } else {
            $videosData = Video_VideoHelper::getSortedVideos($this->_user,$filters,$this->sort,$begin,$begin + $numPerPage);
        }
        $this->videos   = $videosData['videos'];
        $this->page = 1 + (int)($begin / $numPerPage);
        $this->pageSize = $numPerPage;
        $this->isSortRandom = $this->sort['alias'] == Video_VideoHelper::SORT_ORDER_RANDOM;
        $this->numPages = $videosData['numVideos'] == 0 ? 1 : 1 + (int)(($videosData['numVideos'] - 1) / $numPerPage);
        $this->numVideos = $videosData['numVideos'];
        $this->sortOptions = $this->getSortOptions();
        $this->preLoadVideoPreviewFrames($this->videos);
    }

    /**
     * Loads the VideoPreviewFrames with a single query, to prevent them from being queried
     * one by one later in the request.
     *
     * @param $videos array  the Video objects
     */
    private function preLoadVideoPreviewFrames($videos) {
        $videoPreviewFrameIds = array();
        foreach ($videos as $video) {
            if ($video->my->previewFrame) { $videoPreviewFrameIds[] = $video->my->previewFrame; }
        }
        XG_Cache::content($videoPreviewFrameIds);
    }

    /**
     * Returns metadata for the Sort By combobox.
     *
     * @return array  array of arrays, each with displayText, url, and selected
     */
    private function getSortOptions() {
        $sortOptions = array();
        foreach (Video_VideoHelper::getKnownSortingOrders() as $key => $metadata) {
            $sortOptions[] = array(
                    'displayText' => $metadata['name'],
                    'url' => XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('sort' => $key, 'page' => null)),
                    'selected' => $key == $this->sort['alias']);
        }
        return $sortOptions;
    }

    /**
     * Determines whether there are videos with locations in the given array.
     *
     * @param videos The videos
     * @return true if there is at least one video with a location
     */
    private function areCompleteAndHaveLocations($videos) {
        return count(Video_VideoHelper::completedVideosWithLocations($videos)) > 0;
    }

    public function action_listFavorites() {
        $screenNameParam = trim((string) ($_GET['screenName'] ?? ''));
        if ($screenNameParam !== '' && User::isMember($screenNameParam)) {
            $this->user = Video_UserHelper::load($screenNameParam);
        } else {
            XG_SecurityHelper::redirectIfNotMember();
            $this->user = Video_UserHelper::load($this->_user);
        }
        if (!$this->user) { throw new Exception(); }
        if ($screenNameParam === '') {
            // Redirect; otherwise Bloglines bookmarklet will hit sign-in page when looking for RSS autodiscovery elements  [Jon Aquino 2006-09-29]
            $this->redirectTo('listFavorites', 'video', array('screenName' => $this->user->title));
            return;
        }
        $rssParam = $_GET['rss'] ?? '';
        $isRss = ($rssParam !== '' && $rssParam !== '0');
        if ($isRss) {
            header("Content-Type: text/xml");
            $this->setCaching(array('video-video-listFavorites-' . md5(XG_HttpHelper::currentUrl())), 1800);
            if (!empty($_GET['test_caching'])) { var_dump('Not cached'); }
        }
        $this->myOwnFavorites = ($this->user->title == $this->_user->screenName);
        $this->pageUrl = $this->_buildUrl('video', 'listFavorites', array('screenName' => $this->user->title));
        $numPerPage = $isRss ? self::NUM_THUMBS_RSS : self::NUM_THUMBS_TWOCOLUMNVIEW;
        $begin = 0;
        $pageValue = $_GET['page'] ?? null;
        if (is_numeric($pageValue) && (int) $pageValue > 0) {
            $begin = ((int) $pageValue - 1) * $numPerPage;
        }
        if ($isRss) {
            $requestedSort = $_GET['sort'] ?? null;
            $this->sort = ($requestedSort !== null && $requestedSort !== '') ? $this->sort : $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT];
        }
        $favoritesData = Video_VideoHelper::getSpecificVideos($this->_user,
                                                              Video_ContentHelper::ids($this->user, Video_UserHelper::attributeName('favorites')),
                                                              $this->sort['alias'] == Video_VideoHelper::SORT_ORDER_MOSTRECENT ? null : $this->sort,
                                                              $begin,
                                                              $begin + $numPerPage);
        $this->videos = $favoritesData['videos'];
        if ($isRss) {
            Video_FullNameHelper::initialize(array_merge($this->videos, array($this->user)));
            $this->title = xg_text('XS_FAVORITES_X', Video_FullNameHelper::fullName($this->user->title), XN_Application::load()->name);
            $this->link = $this->_buildUrl('video', 'listFavorites', array('screenName' => $this->user->title));
            $this->idToDateArray = count($this->videos) == 0 ? array() : array_combine(Video_ContentHelper::ids($this->user, Video_UserHelper::attributeName('favorites')), Video_ContentHelper::timestamps($this->user, Video_UserHelper::attributeName('favorites')));
            $this->render('rss');
            return;
        }
        $this->page      = 1 + (int)($begin / $numPerPage);
        $this->numPages  = $favoritesData['numVideos'] == 0 ? 1 : 1 + (int)(($favoritesData['numVideos'] - 1) / $numPerPage);
        $this->numVideos = $favoritesData['numVideos'];
        $this->pageTitle = $this->myOwnFavorites ? xg_text('MY_FAVORITE_VIDEOS') :
                    xg_text('XS_FAVORITE_VIDEOS', Video_FullNameHelper::fullName($this->user->title));
        $this->title = $this->myOwnFavorites ? xg_html('MY_FAVORITE_VIDEOS') :
                    xg_html('XS_FAVORITE_VIDEOS', xnhtmlentities(Video_FullNameHelper::fullName($this->user->title)));
        $this->sortOptions = $this->getSortOptions();
        $this->isSortRandom = $this->sort['alias'] == Video_VideoHelper::SORT_ORDER_RANDOM;
    }


    public function action_conversionStatus() {
        $videoId = $_GET['id'] ?? '';
        if ($videoId === '') {
            Video_JsonHelper::outputAndExit(array('conversionStatus' => 'failed'));
        }
        try {
            $video = Video_ContentHelper::findByID('Video', $videoId);
        } catch (Exception $e) {
            // Video likely deleted in conversionUpdated because of an error during conversion [Jon Aquino 2006-12-19]
            Video_JsonHelper::outputAndExit(array('conversionStatus' => 'failed'));
        }
        if ($this->error = Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $video)) {
            $this->render('error', 'index');
            return;
        }
        if ($video->my->conversionStatus != 'complete') {
            Video_JsonHelper::outputAndExit(array('conversionStatus' => $video->my->conversionStatus));
        }
        ob_start();
        $this->renderPartial('fragment_player', array('video' => $video, 'autoplay' => false, 'layout' => 'on_detail_page'));
        $playerHtml = trim(ob_get_contents());
        ob_end_clean();
        Video_JsonHelper::outputAndExit(array(
                'conversionStatus' => $video->my->conversionStatus,
                'playerHtml' => $playerHtml,
                'sharable' => Video_PrivacyHelper::canCurrentUserSeeShareLinks($video),
                'embeddable' => Video_SecurityHelper::isEmbeddable($video),
                'appPrivacyType' => Video_PrivacyHelper::getPrivacyType(),
                'embedHtml' => $this->embedCode));
    }


    public function action_show() {
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        if ($_GET['open_share_dialog'] == 'yes') {
            XG_SecurityHelper::redirectIfNotMember();
        }
        try {
            $this->video = Video_ContentHelper::findByID('Video', $_GET['id']);
        } catch (Exception $e) {
            $mainWidget = W_Cache::getWidget('main');
            $mainWidget->dispatch('error','404');
            exit;
        }

        $this->showRelated = false;

        if (!$this->_user->isLoggedIn()) {
            //  Users not logged in can see a cached page
            //  Detail page - cache only if we're caching order n items (BAZ-2969)
            if (XG_Cache::cacheOrderN()) {
                $cacheKey = md5(XG_HttpHelper::currentUrl());
                $this->setCaching(array($cacheKey), 300);
            }
        }
        if ($this->error = Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $this->video)) {
            $this->render('error', 'index');
            return;
        }
        XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
        if (XG_CommentHelper::feedAvailable($this->video)) {
            $this->commentFeedUrl = $this->_buildUrl('comment', 'feed', array('attachedTo' => $this->video->id, 'xn_auth' => 'no'));
        }
        $this->title = xg_text('VIDEO_DETAILS');
        $this->pageSize = self::NUM_COMMENTS_DETAILVIEW;
        $commentResults = Video_CommentHelper::getCommentsFor($this->video->id, $_GET['page'], $this->pageSize);
        $this->commentQuery = $commentResults['query'];
        $this->comments = $commentResults['comments'];
        $this->numComments = $commentResults['numComments'];
        $this->withLocation = ! is_null($this->video->my->lat);
        ob_start();
        $this->renderPartial('fragment_player', array('video' => $this->video, 'embedVisible' => Video_PrivacyHelper::canCurrentUserSeeShareLinks($this->video)?'on':'off','autoplay' => $_GET['autoplay'] != 'no' && $_GET['open_share_dialog'] != 'yes', 'layout' => 'on_detail_page'));
        $this->playerHtml = trim(ob_get_contents());
        ob_end_clean();
        if (preg_match('/(width)([:=" ]*)(\d+)/u', $this->playerHtml, $matches)) {
            $this->embedWidth = $matches[3];
        }
        if ($this->video->my->conversionStatus == 'in progress') {
            $this->embedWidth = '450';
        }

        $related = Video_VideoHelper::getRelatedVideos($this->_user,$this->video,5);
        $this->relatedVideos = $related['videos'];
        $this->relatedTitle = $related['title'];

        if ($this->relatedVideos && $this->embedWidth < self::EMBED_RELATED_THRESHHOLD_WIDTH) {
            $this->showRelated = true;
        }

        // Unlike photos, videos calls incrementViewCount immediately instead of after a 5-second
        // delay; otherwise the video may stutter. [Jon Aquino 2008-02-04]
        // TODO: Move this call before the setCaching() call; otherwise it won't be called
        // when the page is cached. But this would greatly increase the number of saves,
        // which will cause heavy database load on very active networks like thisis50. [Jon Aquino 2008-04-09]
        $this->video->incrementViewCount();
        // BAZ-1507: Don't invalidate cache here, or the cache gets blown away on each detail view
        XG_App::setInvalidateFromHooks(false);
        $this->video->save();
        XG_App::setInvalidateFromHooks(true);

        $this->context          = isset($_GET['context'])?$_GET['context']:'user';

        $friendStatusArray = Video_UserHelper::getFriendStatusFor($this->_user, array($this->video->contributorName));
        $this->friendStatus = $friendStatusArray[$this->video->contributorName];
        if ($_GET['test_conversion_status']) { $this->video->my->conversionStatus = $_GET['test_conversion_status']; }
        Video_FullNameHelper::initialize(array_merge($this->comments, $this->relatedVideos, array($this->video)));
        $this->tags = XG_TagHelper::getTagNamesForObject($this->video);

    $this->showFacebookMeta = array_key_exists('from', $_GET) && ($_GET['from'] === 'fb');

        // get tags for user if they're admin/NC or contributor
        if (XG_SecurityHelper::userIsAdminOrContributor($this->_user, $this->video)) {
            $this->currentUserTagString = XG_TagHelper::implode(XG_TagHelper::getTagNamesForObjectAndUser($this->video, $this->_user->screenName));
        }
    }

    /**
     * the facebook player config; use the facebook contentMode setting,
     * obtain the appropriate video and then dispatch showPlayerConfig
     */
    public function action_showFacebookPlayerConfig() {
        if (! Video_SecurityHelper::canAccessEmbeddableData($_GET)) {
            throw new Exception("Not allowed");
        }
        try {
            XG_App::includeFileOnce('/lib/XG_FacebookHelper.php');
            $dispType = XG_FacebookHelper::getFacebookDisplayType('video');
            if ($dispType === 'promoted') {
                $video = $this->action_getMostRecentPromotedLocalVideo();
            } else if ($dispType === 'rated') {
                $video = $this->action_getHighestRatedLocalVideo();
            } else {
                $video = $this->action_getMostRecentLocalVideo();
            }
            if (! is_object($video)) { throw new Exception("Returned video was not an object"); }
        } catch (Exception $e) {
            error_log('Error retrieving video content for Facebook player config: ' . $e->getMessage());
            exit;
        }
        $this->_widget->dispatch('video', 'showPlayerConfig', array(array_merge($_GET, array('id' => $video->id))));
    }


    /**
     * Displays the config XML for the video player.
     *
     * Expected GET parameters:
     *     id - ID of the video, or null for a blank video player (used in the Widget Gallery page)
     *     internalView -
     *     x - access code, for private apps
     */
    public function action_showPlayerConfig($args = array()) {
        foreach ($args as $k => $v) { $_GET[$k] = $v; }
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        if (! Video_SecurityHelper::canAccessEmbeddableData($_GET)) { throw new Exception("Not allowed"); }
                if ($_GET['internalView'] != 'true') {
            header("Content-Type: text/xml");
                }
        if ($_GET['id']) {
            $video = Video_ContentHelper::findByID('Video', $_GET['id']);
            if (! Video_SecurityHelper::isEmbeddable($video) && Video_SecurityHelper::failed(Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $video))) {
                throw new Exception();
            }
            ob_start();
            $this->_widget->dispatch('video', 'embeddableProper', array(array('id' => $video->id, 'width' => XG_EmbeddableHelper::EXTERNAL_VIDEO_PLAYER_WIDTH, 'height' => XG_EmbeddableHelper::EXTERNAL_VIDEO_PLAYER_HEIGHT, 'includeFooterLink' => true)));
            $this->embedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
            ob_end_clean();
            $this->videoAttachmentUrl = Video_VideoHelper::videoAttachmentUrl($video);
            $this->previewFrameUrl = Video_VideoHelper::previewFrameUrl($video);
            $this->videoSizeInBytes = $video->my->videoSizeInBytes;
            $this->videoId = $video->id;
        }
    }

    /**
     * Displays the video player in full-screen mode.
     *
     * Expected GET variables:
     *     id - the ID of the Video object
     */
    public function action_showFullScreen() {
        $this->id = $_GET['id'];
    }

    public function action_adjacentVideo() {
        try {
            $video = Video_ContentHelper::findByID('Video', $_GET['id']);
            $adjacentVideos = Video_VideoHelper::adjacentVideos($this->_user, $_GET['direction'] == 'previous' ? '<' : '>', $video, $_GET['idToFilterOut']);
            if (count($adjacentVideos) == 0) { Video_JsonHelper::outputAndExit(array('found' => false, 'more' => false)); }
            ob_start();
            $this->renderPartial('fragment_thumbnailForScrolling', array('video' => $adjacentVideos[0]));
            $html = trim(ob_get_contents());
            ob_end_clean();
            Video_JsonHelper::outputAndExit(array('found' => true, 'more' => count($adjacentVideos)>1, 'id' => $adjacentVideos[0]->id, 'html' => $html));
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_setTitle() {
        try {
            XG_SecurityHelper::redirectIfNotMember();
            XG_JoinPromptHelper::joinGroupOnSave();
            $video = Video_ContentHelper::findByID('Video', $_POST['id']);
            if ($this->error = Video_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $video)) {
                $this->render('error', 'index');
                return;
            }
            $title = mb_ereg_replace("[[:space:]]*(.*)[[:space:]]*", "\\1", $_POST['value']);
            if (mb_strlen($title) == 0) {
                $video->setTitle('untitled');
            } else {
                $video->setTitle(mb_substr($title, 0, 200));
            }
            $video->save();
            Video_JsonHelper::outputAndExit(array('html' => xnhtmlentities($video->title)));
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }



    public function action_tag() {
        XG_SecurityHelper::redirectIfNotMember();
        try {
            $video = Video_ContentHelper::findByID('Video', $_GET['id']);
            if (! XG_SecurityHelper::userIsAdminOrContributor($this->_user, $photo)) {
                $this->render('error', 'index');
                return;
            }
            XG_TagHelper::updateTagsAndSave($video, $_POST['tags']);

            $popularTags = XG_TagHelper::getTagNamesForObject($video->id, 6);
            $hasMoreTags = count($popularTags) > 5;
            $popularTags = array_slice($popularTags, 0, 5);

            ob_start();
            $this->renderPartial('fragment_listForDetailPage',
                                 'tag',
                                 array('tags'           => $popularTags,
                                       'videoId'        => $video->id,
                                       'hasMoreTags'    => $hasMoreTags));
            $html = trim(ob_get_contents());
            ob_end_clean();

            Video_JsonHelper::outputAndExit(array('html' => $html));
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_getTitle() {
        try {
            $video = Video_ContentHelper::findByID('Video', $_GET['id']);
            if ($this->error = Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $video)) {
                $this->render('error', 'index');
                return;
            }
            Video_JsonHelper::outputAndExit(array('html' => xnhtmlentities($video->title)));
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }



    public function action_setDescription() {
        try {
            XG_SecurityHelper::redirectIfNotMember();
            XG_JoinPromptHelper::joinGroupOnSave();
            $video = Video_ContentHelper::findByID('Video', $_POST['id']);
            if ($this->error = Video_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $video)) {
                $this->render('error', 'index');
                return;
            }
            $video->setDescription(mb_substr($_POST['value'], 0, 4000));
            $video->save();
            // Don't wrap the description in xnhtmlentities, as we want to display the raw HTML  [Jon Aquino 2006-07-17]
            Video_JsonHelper::outputAndExit(array('html' => xg_nl2br(xg_resize_embeds($video->description, 737))));
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }



    public function action_getDescription() {
        try {
            $video = Video_ContentHelper::findByID('Video', $_GET['id']);
            if ($this->error = Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $video)) {
                $this->render('error', 'index');
                return;
            }
            Video_JsonHelper::outputAndExit(array('html' => xg_nl2br($video->description)));
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_edit() {
        XG_SecurityHelper::redirectIfNotMember();
        $this->video = Video_ContentHelper::findByID('Video', $_GET['id']);
        if ($this->error = XG_SecurityHelper::userIsNotContributorError($this->_user, $this->video)) {
            $this->render('error', 'index');
            return;
        }
        $this->bodyId = 'add-photos';
        $this->tags = XG_TagHelper::getTagsForUser($this->_user->screenName, 25);
        $this->filename = $_GET['filename'];
        if (!$this->video->title && $this->filename) {
            $prefix = explode('.',$this->filename);
            if (count($prefix)) {
                $this->video->title = $prefix[0];
            }
        }
    }

    public function action_update() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        $video = Video_ContentHelper::findByID('Video', $_GET['id']);
        if ($this->error = XG_SecurityHelper::userIsNotContributorError($this->_user, $video)) {
            $this->render('error', 'index');
            return;
        }
        $this->updateWithPostValuesAndSave($video, $_POST);
       	Video_VideoHelper::updateVideoCount(Video_UserHelper::load($video->contributorName), true);
        $this->redirectTo('show', 'video', array('id' => $_GET['id']));
    }

    /**
     * Displays the form for a new video.
     *
     * @param $error string  An error message to display (optional).
     */
    public function action_new($error = NULL) {
        XG_SecurityHelper::redirectIfNotMember();
        if ($this->error = Video_SecurityHelper::checkCurrentUserCanAddVideos($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        XG_MediaUploaderHelper::setUsingMediaUploader(false);
        $this->errors = $error ? array($error) : NULL;
        $this->bodyId = 'add-photos';
        $this->hideBulkUploaderReferences = W_Cache::getWidget('main')->config['hideBulkUploader'] == 'yes';
        if (!Video_SecurityHelper::isApprovalRequired() ||
            !Video_SecurityHelper::failed(Video_SecurityHelper::checkCurrentUserIsAdmin($this->_user))) {
            $this->uploadingFragment = 'fragment_uploadingUnmoderated';
        } else {
            $this->uploadingFragment = 'fragment_uploadingModerated';
        }
        if ($_GET['test_approval_required']) {
            $this->uploadingFragment = $_GET['test_approval_required'] == 'yes' ? 'fragment_uploadingModerated' : 'fragment_uploadingUnmoderated';
        }
        $this->tags = XG_TagHelper::getTagsForUser($this->_user->screenName, 25);
        $app = XN_Application::load();
        $this->appName = $app->name;
    }

    /**
     * Displays the applet-based uploader.
     */
    public function action_newWithUploader() {
        if (W_Cache::getWidget('main')->config['hideBulkUploader'] == 'yes') {
            $this->redirectTo('new');
        }
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        XG_SecurityHelper::redirectIfNotMember();
        if ($this->error = Video_SecurityHelper::checkCurrentUserCanAddVideos($this->_user)) { return $this->render('error', 'index'); }
        XG_MediaUploaderHelper::setUsingMediaUploader(true);
    }

    /**
     * Redirects to the Media Uploader or the simple uploader, depending on the
     * capabilities of the browser. The current GET parameters will be preserved.
     */
    public function action_chooseUploader() {
        W_Cache::getWidget('main')->dispatch('mediauploader', 'chooseUploader');
    }

    /**
     * Displays the form for a new embedded video.
     *
     * @param $error string  An error message to display (optional).
     */
    public function action_addEmbed($error = NULL) {
        XG_SecurityHelper::redirectIfNotMember();
        if ($this->error = Video_SecurityHelper::checkCurrentUserCanAddVideos($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->errors = $error ? array($error) : NULL;
        $this->bodyId = 'add-photos';
        if (!Video_SecurityHelper::isApprovalRequired() ||
            !Video_SecurityHelper::failed(Video_SecurityHelper::checkCurrentUserIsAdmin($this->_user))) {
            $this->uploadingFragment = 'fragment_uploadingUnmoderated';
        } else {
            $this->uploadingFragment = 'fragment_uploadingModerated';
        }
        if ($_GET['test_approval_required']) {
            $this->uploadingFragment = $_GET['test_approval_required'] == 'yes' ? 'fragment_uploadingModerated' : 'fragment_uploadingUnmoderated';
        }
        $this->tags = XG_TagHelper::getTagsForUser($this->_user->screenName, 25);
        $this->embedCode = $_REQUEST['embedCode'];
        $app = XN_Application::load();
        $this->appName = $app->name;
    }


    /**
     * Displays the information for submitting a video by phone or email.
     *
     */
    public function action_addByPhone($error = NULL) {
        XG_SecurityHelper::redirectIfNotMember();
        if ($this->error = Video_SecurityHelper::checkCurrentUserCanAddVideos($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->errors = $error ? array($error) : NULL;
        $this->bodyId = 'add-photos';
        $app = XN_Application::load();
        $this->appName = $app->name;
    }


    /**
     * AJAX action for accessing the internal geocoder.
     *
     * @param _GET['address'] The address to geocode
     * @return A JSON-encoded array containing 'lat' and 'lng' or nothing if the
     *         address could not be geocoded
     */
    public function action_geocode() {
        if ($this->_user->isLoggedIn()) {
            $result = Video_GeocodingHelper::geocode($_GET['address']);

            try {
                Video_JsonHelper::outputAndExit($result);
            } catch (Exception $e) {
                Video_JsonHelper::handleExceptionInAjaxCall($e);
            }
        }
    }


    public function action_testYouTubeThumbnailExtractionScript() {
        header('Content-Type: text/plain');
        var_dump(Video_VideoHelper::embedPreviewFrameUrlAndMimeType('<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/2KrdBUFeFtY"></param><embed src="http://www.youtube.com/v/2KrdBUFeFtY" type="application/x-shockwave-flash" width="425" height="350"></embed></object>'));
    }


    public function action_testNingThumbnailExtractionScript() {
        header('Content-Type: text/plain');
        var_dump(Video_VideoHelper::embedPreviewFrameUrlAndMimeType('<embed src="http://networkcreators.ning.com/xn_resources/widgets/video/flvplayer/flvplayer.swf" FlashVars="config_url=http%3A%2F%2Fnetworkcreators.ning.com%2Fvideo%2Fvideo%2FshowPlayerConfig%3Fid%3D492224%3AVideo%3A188%26x%3DWaLpQm98u1aIP67Av7KvBiutbQ4zGNXE&share_btn=off&fullscreen_btn=off&app_link=on" width="426" height="356" scale="noscale" wmode="window" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"> <noembed>Ning Visualization</noembed> </embed>'));
    }

    /**
     * The uploaded file (if any) is expected to be a post variable named "file".
     *
     * @param $uploadName The name of the POST variable for the uploaded data (if any)
     * @param $args       Other arguments (typically $_POST): title, description, visibility,
     *                    locationType, address, lat, lng, zoomLevel, tags, embedCode
     * @return the newly created Video, or null if an error occurred.
     */
    private function createVideo($uploadName, $args) {
        $video = Video::create();
        Video_VideoHelper::setApproved($video,
                                       !Video_SecurityHelper::failed(Video_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) ||
                                       !Video_SecurityHelper::isApprovalRequired() ? 'Y' : 'N');
        if ($args['embedCode']) {
            if (Video_VideoHelper::embedCount($args['embedCode']) > 1) { throw new Exception('More than one embed code specified'); }
            $video->my->embedCode = Video_HtmlHelper::scrub(mb_substr($args['embedCode'], 0, 4000));
            if ($embedPreviewFrameUrlAndMimeType = Video_VideoHelper::embedPreviewFrameUrlAndMimeType($args['embedCode'])) {
                try {
                    $previewFrame = VideoPreviewFrame::createFromUrl($embedPreviewFrameUrlAndMimeType['url'], $embedPreviewFrameUrlAndMimeType['mimeType']);
                    $video->my->previewFrame = $previewFrame->id;
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    error_log($e->getTraceAsString());
                }
            }
        } elseif ($_POST[$uploadName . ':status']) {
            XG_App::includeFileOnce('/lib/XG_FileHelper.php');
            Video_VideoHelper::conversionFailed($video, XG_FileHelper::uploadErrorMessage($_POST[$uploadName . ':status']) . ' (status ' . $_POST[$uploadName . ':status'] . ')');
        } elseif ($_POST[$uploadName]) {
            $sourceVideoAttachment = VideoAttachment::create($_POST[$uploadName], $video, TRUE, $uploadName);
            $sourceVideoAttachment->save();
            $video->my->sourceVideoAttachment = $sourceVideoAttachment->id;
            $video->my->conversionStatus = 'in progress';
        } else {
            Video_VideoHelper::conversionFailed($video, xg_text('NEITHER_FILE_NOR_EMBED_CODE'));
        }
        if ($video->my->conversionStatus == 'failed') {
            Video_VideoHelper::delete($video);
            return null;
        }
        if ($_POST['featureOnMain']) {
            XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
            if (XG_PromotionHelper::currentUserCanPromote($video)) {
                XG_PromotionHelper::promote($video);
            }
        }
        $this->updateWithPostValuesAndSave($video, $args);

        if ($sourceVideoAttachment) {
            $sourceVideoAttachment->my->video = $video->id;
            $sourceVideoAttachment->save();
        }
        if ($previewFrame) {
            $previewFrame->my->video = $video->id;
            $previewFrame->save();
        }
        // This should be after the preview frame is saved so it can be used in
        // the "awaiting approval" message
        if ($args['embedCode'] && $video->my->approved == 'N') {
            Video_MessagingHelper::videoAwaitingApproval($video, $this->_user->screenName);
        }

        if ($video->my->conversionStatus == 'in progress') {
            $url = '/content(id=' . $video->my->sourceVideoAttachment . ')/convert';
            $postData = '<conversion xmlns="http://www.ning.com/atom/1.0"><callback>' . xg_xmlentities(XG_SecurityHelper::addCsrfToken($this->_buildUrl('video', 'conversionUpdated', '?id=' . $video->id))) . '</callback><specification>flv</specification></conversion>';
            XN_REST::post($url, $postData);
        }
        $user = Video_UserHelper::load($this->_user);
        // BAZ-3343 You only get credit for videos once they are approved.
        if ($video->my->approved !== 'N') {
        	Video_VideoHelper::updateVideoCount($user, false);
        }
        Video_UserHelper::set($user, 'lastUploadOn', date('c'), XN_Attribute::DATE);
        $user->save();
        $this->logVideoCreation($video);
        return $video;
    }

    // handler for the "quick post" feature
    public function action_createQuick () { # void
        if ($_POST['embedCode']) {
            if (preg_match('#^\s*(https?://\S+)#', $_POST['embedCode'], $m)) { // quick check that embedCode looks like URL
                $this->_widget->includeFileOnce('/lib/helpers/Video_ImportHelper.php');
                if ( !($info = Video_ImportHelper::parseVideoUrl(trim($m[1]))) || !$info['embedCode'] ) {
                    $this->status = 'fail';
                    $this->message = xg_html('CANNOT_ADD_YOUR_VIDEO_FROM_URL');
                    $this->render('blank');
                    return;
                }
                $_POST['embedCode'] = $info['embedCode'];
                $_POST['title'] = $info['title'];
                $_POST['description'] = $info['description'];
                //$_POST['tags'] = $info['tags'] ? '"' . join('","', $info['tags']) . '"' : '';
            }
        } elseif ($_POST['file']) {
            $_POST['title'] = preg_replace('#^.*[/\\\\]#', '', preg_replace('/\.\w+$/iu', '', $_POST['file']));
        }
        $this->action_create();
        $this->render('blank');
        if ($this->_video) { // _video is set if video was successfully created
            if ($this->_video->my->approved != 'Y') {
                $this->status = 'not_approved';
                $this->message = xg_html('YOUR_VIDEO_WAS_SUCCESSFULLY');
            } else {
                $this->status = 'ok';
                $this->viewUrl = $this->_buildUrl('video', 'show', array('id' => $this->_video->id));
                $this->viewText = xg_html('VIEW_THIS_VIDEO');
                $this->message = xg_html('YOUR_VIDEO_WAS_UPLOADED');
            }
            unset($this->_video);
        } else {
            $this->status = 'fail';
            $this->message = xg_html('CANNOT_ADD_YOUR_VIDEO');
        }
    }

    public function action_create() {
        // Used from action_createQuick()
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if ($this->error = Video_SecurityHelper::checkCurrentUserCanAddVideos($this->_user)) { return $this->render('error', 'index'); }
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        if ($_POST['file:status']) { return $this->forwardTo('new', 'video', array(XG_FileHelper::uploadErrorMessage($_POST['file:status']))); }
        $this->_video = $this->createVideo('file', $_POST);
        $this->redirectTo('edit', 'video', array(
                'filename' => preg_replace('@.*[\\\\/]@u', '', $_POST['file']),
                'new' => 'yes', 'id' => $this->_video->id, 'ts' => $_POST['embedCode'] ? 'Embedded Video' : 'Uploaded Video', 'tst' => time()));
    }

    /**
     * Processes data from the Media Uploader. The HTTP Status Code is set to 201
     * if the upload succeeded; 202 if it requires approval; 4xx or 5xx if a problem occurs.
     * If a problem occurs, an XML error description is output.
     */
    public function action_createWithUploader() {
        try {
            XG_App::includeFileOnce('/lib/XG_FileHelper.php');
            if (! User::isMember($this->_user)) { XG_MediaUploaderHelper::exitWithError('media-uploader:3'); }
            if ($_SERVER['REQUEST_METHOD'] != 'POST') { XG_MediaUploaderHelper::exitWithError('media-uploader:4'); }
            if ($error = Video_SecurityHelper::checkCurrentUserCanAddVideos($this->_user)) { XG_MediaUploaderHelper::exitWithError('media-uploader:2'); }
            if ($_POST['content:status']) { XG_MediaUploaderHelper::exitWithError('media-uploader:1', XG_FileHelper::uploadErrorMessage($_POST['content:status'])); }
            if (! Video_VideoHelper::hasVideoExtension($_POST['content'])) { XG_MediaUploaderHelper::exitWithError('media-uploader:5'); }
            $this->createVideo('content', $_POST);
            XG_MediaUploaderHelper::exitWithSuccess(!XG_SecurityHelper::userIsAdmin(XN_Profile::current()) && Video_SecurityHelper::isApprovalRequired());
        } catch (Exception $e) {
            XG_MediaUploaderHelper::exitWithError('media-uploader:6', $e->getMessage());
        }
    }

    public function logVideoCreation($video) {
        //create activity log item
        if(($video->my->approved == 'Y')&&(!($video->my->newContentLogItem)&&($video->contributorName))){
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            $logItem = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_VIDEO, $video->contributorName, array($video));
            $video->my->newContentLogItem = $logItem->id;
            $video->save();
        }
    }

    public function logVideoUpdate($video) {
        //create activity log item
        if( ($video) && ($video->my->approved == 'Y') && ($video->my->newContentLogItem)) {
            try {
                $logItem = XN_Content::load($video->my->newContentLogItem);
            } catch (Exception $e) {
                $video->my->newContentLogItem = null;
                $video->save();
                return;
            }
            $visibilityChoices = array('all', 'friends', 'me');
            if( ($logItem->my->visibility) && (array_search($video->my->visibility,$visibilityChoices) > array_search($logItem->my->visibility,$visibilityChoices)) ){
                $logItem->my->visibility = $video->my->visibility;
                $logItem->save();
            }
        }
    }

    public function action_createTestVideos() {
        if (XN_Profile::current()->screenName != 'JonathanAquino' && ! XG_SecurityHelper::userIsAdmin()) { throw new Exception(); }
        for ($i = 0; $i < $_GET['n']; $i++) {
            $video = $this->createVideo('file', array('visibility' => 'all', 'embedCode' => '<embed style="width:400px; height:326px;" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=7830246530742207581"> </embed>'));
            $video->title = $video->id;
            $video->save();
            echo $video->debugHTML();
        }
        echo 'Done';
    }

    /**
     * The uploaded file (if any) is expected to be a post variable named "file".
     *
     * @param $uploadName The name of the POST variable for the uploaded data (if any)
     * @param $args       Other arguments (typically $_POST): title, description, visibility,
     *                    locationType, address, lat, lng, zoomLevel, tags
     */
    private function updateWithPostValuesAndSave($video, $args) {
        $video->setTitle(mb_substr($args['title'], 0, 200));
        $video->setDescription(mb_substr($args['description'], 0, 4000));
        Video_VideoHelper::setVisibility($video, $args['visibility'] ? $args['visibility'] : Video_UserHelper::load($this->_user)->my->defaultVisibility);
        // location info
        $video->my->location = mb_substr($args['location'], 0, 200);
        if ($args['locationType'] == 'latlng' && $args['lat'] && $args['lng'] && $args['lat'] != 25 && $args['lng'] != -40) {
            if (! is_numeric($args['lat'])) { throw new Exception(); }
            if (! is_numeric($args['lng'])) { throw new Exception(); }
            if ($_POST['zoomLevel'] && ! is_numeric($args['zoomLevel'])) { throw new Exception(); }
            $video->my->lat          = $args['lat'];
            $video->my->lng          = $args['lng'];
            $video->my->locationInfo = $args['zoomLevel'];
        } else {
            $video->my->address = null;
            $video->my->lat = null;
            $video->my->lng = null;
            $video->my->locationInfo = null;
        }
        if (XG_PromotionHelper::isPromoted($video) && $video->my->visibility != 'all') { XG_PromotionHelper::remove($video); }
        XG_TagHelper::updateTagsAndSave($video, $args['tags']);
        $this->logVideoUpdate($video);
    }

    /**
     * Endpoint for uploading videos by mail. To enable it, you have to
     * define this URL mapping for the pattern '/xn/content(?:\?(.*))?' :
     *
     * /index.php/main/video/uploadByMail
     */
    public function action_uploadByMail() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();

        if (! preg_match('@video@u', $_POST['content:type']) && ! Video_VideoHelper::hasVideoExtension($_POST['content'])) { return false; }

        if ($this->_user->isLoggedIn()) {
            // The uploading user will be logged in automatically because we don't support/allow
            // anonymous uploading
            if (Video_PrivacyHelper::isAppBlockingCurrentUser()) {
                Video_MessagingHelper::uploadedToPrivateApp($this->_user, $_POST['subject']);
                return;
            }
            $uploader    = $this->_user;
            $title       = ($_POST['subject'] ? $_POST['subject'] : xg_text('VIDEO_UPLOADED_ON_X', xg_date(xg_text('F_J_Y'))));
            $description = $_POST['body'];
            $video = $this->createVideo('content', array('title' => $title,
                                                'description' => $description,
                                                //TODO Test that the visibility gets set properly  [Jon Aquino 2006-08-02]
                                                'visibility' => Video_UserHelper::load($uploader)->my->defaultVisibility  ));
            // Messaging is handled by Video_MessagingHelper in the transcoder callback  [Jon Aquino 2006-08-02]
            return true;
        }
    }

    /**
     * Deletes a video. Note that this action should only be called by doing a POST via AJAX as it returns nothing.
     */
    public function action_delete() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        $videoId = $_POST['id'] ?? '';
        if ($videoId === '') {
            header("HTTP/1.0 400 Bad Request");
            return;
        }
        $video = Video_ContentHelper::findByID('Video', $videoId);
        if (! $video) {
            header("HTTP/1.0 404 Not Found");
            return;
        }
        if (Video_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $video) == null) {
            Video_VideoHelper::delete($video);
            // Output something; otherwise dojo.io.bind does not seem to get a response
            // (something to do with the v2 layout?) [Jon Aquino 2006-12-03]
            Video_JsonHelper::outputAndExit(array());
        } else {
            header("HTTP/1.0 403 Forbidden");
        }
    }

    private function approveOrReject($video, $approve, $notifyContributor = true) {
        if ($approve) {
            Video_VideoHelper::setApproved($video, 'Y');
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            $logItem = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_VIDEO, $video->contributorName, array($video));
            $video->my->newContentLogItem = $logItem->id;
            $video->save();
            $contributor = User::load($video->contributorName);
            if (! is_null($contributor)) {
				Video_VideoHelper::updateVideoCount($contributor, TRUE);
                if ($notifyContributor) { Video_MessagingHelper::videoApproved($video); }
            }
        } else {
            if ($notifyContributor) { Video_MessagingHelper::videoRejected($video); }
            Video_VideoHelper::delete($video);
        }
    }

    /**
     * Displays the form for uploading a logo image.
     *
     * @param $error string  An error message to display.
     */
    public function action_customizePlayer($error = null) {
        if ($this->error = Video_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->currentHeader = NULL;
        $this->currentWatermark = NULL;
        if ($this->_widget->privateConfig['playerLogoType'] == 'header_image') {
            $this->currentHeader = $this->_widget->privateConfig['playerLogoUrl'];
        }
        else if ($this->_widget->privateConfig['playerLogoType'] == 'watermark_image') {
            $this->currentWatermark = $this->_widget->privateConfig['playerLogoUrl'];
        }
        $this->error = $error;
    }

    /**
     * Processes the form for uploading a logo image.
     */
    public function action_doCustomizePlayer() {
        $this->_widget->privateConfig['playerHeaderBackground'] = $_POST['player_header_background'];
        if ($this->error = Video_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) {
            $this->render('error', 'index');
            return;
        }

        XG_App::includeFileOnce('/lib/XG_FileHelper.php');

        if (isset($_POST['header_logo_file_action'])) {
            switch($_POST['header_logo_file_action']) {
                case 'add':
                    if ($_POST['header_logo_file:status'] > 0) {
                        $this->forwardTo('customizePlayer', 'video', array(XG_FileHelper::uploadErrorMessage($_POST['header_logo_file:status'])));
                        return;
                    }
                    else if ($_POST['header_logo_file']) {
                        $this->_widget->privateConfig['playerLogoUrl'] = VideoPlayerImage::updateWithPostValues('header_logo_file');
                        $this->_widget->privateConfig['playerLogoType'] = 'header_image';
                    } else {
                        throw new Exception('Action is add but header_logo_file not specified.');
                    }
                    break;
                case 'remove':
                    if($_POST['watermark_logo_file_action']=='remove'){
                        $this->_widget->privateConfig['playerImageUrl'] = "";
                        $this->_widget->privateConfig['playerLogoType'] = 'header_text';
                    }
                    break;
            }
        }
        if (isset($_POST['watermark_logo_file_action'])) {
            switch($_POST['watermark_logo_file_action']) {
                case 'add':
                    if ($_POST['watermark_logo_file:status'] > 0) {
                        $this->forwardTo('customizePlayer', 'video', array(XG_FileHelper::uploadErrorMessage($_POST['watermark_logo_file:status'])));
                        return;
                    }
                    else if ($_POST['watermark_logo_file']) {
                        $this->_widget->privateConfig['playerLogoUrl'] = VideoPlayerImage::updateWithPostValues('watermark_logo_file');
                        $this->_widget->privateConfig['playerLogoType'] = 'watermark_image';
                    } else {
                        throw new Exception('Action is add but header_logo_file not specified.');
                    }
                    break;
                case 'remove':
                    if($_POST['header_logo_file_action']=='remove'){
                        $this->_widget->privateConfig['playerLogoUrl'] = "";
                        $this->_widget->privateConfig['playerLogoType'] = 'header_text';
                    }
                    break;
            }
        }
        if ( (!isset($_POST['watermark_logo_file_action'])) && (!isset($_POST['header_logo_file_action']))) {
            throw new Exception('Neither header_logo_file nor watermark_logo_file specified.');
        }
        $this->_widget->privateConfig['playerHeaderBackground'] = $_POST['player_header_background'];

        $this->_widget->saveConfig();
        header('Location: ' . $this->getUrlForPostPlayerLogoCustomization());
    }

    /**
     * Returns the URL to go to after the site owner changes or removes the logo for the video player.
     *
     * @return string  The URL to redirect to.
     */
    private function getUrlForPostPlayerLogoCustomization() {
        return $this->_buildUrl('video', 'index');
    }

    /**
     * Removes the logo image from the player and puts the app name in the player header.
     */
    public function action_removePlayerLogo() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        if ($this->error = Video_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->_widget->privateConfig['playerLogoUrl'] = "";
        $this->_widget->privateConfig['playerLogoType'] = 'header_text';
        $this->_widget->saveConfig();
        header('Location: ' . $this->getUrlForPostPlayerLogoCustomization());
    }

    public function action_approve() {
        $videoId = $_GET['id'] ?? '';
        if ($videoId === '') { throw new Exception('Video id missing'); }
        $video = Video_ContentHelper::findByID('Video', $videoId);
        if (! $video) { throw new Exception('Video not found'); }
        if ($this->error = Video_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $isApproved = (($_GET['approved'] ?? '') === 'Y');
        $this->approveOrReject($video, $isApproved);
        $isJsonRequest = (($_GET['json'] ?? '') === 'yes');
        $targetParam = isset($_GET['target']) ? trim((string) $_GET['target']) : '';
        $targetUrl = ($targetParam !== '') ? $targetParam : $this->_buildUrl('video', 'index');
        if (! $isJsonRequest) {
            header('Location: ' . $targetUrl);
            exit;
        }
        $parsedUrl = parse_url($targetUrl);
        $urlParameters = array();
        if (is_array($parsedUrl) && isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $urlParameters);
        }
        $pageValue = $urlParameters['page'] ?? 1;
        $pageNumber = is_numeric($pageValue) ? max(1, (int) $pageValue) : 1;
        $query = Video_VideoHelper::query($this->_user, $pageNumber, self::APPROVAL_PAGE_SIZE, $this->sorts[Video_VideoHelper::SORT_ORDER_MOSTRECENT], false);
        $query->filter('my->approved', '=', 'N');
        $videos = $query->execute();
        ob_start();
        $paginationValues = Video_HtmlHelper::pagination($query->getTotalCount(), self::APPROVAL_PAGE_SIZE, $targetUrl);
        $this->renderPartial('fragment_pagination', 'video', $paginationValues);
        $this->pagination = trim(ob_get_contents());
        ob_end_clean();
        $this->html = '';
        if (count($videos) == self::APPROVAL_PAGE_SIZE) {
            ob_start();
            $this->renderPartial('fragment_approvalListPlayer', array('video' => $videos[count($videos)-1], 'currentUrl' => $targetUrl));
            $this->html = trim(ob_get_contents());
            ob_end_clean();
        }
        if (count($videos) == 0) {
            ob_start();
            $this->renderPartial('fragment_noVideosToApprove');
            $this->html = trim(ob_get_contents());
            ob_end_clean();
        }
        Video_JsonHelper::outputAndExit(array('page' => intval($paginationValues['curPage']), 'html' => $this->html, 'pagination' => $this->pagination, 'currentPageVideoCount' => count($videos)));
    }


    public function action_conversionUpdated() {
        // Output something, to work around 404'ing on no output (BAZ-827) [Jon Aquino 2006-12-19]
        echo '.';

        // There is a chance that the person has already deleted the video  [Jon Aquino 2006-07-05]
        $video = Video_ContentHelper::findById('Video', $_GET['id']);
        if ($_REQUEST['xn_progress_string']) {
            //TODO: Report progress on detail page  [Jon Aquino 2006-07-24]
            return;
        }
        // Protect against some forms of hacking  [Jon Aquino 2006-06-29]
        if ($video->my->conversionStatus != 'in progress') {
            exit;
        }
        Video_FullNameHelper::initialize(array($video));

        if ($_REQUEST['xn_error']) {
            Video_VideoHelper::conversionFailed($video, xg_text('PROBLEM_OCCURRED_CONVERTING_VIDEO', $_REQUEST['xn_error']));
        } elseif ($_POST['xn_converted_video:status']) {
            Video_VideoHelper::conversionFailed($video, xg_text('STATUS_N', $_POST['xn_converted_video:status']) . ' (xn_converted_video: ' . $_POST['xn_converted_video'] . ')');
        } elseif ($_POST['xn_converted_video_frame:status']) {
            Video_VideoHelper::conversionFailed($video, xg_text('STATUS_N', $_POST['xn_converted_video_frame:status']) . ' (xn_converted_video_frame: ' . $_POST['xn_converted_video_frame'] . ')');
        }
        if ($video->my->conversionStatus == 'failed') {
            Video_MessagingHelper::conversionFailed($video);
            Video_VideoHelper::delete($video);
            return;
        }

        // Use Video_ContentHelper::findByID rather than W_Model::findByID, which does a query - see
        // BAZ-803. This action is called from a different computer (the video transcoding server) than
        // the one that called the create action (the user's computer). Because they do not have the same
        // ID cookie, there is a potential timing issue and the query may return no results for a few seconds. [Jon Aquino 2006-12-18]
        $videoAttachment = VideoAttachment::create(Video_ContentHelper::findById('VideoAttachment', $video->my->sourceVideoAttachment)->title, $video, FALSE, 'xn_converted_video');
        $videoAttachment->save();
        $video->my->videoAttachment = $videoAttachment->id;
        $previewFrame = VideoPreviewFrame::create($video, 'xn_converted_video_frame');
        $previewFrame->save();
        $video->my->previewFrame = $previewFrame->id;
        XN_Content::delete(XG_Cache::content($video->my->sourceVideoAttachment));
        $video->my->sourceVideoAttachment = NULL;
        $video->my->videoSizeInBytes = $_POST['xn_converted_video:size'];
        $video->my->duration = $_POST['xn_duration'];
        $video->my->conversionStatus = 'complete';
        $video->save();
        Video_MessagingHelper::conversionSucceeded($video, Video_SecurityHelper::isApprovalRequired());
        Video_VideoHelper::updateVideoCount(User::load($this->_user));
        // Removed pre-emptive call to previewFrameUrl (NING-4769) [Jon Aquino 2006-11-13]
    }

    /**
     * Returns a string to uniquely identify a cache in the current action. Can be used anywhere to invalidate the cache.
     */
    private function invalidationConditionForCurrentAction($qualifier = null) {
        $names = XG_App::getRequestedRoute();
        return $names['widgetName'] . '-' . $names['controllerName'] . '-' . $names['actionName'] . ($qualifier ? '-' . $qualifier : '');
    }

    /**
     * Supply an 'add content' form for the site and user setup.
     * If the request method is POST, attempt to save any provided info
     * If the request method is GET, just render the template. No error
     * messages are returned to speed the setup/join process
     *
     * @see Bazel Code Structure: The Add Content Page
     */
    public function action_addContent() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if (Video_SecurityHelper::failed(Video_SecurityHelper::checkCurrentUserCanAddVideos($this->_user))) { throw new Exception('Current user is not allowed to add videos'); }
        $this->prefix = W_Cache::current('W_Widget')->dir;
        try {
            if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST[$this->prefix]) && is_array($_POST[$this->prefix])) {
                $uploadName = $this->prefix . '_file';
                $input = $_POST[$this->prefix];
                if (! $_POST[$uploadName] && ! $input['embedCode']) { return; }
                if ($input['selectedTab'] == $this->prefix . '_embed_tab') {
                    unset($_POST[$uploadName]);
                } elseif ($input['selectedTab'] == $this->prefix . '_upload_tab') {
                    unset($input['embedCode']);
                } else {
                    throw new Exception('Invalid value for selectedTab: ' . $input['selectedTab']);
                }
                $input['title'] = $input['title'] == xg_text('DEFAULT_TITLE') ? null : $input['title'];
                $input['description'] = $input['description'] == xg_text('DEFAULT_DESCRIPTION') ? null : $input['description'];
                $video = $this->createVideo($uploadName, $input);
            }
        } catch (Exception $e) {
            error_log("$this->prefix addContent error: {$e->getMessage()}");
            Video_LogHelper::log($e->getTraceAsString(), true);
            if (is_callable(array($e,'getErrorsAsString'))) {
                error_log($e->getErrorsAsString());
            }
        }
    }

    /**
     * Returns the embed code for the video player, and the embed code for its preview.
     * If no video was found, the embed code will be blank and the preview's embed code will
     * be for a dummy video player.
     *
     * Expected GET parameters:
     *     xn_out - set this to xn_json
     *     videoID - the content object ID for the video to display, or
     *             'highest_rated', 'most_recent', 'promoted', 'user_most_recent',
     *             'user_highest_rated'
     *     contributorName - screenName of user to use for 'user_most_recent'
     *             and 'user_highest_rated'
     *     autoplay - whether the video should start playing immediately
     *     layout - fullscreen, within_app, on_detail_page, dummy_external_site, or external_site (default)
     *     noVideosMessage - optional text to display if no videos are available
     */
    public function action_embeddableWithPreview() {
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        ob_start();
        // Keep array_merge args in sync with embeddable/list.php [Jon Aquino 2008-01-15]
        $this->_widget->dispatch('video', 'embeddable', array(array_merge($_GET, array('width' => XG_EmbeddableHelper::EXTERNAL_VIDEO_PLAYER_WIDTH, 'height' => XG_EmbeddableHelper::EXTERNAL_VIDEO_PLAYER_HEIGHT, 'includeFooterLink' => true))));
        $this->embedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
        ob_end_clean();
        ob_start();
        // Keep array_merge args in sync with embeddable/list.php [Jon Aquino 2008-01-15]
        $this->_widget->dispatch('video', 'embeddable', array(array_merge($_GET, array('width' => 300, 'height' => 253, 'externalPreview' => true, 'showDummyVideoIfNoneFound' => true))));
        $this->previewEmbedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
        ob_end_clean();
    }

    /**
     * Displays the Flash object for the video player.
     *
     * Expected GET parameters:
     *     Any of the $args parameters can also be passed as GET parameters
     *
     * @param $args array  parameters:
     *     width - width of the player, in pixels
     *     height - height of the player, in pixels
     *     videoID - the content object ID for the video to display, or
     *             'highest_rated', 'most_recent', 'promoted', 'user_most_recent',
     *             'user_highest_rated'
     *     showDummyVideoIfNoneFound - if no video is found, whether to display a dummy video player or nothing
     *     contributorName - screenName of user to use for 'user_most_recent'
     *             and 'user_highest_rated'
     *     autoplay - whether the video should start playing immediately
     *     layout - fullscreen, within_app, on_detail_page, dummy_external_site, or external_site (default)
     *     includeFooterLink - whether to add a link back to the app
     *     bgColor - optional background color to override the value in showPlayerConfig, e.g., 333333
     *     bgImage - optional background image URL to override the value in showPlayerConfig; use "none" to specify no image
     *     brand - optional brand setting to override the value in showPlayerConfig: name, logo, or none
     *     logoImage - optional brand-logo URL to override the value in showPlayerConfig, or 'none' to show none
     *     logoImageWidth - optional brand-logo width to override the value in showPlayerConfig
     *     logoImageHeight - optional brand-logo height to override the value in showPlayerConfig
     *     externalPreview - whether this is for a preview of how the player will look on an external site.
     *             Do not use this for the actual external embed code.
     *     noVideosMessage - optional text to display if no videos are available
     */
    public function action_embeddable($args = array()) {
        $this->args = array_merge($_GET, $args);
        if (!isset($this->args['videoID'])) {
            $this->args['videoID'] = 'most_recent';
        }
        switch($this->args['videoID']) {
            case 'most_recent':
                $this->args['video'] = $this->action_getMostRecentLocalVideo();
                break;
            case 'highest_rated':
                $this->args['video'] = $this->action_getHighestRatedLocalVideo();
                break;
            case 'promoted':
                $this->args['video'] = $this->action_getMostRecentPromotedLocalVideo();
                break;
            case 'user_most_recent':
                $this->args['video'] = $this->action_getMostRecentLocalVideo($this->args['contributorName']);
                break;
            case 'user_highest_rated':
                $this->args['video'] = $this->action_getHighestRatedLocalVideo($this->args['contributorName']);
                break;
            default:
                $this->args['video'] = XN_Content::load($this->args['videoID']);
        }
        if (! $this->args['video']->id && $this->args['showDummyVideoIfNoneFound']) {
            $this->args['layout'] = 'dummy_external_site';
        } elseif (! $this->args['video']->id) {
            $this->render('blank');
        } else {
            $this->args['id'] = $this->args['video']->id;
            //  dispatch() should only be called within templates - it outputs
            //    the result directly and thus can bypass the output buffer used
            //    by capture() and xn_out=json/html_json!  DC 20070627
        }
    }

    /**
     * Displays the Flash object for the video player.
     *
     * Expected GET parameters:
     *     Any of the $args parameters can also be passed as GET parameters
     *
     * @param $args array  parameters:
     *     id - ID of the Video object
     *     width - width of the player, in pixels
     *     height - height of the player, in pixels
     *     autoplay - whether the video should start playing immediately
     *     layout - fullscreen, within_app, on_detail_page, dummy_external_site, or external_site (default)
     *     includeFooterLink - whether to add a link back to the app
     *     bgColor - optional background color to override the value in showPlayerConfig, e.g., 333333
     *     bgImage - optional background image URL to override the value in showPlayerConfig; use "none" to specify no image
     *     brand - optional brand setting to override the value in showPlayerConfig: name, logo, or none
     *     logoImage - optional brand-logo URL to override the value in showPlayerConfig, or 'none' to show none
     *     logoImageWidth - optional brand-logo width to override the value in showPlayerConfig
     *     logoImageHeight - optional brand-logo height to override the value in showPlayerConfig
     *     externalPreview - whether this is for a preview of how the player will look on an external site.
     *             Do not use this for the actual external embed code.
     *     noVideosMessage - optional text to display if no videos are available
     */
    public function action_embeddableProper($args = array()) {
        $args = array_merge($_GET, $args);
        if (array_key_exists('logoImage', $args) && $args['logoImage']!== 'none') {
            if (!array_key_exists('logoImageWidth', $args)) {
                if (preg_match('@\Wwidth=(\d+)@u', $args['logoImage'], $matches)) {
                    $args['logoImageWidth'] = $matches[1];
                }
            }
            if (!array_key_exists('logoImageHeight', $args)) {
                if (preg_match('@\Wheight=(\d+)@u', $args['logoImage'], $matches)) {
                    $args['logoImageHeight'] = $matches[1];
                }
            }
        }
        foreach ($args as $key => $value) { $this->{$key} = $value; }
        $this->videoSmoothing = ($this->_widget->privateConfig['playerSmoothing'] == 'N')?'off':'on';
        $this->flashVars = array(
                'config_url' => $this->_buildUrl('video', 'showPlayerConfig', array('id' => $this->id, 'x' => Video_SecurityHelper::embeddableAccessCode())),
                'video_smoothing' => $this->videoSmoothing,
                'autoplay' => $this->autoplay ? 'on' : 'off',
                'embed_visible' => $this->embedVisible,
                'layout' => $this->layout,
                // Not bgcolor, a deprecated Bazel FlashVar which may be present in existing <embed> code on the web [Jon Aquino 2007-06-27]
                'background_color' => $this->bgColor,
                'bg_image_url' => $this->bgImage,
                'brand_format' => $this->brand,
                'watermark_url' => $this->logoImage,
                'watermark_width' => $this->logoImageWidth,
                'watermark_height' => $this->logoImageHeight,
                'no_videos_message' => $this->noVideosMessage);
        if ($args['externalPreview']) {
            // Work around caching of showPlayerConfig (BAZ-3631) [Jon Aquino 2007-07-02]
            if (! $this->flashVars['background_color']) { $this->flashVars['background_color'] = XG_EmbeddableHelper::getBackgroundColor(); }
            if (! $this->flashVars['bg_image_url']) { $this->flashVars['bg_image_url'] = XG_EmbeddableHelper::getBackgroundImageUrl(); }
            if (! $this->flashVars['brand_format']) { $this->flashVars['brand_format'] = XG_EmbeddableHelper::getPlayerBrandFormat(); }
            if (! $this->flashVars['watermark_url']) { $this->flashVars['watermark_url'] = XG_EmbeddableHelper::getPlayerLogoUrl(); }
            if (! $this->flashVars['watermark_width']) { $this->flashVars['watermark_width'] = XG_EmbeddableHelper::getPlayerLogoWidth(); }
            if (! $this->flashVars['watermark_height']) { $this->flashVars['watermark_height'] = XG_EmbeddableHelper::getPlayerLogoHeight(); }
        }
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        $this->swfUrl = xg_cdn($this->_widget->buildResourceUrl('flvplayer/flvplayer.swf'));
    }

}
