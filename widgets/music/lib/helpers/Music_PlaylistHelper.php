<?php

/**
 * Common code for saving and querying playlists
 */
class Music_PlaylistHelper {
    /** The maximum number of music tracks per playlist */
    const PLAYLIST_TRACK_LIMIT = 100;

    /**
     * Loads the user default playlist(library) object from the content store.
     *  If there is no default playlist yet for the specified user, a new one will be created and stored.
     */
    public static function loadOrCreateDefaultUserPlaylist($profileOrScreenName, $begin = 0, $end = 100) {
        W_Cache::getWidget('music')->includeFileOnce('/lib/helpers/Music_TrackHelper.php');
        $user = User::load($profileOrScreenName);
        if ($user && !self::get($user, 'defaultPlaylist')) {
            $playlist = Playlist::create();
            $tracks = array();
            $tracksData = array('numTracks'=>0);
            $playlist->my->set('tracks', '', XN_Attribute::STRING);
            $playlist->my->set('trackCount', 0,  XN_Attribute::NUMBER);
            $playlist->save();
            self::set($user, 'defaultPlaylist', $playlist->id, XN_Attribute::STRING);
            self::set($user, 'playlistCount', 1, XN_Attribute::NUMBER);
            $user->save();
        } elseif ($user && self::get($user, 'defaultPlaylist') ) {
            $playlist = Playlist::load(self::get($user, 'defaultPlaylist'));
            $tracksData = Music_TrackHelper::getSpecificTracks($profileOrScreenName, explode(',',$playlist->my->tracks), null,$begin, $end);
            $tracks = $tracksData['tracks'];
        } else {
            throw new Exception('Error loading user object');
        }
        return array('playlist'=>$playlist,'tracks'=>$tracks, 'numTracks' => $tracksData['numTracks']);
    }

    public static function loadOrCreateDefaultNetworkPlaylist($widget = null, $begin = 0, $end = 100) {
        if(!$widget) {
            $widget = W_Cache::getWidget('music');
        }
        if(!$widget->privateConfig['mainPlaylist']) {
            $playlist = Playlist::create();
            $tracks = array();
            $tracksData = array('numTracks'=>0);
            $playlist->my->set('tracks', '', XN_Attribute::STRING);
            $playlist->my->set('trackCount', 0,  XN_Attribute::NUMBER);
            $playlist->my->allowAdminEditing = true;
            $playlist->save();
            $widget->privateConfig['mainPlaylist'] = $playlist->id;
            $widget->saveConfig();
        } else {
            $playlist = Playlist::load($widget->privateConfig['mainPlaylist']);
            $tracksData = Music_TrackHelper::getSpecificTracks(null , explode(',',$playlist->my->tracks), null,$begin, $end);
            $tracks = $tracksData['tracks'];
        }
        return array('playlist'=>$playlist,'tracks'=>$tracks, 'numTracks' => $tracksData['numTracks']);
    }

    public static function limitReached($playlist){
        return ($playlist->my->trackCount >= self::PLAYLIST_TRACK_LIMIT);
    }

    /*returns the number of tracks that can be added before the playlist limit is reached*/
    public static function getRemainingSize($playlist){
        return (self::PLAYLIST_TRACK_LIMIT - $playlist->my->trackCount);
    }

    public function hostHasCrossdomainFile($url){
        $parsedUrl = parse_url($url);
        $xmlUrl = $parsedUrl['scheme'].'://'.$parsedUrl['host'].'/crossdomain.xml';
        try{
            $data = XN_REST::get($xmlUrl);
        } catch (Exception $e){} //no need to bring the error page, let flash handle that [Zuardi -- Feb 5, 2008]
        if (XN_REST::getLastResponseCode()==200){
            $return = (strstr($data, 'cross-domain-policy') != false);
        } else {
            $return = false;
        }
        return $return;
    }
    
