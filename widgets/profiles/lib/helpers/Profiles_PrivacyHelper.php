<?php

class Profiles_PrivacyHelper {

    /**
     * Returns whether Share This links and buttons should be visible to the current user,
     * for the specified photo. Differs from canCurrentUserShare; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * photo (until they sign in).
     *
     * @param $object XN_Content|W_Content The page or blog post to share
     * @return whether to show Share This buttons for the page or blog post
     * @see canCurrentUserShare
     */
    public static function canCurrentUserSeeShareLinks($object = null) {
        // Allow signed-out people to see the Share This link [Jon Aquino 2006-12-20]
        if (! XN_Profile::current()->isLoggedIn()) { return true; }
        if (! XG_App::canSendInvites(XN_Profile::current())) { return false; }
        // For user pages, there's no object-specific properties to check
        if ($object) {
            if ($object->my->visibility == 'me') { return false; }
            // Only published blog posts can be shared
            if (($object->type == 'BlogPost') && ($object->my->publishStatus != 'publish')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns whether the current user can in fact share the specified page
     * or blog post.
     *
     * Differs from canCurrentUserSeeShareLinks; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * object (until they sign in).
     *
     * @param XN_Content|W_Content The page or blog post to share
     * @return whether the current user is allowed to share the object
     * @see canCurrentUserSeeShareLinks
     */
    public static function canCurrentUserShare($object = null) {
        // An invite key is included in the Share This email  [Jon Aquino 2006-10-24]
        return XG_App::canSendInvites(XN_Profile::current()) && self::canCurrentUserSeeShareLinks($object);
    }
}
