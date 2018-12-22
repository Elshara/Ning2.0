<?php

XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');

/**
 * These functions return a security violation message, or null if there is no security violation.
 */
class Video_SecurityHelper {

    private static function assertIsXnProfile($curUser) {
        if (! ($curUser instanceof XN_Profile)) { throw new Exception('$curUser must be an XN_Profile'); }
    }

    public static function isApprovalRequired() {
        return XG_App::contentIsModerated();
    }

    /**
     * Returns true if the provided $video is viewable by the provided $user
     *
     * @param XN_Content $video Photo to check
     * @param XN_Content $user User to check
     * @param bool $isOnProfile true if on user's profile
     *
     * @todo write proper unit tests once checkVisibleToCurrentUser() can be
     *       refactored into something more testable
     */
    public static function isViewableOnLatestActivity($video, $user, $isOnProfile) {
        return ($isOnProfile && !$error = self::checkVisibleToCurrentUser($user, $video)) ||
               (!$isOnProfile && $video->my->visibility == 'all');
    }

    public static function userCanDeleteComment($user,$comment) {
        return XG_SecurityHelper::userIsAdminOrContributor($user,$comment);
    }


    public static function checkCurrentUserIsSignedIn($curUser) {
        self::assertIsXnProfile($curUser);
        if (!$curUser->isLoggedIn()) {
            return array('title'       => xg_text('HOWDY_STRANGER'),
                         'subtitle'    => xg_text('YOU_NEED_TO_BE_SIGNED_IN'),
                         'description' => xg_text('JUST_CLICK_ON_SIGN_IN'));
        } else {
            return null;
        }
    }



