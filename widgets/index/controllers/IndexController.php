<?php
XG_App::includeFileOnce('/lib/XG_Layout.php');
XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
XG_App::includeFileOnce('/lib/XG_Message.php');
XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
XG_App::includeFileOnce('/lib/XG_HttpHelper.php');

class Index_IndexController extends XG_BrowserAwareController {

    /**
     * Pokes a hole in the app-wide privacy mechanism.
     *
     * @param $action string  The name of the action
     * @return boolean  Whether to bypass the privacy mechanism for the given action
     */
    public function action_overridePrivacy($action) {
        if ($action == 'asyncJob') { return true; }
        return false;
    }

    /**
     * Displays the network's main page.
     */
    public function action_index() {
        /** Cache approach index-signedout */
        if (! ($this->_user->isLoggedIn() || XG_App::getLogoutCookie())) {
            $cacheKey = md5(XG_HttpHelper::currentUrl());
            $this->setCaching(array($cacheKey), 300);
        }

        $this->app = XN_Application::load();
        $this->isMainPage = true;
        $this->showFacebookMeta = array_key_exists('from', $_GET) && ($_GET['from'] === 'fb');
        if ($this->showFacebookMeta) {
            // overloading for music?
            if (array_key_exists('fbmusic', $_GET)) {
                $this->facebookPreviewImage = xg_cdn(W_Cache::getWidget('music')->buildResourceUrl('gfx/icon/music_lg.jpg'));
            } else {
                $this->facebookPreviewImage = preg_replace('@default=\d+@u', 'default=-1', $this->app->iconUrl());
            }
        }

        $this->enabledModules = XG_ModuleHelper::getEnabledModules();
        $this->disabledModules = XG_ModuleHelper::getDisabledModules();
        $this->layoutName = 'index';
        $this->xgLayout = XG_Layout::load($this->layoutName);
        //  Use the contents of the 'layout' element within the document
        $this->layout = $this->xgLayout->getLayout()->documentElement;
        $this->layoutName = $this->xgLayout->getName();
        $this->layoutType = $this->xgLayout->getType();
    } // action_index()
    public function action_index_iphone() {
        /** Cache approach index-signedout */
        if (! ($this->_user->isLoggedIn() || XG_App::getLogoutCookie())) {
            $cacheKey = md5(XG_HttpHelper::currentUrl());
            $this->setCaching(array($cacheKey), 300);
        }
        $this->enabledModules = XG_ModuleHelper::getEnabledModules();
        $this->navEntries = XG_IPhoneHelper::getNavEntries();
    }

    /** @deprecated 2.0  Use XG_AuthorizationHelper::signUpUrl instead */
    public function action_sign() {
        header('Location: ' . XG_AuthorizationHelper::signUpUrl($this->getSuccessTarget(), $_GET['groupToJoin']));
    }

    /** @deprecated 2.0  Use XG_AuthorizationHelper::signUpUrl instead */
    public function action_signUp() {
        header('Location: ' . XG_AuthorizationHelper::signUpUrl($this->getSuccessTarget(), $_GET['groupToJoin']));
    }

    /** @deprecated 2.0  Use XG_AuthorizationHelper::signUpUrl instead */
    public function action_join() {
        header('Location: ' . XG_AuthorizationHelper::signUpUrl($this->getSuccessTarget(), $_GET['groupToJoin']));
    }

    /** @deprecated 2.0  Use XG_AuthorizationHelper::signInUrl instead */
    public function action_signIn() {
        header('Location: ' . XG_AuthorizationHelper::signInUrl($this->getSuccessTarget(), $_GET['groupToJoin']));
    }


    public function action_invite() {
        $this->redirectTo('new', 'invitation');
    }

    /**
     * What a non-owner sees before the app is launched
     */
    public function action_notLaunched() {
        if (XG_App::appIsLaunched()) {
            $this->redirectTo('index');
            return;
        }
    }

