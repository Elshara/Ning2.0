<?php
class Photo_PhotoController extends XG_BrowserAwareController {
    /** The number of photo thumbs on a two column view. */
    const NUM_THUMBS_TWOCOLUMNVIEW = 10; // 5 rows with 2 columns
    const NUM_THUMBS_THREECOLUMNVIEW = 18;
    /** The number of photo thumbs on a four column view. */
    const NUM_THUMBS_FOURCOLUMNVIEW = 16; // 8 rows with 4 columns. Keep in sync with AlbumContoller::action_show [Jon Aquino 2007-07-07]
    /** The number of photo thumbs on a six column view. */
    const NUM_THUMBS_SIXCOLUMNVIEW = 12; // 3 rows with 6 columns
    /** The number of photos for slideshows outside Ning and fullscreen pages*/
    const NUM_PHOTOS_EMBEDDABLE  = 100;
    /** The number of photos for slideshows outside Ning and fullscreen pages*/
    const NUM_PHOTOS_FULLSCREEN  = 100;
    /** The number of photos for slideshows outside Ning and fullscreen pages*/
    const NUM_PHOTOS_SLIDESHOW_ALBUM  = 100;
    /** The number of photos for the rss feeds */
    const NUM_THUMBS_RSS = 10;
    /** The ttl for slideshow feeds, in seconds */
    const SLIDESHOW_FEED_CACHE_LIFETIME = 1800; //30 minutes

    public function action_overridePrivacy($action) {
        return
            // Slideshow feeds implement their own privacy mechanism [Jon Aquino 2006-12-09]
            $action == 'slideshowFeed' ||
            $action == 'slideshowFacebookFeed' ||
            $action == 'slideshowFeedForContributor' ||
            $action == 'slideshowFeedAlbum' ||
            $action == 'slideshowFeedFriends' ||
            $action == 'slideshowFeedFavorites' ||
            $action == 'slideshowFeedTagged' ||
            $action == 'showPlayerConfig' ||

            (! XG_App::appIsPrivate() && $action == 'rss') ||
            (! XG_App::appIsPrivate() && $action == 'listFavorites' && $_GET['rss'] == 'yes') ||
            (! XG_App::appIsPrivate() && $action == 'listForContributor' && $_GET['rss'] == 'yes') ||
            (! XG_App::appIsPrivate() && $action == 'listTagged' && $_GET['rss'] == 'yes');
    }

    protected function _before() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_AppearanceHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SlideshowHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_JsonHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_AlbumHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_AppHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_ColorHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_AppearanceHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_MessagingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_GeocodingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_LogHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PrivacyHelper.php');
        XG_App::includeFileOnce('/lib/XG_MapHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        Photo_PrivacyHelper::checkMembership();
        Photo_HttpHelper::trimGetAndPostValues();
        Photo_AppearanceHelper::updateFilesIfNecessary();
        Photo_UserHelper::autoCreateAppOwnerUserObject();

        $this->pageSize = self::NUM_THUMBS_TWOCOLUMNVIEW;
        $this->sorts    = Photo_PhotoHelper::getKnownSortingOrders();
        $this->sort     = self::getSortDescriptor();
    }

    /**
     * Returns the sort order description (one of the $this->sorts items) for the sort order
     * specified in the $_GET array.
     *
     * @param _GET['sort'] The current sort order
     * @return The sort descriptor, one of the $this->sorts items
     */
    private function getSortDescriptor() {
        $result = $this->sorts[$_GET['sort']];
        if (!$result) {
            $result = $this->sorts[Photo_PhotoHelper::SORT_ORDER_MOSTRECENT];
        }
        return $result;
    }

    public function action_index() {
        $this->forwardTo('list');
    }

    public function action_gfx() {
        Photo_LogHelper::log('debug: ' . Photo_HttpHelper::currentUrl());
        Photo_LogHelper::log('referrer: ' . $_SERVER['HTTP_REFERER']);
    }

