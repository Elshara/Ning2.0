<?php

class Photo_AlbumController extends W_Controller {
    /** The number of albums on a four column view. */
    const NUM_THUMBS_FOURCOLUMNVIEW = 12; // 3 rows with 4 columns
    /** The number of photo thumbs on a two column view. */
    const NUM_THUMBS_TWOCOLUMNVIEW = 10; // 5 rows with 2 columns
    /** The number of photo thumbs on the make/edit album view. */
    const NUM_THUMBS_ALBUMEDITVIEW = 20; // 5 rows with 4 columns
    /** The cahe lifetime for RSS feeds */
    const TIME_CACHE_RSS = 1200;


    public function action_overridePrivacy($action) {
        $rssParam = $_GET['rss'] ?? '';
        $rss = is_scalar($rssParam) ? trim((string) $rssParam) : '';

        return (! XG_App::appIsPrivate() && $action == 'show' && $rss === 'yes');
    }

    protected function _before() {
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
        $this->_widget->includeFileOnce('/lib/helpers/Photo_LogHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PrivacyHelper.php');
        Photo_PrivacyHelper::checkMembership();
        Photo_HttpHelper::trimGetAndPostValues();
    }

    public function action_gfx() {
        Photo_LogHelper::log('debug: ' . Photo_HttpHelper::currentUrl());
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        Photo_LogHelper::log('referrer: ' . $referer);
    }

    /**
     * Returns the sort order description for the sort order
     * specified in the $_GET array.
     *
     * @param _GET['sort'] The current sort order
     * @return The sort descriptor
     */
    private function getSortDescriptor() {
        $sorts = Photo_AlbumHelper::getKnownSortingOrders();
        $requested = $_GET['sort'] ?? null;

        if ($requested !== null && isset($sorts[$requested])) {
            return $sorts[$requested];
        }

        return $sorts[Photo_AlbumHelper::SORT_ORDER_MOSTRECENT];
    }

