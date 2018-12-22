<?php
/**
 * Useful functions for working with sign-in and sign-up
 *
 * @see Index_AuthorizationHelperTest
 */
class Index_AuthorizationHelper {

    const APPLICATION_TOS_ABOUT_APPFS = 'xn-app://about/thirdpartyapptos_content.php';
    const DEVELOPER_TOS_ABOUT_APPFS = 'xn-app://about/thirdpartyappdevtos_content.php';

    /**
     * Returns the page to forward or redirect to.
     *
     * @param $formAction string  name of the action for the original form: signIn, signUp, signUpNingUser
     * @param $emailAddress string  the email address entered by the user
     * @param $target string  URL for the page to land on eventually, or null to go to the homepage
     * @param $groupToJoin string - URL of the group to make the user a member of
     * @param $errors array  HTML error messages, optionally keyed by field name, or null if no errors occurred
     * @param $signUpAllowed boolean  whether the user can access the sign-up page
     * @param $isNingUser boolean  whether the current user has a Ning account
     * @param $isPending boolean  whether the current user's network membership is pending
     * @param $isMember boolean  whether the current user is a member of the network
     * @return array  ["forward", action, errors] or ["redirect", action, parameters]
     *
     * @see Diagram of flows at "Basic Flows - Phase One Tasks - Bazel Week 2" in Confluence
     */
    public static function nextAction($args) {
        extract($args);
        if (! $target) { $args['target'] = xg_absolute_url('/'); }
        if (! $errors) { return self::nextActionOnSuccess($args); }
        return self::nextActionOnError($args);
    }

    /**
     * Returns the page to forward or redirect to when a server-side validation error occurs.
     *
     * @param $formAction string  name of the action for the original form: signIn, signUp, signUpNingUser
     * @param $emailAddress string  the email address entered by the user
     * @param $target string  URL for the page to land on eventually
     * @param $groupToJoin string - URL of the group to make the user a member of
     * @param $errors array  HTML error messages, optionally keyed by field name, or null if no errors occurred
     * @param $signUpAllowed boolean  whether the user can access the sign-up page
     * @param $isNingUser boolean  whether the current user has a Ning account
     * @param $isPending boolean  whether the current user's network membership is pending
     * @param $isMember boolean  whether the current user is a regular or pending member of the network
     * @return array  ["forward", action, errors] or ["redirect", action, parameters]
     */
    private static function nextActionOnError($args) {
        extract($args);
        switch (XG_Browser::current()->action($formAction)) {
            case XG_Browser::current()->action('signIn'):
                if ($isNingUser && ! $isMember && ! $isPending && $signUpAllowed && !(XG_Browser::current() instanceof XG_Browser_Iphone)) { return array('redirect', 'signUpNingUser', array('target' => $target, 'groupToJoin' => $groupToJoin, 'emailAddress' => $emailAddress)); }
                return array('forward', 'signIn', $errors);
            case XG_Browser::current()->action('signUp'):
				if ($isNingUser && ! $isMember && ! $isPending && !(XG_Browser::current() instanceof XG_Browser_Iphone)) { return array('redirect', 'signUpNingUser', array('target' => $target, 'groupToJoin' => $groupToJoin, 'emailAddress' => $emailAddress)); }
                return array('forward', 'signUp', $errors);
            case XG_Browser::current()->action('signUpNingUser'):
                if ($isNingUser && ! $isMember && ! $isPending) { return array('forward', 'signUpNingUser', $errors); }
                return array('forward', 'signUpNingUser', $errors);
            case XG_Browser::current()->action('editPassword'):
                return array('forward', 'editPassword', $errors);
            default:
                throw new Exception('Assertion failed (1475768786) - ' . $formAction);
        }
    }

