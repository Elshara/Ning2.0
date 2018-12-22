<?php
XG_App::includeFileOnce('/lib/XG_Embed.php');
XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');

/**
 * Dispatches requests pertaining to page modules, also known as "embeds".
 * These <div> elements typically have the "xg_embed" CSS class.
 */
class Index_EmbedController extends XG_BrowserAwareController {

    const DEFAULT_SIDEBAR_CACHING_TTL = 300;
    const MIN_SIDEBAR_CACHING_INTERVAL = 30;

    /**
     * @param   $highlight                      Optional
     * @param   $title                  string  Optional
     * @param   $moduleCssFiles                 Optional
     * @param   $typographyCssUrl               Optional
     * @param   $profileThemeCssUrl     string  Optional URL for the theme CSS chosen by the owner of the page
     * @param   $profileCustomCssUrl    string  Optional URL for the custom CSS chosen by the owner of the page
     * @param   $options                array   Optional assoc. array of additional options.
     *                                          Possible values include:
     *                                              showFacebookMeta
     *                                              facebookPreviewImage
     *                                              xgDivClass
     *                                              userObject
     *                                              hideAdColors
     *                                              hideNingbar
     *                                              noHead
     *                                              forceDojo
     *                                              loadJQueryUi
     *                                              isMainPage - boolean, is this header for the network main page.
     */
    public function action_header($highlight = NULL, $title = NULL, $moduleCssFiles = NULL,
                  $typographyCssUrl = NULL, $profileThemeCssUrl = NULL, $profileCustomCssUrl = NULL, $options = NULL) {

        $isAdmin = XG_SecurityHelper::userIsAdmin();
        //  Load this later only if needed
        $appInfo = NULL;

        $this->typographyCssUrl = $typographyCssUrl;

        XG_App::includeFileOnce('/lib/XG_HtmlLayoutHelper.php');
        /*  Refer to custom.css only if it's available - if we refer to an unavailable
         *    CSS file Safari will parse the 404 page for CSS!
         */
        $widget = W_Cache::getWidget('main');
        $widget->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
        $customCssPath = Index_AppearanceHelper::getCustomCssUrl();
        $this->includeCustomCss = (XG_App::appIsLaunched() && is_readable($_SERVER['DOCUMENT_ROOT'] . $customCssPath));
        $this->customCssUrl = $customCssPath;

        //  Callers of this action can supply module or profile CSS files which
        //    go in specific places
        $this->moduleCssFiles = $moduleCssFiles;
        $this->profileThemeCssUrl = $profileThemeCssUrl;
        $this->profileCustomCssUrl = $profileCustomCssUrl;
        $this->showFacebookMeta = $options['showFacebookMeta'];
        $this->facebookPreviewImage = $options['facebookPreviewImage'];
        $this->displayHeader = TRUE;
        $this->displayLaunchBar = FALSE;
        $this->displayBlankLaunchBar = FALSE;
        $this->displayInvitePanelButton = FALSE;
        $this->xgDivClass = $options['xgDivClass'];
        $this->userObject = $options['userObject'];
        $this->hideAdColors = $options['hideAdColors'];
        $this->hideNingbar = $options['hideNingbar'];
        $this->noHead = $options['noHead'];
        $this->app = XN_Application::load();
        $this->forceDojo = $options['forceDojo'];
        $this->loadJQueryUi = $options['loadJQueryUi'];
        $this->isMainPage = $options['isMainPage'];

        /*
         * See BAZ-2808.  We need to read the sitewide theme CSS to see if the
         *   ning logo is hidden there - if so, we need to hide it ourselves on
         *   member pages, as the sitewide theme is no longer applied there
         */
        if (mb_strlen($profileThemeCssUrl) > 0) {
            $ningLogoDisplay = '';
            if (array_key_exists('ningLogoDisplay', $widget->config)) {
                $ningLogoDisplay = $widget->config['ningLogoDisplay'];
            } else {
                $defaults = array();
                $imagePaths = array();
                Index_AppearanceHelper::getAppearanceSettings(NULL, $defaults,$imagePaths);
                $ningLogoDisplay = $defaults['ningLogoDisplay'];
            }
            $this->hideNingLogo = (mb_substr($ningLogoDisplay, 0, 4) == 'none');
        } else {
            //  the logo will be hidden by the sitewide theme CSS if desired
            $this->hideNingLogo = false;
        }

        if (isset($options['blankLaunchBar'])) {
            $this->displayBlankLaunchBar = TRUE;
            $options['hideLaunchBar'] = TRUE;
        }

        if (isset($options['displayHeader'])) {
            $this->displayHeader = $options['displayHeader'];
        }
        $this->hideNavigation = (isset($options['hideNavigation']));

        // <meta/> description and keywords (BAZ-1025 and friends)
        if (isset($options['metaDescription'])) {
            // false means "don't display a meta description element
            if ($options['metaDescription'] === false || $options['metaDescription'] === '' || is_null($options['metaDescription'])) {
                $this->metaDescription = false;
            }
            // Anything else means "use this as the meta description"
            else {
                $this->metaDescription = XG_MetatagHelper::forDescription($options['metaDescription']);
            }
        } else {
            //  Default to the description set by the app owner on the directory
            //    profile page
            $this->metaDescription = XG_MetatagHelper::forDescription(XG_MetatagHelper::appDescription());
        }
        if (empty($this->metaDescription) && $this->metaDescription !== false) {
            $this->metaDescription = xg_text('S_IS_A_SOCIAL_NETWORK_ON_NING', $this->app->name);
        }

        if (isset($options['metaKeywords'])) {
            // false means "don't display a meta keywords element
            if ($options['metaKeywords'] === false || $options['metaKeywords'] === '' || is_null($options['metaKeywords'])) {
                $this->metaKeywords = false;
            }
            // Anything else means "use this as the meta keywaords"
            else {
                $this->metaKeywords = XG_MetatagHelper::forMetatags($options['metaKeywords']);
            }
        } else {
            //  Default to app keywords
            $this->metaKeywords = XG_MetatagHelper::forMetatags(XG_MetatagHelper::appTags());
        }
        if (isset($options['metaTitle'])) { // for facebook BAZ-7483
            $this->metaTitle = $options['metaTitle'];
        }
        if (isset($options['videoThumbnail'])) {
            $this->videoThumbnail = $options['videoThumbnail'];
        }
        if (isset($options['pageImage'])) {
            $this->pageImage = $options['pageImage'];
        }
        if (isset($options['videoWithPlayer'])) {
            $this->videoWithPlayer = $options['videoWithPlayer'];
        }
        if (isset($options['videoHeight'])) {
            $this->videoHeight = $options['videoHeight'];
        }
        if (isset($options['videoWidth'])) {
            $this->videoWidth = $options['videoWidth'];
        }
        if (!XG_App::appIsLaunched() && !isset($options['hideLaunchBar'])) {
            //  If we're not yet launched, show launch bar
            $this->displayLaunchBar = TRUE;
            $this->includeCustomCss = FALSE;
            $this->prelaunchSteps = XG_App::getLaunchbarSteps();
            $this->requestedStep = XG_App::getRequestedStep();
            $this->backLink = XG_App::getPreviousStepUrl();
            $this->nextLink = XG_App::getNextStepUrl();
        }

        //  If prelaunch steps have not been completed, we use the default theme
        //    rather than the app's custom CSS
        //  Also hide the standard app header
        if (!XG_App::appIsLaunched()) {
            $this->userCssFilename = $this->_widget->buildResourceUrl('css/theme-ning.css');
            $this->displayHeader = FALSE;
        }
        else {
            //  User CSS filename changes with every update to avoid cache problems
            $filename = Index_AppearanceHelper::getThemeCssUrl();
            $filepath = $_SERVER['DOCUMENT_ROOT'] . $filename;
            $startTime = time();

            //  newly updated CSS might not be available yet if NFS is really slow -
            //  wait for it for a bit
            if (!is_readable($filepath)) {
                error_log("Waiting to read unavailable user CSS file " . $filename . "...");
                while (!is_readable($filepath) && ((time() - $startTime) < 3)) {
                    usleep(500000);
                    clearstatcache();
                }
            }

            //  If we timed out, log an error
            if (!is_readable($filepath)) {
                error_log("Timed out attempting to read user CSS file " . $filename);
                $filename = '/xn_resources/widgets/index/css/themes/Blue%20Jeans.css';
            }

            $this->userCssFilename = $filename;
        }

        if ($this->displayHeader) {
            if ($this->_widget->config['logoImageUrl']) {
                $this->logoImage = $this->_widget->config['logoImageUrl'];
            } else {
                $this->logoImage = NULL;
            }
            $profilesWidget = W_Cache::getWidget('profiles');
            $this->navEntries = XG_ModuleHelper::getNavEntries($isAdmin);
            $this->navHighlight = (isset($highlight) ? $highlight : 'home');
            if ($this->navEntries['method'] == XG_ModuleHelper::TABS_TAB_MANAGER) {
                $this->navHighlight = XG_ModuleHelper::getNavHighlightForTabManager($this->navEntries['tabs'], $this->navHighlight);
            }
            $this->tagline = $this->_widget->config['tagline'];
        }

        if (is_null($title)) {
            $this->title = $this->app->name;
        } else {
            $this->title = $title;
        }

        /* BAZ-4641: if the user's logged in and there's a user object for them,
         * use the app-specific full name and thumbnail URL for them in the
         * Ningbar
         */
        if ($this->_user->isLoggedIn()) {
            $this->fullNameForNingbar = XG_UserHelper::getFullName($this->_user);
            $this->thumbnailUrlForNingbar = str_replace("'","\\'",XG_UserHelper::getThumbnailUrl($this->_user, null, null));
        }
        else {
            $this->fullNameForNingbar = $this->thumbnailUrlForNingbar = '';
        }
    }
    //
    public function action_header_iphone($highlight = NULL, $title = NULL, $moduleCssFiles = NULL,
                  $typographyCssUrl = NULL, $profileThemeCssUrl = NULL, $profileCustomCssUrl = NULL, $options = NULL) {
        $this->action_header($highlight, $title, $moduleCssFiles, $typographyCssUrl, $profileThemeCssUrl, $profileCustomCssUrl, $options);
        $this->largeIcon = $options['largeIcon'];
        $this->contentClass = $options['contentClass'];
        $this->user = $options['user'];

        $isAdmin = XG_SecurityHelper::userIsAdmin();
        //  Load this later only if needed
        $appInfo = NULL;

        $this->typographyCssUrl = $typographyCssUrl;

        XG_App::includeFileOnce('/lib/XG_HtmlLayoutHelper.php');
        /*  Refer to custom.css only if it's available - if we refer to an unavailable
         *    CSS file Safari will parse the 404 page for CSS!
         */
        $widget = W_Cache::getWidget('main');
        $widget->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
        $customCssPath = Index_AppearanceHelper::getCustomCssUrl();
        $this->includeCustomCss = is_readable($_SERVER['DOCUMENT_ROOT'] . $customCssPath);
        $this->customCssUrl = $customCssPath;
        $widget->includeFileOnce('/lib/helpers/Index_AppearanceHelperIPhone.php');
        $this->themeCSSUrl = Index_AppearanceHelperIPhone::getThemeCSSFilename();
        $this->bookmarkIconUrl = XG_IPhoneHelper::bookmarkIconUrl();

        //  Callers of this action can supply module or profile CSS files which
        //    go in specific places
        $this->moduleCssFiles = $moduleCssFiles;
        $this->profileThemeCssUrl = $profileThemeCssUrl;
        $this->profileCustomCssUrl = $profileCustomCssUrl;
        $this->displayHeader = TRUE;

        if (isset($options['displayHeader'])) {
            $this->displayHeader = $options['displayHeader'];
        }
        $this->hideNavigation = (isset($options['hideNavigation']));

        // <meta/> description and keywords (BAZ-1025 and friends)
        if (isset($options['metaDescription'])) {
            // false means "don't display a meta description element
            if ($options['metaDescription'] === false || $options['metaDescription'] === '' || is_null($options['metaDescription'])) {
                $this->metaDescription = false;
            }
            // Anything else means "use this as the meta description"
            else {
                $this->metaDescription = XG_MetatagHelper::forDescription($options['metaDescription']);
            }
        } else {
            //  Default to the description set by the app owner on the directory
            //    profile page
            $this->metaDescription = XG_MetatagHelper::forDescription(XG_MetatagHelper::appDescription());
        }
        if (isset($options['metaKeywords'])) {
            // false means "don't display a meta keywords element
            if ($options['metaKeywords'] === false || $options['metaKeywords'] === '' || is_null($options['metaKeywords'])) {
                $this->metaKeywords = false;
            }
            // Anything else means "use this as the meta keywaords"
            else {
                $this->metaKeywords = XG_MetatagHelper::forMetatags($options['metaKeywords']);
            }
        } else {
            //  Default to app keywords
            $this->metaKeywords = XG_MetatagHelper::forMetatags(XG_MetatagHelper::appTags());
        }

        //  If prelaunch steps have not been completed, we use the default theme
        //    rather than the app's custom CSS
        //  Also hide the standard app header
        if (!XG_App::appIsLaunched()) {
            $this->userCssFilename = $this->_widget->buildResourceUrl('css/theme-ning_iphone.css');
        }
        else {
            //  User CSS filename changes with every update to avoid cache problems
            $filename = Index_AppearanceHelper::getThemeCssUrl();
            $filepath = $_SERVER['DOCUMENT_ROOT'] . $filename;
            $startTime = time();

            //  newly updated CSS might not be available yet if NFS is really slow -
            //  wait for it for a bit
            if (!is_readable($filepath)) {
                error_log("Waiting to read unavailable user CSS file " . $filename . "...");
                while (!is_readable($filepath) && ((time() - $startTime) < 3)) {
                    usleep(500000);
                    clearstatcache();
                }
            }

            //  If we timed out, log an error
            if (!is_readable($filepath)) {
                error_log("Timed out attempting to read user CSS file " . $filename);
                $filename = '/xn_resources/widgets/index/css/themes/Blue%20Jeans.css';
            }

            $this->userCssFilename = $filename;
        }

        if ($this->displayHeader) {
            $this->navEntries = XG_IPhoneHelper::getNavEntries($this->user);
        }

        if (is_null($title)) {
            $this->title = $this->app->name;
        } else {
            $this->title = $title;
        }
    }