    public function action_banned() {
        $this->user = User::load($this->_user);
        if (!User::isBanned($this->user)) {
            $this->redirectTo('index', 'index');
            return;
        }

        //  Send submitted message if present / allowed
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && User::canSendBannedMessage($this->user)) {
            $opts = array();
            $opts['body'] = isset($_POST['message']) ? trim($_POST['message']) : '';
            try {
				XG_Browser::execInEmailContext(array($this,'_sendBannedMessage'), $opts, $this->_user);
            } catch (Exception $e) {
            }

            User::sentBannedMessage($this->user);
            //  Notify of success
            $this->showNotification = true;
            $this->notificationMessage = xg_text('YOUR_REQUEST_HAS_BEEN_SENT');
            $this->notificationClass = 'success';
        }
        $this->showMessageArea = User::canSendBannedMessage($this->user);

    }

	// callback for sending messages
	public function _sendBannedMessage($opts, $from) { # void
		$msg = XG_Message_From_Banned::create($opts);
		$msg->send($from);
    }


    /**
     * What someone sees if they are signed in and their membership is pending
     */
    public function action_pending() {
        if (! $this->_user->isLoggedIn()) {
            $this->redirectTo('index','index');
            return;
        }
        $this->user = User::load($this->_user);
        if (! User::isPending($this->user)) {
            $this->redirectTo('index','index');
            return;
        }
        if (XG_App::homepageIsVisible()) { $this->continueUrl = '/'; }
    }

    /**
     * What someone sees if they are signed in and their membership is pending (iPhone-specific)
     */
    public function action_pending_iphone() {
        $this->action_pending();
    }

    public function action_summary() {
        if (XG_App::appIsLaunched()) {
            $this->_widget->dispatch('index');
        }
        $this->backLink = XG_App::getPreviousStepUrl();
        $this->nextLink = XG_App::getNextStepUrl();
        $this->steps = XG_App::getLaunchbarSteps();
        $this->stateDisplayName = array(
                'complete'   => xg_html('COMPLETE'),
                'incomplete' => xg_html('INCOMPLETE')
            );
    }

	/**
	 * Allowing users to report abuse.
	 * @param $_GET/$_POST['appTitle']  title of the opensocial app to report abuse on
	 * @param $_GET/$_POST['appUrl']  url of the opensocial app to report abuse on
	 */
    public function action_reportAbuse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $opts = array('category' => $_POST['category'],
                          'appTitle' => $_POST['appTitle'],
                          'appUrl' => $_POST['appUrl'],
                          'body' => $_POST['issue'],
                          'url' => $_POST['referrer'],
                          'sendBccTo' => "misc@ning.com");
            XG_Message_ReportThis::create($opts)->send($this->_user->screenName);
            
            // @TODO: OpenSocial_MetricsHelper::log($_POST['appUrl'], $_POST['appName'], $this->_user->screenName); 
            // @TODO: OpenSocial_DirectoryHelper::log($_POST['appUrl'], $_POST['appName'], $this->_user->screenName); 
            
            //  Notify of success
            $this->showNotification = true;
            $this->notificationMessage = xg_text('THANK_YOU_YOUR_MESSAGE_HAS_BEEN_SENT');
            $this->notificationClass = 'success';

            $this->appTitle = $_POST['appTitle'];
            $this->appUrl = $_POST['appUrl'];
        } else {
            $this->appTitle = $_GET['appTitle'];
            $this->appUrl = $_GET['appUrl'];
        }

        // Define the form
        $this->formTitle = xg_html('REPORT_ABUSE');
        $this->formDescription = xg_html('USE_THIS_FORM_TO_REPORT_ABUSE', 'href="' . xnhtmlentities(W_Cache::getWidget('opensocial')->buildUrl('application', 'about', array('appUrl' => $this->appUrl))) . '"', xnhtmlentities($this->appTitle));
        $this->formCategories = array(xg_text('BUG') => 'Bug', xg_text('FRAUD') => 'Fraud', xg_text('SPAM') => 'Spam', xg_text('OTHER') => 'Other');
        $this->formHiddenElemNames = array('referrer', 'appUrl', 'appTitle');

        $defaults = array();
        $defaults['category'] = 'Other';
        $defaults['referrer'] = $_SERVER['HTTP_REFERER'];
        $defaults['appUrl'] = $this->appUrl;
        $defaults['appTitle'] = $this->appTitle;
        $this->form = new XNC_Form($defaults);

        $this->render('report');
    }
    
    /**
     * Allowing users to report an issue
     */
    public function action_report() {
        $this->ownerName = XG_FullNameHelper::fullName(XN_Application::load()->ownerName);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $opts = array('category' => $_POST['category'],
                          'body' => $_POST['issue'],
                          'url' => $_POST['referrer']);
			XG_Browser::execInEmailContext(array($this,'_sendReportMessage'), $opts);

            //  Notify of success
            $this->showNotification = true;
            $this->notificationMessage = xg_text('THANK_YOU_YOUR_MESSAGE_HAS_BEEN_SENT');
            $this->notificationClass = 'success';
        }

        // Define the form
        $this->formTitle = xg_html('REPORT_AN_ISSUE');
        $this->formDescription = xg_html('USE_THIS_FORM_TO_REPORT_ISSUE_TO_NC', 'href="http://' .$_SERVER['HTTP_HOST'] . User::quickProfileUrl(XN_Application::load()->ownerName) . '"', xnhtmlentities($this->ownerName), 'href="http://' . $_SERVER['HTTP_HOST'] . '"', xnhtmlentities(XN_Application::load()->name));
        $this->formCategories = array(xg_text('ADULT') => 'Adult', xg_text('ABUSIVE') => 'Abusive', xg_text('BUG') => 'Bug', xg_text('FRAUD') => 'Fraud', xg_text('SPAM') => 'Spam', xg_text('OTHER') => 'Other');
        $this->formHiddenElemNames = array('referrer');

        $defaults = array();
        $defaults['category'] = 'Other';
        $defaults['referrer'] = $_SERVER['HTTP_REFERER'];
        $this->form = new XNC_Form($defaults);
    }

	// callback for sending messages
    public function _sendReportMessage($opts) { # void
		XG_Message_ReportThis::create($opts)->send($this->_user->screenName);
    }


    public function action_feedback() {
        $this->ownerName = XG_FullNameHelper::fullName(XN_Application::load()->ownerName);
        if (XN_Profile::current()->isLoggedIn()) {
            $feedbackSenderName = XG_UserHelper::getFullName($this->_user);
        } else {
            $feedbackSenderName = xg_text('SOMEBODY');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $heading = xg_text('X_PROVIDED_THE_FOLLOWING_FEEDBACK_ON_Y', $feedbackSenderName, XN_Application::load()->name);
            $body = $_POST['feedback'];
			XG_Browser::execInEmailContext(array($this, '_sendFeedbackMessage'), $body, $heading);

            //  Notify of success
            $this->showNotification = true;
            $this->notificationMessage = xg_text('THANK_YOU_YOUR_MESSAGE_HAS_BEEN_SENT');
            $this->notificationClass = 'success';
        }
        $this->form = new XNC_Form();
        if (! $this->_user->isLoggedIn()) {
            $signUrl = XG_AuthorizationHelper::signInUrl();
            header("Location: $signUrl");
            exit();
        }
    }

	// callback for sending messages
	public function _sendFeedbackMessage($body, $heading) { # void
		XG_Message_Feedback::create(array('body' => $body, 'heading' => $heading))->send($this->_user->screenName);
    }

    /**
     * The /xn/detail/ID URL mapping should be mapped to this action, so that
     * the action can look up the mozzle that owns the content object, see if
     * that mozzle has an index/detail action, and then dispatch that action.
     */
    public function action_detail() {
        // If we can't find a URL, then just go to the homepage
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $dispatched = false;
        try {
            if (! isset($_GET['id'])) {
                throw new Exception("No content object ID specified");
            }
            if (mb_strpos($_GET['id'], 'u_') === 0) {
                $screenName = str_replace('/', '', mb_substr($_GET['id'], 2)); // Remove trailing slash if necessary [Jon Aquino 2007-10-11]
                header('Location: ' . XG_HttpHelper::addParameter(xg_absolute_url(User::profileUrl($screenName)), 'xgp', $_GET['xgp']));
                exit;
            }
            if (mb_strpos($_GET['id'], 'f_') === 0) {
                $screenName = str_replace('/', '', mb_substr($_GET['id'], 2)); // Remove trailing slash if necessary [Jon Aquino 2007-10-11]
                header('Location: ' . xg_absolute_url('/friends/' . User::profileAddress($screenName)));
                exit;
            }
            $object = XN_Content::load($_GET['id']);
            if ($object && $object->my->mozzle) {
                // If the object is owned by the main mozzle then figure out
                // what to do right here.
                if ($mozzle == 'main') {
                    if ($object->type == 'User') {
                        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/profile/' . User::profileAddress($object->contributorName);
                    }
                }
                // Otherwise, hand off control to index/detail in the appropriate
                // mozzle
                else {
                    $mozzle = W_Cache::getWidget($object->my->mozzle);
                    if (in_array('index', $mozzle->getControllerNames()) && ($mozzle->controllerHasAction('index','detail'))) {
                        $mozzle->dispatch('index','detail', array($object));
                        $dispatched = true;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("/xn/detail problem: " . $e->getMessage());
            header("Location: $url");
            exit();
        }
        if (! $dispatched) {
            header("Location: $url");
            exit();
        }

    }

    /**
     * When a user signs out, we clean things up and take them to a special
     * version of the homepage (BAZ-1236). This action should be the target of
     * all sign out activity (e.g. where identity will redirect the user after
     * actually signing them out. In this action, we do any necessary clean up,
     * such as setting a special 'just logged out'
     * cookie, and redirect to the homepage. The homepage, if it notices the
     * special 'just logged out' cookie, unsets that cookie and displays the
     * "you have just signed out" message. (Putting the just-signed-out status
     * in a cookie keeps it out of the URL and potential bookmarks, etc.
     *
     * @deprecated 2.0  Use XG_AuthorizationHelper::signOutUrl instead
     */
    public function action_signOut() {
        header('Location: ' . XG_AuthorizationHelper::signOutUrl($_GET['target'] ? $_GET['target'] : xg_absolute_url('/')));
    }

    /**
     * Outputs the app's locale string - should be called with ?xn_auth=no
     */
    public function action_getLocale() {
        echo $this->_widget->config['locale'];
    }

    protected function getSuccessTarget() {
        $target = NULL;
        if ($_GET['target']) {
            $target = $_GET['target'];
        // Use referrer as target only if it's within this application!  BAZ-2481
        } else if (isset($_SERVER['HTTP_REFERER'])) {
            $parts = parse_url($_SERVER['HTTP_REFERER']);
            $host = $parts['host'];
            if ((!$host) || ($host == $_SERVER['HTTP_HOST'])) {
                $target = $_SERVER['HTTP_REFERER'];
            }
        }
        if (!$target) {
            $target = $_SERVER['HTTP_HOST'];
        }
        if (mb_substr($target, 0, 7) != 'http://') {
            $target = 'http://' . $target;
        }
        return $target;
    }

    /**
     * The SearchController in the main widget is interested to know what this widget
     * has to say about app-wide search queries. @see BAZ-3821
     *
     * @param $query XN_Query The query object to modify
     */
    public function action_annotateSearchQuery($query) {
        /* Exclude banned/blocked users (BAZ-4024) */
        User::addBlockedFilter($query, true);
        /* Exclude pending users (BAZ-4427) */
        User::addPendingFilter($query, true);
         /* Exclude unfinished users (BAZ-8509) */
        User::addUnfinishedFilter($query, true);
    }

    /**
     * Log an error message for BAZ-8252
     */
    public function action_logBaz8252() {
        if ($_POST['marker']) {
            if (is_null(XN_Cache::get($_POST['marker']))) {
                error_log('BAZ-8252 [AJAX]; marker:[' . $_POST['marker'] . '] not replaced on [' . getenv('HTTP_REFERER') . ']; User: [' . XN_Profile::current()->screenName . ']');
            }
            XN_Cache::put($_POST['marker'], '1');
        }
        $this->render('blank');
    }

    /**
     * Stub just to render the error template
     */
    public function action_error() { }

    /**
     * Stub just to render the error template (iPhone-specific)
     */
    public function action_error_iphone() { }

    /**
     * Called by an XN_Task.
     */
    public function action_asyncJob() { # void
        // TODO: Set HTTP status code to 500 on failure? [Jon Aquino 2008-03-21]
        XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        $this->_asyncJobCompleted = 0;
        register_shutdown_function(array($this,'_trackAsyncJobFailures'));
        XG_JobHelper::dispatch($_REQUEST);
        $this->_asyncJobCompleted = 1;
    }

    //
    public function _trackAsyncJobFailures () { # void
        if (!$this->_asyncJobCompleted) {
            error_log("AsyncJob failure:".var_export($_REQUEST,TRUE));
        }
    }
}