    /**
     * Returns the page to redirect to when server-side validation errors occur.
     *
     * @param $formAction string  name of the action for the original form: signIn, signUp, signUpNingUser
     * @param $emailAddress string  the email address entered by the user
     * @param $target string  URL for the page to land on eventually
     * @param $groupToJoin string - URL of the group to make the user a member of
     * @param $signUpAllowed boolean  whether the user can access the sign-up page
     * @param $isPending boolean  whether the current user's network membership is pending
     * @param $isMember boolean  whether the current user is a member of the network
     * @return array  ["redirect", action, parameters]
     */
    private static function nextActionOnSuccess($args) {
        extract($args);
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $invitationHelperClass = $args['invitationHelperClass'] ? $args['invitationHelperClass'] : 'Index_InvitationHelper';
        $hasInvitation = call_user_func(array($invitationHelperClass, 'getUnusedInvitation'), $target);
        if (! $isMember && ! $isPending && ! $signUpAllowed && ! $hasInvitation) {
            return array('redirect', 'invitationOnly', null);
        }
        $targetParts = parse_url($target);
        $targetParameters = array();
        if (isset($targetParts['query'])) { parse_str($targetParts['query'], $targetParameters); }
        if (! $isMember && ! $isPending) {
            $parameters = array('target' => $target, 'groupToJoin' => $groupToJoin);
            if ($targetParameters[Index_InvitationHelper::KEY]) { $parameters[Index_InvitationHelper::KEY] = $targetParameters[Index_InvitationHelper::KEY]; }
            if ($targetParameters[Index_InvitationHelper::SIGN_IN_CHECK_DONE]) { $parameters[Index_InvitationHelper::SIGN_IN_CHECK_DONE] = $targetParameters[Index_InvitationHelper::SIGN_IN_CHECK_DONE]; }
            if ($targetParameters[Index_InvitationHelper::SHARING]) { $parameters[Index_InvitationHelper::SHARING] = $targetParameters[Index_InvitationHelper::SHARING]; }
            if ($formAction == 'signUp') { $parameters['newNingUser'] = '1'; }
            return array('redirect', 'newProfile', $parameters);
        }
        if (isset($targetParameters['requestInv'])) {
            return array('redirect', $target, array('requestInv' => true));
        }
        return array('redirect', $target, null);
    }

    /**
     * Returns the error message corresponding to the given error code.
     *
     * @param $errorCode string  error code returned by an XN_Profile function
     * @param $formAction string  (optional) name of the action for the original form, e.g., signIn
     * @param $userHasInvitation boolean  (optional) whether an invitation was detected
     * @param $target
     * @param $groupToJoin
     * @param $emailAddress string  the supplied email address which resulted in an error during sign in/up
     * @return array  a 1-element array: an HTML error message, possibly keyed by field name
     * @see Profile REST API in Clearspace
     */
    public static function errorMessage($errorCode, $formAction = null, $userHasInvitation = false, $target = null, $groupToJoin = null, $emailAddress = null) {
        if (! $target) { $target = xg_absolute_url('/'); }
        switch ($errorCode) {
            case 'unknown': $result = array(xg_html('OOPS_THAT')); break;
            // Note that the auth:1 message (and other messages) may be modified below. [Jon Aquino 2008-06-11]
            case 'auth:1': $result = array('emailAddress' => xg_html('NO_ACCOUNT_WITH_EMAIL_X', xnhtmlentities($emailAddress))); break;
            case 'auth:2': $result = array('password' => xg_html('INVALID_PASSWORD_CHECK_CAPS_LOCK')); break;
            case 'auth:3': $result = array('captchaValue' => xg_html('PLEASE_ENTER_CODE')); break;
            case 'profile:7': $result = array('emailAddress' => xg_html('EMAIL_ALREADY_REGISTERED')); break;
            case 'profile:8': $result = array('emailAddress' => xg_html('PLEASE_ENTER_EMAIL_ADDRESS')); break;
            case 'profile:9': $result = array('emailAddress' => xg_html('EMAIL_NOT_VALID')); break;
            case 'profile:10': $result = array('password' => xg_html('PLEASE_ENTER_PASSWORD')); break;
            case 'profile:11': $result = array('password' => xg_html('CHOOSE_SHORTER_PASSWORD')); break;
            case 'profile:14': $result = array('fullName' => xg_html('CHOOSE_SHORTER_NAME')); break;
            case 'profile:15': $result = array('location' => xg_html('ENTER_SHORTER_CITY_NAME')); break;
            default: $result = array(xg_html('OOPS_THAT_DID_NOT_WORK_ERROR_CODE', $errorCode)); break;
        }
        if ($formAction == 'editEmailAddress' && $errorCode == 'auth:6') {
            $result[key($result)] = xg_html('SORRY_NCS_CANNOT_CHANGE_EMAIL');
        }
        if ($formAction == 'editPassword' && $errorCode == 'auth:6') {
            // Called from two places: authorization/editPassword and settings/editPassword [Jon Aquino 2007-10-02]
            $result[key($result)] = xg_html('SORRY_CHANGE_PASSWORD_VIA_OWN_NETWORK_OR_PROFILE', 'href="http://' .  XN_AtomHelper::HOST_APP('www') . '/main/profile/editPassword" target="_blank"');
        }
        if ($formAction == 'requestPasswordReset' && $errorCode == 'auth:6') {
			$result[key($result)] = xg_html('SORRY_CHANGE_PASSWORD_VIA_OWN_NETWORK_OR_PROFILE', 'href="http://' .  XN_AtomHelper::HOST_APP('www') . '/main/profile/requestPasswordReset" target="_blank"');
        }
        if ($formAction == 'signIn' && $userHasInvitation) {
            $result[key($result)] .= '<br />' . xg_html('CREATE_ACCOUNT_OR_SIGN_IN', 'href="' . xnhtmlentities(XG_AuthorizationHelper::signUpUrl($target, $groupToJoin)) . '"');
        } elseif ($formAction == 'signUpNingUser' && $userHasInvitation) {
            $result[key($result)] .= '<br />' . xg_html('CREATE_ACCOUNT_OR_JOIN', 'href="' . xnhtmlentities(XG_AuthorizationHelper::signUpUrl($target, $groupToJoin)) . '"');
        } elseif ($errorCode == 'auth:1' && XG_App::appIsPrivate() && ! XG_App::allowJoinByAll()) {
            $result[key($result)] .= '<br />' . xg_html('SIGN_IN_BELOW_OR_USE_INVITE_LINK');
        } elseif ($errorCode == 'auth:1') {
            $result[key($result)] .= '<br />' . xg_html('CLICK_HERE_TO_REGISTER', 'href="' . xnhtmlentities(XG_AuthorizationHelper::signUpUrl($target, $groupToJoin)) . '"');
        }
        return $result;
    }