    /**
    * Analyse a file content and return false in case the data is clearly not supported
    * Used by the external podcast proxy in order to prevent it to be used as a general purpose proxy
    **/
    public static function looksLikeSupportedFormat($data, $headers){
        //@TODO implement a better check
        
        //mime type check
        if ($headers){
            //whitelist of the most common mime types we should care about
            $supportedContentTypes = array(
                                            '@text/xml@ui',
                                            '@application/rss+xml@ui',
                                            '@text/html@ui', //yes, people publish rss with this mime type :(
                                            '@application/xspf+xml@ui',
                                            '@audio/x-mpegurl@ui',
                                            '@audio/mpeg-url@ui'
                                                    );
            $responseType = $headers['Content-Type'];
            if (preg_replace($supportedContentTypes, '', $responseType) == $responseType) return false;
        }
        
        //don't proxy html documents
        if (preg_match('@</.*html>@ui', $data, $matches)) return false;
        
        //the content don't fit any of the exclusion cases, so it might be a supported playlist
        return true;
    }
    public static function get($user, $name) {
        return $user->my->raw(self::attributeName($name));
    }

    public static function set($user, $name, $value, $type = XN_Attribute::STRING) {
        $user->my->set(self::attributeName($name), $value, $type);
    }

    public static function attributeName($attributeName) {
        if (in_array($attributeName, array('defaultVisibility', 'addCommentPermission', 'emailActivityPref', 'emailModeratedPref'))) {
            // These attributes are shared with the Video and Blog widgets [Jon Aquino 2006-12-01]
            return $attributeName;
        }
        return XG_App::widgetAttributeName(W_Cache::getWidget('music'), $attributeName);
    }

    public static function removeTrackEntry($user, $playlist, $trackId) {
        $tracksData = Music_TrackHelper::getSpecificTracks($user, explode(',',$playlist->my->tracks));
        $tracks = $tracksData['tracks'];
        $newtracks = array();
        foreach($tracks as $track ) {
            if($track->id != $trackId) {
                $newtracks[] = $track->id;
            }
        }
        $playlist->my->set('tracks', implode($newtracks,','), XN_Attribute::STRING);
        $playlist->my->trackCount = count($newtracks);
        $playlist->save();
    }
    
    /**
     *  Remove all Playlist-related content objects (except for home playlist) for the specified user
     *
     *  @param $userObj User object for the user in question
     *  @return array number of objects removed, number of objects remaining
     */
    public static function removeByUser($userObj) {
        $removedCount = 0;
        $remainingCount = 0;

        $query = XN_Query::create('Content')
                ->filter('owner')
                ->filter('contributorName', '=', $userObj->contributorName)
                ->filter('type', '=', 'Playlist')
                ->alwaysReturnTotalCount(true);
                
        // BAZ-3207 [Charles 2008-08-14] deleting the homepage playlist causes an error (reference to null)  
        // for the music player. Jon A says its ok for the home playlist to be owned by a banned user
        $homePlaylistId = W_Cache::getWidget('music')->privateConfig['mainPlaylist'];
        if ($homePlaylistId) {
            $query->filter('id', '<>', $homePlaylistId);
        }
                
        $oldCount = $query->execute();
        if (count($oldCount) > 0) {
            $removedCount += count($oldCount);
            XN_Content::delete($oldCount);
        }
        if ($query->getTotalCount() > count($oldCount)) {
            $remainingCount += ($query->getTotalCount() - count($oldCount));
        }

        self::set($userObj, 'defaultPlaylist', '');

        if ($removedCount > 0) {
            $userObj->save();
        }
        
        return array($removedCount, $remainingCount);
    }
    
    public static function updateTrackCount($user, $playlist) {
        $tracksData = Music_TrackHelper::getSpecificTracks($user, explode(',',$playlist->my->tracks));
        $trackIds = array();
        foreach($tracksData['tracks'] as $track){
            $trackIds[] = $track->id;
        }
        $playlist->my->set('tracks', implode($trackIds,','), XN_Attribute::STRING);
        $playlist->my->trackCount = count($trackIds);
        $playlist->save();
    }