    public static function checkCurrentUserContributedOrIsAdmin($curUser, $content) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsAdmin($curUser)) &&
            self::failed(self::checkCurrentUserContributed($curUser, $content))) {
            return self::checkCurrentUserContributed($curUser, $content);
        } else {
            return null;
        }
    }



    public static function checkCurrentUserContributed($curUser, $content) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) {
            return self::checkCurrentUserIsSignedIn($curUser);
        } else {
            return self::checkCurrentUserIs($curUser, $content->contributorName);
        }
    }


    /**
     * Checks if the current user can add videos
     *
     * @param XN_Profile $curUser
     * @return string  null if the permissions are correct, and a string with the error message otherwise
     */
    public static function checkCurrentUserCanAddVideos($curUser) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) {
            return self::checkCurrentUserIsSignedIn($curUser);
        }
        $widget = W_Cache::current('W_Widget');
        switch ($widget->config['addVideoPermission']) {
            case 'me':
                return self::checkCurrentUserIsAdmin($curUser);
            case 'friends':
                return self::checkCurrentUserIsAdminOrFriendOf($curUser, XN_Application::load()->ownerName);
            case 'all':
                return null;
            default:
                throw new Exception("Shouldn't get here");
        }
    }



    private static function checkCurrentUserIs($curUser, $screenName) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) {
            return self::checkCurrentUserIsSignedIn($curUser);
        }
        if ($curUser->screenName != $screenName) {
            W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_FullNameHelper.php');
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('YOU_NEED_TO_BE_X', Video_FullNameHelper::fullName($screenName)));
        }
        return null;
    }



    public static function checkCurrentUserIsAdmin($curUser) {
        self::assertIsXnProfile($curUser);
        if (XG_SecurityHelper::userIsAdmin($curUser)) { return null; }
        return self::checkCurrentUserIs($curUser, XN_Application::load()->ownerName);
    }

    public static function failed($failureMessage) {
        return $failureMessage != null;
    }

    public static function passed($failureMessage) {
        return ! self::failed($failureMessage);
    }

    public static function reportFailure($failureMessage) {
        if (self::failed($failureMessage)) {
            throw new Exception($failureMessage);
        }
        return self::failed($failureMessage);
    }



    public static function addVisibilityFilter($curUser, $query) {
        self::assertIsXnProfile($curUser);
        if (self::checkCurrentUserIsAdmin($curUser) == null) {
            return;
        }
        if ($curUser->isLoggedIn()) {
            $query->filter(XN_Filter::any(
                    XN_Filter('my.visibility','=','all'),
                    XN_Filter::all(XN_Filter('my.visibility','=','friends'),
                                   XN_Filter('contributor', 'in', XN_Query::FRIENDS())),
                    XN_Filter::all(XN_Filter('my.visibility','in',array('me', 'friends')),
                                   XN_Filter('contributorName', '=', $curUser->screenName))));
        } else {
            $query->filter('my.visibility', '=', 'all');
        }
    }


    public static function addApprovedFilter($curUser, $query) {
        self::assertIsXnProfile($curUser);
        if (Video_SecurityHelper::isApprovalRequired()) {
            // Apply the filter even if the person is the app owner or contributor,
            // as non-approved videos should not appear in All Videos, the homepage, etc.  [Jon Aquino 2006-08-05]
            $query->filter('my.approved', '=', 'Y');
        }
    }

    public static function addConversionCompleteFilter($query) {
        $query->filter(XN_Filter::any(
                XN_Filter('my.conversionStatus', '=', null),
                XN_Filter('my.conversionStatus', '=', 'complete')));
    }



    public static function checkVisibleToCurrentUser($curUser, $video) {
        self::assertIsXnProfile($curUser);
        return self::checkVisibleToCurrentUserProper($curUser, $video, $video->my->visibility);
    }

    public static function checkCurrentUserCanComment($curUser, $video) {
        self::assertIsXnProfile($curUser);
        $msg = self::checkCurrentUserIsSignedIn($curUser);
        if ($msg) { return $msg; }
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Video_UserHelper.php');
        $owner = Video_UserHelper::load($video->contributorName);
        switch (Video_UserHelper::get($owner, 'addCommentPermission')) {
            case 'all':
                return null;
            case 'me':
                return self::checkCurrentUserContributed($curUser, $video);
            case 'friends':
                //  Allow owner to comment (BAZ-2798)
                $msg = self::checkCurrentUserContributed($curUser, $video);
                if (self::passed($msg)) {
                    return $msg;
                }
                else {
                    return self::checkCurrentUserIsFriendOf($curUser, $video->contributorName);
                }
            default:
                throw new Exception('Shouldn\'t get here');
        }
    }

    public static function checkVisibleToCurrentUserProper($curUser, $video, $visibility) {
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        if (self::checkCurrentUserIsAdmin($curUser) == null) {
            return null;
        }
        if (self::checkCurrentUserContributed($curUser, $video) == null) {
            return null;
        }
        if (Video_VideoHelper::isAwaitingApproval($video)) {
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('THAT_VIDEO_IS_AWAITING_APPROVAL'));
        }
        switch ($visibility) {
            case 'all':
                return null;
            case 'me':
                return self::checkCurrentUserContributedOrIsAdmin($curUser, $video);
            case 'friends':
                return self::checkCurrentUserIsAdminOrFriendOf($curUser, $video->contributorName);
            default:
                throw new Exception('Shouldn\'t get here');
        }
    }

    public static function isEmbeddable($video) {
        return $video->my->visibility == 'all';
    }

    private static function checkCurrentUserIsAdminOrFriendOf($curUser, $screenName) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) { return self::checkCurrentUserIsSignedIn($curUser); }
        if (self::passed(self::checkCurrentUserIsAdmin($curUser))) { return null; }
        return self::checkCurrentUserIsFriendOf($curUser, $screenName);
    }

    private static function checkCurrentUserIsFriendOf($curUser, $screenName) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) { return self::checkCurrentUserIsSignedIn($curUser); }
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_UserHelper.php');
        if (Video_UserHelper::isFriend($curUser, $screenName)) { return null; }
        return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                     'subtitle'    => '',
                     'description' => xg_text('YOU_NEED_TO_BE_A_FRIEND', Video_FullNameHelper::fullName($screenName)));
    }

    public static function checkConversionComplete($video) {
        if ($video->my->conversionStatus != null && $video->my->conversionStatus != 'complete') {
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('THIS_VIDEO_IS_BEING_PROCESSED'));
        }
        return null;
    }

    /** @deprecated 2.0  Use XG_SecurityHelper::redirectIfNotMember instead */
    public static function redirectToSignInPageIfSignedOut($target = null) {
        XG_SecurityHelper::redirectToSignUpPageIfSignedOut($target);
    }

    public static function signInUrl($target = null) {
        return XG_HttpHelper::signUpUrl($target);
    }

    private static function randomString($length){
       $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
       for (; mb_strlen($randomString) < $length;){
           $randomString .= $characters[mt_rand(0, count($characters)-1)];
       }
       return $randomString;
    }

    public static function embeddableAccessCode() {
        $widget = W_Cache::getWidget('video');
        if (! $widget->privateConfig['embeddableAccessCode']) {
            $widget->privateConfig['embeddableAccessCode'] = self::randomString(32);
            $widget->saveConfig();
        }
        return $widget->privateConfig['embeddableAccessCode'];
    }

    public static function canAccessEmbeddableData($urlParameters) {
        return Video_PrivacyHelper::getPrivacyType() == 'public' || $urlParameters['x'] == self::embeddableAccessCode();
    }
}