    /**
     * Returns the HTML for the Ning Privacy Policy.
     *
     * @param $previousUrl  optional target for the Back link
     * @return string  the HTML with links adjusted for the network
     */
    public static function privacyPolicyHtml($previousUrl) {
        return self::policyHtml('xn-app://about/privacy_content.php', $previousUrl);
    }

    /**
     * Returns the HTML for the Ning Terms of Service.
     *
     * @param $previousUrl  optional target for the Back link
     * @return string  the HTML with links adjusted for the network
     */
    public static function termsOfServiceHtml($previousUrl) {
        return self::policyHtml('xn-app://about/tos_content.php', $previousUrl);
    }

    /**
     * Returns the HTML for the Ning Terms of Service (for Opensocial applications).
     *
     * @param $previousUrl  optional target for the Back link
     * @return string  the HTML with links adjusted for the network
     */
    public static function appTermsOfServiceHtml($previousUrl) {
        return self::policyHtml(self::APPLICATION_TOS_ABOUT_APPFS, $previousUrl);
    }
    
    /**
     * Returns the HTML for the Ning Terms of Service (for third party developers).
     *
     * @param $previousUrl  optional target for the Back link
     * @return string  the HTML with links adjusted for the network
     */
    public static function devTermsOfServiceHtml($previousUrl) {
        return self::policyHtml(self::DEVELOPER_TOS_ABOUT_APPFS, $previousUrl);
    }


    /**
     * Returns the HTML for the specified policy document.
     *
     * @param $path string  location of the original HTML
     * @param $previousUrl  optional target for the Back link
     * @return string  the HTML with links adjusted for the network
     */
    private static function policyHtml($path, $previousUrl) {
        ob_start();
        include($path); /** @allowed */
        $html = trim(ob_get_contents());
        ob_end_clean();
        return self::adjustPolicyLinks($html, $previousUrl);
    }

    /**
     * Adjusts the links in the policy document to work on the current network.
     *
     * @param $path string  location of the original HTML
     * @param $previousUrl  optional target for the Back link
     * @return string  the HTML with links adjusted for the network
     */
    protected static function adjustPolicyLinks($html, $previousUrl) {
        return preg_replace(
            array(
                '@"[^"]*/privacy.php"@u',
                '@"[^"]*/tos.php"@u',
                '@"[^"]*/dmca-notice.php"@u',
                '@"[^"]*/thirdpartyappdevtos.php"@u',
                '@"[^"]*/thirdpartyapptos.php"@u',
            ), array(
                '"' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'privacyPolicy', array('previousUrl' => $previousUrl))) . '"',
                '"' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'termsOfService', array('previousUrl' => $previousUrl))) . '"',
                '"http://about.ning.com/dmca-notice.php"',
                '"' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'developerTos', array('previousUrl' => $previousUrl))) . '"',
                '"' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'applicationTos', array('previousUrl' => $previousUrl))) . '"',
            ), $html);
    }

}
