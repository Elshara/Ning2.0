<?php

XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
XG_App::includeFileOnce('/lib/XG_FileHelper.php');


class Index_AppearanceHelper {

    /** Name of the cache-invalidation condition indicating a change in the app's appearance settings. */
    const APPEARANCE_CHANGED = 'APPEARANCE_CHANGED';

    protected static $_fontAlternatives = array(
        'Andale Mono' => '"Andale Mono", "Courier New", Courier, monospace',
        'Arial' => 'Arial, Helvetica, sans-serif',
        'Arial Black' => '"Arial Black", sans-serif',
        'Comic Sans MS' => '"Comic Sans MS", sans-serif',
        'Courier New' => '"Courier New", Courier, "Andale Mono", monospace',
        'Futura' => 'Futura, "Avant Garde", "Century Gothic", "Gill Sans MT", sans-serif',
        'Georgia' => 'Georgia, "Times New Roman", Times, serif',
        'Gill Sans' => '"Gill Sans", "Gill Sans MT", "Gill", "Century Gothic", sans-serif',
        'Helvetica Neue' => '"Helvetica Neue", Arial, Helvetica, sans-serif',
        'Impact' => 'Impact, sans-serif',
        'Lucida Grande' => '"Lucida Grande", "Lucida Sans Unicode", Arial, clean, sans-serif',
        'Times New Roman' => '"Times New Roman", Times, Georgia, serif',
        'Trebuchet MS' => '"Trebuchet MS", sans-serif',
        'Verdana' => 'Verdana, Helvetica, Arial, sans-serif',
    );

    protected static $_themeNames = array(
        'Light Slate', 'Dark Slate',
        'Blue Jeans', 'Loft', 'Pop', 'Dusty', 'Chocolate', 'Soft', 'Melon', 'Ocean',
        'Winter', 'Martini', 'Office', 'Cheesecake', 'Terracotta', 'Pistachio',
        'Rouge', 'Ice Cream', 'Snow', 'Sunshine', 'Espresso', 'Stone', 'Neon',
        'Bondi', 'Gossip', 'Clothesline', 'Pavement', 'Chirp', 'Quill', 'Highrise',
        'Times', 'Veejay', 'Doodle', 'Glass', 'Guild', 'Flourish', 'Area 51', 'Ezra',
        'Graph', 'Floral', 'Newsroom', 'Notepad', 'Baby', 'Active', 'Gothic', 'Tagged',
        'Tagged Blue', 'Tagged Pink', '8-Bit', 'Family', 'Genie', 'Professional',
        'Reunion', 'Sorority', 'Fraternity', 'Dance Club', 'Encore', 'Chalkboard',
        'Gamer'
    );

    // Map mime type to file extensions
    public function getExtension($mimetype) {
        switch ($mimetype) {
            case 'image/bmp':
                return '.bmp';
            case 'image/gif':
                return '.gif';
            case 'image/png':
                return '.png';
            case 'image/tiff':
                return '.tif';
            case 'image/jpeg':
            case 'image/pjpeg':
            default:
                return '.jpg';
        }
    }

    public function getFontAlternatives() {
        return self::$_fontAlternatives;
    }

    public function getThemeNames() {
        return self::$_themeNames;
    }

    /**
     *  Remove all appearance-related content objects for the specified user
     *
     *  @param $userObj User object for the user in question
     *  @return array number of objects removed, number of objects remaining
     */
    public static function removeByUser($userObj) {
        $removedCount = 0;
        $remainingCount = 0;

        //  Theme CSS
        self::setAttribute($userObj, 'themeCssUrl', '');
        $query = XN_Query::create('Content')
                ->filter('owner')
                ->filter('contributorName', '=', $userObj->contributorName)
                ->filter('type', '=', 'ProfileCustomizationCss')
                ->alwaysReturnTotalCount(TRUE);
        $oldCss = $query->execute();
        if (count($oldCss) > 0) {
            $removedCount += count($oldCss);
            XN_Content::delete($oldCss);
        }
        if ($query->getTotalCount() > count($oldCss)) {
            $remainingCount += ($query->getTotalCount() - count($oldCss));
        }

        //  Custom CSS
        self::setAttribute($userObj, 'customCssUrl', '');
        $query = XN_Query::create('Content')
                ->filter('owner')
                ->filter('contributorName', '=', $userObj->contributorName)
                ->filter('type', '=', 'ProfileCustomizationCustomCss')
                ->alwaysReturnTotalCount(TRUE);
        $oldCss = $query->execute();
        if (count($oldCss) > 0) {
            $removedCount += count($oldCss);
            XN_Content::delete($oldCss);
        }
        if ($query->getTotalCount() > count($oldCss)) {
            $remainingCount += ($query->getTotalCount() - count($oldCss));
        }

        //  Other User object attributes
        self::setAttribute($userObj, 'adColors', '');
        self::setAttribute($userObj, 'darkNingbarText', '');

        if ($removedCount > 0) {
            $userObj->save();
        }
        return array($removedCount, $remainingCount);
    }

    // TODO: Many functions in this class have "if (!isset($user))" checks.
    // This indicates that there should be two classes: UserAppearanceHelper and NetworkAppearanceHelper.
    // See "Replace Conditional With Polymorphism" in Refactoring [Jon Aquino 2008-04-24]

    /**
     *  Returns the theme CSS URL.
     *
     *  @param $profile User object or NULL for site wide theme CSS
     */
    public static function getThemeCssUrl($user = NULL) {
        // TODO: Eliminate this function - use getThemeCssFilename instead [Jon Aquino 2008-04-24]
        if (!isset($user)) {
            return '/theme' . W_Cache::getWidget('main')->config['userCssVersion'] . '.css';
        }
        else {
            // user theme css is stored in the content store, not the filesystem
            return self::getAttribute($user, 'themeCssUrl');
        }
    }


    /**
     *  Returns the theme CSS filename.
     *
     *  @param $profile User object or NULL for site wide theme CSS
     */
    public static function getThemeCssFilename($user = NULL) {
        if (!isset($user)) {
            return $_SERVER['DOCUMENT_ROOT'] .
                    self::getThemeCssUrl();
        }
        else {
            return self::getThemeCssUrl($user);
        }
    }


