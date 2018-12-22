<?php

XG_App::includeFileOnce('/lib/XG_Embed.php');

class OpenSocial_EmbedController extends W_Controller {

    protected function _before() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        $this->_widget->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
    }

    public function action_embed1($args) { $this->renderEmbed($args['embed']); }
    public function action_embed2($args) { $this->renderEmbed($args['embed']); }

    /**
     * Configures the embed.  The new HTML will be in the moduleBodyAndFooterHtml property of the JSON output.
     *
     * Note: appUrl is not treated read only.  The url is used as a key to distinguish gadgets, so, after discussion,
     *       the url isn't a changable parameter.  Removing the gadget and adding a new gadget by url is the user's alternative.
     *
     * Expected GET parameters:
     *     id - The embed instance ID, used to retrieve the module data.
     *
     * Expected POST parameters:
     *      canSendMessage - if the application is allowed to send the user a message
     *      canAddActivities - if the application is allowed to add activity log items for the user
     */
    public function action_setValues() {
        if (! XG_App::openSocialEnabled()) { return; }
        $embed = XG_Embed::load($_GET['id']);
        $screenName = XN_Profile::current()->screenName;
        if (! $embed->isOwnedByCurrentUser() && $embed->getOwnerName() != $screenName) { throw new Exception('Not embed owner'); }
        
        $appUrl = $embed->get('appUrl');
        OpenSocialAppData::updateSettings($appUrl, XN_Profile::current()->screenName, $_POST);

        //TODO: Move setup of viewer, owner, appData, appUrl, renderUrl, baseUrl, etc. so it is in one place only.
        // [Thomas David Baker 2008-07-29]
        $this->gadget = new OpenSocialGadget(md5($embed->getLocator()), $_SERVER['HTTP_HOST'], 
            $embed->get('appUrl'), $screenName, $screenName);
        $this->renderUrl = "http://". OpenSocial_GadgetHelper::getOsocDomain();
        $this->baseUrl   = $this->renderUrl."/gadgets";
        $this->openSocialView = 'profile';
        $appData = OpenSocialAppData::load($appUrl, $screenName);
        if (! $appData) {
            error_log("Tried to setValues of $appUrl for $screenName but no corresponding OpenSocialAppData object.");
            return;
        }
        ob_start();
        $this->_widget->includeFileOnce('/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');
        if (OpenSocial_ApplicationDirectoryHelper::isBlocked($appUrl, $appData->my->installedByUrl)) {
            $this->renderPartial('fragment_removedApp', 'application', array('appUrl' => $this->appUrl));
        } else {
            $this->renderPartial('fragment_moduleBodyAndFooter', '_shared', array('gadget' => $this->gadget, 'baseUrl' => $this->baseUrl,
                                                                                  'maxEmbedWidth' => $_GET['maxEmbedWidth'], 'renderNow' => true));
        }
        $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();
    }

    public function action_error() {
        $this->render('blank');
    }

    private function renderEmbed($embed) {
        if (! XG_App::openSocialEnabled()) { return; }
        $this->embed = $embed;
        $this->appUrl = $embed->get('appUrl');
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');

        // This is the currently logged in user.
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
        $viewerName = (XN_Profile::current()->isLoggedIn() ? XN_Profile::current()->screenName : OpenSocial_PersonHelper::ANONYMOUS);

        switch ($embed->getType()) {
            // Network Homepage
            case "homepage":
                // Network creator owns the homepage
                $ownerName =  XN_Application::load()->ownerName;
                break;
            case "profiles":
                $this->openSocialView = "profile";
                // Profile page owner 
                $ownerName =  $embed->getOwnerName();
                break;
            default:
                // Default: Network creator
                $ownerName =  XN_Application::load()->ownerName;
                break;
        }

        $this->appData = OpenSocialAppData::load($this->appUrl, $ownerName);
        if (! $this->appData) {
            error_log("Tried to render embed of $appUrl for $screenName but no corresponding OpenSocialAppData object.");
            return;
        }

        $this->_widget->includeFileOnce('/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');
        if (OpenSocial_ApplicationDirectoryHelper::isBlocked($this->appUrl, $this->appData->my->installedByUrl)) {
            if ($viewerName == $ownerName) {
                 $this->renderPartial('fragment_removedApp', 'application', array('appUrl' => $this->appUrl));
             }
             return; // Do not render the embed if the app has been banned or removed.  Show nothing if the current user is not the owner.
        }
        // URL for the OpenSocial Rendercore
        $this->renderUrl = "http://". OpenSocial_GadgetHelper::getOsocDomain();
        $this->baseUrl   = $this->renderUrl."/gadgets";
        $this->socialDataUrl = $this->renderUrl."/social/data";
        $this->aboutUrl = $this->_widget->buildUrl("application", "about", "?appUrl=" . urlencode($this->appUrl));
        $this->canvasUrl = $this->_widget->buildUrl("application", "show", array("appUrl" => $this->appUrl, "owner" => $ownerName));
        $this->localDomain = $_SERVER['HTTP_HOST'];
        $this->start = time();
        $prefs = OpenSocial_GadgetHelper::readGadgetUrl($embed->get('appUrl'));
        $this->title = $prefs['title'];
        $this->ningApplication = $prefs['ningApplication'];
        // Gadget constructor.
        $this->gadget = new OpenSocialGadget(md5($embed->getLocator()), $this->localDomain, $embed->get('appUrl'), 
            $viewerName, $ownerName);

        $this->render('embed');
    }
}