    public function action_footer($extraHtml=null, $options = null) {
        $this->app = XN_Application::load();
        $this->cloneLink = 'http://' . XN_AtomHelper::HOST_APP('www');
        $this->ownerProfile = XG_Cache::profiles($this->app->ownerName);

        //  Get widget names for xg.* prefixes
        $this->widgets = XG_ModuleHelper::getAllModules();

        $this->extraHtml = $extraHtml;
        $this->hideLinks = ! XG_App::appIsLaunched();
        $this->ningBranding = ! XG_App::protectYourNetwork();
        $this->displayFooter = $options['displayFooter'] !== false;
        // Setting parseWidgets to false can speed up the page load for large pages
        // with no Dojo widgets [Jon Aquino 2007-12-04]
        $this->parseWidgets = $options['parseWidgets'] !== false;

        /* BAZ-4567: sync some User objects with system profiles */
        XG_UserHelper::syncMapWithProfiles();
    }
    //
    public function action_footer_iphone($extraHtml=null, $options = null) {
        $this->contentClass = $options['contentClass'];
        $this->regularPageUrl = isset($options['regularPage']) ? $options['regularPage'] : XG_HttpHelper::currentUrl();
        if ($this->regularPageUrl) {
            $this->regularPageUrl = XG_Browser::browserUrl('desktop', $this->regularPageUrl);
        }
        if ($_GET['notification']) $this->notification = $_GET['notification'];
        $this->userProfile = XN_Profile::current();
        $this->action_footer($extraHtml, $options);
    }