    /**
     * Get the file for typography based on the value of $configParam.
     *
     * @param   $configParam    String  One of 'small', 'normal' or 'large'; defaults to 'normal' if value not in set
     * @return                  String  Relative file path suitable for passing to buildResourceUrl.
     */
    public static function getTypographyCssFile($configParam) {
        $name = in_array($configParam, array('small','large')) ?
                    $configParam :
                    'normal';
        return "css/typography-$name.css";
    }

    /**
     * Return patterns to use to find existing theme CSS files. The
     * returned array contains  "glob" element that can be used
     * with the glob() function and then a "regex" element that
     * further narrows down the glob results.
     *
     * @return array
     */
    protected static function getThemeCssMatchPattern() {
        return array('glob' => $_SERVER['DOCUMENT_ROOT'] . '/theme*.css',
                     'regex' => '@/theme[0-9]+\.css$@');
    }

    /**
     *  Returns the custom CSS URL.
     *
     *  @param $profile User object or NULL for site wide theme CSS
     */
    public static function getCustomCssUrl($user = NULL) {
        // TODO: Eliminate this function - use getCustomCssFilename instead [Jon Aquino 2008-04-24]
        if (!isset($user)) {
            return '/custom' . W_Cache::getWidget('main')->config['customCssVersion'] . '.css';
        }
        else {
            // user theme css is stored in the content store, not the filesystem
            return self::getAttribute($user, 'customCssUrl');
        }
    }

    /**
     *  Returns the custom CSS filename.
     *
     *  @param $profile User object or NULL for site wide theme CSS
     */
    public static function getCustomCssFilename($user = NULL) {
        if (!isset($user)) {
            return $_SERVER['DOCUMENT_ROOT'] .
                    self::getCustomCssUrl();
        }
        else {
            return self::getCustomCssUrl($user);
        }
    }

    /**
     * Return patterns to use to find existing custom CSS files. The
     * returned array contains  "glob" element that can be used
     * with the glob() function and then a "regex" element that
     * further narrows down the glob results.
     *
     * @return array
     */
    protected static function getCustomCssMatchPattern() {
        return array('glob' => $_SERVER['DOCUMENT_ROOT'] . '/custom*.css',
                     'regex' => '@/custom[0-9]+\.css$@');
    }

    public function getThemeCss($user = NULL) {
        if (!isset($user)) {
            //  retrieve from filesystem
            $oldFilename = self::getThemeCssFilename($user);
            // Hide errors from file_get_contents to avoid
            // having to make an is_readable() check first.
            $css = @file_get_contents($oldFilename);
            // File get contents returns FALSE if it encountered
            // an error (such as the file not existing)
            if ($css !== FALSE) {
                return $css;
            } else {
                return self::$cssTemplate;
            }
        }
        else {
            //  retrieve from content store
            $cssUrl = self::getAttribute($user, 'themeCssUrl');
            if (isset($cssUrl)) {
                return @file_get_contents($cssUrl);
            }
            else {
                return NULL;
            }
        }
    }

    public function setThemeCss($user, $css) {
        if (!isset($user)) {
            //  store on filesystem
            $oldFilename = self::getThemeCssFilename($user);
            self::setAttribute($user, 'userCssVersion',
                    self::getAttribute($user, 'userCssVersion') + 1);
            $newFilename = self::getThemeCssFilename($user);

            @mkdir(dirname($newFilename));
            file_put_contents($newFilename, $css);
            XG_FileHelper::fileCleanup(self::getThemeCssMatchPattern(),
                                       array('max-count' => 5,
                                             'min-count' => 3,
                                             'max-age' => 300));
        }
        else {
            $css = self::sanitizeCss($css);

            //  store new css in the content store
            $url = '/content?binary=true&type=ProfileCustomizationCss';
            $type = 'text/css';
            $rsp = XN_REST::post($url, $css, $type);
            $content = XN_AtomHelper::loadFromAtomFeed($rsp, 'XN_Content');

            //  record the location of the new content object
            self::setAttribute($user, 'themeCssUrl', $content->fileUrl('data'));

            //  cleanup old css (if any)
            $oldCss = XN_Query::create('Content')
                    ->filter('owner')
                    ->filter('contributor', '=', XN_Profile::current())
                    ->filter('type', '=', 'ProfileCustomizationCss')
                    ->filter('id', '!=', $content->id)
                    ->execute();
            if (count($oldCss) > 0) {
                XN_Content::delete($oldCss);
            }
        }
    }

    protected function getCustomCss($user) {
        if (!isset($user)) {
            //  retrieve from filesystem
            $oldFilename = self::getCustomCssFilename($user);
            // Hide errors from file_get_contents to avoid
            // having to make an is_readable() check first.
            $css = @file_get_contents($oldFilename);
            // File get contents returns FALSE if it encountered
            // an error (such as the file not existing)
            if ($css !== false) {
                return $css;
            }
        }
        else {
            //  retrieve from content store via files api
            $cssUrl = self::getAttribute($user, 'customCssUrl');
            if (isset($cssUrl)) {
                return @file_get_contents($cssUrl);
            }
        }
        return NULL;
    }

    protected function setCustomCss($user, $css) {
        if (!isset($user)) {
            //  store on filesystem
            $oldFilename = self::getCustomCssFilename($user);
            self::setAttribute($user, 'customCssVersion',
                    self::getAttribute($user, 'customCssVersion') + 1);
            $newFilename = self::getCustomCssFilename($user);

            @mkdir(dirname($newFilename));
            file_put_contents($newFilename, $css);
            XG_FileHelper::fileCleanup(self::getCustomCssMatchPattern(),
                                       array('max-count' => 5,
                                             'min-count' => 3,
                                             'max-age' => 300));
            return null;
        }
        else {
            //  If $css is empty, the following code will store the contents of
            //  the previous request in a new content object - so we don't try
            if (mb_strlen($css) < 1) {
                self::setAttribute($user, 'customCssUrl', '');
                return;
            }
            $css = self::sanitizeCss($css);

            //  store new css in the content store
            $url = '/content?binary=true&type=ProfileCustomizationCustomCss';
            $type = 'text/css';
            $rsp = XN_REST::post($url, $css, $type);
            $content = XN_AtomHelper::loadFromAtomFeed($rsp, 'XN_Content');

            //  record the location of the new content object
            self::setAttribute($user, 'customCssUrl', $content->fileUrl('data'));
            return $content->id;
        }
    }

