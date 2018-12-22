<?php

/**
 * Contains utility functions for dealing with security and privacy issues.
 */
class Photo_SecurityHelper {
    public static function assertIsXnProfile($curUser) {
        if (!($curUser instanceof XN_Profile)) {
            throw new Exception('$curUser must be an XN_Profile');
        }
    }

    public static function isApprovalRequired() {
        return XG_App::contentIsModerated();
    }

    /**
     * Returns true if the provided $photo is viewable by the provided $user
     *
     * @param XN_Content $photo Photo to check
     * @param XN_Content $user User to check
     * @param bool $isOnProfile true if on user's profile
     *
     * @todo write proper unit tests once checkVisibleToCurrentUser() can be
     *       refactored into something more testable
     */
    public static function isViewableOnLatestActivity($photo, $user, $isOnProfile) {
        return ($isOnProfile && !$error = self::checkVisibleToCurrentUser($user, $photo)) ||
               (!$isOnProfile && $photo->my->visibility == 'all');
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

    public static function checkCurrentUserCanDeleteComment($curUser, $comment, $photo) {
        self::assertIsXnProfile($curUser);
        if(self::failed(self::checkCurrentUserIsAdmin($curUser)) == null ||
           self::failed(self::checkCurrentUserContributed($curUser, $comment)) == null ||
           self::failed(self::checkCurrentUserContributed($curUser, $photo)) == null) {
               return null;
           } else {
               return true;
           }
    }

    public static function checkCurrentUserCanDeletePhoto($curUser, $photo) {
        self::assertIsXnProfile($curUser);
        if(self::failed(self::checkCurrentUserIsAdmin($curUser)) == null ||
           self::failed(self::checkCurrentUserContributed($curUser, $photo)) == null) {
               return null;
           } else {
               return true;
           }
    }

    /**
     * Returns whether the current user is allowed to delete the album.
     *
     * @param $topic  XN_Content|W_Content  The Album object
     * @return boolean  Whether permission is granted
     */
    public static function checkCurrentUserCanDeleteAlbum($curUser, $album) {
        return self::checkCurrentUserContributedOrIsAdmin($curUser, $album);
    }


    /**
     * Returns whether the current user is allowed to edit the album.
     *
     * @param $topic  XN_Content|W_Content  The Album object
     * @return boolean  Whether permission is granted
     */
    public static function checkCurrentUserCanEditAlbum($curUser, $album) {
        return self::checkCurrentUserContributed($curUser, $album);
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
     * Checks if the current user can add photos
     *
     * @param XN_Profile $curUser
     * @return string  null if the permissions are correct, and a string with an error message otherwise
     */
    public static function checkCurrentUserCanAddPhotos($curUser) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) {
            return self::checkCurrentUserIsSignedIn($curUser);
        }
        $widget = W_Cache::current('W_Widget');
        switch ($widget->config['addPhotoPermission']) {
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
            W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_FullNameHelper.php');
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('YOU_NEED_TO_BE_X', Photo_FullNameHelper::fullName($screenName)));
        }
        return null;
    }

    public static function checkCurrentUserIsAdmin($curUser) {
        self::assertIsXnProfile($curUser);
        if (!XG_SecurityHelper::userIsAdmin($curUser)) {
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('YOU_NEED_TO_BE_OWNER'));
        } else {
            return null;
        }
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

    /** Singleton instance of this class */
    protected static $instance;

    /**
     *  Returns the singleton instance of this class.
     *
     *  @return Events_BulkHelper   the BulkHelper, or a mock object for testing
     */
    private function instance() {
        if (! self::$instance) { self::$instance = new Photo_SecurityHelper(); }
        return self::$instance;
    }

    public static function addVisibilityFilter($curUser, $query) {
        self::instance()->assertIsXnProfile($curUser);
        if (self::instance()->checkCurrentUserIsAdmin($curUser) == null) {
            return;
        }
        if ($curUser->isLoggedIn() && W_Cache::getWidget('photo')->config['friendsQueriesEnabled'] == 'N') {
            // Because the friends filter is implemented as a remote join,
            // it can cause heavy server load on very active networks like thisis50.com.
            // Thus, we provide a way to turn off the friends filter (BAZ-7099). [Jon Aquino 2008-04-04]
            $query->filter(XN_Filter::any(
                    XN_Filter('my.visibility','=','all'),
                    XN_Filter::all(XN_Filter('my.visibility','in',array('me', 'friends')),
                                   XN_Filter('contributorName', '=', $curUser->screenName))));
        } elseif ($curUser->isLoggedIn()) {
            $query->filter(XN_Filter::any(
                    XN_Filter('my.visibility','=','all'),
                    XN_Filter::all(XN_Filter('my.visibility','=','friends'),
                                   XN_Filter('contributor', 'in', XN_Query::FRIENDS())),
                    XN_Filter::all(XN_Filter('my.visibility','in',array('me', 'friends')),
                                   XN_Filter('contributorName', '=', $curUser->screenName))));
        } else {
            $query->filter('my.visibility', '=', 'all');
        }
        return $query;
    }

    public static function addApprovedFilter($curUser, $query) {
        self::assertIsXnProfile($curUser);
        if (self::isApprovalRequired()) {
            // Apply the filter even if the person is the app owner or contributor,
            // as non-approved photos should not appear in All Photos, the homepage, etc.  [Jon Aquino 2006-08-05]
            $query->filter('my.approved', '=', 'Y');
        }
        return $query;
    }

    public static function checkVisibleToCurrentUser($curUser, $photo) {
        self::assertIsXnProfile($curUser);
        return self::checkVisibleToCurrentUserProper($curUser, $photo, $photo->my->visibility);
    }

    public static function checkCurrentUserCanComment($curUser, $attachedTo) {
        self::assertIsXnProfile($curUser);
        $msg = self::checkCurrentUserIsSignedIn($curUser);
        if ($msg) { return $msg; }
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $owner = Photo_UserHelper::load($attachedTo->contributorName);
        switch (Photo_UserHelper::get($owner, 'addCommentPermission')) {
            case 'all':
                return null;
            case 'me':
                return self::checkCurrentUserContributed($curUser, $attachedTo);
            case 'friends':
                //  Allow owner to comment (BAZ-2798)
                $msg = self::checkCurrentUserContributed($curUser, $attachedTo);
                if (self::passed($msg)) {
                    return $msg;
                }
                else {
                    return self::checkCurrentUserIsFriendOf($curUser, $attachedTo->contributorName);
                }
            default:
                throw new Exception('Shouldn\'t get here');
        }
    }

    public static function checkVisibleToCurrentUserProper($curUser, $photo, $visibility) {
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        if (self::checkCurrentUserIsAdmin($curUser) == null) {
            return null;
        }
        if (self::checkCurrentUserContributed($curUser, $photo) == null) {
            return null;
        }
        if (Photo_PhotoHelper::isAwaitingApproval($photo)) {
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('THAT_PHOTO_IS_AWAITING_APPROVAL'));
        }
        switch ($visibility) {
            case 'all':
                return null;
            case 'me':
                return self::checkCurrentUserContributedOrIsAdmin($curUser, $photo);
            case 'friends':
                return self::checkCurrentUserIsAdminOrFriendOf($curUser, $photo->contributorName);
            default:
                throw new Exception('Shouldn\'t get here');
        }
    }

    public static function isEmbeddable($photo) {
        return $photo->my->visibility == 'all';
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
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        if (Photo_UserHelper::isFriend($curUser, $screenName)) { return null; }
        return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                     'subtitle'    => '',
                     'description' => xg_text('YOU_NEED_TO_BE_A_FRIEND', Photo_FullNameHelper::fullName($screenName)));
    }

    /** @deprecated 2.0  Use XG_SecurityHelper::redirectIfNotMember instead */
    public static function redirectToSignInPageIfSignedOut($target = null) {
        if (XN_Profile::current()->isLoggedIn()) {
            return;
        }
        header('Location: ' . self::signInUrl($target));
        die;
    }

    public static function signInUrl($target = null) {
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
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
        $widget = W_Cache::getWidget('photo');
        if (! $widget->privateConfig['embeddableAccessCode']) {
            $widget->privateConfig['embeddableAccessCode'] = self::randomString(32);
            $widget->saveConfig();
        }
        return $widget->privateConfig['embeddableAccessCode'];
    }

    public static function canAccessEmbeddableData($urlParameters) {
        return Photo_PrivacyHelper::getPrivacyType() == 'public' || $urlParameters['x'] == self::embeddableAccessCode();
    }
}
