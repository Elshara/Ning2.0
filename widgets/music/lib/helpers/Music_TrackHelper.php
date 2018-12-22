<?php

/**
 * Common code for saving and querying Track objects
 */
class Music_TrackHelper {
    /**
     * Returns specific tracks.
     *
     * @param profile           The XN_Profile object of the user for whom the tracks are queried for
     *                          If null, then visibility/privacy won't be checked
     * @param ids               The array of ids of the tracks to return
     * @return An array 'tracks' => the tracks, 'numTracks' => the total number of tracks that match the query
     *      */
    public static function getSpecificTracks($profile, $ids, $sort = null, $begin = 0, $end = 100, $ignoreApproval = false) {
        if (count($ids) > 0) {
            $ids = array_slice($ids, 0, 100); //BAZ-7201
            $query = XN_Query::create('Content')
                             ->filter('type', '=', 'Track')
                             ->filter('id', 'in', $ids)
                             ->filter('owner');
            if ($profile) {
                XG_SecurityHelper::addVisibilityFilter($profile, $query);
                if(!$ignoreApproval) {
                    XG_SecurityHelper::addApprovedFilter($profile, $query);
                }
            }
            else {
                if (XG_Cache::cacheOrderN()) {
                    $query = XG_Query::create($query);
                    $query->addCaching(XG_Cache::key('type','Track'));
                }
            }

            $query->alwaysReturnTotalCount(true);
            $tracks    = $query->execute();
            $numTracks = $query->getTotalCount();
            if($end>$numTracks) $end = $numTracks;

            // If $sort is null, arrange $tracks in the order of $ids
            if (is_null($sort)) {
                $idsAndObjects = array();
                foreach ($tracks as $track) {
                    $idsAndObjects[$track->id] = $track;
                }
                $tracks = array();
                foreach ($ids as $id) {
                    if ($idsAndObjects[$id]) {
                        $tracks[] = $idsAndObjects[$id];
                    }
                }
                if (count($ids) > $end - $begin) {
                    $trackSlice = array();
                    $counter = 0;
                    foreach ($tracks as $track) {
                        if (($counter < $end) && ($counter >= $begin)) {
                            $trackSlice[] = $track;
                        }
                        $counter++;
                    }
                    $tracks = $trackSlice;
                }
            }
            return array('tracks' => $tracks, 'numTracks' => $numTracks);
        } else {
            return array('tracks' => array(), 'numTracks' => 0);
        }
    }

