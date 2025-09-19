<?php
W_WidgetApp::includeFileOnce('/lib/XG_ConfigCachingApp.php');

/**
 * Handle site-wide tasks (such as rerouting requests before the site is launched)
 *
 * @ingroup XG
 */
class XG_App extends XG_ConfigCachingApp {

    /** Should we unify all dojo.require statements into a single
      *   ning.loader.require call? */
    const COMBINE_REQUIRE_REQUESTS = true;

    /**
     * Query-string parameter indicating that signed-out users should
     * be asked to sign in.
     */
    const SIGN_IN_IF_SIGNED_OUT = 'xgsi';

    /**
     * Key for default CSS replacement section
     */
    const CSS_SECTION_MARKER_KEY = 'css';

    // Temporary workaround for BAZ-3439  [Jon Aquino 2007-06-17]
    public static function composeRequest($widgetInstance, $controller, $action, $qs = null) {
        $url = parent::composeRequest($widgetInstance, $controller, $action, $qs);
        $url = XG_Browser::current()->rewriteUrl($url);
        return str_replace('/lib/scripts/bot.php', '', $url);
    }

    /**
     * Which widget instance is responsible for handling pre-launch tasks and
     * privacy settings?
     */
    public static $prelaunchWidget = 'main';

    /**
     * Whether we are rendering the sidebar.  We need to capture ningLoaderRequire
     * and addToSection calls so we can cache these along with the cached sidebar
     * HTML for BAZ-8304 [ywh 2008-07-10]
     */
    protected static $renderingSidebar = false;
    protected static $sidebarCachingDeps;    // sidebar caching dependencies stored here

    /**
     * Which controller is responsible for handling pre-launch tasks?
     */
    protected static $prelaunchController = 'index';
    /**
     * Which action should folks who aren't the site owner be sent to before the
     * site is launched?
     */
    protected static $prelaunchNotOwnerAction = 'notLaunched';

    /**
     * Starting render of sidebar
     */
    public static function startSidebarRendering() {
        self::$renderingSidebar = true;
        // reset dependencies
        self::$sidebarCachingDeps = array('ningLoaderRequire' => array(), 'addToSection' => array(), 'addToCssSection' => array());
    }

    /**
     * Get sidebar rendering status
     */
    public static function isSidebarRendering() {
        return self::$renderingSidebar;
    }

    /**
     * Ending render of sidebar
     */
    public static function endSidebarRendering() {
        self::$renderingSidebar = false;
    }

    /**
     * Return sidebar caching dependencies
     */
    public static function getSidebarCachingDependencies() {
        // grabbing the dependencies automatically turns off dependency capturing
        self::$renderingSidebar = false;
        return self::$sidebarCachingDeps;
    }

    /**
     * What is the sequence of actions that the site owner should be sent through
     * before the site can be launched?
     */
    protected static function getPrelaunchSteps() {
        static $prelaunchSteps;
        if (! $prelaunchSteps) {
            $prelaunchSteps = array(
                    'About Your Site' => array('admin', 'appProfile', xg_text('ABOUT_YOUR_SITE')),
                    'Features' => array('feature', 'add', xg_text('FEATURES')),
                    'Appearance' => array('appearance', 'edit', xg_text('APPEARANCE')),
            );
        }
        return $prelaunchSteps;
    }

    /**
     * Routes that the owner is allowed to see before launch in addition to the
     *   prelaunch steps
     */
    protected static $prelaunchOwnerRoutes = array(
        array('widgetName' => 'main', 'controllerName' => 'admin', 'actionName' => 'launch'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'report'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'feedback'),
        array('widgetName' => 'admin', 'controllerName' => 'index', 'actionName' => 'manage'),
        array('widgetName' => 'admin', 'controllerName' => 'index', 'actionName' => 'log'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'getLocale'),
        array('widgetName' => 'main', 'controllerName' => 'embeddable', 'actionName' => 'updateResources'),
        array('widgetName' => 'main', 'controllerName' => 'membership', 'actionName' => 'questions'), // BAZ-7608  [Jon Aquino 2008-05-13]
    );

