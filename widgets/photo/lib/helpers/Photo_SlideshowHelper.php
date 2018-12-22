<?php

/**
 * Useful functions for working with the photo slideshow embeddable.
 */
class Photo_SlideshowHelper {

    /**
     * @param $args owner, favorites, friends, albumId, sort
     */
    public static function urls($args) {
        extract($args);
        $url = W_Cache::current('W_Widget')->buildUrl('photo', 'slideshow');
        if ($owner) {
            $url = Photo_HtmlHelper::addParamToUrl($url,  $favorites ? 'favoritesOf' : 'screenName', $owner, false);
            $action = $favorites ?'slideshowFeedFavorites':'slideshowFeedForContributor';
        } else if ($friends) {
            $url = Photo_HtmlHelper::addParamToUrl($url, 'friends', 'true', false);
            $action = 'slideshowFeedFriends';
        } else if ($albumId) {
            $url = Photo_HtmlHelper::addParamToUrl($url, 'albumId', $albumId, false);
            $action = 'slideshowFeedAlbum';
        } else {
            $action = 'slideshowFeed';
        }
        if ($sort) {
            $url = Photo_HtmlHelper::addParamToUrl($url,  'sort', $sort, false);
        }
        $feed_url = urlencode(W_Cache::current('W_Widget')->buildUrl('photo',$action, '?sort='.$sort.'&screenName='.$owner.'&id='.$albumId.'&tag='.$tag));
        return array($url, $feed_url);
    }

    /**
     * Determines the URL for the slideshow feed for the given photoSet
     *
     * @param $photoSet string  the set of photos to display: all, promoted, for_contributor, popular, owner, album_[id]
     * @param $contributorName string  username of the contributor, if $photoSet is "for_contributor"
     * @param $checkIfPhotosExist boolean  whether to check that the slideshow is not empty. Alters the return value.
     * @param $random boolean  whether to shuffle the photos
     * @return the URL, or if $checkIfPhotosExist, an array with the URL and a boolean indicating whether it contains any photos
     */
    public static function feedUrl($photoSet, $contributorName, $checkIfPhotosExist = false, $random = false) {
        if ($photoSet == 'all') {
            $photosData = $checkIfPhotosExist ? Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), array(), null, 0, 1) : null;
            // Slideshow player seems to need the "?"; otherwise it won't display anything. [Jon Aquino 2007-05-28]
            $feedUrl = W_Cache::getWidget('photo')->buildUrl('photo', 'slideshowFeed', '?');
        } elseif ($photoSet == 'promoted') {
            $photosData = $checkIfPhotosExist ? Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), array('promoted' => true), null, 0, 1) : null;
            $feedUrl = W_Cache::getWidget('photo')->buildUrl('photo', 'slideshowFeed') . '?promoted=true';
        } elseif ($photoSet == 'for_contributor') {
            $photosData = $checkIfPhotosExist ? Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), array('contributor' => $contributorName), null, 0, 1) : null;
            $feedUrl = W_Cache::getWidget('photo')->buildUrl('photo', 'slideshowFeedForContributor', array('screenName' => $contributorName));
        } elseif ($photoSet == 'popular') {
            $photosData = $checkIfPhotosExist ? Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), null, Photo_PhotoHelper::getMostPopularSortingOrder(), 0, 1) : null;
            $feedUrl = W_Cache::getWidget('photo')->buildUrl('photo', 'slideshowFeed') . '?popular=true';
        } elseif ($photoSet == 'owner') { // here
            $photosData = $checkIfPhotosExist ? Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), array('contributor' => XN_Application::load()->ownerName), null, 0, 1) : null;
            $feedUrl = W_Cache::getWidget('photo')->buildUrl('photo', 'slideshowFeed') . '?owner=true';
        } elseif (preg_match('/album_(.+)/u', $photoSet, $matches)) {
            try{
                $album = Photo_AlbumHelper::load($matches[1]);
                $photoIds   = Photo_ContentHelper::ids($album, 'photos');
                $photosData = $checkIfPhotosExist ? Photo_PhotoHelper::getSpecificPhotos(XN_Profile::current(), $photoIds, null, 0, 1) : null;
                $feedUrl = W_Cache::getWidget('photo')->buildUrl('photo', 'slideshowFeedAlbum', array('id' => $matches[1]));
            }catch (Exception $e) {
                //if the album was deleted or failed to load, display promoted photos [BAZ-700]
            }
            if(! $album->my->photoCount) {
                $photosData = $checkIfPhotosExist ? Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), array('promoted' => true), null, 0, 1) : null;
                $feedUrl = W_Cache::getWidget('photo')->buildUrl('photo', 'slideshowFeed') . '?promoted=true';
            }
        } else {
            xg_echo_and_throw('Unrecognized photoSet: ' . $photoSet . ' (1847711400)');
        }
        if ($random) { $feedUrl = XG_HttpHelper::addParameter($feedUrl, 'random', 1); }
        return $checkIfPhotosExist ? array($feedUrl, $photosData['numPhotos']) : $feedUrl;
    }

}
