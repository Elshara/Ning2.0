<?php

/**
 * Helper class that deals with showing the correct Sign in, Add, Remove and View links in all the various gadget views.
 */
class OpenSocial_LinkHelper {
    
    /**
     * Return a link to the my applications page
     *
     * @return      string      URI of the link the current user should see to get to My Applications
     */
    public static function getMyApplicationsLink() {       
        if (! XN_Profile::current()->isLoggedIn()) {
            return XG_HttpHelper::signInUrl(W_Cache::getWidget('opensocial')->buildUrl('application', 'apps'));
        } else {
            return W_Cache::getWidget('opensocial')->buildUrl('application', 'apps', array('user' => XN_Profile::current()->screenName));
        }
    }
    
    /**
     * Return a string, unique to the gadget, used to identify where the gadget is in a page
     *
     * @param   $appUrl     string      The URI of the gadget xml
     * @return              string      unique string for the gadget
     */
    public static function getUniqueId($appUrl) {
        return 'xj-osoc-' . md5($appUrl);
    }
    
    /**
     * Get the correct redirect for an application that has already been installed
     *
     * @param   $app        OpenSocialAppData object        application to get the link for
     * @return              string                          URI to redirect the user to
     */
    public static function getAlreadyInstalledApplicationRedirect($app) {
        if($app->my->isOnMyPage) {
            return 'http://' . $_SERVER['HTTP_HOST'] . User::profileUrl(XN_Profile::current()->screenName) . "?oldAppUrl=" . rawurlencode($app->my->appUrl);
        } else {
            // if the member has more than 5 apps, it won't be added to their profile page, so we should redirect to the canvas page
            return W_Cache::getWidget('opensocial')->buildUrl('application', 'show', array('appUrl' => $app->my->appUrl, 'owner' => XN_Profile::current()->screenName, 'readded' => '1'));
        }
    }
    
    /**
     * Get the correct redirect for a newly installed application
     *
     * @param   $app        OpenSocialAppData object        application to get the redirect for
     * @return              string                          URI to redirect the user to
     */
    public static function getInstalledApplicationRedirect($app) {
        if($app->my->isOnMyPage) {
            return 'http://' . $_SERVER['HTTP_HOST'] . User::profileUrl(XN_Profile::current()->screenName) . "?newAppUrl=" . rawurlencode($app->my->appUrl);
        } else {
            // if the member has more than 5 apps, it won't be added to their profile page, so we should redirect to the canvas page
            return W_Cache::getWidget('opensocial')->buildUrl('application', 'show', array('appUrl' => $app->my->appUrl, 'owner' => XN_Profile::current()->screenName, 'newApp' => "1"));
        }
    }
    
    /**
     * Determine if we should display the Add to My Page link on a profile page.
     *
     * @param   $appUrl     string  URL of app in question.
     * @param   $viewerName string  Screen name of the current user.
     * @param   $ownerName  string  Screen name of the app owner.
     * @return              boolean
     */
    public static function showProfileViewAddLink($appUrl, $viewerName, $ownerName) {
        return self::viewerIsNotOwnerAndIsNotInstalledAndIsInDirectory($appUrl, $viewerName, $ownerName);
    }
    
    /**
     * Determine if we should display the Add to My Page link on an "about" page.
     *
     * @param   $appUrl     string  URL of app in question.
     * @param   $viewerName string  Screen name of the current user.
     * @return              boolean
     */
    public static function showAboutPageAddLink($appUrl, $viewerName) {
        return self::isNotInstalledAndIsInDirectory($appUrl, $viewerName);
    }
    
    /**
     * Determine if we should display the Add to My Page link in an appDetail section.
     *
     * @param   $appUrl         string  URL of app in question.
     * @param   $inAppDirectory boolean TRUE if we are currently in the app directory, FALSE if in another context (my apps).
     * @return                  boolean
     */
    public static function showAppDetailAddLink($appUrl, $viewerName, $inAppDirectory) {
        if (! $inAppDirectory) { return false; }
        return self::isNotInstalledAndIsInDirectory($appUrl, $viewerName);
    }
    
    /**
     * Determine if we should display the Add to My Page link on a canvas page.
     *
     * @param   $appUrl     string  URL of app in question.
     * @param   $viewerName string  Screen name of the current user.
     * @param   $ownerName  string  Screen name of the app owner.
     * @return              boolean
     */
    public static function showCanvasViewAddLink($appUrl, $viewerName, $ownerName) {
        return self::viewerIsNotOwnerAndIsNotInstalledAndIsInDirectory($appUrl, $viewerName, $ownerName);
    }

    /**
     * Determine if we should display the Remove Application link on a canvas page.
     *
     * @param   $viewerName string  Screen name of the current user.
     * @param   $ownerName  string  Screen name of the app owner.
     * @return              boolean
     */
    public static function showCanvasViewRemoveLink($viewerName, $ownerName) {
        return ($viewerName == $ownerName);
    }
    
    private static function viewerIsNotOwnerAndIsNotInstalledAndIsInDirectory($appUrl, $viewerName, $ownerName) {
        if ($viewerName == $ownerName) { return false; }
        return self::isNotInstalledAndIsInDirectory($appUrl, $viewerName);
    }
    
    private static function isNotInstalledAndIsInDirectory($appUrl, $screenName) {
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        if (OpenSocial_GadgetHelper::isApplicationInstalled($appUrl, $screenName)) { return false; }
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');
        if (! OpenSocial_ApplicationDirectoryHelper::isAppApproved($appUrl)) { return false; }
        return true;
    }
}