    /* For actions outside of the index mozzle (add profile) that
     * need to render the back/next button
     */
    public function action_backNext($nextText = null) {
        $this->backLink = XG_App::getPreviousStepUrl();
        $this->nextLink = XG_App::getNextStepUrl();
        $this->nextText = is_null($nextText) ? xg_html('NEXT') : $nextText;
    }

    /**
     * Renders the sitewide sidebar embeds.
     *
     * @param boolean $onlySitewide  render only the site-wide sidebar?
     * @param boolean $isMemberProfilePage  are we rendering the user's profile page?
     * @param boolean $onlyUserBox  render only the Hello, User box?
     */
    public function action_sidebar($onlySitewide = true, $isMemberProfilePage = false, $onlyUserBox = false) {
        // render non-cacheable sidebar embeds (like hello,username and pending embeds)
        ob_start();
        XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
        XG_LayoutHelper::renderNonCacheableSidebarEmbeds($this);
        $this->sidebarNonCacheableHtml = ob_get_clean();

    if (! $onlyUserBox) {
        $cacheKey = XG_SecurityHelper::userIsAdmin() ?
                XG_LayoutHelper::ADMIN_SIDEBAR_CACHE_KEY :
                ('sidebar-' . XN_Profile::current()->screenName);
        $sidebarCachingTtl = array_key_exists('sidebarCachingTtl', $this->_widget->privateConfig) ?
                    $this->_widget->privateConfig['sidebarCachingTtl'] :
                    self::DEFAULT_SIDEBAR_CACHING_TTL;
        $sidebarAdminNoCache = array_key_exists('sidebarAdminNoCache', $this->_widget->privateConfig) ?
                    ($this->_widget->privateConfig['sidebarAdminNoCache'] == '1') :
                    false;

        // do not allow NC of centralized apps to set a really low TTL
        $enableCaching = (($sidebarCachingTtl >= self::MIN_SIDEBAR_CACHING_INTERVAL) &&
                  (! XG_SecurityHelper::userIsAdmin() || (XG_SecurityHelper::userIsAdmin() && ! $sidebarAdminNoCache)));

        // try the cache first
        if ($enableCaching) { $sidebarCache = XN_Cache::get($cacheKey, $sidebarCachingTtl); }

        if (is_null($sidebarCache) || ! $enableCaching) {
        // cache entry does not exist so rebuild it

        // enable capturing of caching dependencies (ningLoaderRequire and addToSection)
        XG_App::startSidebarRendering();

        ob_start();
        // render the sitewide modules
        XG_LayoutHelper::renderSidebarEmbeds($this, true);
        $sidebarSitewideHtml = ob_get_clean();
        $sitewideDeps = XG_App::getSidebarCachingDependencies();

        // end capturing
        XG_App::endSidebarRendering();

        // separate dependencies for sitewide vs. non-sitewide embeds
        // enable capturing of caching dependencies (ningLoaderRequire and addToSection)
        XG_App::startSidebarRendering();

        ob_start();
        // always render the non-sitewide modules so we can stuff the contents into the cache
        XG_LayoutHelper::renderSidebarEmbeds($this, false);
        $sidebarNonSitewideHtml = ob_get_clean();
        $nonSitewideDeps = XG_App::getSidebarCachingDependencies();

        // end capturing
        XG_App::endSidebarRendering();

        // cache the sidebar
        $sidebarCache = array('sitewide' => array('html' => $sidebarSitewideHtml, 'deps' => $sitewideDeps),
                      'nonSitewide' => array('html' => $sidebarNonSitewideHtml, 'deps' => $nonSitewideDeps));
        if ($enableCaching) { XN_Cache::put($cacheKey, $sidebarCache); }
        } else {
        // cached

        // deal with sitewide sidebar dependencies
        foreach ((array)$sidebarCache['sitewide']['deps']['addToSection'] as $section) {
            XG_App::addToSection($section);
        }
        foreach ((array)$sidebarCache['sitewide']['deps']['addToCssSection'] as $section) {
            XG_App::addToCssSection($section);
        }
        call_user_func_array(array('XG_App', 'ningLoaderRequire'), $sidebarCache['sitewide']['deps']['ningLoaderRequire']);

        if (! $onlySitewide) {
            // deal with non-sitewide sidebar dependencies
            foreach ((array)$sidebarCache['nonSitewide']['deps']['addToSection'] as $section) {
            XG_App::addToSection($section);
            }
            foreach ((array)$sidebarCache['nonSitewide']['deps']['addToCssSection'] as $section) {
            XG_App::addToCssSection($section);
            }
            call_user_func_array(array('XG_App', 'ningLoaderRequire'), $sidebarCache['nonSitewide']['deps']['ningLoaderRequire']);
        }
        }

        $this->onlySitewide = $onlySitewide;

        if (XG_App::membersCanCustomizeLayout() && $isMemberProfilePage) {
        // all modules in the sidebar on frink member profile pages should have no_drag class
        $sidebarCache['sitewide']['html'] = XG_LayoutHelper::modifyModuleEmbedClasses('+no_drag', $sidebarCache['sitewide']['html'], '<div class="xg_handle">No Drag</div>');
        $sidebarCache['nonSitewide']['html'] = XG_LayoutHelper::modifyModuleEmbedClasses('+no_drag', $sidebarCache['nonSitewide']['html'], '<div class="xg_handle">No Drag</div>');
        } else {
        // remove the no_drag class from all other pages as its not needed
        $sidebarCache['sitewide']['html'] = XG_LayoutHelper::modifyModuleEmbedClasses('-no_drag', $sidebarCache['sitewide']['html']);
        $sidebarCache['nonSitewide']['html'] = XG_LayoutHelper::modifyModuleEmbedClasses('-no_drag', $sidebarCache['nonSitewide']['html']);
        }
        $this->sidebarSitewideHtml = $sidebarCache['sitewide']['html'];
        $this->sidebarNonSitewideHtml = $sidebarCache['nonSitewide']['html'];
    } else {
        // case where only the user box is rendered
        $this->onlySitewide = true;
        $this->sidebarSitewideHtml = $this->sidebarNonSitewideHtml = '';
    }
    }

