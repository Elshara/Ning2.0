<?php

class Music_TrackController extends W_Controller {
    /** The number of music tracks that can be uploaded at a time */
    const NUM_TRACKS_UPLOAD                 = 4;
    /** The number of music tracks that can be linked at a time */
    const NUM_TRACKS_LINK                   = 4;
    /** The number of music tracks displayed on the recent tracks feed/playlist */
    const NUM_TRACKS_RECENT_LIST            = 20;
    /** The number of music tracks displayed on the promoted tracks feed/playlist */
    const NUM_TRACKS_PROMOTED_LIST          = 20;
    /** The number of music tracks displayed on the highest rated tracks feed/playlist */
    const NUM_TRACKS_HIGHEST_RATED_LIST     = 20;

    const LIST_OPTION_NAME_RECENT           = 'recent';
    const LIST_OPTION_NAME_PROMOTED         = 'promoted';
    const LIST_OPTION_NAME_HIGHEST_RATED    = 'highestrated';

    /**
     * Pokes a hole in the app-wide privacy mechanism.
     *
     * @param $action string  The name of the action
     * @return boolean  Whether to bypass the privacy mechanism for the given action
     */
    public function action_overridePrivacy($action) {
        return isset($_GET['fmt']) && in_array($action, array('list','listPromoted','listFacebook'));
    }

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Music_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_TrackHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_PlaylistHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_ContentHelper.php');
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
    }

    public function action_index() {
    }

    public function action_new() {
        XG_SecurityHelper::redirectIfNotMember();
        if ($this->error = Music_SecurityHelper::checkCurrentUserCanAddMusic($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        XG_MediaUploaderHelper::setUsingMediaUploader(false);
        // we might have come here because the previous upload failed
        $this->hideBulkUploaderReferences = W_Cache::getWidget('main')->config['hideBulkUploader'] == 'yes';
        $this->sizeLimitError       = $_GET['sizeLimitError'];
        $this->failedFiles          = $_GET['failedFiles'];
        $this->addToMainPlaylist    = $_GET['isMainPlaylist'];
        $this->playlistId           = $_GET['playlistId'];
        if ($this->playlistId) {
            $playlist = Playlist::load($this->playlistId);
            if (Music_PlaylistHelper::limitReached($playlist)){
                $this->redirectTo('edit', 'playlist', array('id' => $playlist->id, 'limitReached'=>true ));
            }
        }
        $this->maxItems = Music_PlaylistHelper::getRemainingSize($playlist);
        $this->numTracks = min($this->maxItems, self::NUM_TRACKS_UPLOAD);
    }

    /**
     * Displays the applet-based uploader.
     */
    public function action_newWithUploader() {
        if (W_Cache::getWidget('main')->config['hideBulkUploader'] == 'yes') {
            $this->redirectTo('new', 'track',array('playlistId' => $_GET['playlistId'], 'isMainPlaylist' => $_GET['isMainPlaylist']));
        }
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        XG_SecurityHelper::redirectIfNotMember();
        if ($this->error = Music_SecurityHelper::checkCurrentUserCanAddMusic($this->_user)) { return $this->render('error', 'index'); }
        XG_MediaUploaderHelper::setUsingMediaUploader(true);
        $this->addToMainPlaylist = $_GET['isMainPlaylist'];
        $this->playlistId = $_GET['playlistId'];
        $playlist = Music_PlaylistHelper::loadOrCreatePlaylist($this->playlistId, $this->addToMainPlaylist, $this->getUploadHelpers());
        if (Music_PlaylistHelper::limitReached($playlist)){
            $this->redirectTo('edit', 'playlist', array('id' => $playlist->id,  'limitReached'=>true));
        }
        $this->maxItems = Music_PlaylistHelper::getRemainingSize($playlist);
        $this->musicUploaderArgs = array(
            'type' => 'music',
            'uploadUrl' => $this->_widget->buildUrl('track', 'createWithUploader', array('isMainPlaylist'=>$this->addToMainPlaylist, 'playlistId' => $this->playlistId)),
            'maxItems' => $this->maxItems,
            'successUrl' => $this->_buildUrl('playlist', 'edit', array('id' => $playlist->id, 'uploaded' => 1)));
    }

    /**
     * Redirects to the Media Uploader or the simple uploader, depending on the
     * capabilities of the browser. The current GET parameters will be preserved.
     */
    public function action_chooseUploader() {
        W_Cache::getWidget('main')->dispatch('mediauploader', 'chooseUploader');
    }

    public function action_newLink() {
        XG_SecurityHelper::redirectIfNotMember();
        if ($this->error = Music_SecurityHelper::checkCurrentUserCanAddMusic($this->_user)) {
            $this->render('error', 'index');
            return;
        }
        $this->addToMainPlaylist = $_GET['isMainPlaylist'];
        $this->playlistId = $_GET['playlistId'];
        $this->numTracks = self::NUM_TRACKS_LINK;
        if ($this->playlistId) {
            $playlist = Playlist::load($this->playlistId);
            if (Music_PlaylistHelper::limitReached($playlist)){
                $this->redirectTo('edit', 'playlist', array('id' => $playlist->id, 'limitReached'=>true));
            }
        }
    }

	// handler for the "quick post" feature
	public function action_createMultipleQuick() { # void
		$trackIds = $this->action_createMultiple(true);
		$this->render('blank');
		if (count($trackIds)) {
			$this->status = 'ok';
			if (count($trackIds) == 1) {
				$this->viewText = xg_html('VIEW_THIS_MUSIC');
				$this->message = xg_html('YOUR_MUSIC_WAS_UPLOADED');
			} else {
				$this->viewText = xg_html('VIEW_THESE_MUSIC_TRACKS');
				$this->message = xg_html('YOUR_MUSIC_TRACKS_WERE');
			}
			$this->viewUrl = $this->_buildUrl('playlist', 'edit', array('id' => $this->_playlist->id));
		} else {
			$this->status = 'fail';
			$this->message = xg_html('ERROR_ADDING_MUSIC_TRACKS');
		}
		unset($this->_playlist);
    }

    /**
     * Processes the form for adding music tracks.
     *
     * @param $dispatched boolean  whether this action is being called from within another page, using dispatch()
     * @return array  the track IDs if $dispatched is true; otherwise, null
     */
    public function action_createMultiple($dispatched = false) {
		// Used from action_createQuick()
        return $this->createMultiple($dispatched, $this->getUploadHelpers());
    }

    /**
     * Processes the form for adding music tracks.
     *
     * @param $dispatched boolean  whether this action is being called from within another page, using dispatch()
     * @param $helpers array
     *         musicSecurityHelper - Music_SecurityHelper or mock object;
     *         musicUserHelper - Music_UserHelper or mock object;
     *         musicPlaylistHelper - Music_PlaylistHelper or mock object;
     *         xgActivityHelper - XG_ActivityHelper or mock object;
     *         playlistModel - Playlist or mock object;
     *         audioAttachmentModel - AudioAttachment or mock object;
     *         trackModel - Track or mock object;
     * @return array  the track IDs if $dispatched is true; otherwise, null
     */
    protected function createMultiple($dispatched, $helpers) {
        foreach ($helpers as $key => $value) { ${$key} = $value; }
        if ($this->error = $musicSecurityHelper->checkCurrentUserCanAddMusic($this->_user)) { return $this->render('error', 'index'); }
        // TODO: For now we determine whether the file size limit was exceeded, via a hidden input (see PHO-543)
        if (!isset($_POST['uploadMarker']) && !isset($_POST["linkMode"])) { return $this->redirectTo('new', 'track', array('sizeLimitError' => xg_text('UPLOAD_LIMIT_EXCEEDED'))); }
        $tracks = array();
        $files = array();
        $failedFiles = array();
 		if ($_POST['featureOnMain']) {
			XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
			$featureOnMain = 1;
		} else {
			$featureOnMain = 0;
		}
        foreach ($_POST as $name => $value) {
            if (preg_match('@^track_(\d+)$@u', $name, $matches)) {
                $i = $matches[1];
                if(!$_POST["linkMode"]) {
                    //upload mode
                    if (! mb_strlen($_POST[$name])) { continue; }
                    $track = Music_TrackHelper::upload($name, $helpers);
                    if ($track) { $tracks[$i] = $track; }
                    else { $failedFiles[] = $_POST[$name]; }
                } else {
                    //link mode
                    if($_POST[$name]) {
                        $filename = preg_replace('@(.*)\/(.*)$@u', '$2', urldecode($_POST[$name]));
                        $trackTitle = preg_replace('/\.(mp3|ogg|wav)$/ui', '', $filename);
                        $trackTitle = str_replace('_', ' ', $trackTitle);
                        $trackHostUrlGuess = preg_replace('@((.*)(:\/\/)[^\/]*)(.*)@u', '$1', urldecode($_POST[$name]));
                        $track = $trackModel->create();
                        $track->my->audioUrl = $_POST[$name];
                        $track->my->approved = 'Y';
                        $track->my->trackTitle = $trackTitle;
                        $track->my->filename = $filename;
                        $track->my->trackHostUrl = $trackHostUrlGuess;
                        $tracks[$i] = $track;
                    }
                }
				if ($tracks[$i] && $featureOnMain && XG_PromotionHelper::currentUserCanPromote($tracks[$i])) {
					XG_PromotionHelper::promote($tracks[$i]);
				}
            }
        }
        $trackIds = array();
        if (count($tracks) > 0) {
            // we want the tracks to be created in the upload order (which we encoded in the form)
            // rather than the order that the browser choose to put them into the request
            ksort($tracks, SORT_NUMERIC);
            foreach ($tracks as $i => $track) {
                $track->save();
                $trackIds[$i] = $track->id;
            }
            $this->_playlist = Music_PlaylistHelper::addTracks($tracks, $_REQUEST['playlistId'], $_REQUEST['isMainPlaylist'] == 'yes', $helpers);
        }
        if ($dispatched) {
            return $trackIds;
        } elseif (count($tracks) > 0) {
            $this->redirectTo('editMultiple', 'track', array('ids' => implode(',', $trackIds), 'failedFiles' => implode(',', $failedFiles), 'playlistId' => $this->_playlist->id, 'trackCountChange' => count($tracks)));
        } else {
            $this->redirectTo('new', 'track', array('failedFiles' => implode(',', $failedFiles)));
        }
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
            if ($_POST['content:status']) { XG_MediaUploaderHelper::exitWithError('media-uploader:1', XG_FileHelper::uploadErrorMessage($_POST['content:status'])); }
            if (! Music_TrackHelper::isMimeTypeSupported($_POST['content:type'])) { XG_MediaUploaderHelper::exitWithError('media-uploader:5'); }
            $track = Music_TrackHelper::upload('content', $this->getUploadHelpers());
            if (! $track) { XG_MediaUploaderHelper::exitWithError('media-uploader:6'); }
            $args = $_POST;
            if (! $args['title']) { $args['title'] = $track->my->trackTitle; }
            $args['enableDownloadLink'] = $args['enableDownloadLink'] == 'true' ? 'on' : null;
            $args['enableProfileUsage'] = $args['enableProfileUsage'] == 'true' ? 'on' : null;
            $this->updateWithPostValues($track, $args);
            $track->save();
            Music_PlaylistHelper::addTracks(array($track), $_REQUEST['playlistId'], $_REQUEST['isMainPlaylist'] == 'yes', $this->getUploadHelpers());
            XG_MediaUploaderHelper::exitWithSuccess($approvalPending = false);
        } catch (Exception $e) {
            XG_MediaUploaderHelper::exitWithError('media-uploader:6', $e->getMessage());
        }
    }

    public function action_delete() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! isset($_REQUEST['id'])) { throw new Exception("No track specified"); }

        $track = Track::load($_REQUEST['id']);
        if ($this->error = XG_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $track)) {
            $this->render('error', 'index');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($_REQUEST['playlistId']) {
                $playlist = Playlist::load($_REQUEST['playlistId']);
                if ($this->error = XG_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $playlist)) {
                    $this->render('error', 'index');
                    return;
                }
                Music_PlaylistHelper::removeTrackEntry($this->_user, $playlist, $_REQUEST['id']);
                Music_TrackHelper::delete($track);
                $this->redirectTo('edit', 'playlist','?id='.$_REQUEST['playlistId']);
            } else {
                Music_TrackHelper::delete($track);
                $this->redirectTo('edit', 'playlist');
            }
        } else {
            $this->track = $track;
        }
    }


    public function action_edit() { $this->editMultiple(); }
    public function action_editMultiple() { $this->editMultiple(); }

    private function editMultiple() {
        XG_SecurityHelper::redirectIfNotMember();

        $ids = array();
        if ($_GET['ids']) {
            $ids = explode(',', $_GET['ids']);
        } elseif ($_GET['id']) {
            $ids[] = $_GET['id'];
        }
        if (count($ids) == 0) {
            // No ids given
            $this->redirectTo('edit', 'playlist');
            return;
        }
        $this->failedFiles  = $_GET['failedFiles'];
        $tracksData   = Music_TrackHelper::getSpecificTracks($this->_user, $ids, null, 0, 100, true);
        $this->tracks = $tracksData['tracks'];
        $this->availableLicenses = Music_TrackHelper::getKnownLicenses();
        $this->playlistId = $_GET['playlistId'];

        Music_ContentHelper::sortByAttribute($this->tracks, $ids);

        foreach ($this->tracks as $idx => $track) {
            if ($this->error = XG_SecurityHelper::checkCurrentUserContributed($this->_user, $track)) {
                $this->render('error', 'index');
                return;
            }
        }
        $this->trackCountChange = $_GET['trackCountChange'];
        $this->render('editMultiple');
    }

    /**
     * Updates multiple tracks.
     */
    public function action_updateMultiple() {
        XG_SecurityHelper::redirectIfNotMember();
        // we first gather the values
        $trackData = array();
        $trackIds  = array();
        foreach ($_POST as $var => $value) {
            if (preg_match('@^track(\d+)-(.*)$@u', $var, $matches)) {
                $num       = $matches[1];
                $attrName  = $matches[2];
                if (!$trackData[$num]) {
                    $trackData[$num] = array();
                }
                if ($attrName == 'id') {
                    $trackIds[] = $value;
                }
                $trackData[$num][$attrName] = $value;
            }
        }

        $tracks = Music_TrackHelper::getSpecificTracks($this->_user, $trackIds, null, 0, 100, true);

        foreach ($tracks['tracks'] as $track) {
            foreach ($trackData as $num => $curTrackData) {
                if ($curTrackData['id'] == $track->id) {
                    $this->updateWithPostValuesAndSave(W_Content::create($track), $curTrackData, $num);
                }
            }
        }
//        self::invalidateRssFeeds($this, $this->_user->screenName);
//        self::invalidateSlideshowFeeds($this,$this->_user->screenName);
        $this->redirectTo('edit', 'playlist',array('id'=>$_REQUEST['playlistId'], 'trackCountChange' => $_GET['trackCountChange']));
    }

    //Add an existing track to the featured tracks playlist
    public function action_feature() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (! isset($_POST['id'])) { throw new Exception("No track specified"); }
        $track = Track::load($_POST['id']);
        if(XG_PromotionHelper::currentUserCanPromote($track)) {
            XG_PromotionHelper::promote($track);
            XG_PromotionHelper::addActivityLogItem(XG_ActivityHelper::SUBCATEGORY_MUSIC, $track);
            $track->save();
            if ($_POST['fmt'] == 'flashlv') {
                echo '&featured=1';
                $this->render('blank');
            }
        } else {
             if ($_POST['fmt'] == 'flashlv') {
                 echo '&error=1';
                 $this->render('blank');
             } else
                 throw new Exception("This track cannot be featured by this user (8830421824865777)");
        }
    }

    //Remove an existing track of the featured tracks playlist
    public function action_unfeature() {
        // TODO: This action duplicates code from action_feature(). We should extract the
        // common code into a function [Jon Aquino 2008-01-23]
        XG_SecurityHelper::redirectIfNotMember();
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (! isset($_POST['id'])) { throw new Exception("No track specified"); }
        $track = Track::load($_POST['id']);
        if(XG_PromotionHelper::currentUserCanPromote($track)) {
            XG_PromotionHelper::remove($track);
            $track->save();
            if ($_POST['fmt'] == 'flashlv') {
                echo '&featured=0';
                $this->render('blank');
            }
        } else {
             if ($_POST['fmt'] == 'flashlv') {
                 echo '&error=1';
                 $this->render('blank');
             } else
                 throw new Exception("This track cannot be featured by this user (6217032349164168)");
        }
    }

    /**
     * wrapper function for facebook apps that considers the app display
     * type when returning feeds.
     */
    public function action_listFacebook() {
        XG_App::includeFileOnce('/lib/XG_FacebookHelper.php');
        $dispType = XG_FacebookHelper::getFacebookDisplayType('music');
        if ($dispType === 'promoted') {
            self::action_listPromoted();
        } else if ($dispType === 'rated') {
            self::action_listHighestRated();
        } else {
            self::action_list();
        }
    }

    public function action_list()               { return self::getTrackList(self::LIST_OPTION_NAME_RECENT); }
    public function action_listPromoted()       { return self::getTrackList(self::LIST_OPTION_NAME_PROMOTED); }
    public function action_listHighestRated()   { return self::getTrackList(self::LIST_OPTION_NAME_HIGHEST_RATED); }

    private function getTrackList($list = self::LIST_OPTION_NAME_RECENT) {
        switch ($list) {
            case self::LIST_OPTION_NAME_RECENT :
                $tracksData = Music_TrackHelper::getRecentTracks(0, self::NUM_TRACKS_RECENT_LIST);
                break;
            case self::LIST_OPTION_NAME_PROMOTED:
                $tracksData = Music_TrackHelper::getPromotedTracks(self::NUM_TRACKS_PROMOTED_LIST);
                break;
            case self::LIST_OPTION_NAME_HIGHEST_RATED:
                $tracksData = Music_TrackHelper::getHighestRatedTracks(self::NUM_TRACKS_HIGHEST_RATED_LIST);
                break;
        }
        $tracks = $tracksData['tracks'];
        //assure that external/unlogged queries for the playlist dont get the special tracks (profileusage off)
        if (!$this->_user->isLoggedIn()) {
            if ((isset($_GET['fmt']))&&(! Music_SecurityHelper::canAccessEmbeddableData($_GET))) {
                throw new Exception();
            }
            //$tracks = Music_PlaylistHelper::removeRestrictedTracks($tracks);
        }

        $this->tracks = $tracks;
        $this->feedLink = $this->_buildUrl('track', 'list', array('xn_auth' => 'no', 'fmt' => 'xspf') );

        //use the first track album image as playlist image in case the playlist doesnt have one
        $this->playlistImage = $tracks[0]->my->artworkUrl;

        $this->pubDate = $tracks[0]->createdDate;

        if ($_REQUEST['fmt'] == 'm3u') {
            if ($_GET['internalView'] != 'true') {
            header('Content-Type: audio/x-mpegurl');
            }
          $this->render('m3u', 'playlist');
        }
        if ($_REQUEST['fmt'] == 'xspf') {
            if ($_GET['internalView'] != 'true') {
            header('Content-Type: application/xspf+xml');
            }
          $this->render('xspf', 'playlist');
        }

        return $tracks;
    }

    /**
     * Updates the track object with values from the argument array, and saves it then.
     *
     * @param $track The track object, expected to be a Track object (not XN_Content)
     * @param $args  The argument array (e.g. $_POST): title, artist, album, enableDownloadLink,
     *         enableProfileUsage, artwork_action, artwork, genre, year, label, explicit, artistUrl,
     *         trackHostUrl, labelUrl, licenseUrl, otherlicenseName, otherlicenseUrl
     */
    private function updateWithPostValuesAndSave($track, $args, $num = null) {
        if ($this->error = XG_SecurityHelper::checkCurrentUserContributed($this->_user, $track)) {
            $this->render('error', 'index');
            return;
        }
        self::updateWithPostValues($track, $args, "track$num-artwork");
        $track->save();
    }

    /**
     * Updates the track object with values from the argument array, and saves it then.
     *
     * @param $track The track object, expected to be a Track object (not XN_Content)
     * @param $args  The argument array (e.g. $_POST): title, artist, album, enableDownloadLink,
     *         enableProfileUsage, artwork_action, artwork, genre, year, label, explicit, artistUrl,
     *         trackHostUrl, labelUrl, licenseUrl, otherlicenseName, otherlicenseUrl
     * @param $artworkVariableName string  name of the file field containing the uploaded artwork
     */
    private function updateWithPostValues($track, $args, $artworkVariableName = null) {
        if($args['title']) $track->my->trackTitle = $args['title'];
        if($args['artist']) $track->my->artist = $args['artist'];
        $track->title = ($args['artist']?$args['artist']:'').(($args['artist']&&$args['title'])?' - ':'').($args['title']?$args['title']:'');
        if($args['album']) $track->my->album = $args['album'];
        $track->my->enableDownloadLink = $args['enableDownloadLink'];
        $track->my->enableProfileUsage = $args['enableProfileUsage'];
        $track->my->infoUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/xn/detail/' . $track->id;

        //if the owner wants to hide the "Add to my page",
        //treat the track as private content
        if(is_null($args['enableProfileUsage']) ) {
            $track->isPrivate = true;
        } else {
            $track->isPrivate = XG_App::contentIsPrivate();
        }

        if($args['artwork_action'] == 'remove'){
            if($track->my->artworkAtachment) {
                try{
                    XN_Content::delete(XG_Cache::content($track->my->artworkAtachment));
                } catch (Exception $e) {
                    error_log('error removing artwork (9069659071564521): '.$e->getMessage());
                }
                $track->my->artworkAtachment = null;
                $track->my->artworkUrl = null;
            }
        }elseif($args['artwork']) {
            if($track->my->artworkAtachment) {
                //remove the old one
                XN_Content::delete(XG_Cache::content($track->my->artworkAtachment));
            }
            $artworkAtachment = ImageAttachment::create($track,$artworkVariableName);
            $artworkAtachment->save();
            $track->my->artworkAtachment = $artworkAtachment->id;
            $track->my->artworkUrl = $artworkAtachment->fileUrl('data');
        }

        if($args['genre'])          $track->my->genre           = $args['genre'];
        if($args['year'])           $track->my->year            = $args['year'];
        if($args['label'])          $track->my->label           = $args['label'];
        if($args['explicit'])       $track->my->explicit        = ($args['explicit']=='on')?'yes':'no';
        if($args['artistUrl'])      $track->my->artistUrl       = $args['artistUrl'];
        if($args['trackHostUrl'])   $track->my->trackHostUrl    = $args['trackHostUrl'];
        if($args['labelUrl'])       $track->my->labelUrl        = $args['labelUrl'];
        if($args['trackHostUrl'])   $track->my->trackHostUrl    = $args['trackHostUrl'];
        if($args['licenseUrl']) {
            if(!$args['otherlicenseName'] && !$args['otherlicenseUrl']) {
                $track->my->licenseUrl  = $args['licenseUrl'];
                $this->availableLicenses = Music_TrackHelper::getKnownLicenses();
                $track->my->licenseName  = $this->availableLicenses[$args['licenseUrl']];
            } else {
                $track->my->licenseName = $args['otherlicenseName'];
                $track->my->licenseUrl  = $args['otherlicenseUrl'];
            }
        }
    }

    /**
     * Returns helper objects used by the upload actions.
     *
     * @return array  helper objects
     */
    private function getUploadHelpers() {
        return array(
                'musicSecurityHelper' => new Music_SecurityHelper(),
                'musicUserHelper' => new Music_UserHelper(),
                'musicPlaylistHelper' => new Music_PlaylistHelper(),
                'xgActivityHelper' => new XG_ActivityHelper(),
                'playlistModel' => new Playlist(),
                'audioAttachmentModel' => new AudioAttachment(),
                'trackModel' => new Track());
    }

}
