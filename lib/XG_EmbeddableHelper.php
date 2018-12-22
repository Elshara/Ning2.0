<?php

/**
 * Useful functions for working with <embed> embeddables.
 */
class XG_EmbeddableHelper {

    /** Width of the small slideshow, in pixels */
    const SMALL_SLIDESHOW_WIDTH = 160;

    /** Height of the small slideshow, in pixels */
    const SMALL_SLIDESHOW_HEIGHT = 148;

    /** Width of the large slideshow, in pixels */
    const LARGE_SLIDESHOW_WIDTH = 425;

    /** Height of the large slideshow, in pixels */
    const LARGE_SLIDESHOW_HEIGHT = 346;

    /** The default width in pixels for uploaded videos */
    const VIDEO_WIDTH = 448;

    /** The default height in pixels for uploaded videos */
    const VIDEO_HEIGHT = 336;

    /** The height in pixels of the internal video player controls bar*/
    const VIDEO_PLAYER_CONTROLS_HEIGHT_INTERNAL = 24;

    /** The height in pixels of the external video player controls bar*/
    const VIDEO_PLAYER_CONTROLS_HEIGHT_EXTERNAL = 28;

    /** Width of the video player when embedded on another website */
    const EXTERNAL_VIDEO_PLAYER_WIDTH = 448;

    /** Height of the video player when embedded on another website */
    const EXTERNAL_VIDEO_PLAYER_HEIGHT = 364;

    /**
     * Returns the color to use for the badge and player background.
     *
     * @return the RGB color, e.g., "AA0000"
     */
    public static function getBackgroundColor() {
        $mainWidget = W_Cache::getWidget('main');
        if (!mb_strlen($mainWidget->privateConfig['embeds_backgroundColor'])) {
            XG_Version::createBadgeAndPlayerConfig();
        }
        return $mainWidget->privateConfig['embeds_backgroundColor'];
    }

    /**
     * Returns the URL for the badge and player background image. It must be large
     * enough to cover the various embeddables, because Flash does not have a way to tile
     * images without re-requesting the image for each tile (inefficient).
     *
     * @return the URL, or null if no background image has been specified
     */
    public static function getBackgroundImageUrl() {
        return W_Cache::getWidget('main')->privateConfig['embeds_backgroundImageUrl'];
    }

    /**
     * Returns the URL of the original (untiled) badge and player background image
     */
    public static function getBackgroundOriginalImageUrl() {
        //  If there's no original image URL, return the potentially processed one
        $url = W_Cache::getWidget('main')->privateConfig['embeds_backgroundOriginalImageUrl'];
        if (!$url) {
            return W_Cache::getWidget('main')->privateConfig['embeds_backgroundImageUrl'];
        }
        return $url;
    }

    /**
     * Returns the color to use for the network name (if a network logo is not given).
     *
     * @return the RGB color, e.g., "0000AA"
     */
    public static function getNetworkNameColor() {
        return W_Cache::getWidget('main')->privateConfig['embeds_networkNameColor'];
    }

    /**
     * Returns the font families to use for the network name (if a network logo is not given).
     *
     * @return the list of font families e.g., '"Helvetica Neue", Arial, Helvetica, sans-serif'
     */
    public static function getNetworkNameFontFamily() {
        $widget = W_Cache::getWidget('main');
        if (array_key_exists('headingFont', $widget->config)) {
            return $widget->config['headingFont'];
        } else {
            $widget->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
            $defaults = array();
            $imagePaths = array();
            Index_AppearanceHelper::getAppearanceSettings(NULL, $defaults, $imagePaths);
            return $defaults['headingFont'];
        }
    }

    /**
     * Returns the URL for the (unscaled) badge logo.
     *
     * @return the URL, or null if no logo has been specified
     */
    public static function getBadgeLogoUrl() {
        return W_Cache::getWidget('main')->privateConfig['embeds_badgeLogoImageUrl'];
    }

    /**
     * Returns the unscaled width of the badge logo.
     *
     * @return the width, or any value if no logo has been specified
     */
    public static function getBadgeLogoWidth() {
        $badgeLogoUrl = W_Cache::getWidget('main')->privateConfig['embeds_badgeLogoImageUrl'];
        if (preg_match('@\Wwidth=(\d+)@u', $badgeLogoUrl, $matches)) {
            return $matches[1];
        } else {
            return NULL;
        }
    }

    /**
     * Returns the unscaled height of the badge logo.
     *
     * @return the height, or any value if no logo has been specified
     */
    public static function getBadgeLogoHeight() {
        $badgeLogoUrl = W_Cache::getWidget('main')->privateConfig['embeds_badgeLogoImageUrl'];
        if (preg_match('@\Wheight=(\d+)@u', $badgeLogoUrl, $matches)) {
            return $matches[1];
        } else {
            return NULL;
        }
    }

    /**
     * Returns the URL for the (unscaled) player logo.
     *
     * @return the URL, or null if no logo has been specified
     */
    public static function getPlayerLogoUrl() {
        return W_Cache::getWidget('main')->privateConfig['embeds_playerLogoImageUrl'];
    }

    /**
     * Returns the unscaled width of the player logo.
     *
     * @return the width, or any value if no logo has been specified
     */
    public static function getPlayerLogoWidth() {
        $playerLogoUrl = W_Cache::getWidget('main')->privateConfig['embeds_playerLogoImageUrl'];
        if (preg_match('@\Wwidth=(\d+)@u', $playerLogoUrl, $matches)) {
            return $matches[1];
        } else {
            return NULL;
        }
    }