    /**
     * Action which renders either the sidebar box describing the logged in user
     *   or a sign in box if no user is signed in
     * Designed to be called from the sidebar action
     */
    public function action_sidebarUserBox() {
        //  Display announcement if there's one to display (BAZ-2654)
        XG_App::includeFileOnce('/lib/XG_Announcement.php');
        $announcementInfo = XG_Announcement::getAnnouncement();
        if ($announcementInfo) {
            $this->announcementId = $announcementInfo[0];
            $this->announcement = $announcementInfo[1];
        }
        $this->approvalLinks = array();
        foreach(XG_ModuleHelper::getEnabledModules() as $module) {
            if ($module->controllerHasAction('index', 'approvalLink')) {
                $result = $module->capture('index', 'approvalLink');
                if ($result) {
                    list($retval, $output) = $result;
                    $output = trim($output);
                    // Don't add output if it just contains the template start / end comments [Jon Aquino 2006-11-30]
                    if (preg_match('/<a/u', $output)) { $this->approvalLinks[] = $output; }
                }
            }
        }
        if (User::isMember($this->_user)) {
            $profilesWidget = W_Cache::getWidget('profiles');
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
            $profilesWidget->includeFileOnce('/lib/helpers/Profiles_MessageHelper.php');
            $this->numFriendRequests = Profiles_CachedCountHelper::instance()->getApproximateReceivedFriendRequestCount();
            $this->numUnreadMsgs = Profiles_MessageHelper::getInboxUnreadMessageCount();
            $this->numUnreadAlertsMsgs = Profiles_MessageHelper::getAlertsUnreadMessageCount();
        }
    }