    /**
     * AJAX action that returns the albums of the current user to which the indicated photo
     * can still be added to.
     */
    public function action_getAvailableAlbumsFor() {
        try {
            if (!$this->_user->isLoggedIn()) {
                Photo_JsonHelper::outputAndExit(array());
            }

            $photoIdRaw = $_GET['photoId'] ?? null;
            $photoId = is_scalar($photoIdRaw) ? trim((string) $photoIdRaw) : '';
            $albums = Photo_AlbumHelper::getAllAvailableAlbums(
                $this->_user->screenName,
                $photoId !== '' ? $photoId : null
            );

            Photo_JsonHelper::outputAndExit(array('albums' => $albums));
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    /**
     * AJAX POST action that adds a photo to an album, and creates the album in the
     * process if necessary. If $_POST['render'] == 'bar', the action also renders
     * the updated "album bar" for the provided photo. You should probably supply
     * $_GET['xn_out'] = 'htmljson' with that so as to get the HTML back in a nice,
     * digestible form.
     */
    public function action_addPhoto() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if (!$this->_user->isLoggedIn()) {
            header("HTTP/1.0 403 Forbidden");
            return;
        }

        $photoIdRaw = $_POST['photoId'] ?? null;
        $photoId = is_scalar($photoIdRaw) ? trim((string) $photoIdRaw) : '';
        if ($photoId === '') {
            header("HTTP/1.0 403 Forbidden");
            return;
        }

        $photo = Photo_PhotoHelper::load($photoId);
        $album = null;
        $newAlbumNameRaw = $_POST['newAlbumName'] ?? '';
        $newAlbumName = is_scalar($newAlbumNameRaw) ? trim((string) $newAlbumNameRaw) : '';
        if ($newAlbumName !== '') {
            $album = Photo_AlbumHelper::create();
            $album->title = $newAlbumName;
        } else {
            $albumIdRaw = $_POST['albumId'] ?? null;
            $albumId = is_scalar($albumIdRaw) ? trim((string) $albumIdRaw) : '';
            if ($albumId !== '') {
                $album = Photo_AlbumHelper::load($albumId);
            }
        }

        if (!$photo || !$album || (($album->contributorName != null) && ($album->contributorName != $this->_user->screenName))) {
            header("HTTP/1.0 403 Forbidden");
            return;
        }
        Photo_AlbumHelper::addPhoto($this->_user, $album, $photo);
        header("HTTP/1.0 200 OK");

        $renderRaw = $_POST['render'] ?? '';
        $render = is_scalar($renderRaw) ? trim((string) $renderRaw) : '';
        if ($render === 'bar') {
            $albumData = Photo_AlbumHelper::getAlbums(array('photoId' => $photo->id),
                                                      0,
                                                      7);
            $this->albums      = $albumData['albums'];
            $this->coverPhotos = Photo_AlbumHelper::getCoverPhotos($this->albums);
        }
    }

    /**
     * AJAX action that removes a photo from an album.
     */
    public function action_removePhoto() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        if (!$this->_user->isLoggedIn()) {
            header("HTTP/1.0 403 Forbidden");
            return;
        }

        $photoIdRaw = $_POST['photoId'] ?? null;
        $photoId = is_scalar($photoIdRaw) ? trim((string) $photoIdRaw) : '';
        if ($photoId === '') {
            header("HTTP/1.0 403 Forbidden");
            return;
        }

        $photo = Photo_PhotoHelper::load($photoId);
        $album = null;
        $albumIdRaw = $_POST['albumId'] ?? null;
        $albumId = is_scalar($albumIdRaw) ? trim((string) $albumIdRaw) : '';
        if ($albumId !== '') {
            $album = Photo_AlbumHelper::load($albumId);
        } else {
            $newAlbumNameRaw = $_POST['newAlbumName'] ?? '';
            $newAlbumName = is_scalar($newAlbumNameRaw) ? trim((string) $newAlbumNameRaw) : '';
            if ($newAlbumName !== '') {
                $album = Photo_AlbumHelper::create();
                $album->title = $newAlbumName;
            }
        }

        if (!$photo || !$album || ($album->contributorName != $this->_user->screenName)) {
            header("HTTP/1.0 403 Forbidden");
            return;
        }
        Photo_AlbumHelper::removePhoto($this->_user, $album, $photo);
        header("HTTP/1.0 200 OK");
    }

    /**
     * Shows the albums of a particular owner.
     */
    public function action_listForOwner() {
        $requestedScreenNameRaw = $_GET['screenName'] ?? null;
        $requestedScreenName = is_scalar($requestedScreenNameRaw) ? trim((string) $requestedScreenNameRaw) : '';
        if ($requestedScreenName !== '' && User::isMember($requestedScreenName)) {
            // make sure the requested screenName is a member of the app otherwise we'll create xn_anonymous User objects (BAZ-8401) [ywh 2008-07-22]
            $this->user = Photo_UserHelper::load($requestedScreenName);
        } else {
            XG_SecurityHelper::redirectIfNotMember();
            $this->user = Photo_UserHelper::load($this->_user);
        }
        if (! $this->user) {
            // TODO: Throw exception instead - simpler, consistent with other mozzles [Jon Aquino 2008-02-19]
            $this->error = array('title' => xg_text('SLOW_DOWN_THERE_CHIEF'), 'subtitle' => '', 'description' => xg_text('I_DO_NOT_KNOW_USER'));
            return $this->render('error', 'index');
        }
        $this->myOwnAlbums = $this->user->title == $this->_user->screenName;
        $this->pageTitle = $this->myOwnAlbums ?
                                xg_text('MY_ALBUMS') :
                                xg_text('USER_ALBUMS', Photo_FullNameHelper::fullName($this->user->title));
        $this->handleSortingAndPagination(array('owner' => $this->user->title, 'includeHidden' => $this->myOwnAlbums), $this->getSortDescriptor());
    }

