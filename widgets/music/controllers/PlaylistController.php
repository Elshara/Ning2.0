<?php

class Music_PlaylistController extends W_Controller {

    const CACHE_MAX_MINUTES     = 15;

    public function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Music_TrackHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_PlaylistHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_ContentHelper.php');
        XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
    }

    /**
     * Pokes a hole in the app-wide privacy mechanism.
     *
     * @param $action string  The name of the action
     * @return boolean  Whether to bypass the privacy mechanism for the given action
     */
    public function action_overridePrivacy($action) {
        return isset($_GET['fmt']) && in_array($action, array('show'));
    }

    //Add an existing track to an user playlist
    /** @deprecated  This action is no longer used */
    public function action_addTrack() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! isset($_POST['id'])) { throw new Exception("No track specified"); }
        if(!$_REQUEST['playlistId']) {
            //use default playlist for that user
            $playlistData = Music_PlaylistHelper::loadOrCreateDefaultUserPlaylist($this->_user);
            $playlist = $playlistData['playlist'];
        } else {
            $playlist = Playlist::load($_REQUEST['playlistId']);
            if ($this->error = XG_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $playlist)) {
                $this->render('error', 'index');
                return;
            }
        }
        $track = Track::load($_POST['id']);
        if($track->my->enableProfileUsage) {
            $duplicate = (mb_strpos($playlist->my->tracks, $track->id) !== false);
            if(!$duplicate) {
                $playlist->my->set('tracks', $track->id.','.$playlist->my->tracks , XN_Attribute::STRING);
                $playlist->my->set('trackCount', $playlist->my->trackCount + 1 , XN_Attribute::NUMBER);
                $playlist->save();
            }
        } else {
             throw new Exception("This track cannot be added to user pages, ask the owner to check the 'Allow people to put this song on their pages' checkbox");
        }
        if ($_GET['fmt'] == 'flashlv') {
            echo '&added=1&duplicate='.$duplicate;
            $this->render('blank');
        }

    }

    public function action_edit() {
        XG_SecurityHelper::redirectIfNotMember();
        $this->begin = isset($_GET['begin'])?$_GET['begin']:0;
        $this->end = isset($_GET['end'])?$_GET['end']:100;
        if(!$_REQUEST['id']) {
            //use default playlist for that user
            $playlistData = Music_PlaylistHelper::loadOrCreateDefaultUserPlaylist($this->_user, $this->begin, $this->end);
        } else {
            $playlist = Playlist::load($_REQUEST['id']);
            if ($this->error = XG_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $playlist)) {
                $this->render('error', 'index');
                return;
            }
            $playlistData = Music_TrackHelper::getSpecificTracks($this->_user, explode(',',$playlist->my->tracks));
        }
        $tracks = $playlistData['tracks'];
        $this->tracks = $playlistData['tracks'];
        if(!$playlist) $playlist = $playlistData['playlist'];
        //assure that tracks without the enableProfileUsage mark will not be playable (striked visual)
        $tracks = self::removeUnavailableTracks($tracks, $playlist->contributorName);

        $this->playlist = $playlist;
        $this->canReorder         = ( (XG_SecurityHelper::passed(XG_SecurityHelper::checkCurrentUserContributed($this->_user, $this->playlist)))
                                      || ($playlist->my->allowAdminEditing && XG_SecurityHelper::userIsAdmin($this->_user)) );
        $this->canEditTracks      = ( (XG_SecurityHelper::passed(XG_SecurityHelper::checkCurrentUserContributed($this->_user, $this->playlist)))
                                      || ($playlist->my->allowAdminEditing && XG_SecurityHelper::userIsAdmin($this->_user)) );
        $this->canAddTracks       = $this->canReorder;
        $this->trackCountChange = $_GET['trackCountChange'];
        $this->uploaded = $_GET['uploaded'];
        $this->limitReached = $_GET['limitReached'];;
        $this->playlistLimit = Music_PlaylistHelper::PLAYLIST_TRACK_LIMIT;
    }

    /**
     * Proxy an external playlist (to bypass Flash crossdomain.xml requirements)
     * external feeds are cached for 15 minutes
     **/
    public function action_showExternal(){
        $url = $_GET['url'];
        $id = 'playlist-' . md5($url);
        $data = XN_REST::get($url);
        // we want to limit the use of this proxy to rss, xspf and m3u urls only
        if (! Music_PlaylistHelper::looksLikeSupportedFormat($data, XN_REST::getLastResponseHeaders())) { throw new Exception("Content is not a playlist."); }
        if (! XG_Cache::outputCacheStart($id, 60 * self::CACHE_MAX_MINUTES)) {
            echo $data;
            XG_Cache::outputCacheEnd($id); 
        }
    }
    
    /*
     * Replace the track order of the playlist section from $begin to $end
     * with the given trackList (a comma separated track ids list)
    */
    public function action_reorder() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! isset($_REQUEST['id'])) { throw new Exception("No playlist specified"); }
        $playlist = Playlist::load($_REQUEST['id']);
        if ($this->error = XG_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $playlist)) {
            $this->render('error', 'index');
            return;
        }
        if  (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_REQUEST['trackList'])){
            $begin       = isset($_REQUEST['begin'])?$_REQUEST['begin']:0;
            $end         = isset($_REQUEST['end'])?$_REQUEST['end']:100;
            $trackIds    = explode(',',$playlist->my->tracks);
            $newTrackIds = explode(',',$_REQUEST['trackList']);
            $oldTrackIds = array_splice($trackIds, 0+$begin, ($end-$begin), explode(',',$_REQUEST['trackList']));
            $playlist->my->set('tracks', implode($trackIds,','));
            $playlist->save();
        }
        if ($playlist->id == $this->_widget->privateConfig['mainPlaylist']) {
            header('Location: ' . xg_absolute_url('/'));
        } else {
            header('Location: ' . W_Cache::getWidget('profiles')->buildUrl('profile', 'show','?id='.$playlist->contributorName));
        }
    }

    /* renders the music player 100% x 100% on a page, used by the detach button*/
    public function action_popup() { }

    public function action_show($playlistId = null, $playlistFormat = null) {
        $begin       = isset($_GET['begin'])?$_GET['begin']:0;
        $end         = isset($_GET['end'])?$_GET['end']:100;
        if(! is_null($playlistFormat))  $_GET['fmt']    = $playlistFormat;
        $playlistId = (! is_null($playlistId)) ? $playlistId : $_GET['id'];
        if (is_null($playlistId)) {
            if (!$this->_user->isLoggedIn()) {
                throw new Exception("No playlist specified");
            } else {
                $playlistData = Music_PlaylistHelper::loadOrCreateDefaultUserPlaylist($this->_user, $begin, $end);
                $playlist = $playlistData['playlist'];
                $tracks = $playlistData['tracks'];
            }
        } else {
            $playlist = Playlist::load($playlistId);
            $tracksData = Music_TrackHelper::getSpecificTracks($this->_user, explode(',',$playlist->my->tracks), null, $begin, $end);
            $tracks = $tracksData['tracks'];
        }

        //assure that tracks without the enableProfileUsage mark will only be visible on playlists by the track owner
        $tracks = self::removeUnavailableTracks($tracks, $playlist->contributorName);

        //assure that external/unlogged queries for the playlist dont get the special tracks (download link off or profileusage off)
        if (!$this->_user->isLoggedIn()) {
            if ((isset($_GET['fmt']))&&(! Music_SecurityHelper::canAccessEmbeddableData($_GET))) {
                throw new Exception();
            }
            //$tracks = Music_PlaylistHelper::removeRestrictedTracks($tracks);
        }

        $this->tracks = $tracks;
        $this->listDescription = $playlist->description;
        $this->listPage = $this->_buildUrl('playlist', 'show', array('id' => $this->playlist->id));
        $this->feedLink = $this->_buildUrl('playlist', 'show', array('id' => $this->playlist->id, 'xn_auth' => 'no', 'fmt' => 'xspf'));
        $this->listIdentifier = $this->playlist->id;
        $this->pubDate = $this->playlist->createdDate;
        $this->title = $playlist->title;
        $this->creator = $playlist->contributorName;
        //use the first track album image as playlist image in case the playlist doesnt have one
        $this->playlistImage = ($playlist->my->artworkUrl)?$playlist->my->artworkUrl:$tracks[0]->my->artworkUrl;

        try{
            $user = Music_UserHelper::load($this->_user);
        } catch (Exception $e) {}
        $this->user = $user;

        if ($_GET['fmt'] == 'm3u') {
            header('Content-Type: audio/x-mpegurl');
            $this->render('m3u');
        }
        if ($_GET['fmt'] == 'xspf') {
            header('Content-Type: application/xspf+xml');
            $this->render('xspf');
        }
        if ($_GET['fmt'] == 'internal') {
            $this->render('blank');
        }
        return $tracks;
    }

    /*
    */
    public function removeUnavailableTracks($tracks, $playlistOwner) {
        $result = array();
        foreach($tracks as $track) {
            if (($track->my->enableProfileUsage=='on')||($track->contributorName == $playlistOwner)) {
                $result[] = $track;
            }
        }
        return $result;
    }

    /**
     * Returns the embed code for the music player, and the embed code for its preview.
     *
     * Expected GET parameters:
     *     -
     */
    public function action_embeddableWithPreview() {
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        ob_start();
        $this->_widget->dispatch('playlist', 'embeddable', array(array_merge($_GET, array('includeFooterLink' => true))));
        $this->embedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
        ob_end_clean();
        ob_start();
        $this->_widget->dispatch('playlist', 'embeddable', array($_GET));
        $this->previewEmbedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
        ob_end_clean();
    }

    /**
     * Displays the Flash object for the player.
     *
     * Expected GET parameters:
     *     Any of the $args parameters can also be passed as GET parameters
     *
     * @param $args array  parameters:
     *     -
     */
    public function action_embeddable($args = array()) {
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
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        foreach ($args as $key => $value) { $this->{$key} = $value; }
        $flashVars = array(
                'autoplay'              => $this->autoplay=='true',
                'width'                 => $this->width,
                'showplaylist'          => $this->showPlaylist=='true',
                'playlist_url'          => $this->playlistUrl,
                'logo_link'             => $this->embedLogoLink,
                'display_add_links'     => 'off',
                'display_contributor'   => $this->displayContributor,
                'embed'                 => true,
                'bgcolor'               => $this->bgColor,
                'bgimage'               => $this->bgImage,
                'brand'                 => $this->brand,
                'logoImage'             => $this->logoImage,
                'logoImageWidth'        => $this->logoImageWidth,
                'logoImageHeight'       => $this->logoImageHeight,
                'networkNameCss' => $this->brand ? 'h1 { font-family: ' . XG_EmbeddableHelper::getNetworkNameFontFamily() . '; color: #ffffff; }' : null,
                'includeFooterLink' => $this->includeFooterLink,
                'noMusicMessage' => $this->noMusicMessage,
        );
        $this->flashVars = $flashVars;
    }

    /**
     * Removes the track from the specified playlist. Does not delete the Track object.
     *
     * Expected GET parameters:
     *     id - ID of the Playlist
     *     trackId - ID of the Track to remove from the playlist
     *     xn_out - "json"
     */
    public function action_removeTrack() {
        $playlist = Playlist::load($_GET['id']);
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        if (XG_SecurityHelper::failed(XG_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $playlist))) { throw new Exception('Not allowed (962167123)'); }
        Music_PlaylistHelper::removeTrackEntry($this->_user, $playlist, $_GET['trackId']);
    }

}

?>
