<?php

class Photo_PrivacyHelper {

    /** @deprecated */
    public static function checkMembership() { }

    public static function isAppBlockingCurrentUser() {
        $widget = W_Cache::current('W_Widget');
        if (Photo_PrivacyHelper::getPrivacyType() == 'public') { return false; }
        return XG_SecurityHelper::userIsBanned();
    }

    public static function canCurrentUserSeeInviteLinks() {
        return XG_App::canSeeInviteLinks(XN_Profile::current());
    }

    /**
     * Returns whether Share This links and buttons should be visible to the current user,
     * for the specified photo. Differs from canCurrentUserShare; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * photo (until they sign in).
     *
     * @param XN_Content|W_Content photo the Photo to share
     * @return whether to show Share This buttons for the photo
     * @see canCurrentUserShare
     */
    public static function canCurrentUserSeeShareLinks($photo) {
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        // Note that albums are passed to this function as well. Need to address this in the
        // future, but it works for now [Jon Aquino 2006-12-25]
        if (Photo_PhotoHelper::isAwaitingApproval($photo)) { return false; }
        if ($photo->my->visibility == 'me') { return false; }
        // Allow signed-out people to see the Share This link [Jon Aquino 2006-12-20]
        if (! XN_Profile::current()->isLoggedIn()) { return true; }
        if (! XG_App::canSendInvites(XN_Profile::current())) { return false; }
        return true;
    }

    /**
     * Returns whether the current user can in fact share the specified photo.
     * Differs from canCurrentUserSeeShareLinks; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * photo (until they sign in).
     *
     * @param XN_Content|W_Content photo the Photo to share
     * @return whether the current user is allowed to share the photo
     * @see canCurrentUserSeeShareLinks
     */
    public static function canCurrentUserShare($photo) {
        // An invite key is included in the Share This email  [Jon Aquino 2006-10-24]
        return XG_App::canSendInvites(XN_Profile::current()) && self::canCurrentUserSeeShareLinks($photo);
        // Note that albums are passed to this function as well. Need to address this in the
        // future, but it works for now [Jon Aquino 2006-12-25]
    }

    public static function getPrivacyType() {
        return XG_App::appIsPrivate() ? 'private' : 'public';
    }

    public static function addBlockedFilter($query) {
        User::addBlockedFilter($query);
    }

}