    /**
     * Shows all albums.
     */
    public function action_list() {
        $this->handleSortingAndPagination(null, $this->getSortDescriptor());
       	$this->title = xg_text('ALL_ALBUMS');
        $this->pageTitle = xg_text('ALBUMS_NO_COLON');
        if ($this->begin == 0) {
            $visibleFeaturedAlbumCount = 5;
            $albumData = Photo_AlbumHelper::getSortedAlbums(array('promoted' => true), null, 0, $visibleFeaturedAlbumCount);
            $this->featuredAlbums = $albumData['albums'];
            $this->featuredCoverPhotos = Photo_AlbumHelper::getCoverPhotos($this->featuredAlbums);
            $this->showViewAllFeaturedUrl = $albumData['numAlbums'] > $visibleFeaturedAlbumCount;
        }
    }

    /**
     * Displays all albums for the given search terms.
     */
    public function action_search() {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $searchTerms = $_GET['q'] ?? '';
        if (XG_QueryHelper::getSearchMethod() == 'search') {
            try {
                $this->pageSize = 20;
                $pageValue = $_GET['page'] ?? null;
                $pageNumber = is_numeric($pageValue) ? max(1, (int) $pageValue) : 1;
                $begin = XG_PaginationHelper::computeStart($pageNumber, $this->pageSize);
                $query = XN_Query::create('Search');
                $query->filter('type', 'like', 'Album');
                $query->begin($begin);
                $query->end($begin + $this->pageSize);
                $query->alwaysReturnTotalCount(true);
                XG_QueryHelper::addSearchFilter($query, $searchTerms, true);
                XG_QueryHelper::addExcludeFromPublicSearchFilter($query, true);
                $this->albums = XG_QueryHelper::contentFromSearchResults($query->execute(), false);
                $this->numAlbums = $query->getTotalCount();
                Photo_FullNameHelper::initialize($this->albums);
                foreach($this->albums as $album) {
                    $this->coverPhotos[] = $album->coverPhotoId;
                }
            } catch (Exception $e) {
                // DS said that the search core may throw an exception
                // while searchability is being added to an app without search. [Jon Aquino 2008-02-13]
                $filters = ($searchTerms !== '') ? array('searchTerms' => $searchTerms) : null;
                $this->handleSortingAndPagination($filters, $this->getSortDescriptor());
            }
        } else {
            $filters = ($searchTerms !== '') ? array('searchTerms' => $searchTerms) : null;
            $this->handleSortingAndPagination($filters, $this->getSortDescriptor());
        }
    }

    /**
     * Shows a list of albums that have been promoted.
     */
    public function action_listFeatured() {
        $this->handleSortingAndPagination(array('promoted' => true), null);
    }

