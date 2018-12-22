<?php

/**
 * Useful functions for working with cache-expiration conditions.
 */
class XG_CacheExpiryHelper {

    /**
     * Name of the expiry condition when the set of promoted objects changes.
     *
     * @param $contentType string  the content type, e.g., Photo
     * @return string  the name of the invalidation condition
     */
    public static function promotedObjectsChangedCondition($contentType) {
        return "promoted-$contentType-objects-changed";
    }

    /**
     * Name of the expiry condition when any user is banned.
     *
     * @return string  the name of the invalidation condition
     */
    public static function userBannedCondition() {
        return "user-banned";
    }

    /**
     * Name of the expiry condition when any user is unbanned.
     *
     * @return string  the name of the invalidation condition
     */
    public static function userUnbannedCondition() {
        return "user-unbanned";
    }

    /**
     * Name of the expiry condition when the set of photos by a given user changes.
     *
     * @param $screenName string  the person's username
     * @return string  the name of the invalidation condition
     */
    public static function photoAddedCondition($screenName) {
        return "$screenName-added-photo";
    }

    /**
     * Name of the expiry condition when the set of favorite photos for a given user changes.
     *
     * @param $screenName string  the person's username
     * @return string  the name of the invalidation condition
     */
    public static function favoritePhotosChangedCondition($screenName) {
        return "favorite-photos-for-$screenName-changed";
    }

    /**
     * Name of the expiry condition when the album is saved
     *
     * @param $is string  the album's content ID
     * @return string  the name of the invalidation condition
     */
    public static function albumChangedCondition($id) {
        return "album-$id-changed";
    }

    /** Array of screen names that have been banned during the current request. */
    protected static $bannedScreenNames = array();

    /** Array of screen names that have been unbanned during the current request. */
    protected static $unbannedScreenNames = array();

    /**
     * Called when a user's xg_index_status attribute changes.
     *
     * @param $user XN_Content|W_Content  the User object
     * @param $oldStatus  the old xg_index_status
     */
    public static function userStatusChanged($user, $oldStatus) {
        if ($oldStatus !== 'blocked' && $user->my->xg_index_status === 'blocked') {
            self::$bannedScreenNames[$user->title] = $user->title;
        } elseif ($oldStatus === 'blocked' && $user->my->xg_index_status !== 'blocked') {
            self::$unbannedScreenNames[$user->title] = $user->title;
        }
    }

    /** Newly created content objects */
    private static $newObjects = array();

    /**
     * Called before a content object has been saved.
     *
     * @param $object mixed  The content object, an array, or possibly some other thing if the XN_Event API changes
     */
    public static function beforeSave($object) {
        if (is_array($object)) {
            foreach ($object as $o) { self::beforeSave($o); }
            return;
        }
        if (! ($object instanceof XN_Content || $object instanceof W_Content)) { return; }
        if (! $object->id) { self::$newObjects[] = $object; }
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if ($object->type == 'User' && XG_PromotionHelper::isPromoted($object) && User::isBanned($object)) {
        	self::_addChangedType($object);
        }
    }

    /**
     * Called after a content object has been saved.
     *
     * @param $object mixed  The content object, an array, or possibly some other thing if the XN_Event API changes
     */
    public static function afterSave($object) {
        if (is_array($object)) {
            foreach ($object as $o) { self::afterSave($o); }
            return;
        }
        if (! ($object instanceof XN_Content || $object instanceof W_Content)) { return; }
        if ($object->type == 'Album') {
            XN_Cache::invalidate(self::albumChangedCondition($object->id));
        }
        if ($object->type == 'Photo') {
            foreach (self::$newObjects as $newObject) {
                if ($object === $newObject) {
                    XN_Cache::invalidate(self::photoAddedCondition(XN_Profile::current()->screenName));
                }
            }
        }
        if ($object->type == 'User' && mb_strlen(self::$bannedScreenNames[$object->title])) {
            XN_Cache::invalidate(self::userBannedCondition());
        }
        if ($object->type == 'User' && mb_strlen(self::$unbannedScreenNames[$object->title])) {
            XN_Cache::invalidate(self::userUnbannedCondition());
        }
        self::$newObjects = array();
    }

    /**
     * Called after a content object has been deleted.
     *
     * @param mixed $object  The content object, an array, or possibly some other thing if the XN_Event API changes
     */
    public static function beforeDelete($object) {
        if (is_array($object)) {
            foreach ($object as $o) { self::beforeDelete($o); }
            return;
        }
        if (! ($object instanceof XN_Content || $object instanceof W_Content)) { return; }
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (XG_PromotionHelper::isPromoted($object)) {
            self::_addChangedType($object);
        }
    }

    /**
     * Called after a content object has been promoted.
     *
     * @param $object XN_Content|W_Content  The content object.
     */
    public static function afterFeature($object) {
        self::_addChangedType($object);
    }

    /**
     * Called before a content object has been unpromoted.
     *
     * @param $object XN_Content|W_Content  The content object.
     */
    public static function beforeUnfeature($object) {
        self::_addChangedType($object);
    }

    /** Content types of promoted objects that changed during this request. */
    protected static $typesOfChangedPromotedObjects = array();
	protected static $installed = 0;
    /**
     * Called when the script is about to terminate.
     */
    public static function onExit() {
        foreach (self::$typesOfChangedPromotedObjects as $type) {
            XN_Cache::invalidate(self::promotedObjectsChangedCondition($type));
        }
    }
    //
    protected function _addChangedType($object) { # void
        self::$typesOfChangedPromotedObjects[$object->type] = $object->type;
		if (!self::$installed) {
			register_shutdown_function(array('XG_CacheExpiryHelper', 'onExit'));
			self::$installed = 1;
		}
    }

}

XN_Event::listen('xn/content/save/before', array('XG_CacheExpiryHelper', 'beforeSave'));
XN_Event::listen('xn/content/save/after', array('XG_CacheExpiryHelper', 'afterSave'));
XN_Event::listen('user/status/changed', array('XG_CacheExpiryHelper', 'userStatusChanged'));
XN_Event::listen('feature/after', array('XG_CacheExpiryHelper', 'afterFeature'));
XN_Event::listen('unfeature/before', array('XG_CacheExpiryHelper', 'beforeUnfeature'));
XN_Event::listen('xn/content/delete/before', array('XG_CacheExpiryHelper', 'beforeDelete'));
// Don't automatically expire on deletion, as the repeated expiry calls may be too
// expensive during bulk deletion. Instead, rely on the usual 30-minute cache TTL
// for deletes. [Jon Aquino 2007-10-11]