    /**
     * Returns the unscaled height of the player logo.
     *
     * @return the height, or any value if no logo has been specified
     */
    public static function getPlayerLogoHeight() {
        $playerLogoUrl = W_Cache::getWidget('main')->privateConfig['embeds_playerLogoImageUrl'];
        if (preg_match('@\Wheight=(\d+)@u', $playerLogoUrl, $matches)) {
            return $matches[1];
        } else {
            return NULL;
        }
    }

    /**
     * Returns the kind of watermark that players should display
     *
     * @return  logo (image watermark), name (text watermark), or none (no watermark)
     */
    public static function getPlayerBrandFormat() {
        return self::getPlayerLogoUrl() ? 'logo' : (self::displayNameInPlayer() ? 'name': 'none');
    }

    /**
     * Should players display the network name?
     *
     * @return boolean
     */
    private static function displayNameInPlayer() {
        return (W_Cache::getWidget('main')->privateConfig['embeds_displayNameInPlayer'] ? TRUE : FALSE);
    }

    /**
     * Set customization settings for embeds and badges.
     *
     * @param $settings array - An array with any of the following keys:
     * 		embeds_backgroundColor
     * 		embeds_backgroundImageUrl
     * 		embeds_backgroundOriginalImageUrl
     * 		embeds_badgeLogoImageUrl
     * 		embeds_playerLogoImageUrl
     * 		embeds_displayNameInPlayer
     * 		embeds_networkNameColor
     */
    public static function setEmbedCustomization($settings) {
        $widget = W_Cache::getWidget('main');
        $options = array('embeds_backgroundColor', 'embeds_backgroundImageUrl',
                'embeds_badgeLogoImageUrl', 'embeds_playerLogoImageUrl',
                'embeds_displayNameInPlayer', 'embeds_networkNameColor',
                'embeds_backgroundOriginalImageUrl');
        foreach ($options as $name) {
            if (isset($settings[$name])) {
                $widget->privateConfig[$name] = $settings[$name];
            }
        }
        $widget->saveConfig();

        // invalidate admin sidebar cache
        XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
        XG_LayoutHelper::invalidateAdminSidebarCache();
    }

    /**
     * Regenerates the files used by the network badge.
     * Possibly expensive; call this function infrequently (e.g., when the app
     * version changes, when the app name or description changes, when
     * a member is added or removed, every hour, etc.)
     *
     * @param $appName string  (optional) the name of the app
     * @param $appDescription string  (optional) the description of the app
     */
    public static function generateResources($appName = null, $appDescription = null) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_EmbeddableHelper.php');
        Index_EmbeddableHelper::generateResources($appName, $appDescription);
        try {
            W_Cache::getWidget('music')->includeFileOnce('/lib/helpers/Music_EmbeddableHelper.php');
        } catch (Exception $e) {
            // Get here if music widget instance hasn't been created yet [Jon Aquino 2007-06-30]
            XG_App::includeFileOnce('/widgets/music/lib/helpers/Music_EmbeddableHelper.php');
        }
        Music_EmbeddableHelper::generateResources($appName);
        W_Cache::getWidget('main')->privateConfig['embeds_resourceGenerationTime'] = time();
        W_Cache::getWidget('main')->saveConfig();
    }

    /**
     * Regenerates the files used by the network badge approximately
     * every 2 hours. Called by the bot approximately every half hour.
     */
    public static function generateResourcesPeriodically() {
        $configFile = $_SERVER['DOCUMENT_ROOT'] . '/xn_resources/instances/main/embeddable/badge-config.xml';
        if ($_GET['test_generate_resources'] || (file_exists($configFile) && time() - strtotime(date('c', filemtime($configFile))) > 60 * 60 * 2)) {
            self::generateResources();
        }
    }

    /**
     * Returns the path to the badge-config.xml file
     *
     * @return the filesystem path for the config file for badges
     */
    public static function getBadgeConfigXmlPath() {
        return $_SERVER['DOCUMENT_ROOT'] . '/xn_resources/instances/main/embeddable/badge-config.xml';
    }

    /**
     * Returns the path to the music-config.xml file
     *
     * @return the filesystem path for the config file for the music player
     */
    public static function getMusicConfigXmlPath() {
        return $_SERVER['DOCUMENT_ROOT'] . '/xn_resources/instances/music/playlist/music-config.xml';
    }

    /**
     * Appends to the URL the time on which the files were last generated. Useful for ensuring that the browser is
     * getting the the latest xml (rather than old xml in its cache), which is important for pages
     * within the app (less important for Facebook and external websites).
     *
     * Unfortunately Opera and Safari don't cache resources with query parameters
     * (see Cal Henderson, "Serving Javascript Fast", http://www.thinkvitamin.com/features/webapps/serving-javascript-fast ).
     *
     * @param $url string  the URL to which to append the timestamp
     * @return string  the URL with timestamp appended, e.g., http://example.org?t=1183846603
     */
    public static function addGenerationTimeParameter($url) {
        return XG_HttpHelper::addParameter($url, 't', W_Cache::getWidget('main')->privateConfig['embeds_resourceGenerationTime']);
    }

}