    /**
     * Returns the most recent uploaded tracks for a given user screenname
     *
     * */
    public static function getUserTracks($screenname = null, $begin = 0, $end = 100, $order = 'asc') {
        $query = XN_Query::create('Content')
                         ->filter('owner')
                         ->filter('type', '=', 'Track')
                         ->order('createdDate', $order)
                         ->begin($begin)
                         ->end($end);
        if($screenname) $query->filter('contributorName', 'eic', $screenname);
        $query->alwaysReturnTotalCount(true);
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Track'));
        }
        $tracks    = $query->execute();
        return array('tracks'=>$tracks, 'numTracks'=> $query->getTotalCount());
    }

    /**
     * Returns the most recent uploaded tracks of the network
     *
     * */
    public static function getRecentTracks($begin = 0, $end = 100, $order = 'desc') {
        return self::getUserTracks(null, $begin, $end, $order);
    }

    /**
     * Returns the most recent promoted tracks of the network
     *
     * */
    public static function getPromotedTracks($n) {
        return self::getPromotedTracksProper($n, true);
    }

    /**
     * Returns the given number of recent tracks.
     *
     * @param $n max number of tracks to return;
     * @param $promotedOrUnpromoted whether to return promoted or unpromoted tracks
     * @return an array of XN_Content objects of type Track
     */
    private static function getPromotedTracksProper($n, $promotedOrUnpromoted) {
        if ($n == 0) { return array(); }
        $query = XN_Query::create('Content');
        $query->filter('type', '=', 'Track');
        $query->filter('owner');
        $query->end($n);
        $query->alwaysReturnTotalCount(true);
        Music_SecurityHelper::addVisibilityFilter(XN_Profile::current(), $query);
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if($promotedOrUnpromoted){
            if (! XG_PromotionHelper::areQueriesEnabled()) { return array(); }
            XG_PromotionHelper::addPromotedFilterToQuery($query);
            $query->order('my->' . XG_PromotionHelper::attributeName(), 'desc', XN_Attribute::DATE);
        } else {
            XG_PromotionHelper::addUnpromotedFilterToQuery($query);
            $query->order('createdDate', 'desc', XN_Attribute::DATE);
        }

        /* Only cache if the user is not logged in. If the user is logged in, then addVisibilityFilter
         * adds as FRIENDS() filter */
        // TODO: Allow caching when logged in, but use XG_QueryHelper::setMaxAgeForFriendsQuery [Jon Aquino 2008-09-17]
        if (!XN_Profile::current()->isLoggedIn()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Track'));
        }

        $tracks    = $query->execute();
        return array('tracks'=>$tracks, 'numTracks'=> $query->getTotalCount());
    }

    /**
     * Returns the highest rated tracks of the network
     *
     * */
    public static function getHighestRatedTracks($n) {
        return self::getHighestRatedTracksProper($n);
    }

    /**
     * Returns the given number of highest rated tracks .
     *
     * @param $n max number of tracks to return;
     * @return an array of XN_Content objects of type Track
     */
    private static function getHighestRatedTracksProper($n) {
        if ($n == 0) { return array(); }

        $query = XN_Query::create('Content');
        $query->filter('type', '=', 'Track');
        $query->filter('owner');
        $query->end($n);
        $query->order('my->ratingAverage', 'desc', XN_Attribute::NUMBER);
        $query->alwaysReturnTotalCount(true);

        /* Only cache if the user is not logged in. If the user is logged in, then addVisibilityFilter
         * adds as FRIENDS() filter */
        // TODO: Allow caching when logged in, but use XG_QueryHelper::setMaxAgeForFriendsQuery [Jon Aquino 2008-09-17]
        if (!XN_Profile::current()->isLoggedIn()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Track'));
        }
        $tracks    = $query->execute();
        return array('tracks'=>$tracks, 'numTracks'=> $query->getTotalCount());
    }

    /**
     * Delete the specified track updating the contributor's track count and optionally
     * saving the user object.
     *
     * @param XN_Content(Track) track  The track to delete
     * @param boolean saveUser  Whether to save the user object after update
     *
     * @returns integer  Number of tracks removed
     */
    public static function delete($track, $saveUser = true) {
        $user = Music_UserHelper::load($track->contributorName);
        if (! is_null($user)) {
            Music_UserHelper::removeTrack($user);
            if ($saveUser) { $user->save(); }
        }
        if ($track->my->audioAttachment) {
            XN_Content::delete(XG_Cache::content($track->my->audioAttachment));
            $numObjectsDeleted++;
        }
        if ($track->my->artworkAtachment) {
            XN_Content::delete(XG_Cache::content($track->my->artworkAtachment));
            $numObjectsDeleted++;
        }
        // If the track was to-be-moderated, clear the approval-link cache
        if ($track->my->approved == 'N') {
            W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
        }
        try {
            XN_Content::delete($track);
            $numObjectsDeleted++;
        } catch (Exception $e) {
            // nothing - do not increment $numObjectsDeleted
        }
        return $numObjectsDeleted;
    }


    /**
     * Deletes tracks and their ancillary objects.
     *
     * @param $tracks array The Track objects to delete.
     * @param $limit integer maximum number of content objects to remove (approximate).
     * @return the number of objects deleted
     */
    public static function deleteTracks($tracks, $limit) {
        if (count($tracks) > $limit) { $tracks = array_slice($tracks, 0, $limit); }
        if (count($tracks) < 1) { return 0; }

        $users = XG_UserHelper::uniqueContributorUserObjects($tracks);
        $numObjectsDeleted = 0;
        foreach ($tracks as $track) {
            $numObjectsDeleted += self::delete($track, false);
        }
        // save the unique users once, instead of once per deletion
        foreach ($users as $user) {
            $user->save();
        }
        return $numObjectsDeleted;
    }

    public static function getKnownLicenses() {
        // Keep this list in sync with the Java uploader applet [Jon Aquino 2008-01-08]
        return array(
            'http://www.copyright.gov/title17/'                 => xg_text('COPYRIGHT'),
            'http://creativecommons.org/licenses/by/3.0/'       => xg_text('CC_BY_X',       '3.0'),
            'http://creativecommons.org/licenses/by-sa/3.0/'    => xg_text('CC_BY_SA_X',    '3.0'),
            'http://creativecommons.org/licenses/by-nd/3.0/'    => xg_text('CC_BY_ND_X',    '3.0'),
            'http://creativecommons.org/licenses/by-nc/3.0/'    => xg_text('CC_BY_NC_X',    '3.0'),
            'http://creativecommons.org/licenses/by-nc-sa/3.0/' => xg_text('CC_BY_NC_SA_X', '3.0'),
            'http://creativecommons.org/licenses/by-nc-nd/3.0/' => xg_text('CC_BY_NC_ND_X', '3.0'),
            'http://creativecommons.org/licenses/publicdomain/' => xg_text('PUBLICDOMAIN'),
            );
    }

    /**
     * Adds a rating for this track.
     *
     * @param track     The track to be rated
     * @param oldRating The old rating by the user if any
     * @param newRating The new rating
     */
    public function addRating($track, $oldRating, $newRating) {
        $oldTotal = $track->my->ratingAverage * $track->my->ratingCount;
        if ($oldRating) {
            $newTotal          = $oldTotal - $oldRating + $newRating;
        } else {
            $newTotal          = $oldTotal + $newRating;
            $track->my->ratingCount = $track->my->ratingCount + 1;
        }
        $track->my->ratingAverage  = $newTotal / $track->my->ratingCount;
        $track->my->lastActivityOn = date('c', time());
    }

    /**
     * Returns whether uploads with the given MIME type are supported.
     *
     * @param $mimeType string  the MIME type to check
     * @return boolean  whether the MIME type is a supported audio type
     */
    public static function isMimeTypeSupported($mimeType) {
        // TODO: Fix: this check would say that text/plain is a supported audio type.  [Jon Aquino 2008-01-08]
        return ! in_array($mimeType, array('audio/aac', 'audio/aiff', 'audio/mid', 'audio/midi', 'audio/mod', 'audio/wav', 'audio/x-aiff', 'audio/x-m4a', 'audio/x-m4b', 'audio/x-m4p', 'audio/x-ms-wma', 'audio/x-ms-wax', 'audio/x-wav', 'application/octet-stream'));
    }

    /**
     * Creates a Track from the uploaded data.
     *
     * @param $name string  name of the POST variable containing the uploaded file.
     * @param $helpers array
     *         audioAttachmentModel - AudioAttachment or mock object;
     *         trackModel - Track or mock object;
     * @return W_Content  an unsaved Track, or null if a problem occurred
     */
    public static function upload($name, $helpers) {
        foreach ($helpers as $key => $value) { ${$key} = $value; }
        $filename = $_POST[$name];
        if ($_POST["$name:status"] || ! self::isMimeTypeSupported($_POST["$name:type"])) {
            return null;
        }
        $trackTitle = preg_replace('/\.(mp3|ogg|wav)$/ui', '', $filename);
        // Also remove initial directory paths that IE may insert
        if (mb_strpos($trackTitle, '\\') !== false) {
            $trackTitle = preg_replace('@^.*\\\\([^\\\\]+)$@u', '$1', $trackTitle);
        }
        $track = $trackModel->create();
        //@TODO support convertion, at the moment the mp3 goes unmodified
        $audioAttachment = $audioAttachmentModel->create($filename, $track, false, $name, $_POST["$name:type"]);
        $audioAttachment->save();
        $track->my->audioAttachment = $audioAttachment->id;
        $track->my->audioUrl = $audioAttachment->fileUrl('data');
        $track->my->approved = 'Y';
        //@TODO use id3 tags if present, also, better filename stripping to guess artist/title for files named "Artist Name - Title Name.mp3"
        $track->my->trackTitle = $trackTitle;
        $track->my->filename = xg_basename($filename);
        $track->my->length = $_POST["$name:size"];
        return $track;
    }
}