    /** For actions outside of the index mozzle that need to render the
     * next button for the join-the-app flow
     */
    public function action_joinBackNext($backLabel = NULL, $nextLabel = NULL) {
        $this->backLabel = $backLabel;
        $this->nextLabel = $nextLabel;
    }

    /** A message that appears until hidden */
    public function action_tempMessage($args) {
        $embed = $args['embed'];
        $visible = $embed->get('visible');
        if (! ($visible && $embed->isOwnedByCurrentUser())) {
            $this->render('blank');
            return;
        }
        $this->embedLocator = $embed->getLocator();
        $this->message = $embed->get('message');
    }

    public function action_hideTempMessage() {
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not layout owner.'); }
        $embed->set('visible', 0);
    }

    /** A message for just-logged-out folks */
    public function action_justLoggedOut() {
        if (XG_App::getLogoutCookie()) {
            XG_App::setLogoutCookie(false);
        } else {
            $this->render('blank');
        }
    }

    public function action_embed1siteDescription() {
        $this->description = XG_MetatagHelper::appDescription();
    }

    public function action_embed2siteDescription() {
        $this->forwardTo('embed1siteDescription');
    }

    public function action_embed1you() {
        //TODO This item has several names.  Rationalize?
        $this->forwardTo('sidebarUserBox');
    }

