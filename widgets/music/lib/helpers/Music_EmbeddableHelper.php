<?php

/**
 * Useful functions for working with <embed> embeddables.
 */
class Music_EmbeddableHelper {

    /**
     * Regenerates the files used by the music player.
     * Possibly expensive; call this function infrequently (e.g., when the app
     * version changes, when the app name or description changes, every hour, etc.)
     *
     * @param $appName string  (optional) the name of the app
     */
    public static function generateResources($appName) {
        self::generateMusicConfigXml($appName);
    }

    /**
     * Regenerates the music-config.xml file.
     *
     * @param $appName string  (optional) the name of the app
     */
    private static function generateMusicConfigXml($appName) {
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        $app_url = 'http://' . $_SERVER['HTTP_HOST'];
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <config>
                <backgroundColor>' . xg_xmlentities(XG_EmbeddableHelper::getBackgroundColor()) . '</backgroundColor>
                <backgroundImageUrl>' . xg_xmlentities(XG_EmbeddableHelper::getBackgroundImageUrl()) . '</backgroundImageUrl>
                <networkName>' . xg_xmlentities(is_null($appName) ? XN_Application::load()->name : $appName) . '</networkName>
                <networkNameCss>' . xg_xmlentities('h1 { font-family: ' . XG_EmbeddableHelper::getNetworkNameFontFamily() . '; color: #FFFFFF; }') . '</networkNameCss>
                <logoUrl>' . xg_xmlentities(XG_EmbeddableHelper::getPlayerLogoUrl()) . '</logoUrl>
                <logoWidth>' . xg_xmlentities(XG_EmbeddableHelper::getPlayerLogoWidth()) . '</logoWidth>
                <logoHeight>' . xg_xmlentities(XG_EmbeddableHelper::getPlayerLogoHeight()) . '</logoHeight>
                <logo_link>'. xg_xmlentities($app_url) .'</logo_link>
                <brandFormat>' . xg_xmlentities(XG_EmbeddableHelper::getPlayerBrandFormat()) . '</brandFormat>
                <l_feature>'. xg_html('ACTION_BUTTON_FEATURE') . '</l_feature>
                <l_featuring>'. xg_html('FEATURING_ELLIPSIS') . '</l_featuring>
                <l_success_feature>'. xg_html('NOW_FEATURED_ON_MAIN') . '</l_success_feature>
                <l_unfeature>'. xg_html('DONT_FEATURE') . '</l_unfeature>
                <l_unfeaturing>'. xg_html('UNFEATURING_ELLIPSIS') . '</l_unfeaturing>
                <l_success_unfeature>'. xg_html('REMOVED_FROM_MAIN') . '</l_success_unfeature>
                <l_add_to_my_page>'. xg_html('ADD_TO_MY_PAGE') . '</l_add_to_my_page>
                <l_success_add>'. xg_html('ADDED_EXCLAMATION') . '</l_success_add>
                <l_error>'. xg_html('ERROR') . '</l_error>
                <l_adding>'. xg_html('ADDING_ELLIPSIS') . '</l_adding>
                <l_failure_track_load>'. xg_html('TRACK_COULD_NOT_BE_LOADED') . '</l_failure_track_load>
                <l_added_by_colon>'. xg_html('ADDED_BY') . '</l_added_by_colon>
                <l_embed_code>'. xg_html('EMBED_CODE') . '</l_embed_code>
                <l_copy_to_clipboard>'. xg_html('COPY_TO_CLIPBOARD') . '</l_copy_to_clipboard>
                <l_success_copy_to_clipboard>'. xg_html('COPIED_TO_CLIPBOARD') . '</l_success_copy_to_clipboard>
                <l_cancel>'. xg_html('CANCEL') . '</l_cancel>
                <l_invalid_url>'. xg_html('INVALID_URL') . '</l_invalid_url>
                <l_playback_normal>'. xg_html('NORMAL_PLAYBACK_ON') . '</l_playback_normal>
                <l_playback_repeat>'. xg_html('REPEAT_PLAYBACK_ON') . '</l_playback_repeat>
                <l_playback_shuffle>'. xg_html('SHUFFLE_PLAYBACK_ON') . '</l_playback_shuffle>
                <l_open_popup_window>'. xg_html('OPEN_POPUP_WINDOW') . '</l_open_popup_window>
                <l_loading_playlist>'. xg_html('LOADING_PLAYLIST_ELLIPSIS') . '</l_loading_playlist>
            </config>';
        $directory = dirname(XG_EmbeddableHelper::getMusicConfigXmlPath());
        if (! file_exists($directory)) { @mkdir($directory, 0777, true); }
        file_put_contents(XG_EmbeddableHelper::getMusicConfigXmlPath(), $xml);
    }

}