    /**
     *  Removes any unused custom Css files for the current user
     */
    protected static function cleanUpOldCustomCss($id) {
        if ($id) {
            $oldCss = XN_Query::create('Content')
                    ->filter('owner')
                    ->filter('contributor', '=', XN_Profile::current())
                    ->filter('type', '=', 'ProfileCustomizationCustomCss')
                    ->filter('id', '!=', $id)
                    ->execute();
            if (count($oldCss) > 0) {
                XN_Content::delete($oldCss);
            }
        }
    }

    /**
     *  Get an attribute from either the widget config or the user object
     */
    protected static function getAttribute($user, $name) {
        if (!isset($user)) {
            return W_Cache::getWidget('main')->config[$name];
        }
        else {
            return $user->my->raw(XG_App::widgetAttributeName(
                    W_Cache::getWidget('profiles'), $name));
        }
    }

    /**
     *  Set an attribute in either the widget config or the user object
     */
    protected static function setAttribute($user, $name, $value) {
        if (!isset($user)) {
            W_Cache::getWidget('main')->config[$name] = $value;
        }
        else {
            $user->my->set(XG_App::widgetAttributeName(
                    W_Cache::getWidget('profiles'), $name), $value);
        }
    }

    /**
     *  Save changes in either the widget config or the user object
     */
    protected static function saveAttributes($user) {
        if (!isset($user)) {
            W_Cache::getWidget('main')->saveConfig();
        }
        else {
            $user->save();
        }
    }

    /**
     *  Stores a newly uploaded image to the filesystem (app-wide) or the
     *    content store (user-specific).
     *
     * @param $user User The owner of the image, or NULL for app-wide
     * @param $key unknown_type The name of the image (e.g. siteBgImage)
     * @return URL to be used when serving the new image
     */
    protected static function storeUploadedImage($user, $key, $post) {
        //  Store in the content store
        //  Save the uploaded file into a content object
        $newImage = XN_Content::create('ProfileCustomizationImage', $key, '')
                ->set('data', $post[$key], XN_Attribute::UPLOADEDFILE)
                ->save();
        $filepath = $post[$key];
        //  BAZ-944: basename() doesn't like DOS separators when running on Unix
        if (mb_strpos($filepath, '\\')) {
            $filepath = str_replace('\\', '/', $filepath);
        }
        self::setAttribute($user, $key . 'Name', basename($filepath));

        //  Return the file API URL for the content object
        $newImageUrl = $newImage->fileUrl('data');
        $newImageDimensions = $newImage->imageDimensions('data');
        if (is_array($newImageDimensions) && isset($newImageDimensions[0]) && isset($newImageDimensions[1])) {
            $newImageUrl = XG_HttpHelper::addParameter($newImageUrl, 'width', $newImageDimensions[0]);
            $newImageUrl = XG_HttpHelper::addParameter($newImageUrl, 'height', $newImageDimensions[1]);
        }
        $newImageUrl = XG_HttpHelper::addParameter($newImageUrl,'xn_auth','no');
        if (preg_match('@image/(.*)@u', $post[$key . ':type'], $matches)) {
            $newImageUrl = XG_HttpHelper::addParameter($newImageUrl,'type',$matches[1]);
        }

        return $newImageUrl;
    }

    /**
     * Takes a predefined set of keys to keep in sync in the main widget config
     * An incremental update will only add keys from $fastKeys that are not already
     * in the main widget config.  A non-incremental update will replace all keys
     * in the main widget config with values from $defaults.
     *
     * @param   $defaults       array       key=>value pairs from CSS parsed by getAppearanceSettings
     * @param   $incremental    boolean     Perform an incremental update or full update of keys?
     */
    public static function synchronizeAppearanceWidgetKeys($defaults, $incremental = false) {
        // a few css keys are used frequently without the rest so we'll keep them
        // in the main widget config for faster lookup (and fewer calls to this method)
        // this checks if we need to migrate any keys to the main widget config
        // you can add to $fastKeys and this should handle incremental migration of keys
        // ** removing from $fastKeys will not remove the synced key/value pair from the
        // main widget config. BAZ-3759 [ywh 2008-05-15]
        $fastKeys = array('headingFont', 'siteLinkColor', 'ningbarColor', 'ningLogoDisplay');
        $syncedKeys = 0;
        $widget = W_Cache::getWidget('main');
        foreach ($fastKeys as $key) {
            if (! array_key_exists($key, $widget->config) ||
                ! $incremental) {
                $widget->config[$key] = array_key_exists($key, $defaults) ?
                        preg_replace('/;$/u', '', preg_replace('/^#([0-9a-f]{3,6})(?:!important)?/ui', '$1', $defaults[$key])) :
                        '';
                $syncedKeys++;
            }
        }
        if ($syncedKeys > 0) { $widget->saveConfig(); }
        return $syncedKeys;
    }

