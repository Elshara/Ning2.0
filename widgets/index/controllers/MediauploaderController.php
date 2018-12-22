<?php

/**
 * Dispatches requests pertaining to the Media Uploaderr.
 */
class Index_MediauploaderController extends W_Controller {

    /**
     * Outputs the parent element for the Media Uploader.
     *
     * @param $acceptedFormatsMessageHtml string  HTML for the message about the file formats we accept
     * @param $helpMessageHtml string  HTML for the instructions about what to do if problems occur
     * @param $javaRequiredMessageHtml string  HTML for notice that Java is required
     */
    public function action_container($args) {
        foreach ($args as $key => $value) { $this->{$key} = $value; }
    }

    /**
     * Code for the bottom of the Media Uploader page.
     *
     * @param $type string  the type of uploader: videos, photos, or music
     * @param $uploadUrl string  the endpoint to which to post the files
     * @param $successUrl string  the URL to redirect to after the upload
     */
    public function action_footer($args) {
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        foreach ($args as $key => $value) { $this->{$key} = $value; }
        $this->uploadUrl = XG_SecurityHelper::addCsrfToken($this->uploadUrl);
        $this->appletUrl = xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('jar/uploader.jar'));
        if ($_GET['xn_debug_applet']) { $this->appletUrl = XG_HttpHelper::addParameter($this->appletUrl, 't', filemtime($_SERVER['DOCUMENT_ROOT'] . '/xn_resources/widgets/index/jar/uploader.jar')); }
        if ($_GET['xn_debug_show_disabled_files']) { $this->showDisabledFiles = true; }
        if (in_array($_GET['xn_debug_file_browser_type'], array('TREE', 'TREE_AUTO_EXPAND', 'LIST', 'LIST_AUTO_EXPAND'))) { $this->fileBrowserType = $_GET['xn_debug_file_browser_type']; }
        if (W_Cache::getWidget('main')->config['disableMusicDownload']!='yes') { $this->disableMusicDownload = true; }
    }

    /**
     * Redirects to the Media Uploader or the simple uploader, depending on the
     * capabilities of the browser. The current GET parameters will be preserved.
     */
    public function action_chooseUploader() {
        $route = XG_App::getRequestedRoute();
        $this->mediaUploaderUrl = XG_HttpHelper::addParameters(W_Cache::getWidget($route['widgetName'])->buildUrl($route['controllerName'], 'newWithUploader'), $_GET);
        $this->simpleUploaderUrl = XG_HttpHelper::addParameters(W_Cache::getWidget($route['widgetName'])->buildUrl($route['controllerName'], 'new'), $_GET);
    }

}


