<?php

/**
 * Useful functions for working with presence.
 */
class XG_PresenceHelper {

    /**
     * Returns whether to show the presence indicator for the given profile.
     *
     * @param XN_Profile  The user's profile
     * @return boolean  Whether permission is granted
     */
    public static function canShowPresenceIndicator($profile) {
        return $profile->screenName == XN_Profile::current()->screenName || $profile->presence == 'online';
    }

}
