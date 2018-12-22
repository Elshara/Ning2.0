<?php

class Video_PrivacyHelper {

    public static function checkMembership() { }

    public static function isAppBlockingCurrentUser() {
        $user = User::load(XN_Profile::current()->screenName);
        return (! $user) || User::isBanned($user);

        $widget = W_Cache::current('W_Widget');
        if (Video_PrivacyHelper::getPrivacyType() == 'public') { return false; }
        return XG_SecurityHelper::userIsBanned();
    }

    public static function canCurrentUserSeeInviteLinks() {
        return XG_App::canSeeInviteLinks(XN_Profile::current());
    }

    /**
     * Returns whether Share This links and buttons should be visible to the current user,
     * for the specified video. Differs from canCurrentUserShare; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * video (until they sign in).
     *
     * @param XN_Content|W_Content video the Video to share
     * @return whether to show Share This buttons for the video
     * @see canCurrentUserShare
     */
    public static function canCurrentUserSeeShareLinks($video) {
        if (Video_VideoHelper::isAwaitingApproval($video)) { return false; }
        if ($video->my->visibility == 'me') { return false; }
        // Allow signed-out people to see the Share This link [Jon Aquino 2006-12-20]
        if (! XN_Profile::current()->isLoggedIn()) { return true; }
        if (! XG_App::canSendInvites(XN_Profile::current())) { return false; }
        return true;
    }

    /**
     * Returns whether the current user can in fact share the specified video.
     * Differs from canCurrentUserSeeShareLinks; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * video (until they sign in).
     *
     * @param XN_Content|W_Content video the Video to share
     * @return whether the current user is allowed to share the video
     * @see canCurrentUserSeeShareLinks
     */
    public static function canCurrentUserShare($video) {
        // An invite key is included in the Share This email  [Jon Aquino 2006-10-24]
        return XG_App::canSendInvites(XN_Profile::current()) && self::canCurrentUserSeeShareLinks($video);
    }

    public static function getPrivacyType() {
        return XG_App::appIsPrivate() ? 'private' : 'public';
    }

    public static function addBlockedFilter($query) {
        User::addBlockedFilter($query);
    }

}