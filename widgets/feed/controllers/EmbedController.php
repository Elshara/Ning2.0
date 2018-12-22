<?php

XG_App::includeFileOnce('/lib/XG_Embed.php');

class Feed_EmbedController extends XG_GroupEnabledController {
    const CACHE_MAX_MINUTES = 60;

    protected function _before() {
        $this->trimGetAndPostValues();
    }

    public function action_embed1($args) { $this->renderEmbed($args['embed'],$args['maxEmbedWidth']); }
    public function action_embed2($args) { $this->renderEmbed($args['embed'],$args['maxEmbedWidth']); }
    public function action_embed3($args) { $this->renderEmbed($args['embed'],$args['maxEmbedWidth']); }

    private function renderEmbed($embed,$maxEmbedWidth) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->userCanEdit = $embed->isOwnedByCurrentUser() || Groups_SecurityHelper::currentUserCanEditGroupEmbed($embed);
        $this->embed = $embed;
        $this->title = $embed->get('title');
        $this->feedUrl = $embed->get('feedUrl');
        $this->itemCount = $embed->get('itemCount');
        $this->showDescriptions = $embed->get('showDescriptions');
        $this->itemCount = isset($this->itemCount) ? $this->itemCount : 5;
        if ((! $this->feedUrl && ! $this->userCanEdit) || (! $this->itemCount && ! $this->userCanEdit)) {
            $this->render('blank');
            return;
        }
        $this->title = $this->title ? $this->title : $this->_widget->title;
        $this->showDescriptions = mb_strlen($this->showDescriptions) ? $this->showDescriptions : 1;
        $this->maxEmbedWidth = $maxEmbedWidth;
        $this->render('embed');
    }
    public function action_setValues() {
        $embed = XG_Embed::load($_GET['id']);
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if (! ($embed->isOwnedByCurrentUser() || Groups_SecurityHelper::currentUserCanEditGroupEmbed($embed))) { throw new Exception('Not embed owner'); }
        $embed->set('title', $_POST['title']);
        $embed->set('feedUrl', $_POST['feedUrl']);
        $embed->set('itemCount', $_POST['itemCount']);
        $embed->set('showDescriptions', $_POST['showDescriptions']);
        if ($embed->getType() == 'profiles') {
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ActivityLogHelper.php');
            Profiles_ActivityLogHelper::createProfileUpdateItem(XN_Profile::current()->screenName);  
        }
        ob_start();
        $this->renderPartial('fragment_moduleBodyAndFooter', array('feedUrl' => $_POST['feedUrl'], 'itemCount' => $embed->get('itemCount'), 'showDescriptions' => $_POST['showDescriptions'], 'maxEmbedWidth' => $_GET['maxEmbedWidth']));
        $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }
    /**
     * Update the module body and footer HTML - only called for Frink drop updates
     *
     * Expected GET variables:
     * @param id string  the embed instance ID, used to retrieve module data
     * @param xn_out string  'json'
     *
     * Expected POST variables:
     * @param maxEmbedWidth integer  the maximum width for embeds in the current container
     *
     * @return string  JSON string containing the new module body and footer or an error
     *                 message if the module body and footer could not be updated
     */
    public function action_updateEmbed() {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        try {
            $embed = XG_Embed::load($_GET['id']);
            if (! ($embed->isOwnedByCurrentUser() || Groups_SecurityHelper::currentUserCanEditGroupEmbed($embed))) { throw new Exception('Not embed owner.'); }

            ob_start();
            $this->renderPartial('fragment_moduleBodyAndFooter', array('feedUrl' => $embed->get('feedUrl'), 'itemCount' => $embed->get('itemCount'), 'showDescriptions' => $embed->get('showDescriptions'), 'maxEmbedWidth' => $_POST['maxEmbedWidth']));
            $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
            ob_end_clean();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }
    /**
     * Displays the feed items.
     *
     * @param $feedUrl string  the url for the feed
     * @param $itemCount integer  the maximum number of feed items to display
     * @param $showDescriptions boolean  whether to show titles and descriptions, or just titles
     */
    public function action_moduleBodyAndFooterProper($feedUrl, $itemCount, $showDescriptions, $maxEmbedWidth) {
        if (! $feedUrl) { $this->render('blank'); return; }
        $this->feed = $this->feed($feedUrl);
        if (! $this->feed->data) { $this->render('blank'); return; }
        $this->itemCount = $itemCount;
        $this->maxEmbedWidth = $maxEmbedWidth;
        $this->showDescriptions = $showDescriptions;
    }
    private function cacheDirectory() {
        $cacheDirectory = $_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile/simplepie-cache';
        if (! file_exists($cacheDirectory)) { @mkdir($cacheDirectory, 0777, true); }
        return $cacheDirectory;
    }
    private function feed($feedUrl) {
        $this->_widget->includeFileOnce('/lib/SimplePie/simplepie.inc');
        // Subtract 1 from CACHE_MAX_MINUTES to ensure that it is less than the moduleBodyAndFooterProper action cache [Jon Aquino 2007-05-09]
        $feed = new SimplePie(null, $this->cacheDirectory(), self::CACHE_MAX_MINUTES - 1);
        $feed->strip_attributes(false); // Otherwise width and height are stripped from Google Videos [Jon Aquino 2008-02-29]
        $feed->set_feed_url($feedUrl);
        $feed->init();
        return $feed;
    }
    private function trimGetAndPostValues() {
        foreach ($_GET as $key => $value) {
            $_GET[$key] = trim($value);
        }
        foreach ($_POST as $key => $value) {
            $_POST[$key] = trim($value);
        }
    }

    public function action_error() {
        $this->render('blank');
    }
}