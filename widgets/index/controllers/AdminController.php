<?php
XG_App::includeFileOnce('/lib/XG_Layout.php');

class Index_AdminController extends W_Controller {

    protected static $nonregVisibilityChoices;

    public function __construct(W_BaseWidget $widget) {
        parent::__construct($widget);
        self::$nonregVisibilityChoices = array(
                'everything' => xg_text('EVERYTHING'),
                'homepage' => xg_text('JUST_THE_HOMEPAGE'),
                'message' => xg_text('JUST_THE_SIGN_UP_PAGE'));
    }

    public function action_launch() {
        XG_SecurityHelper::redirectIfNotOwner();
        if (! XG_App::appIsLaunched()) {
            XG_App::launchApp();
        }
        $this->redirectTo('index', 'index');
    }

    public function action_manage() {
        XG_SecurityHelper::redirectIfNotAdmin();
        XG_App::includeFileOnce('/lib/XG_TabLayout.php');
        $this->tabManagerEnabled = XG_TabLayout::isEnabled();
        $app = XN_Application::load();
        $this->requestCodeUrl = 'http://' . XN_AtomHelper::HOST_APP('help') . '/?page_id=139&appUrl=' . $app->relativeUrl . ".ning.com";
        $this->premiumFeaturesUrl = 'http://' . XN_AtomHelper::HOST_APP('www') . '/home/apps/premium?appUrl=' . $app->relativeUrl;
        $forumWidget = W_Cache::getWidget('forum');
        if ($forumWidget->privateConfig['isEnabled']) {
            $this->forumManageUrl = $forumWidget->buildUrl('manage','index');
        }
        // For a better user experience we now send them to the homepage rather than somewhere that forces login, see BAZ-4930.
        $this->gyoUrl = 'http://' . XN_AtomHelper::HOST_APP('www');
        if (XG_SecurityHelper::userIsOwner()) {
            $this->haveCode = !XG_App::appIsCentralized();
        }
    }