    /**
     * Reads appearance settings from a theme CSS file and populates the provided arrays.
     *
     * @param $profile      XN_Profile  Profile to retreive settings for or NULL for the site wide theme CSS
     * @param &$defaults    Array       Output parameter for key=>value of colors, fonts, etc. in non-CSSified form
     *                                  (for example, 'moduleBgColor' => 'ffffff').  Will need wrapping in "#$value;"
     *                                  for inclusion as CSS value.
     * @param &$imagePaths  Array       Output parameter for key=>value of image paths in non-CSSified form
     *                                  (for example, 'moduleBgImage' => 'http://ning.com/foo.png').  Will need wrapping
     *                                  in "url($value);" for inclusion as CSS value.
     */
    public static function getAppearanceSettings($profile, &$defaults, &$imagePaths) {
        //TODO: This routine does some of the same stuff as the cssify function but without calling cssify.
        // It would be better to only have that logic in one place.
        $user = (isset($profile) ? User::load($profile) : NULL);
        $themeCss = self::getThemeCss($user);
        if (!$themeCss) {
            // Load site-wide CSS and site-wide attribute settings assuming this is a user customizing for the first time
            $user = NULL;
            $themeCss = self::getThemeCss(NULL);
        }

        list($themeSettings, $imagePaths) = self::parseCss($themeCss, $user);
        foreach ($themeSettings as $key => $value) {
            $defaults[$key] = $value;
        }

        // Not implementing user logo override for now
        // Logo is stored in instance config, not css
        if (!isset($user) && self::getAttribute(NULL, 'logoImageUrl')) {
            $defaults['logoImage'] = self::getAttribute(NULL, 'logoImageName');
            $imagePaths['logoImage'] = self::getAttribute(NULL, 'logoImageUrl');
        }

        $defaults['customCss'] = self::getCustomCss($user);
        $defaults['customCss'] = preg_replace_callback('/\burl\s*\((.*?)\)/iu', array('self','cssDecodePathParts'), $defaults['customCss']);

        $substitutions = array();
        foreach ($defaults as $key => $value) {
            $substitutions[$key] = $value;
        }
        foreach ($imagePaths as $key => $value) {
            $substitutions[$key] = $value;
        }
        if (self::migrationRequired($substitutions)) {
            $defaults['customCss'] = self::getMigrationCss($substitutions) . "\n" . $defaults['customCss'];
        }

        // incremental synchronization of desired fast keys (BAZ-3759) [ywh 2008-05-15]
        if (! $user) {
            self::synchronizeAppearanceWidgetKeys($defaults, true);
        }
    } // getAppearanceSettings()

    private static function getTileCssValue($tile) {
        $tile = trim($tile);
        if (is_null($tile) || (mb_strlen($tile) < 1) || ($tile == '0') || ($tile === false)) {
            return 'no-repeat';
        } else if (($tile == '1') || ($tile === true)) {
            return 'repeat';
        } else {
            return $tile;
        }
    }

    /**
     *  Sets appearance settings in the appropriate user or site-wide theme CSS.
     *
     * @param   $profile    XN_Profile  Profile of user to set settings for, or null to set sitewide settings.
     * @param   $post       Array       Contents of $_POST TODO add more detail.
     */
    public function setAppearanceSettings($profile, $post) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_ClearspringHelper.php');
        $user = (isset($profile) ? User::load($profile) : NULL);

        $mainWidget = W_Cache::getWidget('main');
        if (! $profile && $post['fontSize'] !== $mainWidget->config['typography']) {
            $mainWidget->config['typography'] = $post['fontSize'];
            $mainWidget->saveConfig();
        }

        //  Need to start with existing template's settings, at least in the
        //    case of existing images you're keeping
        $initialCss = self::getThemeCss($user);
        if (!$initialCss) {
            $initialCss = self::getThemeCss(NULL);
        }
        list(, $imagePaths) = self::parseCss($initialCss, $user);
        $substitutions = array();
        //  Use existing image paths as default substitutions
        foreach ($imagePaths as $key => $value) {
            $substitutions[$key] = "url($value);";
        }

        //  Default substitutions
        $substitutions['date'] = date('c');

        // set the %buttonTextColor% based on the value of %siteLinkColor%

        if ($post['siteLinkColor']) {
            $siteLinkColor = strip_tags($post['siteLinkColor']);
            $red = hexdec(mb_substr($siteLinkColor, 1, 2));
            $green = hexdec(mb_substr($siteLinkColor, 3, 2));
            $blue = hexdec(mb_substr($siteLinkColor, 5, 2));
            $value = (0.3 * $red + 0.59 * $green + 0.118 * $blue);
            if ($value > 160) {
                $substitutions['buttonTextColor'] = '#222;';
            } else {
                $substitutions['buttonTextColor'] = '#fff;';
            }
        }

        //  Color values
        $colorKeys = array('siteBgColor', 'pageBgColor', 'siteLinkColor',
                'headBgColor', 'headTabColor', 'pageTitleColor', 'pageHeaderTextColor',
                'moduleHeadBgColor', 'moduleHeadTextColor', 'moduleBodyBgColor',
                'moduleBodyTextColor', 'ningbarColor', 'moduleHeadingColor');
        $importantColors = array('ningbarColor');
        foreach ($colorKeys as $key) {
            if (isset($post[$key])) {
                $value = strip_tags($post[$key]);
                if (in_array($key, $importantColors)) {
                    $value .= '!important';
                }
                $substitutions[$key] = $value . ';';
            }
        }
        //  Use the ningbar color to determine whether we want light or dark text
        $ningbarColor = strip_tags($post['ningbarColor']);
        $red = hexdec(mb_substr($ningbarColor, 1, 2));
        $green = hexdec(mb_substr($ningbarColor, 3, 2));
        $blue = hexdec(mb_substr($ningbarColor, 5, 2));
        $value = (0.3 * $red + 0.59 * $green + 0.118 * $blue);
        if ($value > 130) {
            $url = xg_absolute_url('/xn_resources/widgets/index/css/ningbar-invert.css');
            self::setAttribute($user, 'darkNingbarText', '1');
        }
        else {
            self::setAttribute($user, 'darkNingbarText', '0');
        }
        //  ensure that obsolete ningbarInvert substitution is blank
        $substitutions['ningbarInvert'] = '';

        //  Fonts
        $fontKeys = array('textFont', 'headingFont');
        foreach ($fontKeys as $key) {
            if (isset($post[$key])) {
                $fontAlternatives = self::getFontAlternatives();
                $value = $fontAlternatives[$post[$key]] . ';';
                $substitutions[$key] = $value;
            }
        }

