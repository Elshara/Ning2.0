<?php

/**
 * Common code for saving and querying User objects.
 */
class Music_UserHelper {
    /**
     * Loads a user object from the content store. If there is no user object yet for
     * the specified user, a new one will be created and stored.
     */
    public static function loadOrCreate($profileOrScreenName) {
        return self::load($profileOrScreenName, true);
    }

    public static function load($profileOrScreenName, $createIfNecessary = false) {
        $user = User::load($profileOrScreenName, $createIfNecessary);
        if ($user && is_null(self::get($user, 'trackCount'))) {
            self::set($user, 'trackCount', 0, XN_Attribute::NUMBER);
        }
        return $user;
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
        return XG_App::widgetAttributeName(W_Cache::current('W_Widget'), $attributeName);
    }
    
    /**
     * Updates the activity value for this user.
     */
    public static function updateActivityCount($user) {
        // In Bazel Tracks, activity excludes comments and ratings - you need to contribute
        // a track to appear on the Popular Contributors page [from Jon Aquino 2006-12-04 in photo_userhelper]
        self::set($user, 'activityCount', self::get($user, 'trackCount'), XN_Attribute::NUMBER);
    }    

    /**
     * Registers at the user that he or she added a number of tracks.
     *
     * @param numTracks The number of added tracks
     */
    public static function addTracks($user, $numTracks) {
        self::set($user, 'trackCount', self::get($user, 'trackCount') + $numTracks, XN_Attribute::NUMBER);
        self::set($user, 'lastUploadOn', date('c'), XN_Attribute::DATE);
        self::updateActivityCount($user);
    }
    
    /**
     * Registers at the user that one of his tracks was removed.
     */
    public static function removeTrack($user) {
        if (self::get($user, 'trackCount') > 0) {
            self::set($user, 'trackCount', self::get($user, 'trackCount') - 1, XN_Attribute::NUMBER);
        }
    }
    
    /**
     * Returns the rating that the user applied to the indicated track (if any).
     *
     * @param trackId The id of the track
     * @param The rating or null if the user has not rated the photo
     */
    public static function getRating($user, $trackId) {
        return Music_ContentHelper::value($trackId, $user, Music_UserHelper::attributeName('ratings'));
    }

    /**
     * Adds or updates the rating of the user for the indicated track.
     *
     * @param trackId The id of the track
     * @param rating  The rating value
     */
    public static function setRating($user, $trackId, $rating) {
        Music_ContentHelper::add($trackId, $user, Music_UserHelper::attributeName('ratings'), $rating);
        self::set($user, 'ratingCount', Music_ContentHelper::count($user, Music_UserHelper::attributeName('ratings')), XN_Attribute::NUMBER);
        self::updateActivityCount($user);
    }
    

}