<?php
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');

class Profiles_AppearanceController extends W_Controller {

    protected function _before() {
        XG_App::enforceMembership('index','index');
    }

    public function action_edit() {
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_SecurityHelper::redirectToSignInPageIfSignedOut();
        if (! XG_App::membersCanCustomizeTheme()) {
            XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
            $this->redirectTo(XG_HttpHelper::profileUrl($this->_user->screenName));
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->forwardTo('save');
            return;
        }
        if (isset($_GET['saved']) && $_GET['saved']) {
            $this->showNotification = true;
            $this->notificationTitle = xg_text('SUCCESS_EXCLAMATION');
            $this->notificationMessage = xg_text('YOUR_CHANGES_HAVE_BEEN_SAVED');
            $this->notificationClass = 'success';
        }

        $this->app = XN_Application::load();
        $this->defaults = array();
        $this->imagePaths = array();
        Index_AppearanceHelper::getAppearanceSettings($this->_user, $this->defaults,
                $this->imagePaths);
        $this->fontOptions = Index_AppearanceHelper::getFontAlternatives();
        $this->themes = Index_AppearanceHelper::getThemeNames();
        $this->mainWidget = W_Cache::getWidget('main');
        
        $this->submitUrl = $this->_widget->buildUrl('appearance', 'edit');

        //TODO: This is redundant because it is now in outputEditAppearancePage.
        // However, perhaps philosophically all this kind of stuff should live in controllers.
        // That said, that means having it in two places instead of one.  So perhaps not.
        //$this->hideNetworkName = ($this->mainWidget->config['logoImageUrl'] ? TRUE : FALSE);

        //  Are we in the join flow?
        $joinTarget = isset($_GET['joinTarget']) ? $_GET['joinTarget'] : ( isset($_POST['joinTarget']) ? $_POST['joinTarget'] : null);
        if (isset($joinTarget)) {
            $this->inJoinFlow = true;
            $defaults['joinTarget'] = $joinTarget;
        } else {
            $this->inJoinFlow = false;
        }

        $this->form = new XNC_Form($defaults);

    }  // action_edit()


    public function action_save() {
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_SecurityHelper::redirectToSignInPageIfSignedOut();
        if (! XG_App::membersCanCustomizeTheme()) {
            XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
            $this->redirectTo(XG_HttpHelper::profileUrl($this->_user->screenName));
        }
        //  Save appearance settings from post data
        Index_AppearanceHelper::setAppearanceSettings($this->_user, $_POST);
        
        /* If joinTarget is set, then we're in the join-app flow, so the next
         * step is the add-content page (with the joinTarget sent along to that (BAZ-947) */
        if (isset($_POST['joinTarget'])) {
            $url = W_Cache::getWidget('main')->buildUrl('content','content',
                    array('joinTarget' => $_POST['joinTarget']));
            header("Location: $url");
            exit;
        } else {
            // BAZ-1241: back to your page
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ActivityLogHelper.php');
            Profiles_ActivityLogHelper::createProfileUpdateItem($this->_user->screenName);
            XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
            $url = XG_HttpHelper::profileUrl($this->_user->screenName);
            header("Location: $url");
            exit;
        }

    }  // action_save()

}