        //  Images
        $imageKeys = array('siteBgImage', 'headBgImage', 'pageBgImage',
                'moduleBgImage');
        if (!isset($user)) {
            $imageKeys[] = 'logoImage';
        }
        foreach ($imageKeys as $key) {
            switch ($post[$key . '_action']) {
                case 'remove':
                    //  Clear the user CSS (or instance config)
                    if ($key == 'logoImage') {
                        self::setAttribute($user, 'logoImageUrl', '');
                    } else {
                        $substitutions[$key] = ' none;';
                        $substitutions[$key . '_repeat'] = ' no-repeat;';
                    }
                    break;
                case 'theme':
                    $substitutions[$key] = 'url(' . $post[$key . '_themeImage'] . ');';
                    // Absolute URL needed for profile CSS, whose URL is api.ning.com (BAZ-5004) [Jon Aquino 2007-10-17]
                    $url = xg_absolute_url($post[$key . '_themeImage']);
                    $substitutions[$key] = "url($url);";
                    $substitutions[$key . '_repeat'] = self::getTileCssValue($post[$key . '_repeat']) . ';';
                    break;
                case 'add':
                    // TODO: This check for accepted image types is done in a number of places.
                    // We should create a function for this in XG_FileHelper so that the logic is not duplicated. [Jon Aquino 2008-01-24]
                    $acceptedTypes = array('image/jpeg','image/pjpeg','image/gif','image/png','image/x-png');
                    try {
                        if (!isset($post[$key . ':status']) || !($post[$key . ':status'] == 0) || !(in_array($post[$key . ':type'], $acceptedTypes))) {
                            break;
                        }
                        $newFileUrl = self::storeUploadedImage($user, $key, $post);

                        // Get width and height if storeUploadedImage wasn't able to calculate it
                        if (mb_strpos($newFileUrl,'width=') === false) {
                            // TODO: Do this in storeUploadedImage [Jon Aquino 2008-04-24]
                            $imageInfo = getImageSize($newFileUrl);
                            if (is_array($imageInfo) && isset($imageInfo[0]) && isset($imageInfo[1])) {
                                // TODO: Bug: The URL should be passed to addParameter [Jon Aquino 2008-04-24]
                                $newFileUrl = XG_HttpHelper::addParameter('width',$imageInfo[0]);
                                $newFileUrl = XG_HttpHelper::addParameter('height',$imageInfo[1]);
                            }
                        }

                        //  Set the user CSS (or instance config) to use it
                        if ($key == 'logoImage') {
                            //  Limit width to 925px;
                            $imageWidth = $imageHeight = null;
                            if (preg_match('@width=(\d+)@u', $newFileUrl, $matches)) {
                                $imageWidth = $matches[1];
                            }
                            if (preg_match('@height=(\d+)@u', $newFileUrl, $matches)) {
                                $imageHeight = $matches[1];
                            }
                            if (isset($imageHeight) && isset($imageWidth) && ($imageWidth > 925)) {
                                $imageAspectRatio = $imageWidth / $imageHeight;
                                $imageWidth = 925;
                                $imageHeight = intval($imageWidth / $imageAspectRatio);
                                $newFileUrl = XG_HttpHelper::addParameters($newFileUrl, array('height' => $imageHeight, 'width' => $imageWidth));
                            }
                            // TODO: No need to call htmlspecialchars - plain text would be better [Jon Aquino 2008-04-24]
                            self::setAttribute($user, 'logoImageUrl', htmlspecialchars($newFileUrl));
                        } else {
                            $substitutions[$key] = "url(" . $newFileUrl . ");";
                        }
                    } catch (Exception $e) {
                        // Ignore (BAZ-2341) [Jon Aquino 2007-03-26]
                    }
                    // Intentional fall through
                default:
                    // keep
                    $substitutions[$key . '_repeat'] = self::getTileCssValue($post[$key . '_repeat']) . ';';

            } // switch
        } // foreach

        // Show Ning Logo? (BAZ-1564)
        if ($user) {
            $substitutions['ningLogoDisplay'] = 'inherit;';
        }
        else {
            $substitutions['ningLogoDisplay'] = (isset($post['ningLogoDisplay']) && $post['ningLogoDisplay'] == 'block')
            ? 'block!important;' : 'none!important;';
        }

        //  Rewrite the CSS template making the chosen substitutions
        //  Always start from the current template to incorporate changes in
        //    the template's structure (BAZ-2192)
        $userCss = self::$cssTemplate;
        $clearspringCss = Index_ClearspringHelper::getClearspringCssTemplate();
        foreach ($substitutions as $key => $value) {
            // find /* %blah% /* whatever and replace whatever (to EOL)
            $pattern = '/\* %' . $key . '% \*/';
            $userCss = preg_replace('@(' . $pattern . ').*@u', '$1 ' . $value, $userCss);
            $clearspringCss = preg_replace('@(' . $pattern . ').*@u', '$1 ' . $value, $clearspringCss);
        }

        // full synchronization of desired fast keys (BAZ-3759) [ywh 2008-05-15]
        if (! $user) {
            self::synchronizeAppearanceWidgetKeys($substitutions);
        }