    /**
     * AJAX POST action that increments the view count of a specific photo.
     */
    public function action_registershown() {
        try {
            $albumId = $_POST['id'] ?? null;
            if (!$albumId) {
                Photo_JsonHelper::outputAndExit(array());
            }

            $album = Photo_ContentHelper::findByID('Album', $albumId, true, false);
            if ($album && (!$this->_user->isLoggedIn() || ($this->_user->screenName != $album->contributorName))) {
                $album->my->viewCount = $album->my->viewCount + 1;
                // BAZ-1507: Don't invalidate cache here, or the cache gets blown away on each detail view
                XG_App::setInvalidateFromHooks(false);
                $album->save();
                XG_App::setInvalidateFromHooks(true);
            }
            Photo_JsonHelper::outputAndExit(array());
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    /**
     * Shows the albums that contain a specific photo.
     */
    public function action_listContaining() {
        if (isset($_GET['photoId'])) { $this->photo = Photo_PhotoHelper::load($_GET['photoId']); }
        if (! $this->photo) {
            // TODO: Throw exception instead - simpler, consistent with other mozzles [Jon Aquino 2008-02-19]
            $this->error = array('title' => xg_text('SLOW_DOWN_THERE_CHIEF'), 'subtitle' => '', 'description' => xg_text('I_DO_NOT_HAVE_PHOTO'));
            return $this->render('error', 'index');
        }
        $this->handleSortingAndPagination(array('photoId' => $this->photo->id), $this->getSortDescriptor());
    }

    private function handleSortingAndPagination($filters, $sort) {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->pageSize = 20;
        $pageValue = $_GET['page'] ?? null;
        $pageNumber = is_numeric($pageValue) ? max(1, (int) $pageValue) : 1;
        $this->begin = XG_PaginationHelper::computeStart($pageNumber, $this->pageSize);
        $albumData = Photo_AlbumHelper::getSortedAlbums($filters, $sort, $this->begin, $this->begin + $this->pageSize);
        $this->albums = $albumData['albums'];
        $this->numAlbums = $albumData['numAlbums'];
        $this->coverPhotos = Photo_AlbumHelper::getCoverPhotos($this->albums);
        $sorts = Photo_AlbumHelper::getKnownSortingOrders();
        $sortDescriptor = $sort ?? $sorts[Photo_AlbumHelper::SORT_ORDER_MOSTRECENT];
        $sortAlias = $sortDescriptor['alias'] ?? Photo_AlbumHelper::SORT_ORDER_MOSTRECENT;
        $this->sortOptions = Photo_HtmlHelper::toSortOptions($sorts, $sortAlias);
        $this->isSortRandom = $sortAlias == Photo_AlbumHelper::SORT_ORDER_RANDOM;
        Photo_FullNameHelper::initialize($this->albums);
    }

    /**
     * Shows a specific album.
     */
    public function action_show() {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        if (isset($_GET['id'])) {
            // Don't use a cached query here so that the view count will be up to date
            $this->album = Photo_AlbumHelper::load($_GET['id'], false);
        }
        if (!$this->album) {
            // TODO: Throw exception instead - simpler, consistent with other mozzles [Jon Aquino 2008-02-19]
            $this->error = array('title' => xg_text('SLOW_DOWN_THERE_CHIEF'), 'subtitle' => '', 'description' => xg_text('I_DO_NOT_HAVE_ALBUM'));
            return $this->render('error', 'index');
        }
        XG_CommentHelper::stopFollowingIfRequested($this->album);
        $this->isOwner = XG_SecurityHelper::userIsContributor($this->_user, $this->album);
        if ($this->isOwner) { $this->updateAlbum($this->album); }
        $rss = (($_GET['rss'] ?? '') === 'yes');
        if ($rss) {
            header("Content-Type: text/xml");
            $this->setCaching(array('photos-album-show-' . md5(XG_HttpHelper::currentUrl())), 1800);
            if (!empty($_GET['test_caching'])) { var_dump('Not cached'); }
        }
        $this->pageSize = 16;
        $pageValue = $_GET['page'] ?? null;
        $pageNumber = is_numeric($pageValue) ? max(1, (int) $pageValue) : 1;
        $begin = XG_PaginationHelper::computeStart($pageNumber, $this->pageSize);
        $this->coverPhoto = Photo_AlbumHelper::getCoverPhotos(array($this->album));
        $photoIds = Photo_ContentHelper::ids($this->album, 'photos');
        $photosData = Photo_PhotoHelper::getSpecificPhotos($this->isOwner ? null : $this->_user, $photoIds, null, $begin, $begin + $this->pageSize);
        $this->photos = $photosData['photos'];
        // For the non-owner, $album->my->photoCount might be wrong due to removed/no longer visible
        // photos; hence we store the real number of photos for use in the template
        $this->numPhotos = $photosData['numPhotos'];
        Photo_FullNameHelper::initialize(array_merge(array($this->album), $this->photos));
        Photo_ContentHelper::sortByAttribute($this->photos, $photoIds);
        if ($this->album->my->photoCount != count($this->photos)) {
            $this->album->my->photoCount = count($this->photos);
            $this->album->save();
        }
        $this->showFacebookMeta = array_key_exists('from', $_GET) && ($_GET['from'] === 'fb');
        if ($rss) {
            $appname = XN_Application::load()->name;
            $this->title = xg_xmlentities($this->album->title);
            $this->description = xg_xmlentities(xg_text('ALBUM_BY_X_ON_X', $this->album->contributorName, $appname));
            $this->link = $this->_buildUrl('album','show', '?id=' . $this->album->id);
            if ($this->album->my->coverPhotoId) {
                Photo_HtmlHelper::fitImageIntoThumb($this->coverPhoto[0], 50, 50, $imgUrl, $imgWidth, $imgHeight);
                $this->feedImageUrl = $imgUrl;
                $this->feedImageHeight = 50;
            }
            $this->useTags = 1;
            $this->render('rss','photo');
        }
    }

    /**
     * Retrieve just the HTML fragment for an album that shows what the cover is. This is retrieved via
     * AJAX in the photo detail page to update a cover fragment after the current image changes (for example,
     * it is rotated) if the current image is a displayed album cover.
     */
    public function action_showCoverFragment() {
        if (isset($_GET['id'])) {
            $this->album = Photo_AlbumHelper::load($_GET['id']);
        }
        if (! $this->album) {
           // @todo render some error via javascript
        }

        $coverPhotos = Photo_AlbumHelper::getCoverPhotos(array($this->album));
        if (count($coverPhotos) == 1) {
            $this->coverPhoto = $coverPhotos[0];
        } else {
            $this->coverPhoto = null;
        }
    }

    /**
     * Deletes an album. Note that this action should only be called by doing a POST via AJAX as it returns nothing.
     */
    public function action_delete() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        $albumId = $_GET['id'] ?? '';
        if ($albumId === '') { throw new Exception('Album id missing'); }
        $album = Photo_AlbumHelper::load($albumId);
        if (Photo_SecurityHelper::failed(Photo_SecurityHelper::checkCurrentUserCanDeleteAlbum($this->_user, $album))) { throw new Exception('Not allowed'); }
        Photo_AlbumHelper::delete($album);
        Photo_AlbumHelper::updateAlbumCount(User::load(XN_Profile::current()));
        $targetParam = isset($_GET['target']) ? trim((string) $_GET['target']) : '';
        $target = ($targetParam !== '') ? $targetParam : $this->_buildUrl('album', 'list');
        header('Location: ' . $target);
    }

    public function action_new() {
        XG_SecurityHelper::redirectIfNotMember();
    }

    public function action_edit() {
        XG_SecurityHelper::redirectIfNotMember();

        if (isset($_GET['id'])) {
            $this->album = Photo_AlbumHelper::load($_GET['id']);
        }
        if (!$this->album) {
            $this->error = array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                                 'subtitle'    => '',
                                 'description' => xg_text('I_DO_NOT_HAVE_ALBUM'));
            $this->render('error', 'index');
            return;
        }
        if ($this->error = Photo_SecurityHelper::checkCurrentUserCanEditAlbum($this->_user, $this->album)) {
            $this->render('error', 'index');
            return;
        }
        $this->updateAlbum($this->album);

        $photoIds   = Photo_ContentHelper::ids($this->album, 'photos');
        $photosData = Photo_PhotoHelper::getSpecificPhotos(null, $photoIds); // TODO: Should null be $this->_user? [Jon Aquino 2008-03-18]
        $this->albumPhotos = $photosData['photos'];
        Photo_ContentHelper::sortByAttribute($this->albumPhotos, $photoIds);
    }