    /** @deprecated  This function is no longer used */
    public static function removeRestrictedTracks($tracks) {
        $result = array();
        foreach($tracks as $track) {
            if ($track->my->enableProfileUsage == 'on') {
                $result[] = $track;
            }
        }
        return $result;
    }

    /**
     * Returns the playlist URL for the playlist with the specified ID
     *
     * @param $id string The ID of a specific playlist, or 'most_recent' or
     *     'highest_rated' for these virtual playlists
     */
    public static function getUrl($id) {
        $widget = W_Cache::getWidget('music');
        switch ($id) {
            case 'most_recent':
                $url = $widget->buildUrl('track', 'list');
                break;
            case 'featured':
                $url = $widget->buildUrl('track', 'listPromoted');
                break;
            case 'highest_rated':
                $url = $widget->buildUrl('track', 'listHighestRated');
                break;
            default:
                $url = W_Cache::getWidget('music')->buildUrl('playlist', 'show', array(
                        'id' => $id));
        }
        $url = XG_HttpHelper::addParameter($url, 'fmt', 'xspf');
        return $url;
    }

    /**
     * Adds the tracks to the playlist
     *
     * @param $tracks array  the XN_Content or W_Content Track objects
     * @param $playlistId string  the ID of the playlist to update
     * @param $isMainPlaylist boolean  (if $playlistId is null) whether to update the network playlist or the user playlist
     * @param $helpers array
     *         musicUserHelper - Music_UserHelper or mock object;
     *         musicPlaylistHelper - Music_PlaylistHelper or mock object;
     *         xgActivityHelper - XG_ActivityHelper or mock object;
     *         playlistModel - Playlist or mock object;
     * @return $playlist XN_Content|W_Content  the updated Playlist
     */
    public static function addTracks($tracks, $playlistId, $isMainPlaylist, $helpers) {
        foreach ($helpers as $key => $value) { ${$key} = $value; }
        $playlist = self::loadOrCreatePlaylist($playlistId, $isMainPlaylist, $helpers);
        $playlist->my->trackCount += count($tracks);
        $trackIds = array();
        foreach ($tracks as $track) { $trackIds[] = $track->id; }
        $playlist->my->set('tracks', implode($trackIds, ',') . ',' . $playlist->my->tracks, XN_Attribute::STRING);
        $playlist->save();
        $user = $musicUserHelper->load(XN_Profile::current());
        $musicUserHelper->addTracks($user, count($tracks));
        $user->save();
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        $subcategory = $isMainPlaylist ? XG_ActivityHelper::SUBCATEGORY_HOME_TRACK : XG_ActivityHelper::SUBCATEGORY_TRACK;
        $xgActivityHelper->logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, $subcategory, $user->contributorName, $tracks);
        return $playlist;
    }

    /**
     * Loads the specified playlist, or creates it if necessary.
     *
     * @param $playlistId string  the ID of the playlist to update
     * @param $isMainPlaylist boolean  (if $playlistId is null) whether to update the network playlist or the user playlist
     * @param $helpers array
     *         musicPlaylistHelper - Music_PlaylistHelper or mock object;
     *         playlistModel - Playlist or mock object;
     * @return $playlist XN_Content|W_Content  the updated Playlist
     */
    public static function loadOrCreatePlaylist($playlistId, $isMainPlaylist, $helpers) {
        foreach ($helpers as $key => $value) { ${$key} = $value; }
        if ($playlistId) {
            return $playlistModel->load($playlistId);
        } elseif ($isMainPlaylist) {
            $playlistData = $musicPlaylistHelper->loadOrCreateDefaultNetworkPlaylist(W_Cache::current('W_Widget'));
            return $playlistData['playlist'];
        } else {
            $playlistData = $musicPlaylistHelper->loadOrCreateDefaultUserPlaylist(XN_Profile::current());
            return $playlistData['playlist'];
        }
    }

}