        self::setThemeCss($user, self::escapeCssUrls(self::applyCdnUrls($userCss)));
        Index_ClearspringHelper::setClearspringCss($clearspringCss);
        $newCssId = self::setCustomCss($user, self::escapeCssUrls(self::applyCdnUrls(trim(strip_tags($post['customCss'])))));
        self::setAttribute($user, 'adColors', self::adColors($user));
        self::saveAttributes($user);
        self::cleanUpOldCustomCss($newCssId);
    }

    public function clearAppearanceSettings($profile = NULL) {
        $user = (isset($profile) ? User::load($profile) : NULL);
        if (isset($user)) {
            self::setThemeCss($user, NULL);
            self::setCustomCss($user, NULL);
        }
        else {
            // TODO: Rename cssTemplate to themeCssTemplate, and cssCustom to customCssTemplate [Jon Aquino 2008-09-10]
            self::setThemeCss($user, self::$cssTemplate);
            self::setCustomCss($user, self::$cssCustom);
        }
        self::setAttribute($user, 'logoImageUrl', NULL);
        self::saveAttributes($user);
    }

    /**
     *  Parse the provided theme CSS for variables
     */
    public function parseCss($css, $user = NULL) {
        $values = array();
        $imagePaths = array();
        if (preg_match_all('@/\* %([\w_]+)% \*/(.+);@u', $css, $matches)) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $key = $matches[1][$i];
                $value = trim($matches[2][$i]);
                if (mb_substr($key, -5) == 'Color') {
                    if (preg_match('@([a-fA-F0-9]{3,6})@u', $value, $matches2)) {
                        $value = $matches2[1];
                    }
                }
                else if (mb_substr($key, -5) == 'Image') {
                    //  Use the user-supplied name rather than the generated
                    //    name found in the CSS (only if the image is actually
                    //    present in the CSS)

                    // What is the difference? [Jon Aquino 2008-04-24]
                    if ($value && ($value != 'none')) {
                        $imagePaths[$key] = mb_substr($value, 4, -1);
                        $value = self::getAttribute($user, $key . 'Name');
                    }
                    else {
                        $value = '';
                    }
                }
                if (mb_substr($value, -1) == ';') {
                    $value = mb_substr($value, 0, -1);
                }
                $values[$key] = $value;
            }
        }
        self::$lastParsedCssValues = $values;
        return array($values, $imagePaths);
    }

    /**
     * Determine if the theme CSS represented by $substitutions requires a migration step from the pre-3.0 to post-3.0 CSS template.
     *
     * @param   $substitutions  Array       Set of key=>value pairs representing theme CSS information.
     * @return                  Boolean     true if migration is required (theme is using old keys).
     */
    public static function migrationRequired($substitutions) {
        /* TODO: 3.0 removed these options from the available user CSS keys.  We should perhaps remove them everywhere they appear in the code
         (apart from migrating pre-3.0 CSS to post-3.0 CSS) and possibly even throw an exception upon encountering
         them. */
        $removedOptions = array('pageTitleColor', 'headTabColor', 'moduleBodyBgColor', 'moduleBgImage', 'moduleBgImage_repeat', 'moduleHeadingColor');
        foreach ($removedOptions as $option) {
            if (array_key_exists($option, $substitutions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the CSS necessary to preserve pre-3.0 customizations when using post-3.0 cssTemplate.
     * The resulting CSS must be copied to custom CSS to ensure preservation of the customizations.
     *
     * @param   $substitutions  Array       Set of key=>value pairs representing theme CSS information.
     * @return                  String      CSS suitable for prepending to custom CSS.
     */
    public static function getMigrationCss($substitutions) {
        //  Rewrite the CSS template making the chosen substitutions
        $css = self::$cssMigrationTemplate;
        //TODO: this is copy and paste code from above BUT here we call cssifiy and there it has been done beforehand.
        // If we clean up so that cssify is the only place where we add semicolons, etc. then it will be trivial
        // to clean this up into one function, too.  Beware "!important" when doing so, it is not in cssify yet.
        foreach ($substitutions as $key => $value) {
            // find /* %blah% /* whatever and replace whatever (to EOL)
            $pattern = '/\* %' . $key . '% \*/';
            $css = preg_replace('@(' . $pattern . ').*@u', '$1 ' . self::cssify($key, $value), $css);
        }
        return trim(strip_tags($css));
    }

    /**
     * Takes a value like 'FFFFFF' or 'http://ning.com/foo.png' and makes it suitable for inserting on
     * the end of a line of CSS as a value.  The type of wrapping is determined by $key.
     *
     * Example:
     *
     *    cssify('moduleBgColor', 'fff') => '#fff;'
     *
     * @param   $key    String  Key for this CSS customization.
     * @param   $value  String  Value to CSS-ify.
     */
    public static function cssify($key, $value) {
        //TODO: these mb_substr tests are used in parseCss, too.  Create one location for them.
        if (mb_substr($key, -5) == 'Color' && preg_match('@([a-fA-F0-9]{3,6})@u', $value)) {
            $s = '#' . $value;
        } else if (mb_substr($key, -5) == 'Image' && $value) {
            $s = "url($value)";
        } else if (mb_substr($key, -5) == 'Image') {
            $s = 'none';
        } else if (mb_substr($key, -7) == '_repeat' && ! $value) {
            $s = 'no-repeat';
        } else {
            $s = $value;
        }
        return $s . ";";
    }

    /**
     * Scan CSS for urls that begin with a forward slash and replace them with a URL served by the CDN.
     *
     * @param   string  CSS to scan.
     * @return  string  CSS with urls replaced.
     */
    public static function applyCdnUrls($css) {
        // TODO replace create_function() code with a real function - create_function() is horribly slow [Travis S. 2008-09-04]
        $f = create_function('$a', 'return "url(" . xg_cdn($a[1]) . ")";');
        $css = preg_replace_callback('|\burl\s*\((/.*)\)|', $f, $css);
        $css = preg_replace_callback('|\burl\s*\((http://' . preg_quote($_SERVER['HTTP_HOST'], '|') . '/.*)\)|', $f, $css);
        $css = preg_replace_callback('|\burl\s*\((http://' . preg_quote(XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX, '|') . '/.*)\)|', $f, $css);
        return $css;
    }

    /**
     * Encode URL path parts.. i.e. http://(host/dir1/dir2/....)?query_params
     * the host/dir1/dir2/.... are considered the path parts.  URLs do not
     * necessarily have to be absolute.  The individual path parts (delimited
     * by '/') are rawurlencoded.  The entire path parts string is urldecoded
     * first to prevent duplicate encoding.
     *
     * @param url string  an absolute or relative url; can contain query_string params
     *
     * @return string  the same URL with path parts URL encoded
     */
    public static function cssEncodePathParts($url) {
        $url = preg_replace('/^(["\'])?(.*?)(["\'])?$/', '$2', $url);
        $parsed = parse_url($url);
        if (array_key_exists('path', $parsed)) {
            // parse_url probably decodes but this should be harmless
            $parts = explode('/', rawurldecode($parsed['path']));
            $numParts = count($parts);
            for ($i = 0; $i < $numParts; $i++) {
                $parts[$i] = rawurlencode($parts[$i]);
            }
            $parsed['path'] = implode('/', $parts);
        }
        return XG_HttpHelper::joinParsedUrl($parsed);
    }

    /**
     * Encode URL path parts.. i.e. http://(host/dir1/dir2/....)?query_params
     * the host/dir1/dir2/.... are considered the path parts.  URLs do not
     * necessarily have to be absolute.  The individual path parts (delimited
     * by '/') are rawurlencoded.  The entire path parts string is urldecoded
     * first to prevent duplicate encoding.
     *
     * @param $matches list  The list of regexp matches. The [1] item contains an absolute or relative url; can contain query_string params
     *
     * @return string  the same URL with path parts URL encoded
     */
    public static function cssDecodePathParts($matches) {
        $url = $matches[1];
        $parsed = parse_url($url);
        if (array_key_exists('path', $parsed)) {
            // parse_url probably decodes but this should be harmless
            $parts = explode('/', rawurldecode($parsed['path']));
            $parsed['path'] = implode('/', $parts);
        }
        return 'url(' . XG_HttpHelper::joinParsedUrl($parsed) . ')';
    }

    /**
     * A helper function to call cssEncodePathParts because an anonymous function (i.e. created
     * using create_function cannot access methods of this class
     *
     * @param matches array(string)  matches passed by preg_replace_callback
     *
     * @return string  CSS url parameter with the actual URL properly escaped
     */
    protected static function escapeCssUrlsHelper($matches) {
        return "url(" . self::cssEncodePathParts($matches[1]) . ")";
    }

    /**
     * Scan CSS and update URLs so the path parts are properly escaped (BAZ-8112) [ywh 2008-06-27]
     *
     * @param css string  CSS text to scan
     *
     * @return string  CSS text after URL path parts have been properly escaped
     */
    public static function escapeCssUrls($css) {
        $css = preg_replace_callback('/\burl\s*\((.*?)\)/iu', array('self','escapeCssUrlsHelper'), $css);
        return $css;
    }

    /** The most recent values extracted by parseCss. Used by the BAZ-2160 workaround [Jon Aquino 2007-03-08] */
    public static $lastParsedCssValues;

    //TODO: this is kept in sync with Blue Jeans.css -- perhaps just load that file here instead of keeping a hardcoded copy in the PHP?
    protected static $cssTemplate = '
/*----------------------------------------------------------------------
    This file is generated whenever you customize the appearance of your
    app. We recommend that you avoid editing this file directly; instead,
    simply use or not use these styles in your app as desired.
    For more information, contact support@ning.com.
----------------------------------------------------------------------*/


/*----------------------------------------------------------------------
    Fonts
----------------------------------------------------------------------*/
/* Font (Body Text) */
body, select, input, button, textarea, .xg_bodyfont {
    font-family:/* %textFont% */ "Lucida Grande", "Lucida Sans Unicode", Arial, clean, sans-serif;
}
/* Font (Headings) */
h1, h2, h3, h4, h5, h6,
#xg_sitename,
.xg_headingfont {
    font-family:/* %headingFont% */ Georgia, "Times New Roman", Times, serif;
}


/*----------------------------------------------------------------------
    Colors
----------------------------------------------------------------------*/
/* Main Color */
body,
#xg_navigation li.this a,
.xg_bodytexgcolor {
    color:/* %moduleBodyTextColor% */ #333333;
}
a, .xg_linkcolor {
    color:/* %siteLinkColor% */ #283F4B;
}
a.button, button, input.button,
a.button:hover, button:hover, input.button:hover {
    color:/* %buttonTextColor% */ #FFF;
    background:/* %siteLinkColor% */ #283F4B;
}
/* Margins */
body, .xg_marginbg {
    background-color:/* %siteBgColor% */ #528097;
    background-image:/* %siteBgImage% */ url(/xn_resources/widgets/index/gfx/themes/Blue%20Jeans/bg.gif);
    background-repeat:/* %siteBgImage_repeat% */ repeat;
}
/* Header Background Color/Image */
#xg_head,
#xg_head a,
.xg_headertextcolor {
    color:/* %pageHeaderTextColor% */ #283F4B;
}
#xg, .xg_headerbg {
    background-color:/* %headBgColor% */ #B1C8D4;
    background-image:/* %headBgImage% */ url(/xn_resources/widgets/index/gfx/themes/Blue%20Jeans/header.gif);
    background-repeat:/* %headBgImage_repeat% */ no-repeat;
}
/* Page */
#xg_body,
.xg_bodybg,
.xg_floating_container {
    background-color:/* %pageBgColor% */ #ffffff;
    background-image:/* %pageBgImage% */ url(/xn_resources/widgets/index/gfx/themes/Blue%20Jeans/xg-v2.gif);
    background-repeat:/* %pageBgImage_repeat% */ repeat;
}
#xg_navigation ul li.this a,
ul.page_tabs li.this {
    background-color:/* %pageBgColor% */ #ffffff;
}
/* Module Headers */
.xg_module_head {
    color:/* %moduleHeadTextColor% */ #333333;
    background-color:/* %moduleHeadBgColor% */ #d4e2e9;
}
/* Ningbar */
/* %ningbarInvert% */
#xn_bar,
.xg_ningbarbg {
    background-color:/* %ningbarColor% */ #3F484B!important;
}
/* Footer */
#xg_foot {
    background-color:/* %ningbarColor% */ #3F484B;
}


/*----------------------------------------------------------------------
    Misc
----------------------------------------------------------------------*/
#xn_brand,
.xg_displaybrand {
    display:/* %ningLogoDisplay% */ block!important;
}

/* this color is the average between moduleBodyTextColor and pageBgColor: */
.xg_lightfont {
    color:#999999;
}
hr {
    color:#999999;
    background-color:#999999;
}
/* this color is (moduleBodyTextColor + (2 * pageBgColor))/3 */
.xg_lightborder {
    border-style:solid;
    border-color:#bbbbbb;
}
';

    protected static $cssCustom = '
/* Page Title */
#xg_body h1,
#xg_body ul.navigation a,
#xg_navigation ul li.this a,
#xg_navigation ul li a:hover,
.navigation-solo,
.navigation-solo a {
  color: #222 !important;
}
/* Page */
#xg_body,
#xg_navigation ul li.this a,
#xg_navigation ul li a:hover {
  background-color: #bed6e3;
}
/* Module Body: Background & Text */
.xg_module_body, .xg_module_foot {
  background-color: #FFF;
}
.xg_module_head {
  border-top:1px solid #9faaaf;
}
';

    protected static $cssMigrationTemplate = '
/* Page Title */
#xg_body h1,
#xg_body ul.navigation a,
#xg_navigation ul li.this a,
#xg_navigation ul li a:hover {
    color:/* %pageTitleColor% */ #EEE7AA;
}
/* Tab Color */
#xg_navigation ul li a {
    background-color:/* %headTabColor% */ #3E321E;
}
/* Module Body: Background & Text */
.xg_module_body,
.xg_module_body legend,
.xg_module_body legend.toggle a,
.xg_module_foot,
ul.page_tabs li.this {
    background-color:/* %moduleBodyBgColor% */ #720000;
    background-image:/* %moduleBgImage% */ none;
    background-repeat:/* %moduleBgImage_repeat% */ no-repeat;
}
/* Module Body: Headings */
.xg_module_body h3,
.xg_module_body caption {
    color:/* %moduleHeadingColor% */ #EEE7AA;
}';

    public function sanitizeCss ($css) { # string
        $css = strip_tags($css); // be paranoid
        $css = str_replace('\\','', $css); // Discard all backslashes. Sorry dudes, look for other ways...
        $css = preg_replace_callback('#(?<=\w)(\s*)(/\*)#',__CLASS__.'::_addSpaceBeforeComments',$css); // Protect against ur/**/l()
        // hacks like -mo\007A-binding or exp/*comment*/ression are already processed by the previous checks.
        $css = preg_replace('/@import/i', '@im /*disabled for the security reasons*/port', $css);
        $css = preg_replace('/\bexpression\b/i', 'expre  /*disabled for the security reasons*/ssion', $css);
        $css = preg_replace('/-moz-binding\b/i', '-moz /*disabled for the security reasons*/-binding', $css);

        return $css;
    }

    public static function _addSpaceBeforeComments($m) {
        return (strlen($m[1]) ? $m[1] : ' ') . $m[2];
    }

    /**
     * Outputs JavaScript for initializing the advertisement block.
     *
     * @param XN_Content|W_Content  a User object for a user's theme, or null for the site-wide theme
     */
    public static function outputAdInitScript($user) {
        if ($user && ! self::getThemeCssFilename($user)) { $user = NULL; }
        $adColors = self::getAttribute($user, 'adColors');
        if (! $adColors) {
            $adColors = self::adColors($user);
            // Lock for at least a couple of minutes, because of NFS caching [Jon Aquino 2008-05-07]
            if (XG_Cache::lock('update-ad-colors-for-' . ($user ? $user->title : 'site'), 300)) {
                self::setAttribute($user, 'adColors', $adColors);
                self::saveAttributes($user);
            }
        }
        self::outputAdInitScriptProper(unserialize($adColors));
    }

    /**
     * Outputs JavaScript for initializing the advertisement block.
     *
     * @param $colors array  colors for border, bg, link, text, url, e.g., FFFFFF
     */
    protected static function outputAdInitScriptProper($colors) {
        // this results in ads not showing. should we use defaults instead? [ywh 2008-05-29]
        if (! $colors) { return; }  // Just in case [Jon Aquino 2008-05-07]
        ?>
        <script type="text/javascript">
            google_ad_client = "pub-5349214509828986";
            google_ad_width = 160;
            google_ad_height = 600;
            google_ad_format = "160x600_as";
            google_ad_type = "text";
            google_ad_channel = "";
            google_color_border = "<%= $colors['bg'] %>";
            google_color_bg = "<%= $colors['bg'] %>";
            google_color_link = "<%= $colors['link'] %>";
            google_color_text = "<%= $colors['text'] %>";
            google_color_url = "<%= $colors['url'] %>";
        </script>
    <?php
    }

    /**
     * Returns the colors to use for the ad's border, bg, link, text, url, e.g., FFFFFF
     *
     * @param XN_Content|W_Content  a User object for a user's theme, or null for the site-wide theme
     * @return string  serialized array of named colors
     */
    private static function adColors($user) {
        $css = @file_get_contents(self::getThemeCssFilename($user));
        if (! $css) { return serialize(array()); } // Just in case [Jon Aquino 2008-05-07]
        list($themeSettings, $imagePaths) = self::parseCss($css, $user);
        return serialize(self::adColorsProper($themeSettings));
    }

    /**
     * Returns the colors to use for the ad's border, bg, link, text, url, e.g., FFFFFF
     *
     * @param array  values from the CSS file
     * @return array  the colors, e.g., bg => FFFFFF
     */
    protected static function adColorsProper($themeSettings) {
        return array(
                'bg' => $themeSettings['moduleBodyBgColor'] ? $themeSettings['moduleBodyBgColor'] : $themeSettings['pageBgColor'],
                'link' => $themeSettings['siteLinkColor'],
                'text' => $themeSettings['moduleBodyTextColor'],
                'url' => $themeSettings['siteLinkColor']);
    }

    /**
     * Re-saves the network's pre-3.3 CSS files so that they will use CDN URLs
     */
    public static function doPre33Migration() {
        error_log('Doing pre-3.3 CSS migration for network');
        self::setThemeCss(NULL, self::applyCdnUrls(self::getThemeCss(NULL)));
        self::setCustomCss(NULL, self::applyCdnUrls(self::getCustomCss(NULL)));
        self::saveAttributes(NULL);
    }


}