    /**
     * Which routes are allowed for all in public apps in which nonregistered users can only see the homepage?
     * MAKE SURE this contains whatever $publicFallbackRoute is set to, so that the fallback route can be
     * displayed properly.
     */
    protected static $publicNonregHomepageRoutes = array(
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'index'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'getLocale'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'banned'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'pending'),
        array('widgetName' => 'main', 'controllerName' => 'members', 'actionName' => '10_featured'),
        array('widgetName' => 'chat', 'controllerName' => 'index', 'actionName' => 'read'),
        array('widgetName' => 'chat', 'controllerName' => 'index', 'actionName' => 'startChat'),
        array('widgetName' => 'opensocial', 'controllerName' => 'activity', 'actionName' => '10_activity'),
        array('widgetName' => 'opensocial', 'controllerName' => 'persistence', 'actionName' => '10_persistence'),
        array('widgetName' => 'opensocial', 'controllerName' => 'person', 'actionName' => '10_person'),
      );

    /**
     * Which routes are allowed for all in public apps in which nonregistered users can only see a join-message page?
     * MAKE SURE this contains whatever $publicFallbackRoute is set to, so that the fallback route can be
     * displayed properly.
     */
    protected static $publicNonregMessageRoutes = array(
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'getLocale'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'banned'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'pending'),
        array('widgetName' => 'main', 'controllerName' => 'members', 'actionName' => '10_featured'),
        array('widgetName' => 'chat', 'controllerName' => 'index', 'actionName' => 'read'),
        array('widgetName' => 'chat', 'controllerName' => 'index', 'actionName' => 'startChat'),
        array('widgetName' => 'opensocial', 'controllerName' => 'activity', 'actionName' => '10_activity'),
        array('widgetName' => 'opensocial', 'controllerName' => 'persistence', 'actionName' => '10_persistence'),
        array('widgetName' => 'opensocial', 'controllerName' => 'person', 'actionName' => '10_person'),
    );

    /**
     * Which route should a user be sent to when a not-allowed page in a public app is asked for?
     */
    protected static $publicFallbackRoute = array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'join');

    /**
     * Which routes are allowed for all in private apps?
     */
    protected static $privateRoutes = array(
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'getLocale'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'banned'),
        array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'pending'),
        array('widgetName' => 'main', 'controllerName' => 'members', 'actionName' => '10_featured'),
        array('widgetName' => 'chat', 'controllerName' => 'index', 'actionName' => 'read'),
        array('widgetName' => 'chat', 'controllerName' => 'index', 'actionName' => 'startChat'),
        array('widgetName' => 'opensocial', 'controllerName' => 'activity', 'actionName' => '10_activity'),
        array('widgetName' => 'opensocial', 'controllerName' => 'persistence', 'actionName' => '10_persistence'),
        array('widgetName' => 'opensocial', 'controllerName' => 'person', 'actionName' => '10_person'),
    );

    /**
     * Which routes don't require a PIN?
     */
    protected static $pinOptionalRoutes = array(
        array('widgetName' => 'main', 'controllerName' => 'authorization', 'actionName' => 'editPassword'),
        array('widgetName' => 'main', 'controllerName' => 'authorization', 'actionName' => 'updatePassword'),
        array('widgetName' => 'main', 'controllerName' => 'members', 'actionName' => '10_featured'),
        array('widgetName' => 'opensocial', 'controllerName' => 'activity', 'actionName' => '10_activity'),
        array('widgetName' => 'opensocial', 'controllerName' => 'persistence', 'actionName' => '10_persistence'),
        array('widgetName' => 'opensocial', 'controllerName' => 'person', 'actionName' => '10_person'),

    );

    /**
     * Which route should a user be sent to when a request comes in for a nonexistent widget? (BAZ-1682)
     */
    protected static $nonexistentWidgetRoute = array('widgetName' => 'main', 'controllerName' => 'error', 'actionName' => '404');

    /**
     * Which route should a banned user be sent to?
     */
    protected static $bannedUserRoute = array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'banned');

    /**
     * Which route should a pending user be sent to when they request a page only visible to members?
     */
    public static $pendingUserRoute = array('widgetName' => 'main', 'controllerName' => 'index', 'actionName' => 'pending');

    /**
     * Widget, controller and action requested by the user
     */
    protected static $requestedRoute = NULL;

    /**
     * Sets the value of $requestedRoute. For unit-testing only.
     */
    protected static function setRequestedRouteForTesting($requestedRoute) {
        if (! defined('UNIT_TESTING')) { xg_echo_and_throw('Not allowed (1157955462)'); }
        self::$requestedRoute = $requestedRoute;
    }

    /**
     * Returns whether the current page is the profile page for the current user.
     *
     * @return boolean  whether the current page is My Page
     */
    public static function onMyProfilePage() {
        return XG_HttpHelper::isMyPage(XG_HttpHelper::currentUrl());
    }

    /**
     * Returns whether the current page is a profile page
     *
     * @return boolean  whether the current page is somone's profile page
     */
    public static function onProfilePage() {
        return self::getRequestedRoute() == array('widgetName' => 'profiles', 'controllerName' => 'profile', 'actionName' => 'show');
    }

    /**
     * Restrict what can be accessed before the site is launched and the necessary
     * setup steps have been completed.
     *
     * @return array The route to dispatch, if it is allowed
     */
    public static function routeRequest() {
        $route = parent::routeRequest();

        // Logging for BAZ-8272 [Jon Aquino 2008-06-30]
        if (! XN_Profile::current()->isLoggedIn() && 'main/authorization/newProfile' == $route['widgetName'] . '/' . $route['controllerName'] . '/' . $route['actionName']) {
            XG_App::includeFileOnce('/lib/XG_LogHelper.php');
            $clientTime = $_GET['eoc144'] ? gmdate('c', $_GET['eoc144']/1000) : $_GET['eoc144'];
            XG_LogHelper::logBasicFlows('newProfile. EOC-144: Tried to access profile-questions page while signed out @ Client time: ' . $clientTime . ' @ Server time: ' . gmdate('c', time()) . ' @ User agent: ' . $_SERVER['HTTP_USER_AGENT'] . ' @ XN_REST::$TRACE: ' . XN_REST::$TRACE. ' @ Referrer: ' . $_SERVER['HTTP_REFERER'] . ' @ Cookies: ' . implode(', ', array_keys($_COOKIE)) . ' @ Remote Addr: ' . $_SERVER['REMOTE_ADDR']);
        }

        if (XN_Profile::current()->isLoggedIn()) {
            $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
            if ($requestMethod === 'POST') {
                // Note that routesExemptFromCsrfCheck will not have CSRF protection; avoid adding routes that destroy data [Jon Aquino 2008-09-10]
                $routesExemptFromCsrfCheck = array(
                        'main/authorization/doSignIn', // BAZ-7398 [Jon Aquino 2008-06-30]
                        'main/authorization/doSignUp', // BAZ-8314 [Jon Aquino 2008-06-30]
                        'main/index/logBaz8252', // BAZ-8314 [Jon Aquino 2008-09-10]
                        'profiles/profile/acceptEmailContent',
                        'music/playlist/addTrack',
                        'music/track/feature',
                        'music/track/unfeature',
                        'music/rating/updateFromPlayer',
                        'chat/index/startChat', // BAZ-9736 [ywh 2008-09-12]
                        );
                if (! XG_SecurityHelper::checkCsrfToken() && ! in_array($route['widgetName'] . '/' . $route['controllerName'] . '/' . $route['actionName'], $routesExemptFromCsrfCheck)) {
                    XG_App::includeFileOnce('/lib/XG_LogHelper.php');
                    XG_LogHelper::logCentrallyAndLocally('CSRF token invalid. Use XG_SecurityHelper::csrfTokenHiddenInput(). @ ' . $_REQUEST['xg_token'] . ' @ Current user: ' . XN_Profile::current()->screenName . ' @ Current URL: ' . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER'] . ' @ User Agent: ' . $_SERVER['HTTP_USER_AGENT']);
// Logging only for 3.2 (BAZ-7397, BAZ-7398) [Jon Aquino 2008-04-30]
//                    header("Location: http://{$_SERVER['HTTP_HOST']}/main/index/error");
//                    exit;
                }
            }
        }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        if ($_GET[self::SIGN_IN_IF_SIGNED_OUT]) {
            $url = XG_HttpHelper::removeParameter(XG_HttpHelper::currentUrl(), self::SIGN_IN_IF_SIGNED_OUT);
            if (! XN_Profile::current()->isLoggedIn()) { $url = XG_HttpHelper::signInUrl($url); }
            header('Location: ' . $url);
            exit;
        }
        /* Fetch the widget responsible for handling prelaunch configuration */
        $mainWidget = W_Cache::getWidget(self::$prelaunchWidget);
        /* BAZ-6053: allow instances to modify the route as desired */
        try {
            $routedWidget = W_Cache::getWidget($route['widgetName']);
            if (is_callable($routedWidget->config['router'])) {
                $route = call_user_func($routedWidget->config['router'], $route);
            }
        } catch (Exception $e) {
            // If there was trouble getting the widget or rerouting, just
            // use the original route
        }
        // BAZ-7474: XSS Vulnerability - handle by encoding the html characters
        $route['widgetName']     = htmlspecialchars($route['widgetName'], ENT_QUOTES);
        $route['controllerName'] = htmlspecialchars($route['controllerName'], ENT_QUOTES);
        $route['actionName']     = htmlspecialchars($route['actionName'], ENT_QUOTES);

        /* Store it for later */
        self::$requestedRoute = $route;
        /* Initialized configuration if necessary -- immediately after cloning */
        self::initializeConfiguration();
        $signOutRoute = array('widgetName' => 'main', 'controllerName' => 'authorization', 'actionName' => 'signOut');
        if ($route == $signOutRoute) { return $route; }  // Don't prevent sign-outs; otherwise may get infinite redirects [Jon Aquino 2007-09-28]
        if (XN_Profile::current()->isLoggedIn()
                && ! $_GET[Index_InvitationHelper::KEY]
                && 'main/authorization' != $route['widgetName'] . '/' . $route['controllerName']
                && 'main/error' != $route['widgetName'] . '/' . $route['controllerName']  // BAZ-4915 [Jon Aquino 2007-10-11]
                && ! User::isMember(XN_Profile::current())
                && ! User::isPending(XN_Profile::current())
                && ! User::isBanned(XN_Profile::current())) {
            error_log('Possibly BAZ-7028 @ Current user: ' . XN_Profile::current()->screenName . ' @ Current URL: ' . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER'] . ' @ User Agent: ' . $_SERVER['HTTP_USER_AGENT']);
            header('Location: ' . W_Cache::getWidget('main')->buildUrl('authorization', 'redirectToSignOut')); // BAZ-4529, BAZ-7028 [Jon Aquino 2007-09-24]
            exit;
        }
        /* If the site has not launched, restrict what can be accessed */
        if (! $mainWidget->config['launched']) {
            /* If the current user is not the owner, then send them to a
             * "hey, this hasn't launched" page (unless that's what the current
             * route already is)
             */
            $prelaunchNotOwnerRoute = array('widgetName' => self::$prelaunchWidget,
                                            'controllerName' => self::$prelaunchController,
                                            'actionName' => self::$prelaunchNotOwnerAction);
            if (! XG_SecurityHelper::userIsOwner()) {
                if ($route != $prelaunchNotOwnerRoute) {
                    /* Redirect to the "hey, this hasn't launched" page and exit */
                    header('Location: ' . $mainWidget->buildUrl(self::$prelaunchController, self::$prelaunchNotOwnerAction));
                    exit;
                }
            }
            else {
                /* If the app hasn't had its privacy setting set yet, set it. Ideally
                 * there is an 'appPrivacy' query string parameter, since this would get
                 * set on the first request after cloning, but just in case there isn't,
                 * default to private.
                 */
                 $shouldSaveConfig = false;
                 if (! $mainWidget->privateConfig['appPrivacySet']) {
                     $mainWidget->privateConfig['appPrivacySet'] = 1;
                     $shouldSaveConfig = true;
                     if (isset($_GET['appPrivacy'])) {
                         if ($_GET['appPrivacy'] == 'public') {
                             $mainWidget->config['appPrivacy'] = 'public';
                         } else {
                             $mainWidget->config['appPrivacy'] = 'private';
                         }
                     } else {
                         $mainWidget->config['appPrivacy'] = 'public';
                     }
                 }

                /* If the app hasn't had its timezone settings set yet, set them. These could come
                 * from various requests during the pre-launch process. The XG_App::launchApp() is
                 * responsible for setting defaults if this hasn't been set by launch-time.
                 */
                 if ((! $mainWidget->privateConfig['tzSet']) && isset($_GET['tzOffset'])) {
                     $mainWidget->privateConfig['tzSet'] = 1;
                     $shouldSaveConfig = true;
                     $mainWidget->config['tzOffset'] = (integer) $_GET['tzOffset'];
                     if (isset($_GET['tzUseDST'])) {
                         if ((integer) $_GET['tzUseDST']) {
                             $mainWidget->config['tzUseDST'] = 1;
                         } else {
                             $mainWidget->config['tzUseDST'] = 0;
                         }
                     } else {
                         /* Default to observing DST */
                         $mainWidget->config['tzUseDST'] = 1;
                     }
                 }

                /* Save the config if any we just set any initial values */
                if ($shouldSaveConfig) { $mainWidget->saveConfig(); }

                /* Since we've reached this spot because the current user is the
                 * owner and the app has not launched yet, create a User object for
                 * the app owner if there isn't one already (BAZ-101).
                 */
                 //TODO this needs to be extracted to form a separate function, with unit tests.
                 $ownerUserObject = User::loadOrCreate(XN_Profile::current());
                 if (is_null($ownerUserObject->my->profileAddress)) {
                     User::setProfileAddress($ownerUserObject, User::generateProfileAddress($ownerUserObject)); //BAZ-5480
                 }
                 XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
                 $ownerUserObject->my->memberStatus = XG_MembershipHelper::OWNER;
                 $ownerUserObject->save();

                 /* Similarly, add the app owner to the XN_ProfileSet::USERS alias
                  * (BAZ-4595)
                  */
                 if (! XN_ProfileSet::setContainsUser(XN_ProfileSet::USERS,XN_Profile::current()->screenName)) {
                     XN_ProfileSet::addMembersToSets(XN_Profile::current()->screenName, XN_ProfileSet::USERS);
                 }

                /* If the current user is the owner, then make sure the current route
                 * is allowed (based on configured step). If it's not allowed, redirect
                 * them to the next step they should see.
                 *
                 * If the owner is trying to view the unlaunched app page, it's likely
                 *   he/she was directed there before login and should now go back to
                 *   the app proper (BAZ-10).  Direct there.
                 */
                if (! self::prelaunchRouteIsAllowed($route)
                        || ($route == $prelaunchNotOwnerRoute)) {
                    $currentRoute = self::currentLaunchStepRoute();
                    header('Location: ' . $mainWidget->buildUrl($currentRoute['controllerName'],
                            $currentRoute['actionName']));
                    exit;
                }
            }
        }
        /* If the app is launched, inspect the route and see if it's allowed
         * based on the app's privacy settings and the currently logged in user */
        else {
            $p = XN_Profile::current();
            if ($p->isLoggedIn()
                    && ! $p->isOwner()
                    && ! $_GET[Index_InvitationHelper::KEY]
                    && 'main/authorization' != $route['widgetName'] . '/' . $route['controllerName']
                    && 'main/error' != $route['widgetName'] . '/' . $route['controllerName']  // BAZ-4915 [Jon Aquino 2007-10-11]
                    && ! User::isBanned($p)
                    && ($u = User::loadOrRetrieveIfLoaded($p->screenName))
                    && $u->my->xg_index_status == 'unfinished') {
                error_log("BAZ-8509: Redirecting broken user: $user->title @ Current URL: " . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER'] . ' @ User Agent: ' . $_SERVER['HTTP_USER_AGENT']);
                header('Location: ' . W_Cache::getWidget('main')->buildUrl('authorization', 'newProfile', array(
                    'unfinishedProfile' => 1,
                    'target' => XG_HttpHelper::currentURL(),
                )));
                exit;
            }

            XG_AuthorizationHelper::redirectIfPinRequired();
            //  If logged in user is banned, send off to the banned page
            if ($route != self::$bannedUserRoute) {
                XG_SecurityHelper::redirectIfBanned();
            }
            XG_GroupHelper::convertGroupUrlToGroupId();
            Index_InvitationHelper::processInvitation();
            if (isset($_GET['xgo']) && $_SERVER['PATH_INFO'] == '/') { // BAZ-8465: Shorten opt-out links [Andrey 2008-07-15]
                header("Location: /main/optout/?code=".urlencode($_GET['xgo']));
                exit;
            }
            // Note that in the logic below, the isPending check does not stop the sign-out action,
            // for which privacyRouteIsAllowed returns true. [Jon Aquino 2007-09-20]
            if (! self::privacyRouteIsAllowed($route)) {
                if (XN_Profile::current()->isLoggedIn() && User::isPending(XN_Profile::current())) {
                    XG_SecurityHelper::redirectToPendingAndExit();
                }
                header("Location: " . XG_AuthorizationHelper::signInUrl());
                exit;
            }
            XG_GroupHelper::checkGroupNotDeleted(XG_App::getRequestedRoute() == array('widgetName' => 'forum', 'controllerName' => 'topic', 'actionName' => 'show'));
            XG_GroupHelper::checkCurrentUserCanAccessGroup();
        }

        /* If we haven't redirected and exited by this point, just provide the
         * route to the caller and let the request proceed as usual
         */
        return $route;
    }

    /**
     * Determine whether the current route is allowed, based on launch configuration.
     * This function assumes that the site is not yet launched -- if it has launched
     * than any route should be allowed.
     *
     * This function also assumes the user making the request is the owner - any
     *   other user's view of the site is limited to the not launched page.
     *
     * @param $route array The route to check
     * @return boolean
     */
    protected static function prelaunchRouteIsAllowed($route) {
        /* Always allow JSON and HTML_JSON requests which are probably from dojo */
        if (isset($_GET['xn_out']) && (in_array($_GET['xn_out'], array('json', 'htmljson')))) {
            return true;
        }

        /* Allow any route explicitly allowed above */
        if (array_search($route, self::$prelaunchOwnerRoutes) !== false) {
            return true;
        }

        /* Allow the request if the user requested any GYO step */
        $routeStepIndex = self::getRequestedStep();
        return ($routeStepIndex !== false);
    }

    /**
     * Determine whether the app's privacy setting allows the current route
     *
     * @param array The route
     * @return boolean
     */
    public static function privacyRouteIsAllowed($route) {
        if (self::appIsPrivate()) {
            if (array_search($route, self::$privateRoutes) === false) {
                return (User::isMember(XN_Profile::current()) || self::overrideRouteIsAllowed($route));
            } else {
                return true;
            }
        }
        /* The app is public */
        else {
            $widget = W_Cache::getWidget(self::$prelaunchWidget);
            /* Non-registered users can see everything */
            if ($widget->config['nonregVisibility'] == 'everything') {
                return true;
            }
            else {
                /* Non-registered users can see just the homepage */
                if ($widget->config['nonregVisibility'] == 'homepage') {
                    $routesToAllow = self::$publicNonregHomepageRoutes;
                }
                /* Non-registered users see a join-message page */
                else {
                    $routesToAllow = self::$publicNonregMessageRoutes;
                }
                /* Is the route allowed for everyone? */
                if (array_search($route, $routesToAllow) === false) {
                    /* If not, see if the current user is registered
                     * or the route is on the override list */
                    return (User::isMember(XN_Profile::current()) || self::overrideRouteIsAllowed($route));
                } else {
                    return true;
                }
            }
        }
    }

    /**
     * Determine whether the route requires the user to enter a pin if they have one
     *
     * @param array The route
     * @return boolean
     */
    public static function pinOptional($route) {
      return (array_search($route, self::$pinOptionalRoutes) !== false);
    }

    /**
    * Determine whether a widget wants to override the app's privacy setting for a
    * particular route. This is used, for example, by feed-generating routes that
    * should still be accessible by everyone. Typically, actions that are allowed through
    * here will implement their own logic to determine if they are allowed. For example,
    * feeds use the presence of a key in the URL to determine if the user can see them
    */
    public static function overrideRouteIsAllowed($route) {
        if ($route['controllerName'] == 'sequencedjob') { return true; } // BAZ-7999 [Jon Aquino 2008-06-06]
        $isAllowed = false;
        if ($route['widgetName'] == 'undefined') {
            error_log('BAZ-5727 @ Current user: ' . XN_Profile::current()->screenName . ' @ Current URL: ' . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER']);
            return false;
        }
        try {
            $widget = W_Cache::getWidget($route['widgetName']);
            if ($widget->controllerHasAction($route['controllerName'], 'overridePrivacy')) {
                list($r, $html) = $widget->capture($route['controllerName'], 'overridePrivacy', array($route['actionName']));
                $isAllowed = (boolean) $r;
            }
        } catch (Exception $e) {
            error_log("Can't check override for route: " . implode('/',$route));
        }
        return $isAllowed;
    }


    /**
      * Determine the appropriate route for the first incomplete step in the
      *   prelaunch process
      *
      * @return array The route
      */
    public static function currentLaunchStepRoute() {
        $prelaunchSteps = self::getPrelaunchSteps();
        $stepNames = array_keys($prelaunchSteps);
        $completedSteps = explode(':', urldecode(
                W_Cache::getWidget(self::$prelaunchWidget)->config['prelaunchStepsCompleted']));
        if ($completedSteps) {
            foreach ($stepNames as $step) {
                if (!in_array($step, $completedSteps)) {
                    $action = $prelaunchSteps[$step];
                    return array('widgetName' => self::$prelaunchWidget,
                                 'controllerName' => $action[0],
                                 'actionName' => $action[1]);
                }
            }
        }
        /* Use the first prelaunch step if the compelted steps are invalid (or missing) */
        $action = $prelaunchSteps[$stepNames[0]];
        return array('widgetName' => self::$prelaunchWidget,
                     'controllerName' => $action[0],
                     'actionName' => $action[1]);
    }

    /**
     *  Get URL of previous step in GYO sequence
     */
    public static function getPreviousStepUrl() {
        $currentStep = XG_App::getRequestedStep();
        if ($currentStep > 0) {
            $stepsByIndex = array_values(self::getPrelaunchSteps());
            $prev = $stepsByIndex[$currentStep - 1];
            return W_Cache::getWidget(self::$prelaunchWidget)->buildUrl(
                    $prev[0], $prev[1]);
        }
        return null;
    }

    /**
     *  Get URL of next step in GYO sequence
     */
    public static function getNextStepUrl() {
        $currentStep = XG_App::getRequestedStep();
        if ($currentStep === false) {
            return null;
        }
        $numSteps = count(self::getPrelaunchSteps());
        if ($currentStep < $numSteps - 1) {
            $stepsByIndex = array_values(self::getPrelaunchSteps());
            $next = $stepsByIndex[$currentStep + 1];
            return W_Cache::getWidget(self::$prelaunchWidget)->buildUrl(
                    $next[0], $next[1]);
        }
        return null;
    }

    /**
     *  Get URL of final step in GYO sequence
     */
    public static function getFinalStepUrl() {
        $final = array_pop(array_values(self::getPrelaunchSteps()));
        return W_Cache::getWidget(self::$prelaunchWidget)->buildUrl(
                $final[0], $final[1]);
    }

    /**
     *  Determine whether all prelaunch steps have been completed
     *
     *  @return boolean
     */
    public static function allStepsCompleted() {
        if (self::appIsLaunched()) {
            return true;
        }
        $completedSteps = self::_getCompletedSteps();
        foreach (array_keys(self::getPrelaunchSteps()) as $step) {
            if (!in_array($step, $completedSteps)) {
                return false;
            }
        }
        return true;
    }

    /**
     *  Determine whether a particular prelaunch step has been completed
     *
     *  @param $step string The name of a prelaunch step
     *  @return boolean
     */
    public static function stepCompleted($step) {
        if (self::appIsLaunched()) {
            return true;
        }
        $completedSteps = self::_getCompletedSteps();
        return in_array($step, $completedSteps);
    }

    /**
     * Returns the app's privacy status.
     *
     * @return boolean true if the app is private, false if the app is public
     */
    public static function appIsPrivate() {
        return (W_Cache::getWidget(self::$prelaunchWidget)->config['appPrivacy'] == 'private');
    }

    /**
     * Determines if a network is currently set to run its own ads.
     *
     * @return 	boolean	true if network is currently set to run its own ads.
     */
    public static function runOwnAds() {
        return XN_Application::load()->premiumServices['run-own-ads'];
    }

    /**
     * Determines if a network is currently "protected" (need not show Ning branding).
     *
     * @return boolean	true if network is currently protected.
     */
    public static function protectYourNetwork() {
        return XN_Application::load()->premiumServices['private-source'];
    }

    /**
     *  Determine whether new content should be forced to be private (and not
     *    public or private according to its own rules of visibility)
     *
     *  @return boolean
     */
    public static function contentIsPrivate() {
        if (XG_App::appIsLaunched()) {
            return XG_App::appIsPrivate();
        }
        else {
            // all prelaunch content should be public - if the app is private at
            //   launch time the content will be converted to private
            return false;
        }
    }

    /**
     * Returns true IFF the app is public and the visibility setting
     * is 'everything' -- if there are any restrictions on what non-members
     * can see, this method returns false
     *
     * @return boolean
     */
     public static function everythingIsVisible() {
         return ((! self::appIsPrivate()) && (W_Cache::getWidget(self::$prelaunchWidget)->config['nonregVisibility'] == 'everything'));
     }

    /**
     * Returns true IFF the app is public and the visibility setting
     * is 'everything' or 'homepage'
     *
     * @return boolean
     *
     * @see XG_HttpHelper::isHomepage($url)
     */
     public static function homepageIsVisible() {
         return ((! self::appIsPrivate()) && (W_Cache::getWidget(self::$prelaunchWidget)->config['nonregVisibility'] != 'message'));
     }

    /**
     * Sets the privacy of the current network to the specified level.
     *
     * @privacyLevel    string  One of 'public' or 'private'.
     * @return          void
     */
    public static function setNetworkPrivacyLevel($privacyLevel) {
        if ($privacyLevel !== 'public' && $privacyLevel !== 'private') {
            throw new Exception("Network privacy level must be 'public' or 'private'.");
        }
        $widget = W_Cache::getWidget('main');
        $widget->config['appPrivacy'] = $privacyLevel;
        $widget->saveConfig();
    }

    /**
     * Takes a network on or offline, as specfied by $status.
     *
     * @param   $status boolean true to take the network online, false to take it offline.
     * @return  void
     */
    public static function setOnlineStatus($status) {
        $url = 'http://' . XN_AtomHelper::HOST_APP(XN_Application::load()->relativeUrl)
                       . '/xn/rest/1.0/application:' . urlencode(XN_Application::load()->relativeUrl)
                       . '?xn_method=PUT';
        $postData = array('application_online' => ($status ? 'true' : 'false'));
        $networkStatusJson = XN_REST::put($url, $postData, null);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $response = $json->decode($networkStatusJson);
        if ($response['application']['online'] !== $status) { throw new Exception("Application online status not successfully set."); }
    }

     /**
     *  Determine whether the application has been launched
     *
     *  @return boolean
     */
    public static function appIsLaunched() {
        return (W_Cache::getWidget(self::$prelaunchWidget)->config['launched'] == TRUE);
    }

    /**
     *  Mark the application as launched
     *
     */
    public static function launchApp() {
        $widget = W_Cache::getWidget(self::$prelaunchWidget);
        if ($widget) {
            $widget->config['launched'] = 1;
            /* Set timezone if it hasn't been set yet */
            if (! $widget->privateConfig['tzSet']) {
                /* Default to PST8PDT */
                $widget->config['tzOffset'] = 480;
                $widget->config['tzUseDST'] = 1;
            }
            $widget->saveConfig();
        }

        // Mark ALL content objects in the app as private if the app is private!
        //   Yes, this is painful!  see http://home.ninginc.com:8888/x/OCQ
        if (XG_App::appIsPrivate()) {
            $objects = array();
            $start = 0;
            //TODO Create should not be captialized here, surely?
            $query = XN_Query::Create('Content')
                    ->filter('owner')
                    ->filter('isPrivate', '=', false);
            do {
                //  do only 20 at a time to avoid using a lot of memory at once
                $results = $query->begin($start)->end($start + 20)->execute();
                $start += $query->getResultSize();
                $objects = array_merge($objects, $results);
            } while (count($results) > 0);

            foreach ($objects as $object) {
                $object->isPrivate = true;
                $object->save();
            }
        }

        //  If we don't have a layout yet, setup the default layout
        $layout = XG_Layout::load('index');
        if ($layout->willReInitializeOnLaunch()) {
            $layout->reInitializeAndSave();
            //  Update the enabled mozzles based on the new layout
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_FeatureHelper.php');
            Index_FeatureHelper::updateMozzleStatusFromLayout($layout->getLayout());
        }

        //  Initialize badge and widget configuration (BAZ-3726)
        XG_Version::createBadgeAndPlayerConfig(true);

        //  Create the badge-config.xml file and the avatar-grid image [Jon Aquino 2007-06-12]
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        XG_EmbeddableHelper::generateResources();

        //  Create the site broadcast alias and add the network creator (1.11.1)
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        $set = XN_ProfileSet::loadOrCreate(Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME);
        $set->addMembers(XN_Profile::current()->screenName);

        /*  Set the default value for music tracks download links to be disabled on new networks
        (this beahivor shouldnt be updated for existing networks
            that's why the code is here an not on appWideCodeUpgrade, see BAZ-4796) */
        $mainWidget = W_Cache::getWidget('main');
        $mainWidget->config['disableMusicDownload'] = 'yes';
        $mainWidget->saveConfig();

        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NETWORK, XG_ActivityHelper::SUBCATEGORY_NETWORK_CREATED);

        // Set default values for features sort order on the Add Features page.
        XG_App::includeFileOnce('/lib/XG_ConfigHelper.php');
        XG_ConfigHelper::updateAddFeaturesSortOrder();
    } // launchApp()

    /**
     *  Get a list of launch steps and completion states
     *
     *  Each entry in the array is an associative array with
     *    entries for name, displayName (localized), state, controller and action
     *
     *  @return array
     */
    public static function getLaunchbarSteps($forceReload = false) {
        static $steps = NULL;
        if (!isset($steps) || $forceReload) {
            $steps = array();
            $completedSteps = self::_getCompletedSteps();
            foreach (self::getPrelaunchSteps() as $name => $action) {
                $newStep = array();
                $newStep['name'] = $name;
                $newStep['state'] = (in_array($name, $completedSteps)
                        ? 'complete' : 'incomplete');
                $newStep['controller'] = $action[0];
                $newStep['action'] = $action[1];
                $newStep['displayName'] = $action[2];
                $steps[] = $newStep;
            }
        }
        return $steps;
    }

    /**
     *  Mark the specified step as completed and advance to the next step.  If
     *    the step is already completed this function does nothing.
     *
     *  @param $step string - name of newly completed step
     */
    public static function markStepCompleted($newlyCompletedStep) {
        $completedSteps = self::_getCompletedSteps();
        if (!in_array($newlyCompletedStep, $completedSteps)) {
            $completedSteps[] = $newlyCompletedStep;
            self::_setCompletedSteps($completedSteps);
        }
    }

    /**
     *  Get the index of the step requested by the user
     *
     *  @return integer
     */
    public static function getRequestedStep() {
        static $requestedStep = NULL;
        if (!isset($requestedStep)) {
            $route = self::$requestedRoute;
            $requestedStep = false;
            $tempStep = 0;
            foreach (self::getPrelaunchSteps() as $name => $value) {
                if (($route['controllerName'] == $value[0])
                        && ($route['actionName'] == $value[1])) {
                    $requestedStep = $tempStep;
                    break;
                }
                $tempStep++;
            }
        }
        return $requestedStep;
    }

    /**
     * Reset configuration as appropriate immediately after cloning
     *
     */
    protected static function initializeConfiguration() {
        $widget = W_Cache::getWidget(self::$prelaunchWidget);
        $app = XN_Application::load();
        if (strcasecmp($widget->config['appSubdomain'], $app->relativeUrl) !== 0 && XG_Cache::lock('initializeConfiguration', 10)) {
            $widget->config['appSubdomain'] = $app->relativeUrl;
            $widget->config['launched'] = 0;
            $widget->config['prelaunchStepsCompleted'] = '';
            $widget->config['tagline'] = '';
            $widget->config['description'] = '';
            $widget->config['appCodeVersion'] = XG_Version::currentCodeVersion();
            $locale = self::getParentLocale();
            if ($locale) { XG_LanguageHelper::setCurrentLocale($locale); }
            $widget->saveConfig();
            try {
                W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
                Index_AppearanceHelper::clearAppearanceSettings();
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
            try {
                //  Clear any action caching results
                NF_Controller::invalidateCache(NF::INVALIDATE_ALL);
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
            try {
                XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
                XG_EmbeddableHelper::generateResources();
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
            try {
                W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
                Index_InvitationHelper::generateBulkInvitationUrlIfNecessary();
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
            try {
                // Make sure that searchable shapes are searchable even if config is corrupt (BAZ-4390)
                XG_App::includeFileOnce('/lib/XG_ShapeHelper.php');
                XG_ShapeHelper::setStandardIndexingForSearchableModels();
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
            try {
                XG_App::includeFileOnce('/lib/XG_JobHelper.php');
                W_Cache::getWidget('main')->privateConfig[XG_JobHelper::GRACE_PERIOD_KEY] = date('c', 0);
                W_Cache::getWidget('main')->privateConfig[XG_SecurityHelper::CSRF_GRACE_PERIOD_KEY] = date('c', 0);
                W_Cache::getWidget('main')->saveConfig();
                XG_JobHelper::getSecretKey(); // Generate the async job key immediately, to avoid concurrency problems [Jon Aquino 2008-05-02]
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
            try {
                W_Cache::getWidget('main')->config['membersCanCustomizeTheme'] = 'yes';
                W_Cache::getWidget('main')->config['membersCanCustomizeLayout'] = 'yes';
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
            try {
                $widget = W_Cache::getWidget('opensocial');
                $widget->privateConfig['isEnabled'] = true;
                $widget->saveConfig();
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
        }
    }

    /**
     * Returns true IFF the specified attribute name conforms to the naming convention
     *   for attributes added by mozzles to shared content objects.
     *
     * @return boolean
     */
    public static function isWidgetAttribute($name) {
        return (preg_match('@^xg_[a-zA-Z0-9]+_[a-zA-Z0-9_]+$@u', $name));
    }

    /**
     * Returns an appropriately prefixed attribute name for a shared content object
     * given a widget and an attribute name suffix
     *
     * @param $widget W_BaseWidget The widget instance
     * @param $attr string The attribute name suffix
     * @return string
     */
    public static function widgetAttributeName(W_BaseWidget $widget, $attr) {
        if (! $widget) { throw new Exception('$widget not specified (1711076406)'); }
        return 'xg_' . $widget->dir . '_' . $attr;
    }

    /**
     * This method enforces app membership; use it at the beginning of an
     * action to ensure that the action is only used by a signed in user that
     * is a member of an app
     *
     * @param $controller string The controller for the destination after signin/signup/join
     * @param $action string     The action for the destination after signin/signup/join
     * @param $extra string optional bits for the URL after the controller and action
     *
     * @deprecated 2.0  Use XG_SecurityHelper::redirectIfNotMember instead
     */
    public static function enforceMembership($controller, $action, $extra = null) {
        // TODO: Delegate to XG_SecurityHelper::redirectIfNotMember  [Jon Aquino 2007-09-21]
        $widget = W_Cache::current('W_Widget');
        $targetUrl = $widget->buildUrl($controller,$action) . $extra;
        $user = XN_Profile::current();
        if (! $user->isLoggedIn()) {
           $signUrl = XG_AuthorizationHelper::signUpUrl($targetUrl);
            header("Location: $signUrl");
            exit();
        } elseif (! User::isMember($user)) {
            if (User::isPending($user)) {
                $joinUrl = W_Cache::getWidget(self::$pendingUserRoute['widgetName'])->buildUrl(self::$pendingUserRoute['controllerName'], self::$pendingUserRoute['actionName']);
            }
            else {
                $joinUrl = XG_AuthorizationHelper::signUpUrl($targetUrl);
            }
            header("Location: $joinUrl");
            exit();
        }
    }

    public static function checkCurrentUserIsSignedIn() {
        if (XN_Profile::current()->isLoggedIn()) { return; }
        header('Location: ' . XG_AuthorizationHelper::signUpUrl());
        exit;
    }

    /**
     * Is new content moderated?
     *
     * @return boolean true if content is moderated before showing up
     */
    public static function contentIsModerated() {
        return W_Cache::getWidget('main')->config['moderate'] == 'yes';
    }

    /**
     * Are new groups moderated?
     *
     * @return boolean true if groups are moderated before showing up
     */
    public static function groupsAreModerated() {
        return W_Cache::getWidget('main')->config['moderateGroups'] == 'yes';
    }

    /**
     * Are new members moderated?
     *
     * @return boolean true if members must be approved
     */
    public static function membersAreModerated() {
        return (W_Cache::getWidget('main')->config['moderateMembers'] == 'yes');
    }

    /**
     * is group creation restricted to admins?
     *
     * @return boolean true if only admins can create groups
     */
    public static function membersCanCreateGroups() {
        return (W_Cache::getWidget('main')->config['onlyAdminsCanCreateGroups'] != 'yes');
    }

    /**
     * is events creation restricted to admins?
     *
     * @return boolean true if only admins can create events
     */
    public static function membersCanCreateEvents() {
        return (W_Cache::getWidget('main')->config['onlyAdminsCanCreateEvents'] != 'yes');
    }

    /**
     * Can members customize the theme of their "My Page"?
     *
     * @return  boolean
     */
    public static function membersCanCustomizeTheme() {
        return (W_Cache::getWidget('main')->config['membersCanCustomizeTheme'] == 'yes');
    }


    private static $membersCanCustomizeLayout = null;
    /**
     * Can members customize the layout of their "My Page"?
     *
     * @return  boolean
     */
    public static function membersCanCustomizeLayout() {
        if (! is_null(self::$membersCanCustomizeLayout)) { return self::$membersCanCustomizeLayout; }
        XG_App::includeFileOnce('/lib/XG_LayoutEditHelper.php');
        return XG_LayoutEditHelper::layoutEditingEnabled() && (W_Cache::getWidget('main')->config['membersCanCustomizeLayout'] == 'yes');
    }

    /**
     * Are Music Player Download Links Disasbled?
     *
     * @return boolean true if the owner doesnt want to give users the option to disclose the download link
     */
    public static function musicDownloadIsDisabled() {
        return (W_Cache::getWidget('main')->config['disableMusicDownload'] == 'yes');
    }

    /**
     * Is OpenSocial enabled?
     *
     * @return  boolean
     */
    public static function openSocialEnabled() {
        return (W_Cache::getWidget('opensocial')->privateConfig['isEnabled']);
    }

    /**
     * Are members allowed to invite others to join?
     *
     * @return  boolean true if members are allowed to invite others.
     * @deprecated 2.0
     */
    public static function allowInvites() {
        return true;
    }

    /**
     * Can visitors request invites?
     *
     * @return boolean true if visitors can request invites
     * @deprecated 2.0
     */
    public static function allowInviteRequests() {
        // No longer supported as of 2.2 (new invitation API)  [Jon Aquino 2007-10-30]
        return false;
    }

    /**
     * Can visitors join the network (without having an invite)?
     * Applies to private networks only.
     *
     * @return boolean true if you don't need an invite to join the network, if it is private
     */
    public static function allowJoinByAll() {
        // TODO: Return true if app is public, or rename to "allowAllToJoinIfPrivate"? [Jon Aquino 2007-09-27]
        return (W_Cache::getWidget('main')->config['allowJoin'] == 'all');
    }

    /**
     * Does the app keep track of new contents activity?
     *
     * @return  boolean true if the app should generate log items for new content.
     */
    public static function logNewContent() {
        return W_Cache::getWidget('activity')->config['logNewContent'] != 'N';
    }

    /**
     * Does the app keep track of new comments activity?
     *
     * @return  boolean true if the app should generate log items for new comments and replies.
     */
    public static function logNewComments() {
        return W_Cache::getWidget('activity')->config['logNewComments'] != 'N';
    }
    /**
     * Does the app keep track of new members activity?
     *
     * @return  boolean true if the app should generate log items upon members joining the network and groups.
     */
    public static function logNewMembers() {
        return W_Cache::getWidget('activity')->config['logNewMembers'] != 'N';
    }
    /**
     * Does the app keep track of new friending activity?
     *
     * @return  boolean true if the app should generate log items for members becoming friends.
     */
    public static function logFriendships() {
        return W_Cache::getWidget('activity')->config['logFriendships'] != 'N';
    }
    /**
     * Does the app keep track of events activity?
     *
     * @return  boolean true if the app should generate log items for events activity
     */
    public static function logNewEvents() {
        return W_Cache::getWidget('activity')->config['logNewEvents'] != 'N';
    }
    /**
     * Does the app keep track of profile updates?
     *
     * @return  boolean true if the app should generate log items for profile updates.
     */
    public static function logProfileUpdates() {
        return W_Cache::getWidget('activity')->config['logProfileUpdates'] != 'N';
    }

    /**
     * Does the app keep track of OpenSocial changes?
     *
     * @return  boolean true if the app should generate log items for opensocial changes.
     */
    public static function logOpenSocial() {
        return W_Cache::getWidget('activity')->config['logOpenSocial'] != 'N';
    }

    public static function appIsCentralized() {
        return filetype(NF_APP_BASE . '/widgets') == 'link';
    }

    /**
     * What is the requested top-level route?
     *
     * @return array
     */
    public static function getRequestedRoute() { return self::$requestedRoute; }

    /**
     * Get Ning Directory Profile information for the current app
     *
     */
    public static function getDirectoryProfile() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $appInfoUrl = 'http://' . XN_AtomHelper::HOST_APP(XN_Application::load()->relativeUrl) .
                      '/xn/rest/1.0/application:' . urlencode(XN_Application::load()->relativeUrl);
        try {
            $appInfoJson = XN_REST::get($appInfoUrl);
            $appStructure = $json->decode($appInfoJson);
            $appInfo = $appStructure['application'];
        } catch (Exception $e) {
            error_log("Can't get app info: " . $e->getMessage());
            $appInfo = array();
        }
        return $appInfo;
    }

    /**
     * Returns an XN_Application object for the current application's parent.
     *
     * @return XN_Application
     */
    public static function getParentInfo() {
        try {
            $xml = @XN_REST::get('/application/parent');
            if (!$xml) { return NULL; }
            $parent = @XN_AtomHelper::loadFromAtomFeed($xml,'XN_Application');
            return $parent;
        }
        catch (Exception $e) {
            error_log('getParentInfo got exception: ' . $e->getMessage());
        }
    }

    public static function getParentLocale() {
        try {
            $parent = self::getParentInfo();
            if (!$parent) { return NULL; }
            $parentUrl = 'http://' . XN_AtomHelper::HOST_APP($parent->relativeUrl)
                    . '/main/index/getLocale?xn_auth=no';
            $locale = trim(@XN_REST::get($parentUrl));
            if (mb_strlen($locale) > 0
                    && file_exists(NF_APP_BASE . '/lib/XG_MessageCatalog_' . $locale . '.php')) {
                return $locale;
            }
            else {
                //  don't return an error page!
                return NULL;
            }
        }
        catch (Exception $e) {
            error_log('getParentLocale got exception: ' . $e->getMessage());
        }
    }

    public static function canSeeInviteLinks($profile) {
        // Shouldn't the isMember call be in canSendInvites? [Jon Aquino 2007-08-29]
        return self::canSendInvites($profile) && User::isMember($profile);
    }

    public static function canSendInvites($profile) {
        if (! XG_App::appIsLaunched()) { return false; }
        if (! $profile->isLoggedIn()) { return false; }
        return true;
    }

    public static function _getCompletedSteps() {
        $widget = W_Cache::getWidget(self::$prelaunchWidget);
        $stepNames = urldecode($widget->config['prelaunchStepsCompleted']);
        if ($stepNames) {
            return explode(':', $stepNames);
        }
        else {
            return array();
        }
    }

    public static function _setCompletedSteps($stepNames) {
        $widget = W_Cache::getWidget(self::$prelaunchWidget);
        if (is_array($stepNames)) {
            $stepNames = implode(':', $stepNames);
        }
        $widget->config['prelaunchStepsCompleted'] = urlencode($stepNames);
        $widget->saveConfig();
    }

    /**
     * Add one or more Javascript dependencies to the request-wide list
     *
     * @param $arg string What you'd pass to ning.loader.require() or dojo.require()
     * @param $arg string ...
     */
    public static function ningLoaderRequire() {
        if (count(self::$ningLoaderRequireArgs) == 0) {
            self::$ningLoaderRequireArgs[] = 'xg.shared.util';
            self::$ningLoaderRequireArgs[] = 'xg.shared.messagecatalogs.en_US';
            self::$ningLoaderRequireArgs[] = 'xg.shared.messagecatalogs.' . XG_LanguageHelper::baseLocale(XG_LOCALE);
            if (XG_LanguageHelper::currentLocaleHasCustomCatalog()) {
                self::$ningLoaderRequireArgs[] = 'xg.custom.shared.messagecatalogs.' . XG_LOCALE;
            }
        }
        $args = func_get_args();
        foreach ($args as $arg) {
            if (mb_strpos($arg, '.nls.') !== false) { continue; }  // nls packages are no longer used (BAZ-3955) [Jon Aquino 2007-08-01]
            if (self::COMBINE_REQUIRE_REQUESTS) {
                if (self::$renderingSidebar) {
                    if (! in_array($arg, self::$sidebarCachingDeps['ningLoaderRequire'])) {
                        array_push(self::$sidebarCachingDeps['ningLoaderRequire'], $arg);
                    }
                }
                if (in_array($arg, self::$ningLoaderRequireArgs)) { continue; }
                self::$ningLoaderRequireArgs[] = $arg;
            } else {
                echo "<script type='text/javascript'>dojo.require('$arg');</script>\n";
            }
        }
    }

    /** Accumulated arguments to pass to ning.loader.require. */
    public static $ningLoaderRequireArgs = array();

    /**
     * Invalidate cached queries based on the content type of the provided object
     *
     * @param $objects XN_Content|array The xn/content/save/after event will provide
     * a content object as the first argument, but xn/content/delete/before may provide
     * an content object, an ID, or an array of same.
     */
    public static function invalidateByContentType($objects) {
        // Don't do the invalidation if it has been turned off
        if (! self::$invalidateFromHooks) {
            return;
        }
        // Don't do any invalidation if query caching is disable
        if (XG_Query::getCacheStorage() == 'none') {
            return;
        }
        $types = array();
        if ($objects instanceof XN_Content || $objects instanceof W_Content) {
            $types[$objects->type] = true;
        }
        else if (is_array($objects)) {
            foreach ($objects as $object) {
                if ($object instanceof XN_Content || $object instanceof W_Content) {
                    $types[$object->type] = true;
                }
            }
        }
        $keys = array_keys($types);
        foreach ($keys as $type) {
            if (XG_Query::getCacheStorage() == 'file') {
                XG_Cache::invalidate(XG_Cache::key('type',$type));
            }
            else {
                XN_Cache::invalidate(XG_Cache::key('type',$type));
            }
        }
    }

    /** BAZ-1507: We need a way to programatically turn on and off the hook-invoked
     *  invalidation */
    protected static $invalidateFromHooks = true;
    public static function getInvalidateFromHooks() { return self::$invalidateFromHooks; }
    public static function setInvalidateFromHooks($b) { self::$invalidateFromHooks = (bool) $b; }


    /** What's the name of the "just logged out" cookie? */
    protected static $logoutCookieName = 'xgl';

    /**
     *  Send "do not cache this page" headers if user is logged in.
     *
     *  @param      $force   bool		Force "don't cache" headers even if user is not logged in.
     *  @return     void
     */
    public static function sendNoCacheHeaders($force = false) {
        if ($force || XN_Profile::current()->isLoggedIn()) {
            header('Pragma: no-cache');
            header('Cache-Control: private, no-cache, proxy-revalidate'); // no-cache="Set-Cookie",
        }
    }

    /**
     * Set or unset the "just logged out" cookie
     *
     * @param $state boolean true sets the cookie, false removes it
     * @deprecated
     */
    public static function setLogoutCookie($state) {
        // TODO: Delete this function. In this past, this cookie was used
        // for displaying a message to the user after they signed out. [Jon Aquino 2008-01-22]

        // Even when state is values we have to supply a value
        // for the cookie since a core bug prevents us from actually
        // deleting the cookie
        // @todo: change once NING-5012 is fixed
        $logoutCookieValue = $state ? 'y' : 'n';
        setcookie(self::$logoutCookieName, $logoutCookieValue, 0, '/');
    }

    /**
     * Get the value of the "just logged out" cookie
     *
     * @return boolean
     */
    public static function getLogoutCookie() {
        return (isset($_COOKIE[self::$logoutCookieName]) && ($_COOKIE[self::$logoutCookieName] == 'y'));
    }

    /**
     * An XG-specific version of loadWidgets(), for activities that should
     * happen immediately after widgets are loaded (and we have access to their config)
     */
    public static function loadWidgets() {
        // If we're displaying timing info, suppress it temporarily
        // so we can include it in the timing info that BAZ-2287 code
        // (below) captures
        $suppressedTimingInfo = null;
        if (defined(NF::NF_DISPLAY_TIMING)) { ob_start(); }
        // Do the regular widget-loadin'
        parent::loadWidgets();
        /* Set the right locale to use for the request */
        XG_LanguageHelper::setXgLocale();
        if (defined(NF::NF_DISPLAY_TIMING)) { $suppressedTimingInfo = ob_get_clean(); }

        // If debugging is enabled (see BAZ-1741), set up XN_Debug appropriately)
        $debug = trim(W_Cache::getWidget('admin')->config['debug']);
        if (mb_strlen($debug)) {
            $debugSettings = array_flip(preg_split('@;\s*@u',$debug));
            // query: Query logging for performance analysis at page bottom
            if (isset($debugSettings['query'])) {
                $_GET['xn_debug'] = 'api-comm-stack';
                XN_Debug::allowDebug(true);
                XN_Debug::suppressAutomaticDebugPrinting(true);
            }
            // BAZ-2074: log to error log for query timing
            if (isset($debugSettings['query-log'])) {
                XG_App::includeFileOnce('/lib/XG_DebugHelper.php');
                XN_Event::listen('xn/rest/request/before', array('XG_DebugHelper','queryLogBefore'));
                XN_Event::listen('xn/rest/request/after', array('XG_DebugHelper','queryLogAfter'));
            }
            // BAZ-5787: Allow xg_perflog activation via config variable
            if (isset($debugSettings['perflog'])) {
                XG_PerfLogger::activate();
            }
        }
        // BAZ-2287: log all timing information at the bottom of the page
        if (defined(NF::NF_DISPLAY_TIMING)) {
            XG_App::includeFileOnce('/lib/XG_DebugHelper.php');
            ob_start(array('XG_DebugHelper','insertTimingInformation'));
        }
        // If we saved any suppressed Timing info above, print it out now
        if (! is_null($suppressedTimingInfo)) {
            print $suppressedTimingInfo;
        }

        /* BAZ-3537: calculate how long the page took to render and do
         * something with it if it's too long */
        register_shutdown_function(array('XG_PerfLogger','measurePhpRuntime'));

        // register browser type for mobile differentiation before calling XG_App::go calls dispatchRequest (BAZ-9165) [ywh 2008-08-22]
        XG_Browser::initFromRequest();

        // Take any necessary actions if the centralized code has been updated
        XG_Version::noticeNewCodeVersion();
    }

    /**
     * We've got to override W_WidgetApp::dispatchRequest() so that we can catch
     * errors that result from nonexistent widgets (BAZ-1682)
     */
    public static function dispatchRequest($route = null) {
        XG_PerfLogger::$usefulTime = microtime(true); // track the beginning of a useful work
        W_Cache::getWidget('admin')->includeFileOnce('/lib/helpers/Admin_DomainRedirectionHelper.php');
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($headers = Admin_DomainRedirectionHelper::domainRedirectionHeaders(W_Cache::getWidget('main')->config['domainName'], $_SERVER['HTTP_X_NING_REQUEST_URI'], $requestMethod)) {
            foreach ($headers as $header) { header($header); }
            exit;
        }
        // fix redirection target if it exists (BAZ-8305) [ywh 2008-07-03]
        if (array_key_exists('target', $_GET)) {
            $_GET['target'] = XG_HttpHelper::replaceUrlHost($_GET['target'], $_SERVER['SERVER_NAME']);
        }
        /* BAZ-6015: post-process page with any replacement sections to
         * allow for data to be inserted in the page head at arbitrary
         * places in the flow. Also turn off XNHTML parsing since we
         * no longer need it. */
        // actual ob_start() is happening inside startSectionMarkerProcessing()
        header('X-XN-XNHTML: false');
        XG_App::sendNoCacheHeaders();

        // If no route is specified (which is what usually happens), calculate
        // one from the URL
        if (! (is_array($route) && isset($route['widgetName']) &&
        isset($route['controllerName']) && isset($route['actionName']))) {
            // Use call_user_func() for proper inheritance
            $route = call_user_func(array(W_Cache::getClass('app'),'routeRequest'));
        }
        if ($route['actionName'] == 'undefined') {
            error_log('BAZ-5794 @ Current user: ' . XN_Profile::current()->screenName . ' @ Current URL: ' . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER']);
            header("Location: http://{$_SERVER['HTTP_HOST']}/error.php?code=500");
            exit;
        }
        // Make sure the requested widget exists;
        try {
            $requestedWidget = W_Cache::getWidget($route['widgetName']);
            $requestedWidget->dispatch($route['controllerName'], $route['actionName']);
        } catch (Exception $e) {
            // No? Off to the 404 page. We don't have to check for privacy here, since
            // routeRequest has already taken care of that for us
            try {
                $errorWidget = W_Cache::getWidget(self::$nonexistentWidgetRoute['widgetName']);
                $errorWidget->dispatch(self::$nonexistentWidgetRoute['controllerName'], self::$nonexistentWidgetRoute['actionName']);
            } catch (Exception $e) {
                // Something has gone very wrong
                header("Location: http://{$_SERVER['HTTP_HOST']}/error.php?code=500");
                exit();
            }
        }
    }

    /**
     * This is called after objects are saved. If the shape of the object that was just saved hasn't
     * had its indexing status set yet, then it gets set here.
     *
     * @param $object XN_Content The xn/content/save/after event provides
     * a content object as the first argument.
     */
    public static function verifyIndexing($object) {
        XG_App::includeFileOnce('/lib/XG_ShapeHelper.php');
        $indexingComplete = XG_ShapeHelper::isModelIndexingComplete($object->type);
        if ($indexingComplete === false) {
            XG_ShapeHelper::markModelIndexingComplete($object->type);
        }
    }

    /**
     * Returns whether the app is symlinked or full-source.
     *
     * @return whether the app is centralized or decentralized.
     */
    public static function symlinked() {
        if (W_Cache::getWidget('main')->config['debugSimulateSymlinkedApp']) { return true; }
        $filetype = filetype($_SERVER['DOCUMENT_ROOT'] . '/lib');
        if ($filetype == 'link') { return true; }
        if ($filetype == 'dir') { return false; }
        throw new Exception('Unexpected filetype: ' . $filetype . ' (1937658416)');
    }


    /**
     * The contents of the various replacement seconds manipulated by
     * XG_App::addToSection() and friends
     */
    protected static $replacementSections = array();

    /**
     * The name of the default replacement section. Set to some
     * arbitrary per-request value on first use.
     */
    protected static $defaultSectionMarker = null;

    /**
     * The name of the default css replacement section. Set to some
     * arbitrary per-request value on first use.
     */
    protected static $defaultCssSectionMarker = null;

    /**
     * User-defined replacement sections (i.e. ones other than the
     * default)
     */
    protected static $userSectionMarkers = array();

    /**
     * Returns the magic string that should be inserted in the page where the
     * replacement contents will eventually go. The value of the magic string
     * may change from request to request and user code should not make any
     * assumptions about its format or structure
     *
     * @param $section string optional replacement section to generate a string for.
     *        With no argument, uses the default section.
     * @return string
     * @see BAZ-6015
     */
    public static function sectionMarker($section = null) {
        if (is_null($section)) {
            if (is_null(self::$defaultSectionMarker)) {
                self::$defaultSectionMarker = '<div id="baz8252" style="display: none;">' . md5(microtime(true)) . '</div>';
                self::$replacementSections[self::$defaultSectionMarker] = '';
            }
            return self::$defaultSectionMarker;
        }
        else {
            if (! isset(self::$userSectionMarkers[$section])) {
                self::$userSectionMarkers[$section] = '<div id="baz8252" style="display: none;">' . md5(microtime(true).$section) . '</div>';
                self::$replacementSections[self::$userSectionMarkers[$section]] ='';
            }
            return self::$userSectionMarkers[$section];
        }
    }

    /**
     * Returns magic string for CSS replacement section; see doc for sectionMarker
     */
    public static function cssSectionMarker() {
        if (is_null(self::$defaultCssSectionMarker)) {
            self::$defaultCssSectionMarker = self::sectionMarker(self::CSS_SECTION_MARKER_KEY);
        }
        return self::$defaultCssSectionMarker;
    }

    /**
     * Adds text to the specified section, or the default section if $section is null.
     *
     * @param $text string text to add
     * @param $section string optional which section to add the text to
     * @see BAZ-6015
     */
    public static function addToSection($text, $section = null) {
        $marker = self::sectionMarker($section);
        self::$replacementSections[$marker] .= $text;
        if (self::$renderingSidebar && is_null($section)) {
            self::$sidebarCachingDeps['addToSection'][] = $text;
        }
    }

    /**
     * Adds text to the default CSS replacement section or the default replacement section
     * if no CSS replacement section exists
     *
     * @param $text string  text to add
     */
    public static function addToCssSection($text) {
        // prevents BAZ-8273 edge cases resulting in duplicate loads of the same css files [ywh 2008-07-01]
        static $cssLinks = array();
        if (! array_key_exists($text, $cssLinks)) {
            if (! is_null(self::$defaultCssSectionMarker)) {
                self::addToSection($text, self::CSS_SECTION_MARKER_KEY);
            } else {
                self::addToSection($text);
            }
            $cssLinks[$text] = 1;
        }
        if (self::$renderingSidebar) {
            self::$sidebarCachingDeps['addToCssSection'][] = $text;
        }
    }

    static protected $sectionStarted = 0;
    /**
     *  Starts section marker processing.
     *
     *  @return     void
     */
    public function startSectionMarkerProcessing() {
        self::$sectionStarted = 1;
        ob_start(array('XG_App','populateSections'));
    }

    /**
     *  Starts section marker processing.
     *
     *  @return     void
     */
    public function finishSectionMarkerProcessing() {
        if (self::$sectionStarted) {
            self::$sectionStarted = 0;
            ob_end_flush();
        }
    }

    /**
     * The callback function that should be passed to ob_start(). Replaces all of
     * the magic strings with the contents of the corresponding sections. This
     * function should never be called directly; it should only be called as an
     * output buffer callback.
     *
     * @param string the raw output buffer contents
     * @return string the processed output buffer contents
     * @see BAZ-6015
     */
    public static function populateSections($buffer) {
        /* Return immediately if no sections are in use */
        if (! count(self::$replacementSections)) {
            return $buffer;
        }

        /* Log if the input buffer does not contain the replacement string(s) */
        foreach (array_keys(self::$replacementSections) as $marker) {
            if (mb_strpos($buffer, $marker) === false) {
                error_log('BAZ-8252 [XG_App]; marker:[' . $marker . '] not present in [' . getenv('SCRIPT_URI') . ']; Referer: [' . getenv('HTTP_REFERER') . ']; User: [' . XN_Profile::current()->screenName . ']');
            }
        }

        /* If there are some sections in use, modify the buffer
         * appropriately and return it
         */
        return str_replace(array_keys(self::$replacementSections),
                           array_values(self::$replacementSections),
                           $buffer);
    }

    /**
     * Deal with an unhandled exception - log the problem and
     * redirect to generic error page (BAZ-6285)
     */
    public static function fatalExceptionHandler(Throwable $e) {
        $msg = "Unhandled exception in {$e->getFile()}@{$e->getLine()}:  {$e->getMessage()}\n";
        $msg .= $e->getTraceAsString();
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/error.php?' .
            http_build_query(array('code' => 500,
                       'uri' => $_SERVER['HTTP_X_NING_REQUEST_URI']));
        error_log($msg);
        if (PHP_SAPI === 'cli') {
            fwrite(STDERR, $msg . PHP_EOL);
            exit(1);
        }
        if (!headers_sent()) {
            header("Location: $url");
        }
        exit();
    }

    /**
     * Allows constants to be overridden in the admin config file.
     *
     * Looks up the given constant in the admin config file.
     * If it is not there, returns the value of the constant in the code.
     *
     * For example, if 'XG_LangHelper::FOO' is given, we first check the admin config file
     * for a property named 'XG_LangHelper_FOO'. Failing that, we return XG_LangHelper::FOO.
     *
     * @param $name string  name of the constant, in the form <class>::<constant>
     * @return string  value of the constant
     */
    public static function constant($name) {
        $x = W_Cache::getWidget('admin')->config[str_replace('::', '_', $name)];
        if (mb_strlen($x)) { return $x; }
        $x = @constant($name);
        if (mb_strlen($x) || is_bool($x)) { return $x; }
        throw new Exception('Constant not defined: ' . $name);
    }

}
/** Display a generic error page if an exception is not handled (BAZ-6285) */
set_exception_handler('XG_App::fatalExceptionHandler');

/**
 * App-wide includes
 */
XG_App::includeFileOnce('/lib/XG_Browser.php');
XG_App::includeFileOnce('/lib/XG_BrowserAwareController.php');
XG_App::includeFileOnce('/lib/XG_LanguageHelper.php');
XG_App::includeFileOnce('/lib/XG_TemplateHelpers.php');
XG_App::includeFileOnce('/lib/XG_PageHelper.php');
XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
XG_App::includeFileOnce('/lib/XG_AuthorizationHelper.php');
XG_App::includeFileOnce('/lib/XG_GroupHelper.php');
XG_App::includeFileOnce('/lib/XG_GroupEnabledController.php');
XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
XG_App::includeFileOnce('/lib/XG_JoinPromptHelper.php');
XG_App::includeFileOnce('/lib/XG_UserHelper.php');
XG_App::includeFileOnce('/lib/XG_Version.php');
XG_App::includeFileOnce('/lib/XG_PerfLogger.php');
XG_App::includeFileOnce('/lib/XG_CacheExpiryHelper.php');
XG_App::includeFileOnce('/lib/XG_MediaUploaderHelper.php');

/* Tell the framework that this class should handle app-wide stuff */
W_Cache::putClass('app','XG_App');

/* Set up type-based query-cache invalidation */
XN_Event::listen('xn/content/save/after', array('XG_App', 'invalidateByContentType'));
XN_Event::listen('xn/content/delete/before', array('XG_App', 'invalidateByContentType'));

/* Set up indexing on new content types after the first object of that type is saved */
XN_Event::listen('xn/content/save/after', array('XG_App', 'verifyIndexing'));