    public function action_getAvailablePhotos() {
        try {
            if (!$this->_user->isLoggedIn()) {
                Photo_JsonHelper::outputAndExit(array());
            }

            $filters = array();

            if (($_GET['origin'] ?? '') === 'my') {
                $filters['contributor'] = $this->_user->screenName;
            }
            if (isset($_GET['tags'])) {
                $filters['tags'] = XN_Tag::parseTagString($_GET['tags']);
            }

            $pageValue = $_GET['page'] ?? null;
            $pageNumber = is_numeric($pageValue) ? max(1, (int) $pageValue) : 1;
            $begin = ($pageNumber - 1) * self::NUM_THUMBS_ALBUMEDITVIEW;

            $photosData = Photo_PhotoHelper::getSortedPhotos($this->_user,
                                                             $filters,
                                                             Photo_PhotoHelper::getMostRecentSortingOrder(),
                                                             $begin,
                                                             $begin + self::NUM_THUMBS_ALBUMEDITVIEW);

            if (($begin >= $photosData['numPhotos']) && ($photosData['numPhotos'] > 0)) {
                $begin      = ((int)($photosData['numPhotos'] - 1) / self::NUM_THUMBS_ALBUMEDITVIEW) * self::NUM_THUMBS_ALBUMEDITVIEW;
                $photosData = Photo_PhotoHelper::getSortedPhotos($this->_user,
                                                                 $filters,
                                                                 Photo_PhotoHelper::getMostRecentSortingOrder(),
                                                                 $begin,
                                                                 $begin + self::NUM_THUMBS_ALBUMEDITVIEW);
            }

            $page            = 1 + (int)($begin / self::NUM_THUMBS_ALBUMEDITVIEW);
            $numPages        = $photosData['numPhotos'] == 0 ? 1 : 1 + (int)(($photosData['numPhotos'] - 1) / self::NUM_THUMBS_ALBUMEDITVIEW);
            $photoUrlsById   = array();
            $photoTitlesById = array();

            foreach ($photosData['photos'] as $photo) {
                Photo_HtmlHelper::getImageUrlAndDimensions($photo, $imgUrl, $width, $height);
                // PHO-590: If the image (for some reason) was not properly saved, then the fileUrl will be
                //          empty which means that imgUrl will be null
                //          Since we don't want these to show up in the album page, we'll simply filter them here
                if ($imgUrl) {
                    $photoUrlsById[$photo->id]   = $imgUrl;
                    $photoTitlesById[$photo->id] = $photo->title;
                }
            }
            Photo_JsonHelper::outputAndExit(array('page'            => $page,
                                                  'numPages'        => $numPages,
                                                  'photoUrlsById'   => $photoUrlsById,
                                                  'photoTitlesById' => $photoTitlesById));
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    // AJAX POST action that saves the indicated album
    public function action_saveAlbum() {
        try {
            XG_SecurityHelper::redirectIfNotMember();
            XG_JoinPromptHelper::joinGroupOnSave();
            if (!$this->_user->isLoggedIn()) {
                header("HTTP/1.0 403 Forbidden");
                return;
            }
            $newAlbum = false;
            if (isset($_POST['albumId']) && (mb_strlen($_POST['albumId']) > 0)) {
                try {
                    $album = Photo_AlbumHelper::load($_POST['albumId']);
                } catch (Exception $e) {
                    $album = null;
                }
                if (($album == null) || Photo_SecurityHelper::failed(Photo_SecurityHelper::checkCurrentUserCanEditAlbum($this->_user, $album))) {
                    header("HTTP/1.0 403 Forbidden");
                    return;
                }
            } else {
                $newAlbum = true;
                $album = Photo_AlbumHelper::create();
            }
            $album->setTitle($_POST['title']);
            $album->setDescription($_POST['description']);
            $album->my->coverPhotoId = null;
            if (isset($_POST['photos']) && (mb_strlen(trim($_POST['photos'])) > 0)) {
                $photoIds  = explode(' ', $_POST['photos']);
                $photoData = Photo_PhotoHelper::getSpecificPhotos($this->_user, $photoIds, null, 0, 100, true);
                $photos    = $photoData['photos'];
                Photo_ContentHelper::sortByAttribute($photos, $photoIds);
                if (isset($_POST['coverPhotoId']) && (mb_strlen($_POST['coverPhotoId']) > 0)) {
                    $coverPhotoId = $_POST['coverPhotoId'];
                    foreach ($photos as $photo) {
                        if ($photo->id == $coverPhotoId) {
                            $album->my->coverPhotoId = $photo->id;
                            break;
                        }
                    }
                }
                Photo_AlbumHelper::setPhotos($album, $photos);
            } else {
                Photo_AlbumHelper::setPhotos($album, array());
            }
            if ($newAlbum) {
                Photo_AlbumHelper::logAlbumCreation($album);
            }
            Photo_JsonHelper::outputAndExit(array('target' => $this->_buildUrl('album', 'show', '?id=' . $album->id)));
        } catch (Exception $e) {
            header("HTTP/1.0 403 Forbidden");
        }
    }

    // AJAX POST action from editable text areas
    public function action_setTitle() {
        // TODO: Delete this action - does not seem to be called anywhere [Jon Aquino 2008-05-05]
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        $this->html = '';

        if (!$this->_user->isLoggedIn()) {
                header("HTTP/1.0 403 Forbidden");
                return;
        }
        if (isset($_POST['id']) && (mb_strlen($_POST['id']) > 0) && isset($_POST['value'])) {
            if (mb_strlen($_POST['value']) == 0) {
                $_POST['value'] = 'untitled';
            }
            try {
                $album = Photo_AlbumHelper::load($_POST['id']);
                if ($album && XG_SecurityHelper::userIsContributor($this->_user, $album)) {
                    $album->title = $_POST['value'];
                    $album->save();
                    Photo_JsonHelper::outputAndExit(array('html' => xnhtmlentities($album->title)));
                } else {
                    $album = null;
                }
            } catch (Exception $e) {
                $album = null;
            }
            if (! $album) {
                header("HTTP/1.0 403 Forbidden");
                return;
            }
        }
    }

    // AJAX POST action from editable text areas
    // udpated to allow empty descriptions; set json array as return var [Phil McCluskey 2006-09-24]
    public function action_setDescription() {
        // TODO: Delete this action - does not seem to be called anywhere [Jon Aquino 2008-05-05]
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        $this->html = '';

        if (!$this->_user->isLoggedIn()) {
                header("HTTP/1.0 403 Forbidden");
                return;
        }
        if (isset($_POST['id']) && (mb_strlen($_POST['id']) > 0) && isset($_POST['value'])) {
            try {
                $album = Photo_AlbumHelper::load($_POST['id']);
                if ($album && XG_SecurityHelper::userIsContributor($this->_user, $album)) {
                    $album->setDescription($_POST['value']);
                    $album->save();
                    Photo_JsonHelper::outputAndExit(array('html' => xg_nl2br(xg_resize_embeds($album->description, 171))));
                } else {
                    $album = null;
                }
            } catch (Exception $e) {
                $album = null;
            }
            if (! $album) {
                header("HTTP/1.0 403 Forbidden");
                return;
            }
        }
    }

    /**
     * Updates the given album and stores the number of removed photos in $this->removedPhotoCount
     * if photos have been removed.
     *
     * @param $album Album The album
     */
    private function updateAlbum($album) {
        // we update the album before showing it in order to get rid of references to photos
        // that have been deleted or that are no longer visible to the owner of the album
        $old = $album->export();
        Photo_AlbumHelper::updateAlbum($this->_user, $album);
        if (array_diff_assoc($old, $album->export())) {
            $album->save();
        }
        // TODO: Maybe eliminate removedPhotoCount [Jon Aquino 2008-02-20]
        if (isset($old['photoCount']) && $album->my->photoCount < $old['photoCount']) {
            $removed = $old['photoCount'] - $album->my->photoCount;
            if ($removed > 0) {
                $this->removedPhotoCount = $removed;
            }
        }
    }
}
