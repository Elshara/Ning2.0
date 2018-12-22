<?php

/**
 * Useful functions for working with sign-in and sign-up.
 */
class XG_AuthorizationHelper {

    /**
     * Returns the URL for the sign-in page.
     *
     * @param $target string  the URL to go to after sign-in, or null for the current page
     * @param $groupToJoin string|XN_Content|W_Content  (optional) the Group object (or its URL) to make the user a member of
     * @param string emailAddress (optional) initial value for the Email Address field, or null to use that of the current user (if any)
     * @return string  the URL
     */
    public static function signInUrl($target = null, $groupToJoin = null, $emailAddress = null) {
        if ($groupToJoin instanceof XN_Content || $groupToJoin instanceof W_Content) { $groupToJoin = $groupToJoin->my->url; }
        if (! $target) { $target = XG_HttpHelper::currentUrl(); }
        return W_Cache::getWidget('main')->buildUrl('authorization', 'signIn', array('target' => self::updateErrorTarget($target), 'groupToJoin' => $groupToJoin, 'emailAddress' => $emailAddress));
    }

    /**
     * Returns the URL for the sign-up page.
     *
     * @param $target string  the URL to go to after sign-up, or null for the current page
     * @param $groupToJoin string|XN_Content|W_Content  (optional) the Group object (or its URL) to make the user a member of
     * @param string emailAddress (optional) initial value for the Email Address field, or null to use that of the current user (if any)
     * @return string  the URL
     */
    public static function signUpUrl($target = null, $groupToJoin = null, $emailAddress = null) {
        if ($groupToJoin instanceof XN_Content || $groupToJoin instanceof W_Content) { $groupToJoin = $groupToJoin->my->url; }
        if (! $target) { $target = XG_HttpHelper::currentUrl(); }
        $target = self::updateErrorTarget($target);
        if ($target == xg_absolute_url('/')) { $target = NULL; }
        return W_Cache::getWidget('main')->buildUrl('authorization', 'signUp', array('target' => $target, 'groupToJoin' => $groupToJoin, 'emailAddress' => $emailAddress));
    }

    /**
     * Returns the URL for the sign-up-Ning-user page.
     *
     * @param $target string  the URL to go to after sign-up, or null for the current page
     * @param $groupToJoin string|XN_Content|W_Content  (optional) the Group object (or its URL) to make the user a member of
     * @param string emailAddress (optional) initial value for the Email Address field, or null to use that of the current user (if any)
     * @return string  the URL
     */
    public static function signUpNingUserUrl($target = null, $groupToJoin = null, $emailAddress = null) {
        if ($groupToJoin instanceof XN_Content || $groupToJoin instanceof W_Content) { $groupToJoin = $groupToJoin->my->url; }
        if (! $target) { $target = XG_HttpHelper::currentUrl(); }
        return W_Cache::getWidget('main')->buildUrl('authorization', 'signUpNingUser', array('target' => self::updateErrorTarget($target), 'groupToJoin' => $groupToJoin, 'emailAddress' => $emailAddress));
    }

    /**
     * Returns the URL for signing out.
     *
     * @param $target string  the URL to go to after sign-out, or null for the homepage
     * @return string  the URL
     */
    public static function signOutUrl($target = null) {
        if (! $target) { $target = xg_absolute_url('/'); }
        return XG_SecurityHelper::addCsrfToken(W_Cache::getWidget('main')->buildUrl('authorization', 'signOut', array('target' => $target)));
    }

    /**
     * Returns whether the email-password combination is valid.
     *
     * @param $screenNameOrEmail string  a Ning username or email address
     * @param $password string  a password to check
     * @return mixed  true if valid; otherwise an array of error messages keyed by numeric error codes
     */
    public static function verifyPassword($screenNameOrEmail, $password) {
        return XN_Profile::signIn($screenNameOrEmail, $password, array('set-cookies' => false));
    }

    /**
     * Redirects to the PIN-verification form if necessary.
     */
    public static function redirectIfPinRequired() {
      if (XN_Profile::current()->loginIsVerified() != XN_Profile::IS_VERIFIED && XG_SecurityHelper::userIsAdmin() && !XG_App::pinOptional(XG_App::getRequestedRoute())) {
          $url = XN_Profile::verificationUrl(XG_HttpHelper::currentUrl());
          header('Location: '.$url, true, 302);
          exit;
        }
    }

    /**
     * Updates the $target parameter in cases where it's a 404 action in the error controller
     *
     * @param $target string the target parameter in the url
     * @return $target string  the target parameter, modified if necessary
     */
     private function updateErrorTarget($target) {
         if (mb_strpos($target, 'main/error/404') !== false) {
             return xg_absolute_url('/');
         }
         return $target;
     }

}