    public function action_embed1ads() {
        $this->render('_ad');
    }

    public function action_embed2ads() {
        $this->forwardTo('embed1ads');
    }

    public function action_embed1createdBy() {
        $this->app = XN_Application::load();
        if (XG_SecurityHelper::userIsOwner()) {
            $this->owner = $this->_user;
        }
        else {
            $this->owner = XG_Cache::Profiles($this->app->ownerName);
        }
        $this->route = XG_App::getRequestedRoute();
        $this->render('_networkCreator');
    }

    public function action_error() {
        $this->render('blank');
    }

    /** The welcome message that appears until hidden */
    public function action_embed2welcome($args) {
        if (! $this->_user->isLoggedIn()) { return $this->render('blank'); }
        if (! $this->_user->isOwner()) {
            if (User::isMember($this->_user)) {
                return $this->forwardTo('embed2welcomeMember');
            }
            return $this->render('blank');
        }

        $this->embed = $args['embed'];
        $this->embedLocator = $this->embed->getLocator();
        if (! $this->embed->get('visible') && ! $_GET['test_welcome']) {
            return $this->render('blank');
        }
    }

    /**
     * Displays a welcome box for a new member.
     * Assumes that the current user is a member of the network.
     */
    public function action_embed2welcomeMember() {
        if (User::load($this->_user)->my->showWelcomeBox !== 'Y') { return $this->render('blank'); }
        $this->render('embed2welcome');
    }

    public function action_welcomeSetValues() {
        if ($this->_user->isOwner()) { return XG_Embed::load($_GET['id'])->set('visible', 0); }
        if ($user = User::load($this->_user)) {
            $user->my->showWelcomeBox = null;
            $user->save();
        }
    }

    /**
     * Called asynchronously via dojo with xn_out=json to acknowledge an
     *   announcement when a link in it is clicked
     */
    public function action_acknowledgeAnnouncement() {
        if (!isset($_POST['id'])) {
            $this->error = 'No ID specified';
            return;
        }
        $id = $_POST['id'];
        XG_App::includeFileOnce('/lib/XG_Announcement.php');
        XG_Announcement::acknowledge($_POST['id']);
    }

    /**
     * Displays a module promoting network badges
     */
    public function action_getBadge() {
    }

    /** For actions outside of the index mozzle that need to render the
     *  Hello X, (signout) link in the manage pages
     */
    public function action_signOut() {
    }

}