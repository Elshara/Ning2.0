<?php

/**
 * Common code for app-level functions
 */
class Photo_AppHelper {
    /**
     * Determines whether the application shall be Ning-branded.
     *
     * @return true if the application is ning-branded
     */
    public static function isNingBranded() {
        return in_array(mb_strtolower(XN_Application::load()->relativeUrl),
                        array('devphotos', 'devphotos1phil', 'devphotos2tom', 'athena24sdevphotos2tom'));
    }

    public static function hasAboutSection($profile) {
        return ($profile->isLoggedIn() && !Photo_SecurityHelper::failed(Photo_SecurityHelper::checkCurrentUserIsAdmin($profile))) ||
               (mb_strlen(W_Cache::current('W_Widget')->privateConfig['aboutHeading']) > 0) ||
               (mb_strlen(W_Cache::current('W_Widget')->privateConfig['aboutText']) > 0);
    }

}