    public function action_tracking() {
        XG_SecurityHelper::redirectIfNotAdmin();
        $mainWidget = W_Cache::getWidget('main');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ( mb_strpos($_POST['pageEnd'], '<%') === false  &&
                 mb_strpos($_POST['pageEnd'], '<?' . 'php') === false ) {
                $mainWidget->setPlugin('pageEnd', trim($_POST['pageEnd']));
                $savedVars = array('saved'=>1);
                $this->redirectTo('tracking','admin',$savedVars);
            } else {
                $this->error = 1;
                $this->pageEndCode = $_POST['pageEnd'];
            }
        } else {
            $this->pageEndCode = $mainWidget->getPlugin('pageEnd');
        }

    }

    /**
     * Editing of the apps' directory data (category, appatar, etc.)
     */
    public function action_appProfile() {
        XG_SecurityHelper::redirectIfNotOwner();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->forwardTo('saveAppProfile');
            return;
        }

        $app = XN_Application::load();
        $this->appName = $app->name;
        /* Randomize the appatar if it hasn't been set */
        $this->appIconUrl = preg_replace('@default=\d+@u', 'default=-1', $app->iconUrl());
        //  BAZ-1740 - append a cache killing parameter to force miss in browser cache
        $this->appIconUrl .= '&ck=' . rand();
        $this->appUrl = XN_AtomHelper::HOST_APP($app->relativeUrl);

        /* Default profile icon */
        $defaultAvatarUrl = W_Cache::getWidget('main')->config['defaultAvatarUrl'];
        if (!$defaultAvatarUrl) {
            $defaultAvatarUrl = xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/avatar.png'));
        }
        $this->defaultAvatar = XG_HttpHelper::addParameters($defaultAvatarUrl, array('width' => 64, 'height' => 64, 'crop' => '1:1'));

        /* Retrieve defaults for the app */
        $appInfo = XG_App::getDirectoryProfile();
        $defaults = array('name' => $appInfo['name']);
        $defaults['tags'] = isset($appInfo['tags']) ? $appInfo['tags'] : '';
        foreach (array('nonregVisibility', 'moderate', 'allowInvites',
                'allowRequests', 'description', 'tagline', 'appPrivacy') as $c) {
             $defaults[$c] = $this->_widget->config[$c];
        }
        $defaults['allowJoin'] = 'invited'; // BAZ-7228 [Andrey 2008-04-14]
        $this->form = new XNC_Form($defaults);
         XG_App::includeFileOnce('/lib/XG_LanguageHelper.php');
        $this->languages = XG_LanguageHelper::localesAndNames();
        $this->locale = XG_LOCALE;

        //  If we're in the prelaunch ('gyo') sequence, the buttons at the bottom
        //    change
        $this->displayPrelaunchButtons = !XG_App::appIsLaunched();
        if ($this->displayPrelaunchButtons) {
            $this->backLink = XG_App::getPreviousStepUrl();
            $this->nextLink = XG_App::getNextStepUrl();
        }
    }

    public function action_saveAppProfile() {
        XG_SecurityHelper::redirectIfNotOwner();
        //  Mark the step completed if we haven't yet
        if (!XG_App::allStepsCompleted()) {
            //  Mark the prelaunch step as completed if necessary
            XG_App::markStepCompleted('About Your Site');
        }

        // Flush the apps' cache of its metadata
        XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
        XG_MetatagHelper::flushCache();

        foreach (array('description', 'tagline') as $c) {
            $this->_widget->config[$c] = html_entity_decode(xg_scrub($_POST[$c]), ENT_QUOTES, 'UTF-8'); // Save as text not HTML, see BAZ-10490
        }
        XG_LanguageHelper::setCurrentLocale($_POST['locale']);

        if (!XG_App::appIsLaunched()) {
            // TODO: Delete this code, as we can never get here? [Jon Aquino 2007-09-28]
            $privacy = ((isset($_POST['appPrivacy']) && ($_POST['appPrivacy'] == 'private')) ? 'private' : 'public');
            $this->_widget->config['moderate'] = xg_scrub($_POST['moderate']);
            $this->_widget->config['appPrivacy'] = $privacy;
            if ($privacy == 'private') {
                // Private
                // can members invite others?
                $this->_widget->config['allowInvites'] = (isset($_POST['allowInvites']) && ($_POST['allowInvites'] == 'yes')) ? 'yes' : 'no';
                // can visitors request invites?
                $this->_widget->config['allowRequests'] = (isset($_POST['allowRequests']) && ($_POST['allowRequests'] == 'yes')) ? 'yes' : 'no';
                // who can join? BAZ-7228 [Andrey 2008-04-14]
                $this->_widget->config['allowJoin'] = (isset($_POST['allowJoin']) && ($_POST['allowJoin'] == 'all')) ? 'all' : 'invited';
            } else {
                // Public
                // what can nonregistered users see?
                $this->_widget->config['nonregVisibility'] =
                        (isset($_POST['nonregVisibility']) && isset(self::$nonregVisibilityChoices[$_POST['nonregVisibility']])) ?
                        $_POST['nonregVisibility'] : reset(array_keys(self::$nonregVisibilityChoices));
            }
            $this->_widget->config['moderate'] = (isset($_POST['moderate']) && ($_POST['moderate'] == 'yes')) ? 'yes' : 'no';
        }

        if ($_POST['profile_icon']) {
            XG_App::includeFileOnce('/lib/XG_FileHelper.php');
            if (XG_FileHelper::isValidImageType('profile_icon')) {
                list($avatar, $filename) = XG_FileHelper::createUploadedFileObject('profile_icon');
                $this->_widget->config['defaultAvatarUrl'] = $avatar->fileUrl('data');
                $this->_widget->config['defaultAvatarId'] = $avatar->id;
            }
        }

        $this->_widget->saveConfig();

        $postData = array(
                'application_name' => $_POST['name'],
                'application_description' => strip_tags(html_entity_decode($_POST['description'], ENT_QUOTES, 'UTF-8')),
                'application_tags' => $_POST['tags']);
        if ($_POST['icon']) {
            $tempFileName = $_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile/app-icon.tmp';
            file_put_contents($tempFileName, XN_Request::uploadedFileContents($_POST['icon']));
            $postData['application_icon'] = '@' . $tempFileName;
        }
        XN_REST::put('/xn/rest/1.0/application:' . urlencode(XN_Application::load()->relativeUrl) . '?xn_method=put', $postData, null);
        if ($tempFileName) {
        	unlink($tempFileName);
        	XG_App::includeFileOnce('/lib/XG_IPhoneHelper.php');
        	XG_IPhoneHelper::updateBookmarkIcon(); // Update iPhone icon
		}

        //  Check for an explicit success target (e.g. launch)
        if (isset($_POST['successTarget']) && mb_strlen($_POST['successTarget']) > 0) {
            $successTarget = $_POST['successTarget'];
        }
        else {
            if (XG_App::appIsLaunched()) {
                //  We're editing post-sequence - redisplay the form
                $successTarget = W_Cache::getWidget('main')->buildUrl('admin', 'appProfile', array('saved'=>1));
            } else {
                //  Redirect to the new current step
                $nextStep = XG_App::currentLaunchStepRoute();
                $successTarget = W_Cache::getWidget('main')->buildUrl($nextStep['controllerName'], $nextStep['actionName']);
            }
        }
        $this->redirectTo('updateResources','embeddable', array(
                            'application_name'=> $postData['application_name'],
                            'application_description' => $postData['application_description'],
                            'successTarget' => $successTarget));
    }

    /**
     * Static page with information on using featuring.
     */
    public function action_featuring() {

    }

}