    public function action_listForApproval() {
        if ($this->error = Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->bodyId = 'photos-to-approve';
        if (Photo_SecurityHelper::isApprovalRequired()) {
            self::handleSortingAndPagination($this->_user, array('forApproval' => true), self::NUM_THUMBS_TWOCOLUMNVIEW);
        } else {
            $this->photos   = array();
            $this->page     = 1;
            $this->numPages = 1;
        }
        Photo_FullNameHelper::initialize($this->photos);
    }

    public function action_list() {
        if (! $this->_user->isLoggedIn()) { $this->setCaching(array(md5(XG_HttpHelper::currentUrl())), 300); }
        self::handleSortingAndPagination($this->_user, array(), 20);
        Photo_FullNameHelper::initialize($this->photos);
        if ($this->page == 1) {
            $this->featuredPhotos = Photo_PhotoHelper::getPromotedPhotos(6);
            if (count($this->featuredPhotos) == 6) {
                $this->showViewAllFeaturedUrl = true;
                array_pop($this->featuredPhotos);
            }
        }
        $this->showFacebookMeta = array_key_exists('from', $_GET) && ($_GET['from'] === 'fb');
        $this->slideshowLink = $this->numPhotos ? Photo_HtmlHelper::slideshowLink(array('sort' => $_GET['sort'])) : NULL;
        $this->subMenuItem = 'allPhotos';
        $this->title = xg_text('ALL_PHOTOS');
        $this->pageTitle = xg_text('PHOTOS');
    }

    /**
     * Displays all photos for the given search terms.
     */
    public function action_search() {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        if (XG_QueryHelper::getSearchMethod() == 'search') {
            try {
                $this->pageSize = 20;
                $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
                $query = XN_Query::create('Search');
                $query->filter('type', 'like', 'Photo');
                $query->begin($begin);
                $query->end($begin + $this->pageSize);
                $query->alwaysReturnTotalCount(true);
                XG_QueryHelper::addSearchFilter($query, $_GET['q'], true);
                XG_QueryHelper::addExcludeFromPublicSearchFilter($query, true);
                $this->photos = XG_QueryHelper::contentFromSearchResults($query->execute(), false);
                $this->numPhotos = $query->getTotalCount();
            } catch (Exception $e) {
                // According to David Sklar, the search core may throw an exception
                // while searchability is being added to an app without search. [Jon Aquino 2008-02-13]
                self::handleSortingAndPagination($this->_user, array('searchTerms' => $_GET['q']), 20);
            }
        } else {
            self::handleSortingAndPagination($this->_user, array('searchTerms' => $_GET['q']), 20);
        }
        Photo_FullNameHelper::initialize($this->photos);
    }

    /**
     * Displays all photos for a given location.
     */
    public function action_listForLocation() {
        $this->location = $_GET['location'];
        $this->pageSize = 20;
        self::handleSortingAndPagination($this->_user, array('location' => $this->location), $this->pageSize);
        Photo_FullNameHelper::initialize($this->photos);
    }

    /**
     * Displays a list of photos that have been promoted.
     */
    public function action_listFeatured() {
        $this->pageSize = 20;
        self::handleSortingAndPagination($this->_user, array('promoted' => true), $this->pageSize);
        Photo_FullNameHelper::initialize($this->photos);
        $this->subMenuItem = NULL;
    }

    public function action_rss() {
        if ($_GET['internalView'] != 'true') {
            header("Content-Type: text/xml");
            $this->setCaching(array('photo-photo-rss-' . md5(XG_HttpHelper::currentUrl())), 1800);
            if ($_GET['test_caching']) { var_dump('Not cached'); }
        }
        self::handleSortingAndPagination($this->_user, null, self::NUM_THUMBS_RSS);
        $appName = XN_Application::load()->name;
        $this->description = xg_text('RECENT_PUBLIC_PHOTOS_ON_X', $appName);
        $this->title = xg_text('X_ALL_PHOTOS', $appName);
        $this->link = 'http://' . $_SERVER['HTTP_HOST'];
        if (preg_match('/custom_image/u', $this->_widget->config['headerLayout'])) {
            $headerImageUrl = $this->_widget->config['headerImageUrl'];
            $headerImageHeight = $this->_widget->config['headerImageHeight'];
            if ($this->_widget->config['scaleHeaderImageIfNecessary'] == 'Y' && $headerImageHeight > $this->_widget->config['scaledHeaderImageHeight']) {
                $headerImageUrl = '/images/theme/custom-scaled-header-image-' . $this->_widget->config['updatedOn'] . '.png';
                $headerImageHeight = $this->_widget->config['scaledHeaderImageHeight'];
            }
            $this->feedImageUrl = 'http://' . $_SERVER['HTTP_HOST'].$headerImageUrl;
            $this->feedImageHeight = $headerImageHeight;
        } else {
            $this->feedImageUrl = XN_Application::load()->iconUrl(50, 50);
            $this->feedImageHeight = 50;
        }
        Photo_FullNameHelper::initialize($this->photos);
        $this->useTags = 1;
    }

    public function action_listTagged() {
        $this->tag = $_GET['tag'];
        if (! $this->tag) {
            $this->error = xg_text('NO_TAG_WAS_SPECIFIED');
            return $this->render('error', 'index');
        }
        if($_GET['rss']=='yes'){ header("Content-Type: text/xml"); }
        $this->pageSize = 20;
        self::handleSortingAndPagination($this->_user, array('tag' => $this->tag), $this->pageSize);
        if ($_GET['rss'] == 'yes') {
            $appname = XN_Application::load()->name;
            $this->description = xg_xmlentities(xg_text('PUBLIC_PHOTOS_TAGGED_WITH_X', $this->tag, $appname));
            $this->title =  xg_xmlentities(xg_text('ALL_PHOTOS_TAGGED_X', $this->tag));
            $this->link = $this->_buildUrl('photo','listTagged', '?tag=' . urlencode($this->tag));
            // TODO: Eliminate duplicate code [Jon Aquino 2008-02-07]
            if (preg_match('/custom_image/u', $this->_widget->config['headerLayout'])) {
                $headerImageUrl = $this->_widget->config['headerImageUrl'];
                $headerImageHeight = $this->_widget->config['headerImageHeight'];
                if ($this->_widget->config['scaleHeaderImageIfNecessary'] == 'Y' && $headerImageHeight > $this->_widget->config['scaledHeaderImageHeight']) {
                    $headerImageUrl = '/images/theme/custom-scaled-header-image-' . $this->_widget->config['updatedOn'] . '.png';
                    $headerImageHeight = $this->_widget->config['scaledHeaderImageHeight'];
                }
                $headerImageUrl = $this->_widget->config['headerImageUrl'];
                $this->feedImageUrl = 'http://' . $_SERVER['HTTP_HOST'].$headerImageUrl;
                $this->feedImageHeight = $headerImageHeight;
            } else {
                $this->feedImageUrl = XN_Application::load()->iconUrl(50, 50);
                $this->feedImageHeight = 50;
            }
            $this->useTags = 1;
            $this->render('rss');
        }
        Photo_FullNameHelper::initialize($this->photos);
    }

    /**
     * Expected GET parameters:
     *     uploaded - whether to notify the user that her photos were successfully uploaded
     */
    public function action_listForContributor($args=NULL) {
        if (isset($_GET['screenName']) && User::isMember($_GET['screenName'])) {
            // make sure the requested screenName is a member of the app otherwise we'll create xn_anonymous User objects (BAZ-8401) [ywh 2008-07-22]
            $this->user = Photo_UserHelper::load($_GET['screenName']);
        } else {
            XG_SecurityHelper::redirectIfNotMember();
            $this->user = Photo_UserHelper::load($this->_user);
        }
        if (! $this->user) {
            $this->error = array('title' => xg_text('SLOW_DOWN_THERE_CHIEF'), 'subtitle' => '', 'description' => xg_text('I_DO_NOT_KNOW_USER'));
            return $this->render('error', 'index');
        }
        if($_GET['rss']=='yes'){
            header("Content-Type: text/xml");
            $this->setCaching(array('photo-photo-listForContributor-' . md5(XG_HttpHelper::currentUrl())), 1800);
            if ($_GET['test_caching']) { var_dump('Not cached'); }
        }
        $this->myOwnPhotos = $this->user->title == $this->_user->screenName;
        $this->pageTitle = $this->myOwnPhotos ? xg_text('MY_PHOTOS') : xg_text('XS_PHOTOS', Photo_FullNameHelper::fullName($this->user->title));
        $begin = 0;
        $this->pageSize = 20;
        if (preg_match('@^[.0-9]+$@u', $_GET['page']) && ($_GET['page'] > 0)) {
            $begin = ($_GET['page'] - 1) * $this->pageSize;
        }
        $begin = max(0, $begin);
        $filters = array('contributor' => $this->user->title);
        $photosData = Photo_PhotoHelper::getSortedPhotos($this->_user, $filters, $this->sort, $begin, $begin + $this->pageSize);
        $this->numPhotos = $photosData['numPhotos'];
        $this->photos   = $photosData['photos'];
        $this->isSortRandom = $this->sort['alias'] == Photo_PhotoHelper::SORT_ORDER_RANDOM;
        Photo_FullNameHelper::initialize(array_merge(array($this->user), $this->photos));
        if($_GET['rss']=='yes'){
            $this->description = xg_xmlentities(xg_text('XS_PUBLIC_PHOTOS_ON_X', Photo_FullNameHelper::fullName($this->user->title), XN_Application::load()->name));
            $this->title = xg_xmlentities(xg_text('XS_PHOTOS', Photo_FullNameHelper::fullName($this->user->title)));
            $this->link = $this->_buildUrl('photo','listForContributor', '?screenName=' . $this->user->title);
            $this->feedImageUrl = XG_UserHelper::getThumbnailUrl(Photo_FullNameHelper::profile($this->user->title), 50, 50);
            $this->feedImageHeight = 50;
            $this->useTags = 1;
            $this->render('rss');
            return;
        }
        if ($_GET['uploaded']) {
            if (XG_SecurityHelper::userIsAdmin(XN_Profile::current()) || !Photo_SecurityHelper::isApprovalRequired()) {
                $this->uploadMessage = xg_text('PHOTOS_SUCCESSFULLY_UPLOADED');
            } else {
                $this->uploadMessage = xg_text('PHOTOS_UPLOADED_AWAITING', XN_Application::load()->name);
            }
        }
        if($args['output']=='embed'){
            $this->render('fragment_embeddableList');
            return;
        }
        $this->sortOptions = $this->getSortOptions();
    }
    public function action_listForContributor_iphone($args=NULL) {
        if (isset($_GET['screenName']) && User::isMember($_GET['screenName'])) {
            // make sure the requested screenName is a member of the app otherwise we'll create xn_anonymous User objects (BAZ-8401) [ywh 2008-07-22]
            $this->user = Photo_UserHelper::load($_GET['screenName']);
        } else {
            XG_SecurityHelper::redirectIfNotMember();
            $this->user = Photo_UserHelper::load($this->_user);
        }
        if (! $this->user) {
            $this->error = array('title' => xg_text('SLOW_DOWN_THERE_CHIEF'), 'subtitle' => '', 'description' => xg_text('I_DO_NOT_KNOW_USER'));
            return $this->render('error', 'index');
        }
        $this->myOwnPhotos = $this->user->title == $this->_user->screenName;
        $begin = 0;
        $end = 8;
        $filters = array('contributor' => $this->user->title);
        $photosData = Photo_PhotoHelper::getSortedPhotos($this->_user, $filters, $this->sort, $begin, $end);
        $this->numPhotos = $photosData['numPhotos'];
        $this->photos   = $photosData['photos'];
        $this->isSortRandom = $this->sort['alias'] == Photo_PhotoHelper::SORT_ORDER_RANDOM;
        Photo_FullNameHelper::initialize(array_merge(array($this->user), $this->photos));
        if($args['output']=='embed'){
            $this->render('fragment_embeddableList');
            return;
        }
    }

    /**
     * generate slideshow feeds specific for Facebook-embedded photo slideshow player
     * based on Facebook app configuration; dispatches to slideshowFeed with the appropriate
     * parameters.
     */
    public function action_slideshowFacebookFeed() {
        if (! Photo_SecurityHelper::canAccessEmbeddableData($_GET)) { throw new Exception('Not Allowed (2983478234)'); }
        XG_App::includeFileOnce('/lib/XG_FacebookHelper.php');
        $dispType = XG_FacebookHelper::getFacebookDisplayType('photo');
        $params = in_array($dispType, array('promoted', 'popular')) ?
                      array($dispType => 'true') :
                      array();

        $this->_widget->dispatch('photo', 'slideshowFeed', array(array_merge($_GET, $params)));
    }

    public function action_slideshowFeed($args = array()) {
        foreach ($args as $k => $v) { $_GET[$k] = $v; }
        if (! Photo_SecurityHelper::canAccessEmbeddableData($_GET)) { throw new Exception(); }
        list($viewerProfile, $shouldCache) = $this->prepareSlideshowFeed();
        if ($_GET['internalView'] != 'true') { header("Content-Type: text/xml"); }
        if ($shouldCache) {
            $expiryConditions = array('photo-photo-slideshowFeed-' . md5(XG_HttpHelper::currentUrl()));
            if ($_GET['promoted']) { $expiryConditions[] = XG_CacheExpiryHelper::promotedObjectsChangedCondition('Photo'); }
            if ($_GET['owner']) { $expiryConditions[] = XG_CacheExpiryHelper::photoAddedCondition(XN_Application::load()->ownerName); }
            $this->setCaching($expiryConditions, self::SLIDESHOW_FEED_CACHE_LIFETIME);
        }
        $numPhotos = ($_GET['fullscreen'] ? self::NUM_PHOTOS_FULLSCREEN : self::NUM_PHOTOS_EMBEDDABLE);
        if ($_GET['random']) $this->sort = Photo_PhotoHelper::getRandomSortingOrder();
        if ($_GET['promoted']) {
            // Invalidation conditions don't account for promoted state, so don't cache.
            // Reinstate caching once BAZ-713 is resolved (simplifying the cache design) [Jon Aquino 2006-12-14]
            $this->photos = Photo_PhotoHelper::getPromotedPhotos($numPhotos, $this->sort);
        } elseif ($_GET['owner']) {
            self::handleSortingAndPagination($viewerProfile, array('contributor' => XN_Application::load()->ownerName), $numPhotos, null);
        } elseif ($_GET['popular']) {
            if (!$_GET['random']) $this->sort = Photo_PhotoHelper::getMostPopularSortingOrder();
            self::handleSortingAndPagination($viewerProfile, null, $numPhotos, null);
        } else {
            self::handleSortingAndPagination($viewerProfile, null, $numPhotos, null);
        }
        $this->useTags = $_GET['useTags'];
        $this->render($this->useTags ? 'rss' : 'rssCompact');
    }

    public function action_slideshowFeedForContributor() {
        if (! Photo_SecurityHelper::canAccessEmbeddableData($_GET)) { throw new Exception(); }
        list($viewerProfile, $shouldCache) = $this->prepareSlideshowFeed();
        if (isset($_GET['screenName']) && User::isMember($_GET['screenName'])) {
            // make sure the requested screenName is a member of the app otherwise we'll create xn_anonymous User objects (BAZ-8401) [ywh 2008-07-22]
            $this->user = Photo_UserHelper::load($_GET['screenName']);
        } else {
            // allow anonymous users here? [ywh 2008-07-22]
            $this->user = Photo_UserHelper::load($viewerProfile);
        }
        if (!$this->user) {
            //@TODO error xml if screenName is not there
            return;
        }
        header("Content-Type: text/xml");
        if ($shouldCache) { $this->setCaching(array(XG_CacheExpiryHelper::photoAddedCondition($this->user->title), 'photo-photo-slideshowFeedForContributor-' . md5(XG_HttpHelper::currentUrl())), self::SLIDESHOW_FEED_CACHE_LIFETIME); }
        $myOwnPhotos = ($this->user->title == $viewerProfile->screenName);
        if ($myOwnPhotos) {
            $this->sort = $_GET['sort'] ? $this->sort : $this->sorts[Photo_PhotoHelper::SORT_ORDER_MOSTRECENT];
        }
        $numPhotos = ($_GET['fullscreen'] ? self::NUM_PHOTOS_FULLSCREEN : self::NUM_PHOTOS_EMBEDDABLE);
        self::handleSortingAndPagination($viewerProfile, array('contributor' => $this->user->title), $numPhotos);
        if ($_GET['random']) { shuffle($this->photos); }
        $this->useTags = $_GET['useTags'];
        $this->render($this->useTags ? 'rss' : 'rssCompact');
    }

    public function action_slideshowFeedFavorites() {
        if (! Photo_SecurityHelper::canAccessEmbeddableData($_GET)) { throw new Exception(); }
        list($viewerProfile, $shouldCache) = $this->prepareSlideshowFeed();
        if (isset($_GET['screenName']) && User::isMember($_GET['screenName'])) {
            // make sure the requested screenName is a member of the app otherwise we'll create xn_anonymous User objects (BAZ-8401) [ywh 2008-07-22]
            $this->user = Photo_UserHelper::load($_GET['screenName']);
        } else {
            // allow anonymous users here? [ywh 2008-07-22]
            $this->user = Photo_UserHelper::load($viewerProfile);
        }
        if (!$this->user) {
            //@TODO error xml if screenName is not there
            return;
        }
        header("Content-Type: text/xml");
        if ($shouldCache) { $this->setCaching(array(XG_CacheExpiryHelper::favoritePhotosChangedCondition($this->user->title), 'photo-photo-slideshowFeedFavorites-' . md5(XG_HttpHelper::currentUrl())), self::SLIDESHOW_FEED_CACHE_LIFETIME); }
        $myOwnFavorites = ($this->user->title == $viewerProfile->screenName);
        $begin          = 0;
        $numPerPage     = ($_GET['fullscreen'] ? self::NUM_PHOTOS_FULLSCREEN : self::NUM_PHOTOS_EMBEDDABLE);
        if (preg_match('@^[.0-9]+$@u', $_GET['page']) && ($_GET['page'] > 0)) {
            $begin = ($_GET['page'] - 1) * $numPerPage;
        }
        $favoritesIds  = Photo_ContentHelper::ids($this->user, Photo_UserHelper::attributeName('favorites'));
        if ($_GET['small'] || $_GET['fullscreen']) {
            $this->small   = true;
            $favoritesData = Photo_PhotoHelper::getSpecificPhotos($viewerProfile, $favoritesIds, $this->sort, $begin, $begin + $numPerPage);
            $this->photos = $favoritesData['photos'];
        } else {
            $favoritesData = Photo_PhotoHelper::getSpecificPhotos($viewerProfile, $favoritesIds, $this->sort, $begin, $begin + $numPerPage);
            $this->photos   = $favoritesData['photos'];
            $this->page     = 1 + (int)($begin / $numPerPage);
            $this->numPages = $favoritesData['numPhotos'] == 0 ? 1 : 1 + (int)(($favoritesData['numPhotos'] - 1) / $numPerPage);
        }
        if ($_GET['random']) { shuffle($this->photos); }
        $this->useTags = $_GET['useTags'];
        $this->render($this->useTags ? 'rss' : 'rssCompact');
    }

    public function action_slideshowFeedAlbum() {
        if (! Photo_SecurityHelper::canAccessEmbeddableData($_GET)) { throw new Exception(); }
        list($viewerProfile, $shouldCache) = $this->prepareSlideshowFeed();
        header("Content-Type: text/xml");
        if ($shouldCache) { $this->setCaching(array(XG_CacheExpiryHelper::albumChangedCondition($_GET['id']), 'photo-photo-slideshowFeedAlbum-' . md5(XG_HttpHelper::currentUrl())), self::SLIDESHOW_FEED_CACHE_LIFETIME); }
        if (isset($_GET['id'])) {
            try {
                $this->album = Photo_AlbumHelper::load($_GET['id']);
            } catch (Exception $e) {
                $this->album = null;
            }
        }
        if (!$this->album) {
            $this->photos = array();
            $this->render($this->useTags ? 'rss' : 'rssCompact');
            return;
        }
        $photoIds   = Photo_ContentHelper::ids($this->album, 'photos');
        if ($_GET['small'] || $_GET['fullscreen']) {
            $this->small = true;
        }
        $numPerPage = self::NUM_PHOTOS_SLIDESHOW_ALBUM;
        $photosData = Photo_PhotoHelper::getSpecificPhotos($viewerProfile, $photoIds, null, 0, $numPerPage);
        $this->photos = $photosData['photos'];

        Photo_ContentHelper::sortByAttribute($this->photos, $photoIds);
        if ($_GET['random']) { shuffle($this->photos); }
        $this->useTags = $_GET['useTags'];
        $this->render($this->useTags ? 'rss' : 'rssCompact');
    }

    public function action_slideshowFeedFriends() {
        $numPhotos = ($_GET['fullscreen'] ? self::NUM_PHOTOS_FULLSCREEN : self::NUM_PHOTOS_EMBEDDABLE);
        if (! Photo_SecurityHelper::canAccessEmbeddableData($_GET)) { throw new Exception(); }
        list($viewerProfile, $shouldCache) = $this->prepareSlideshowFeed();
        header("Content-Type: text/xml");
        if ($shouldCache) { $this->setCaching(array('photo-photo-slideshowFeedFriends-' . $this->_user->screenName . '-' . md5(XG_HttpHelper::currentUrl())), self::SLIDESHOW_FEED_CACHE_LIFETIME); }
        self::handleSortingAndPagination($viewerProfile, array('friends' => true), $numPhotos);
        if ($_GET['random']) { shuffle($this->photos); }
        $this->useTags = $_GET['useTags'];
        $this->render($this->useTags ? 'rss' : 'rssCompact');
    }

    public function action_slideshowFeedTagged() {
        header("Content-Type: text/xml");
        if (! Photo_SecurityHelper::canAccessEmbeddableData($_GET)) { throw new Exception(); }
        list($viewerProfile, $shouldCache) = $this->prepareSlideshowFeed();
        $this->tag = $_GET['tag'];
        if (isset($_GET['screenName']) && User::isMember($_GET['screenName'])) {
            // make sure the requested screenName is a member of the app otherwise we'll create xn_anonymous User objects (BAZ-8401) [ywh 2008-07-22]
            $this->user = Photo_UserHelper::load($_GET['screenName']);
        } else {
            // allow anonymous users here? [ywh 2008-07-22]
            $this->user = Photo_UserHelper::load($viewerProfile);
        }
        if (!$this->tag) { return; } //@TODO error xml if screenName is not there
        $numPhotos = ($_GET['fullscreen'] ? self::NUM_PHOTOS_FULLSCREEN : self::NUM_PHOTOS_EMBEDDABLE);
        self::handleSortingAndPagination($viewerProfile, array('tag' => $this->tag, 'contributor'=> $this->user->title), $numPhotos);
        if ($_GET['random']) { shuffle($this->photos); }
        $this->useTags = $_GET['useTags'];
        $this->render($this->useTags ? 'rss' : 'rssCompact');
    }

    /**
     * Returns data for slideshow feeds.
     *
     * Expected GET variables
     *     - internalView - whether a Facebook feed is being generated
     *
     * @return array  an array containing the XN_Profile to use for the current user,
     *         and a boolean indicating whether to cache the feed.
     */
    protected function prepareSlideshowFeed() {
        $viewerProfile = $_GET['internalView'] ? Photo_UserHelper::createAnonymousProfile() : $this->_user;
        // Don't cache if current user is logged in, so that she sees Friends-only photos (BAZ-6183) [Jon Aquino 2008-03-06]
        // TODO: Allow caching when logged in, but use XG_QueryHelper::setMaxAgeForFriendsQuery [Jon Aquino 2008-09-17]
        $shouldCache = ! $viewerProfile->isLoggedIn();
        return array($viewerProfile, $shouldCache);
    }

    public function action_slideshow() {
        $this->displayHeader = true;
        $this->hideNavigation = true;
        if (isset($_GET['favoritesOf'])){
            $username = $_GET['favoritesOf'];
        } else if(isset($_GET['friends'])) {
            $username = $this->_user->screenName;
        } else if(isset($_GET['screenName']) && User::isMember($_GET['screenName'])) {
            $username = $_GET['screenName'];
        } else{
            $username = null;
        }
        if ($username) {
            $this->user = Photo_UserHelper::load($username);
            Photo_FullNameHelper::initialize(array($this->user));
            $my = $this->user->title == $this->_user->screenName;
            $this->myOwnPhotos = ($this->user->title == $this->_user->screenName);
            if (!$this->user) {
                $this->error = array('title'      => xg_text('OUR_APOLOGIES'),
                                    'subtitle'    => '',
                                    'description' => xg_text('WE_DO_NOT_KNOW_USER'));
                $this->render('error', 'index');
                return;
            }
            if ($_GET['friends']){
                $this->action = 'slideshowFeedFriends';
                $this->parentPage = $this->_buildUrl('photo','listFriends');
                //@TODO change to 's if user have only one friend
                $this->pageTitle .= $my ? xg_text('MY_FRIENDS_PHOTOS') : xg_text('XS_FRIENDS_PHOTOS', Photo_FullNameHelper::fullName($this->user->title));
            } else if (isset($_GET['favoritesOf'])){
                $this->action = 'slideshowFeedFavorites';
                $this->pageTitle .= $my ? xg_text('MY_FAVORITES') : xg_text('XS_FAVORITES', Photo_FullNameHelper::fullName($this->user->title));
                $this->parentPage = $this->_buildUrl('photo','listFavorites', '?screenName='.$username);
            } else {
                $this->action = 'slideshowFeedForContributor';
                $this->pageTitle .= $my ? xg_text('MY_PHOTOS') : xg_text('XS_PHOTOS', Photo_FullNameHelper::fullName($this->user->title));
                $this->parentPage = $this->_buildUrl('photo','listForContributor', '?screenName='.$username);
            }
        } else if (isset($_GET['albumId'])){
            $album = Photo_AlbumHelper::load($_GET['albumId']);
            $this->action = 'slideshowFeedAlbum';
            $this->pageTitle = $album->title;
            $this->parentPage = $this->_buildUrl('album','show','?id='.$_GET['albumId']);
        } else if (isset($_GET['tag'])){
            $this->action = 'slideshowFeedTagged';
            $this->pageTitle = xg_text('ALL_PHOTOS_TAGGED_X', $_GET['tag']);
            $this->parentPage = $this->_buildUrl('photo','listTagged','?tag='.$_GET['tag']);
        } else {
            if($_GET['feed_url']){
                $feedUrl = $_GET['feed_url'];
                if(preg_match('@slideshowFeedAlbum(.*)id=([^&]*)@u', $feedUrl, $matches)) {
                    $album = Photo_AlbumHelper::load(urldecode($matches[2]));
                    $this->action = 'slideshowFeedAlbum';
                    $this->pageTitle = $album->title;
                    $this->parentPage = $this->_buildUrl('album','show','?id='.$matches[2]);
                } else if(preg_match('@slideshowFeed(.*)owner=true@u', $feedUrl, $matches)) {
                    $username = XN_Application::load()->ownerName;
                    $this->action = 'slideshowFeedForContributor';
                    $this->pageTitle .= $username === $this->_user->screenName ? xg_text('MY_PHOTOS') : xg_text('XS_PHOTOS', Photo_FullNameHelper::fullName($username));
                    $this->parentPage = $this->_buildUrl('photo','listForContributor', '?screenName='.$username);
                } else if(preg_match('@slideshowFeedForContributor(.*)screenName=([^&]*)@u', $feedUrl, $matches)) {
                    $username = $matches[2];
                    $this->action = 'slideshowFeedForContributor';
                    $this->pageTitle .= $username === $this->_user->screenName ? xg_text('MY_PHOTOS') : xg_text('XS_PHOTOS', Photo_FullNameHelper::fullName($username));
                    $this->parentPage = $this->_buildUrl('photo','listForContributor', '?screenName='.$username);
                } else if(preg_match('@slideshowFeed(.*)popular=true@u', $feedUrl, $matches)) {
                    $this->action = 'slideshowFeed';
                    $this->pageTitle = xg_text('POPULAR_PHOTOS');
                    $this->parentPage = $this->_buildUrl('photo','index', '?sort=mostPopular');
                } else if(preg_match('@slideshowFeed(.*)promoted=true@u', $feedUrl, $matches)) {
                    $this->action = 'slideshowFeed';
                    $this->pageTitle = xg_text('FEATURED_PHOTOS');
                    $this->parentPage = '/';
                } else {
                    $this->action = 'slideshowFeed';
                    $this->pageTitle = xg_text('ALL_PHOTOS');
                    $this->parentPage = $this->_buildUrl('photo','index');
                    $this->parentLinkText = xg_text('RETURN_TO_PHOTOS_HOME');
                }
            } else {
                $this->action = 'slideshowFeed';
                $this->pageTitle = xg_text('ALL_PHOTOS');
                $this->parentPage = $this->_buildUrl('photo','index');
                $this->parentLinkText = xg_text('RETURN_TO_PHOTOS_HOME');
            }
        }
        $this->useTags = 1;
        if (! $this->parentLinkText) { $this->parentLinkText = xg_text('RETURN_TO_X', $this->pageTitle); }
    }

    /**
     * Returns the embed code for the photo slideshow, and the embed code for its preview.
     *
     * Expected GET parameters:
     *     -
     */
    // @todo Document the parameters
    public function action_embeddableWithPreview() {
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        ob_start();
        $this->_widget->dispatch('photo', 'embeddable', array(array_merge($_GET, array('includeFooterLink' => true))));
        $this->embedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
        ob_end_clean();
        ob_start();
        $this->_widget->dispatch('photo', 'embeddable', array(array_merge($_GET, array('externalPreview' => true))));
        $this->previewEmbedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
        ob_end_clean();
    }

    /**
     * Displays the Flash object for the slideshow player.
     *
     * Expected GET parameters:
     *     Any of the $args parameters can also be passed as GET parameters
     *
     * @param $args array  parameters:
     *      -
     */
    // @todo Document the parameters
    public function action_embeddable($args = array()) {
        $this->args = array_merge($_GET, $args);
        if (array_key_exists('logoImage', $this->args) && $this->args['logoImage']!== 'none') {
            if (!array_key_exists('logoImageWidth', $this->args)) {
                if (preg_match('@\Wwidth=(\d+)@u', $this->args['logoImage'], $matches)) {
                    $this->args['logoImageWidth'] = $matches[1];
                }
            }
            if (!array_key_exists('logoImageHeight', $this->args)) {
                if (preg_match('@\Wheight=(\d+)@u', $this->args['logoImage'], $matches)) {
                    $this->args['logoImageHeight'] = $matches[1];
                }
            }
        }
        if (!isset($this->args['photoSet'])) {
            $this->args['photoSet'] = 'recent';
        }
        $this->args['feed'] = urlencode(Photo_SlideshowHelper::feedUrl($this->args['photoSet'], $this->args['contributorName']));
        $this->args['fullsize_url'] = urlencode($this->_widget->buildUrl('photo', 'slideshow',
                '?feed_url=' . $this->args['feed']));
        $this->args['slideshow_width'] = $this->args['width'];
        $this->args['slideshow_height'] = $this->args['height'];
        foreach ($this->args as $key => $value) { $this->{$key} = $value; }
        $this->args['bgcolor'] = $this->bgColor;
        $this->args['bgimage'] = $this->bgImage;
        $this->args['brand'] = $this->brand;
        $this->args['logoImage'] = $this->logoImage;
        $this->args['logoImageWidth'] = $this->logoImageWidth;
        $this->args['logoImageHeight'] = $this->logoImageHeight;
        $this->args['externalPreview'] = $this->externalPreview;
        $this->args['includeFooterLink'] = $this->includeFooterLink;
    }

    public function action_showPlayerConfig() {
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        if (! Photo_SecurityHelper::canAccessEmbeddableData($_GET)) { throw new Exception(); }
        if ($_GET['internalView'] != 'true') {
            header("Content-Type: text/xml");
        }
        $widget = W_Cache::getWidget('main');
        if (array_key_exists('headingFont', $widget->config) &&
            array_key_exists('siteLinkColor', $widget->config)) {
            $this->selectedFont = $widget->config['headingFont'];
            $this->selectedLinkColor = $widget->config['siteLinkColor'];
        } else {
            $defaults = self::getMainAppearanceSettings();
            $this->selectedFont = $defaults['headingFont'];
            $this->selectedLinkColor = $defaults['siteLinkColor'];
        }
        ob_start();
        $this->renderPartial('fragment_embeddableFooter');
        $this->footerHtml = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
        ob_end_clean();
    }

    public function action_listFriends() {
        XG_SecurityHelper::redirectIfNotMember();
        self::handleSortingAndPagination($this->_user, array('friends' => true),
                                         self::NUM_THUMBS_FOURCOLUMNVIEW);

        $this->pageUrl = $this->_buildUrl('photo', 'listFriends');
        $this->friends = Photo_UserHelper::getFriends($this->_user, 7);
        Photo_FullNameHelper::initialize($this->photos, $this->friends);
    }

    /**
     * Handles pagination and sorting for the list actions.
     *
     * @param profile XN_Profile  The XN_Profile object of the user for whom the photos are queried for
     * @param filters    The filters for selecting the photos
     *                   (see Photo_PhotoHelper::getSortedPhotos)
     * @param numPerPage The number of thumbs per page
     */
    private function handleSortingAndPagination($profile, $filters = null, $numPerPage = self::NUM_THUMBS_TWOCOLUMNVIEW, $beginOffset = 0) {
        $begin = 0;
        if (intval($_GET['page']) > 0) {
            $begin = ($_GET['page'] - 1) * $numPerPage;
        }
        $begin = max(0, $begin + $beginOffset);
        $photosData = Photo_PhotoHelper::getSortedPhotos($profile, $filters, $this->sort, $begin, $begin + $numPerPage);
        $this->isSortRandom = $this->sort['alias'] == Photo_PhotoHelper::SORT_ORDER_RANDOM;
        $this->photos = $photosData['photos'];
        $this->page = 1 + (int)($begin / $numPerPage);
        $this->pageSize = $numPerPage;
        $this->numPages = $photosData['numPhotos'] == 0 ? 1 : 1 + (int)(($photosData['numPhotos'] - 1) / $numPerPage);
        $this->numPhotos = $photosData['numPhotos'];
        $this->sortOptions = $this->getSortOptions();
    }

    /**
     * Returns metadata for the Sort By combobox.
     *
     * @return array  array of arrays, each with displayText, url, and selected
     */
    protected function getSortOptions() {
        return Photo_HtmlHelper::toSortOptions(Photo_PhotoHelper::getKnownSortingOrders(), $this->sort['alias']);
    }

    /**
     * Determines whether there are photos with locations in the given array.
     *
     * @param photos The photos
     * @return true if there is at least one photo with a location
     */
    private function haveLocations($photos) {
        if (count($photos) > 0) {
            foreach ($photos as $photo) {
                if ($photo->my->lat && (mb_strlen($photo->my->lat) > 0) &&
                    $photo->my->lng && (mb_strlen($photo->my->lng) > 0)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Gets the 5 most popular tags for the photo in $this->photo, and stores them in
     * $this->popularTags. Also sets $this->hasMoreTags if more than 5 tags are available
     * for the photo.
     */
    private function getPopularTags($photo, &$popularTags, &$hasMoreTags) {
        // in order to determine whether we have more than the presented 5 tags, we try to fetch 6
        // and if that returns 6, then we have more than 5 :-)
        $popularTags = XG_TagHelper::getTagNamesForObject($photo->id, 6);
        $hasMoreTags = (count($popularTags) > 5);
        if ($hasMoreTags) {
            $popularTags = array_slice($popularTags, 0, 5);;
        }
    }

    public function action_listFavorites() {
        if (isset($_GET['screenName']) && User::isMember($_GET['screenName'])) {
            $this->user = Photo_UserHelper::load($_GET['screenName']);
        } else {
            XG_SecurityHelper::redirectIfNotMember();
            $this->user = Photo_UserHelper::load($this->_user);
        }
        if (!$this->user) {
            $this->error = array('title' => xg_text('SLOW_DOWN_THERE_CHIEF'), 'subtitle' => '', 'description' => xg_text('I_DO_NOT_KNOW_USER'));
            return $this->render('error', 'index');
        }
        if($_GET['rss']=='yes'){
            header("Content-Type: text/xml");
            $this->setCaching(array('photo-photo-listFavorites-' . md5(XG_HttpHelper::currentUrl())), 1800);
            if ($_GET['test_caching']) { var_dump('Not cached'); }
        }
        $this->myOwnFavorites = ($this->user->title == $this->_user->screenName);
        $begin = 0;
        $this->pageSize = 20;
        if (preg_match('@^[.0-9]+$@u', $_GET['page']) && ($_GET['page'] > 0)) {
            $begin = ($_GET['page'] - 1) * $this->pageSize;
        }
        $begin = max(0, $begin);
        $favoritesIds = Photo_ContentHelper::ids($this->user, Photo_UserHelper::attributeName('favorites'));
        $favoritesData = Photo_PhotoHelper::getSpecificPhotos($this->_user, $favoritesIds, $this->sort == Photo_PhotoHelper::getMostRecentSortingOrder() ? null : $this->sort, $begin, $begin + $this->pageSize);
        $this->photos = $favoritesData['photos'];
        $this->numPhotos = $favoritesData['numPhotos'];
        $this->isSortRandom = $this->sort['alias'] == Photo_PhotoHelper::SORT_ORDER_RANDOM;
        $this->pageTitle = $this->myOwnFavorites ?
                                xg_text('MY_FAVORITE_PHOTOS') :
                                xg_text('XS_FAVORITE_PHOTOS', Photo_FullNameHelper::fullName($this->user->title));
        Photo_FullNameHelper::initialize(array_merge(array($this->user), $this->photos));
        if($_GET['rss']=='yes'){
            $appname = XN_Application::load()->name;
            $this->description = xg_xmlentities(xg_text('XS_FAVORITE_PHOTOS_ON_X', Photo_FullNameHelper::fullName($this->user->title), $appname));
            $this->title = xg_xmlentities($this->pageTitle);
            $this->link = $this->_buildUrl('photo','listFavorites', '?screenName=' . $this->user->title);
            $this->feedImageUrl = XG_UserHelper::getThumbnailUrl(Photo_FullNameHelper::profile($this->user->title), 50, 50);
            $this->feedImageHeight = 50;
            $this->useTags = 1;
            $this->render('rss');
        }
        $this->sortOptions = $this->getSortOptions();
    }

    public function action_show() {
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        if (! $this->_user->isLoggedIn()) {
            // Users not logged in can see a cached page
            // Detail page - cache only if we're caching order n items (BAZ-2969)
            if (XG_Cache::cacheOrderN()) { $this->setCaching(array(md5(XG_HttpHelper::currentUrl())), 300); }
        }
        try {
            // Don't use a cached query here, so the view count is up to date
            $this->photo = Photo_PhotoHelper::load($_GET['id'],false, false);
        } catch (Exception $e) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if(isset($_GET['albumId'])) {
            $this->albumId = $_GET['albumId'];
            $this->album = Photo_AlbumHelper::load($_GET['albumId']);
            $photoIds = Photo_ContentHelper::ids($this->album, 'photos');
        }
        if ($this->error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $this->photo)) {
            return $this->render('error', 'index');
        }
        $this->pageSize = XG_CommentHelper::DEFAULT_PAGE_SIZE;
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
        if (XG_CommentHelper::feedAvailable($this->photo)) {
            $this->commentFeedUrl = $this->_buildUrl('comment', 'feed', array('attachedTo' => $this->photo->id, 'xn_auth' => 'no'));
        }
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $commentResults = Photo_CommentHelper::getCommentsFor($this->photo->id, $begin, $begin + $this->pageSize);
        $this->comments = $commentResults['comments'];
        $this->numComments = $commentResults['numComments'];
        $this->context = $_GET['context'] ? $_GET['context'] : 'user';
        $this->nextPhoto = Photo_PhotoHelper::getNextPhoto($this->_user, $this->photo, $this->context, $photoIds);
        $this->previousPhoto = Photo_PhotoHelper::getPreviousPhoto($this->_user, $this->photo, $this->context, $photoIds);

        // get all tags rollup, descending by occurrence
        $this->tags = XG_TagHelper::getTagNamesForObject($this->photo);
        $this->hasMoreTags = count($this->tags) > 5;

        // get tags for user if they're admin/NC or contributor
        if (XG_SecurityHelper::userIsAdminOrContributor($this->_user, $this->photo)) {
            $this->currentUserTagString = XG_TagHelper::implode(XG_TagHelper::getTagNamesForObjectAndUser($this->photo, $this->_user->screenName));
        }

        $albumData = Photo_AlbumHelper::getSortedAlbums(array('photoId' => $this->photo->id), array('attribute' => 'my->photoCount', 'direction' => 'desc', 'type' => XN_Attribute::NUMBER), 0, 50);
        $this->albums = $albumData['albums'];
        Photo_FullNameHelper::initialize(array_merge(array($this->photo), $this->comments));
        Photo_HtmlHelper::getImageUrlAndDimensions($this->photo, $originalUrl, $originalWidth, $originalHeight);
        $this->originalUrl = $originalUrl;
        $this->originalWidth = $originalWidth;
        $this->originalHeight = $originalHeight;
        Photo_HtmlHelper::fitImageIntoThumb($this->photo, 737, 600, $scaledUrl, $scaledWidth, $scaledHeight);
        $this->scaledUrl = $scaledUrl;
        $this->scaledWidth = $scaledWidth;
        $this->scaledHeight = $scaledHeight;
        if ($this->_user->isLoggedIn()) { $this->user = Photo_UserHelper::load($this->_user); }
    }

    /**
     * Shows a photo slideshow of a set of pictures (iPhone-specific)
     * Expected GET parameters:
     *  'activityItemId' ActivityLogItem with photos objects
     * 	'ids' a comma separated list of photo ids to display in the slideshow
     *  'previousUrl' (optional) a url leading back to the screen the user was viewing before following the photo view link
     *  'first' (optional) index of the first photo to show in the slideshow
     *
     */
    public function action_show_iphone() {
        if (! $this->_user->isLoggedIn()) {
            // Users not logged in can see a cached page
            // Detail page - cache only if we're caching order n items (BAZ-2969)
            if (XG_Cache::cacheOrderN()) { $this->setCaching(array(md5(XG_HttpHelper::currentUrl())), 300); }
        }
        try {
            if ($_GET['activityItemId']) {
                $item = XN_Content::load($_GET['activityItemId']);
                $photoIds = explode(',',$item->my->contents);
            } else {
                $photoIds = explode(',',$_GET['ids']);
            }
            $this->photos = array();
            foreach ($photoIds as $photoId) {
                $photo = Photo_PhotoHelper::load($photoId, false, true);
                if ($this->error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $photo)) {
                    return $this->render('error', 'index');
                }
                // Width and Height are taken from the fullscreen slideshow. Feel free to adjust
                Photo_HtmlHelper::fitImageIntoThumb($photo, '800', '604', $url, $width, $height);
                $this->photos[] = array('title' => $photo->title, 'url' => $url, 'scaledWidth' => $width, 'scaledHeight' => $height);
            }
        } catch (Exception $e) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $this->previousUrl = $_GET['previousUrl'];
        $this->first = $_GET['first'];
    }

    /**
     * AJAX POST action that increments the view count of a specific photo.
     */
    public function action_registershown() {
        try {
            $photo = Photo_ContentHelper::findByID('Photo', $_POST['id'], true, false);
            if ($photo && (!$this->_user->isLoggedIn() || ($this->_user->screenName != $this->photo->contributorName))) {
                $photo->incrementViewCount();
                // BAZ-1507: Don't invalidate cache here, or the cache gets blown away on each detail view
                XG_App::setInvalidateFromHooks(false);
                $photo->save();
                XG_App::setInvalidateFromHooks(true);
            }
            Photo_JsonHelper::outputAndExit(array());
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_setTitle() {
        // TODO: Delete this action - does not seem to be called anywhere [Jon Aquino 2008-05-05]
        try {
            XG_SecurityHelper::redirectIfNotMember();
            XG_JoinPromptHelper::joinGroupOnSave();
            $photo = Photo_PhotoHelper::load($_POST['id']);
            if ($this->error = XG_SecurityHelper::userIsNotContributorError($this->_user, $photo)) {
                $this->render('error', 'index');
                return;
            }
            $photo->setTitle($_POST['value']);
            $photo->save();
            self::invalidateRssFeeds($this, $photo->contributorName);
            Photo_JsonHelper::outputAndExit(array('html' => xnhtmlentities($photo->title)));
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_tag() {
        XG_SecurityHelper::redirectIfNotMember();
        try {
            $photo = Photo_PhotoHelper::load($_GET['id']); // needs to be $_GET. [Phil McCluskey 2006-09-22]
            if (! XG_SecurityHelper::userIsAdminOrContributor($this->_user, $photo)) {
                $this->render('error', 'index');
                return;
            }

            XG_TagHelper::updateTagsAndSave($photo, $_POST['tags']);
            self::getPopularTags($photo, $popularTags, $hasMoreTags);

            ob_start();
            $this->renderPartial('fragment_listForDetailPage',
                                 'tag',
                                 array('tags'           => $popularTags,
                                       'photoId'        => $photo->id,
                                       'hasMoreTags'    => $hasMoreTags));
            $html = trim(ob_get_contents());
            ob_end_clean();

            Photo_JsonHelper::outputAndExit(array('html' => $html));
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_getTitle() {
        try {
            $photo = Photo_PhotoHelper::load($_GET['id']);
            if ($this->error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $photo)) {
                $this->render('error', 'index');
                return;
            }
            Photo_JsonHelper::outputAndExit(array('html' => xnhtmlentities($photo->title)));
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_setDescription() {
        // TODO: Delete this action - does not seem to be called anywhere [Jon Aquino 2008-05-05]
        try {
            XG_SecurityHelper::redirectIfNotMember();
            XG_JoinPromptHelper::joinGroupOnSave();
            $photo = Photo_PhotoHelper::load($_POST['id']);
            if ($this->error = XG_SecurityHelper::userIsNotContributorError($this->_user, $photo)) {
                $this->render('error', 'index');
                return;
            }
            $photo->setDescription($_POST['value']);
            $photo->save();

            self::invalidateRssFeeds($this, $photo->contributorName);

            // Don't wrap the description in xnhtmlentities, as we want to display the raw HTML  [Jon Aquino 2006-07-17]
            Photo_JsonHelper::outputAndExit(array('html' => xg_nl2br(xg_resize_embeds($photo->description, 737))));
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_getDescription() {
        try {
            $photo = Photo_PhotoHelper::load($_GET['id']);
            if ($this->error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $photo)) {
                $this->render('error', 'index');
                return;
            }
            Photo_JsonHelper::outputAndExit(array('html' => xg_nl2br($photo->description)));
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_edit() {
        XG_SecurityHelper::redirectIfNotMember();
        /* $this->photos and $this->tags are arrays so that the editMultiple template can be reused */
        $this->photos = array(Photo_PhotoHelper::load($_GET['id']));
        if ($this->error = XG_SecurityHelper::userIsNotContributorError($this->_user, $this->photos[0])) {
            $this->render('error', 'index');
            return;
        }
        $this->user = Photo_UserHelper::load($this->_user);
        $this->tags = array();
        $this->tags[] = $this->_user->isLoggedIn() ? XG_TagHelper::getTagStringForObjectAndUser($this->photos[0]->id, $this->_user->screenName) : '';
        $this->pageTitle = xg_text('EDIT_PHOTO');
        $this->render('editMultiple');
    }

    public function action_editMultiple() {
        XG_SecurityHelper::redirectIfNotMember();
        $ids = array();
        if ($_GET['ids']) {
            // TODO: check if the current user can edit the photos [ywh 2008-07-22]
            $ids = explode(',', $_GET['ids']);
        }
        if (count($ids) == 0) {
            // No ids given
            $this->redirectTo('listForContributor', 'photo', array('screenName' => $this->_user->screenName));
            return;
        }
        $this->pageTitle = xg_text('EDIT_PHOTO_INFORMATION');
        /* So we will display the "here's where you edit what you've just uploaded" message */
        $this->justUploaded = true;
        $this->failedFiles  = $_GET['failedFiles'];
        $photosData   = Photo_PhotoHelper::getSpecificPhotos($this->_user, $ids, null, 0, 100, true);
        $this->photos = $photosData['photos'];

        Photo_ContentHelper::sortByAttribute($this->photos, $ids);

        $this->user = Photo_UserHelper::load($this->_user);
        $this->tags = array();
        foreach ($this->photos as $idx => $photo) {
            $tags             = XG_TagHelper::getTagsForObjectAndUser($photo->id, $this->_user->screenName);
            $tagNames         = XN_Tag::tagNamesFromTags($tags);
            $this->tags[$idx] = count($tags) > 0 ? implode(", ", $tagNames) : "";
        }
    }

    public function action_new() {
        XG_SecurityHelper::redirectIfNotMember();
        $curWidget = W_Cache::getWidget('photo');
        if ($this->error = Photo_SecurityHelper::checkCurrentUserCanAddPhotos($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        XG_MediaUploaderHelper::setUsingMediaUploader(false);
        $this->hideBulkUploaderReferences = W_Cache::getWidget('main')->config['hideBulkUploader'] == 'yes';
        $this->user = Photo_UserHelper::load($this->_user);
        $this->approvalRequired = Photo_SecurityHelper::isApprovalRequired() &&
                                  Photo_SecurityHelper::failed(Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user));
        if ($_GET['test_approval_required']) { $this->approvalRequired = $_GET['test_approval_required'] == 'yes'; }
        $this->tags = XG_TagHelper::getTagsForUser($this->_user->screenName, 25);
        // we might have come here because the previous upload failed
        $this->sizeLimitError = $_GET['sizeLimitError'];
        $this->failedFiles    = $_GET['failedFiles'];
        $this->flickrEnabled = $curWidget->privateConfig['flickrEnabled'];
        if (($this->flickrEnabled != 'Y') && ($curWidget->privateConfig['promptOwnerForFlickr'] != 'N')) {
            $this->showFlickrToOwner = 'Y';
        }
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
        if ($this->error = Photo_SecurityHelper::checkCurrentUserCanAddPhotos($this->_user)) { return $this->render('error', 'index'); }
        XG_MediaUploaderHelper::setUsingMediaUploader(true);
        $this->flickrEnabled = $this->_widget->privateConfig['flickrEnabled'];
        if ($this->flickrEnabled != 'Y' && $this->_widget->privateConfig['promptOwnerForFlickr'] != 'N') {
            $this->showFlickrToOwner = 'Y';
        }
    }

    /**
     * Redirects to the Media Uploader or the simple uploader, depending on the
     * capabilities of the browser. The current GET parameters will be preserved.
     */
    public function action_chooseUploader() {
        W_Cache::getWidget('main')->dispatch('mediauploader', 'chooseUploader');
    }

    public function action_addByPhone() {
        XG_SecurityHelper::redirectIfNotMember();
        $curWidget = W_Cache::getWidget('photo');
        if ($this->error = Photo_SecurityHelper::checkCurrentUserCanAddPhotos($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->user = Photo_UserHelper::load($this->_user);
        $this->approvalRequired = Photo_SecurityHelper::isApprovalRequired() &&
                                  Photo_SecurityHelper::failed(Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user));
        if ($_GET['test_approval_required']) { $this->approvalRequired = $_GET['test_approval_required'] == 'yes'; }
        $this->tags = XG_TagHelper::getTagsForUser($this->_user->screenName, 25);
        $this->flickrEnabled = $curWidget->privateConfig['flickrEnabled'];
        if (($this->flickrEnabled != 'Y') && ($curWidget->privateConfig['promptOwnerForFlickr'] != 'N')) {
            $this->showFlickrToOwner = 'Y';
        }
        $app = XN_Application::load();
        $this->appName = $app->name;
    }

    public function action_flickr() {
        XG_SecurityHelper::redirectIfNotMember();
        $curWidget = W_Cache::getWidget('photo');
        if ($this->error = Photo_SecurityHelper::checkCurrentUserCanAddPhotos($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->user = Photo_UserHelper::load($this->_user);
        $this->approvalRequired = Photo_SecurityHelper::isApprovalRequired() &&
                                  Photo_SecurityHelper::failed(Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user));
        if ($_GET['test_approval_required']) { $this->approvalRequired = $_GET['test_approval_required'] == 'yes'; }
        if (Photo_UserHelper::get($this->user, 'flickrAuthentication') == 'Y') {
            $this->redirectTo('index', 'flickr');
        } else {
            XG_App::includeFileOnce('/lib/XG_TagHelper.php');
            $this->tags = XG_TagHelper::getTagsForUser($this->_user->screenName, 25);
            $this->flickrEnabled = $curWidget->privateConfig['flickrEnabled'];
            if (($this->flickrEnabled != 'Y') && ($curWidget->privateConfig['promptOwnerForFlickr'] != 'N')) {
                $this->showFlickrToOwner = 'Y';
            }
            $app = XN_Application::load();
            $this->appName = $app->name;
        }
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
            $result = Photo_GeocodingHelper::geocode($_GET['address']);
            try {
                Photo_JsonHelper::outputAndExit($result);
            } catch (Exception $e) {
                Photo_JsonHelper::handleExceptionInAjaxCall($e);
            }
        }
    }

    //will dispatch the form values to action_createMultiple if it is a POST
    //otherwise just set the template variables to the add content module
    public function action_addContent() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if ($this->error = Photo_SecurityHelper::checkCurrentUserCanAddPhotos($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->user = Photo_UserHelper::load($this->_user);
        $this->approvalRequired = Photo_SecurityHelper::isApprovalRequired() &&
                                  Photo_SecurityHelper::failed(Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user));
        if ($_GET['test_approval_required']) { $this->approvalRequired = $_GET['test_approval_required'] == 'yes'; }
        // we might have come here because the previous upload failed
        $this->sizeLimitError = $_GET['sizeLimitError'];
        $this->failedFiles    = $_GET['failedFiles'];
        $this->prefix = W_Cache::current('W_Widget')->dir;
        $this->defaultTitle = xg_html('DEFAULT_TITLE');
        $this->defaultDescription = xg_html('DEFAULT_DESCRIPTION');
        $widget = W_Cache::current('W_Widget');

        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // TODO: For now we determine whether the file size limit was exceeded, via a hidden input (see PHO-543)
                if (!isset($_POST['uploadMarker'])) {
                    $this->sizeLimitError = xg_text('UPLOAD_LIMIT_EXCEEDED');
                    return;
                }
                $photoIds = $widget->dispatch('photo','createMultiple',array('dispatched'=>true));
                foreach($photoIds as $photoId) {
                    $photo = W_Content::load($photoId);
                    if ($_POST[$this->prefix.'_title']!=$this->defaultTitle) {
                        $photo->setTitle($_POST[$this->prefix.'_title']);
                    }
                    if($_POST[$this->prefix.'_description']!=$this->defaultDescription) {
                        $photo->setDescription($_POST[$this->prefix.'_description']);
                    }
                    $photo->save();
                }
            }
        } catch (Exception $e) {
            error_log("$this->prefix addContent error: {$e->getMessage()}");
            if (is_callable(array($e,'getErrorsAsString'))) {
                error_log($e->getErrorsAsString());
            }
        }
    }

    // handler for the "quick post" feature
    public function action_createMultipleQuick() { # void
        if (!isset($_POST['uploadMarker'])) {
            $this->render('blank');
            error_log('Partial upload');
            return; // partial upload
        }
        $photoIds = $this->action_createMultiple(true);
        $this->render('blank');
        if (count($photoIds)) {
            $approved = !Photo_SecurityHelper::failed(Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) ||
                        !Photo_SecurityHelper::isApprovalRequired();
            if ($approved) {
                $this->status = 'ok';
                if (count($photoIds) == 1) {
                    $this->viewUrl = $this->_buildUrl('photo', 'show', array('id' => reset($photoIds)));
                    $this->viewText = xg_html('VIEW_THIS_PHOTO');
                    $this->message = xg_html('YOUR_PHOTO_WAS_UPLOADED');
                } else {
                    $this->viewUrl = $this->_buildUrl('photo', 'listForContributor', array('screenName' => $this->_user->screenName));
                    $this->viewText = xg_html('VIEW_THESE_PHOTOS');
                    $this->message = xg_html('YOUR_PHOTOS_WERE_UPLOADED');
                }
            } else {
                $this->status = 'not_approved';
                if (count($photoIds) == 1) {
                    $this->message = xg_html('YOUR_PHOTO_WAS_SUCCESSFULLY');
                } else {
                    $this->message = xg_html('YOUR_PHOTOS_WERE_SUCCESSFULLY');
                }
            }
        } else {
            $this->status = 'fail';
            $this->message = xg_html('ERROR_UPLOADING_PHOTOS');
        }
    }

    public function action_createMultiple($dispatched = false) {
        // Used from action_createMultipleQuick()
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if ($this->error = Photo_SecurityHelper::checkCurrentUserCanAddPhotos($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        // TODO: For now we determine whether the file size limit was exceeded, via a hidden input (see PHO-543)
        if (!isset($_POST['uploadMarker'])) {
            $this->redirectTo('new', 'photo', array('sizeLimitError' => xg_text('UPLOAD_LIMIT_EXCEEDED')));
            return;
        }
        // TODO: Replace the logic below with a few calls to Photo_PhotoHelper::upload()  [Jon Aquino 2008-01-04]
        $approved  = !Photo_SecurityHelper::failed(Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) ||
                     !Photo_SecurityHelper::isApprovalRequired() ? 'Y' : 'N';
        $photoIds  = array();
        $photos    = array();
        $user      = Photo_UserHelper::load($this->_user);
        $filenames = array();
        $erroneous = array();



        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        if($approved=='Y') $logItem = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_PHOTO, $this->_user->screenName, $photos);

        if ($_POST['featureOnMain']) {
            XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
            $featureOnMain = 1;
        } else {
            $featureOnMain = 0;
        }
        foreach ($_POST as $var => $value) {
            $matches = array();
            if (preg_match('@^photo(\d+)$@u', $var, $matches)) {
                $idx = intval($matches[1]);
                $mimeType        = $_POST["$var:type"];
                $filenames[$idx] = $_POST["$var"];
                if ($_POST["$var:status"]) {
                    $erroneous[] = $filenames[$idx];
                    continue;
                }
                if (isset($_POST["$var:status"]) && ($_POST["$var:status"] == 0)) {
                    if (($mimeType && !preg_match('@^image/.*@u', $mimeType))||($mimeType=='image/tiff')) {
                        $erroneous[] = $filenames[$idx];
                    } else {
                        $stripped_filename = preg_replace('/\.(jpe?g|gif|bmp|png)$/ui','',$filenames[$idx]);
                        // Also remove initial directory paths that IE may insert
                        if (mb_strpos($stripped_filename,'\\') !== false) {
                            $stripped_filename = preg_replace('@^.*\\\\([^\\\\]+)$@u','$1',$stripped_filename);
                        }
                        $photo = Photo_PhotoHelper::create();
                        $photo->title = $stripped_filename;
                        $photo->set('data', $_POST[$var], XN_Attribute::UPLOADEDFILE);
                        $photo->setApproved($approved);
                        $photo->my->mimeType = $_POST["$var:type"];
                        $photo->setVisibility(Photo_UserHelper::get($user, 'defaultVisibility'));

                        if ($featureOnMain && XG_PromotionHelper::currentUserCanPromote($photo)) {
                            XG_PromotionHelper::promote($photo);
                        }

                        if($logItem) $photo->my->newContentLogItem = $logItem->id;
                        $photos[$idx] = $photo;
                    }
                }
            }
        }
        if (count($photos) > 0) {
            // we want the photos to be created in the upload order (which we encoded in the form)
            // rather than the order that the browser choose to put them into the request
            ksort($photos, SORT_NUMERIC);
            foreach ($photos as $idx => $photo) {
                // Is this action passed any POST values used by updateWithPostValuesAndSave?
                // If not, then we don't need to call updateWithPostValuesAndSave() [Jon Aquino 2008-02-11]
                $this->updateWithPostValuesAndSave($photo, $_POST);
                $photoIds[$idx] = $photo->id;
            }
            if(($approved=='Y')&&(count($photoIds)>0)&&($logItem)){
                $logItem->my->contents = implode(',',$photoIds);
                $logItem->save();
            }
        }
        if (count($photos) > 0) {
            if ($approved == 'N') {
                Photo_MessagingHelper::photosAwaitingApproval($photos, $user->title);
            } else {
                Photo_UserHelper::addPhotos($user);
            }
            $user->save();

            self::invalidateRssFeeds($this, $user->title);
            self::invalidateSlideshowFeeds($this, $user->title);
            if($dispatched) {
                return $photoIds;
            }
            $this->redirectTo('editMultiple', 'photo',
                                          array('ids'         => implode(',', $photoIds),
                                                'failedFiles' => implode(',', $erroneous)));
        } else {
            if($dispatched) {
                return $photoIds;
            }
            $this->redirectTo('new', 'photo',
                              array('failedFiles' => implode(',', $erroneous)));
        }
    }

    public function logPhotoUpdate($photo) {
        //create activity log item
        if( ($photo) && ($photo->my->approved == 'Y') && ($photo->my->newContentLogItem)) {
            try {
                $logItem = XN_Content::load($photo->my->newContentLogItem);
            } catch (Exception $e) {
                $photo->my->newContentLogItem = null;
                $photo->save();
                return;
            }
            $visibilityChoices = array('all', 'friends', 'me');
            if( ($logItem->my->visibility) && (array_search($photo->my->visibility,$visibilityChoices) > array_search($logItem->my->visibility,$visibilityChoices)) ){
                $logItem->my->visibility = $photo->my->visibility;
                $logItem->save();
            }
        }
    }


    public function action_createTestPhotos() {
        if (XN_Profile::current()->screenName != 'JonathanAquino' && ! XG_SecurityHelper::userIsAdmin()) { throw new Exception(); }
        for ($i = 0; $i < $_GET['n']; $i++) {
            $response = XN_REST::post( '/content?binary=true&type=Photo', file_get_contents( 'http://yubnub.org/images/xml_button.gif' ), 'image/gif');
            $photoObject = XN_AtomHelper::loadFromAtomFeed( $response, 'XN_Content');
            $photo = W_Content::load($photoObject);
            Photo_PhotoHelper::initialize($photo);
            $photo->setApproved(Photo_SecurityHelper::passed(Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) || !Photo_SecurityHelper::isApprovalRequired() ? 'Y' : 'N');
            $photo->my->mimeType = 'image/png';
            $photo->setVisibility(Photo_UserHelper::get(Photo_UserHelper::load($this->_user), 'defaultVisibility'));
            $photo->save();
            $photo->title = $photo->id;
            $photo->save();
            echo $photo->debugHTML();
        }
        echo 'Done';
    }

    /**
     * Updates multiple photos.
     */
    public function action_updateMultiple() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        // we first gather the values
        $photoData = array();
        $photoIds  = array();
        foreach ($_POST as $var => $value) {
            if (preg_match('@^photo(\d+)-(.*)$@u', $var, $matches)) {
                $num       = $matches[1];
                $attrName  = $matches[2];
                if (!$photoData[$num]) {
                    $photoData[$num] = array();
                }
                if ($attrName == 'id') {
                    $photoIds[] = $value;
                }
                $photoData[$num][$attrName] = $value;
            }
        }
        // PHO-516: Tell getSpecificPhotos() to ignore approval so that settings get saved on just-uploaded
        // moderated photos [ David Sklar 2006-10-06 ]
        $photos = Photo_PhotoHelper::getSpecificPhotos($this->_user, $photoIds, null, 0, 100, true);

        foreach ($photos['photos'] as $photo) {
            foreach ($photoData as $curPhotoData) {
                if ($curPhotoData['id'] == $photo->id) {
                    $this->updateWithPostValuesAndSave(W_Content::create($photo), $curPhotoData);
                }
            }
        }
        Photo_PhotoHelper::updatePhotoCount(User::load($this->_user));
        self::invalidateRssFeeds($this, $this->_user->screenName);
        self::invalidateSlideshowFeeds($this,$this->_user->screenName);


        if (count($photos['photos']) == 1) {
            $this->redirectTo('show', 'photo', array('id' => $photo->id));
            return;
        }
        $this->redirectTo('listForContributor', 'photo', array('screenName' => $this->_user->screenName));
    }

    /**
     * Endpoint for uploading photos by mail. To enable it, you have to
     * define this URL mapping for the pattern '/xn/content(?:\?(.*))?' :
     *
     * /index.php/main/photo/uploadByMail
     *
     * @return whether this action can handle the given content
     */
    public function action_uploadByMail() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        // TODO: Fix: The check (isAppBlockingCurrentUser) does not seem to match
        // the error message (uploadedToPrivateApp)  [Jon Aquino 2008-01-04]
        if (Photo_PrivacyHelper::isAppBlockingCurrentUser()) {
            Photo_MessagingHelper::uploadedToPrivateApp($this->_user, $_POST['subject']);
            return false;
        }
        if (! Photo_PhotoHelper::imageMimeType($_POST['content:type'], $_POST['content'])) { return false; }
        $user = Photo_UserHelper::load(XN_Profile::current());
        $body = strip_tags(xg_scrub($_POST['body'])); // Validate html and then remove it. BAZ-10093 [Andrey 2008-09-17]
        Photo_PhotoHelper::upload('content', $_POST['subject'], $body, Photo_UserHelper::get($user, 'defaultVisibility'), null);
        return true;
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
            if ($error = Photo_SecurityHelper::checkCurrentUserCanAddPhotos($this->_user)) { XG_MediaUploaderHelper::exitWithError('media-uploader:2'); }
            if ($_POST['content:status']) { XG_MediaUploaderHelper::exitWithError('media-uploader:1', XG_FileHelper::uploadErrorMessage($_POST['content:status'])); }
            if (! Photo_PhotoHelper::imageMimeType($_POST['content:type'], $_POST['content'])) { XG_MediaUploaderHelper::exitWithError('media-uploader:5'); }
            Photo_PhotoHelper::upload('content', $_POST['title'], $_POST['description'], $_POST['visibility'], $_POST['tags']);
            XG_MediaUploaderHelper::exitWithSuccess(!XG_SecurityHelper::userIsAdmin(XN_Profile::current()) && Photo_SecurityHelper::isApprovalRequired());
        } catch (Exception $e) {
            XG_MediaUploaderHelper::exitWithError('media-uploader:6', $e->getMessage());
        }
    }

    /**
     * Updates the photo object with values from the argument array, and saves it then.
     *
     * @param $photo The photo object, expected to be a Photo object (not XN_Content)
     * @param $args  The argument array (e.g. $_POST): title, description, visibility,
     *               locationType, address, lat, lng, zoomLevel, tags, rotation
     */
    private function updateWithPostValuesAndSave($photo, $args) {
        if($args['title']) $photo->setTitle($args['title']);
        $photo->setDescription($args['description']);
        if ($args['visibility']) { $photo->setVisibility($args['visibility']); }
        if (mb_strlen($args['rotation'])) { $photo->my->rotation = $args['rotation']; }
        $photo->my->location = $args['location'];
        $photo->my->address = null;
        $photo->my->lat = null;
        $photo->my->lng = null;
        $photo->my->locationInfo = null;
        switch ($args['locationType']) {
            case 'address':
                if ($args['address']) {
                    $photo->my->address = $args['address'];
                }
                // fall through so that if we have lat and lng for this address,
                // then we store it as well
            case 'latlng':
                if ($args['lat'] && $args['lng'] && $args['lat'] != 25 && $args['lng'] != -40) {
                    if (!is_numeric($args['lat'])) {
                        throw new Exception('Latitude should be a number');
                    }
                    if (!is_numeric($args['lng'])) {
                        throw new Exception('Longitude should be a number');
                    }
                    if ($_POST['zoomLevel'] && !is_numeric($args['zoomLevel'])) {
                        throw new Exception('The zoom level should be a number');
                    }
                    $photo->my->lat          = $args['lat'];
                    $photo->my->lng          = $args['lng'];
                    $photo->my->locationInfo = $args['zoomLevel'];
                }
                break;
        }
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        if (XG_PromotionHelper::isPromoted($photo) && $photo->my->visibility != 'all') { XG_PromotionHelper::remove($photo); }
        XG_TagHelper::updateTagsAndSave($photo, $args['tags']);
        self::logPhotoUpdate($photo);
    }

    /**
     * Rotates the photo 90 degrees counter-clockwise.
     *
     * Expected GET variables:
     *         - id - the content ID of the Photo
     *         - xn_out - json to return Ajax data,or null to redirect to the target
     *         - target - the URL to redirect to, if xn_out is not specified
     *         - maxWidth - the max width for the thumbnail, in pixels; required if xn_out = 'json'
     *         - maxHeight - the max height for the thumbnail, in pixels; required if xn_out = 'json'
     *         - save - whether to save the changes
     *
     * Expected POST variables:
     *         - rotation - the new rotation in degrees, or null to rotate the photo clockwise by 90 degrees
     */
    public function action_rotate() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (339135691)'); }
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        $photo = Photo_ContentHelper::findByID('Photo', $_GET['id']);
        if (! XG_SecurityHelper::userIsContributor($this->_user, $photo)) { throw new Exception('Not allowed (351304774)'); }
        if (isset($_POST['rotation'])) {
            $this->setRotation($photo, $_POST['rotation']);
        } else {
            $photo->rotateRight();
        }
        if ($_GET['save']) { $photo->save(); }
        self::invalidateRssFeeds($this, $photo->contributorName);
        self::invalidateSlideshowFeeds($this, $photo->contributorName);
        if ($_GET['xn_out'] == 'json') {
            Photo_HtmlHelper::getImageUrlAndDimensions($photo, $imgUrl, $realWidth, $realHeight);
            Photo_HtmlHelper::fitImageIntoThumb($photo, $_GET['maxWidth'], $_GET['maxHeight'], $imgUrl, $width, $height);
            Photo_JsonHelper::outputAndExit(array('imgUrl' => $imgUrl));
        } else {
            $this->redirectTo($_GET['target']);
        }
    }

    /**
     * Sets the rotation of the photo. Does nothing if the rotation is not a valid value.
     *
     * @param $photo W_Content  the Photo to rotate
     * @param $rotation integer  0, 90, 180, or 270
     */
    protected function setRotation(W_Content $photo, $rotation) {
        if (in_array($rotation, array(0, 90, 180, 270))) {
            $photo->my->rotation = $rotation;
        }
    }

    /**
     * Deletes a photo. Note that this action should only be called by doing a POST via AJAX as it returns nothing.
     */
    public function action_delete() {
        // TODO: Is this action still called? If not, we should delete it. [Jon Aquino 2008-04-01]
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        $photo = Photo_PhotoHelper::load($_POST['id']);
        if (($photo != null) && XG_SecurityHelper::userIsAdminOrContributor($this->_user, $photo)) {
                // delete comments for the photo [Phil McCluskey 2007-01-18]
                $x = Comment::getCommentsFor($photo->id);
                Comment::removeComments($x['comments']);

                Photo_PhotoHelper::delete($photo);

                self::invalidateRssFeeds($this, $photo->contributorName);
                self::invalidateSlideshowFeeds($this, $photo->contributorName);

                // Output something; otherwise dojo.io.bind does not seem to get a response
                // (something to do with the v2 layout?) [Jon Aquino 2006-12-03]
                Photo_JsonHelper::outputAndExit(array());
        } else {
            header("HTTP/1.0 403 Forbidden");
        }
    }

    private function approveOrReject($photo, $approve, $notifyContributor = true, $save = true) {
        if ($approve) {
            W_Content::load($photo)->setApproved('Y');
            if ($save) {
                $photo->save();
            }
            if ($notifyContributor) { Photo_MessagingHelper::photoApproved($photo); }
        } else {
            W_Content::load($photo)->setApproved('N');
            if ($save) {
                $photo->save();
            }
            if ($notifyContributor) { Photo_MessagingHelper::photoRejected($photo); }
            Photo_PhotoHelper::delete($photo);
        }
    }

    public function action_approve() {
        XG_SecurityHelper::redirectIfNotMember();
        try {
            $photo = Photo_PhotoHelper::load($_POST['id']);
            if (($photo != null) && (Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user) == null)) {
                $this->approveOrReject($photo, $_POST['approved'] == 'Y', true, false);
                if ($_POST['approved'] == 'Y') {
                    Photo_PhotoHelper::logPhotoCreation($photo, false);
                    $photo->save();
                }
            }
            if ($_POST['approved'] == 'Y') {
                Photo_UserHelper::addPhotos(User::load($photo->contributorName))->save();
            }
            self::handleSortingAndPagination($this->_user, array('forApproval' => true), self::NUM_THUMBS_TWOCOLUMNVIEW);
            Photo_FullNameHelper::initialize($this->photos);
            ob_start();
            $this->renderPartial('fragment_listForApproval',
                                 'photo',
                                 array('photos'    => $this->photos,
                                       'changeUrl' => $this->_buildUrl('photo', 'listForApproval'),
                                       'curPage'   => $this->page,
                                       'numPages'  => $this->numPages));
            $html = trim(ob_get_contents());
            ob_end_clean();

            Photo_JsonHelper::outputAndExit(array('html' => $html));
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }


    /**
     * Displays the form for uploading a logo image.
     *
     * @param $error string  An error message to display.
     */
    public function action_customizePlayer($error = null) {
        if ($this->error = Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        if ($this->_widget->privateConfig['playerLogoType'] == 'header_image') {
            $this->currentLogo = $this->_widget->privateConfig['playerImageUrl'];
        }
        else {
            $this->currentLogo = NULL;
        }
        $this->error = $error;
    }

    /**
     * Processes the form for uploading a logo image.
     */
    public function action_doCustomizePlayer() {
        if ($this->error = Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) {
            $this->render('error', 'index');
            return;
        }

        XG_App::includeFileOnce('/lib/XG_FileHelper.php');

        switch($_POST['header_logo_file_action']) {
            case 'add':
                if ($_POST['header_logo_file:status'] > 0) {
                    $this->forwardTo('customizePlayer', 'photo', array(XG_FileHelper::uploadErrorMessage($_POST['header_logo_file:status'])));
                    return;
                }
                else if ($_POST['header_logo_file']) {
                    $this->_widget->privateConfig['playerImageUrl'] = SlideshowPlayerImage::updateWithPostValues('header_logo_file');
                    $this->_widget->privateConfig['playerLogoType'] = 'header_image';
                } else {
                    throw new Exception('Action is add but header_logo_file not specified.');
                }
                break;
            case 'remove':
                $this->_widget->privateConfig['playerImageUrl'] = "";
                $this->_widget->privateConfig['playerLogoType'] = 'header_text';
                break;
        }
        $this->_widget->privateConfig['playerHeaderBackground'] = $_POST['player_header_background'];

        $this->_widget->saveConfig();
        header('Location: ' . self::getUrlForPostPlayerLogoCustomization());
    }

    /**
     * Returns the URL to go to after the site owner changes or removes the logo for the player.
     *
     * @return string  The URL to redirect to.
     */
    private function getUrlForPostPlayerLogoCustomization() {
        return $this->_buildUrl('photo', 'index');
    }

    /** @see "Handling Uploaded Files", http://documentation.ning.com/post.php?Post:slug=FileUpload */
    private static function uploadErrorMessage($status) {
        switch ($status) {
            case 1:
                return xg_text('FILE_EXCEEDED_MAXIMUM_SIZE');
            case 2:
                return xg_text('FILE_EXCEEDED_MAXIMUM_SIZE');
            case 3:
                return xg_text('PART_OF_FILE_WAS_UPLOADED');
            case 4:
                return xg_text('NO_FILE_WAS_UPLOADED');
            default:
                return xg_text('PROBLEM_OCCURRED_DURING_UPLOAD');
        }
    }


    private static function invalidateRssFeeds($object, $username, $tags=null, $albums=null, $favorites=null){
        //@FUTURE invalidate cache of tag feeds for all tags of this photo
        //@FUTURE invalidate cache of favorites feeds of all users that have this photo
        //@FUTURE invalidate cache of albums feeds containing this photo
        // $object->invalidateCache(Photo_PhotoHelper::PHOTO_RSS);
        // $object->invalidateCache(Photo_PhotoHelper::PHOTO_RSS_CONTRIBUTOR.$username);
    }

    private static function invalidateSlideshowFeeds($object, $username, $tags=null, $albums=null, $favorites=null){
        //@FUTURE invalidate cache of tag feeds for all tags of this photo
        //@FUTURE invalidate cache of favorites feeds of all users that have this photo
        //@FUTURE invalidate cache of albums feeds containing this photo
        // $object->invalidateCache(Photo_PhotoHelper::PHOTO_SLIDESHOW.'SMALL');
        // $object->invalidateCache(Photo_PhotoHelper::PHOTO_SLIDESHOW.'MINI');
        // $object->invalidateCache(Photo_PhotoHelper::PHOTO_SLIDESHOW_CONTRIBUTOR.'SMALL_'.$username);
        // $object->invalidateCache(Photo_PhotoHelper::PHOTO_SLIDESHOW_CONTRIBUTOR.'MINI_'.$username);
    }

    /**
     * Returns a string to uniquely identify a cache in the current action. Can be used anywhere to invalidate the cache.
     */
    private function invalidationConditionForCurrentAction($qualifier = null) {
        $names = XG_App::getRequestedRoute();
        return $names['widgetName'] . '-' . $names['controllerName'] . '-' . $names['actionName'] . ($qualifier ? '-' . $qualifier : '');
    }

    private static function getMainAppearanceSettings(){
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
        $defaults = array();
        $imagePaths = array();
        Index_AppearanceHelper::getAppearanceSettings(NULL, $defaults,$imagePaths);
        return $defaults;
    }

}
