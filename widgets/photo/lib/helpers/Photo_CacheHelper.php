<?php
/**
 * Contains helper methods for dealing with Cache.
 */
class Photo_CacheHelper {

    public static function getInvalidationConditionsForAlbum($album) {
		$keys = array();
		$keys[] = Photo_PhotoHelper::PHOTO_RSS_ALBUM.$album->id;
	   	$keys[] = Photo_PhotoHelper::PHOTO_SLIDESHOW_ALBUM.'SMALL_'.$album->id;
       	$keys[] = Photo_PhotoHelper::PHOTO_SLIDESHOW_ALBUM.'MINI_'.$album->id;
		return $keys;	
    }

    public static function getInvalidationConditionsForPhoto($photo) {
		$keys = array();
		$keys[] = Photo_PhotoHelper::PHOTO_RSS;
		$keys[] = Photo_PhotoHelper::PHOTO_RSS_CONTRIBUTOR.$photo->contributorName;
	   	$keys[] = Photo_PhotoHelper::PHOTO_SLIDESHOW.'SMALL';
       	$keys[] = Photo_PhotoHelper::PHOTO_SLIDESHOW.'MINI';
       	$keys[] = Photo_PhotoHelper::PHOTO_SLIDESHOW_CONTRIBUTOR.'SMALL_'.$photo->contributorName;
       	$keys[] = Photo_PhotoHelper::PHOTO_SLIDESHOW_CONTRIBUTOR.'MINI_'.$photo->contributorName;
		return $keys;
    }
}
?>
