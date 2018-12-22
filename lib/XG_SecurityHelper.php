<?php

XG_App::includeFileOnce('/lib/XG_HttpHelper.php');

/**
 * Useful functions for authorizing access to pages and other resources.
 */
class XG_SecurityHelper {


    public static function assertIsXnProfile($curUser) {
        if (!($curUser instanceof XN_Profile)) {
            throw new Exception('$curUser must be an XN_Profile');
        }
    }

    /** @deprecated  Favor boolean checks over old-style checks that return arrays. */
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

    /** @deprecated  Favor boolean checks over old-style checks that return arrays. */
    public static function checkCurrentUserIsAppOwner($curUser) {
        self::assertIsXnProfile($curUser);
        if (! $curUser->isOwner()) {
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('YOU_NEED_TO_BE_OWNER'));
        } else {
            return null;
        }
    }

    /** @deprecated  Favor boolean checks over old-style checks that return arrays. */
    private static function checkCurrentUserIs($curUser, $screenName) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) {
            return self::checkCurrentUserIsSignedIn($curUser);
        }
        if ($curUser->screenName != $screenName) {
            XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('YOU_NEED_TO_BE_A_FRIEND', XG_FullNameHelper::fullName($screenName)));
        }
        return null;
    }

    /** @deprecated  Favor boolean checks over old-style checks that return arrays. */
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

    /**
     * Add an appropriate set of filters to a query to respect in-app visibility
     * rules (e.g. "Only my friends can see this photo". By default this method
     * thinks it's dealing with a Content query, but with $isSearch == true, it
     * will add appropriate filters for a Search query.
     *
     * @param $curUser XN_Profile The current user
     * @param $query XN_Query The query to modify
     * @param $isSearch boolean Is this query a Search query?
     */
    public static function addVisibilityFilter($curUser, $query, $isSearch = false) {
        self::assertIsXnProfile($curUser);
        /* Admin users can see everything, so no need to add a filter */
        if (self::checkCurrentUserIsAdmin($curUser) == null) {
            return;
        }
        if ($isSearch) {
            /* The search endpoint doesn't know anything about friends */
            $query->filter('my.visibility','!like','friends');
            if ($curUser->isLoggedIn()) {
                $query->filter(XN_Filter::any(XN_Filter('my.visibility','!like', 'me'),
                                              XN_Filter::all(XN_Filter('my.visibility','like','me'),
                                                             XN_Filter('contributorName','like',$curUser->screenName))));

            }
            else {
                $query->filter('my.visibility','!like','me');
            }
        }
        else {
            if ($curUser->isLoggedIn()) {
                $query->filter(XN_Filter::any(
                        XN_Filter('my.visibility','=',null),
                        XN_Filter('my.visibility','=','all'),
                        XN_Filter::all(XN_Filter('my.visibility','=','friends'),
                                       XN_Filter('contributor', 'in', XN_Query::FRIENDS())),
                        XN_Filter::all(XN_Filter('my.visibility','in',array('me', 'friends')),
                                       XN_Filter('contributorName', '=', $curUser->screenName))));
            } else {
                $query->filter(XN_Filter::any(
                        XN_Filter('my.visibility','=',null),
                        XN_Filter('my.visibility','=','all')
                         ));
            }
        }
    }

    public static function isApprovalRequired() {
        return XG_App::contentIsModerated();
    }

    public static function addApprovedFilter($curUser, $query) {
        self::assertIsXnProfile($curUser);
        if (self::isApprovalRequired()) {
            // Apply the filter even if the person is the app owner or contributor,
            // as non-approved photos should not appear in All Photos, the homepage, etc.  [Jon Aquino 2006-08-05]
            $query->filter('my.approved', '=', 'Y');
        }
    }

    public static function failed($failureMessage) {
        return $failureMessage != null;
    }

    public static function passed($failureMessage) {
        return ! self::failed($failureMessage);
    }

    /** @deprecated  Favor boolean checks over old-style checks that return arrays. */
    private static function checkCurrentUserIsFriendOf($curUser, $screenName) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) { return self::checkCurrentUserIsSignedIn($curUser); }
        XG_App::includeFileOnce('/lib/XG_UserHelper.php');
        if (XG_UserHelper::isFriend($curUser, $screenName)) { return null; }
        XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
        return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                     'subtitle'    => '',
                     'description' => xg_text('YOU_NEED_TO_BE_A_FRIEND', XG_FullNameHelper::fullName($screenName)));
    }

    /** @deprecated  Favor boolean checks over old-style checks that return arrays. */
    public static function checkCurrentUserIsAppOwnerOrFriendOf($curUser, $screenName) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) { return self::checkCurrentUserIsSignedIn($curUser); }
        if (self::passed(self::checkCurrentUserIsAppOwner($curUser))) { return null; }
        return self::checkCurrentUserIsFriendOf($curUser, $screenName);
    }

    /** @deprecated  Favor boolean checks over old-style checks that return arrays. */
    public static function checkCurrentUserContributed($curUser, $content) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) {
            return self::checkCurrentUserIsSignedIn($curUser);
        } else {
            return self::checkCurrentUserIs($curUser, $content->contributorName);
        }
    }

    /** @deprecated  Favor boolean checks over old-style checks that return arrays. */
    public static function checkCurrentUserContributedOrIsAdmin($curUser, $content) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsAdmin($curUser)) &&
            self::failed(self::checkCurrentUserContributed($curUser, $content))) {
            return self::checkCurrentUserContributed($curUser, $content);
        } else {
            return null;
        }
    }


    /**
     * Returns true if a banned user is logged in
     */
    public static function userIsBanned() {
        $profile = XN_Profile::current();
        if (!$profile->isLoggedIn()) { return FALSE; }
        if ($profile->isOwner()) { return FALSE; }

        $user = User::load($profile);
        if (!$user) { return FALSE; }
        return User::isBanned($user);
    }

    /**
     * Returns true if the supplied user is an admin, or if an admin is
     *   currently logged in if no user is supplied
     *
     * @param $profile XN_Profile or NULL (or empty) for current profile
     */
    public static function userIsAdmin($profile = NULL) {
        if (!$profile) {
            $profile = XN_Profile::current();
            if (!$profile->isLoggedIn()) { return FALSE; }
        }
        if ($profile->isOwner()) { return TRUE; }

        $user = User::load($profile);
        if (!$user) { return FALSE; }
        return User::isMember($user) && User::isAdmin($user);
    }

    /**
     * Returns true if the supplied user is the app owner, or if the app owner is
     *   currently logged in if no user is supplied
     *
     * @param $profile XN_Profile or NULL (or empty) for current profile
     */
    public static function userIsOwner($profile = NULL) {
        if (!$profile) {
            $profile = XN_Profile::current();
            if (!$profile->isLoggedIn()) { return FALSE; }
        }
        return $profile->isOwner();
    }

    /**
     * Redirects to the sign-up page if the current user is not logged in.
     */
    public static function redirectToSignUpPageIfSignedOut($target = null) {
        if (XN_Profile::current()->isLoggedIn()) { return; }
        header('Location: ' . XG_HttpHelper::signUpUrl($target));
        exit;
    }

    /**
     * Redirects to the sign-in page if the current user is not logged in.
     */
    public static function redirectToSignInPageIfSignedOut($target = null) {
        if (XN_Profile::current()->isLoggedIn()) { return; }
        header('Location: ' . XG_HttpHelper::signInUrl($target));
        exit;
    }

    /**
     * If a banned user is logged in, redirect to the banned page
     */
    public static function redirectIfBanned() {
        if (self::userIsBanned()) {
			header('Location: ' . XG_Browser::browserUrl('desktop',W_Cache::getWidget('main')->buildUrl('index', 'banned')));
            exit;
        }
    }

    /**
     * If no admin is logged in, redirect to the index page
     */
    public static function redirectIfNotAdmin() {
        self::redirectToSignInPageIfSignedOut();
        if (!self::userIsAdmin()) {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/');
            exit;
        }
    }

    /**
     * If the network creator is not logged in, redirect to the index page
     */
    public static function redirectIfNotOwner() {
        self::redirectToSignInPageIfSignedOut();
        if (!self::userIsOwner()) {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/');
            exit;
        }
    }

    /**
     * If the user is not a member, redirects to the sign-in page, sign-up page,
     * or membership-pending page.
     *
     * @param $target string  URL to go to after sign-in or sign-up, or null to go to the current page.
     * @param $signInInsteadOfSignUp boolean  whether to prefer the sign-in page to the sign-up page
     */
    public static function redirectIfNotMember($target = null, $signInInsteadOfSignUp = false) {
        if (! $target) { $target = XG_HttpHelper::currentUrl(); }
        $curUser = XN_Profile::current();
        $authenticationUrl = $signInInsteadOfSignUp ? XG_AuthorizationHelper::signInUrl($target) : XG_AuthorizationHelper::signUpUrl($target);
        if (! $curUser->isLoggedIn()) {
            // Sign-up, not sign-in. If you arrive at the New Discussion page but aren't signed-in,
            // you should be taken to the sign-up page, not sign-in.  [Jon Aquino 2007-09-21]
            header('Location: ' . $authenticationUrl);
            exit;
        }
        if (User::isPending($curUser)) {
            self::redirectToPendingAndExit();
        }
        if (! User::isMember($curUser)) {
            header('Location: ' . $authenticationUrl);
            exit;
        }
    }

    /**
     * Redirects to the Membership Pending page.
     *
     * Expected GET parameters
     *     target - (optional) URL for the "Continue" link on the Membership Pending page
     */
    public static function redirectToPendingAndExit() {
        $pendingUserWidget = W_Cache::getWidget(XG_App::$pendingUserRoute['widgetName']);
        header('Location: ' . $pendingUserWidget->buildUrl(XG_App::$pendingUserRoute['controllerName'],
                XG_App::$pendingUserRoute['actionName']));
        exit;
    }


    /**
     * User deletion rules in the world of multiple administrators:
     *
     * If you're the app owner, you can delete anyone except yourself
     * If you're an admin, you can delete anyone who is not an admin
     *   or the app owner
     * If you're a regular user, you can only delete yourself
     *
     * @param $user string The user that the current user wants to delete
     */
     public static function currentUserCanDeleteUser($user) {
         $currentUser = XN_Profile::current();
         if (! $currentUser->isLoggedIn()) { return false; }
         $deletingSelf = (strcasecmp($currentUser->screenName, $user) == 0);
         if (self::userIsOwner()) {
             return !$deletingSelf;
         }
         else if ($deletingSelf) {
             return TRUE;
         }
         else if (self::userIsAdmin()) {
             $profile = XG_Cache::profiles($user);
             return $profile && !self::userIsAdmin($profile);
         } else {
             return FALSE;
         }
     }

    /**
     * Returns the User objects for all admins, including the network creator.
     *
     * @return array  User objects for the network creator and any network administrators
     */
    public static function getAdministrators() {
        $adminInfo = User::find(array('admin' => TRUE), 0, 100, 'createdDate', 'desc', TRUE /* cache */);
        return $adminInfo['users'];
    }

     /**
      * Get User objects for all Administrators (NOT including the site owner)
      *
      * @return array Array of XN_Content objects of type User representing
      *   network administrators
      */
    public static function getAdministratorsBesidesOwner() {
        $administratorsBesidesOwner = array();
        foreach(XG_SecurityHelper::getAdministrators() as $administrator) {
            if ($administrator->title == XN_Application::load()->ownerName) { continue; }
            $administratorsBesidesOwner[] = $administrator;
        }
        return $administratorsBesidesOwner;
    }

     /**
      * Returns whether the current user is allowed to send a message to the given user.
      * This function errs on the side of "not allowed"; it is more likely to return true
      * if $friendStatus is specified and if User::retrieveIfLoaded($screenName) is not null.
      *
      * @param $screenName string  the username of the recipient
      * @param $friendStatus string  the relationship (contact, friend, pending, requested, groupie,
      *         blocked, or not-friend), or null if it is not known (or has not been queried, for performance)
      * @return boolean  whether permission is granted
      */
    public static function currentUserCanSendMessageTo($screenName, $friendStatus) {
        if (! User::isMember(XN_Profile::current())) { return false; }
        if (XG_SecurityHelper::userIsAdmin()) { return true; }
        if ($friendStatus === XN_Profile::FRIEND) { return true; }
        if (XN_Application::load()->ownerName == $screenName) { return true; }
        // Outcome of discussion with Diego: Don't bother querying the User object
        // if we don't already have it, to save a query [Jon Aquino 2007-07-26]
        if (! User::retrieveIfLoaded($screenName)) { return false; }
        return User::isAdmin(User::retrieveIfLoaded($screenName));
     }

    /**
     * Returns whether the current user is allowed to subscribe to comments on the current page
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanFollowComments() {
        // Code is similar to that in Forum_SecurityHelper::currentUserCanFollowNewTopics() [Jon Aquino 2007-08-23]
        if (XG_SecurityHelper::userIsAdmin()) { return true; }
        if (! User::isMember(XN_Profile::current())) { return false; }
        if (XG_GroupHelper::inGroupContext() && ! XG_GroupHelper::userIsMember()) { return false; }
        return true;
    }

    /**
     * Returns whether the current user is able to see a link to add content that will appear in the specified embed.
     * Used to determine whether to put an "Add Photo" or similar link on the bottom of module embeds.
     *
     * @param   $embed          XG_Embed    The embed in question.
     * @param   $itemsExist     boolean     Whether the emebd already contains at least one content item.
     * @return                  boolean     true if the user should see the add content link.
     */
    public static function currentUserCanSeeAddContentLink($embed, $itemsExist) {
        $user = XN_Profile::current();
        if (! User::isMember($user)) { return false; }
        if (! $user->isLoggedIn()) { return false; }
        if ($embed->getType() == 'homepage') {
            return self::userIsAdmin() || $itemsExist;
        }
        return ($embed->isOwnedByCurrentUser());
    }

    /**
     * Returns a CSRF token for the current user. On your forms, put this in a hidden field
     * using the name given by CSRF_TOKEN_NAME. Then, in the form-handling code,
     * throw an exception if checkCsrfToken() returns false.
     *
     * @return string  a token for preventing CSRF-attacks, or null if the current user is not signed in
     */
    public static function getCsrfToken() {
        return self::getCsrfTokenProper(XN_Profile::current());
    }

    /**
     * Adds a CSRF token to the given URI.
     *
     * @param $uri string  the URI to append the token to
     * @return string  the URI with CSRF token appended
     */
    public static function addCsrfToken($uri) {
        return XG_HttpHelper::addParameter($uri, self::CSRF_TOKEN_NAME, self::getCsrfToken());
    }

    /**
     * Returns a CSRF token for the current user. On your forms, put this in a hidden field
     * using the name given by CSRF_TOKEN_NAME. Then, in the form-handling code,
     * throw an exception if checkCsrfToken() returns false.
     *
     * @return string  a token for preventing CSRF-attacks, or null if the current user is not signed in
     */
    protected static function getCsrfTokenProper($currentProfile) {
        $tokens = self::getCsrfTokens($currentProfile);
        return count($tokens) ? reset($tokens) : null;
    }

    /**
     * Returns valid CSRF tokens for the current user.
     *
     * @param $currentProfile XN_Profile  profile object for the current user
     * @return string  recent tokens for preventing CSRF-attacks, newest first, keyed by creation date
     */
    protected static function getCsrfTokens($currentProfile) {
        if (! $currentProfile->isLoggedIn()) { return array(); }
        return self::getCsrfTokensProper($currentProfile->screenName, self::getCsrfSalts());
    }

    /**
     * Returns valid CSRF tokens for the current user.
     *
     * @param $screenName  screen name of the current user
     * @param $salts array  recent seed values for encryption, newest first
     * @return string  recent tokens for preventing CSRF-attacks, newest first, keyed by creation date
     */
    protected static function getCsrfTokensProper($screenName, $salts) {
        $tokens = array();
        foreach($salts as $creationDate => $salt) {
            $tokens[$creationDate] = md5($screenName . $salt);
        }
        return $tokens;
    }

    /**
     * Returns recent seed values for encryption, newest first.
     *
     * @return array  the salt strings, newest first, keyed by creation date
     */
    private static function getCsrfSalts() {
        return self::getCsrfSaltsProper(W_Cache::getWidget('main'), time(), 3600*24*3);
    }

    /**
     * Returns recent seed values for encryption, newest first.
     *
     * @param $mainWidget W_Widget  the main widget
     * @param $time integer  the current Unix timestamp
     * @param $maxAge integer  lifetime of a salt, in seconds
     * @param $lockId string  ID to use for the advisory lock
     * @param $newSalt string  value to use for new salt if needed (for testing), or null to generate it randomly
     * @return array  the salt strings, newest first, keyed by creation date
     */
    protected static function getCsrfSaltsProper($mainWidget, $time, $maxAge, $lockId = 'generate-csrf-salts', $newSalt = null) {
        $salts = unserialize($mainWidget->privateConfig['csrfSalts']);
        if (! $salts) { $salts = array(); }
        if (count($salts) == 0 || $time - strtotime(key($salts)) > $maxAge) {
            // Lock for at least a couple of minutes, because of NFS caching [Jon Aquino 2008-05-02]
            if (XG_Cache::lock($lockId, 300)) {
                $salts = array_slice($salts, 0, 1);
                if (! $newSalt) { $newSalt = uniqid(mt_rand(), true); }
                $salts = array(date('c', $time) => $newSalt) + $salts;
                $mainWidget->privateConfig['csrfSalts'] = serialize($salts);
                $mainWidget->saveConfig();
            }
        }
        return $salts;
    }

    /**
     * Returns whether $_REQUEST[CSRF_TOKEN_NAME] matches the user's token.
     *
     * @param $tokenName string  name of the token field in $_REQUEST
     */
    public static function checkCsrfToken($tokenName = self::CSRF_TOKEN_NAME) {
        return self::checkCsrfTokenProper($_REQUEST[self::CSRF_TOKEN_NAME], time(), self::getCsrfTokens(XN_Profile::current()), W_Cache::getWidget('main')->privateConfig[self::CSRF_GRACE_PERIOD_KEY]);
    }

    /**
     * Returns whether the submitted token matches the user's token.
     *
     * @param $token string  the submitted token
     * @param $time integer  the current Unix timestamp
     * @param $validTokens  recent tokens for preventing CSRF-attacks, newest first, keyed by creation date
     * @param $gracePeriodStart  date on which the 24-hour grace period begins
     */
    protected static function checkCsrfTokenProper($token, $time, $validTokens, $gracePeriodStart) {
        if (in_array($token, $validTokens)) { return true; }
        // 24-hour grace period to allow async jobs and video callbacks without the token to finish. [Jon Aquino 2008-04-25]
        $gracePeriod = 24 * 3600;
        return $time - strtotime($gracePeriodStart) < $gracePeriod;
    }

    /** Standard name for the token form field. */
    const CSRF_TOKEN_NAME = 'xg_token';

    /** Name of the config parameter for the date on which the 24-hour grace period begins. */
    const CSRF_GRACE_PERIOD_KEY = 'csrfTokenGracePeriodStart';

    /**
     * Returns HTML for a hidden <input> for the CSRF token.
     *
     * @return string  the input element, for adding to forms
     */
    public static function csrfTokenHiddenInput() {
        return '<input type="hidden" name="' . self::CSRF_TOKEN_NAME . '" value="' . xnhtmlentities(self::getCsrfToken()) . '">';
    }

    /**
     * Returns true if $user is the owner of $object, or $user is an admin
     *
     * @param   $user   XN_Profile              profile of the user to test
     * @param   $object XN_Content|W_Content    content object to test
     */
    public static function userIsAdminOrContributor($user,$object) {
        if (self::userIsContributor($user,$object)) {
            return true;
        } else {
            return self::userIsAdmin($user);
        }
    }

    /**
     * Returns true if $user is the owner of $object
     *
     * @param   $user   XN_Profile              profile of the user to test
     * @param   $object XN_Content|W_Content    content object to test
     */
    public static function userIsContributor($user, $object) {
        if (mb_strlen($user->screenName) && $user->screenName == $object->contributorName) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns old-style array error message if $user is not the owner of $object
     *
     * @param   $user   XN_Profile              profile of the user to test
     * @param   $object XN_Content|W_Content    content object to test
     */
    public static function userIsNotContributorError($user, $object) {
        if (! self::userIsContributor($user, $object)) {
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('ONLY_CONTRIBUTORS_CAN_EDIT'));
        }
        return null;
    }

}
