<?php
XG_App::includeFileOnce('/lib/XG_HttpHelper.php');

/**
 * Dispatches requests pertaining to <embed> elements that can be put
 * on MySpace pages and other external sites.
 */
class Index_EmbeddableController extends W_Controller {

    /**
     * Displays the form for customizing the embeddables.
     */
    public function action_edit() {
        XG_SecurityHelper::redirectIfNotAdmin();

        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_EmbeddableHelper.php');

        $defaults = array(
            'submitAction' => 'save',
            'badgeBranding' => (XG_EmbeddableHelper::getBadgeLogoUrl() ? 'logo' : 'name'),
            'playerBranding' => 'none',
            'badgePreview' => 'profile',
            'bgColor' => XG_EmbeddableHelper::getBackgroundColor(),
            'badgeFgColor' => XG_EmbeddableHelper::getNetworkNameColor(),
            'playerBranding' => XG_EmbeddableHelper::getPlayerBrandFormat(),
        );
        $this->enabledModules = XG_ModuleHelper::getEnabledModules();
        $defaults['playerPreview'] = ($this->enabledModules['video'] ? 'video'
                : ($this->enabledModules['photo'] ? 'photo' : 'music'));

        $this->imageUrls = array(
            'bgImage' => XG_EmbeddableHelper::getBackgroundImageUrl(),
            'bgOriginalImage' => XG_EmbeddableHelper::getBackgroundOriginalImageUrl(),
            'badgeFgImage' => XG_EmbeddableHelper::getBadgeLogoUrl(),
            'playerLogoImage' => XG_EmbeddableHelper::getPlayerLogoUrl(),
        );

        //  Form may have been submitted for preview or save
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['submitAction'] == 'preview') {
                //  Form was submitted because an image was selected.  This image
                //    should be saved just to update the preview - it's not final.
                foreach (array('bgImage', 'badgeFgImage', 'playerLogoImage') as $name) {
                    switch($_POST[$name . '_action']) {
                        case 'add':
                            switch($name) {
                                case 'badgeFgImage':
                                    $defaults['badgeBranding'] = 'logo';
                                    break;
                                case 'playerLogoImage':
                                    $defaults['playerBranding'] = 'logo';
                                    break;
                            }

                            if ($name == 'bgImage') {
                                //  Keep both original and tiled versions - we'll
                                //    display the original in the picker and the
                                //    tiled in the preview
                                $newBgImageUrl = self::addCustomizationImage('bgImage', $_POST);
                                $this->imageUrls['bgOriginalImage'] = $newBgImageUrl;
                                $this->imageUrls['bgImage'] = Index_EmbeddableHelper::getTiledImageUrl(
                                        $newBgImageUrl, 500, 500, 'PlayerCustomizationImage');
                            } else {
                                $this->imageUrls[$name] = self::addCustomizationImage($name, $_POST);
                            }
                            break;
                        case 'keep':
                            $this->imageUrls[$name] = $_POST[$name . '_currentUrl'];
                            break;
                    }
                }

                //  Some settings on the page are not automatically maintained across
                //    the reload - reset them
                if (!array_key_exists('bgOriginalImage', $this->imageUrls)) {
                    $this->imageUrls['bgOriginalImage'] = $_POST['bgOriginalImage_currentUrl'];
                }
                $defaults['badgePreview'] = $_POST['badgePreview'];
                $defaults['playerPreview'] = $_POST['playerPreview'];
                $defaults['bgColor'] = $_POST['bgColor'];
                $defaults['badgeFgColor'] = $_POST['badgeFgColor'];
            }
            else if ($_POST['submitAction'] == 'save') {
                $this->forwardTo('update');
                return;
            }
        }

        W_Cache::getWidget('music')->includeFileOnce('/lib/helpers/Music_PlaylistHelper.php');
        W_Cache::getWidget('music')->includeFileOnce('/lib/helpers/Music_TrackHelper.php');

        $networkPlaylistInfo = Music_PlaylistHelper::loadOrCreateDefaultNetworkPlaylist();
        $networkPlaylist = $networkPlaylistInfo['playlist'];
        $this->defaultPlaylistUrl = Music_PlaylistHelper::getUrl($networkPlaylist->id);

        $this->form = new XNC_Form($defaults);
        $this->defaults = $defaults;
    }

    /**
     * Processes the form for customizing the embeddables.
     */
    public function action_update() {
        XG_SecurityHelper::redirectIfNotAdmin();

        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');

        $settings = array();
        $settings['embeds_backgroundColor'] = mb_substr($_POST['bgColor'], 1);
        if ($_POST['bgImage_action'] == 'keep') {
            $settings['embeds_backgroundOriginalImageUrl'] = $_POST['bgOriginalImage_currentUrl'];
            $settings['embeds_backgroundImageUrl'] = $_POST['bgImage_currentUrl'];
        } else {
            $settings['embeds_backgroundOriginalImageUrl'] = '';
            $settings['embeds_backgroundImageUrl'] = '';
        }
        if ($_POST['badgeBranding'] == 'logo' && $_POST['badgeFgImage_action'] != 'remove') {
            $settings['embeds_badgeLogoImageUrl'] = $_POST['badgeFgImage_currentUrl'];
        } else {
            $settings['embeds_badgeLogoImageUrl'] = '';
        }
        if ($_POST['playerBranding'] == 'logo'&& $_POST['playerLogoImage_action'] != 'remove') {
            $settings['embeds_playerLogoImageUrl'] = $_POST['playerLogoImage_currentUrl'];
        } else  {
            $settings['embeds_playerLogoImageUrl'] = '';
        }
                
        $settings['embeds_displayNameInPlayer'] = ($_POST['playerBranding'] == 'name' ? 'Y' : '');
        $settings['embeds_networkNameColor'] = mb_substr($_POST['badgeFgColor'], 1);

        XG_EmbeddableHelper::setEmbedCustomization($settings);

        //  TODO: delete any player customization images not currently selected?

        // Update the network badge after customization [Jon Aquino 2007-06-12]
        XG_EmbeddableHelper::generateResources();

        // Redirect to gallery page
        $this->redirectTo('edit', 'embeddable', array('saved' => 1));
    }

    public function action_updateResources(){
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        $applicationName = $this->getQueryValue('application_name');
        $applicationDescription = $this->getQueryValue('application_description', 2048);
        XG_EmbeddableHelper::generateResources($applicationName, $applicationDescription);
        $successTarget = $this->sanitizeRedirectTargetFromGet('successTarget');
        if ($successTarget === null) {
            $successTarget = '/';
        }
        header('Location: ' . $successTarget);
        exit();
    }
    /**
    * Displays a list of <embed> elements offered by the network: badges, slideshow, etc.
     */
    public function action_list() {

        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');

        $this->json = new NF_JSON();
        $enabledModules = XG_ModuleHelper::getEnabledModules();

        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        $defaultSizes = array(
                    array('label' => xg_text('LARGE_N_PIXELS_WIDE', XG_EmbeddableHelper::LARGE_SLIDESHOW_WIDTH), 'width' => XG_EmbeddableHelper::LARGE_SLIDESHOW_WIDTH, 'height' => XG_EmbeddableHelper::LARGE_SLIDESHOW_HEIGHT),
                    array('label' => xg_text('MEDIUM_N_PIXELS_WIDE', 300), 'width' => 300, 'height' => 253, 'selected' => true),
                    array('label' => xg_text('SMALL_N_PIXELS_WIDE', XG_EmbeddableHelper::SMALL_SLIDESHOW_WIDTH), 'width' => XG_EmbeddableHelper::SMALL_SLIDESHOW_WIDTH, 'height' => XG_EmbeddableHelper::SMALL_SLIDESHOW_HEIGHT));
        // Photos
        if ($enabledModules['photo']) {
            W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_AlbumHelper.php');
            $this->photoSlideshowSourceOptions = array();
            if (User::isMember($this->_user)) {
                $this->photoSlideshowSourceOptions[] = array('label' => xg_text('MY_MOST_RECENT'), 'photoSet' => 'for_contributor', 'selected' => true, 'noPhotosMessage' => xg_text('PERSON_DOES_NOT_HAVE_PHOTOS'));
            }
            $this->photoSlideshowSourceOptions[] = array('label' => xg_text('FROM_THE_BAZEL'), 'photoSet' => 'all', 'selected' => ! $this->photoSlideshowSourceOptions, 'noPhotosMessage' => xg_text('NETWORK_DOES_NOT_HAVE_PHOTOS'));
            foreach(Photo_AlbumHelper::getAllAvailableAlbums($this->_user->screenName) as $id => $name) {
                $this->photoSlideshowSourceOptions[] = array('label' => $name, 'photoSet' => 'album_' . $id, 'noPhotosMessage' => xg_text('ALBUM_DOES_NOT_HAVE_PHOTOS'));
            }
            foreach ($this->photoSlideshowSourceOptions as $option) {
                if ($option['selected']) { $this->defaultPhotoSlideshowSourceOption = $option; }
            }
            $this->photoSlideshowSizeOptions = $defaultSizes;
            foreach ($this->photoSlideshowSizeOptions as $option) {
                if ($option['selected']) { $this->defaultPhotoSlideshowSizeOption = $option; }
            }
        }

        //the correspondence table between the dropdown values of /main/facebook/setup and /main/embeddable/list
        $this->facebookPhotoSourceReference = array(
            'default' => 'all'
            );


        // Music
        W_Cache::getWidget('music')->includeFileOnce('/lib/helpers/Music_PlaylistHelper.php');
        W_Cache::getWidget('music')->includeFileOnce('/lib/helpers/Music_TrackHelper.php');
        $this->musicPlayerSourceOptions = array();
        $networkPlaylistInfo = Music_PlaylistHelper::loadOrCreateDefaultNetworkPlaylist();
        $networkPlaylist = $networkPlaylistInfo['playlist'];
        $this->musicPlayerSourceOptions[] = array('label' => xg_html('NETWORK_PLAYLIST'),
                'playlist' => 'network',
                'url' => urlencode(Music_PlaylistHelper::getUrl($networkPlaylist->id)),
                'selected' => !User::isMember($this->_user),
                'displayContributor' => 0,
                'noMusicMessage' => xg_text('NETWORK_DOES_NOT_HAVE_MUSIC'));
        $this->musicPlayerSourceOptions[] = array('label' => xg_html('MOST_RECENT'),
                'playlist' => 'recent',
                'url' => urlencode(Music_PlaylistHelper::getUrl('most_recent')),
                'displayContributor' => 1,
                'noMusicMessage' => xg_text('NETWORK_DOES_NOT_HAVE_MUSIC'));
        $this->musicPlayerSourceOptions[] = array('label' => xg_html('FEATURED_TRACKS'),
                'playlist' => 'promoted',
                'url' => urlencode(Music_PlaylistHelper::getUrl('featured')),
                'displayContributor' => 1,
                'noMusicMessage' => xg_text('NO_FEATURED_TRACKS'));
        $this->musicPlayerSourceOptions[] = array('label' => xg_html('HIGHEST_RATED'),
                'playlist' => 'rated',
                'url' => urlencode(Music_PlaylistHelper::getUrl('highest_rated')),
                'displayContributor' => 1,
                'noMusicMessage' => xg_text('NETWORK_DOES_NOT_HAVE_MUSIC'));
        if (User::isMember($this->_user)) {
            $userPlaylistInfo = Music_PlaylistHelper::loadOrCreateDefaultUserPlaylist($this->_user);
            $userPlaylist = $userPlaylistInfo['playlist'];
            $this->musicPlayerSourceOptions[] = array('label' => xg_html('MY_PLAYLIST'),
                    'url' => urlencode(Music_PlaylistHelper::getUrl($userPlaylist->id)),
                    'playlist' => 'user',
                    'selected' => true, 'userOwned' => true,
                    'displayContributor' => 0,
                    'noMusicMessage' => xg_text('PERSON_DOES_NOT_HAVE_MUSIC'));
        }
        foreach ($this->musicPlayerSourceOptions as $option) {
            if ($option['selected']) { $this->defaultMusicPlayerSourceOption = $option; }
        }
        $this->musicPlayerSizeOptions = $defaultSizes;
        foreach ($this->musicPlayerSizeOptions as $option) {
            if ($option['selected']) { $this->defaultMusicPlayerSizeOption = $option; }
        }
        $this->displayMusicContributor = $this->defaultMusicPlayerSourceOption['displayContributor'];
        //the correspondence table between the dropdown values of /main/facebook/setup and /main/embeddable/list
        $this->facebookMusicSourceReference = array(
            'default'   => 'recent',
            'rated'     => 'rated',
            'promoted'  => 'promoted'
            );

        // Video
        if ($enabledModules['video']) {
            $this->videoPlayerSourceOptions = array();
            $this->videoPlayerSourceOptions[] = array('label' => xg_html('MOST_RECENT'),
                    'videoID' => 'most_recent', 'selected' => true, 'noVideosMessage' => xg_text('NETWORK_DOES_NOT_HAVE_VIDEOS'));
            $this->videoPlayerSourceOptions[] = array('label' => xg_html('HIGHEST_RATED'),
                    'videoID' => 'highest_rated', 'noVideosMessage' => xg_text('NETWORK_DOES_NOT_HAVE_VIDEOS'));
            $this->videoPlayerSourceOptions[] = array('label' => xg_html('FEATURED'),
                    'videoID' => 'promoted', 'noVideosMessage' => xg_text('NETWORK_DOES_NOT_HAVE_FEATURED_VIDEOS'));
            if (User::isMember($this->_user)) {
                $this->videoPlayerSourceOptions[] = array('label' => xg_html('MOST_RECENT'),
                        'videoID' => 'user_most_recent', 'userOwned' => true, 'noVideosMessage' => xg_text('PERSON_DOES_NOT_HAVE_VIDEOS'));
                $this->videoPlayerSourceOptions[] = array('label' => xg_html('HIGHEST_RATED'),
                        'videoID' => 'user_highest_rated', 'userOwned' => true, 'noVideosMessage' => xg_text('PERSON_DOES_NOT_HAVE_VIDEOS'));
            }
            $this->defaultVideoPlayerSourceOption = $this->videoPlayerSourceOptions[0];
            $this->facebookVideoSourceReference = array(
                'default'   => 'most_recent',
                'promoted'  => 'promoted',
                'rated'     => 'highest_rated'
                );
        }
    }

    /**
     * Displays the Flash object for the network badge.
     *
     * Expected GET parameters:
     *     Any of the $args parameters can also be passed as GET parameters
     *
     * @param $args array  parameters:
     *     large - whether to show the large badge or the small badge
     *     customText - optional replacement for the "I'm a member of" text (applies to the user badge)
     *     fgColor - optional network-name color to override the value in badge-config.xml, e.g., FF0000
     *     fgImage - optional brand-logo URL to override the value in badge-config.xml; use "none" to specify no image
     *     fgImageWidth - optional brand-logo width to override the value in badge-config.xml
     *     fgImageHeight - optional brand-logo height to override the value in badge-config.xml
     *     bgColor - optional background color to override the value in badge-config.xml, e.g., 333333
     *     bgImage - optional background image URL to override the value in badge-config.xml; use "none" to specify no image
     *     includeFooterLink - whether to add a link back to the app
     *     internal - asking for internal badge
     */
    public function action_embeddable($args = array()) {
        $this->args = array_merge($_GET, $args);
        $this->args['panel'] = $this->args['large'] ? 'network_large' : 'network_small';
        if ($this->args['includeFooterLink']) {
            $this->args['footerLinkUrl'] = xg_absolute_url('/');
            $this->args['footerLinkHtml'] = xg_html('VISIT_APPNAME', XN_Application::load()->name);
        }
        $this->forwardTo('embeddableProper', 'embeddable', array($this->args));
    }

    /**
     * Displays the Flash object for the user or network badge.
     *
     * @param $args array  parameters:
     *     panel - which faceplate to display: "network_large", "network_small", or "user"
     *     username - the screen name, if this is a user badge
     *     customText - optional replacement for the "I'm a member of" text (applies to the user badge)
     *     fgColor - optional network-name color to override the value in badge-config.xml, e.g., FF0000
     *     fgImage - optional brand-logo URL to override the value in badge-config.xml; use "none" to specify no image
     *     fgImageWidth - optional brand-logo width to override the value in badge-config.xml
     *     fgImageHeight - optional brand-logo height to override the value in badge-config.xml
     *     bgColor - optional background color to override the value in badge-config.xml, e.g., 333333
     *     bgImage - optional background image URL to override the value in badge-config.xml; use "none" to specify no image
     *     footerLinkUrl - URL for the link back to the app, or null to not have the link
     *     footerLinkHtml - text for the footer link
     *     internal - asking for internal badge
     */
    public function action_embeddableProper($args) {
        if (array_key_exists('fgImage', $args) && $args['fgImage']!== 'none') {
            if (!array_key_exists('fgImageWidth', $args)) {
                if (preg_match('@\Wwidth=(\d+)@u', $args['fgImage'], $matches)) {
                    $args['fgImageWidth'] = $matches[1];
                }
            }
            if (!array_key_exists('fgImageHeight', $args)) {
                if (preg_match('@\Wheight=(\d+)@u', $args['fgImage'], $matches)) {
                    $args['fgImageHeight'] = $matches[1];
                }
            }
        }
        if (array_key_exists('embedId', $args)) $this->embedId = $args['embedId'];
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        $this->width = (isset($args['internal']) && $args['internal']) ? '100%' : '206';
        // Keep these heights in sync with the values in Config.as in the ActionScript [Jon Aquino 2007-06-28]
        $heights = array('network_large' => 242, 'network_small' => 104, 'user' => 64);
        $this->height = $heights[$args['panel']];
        $this->flashVars = array(
                'networkUrl' => xg_absolute_url('/'),
                'panel' => $args['panel'],
                // If overrides are unset, leave them unset - don't set them to any default values,
                // which will override badge-config.xml values [Jon Aquino 2007-06-28]
                'backgroundColor' => $args['bgColor'],
                'backgroundImageUrl' => $args['bgImage'],
                'networkNameCss' => $args['fgColor'] ? 'h1 { font-family: ' . XG_EmbeddableHelper::getNetworkNameFontFamily() . '; color: #' . $args['fgColor'] . '; }' : null,
                'logoUrl' => $args['fgImage'],
                'logoWidth' => $args['fgImageWidth'],
                'logoHeight' => $args['fgImageHeight']);
        //TODO In changing the way the sidebar is rendered I've broken this.  $args['username'] used to always be set when we reached here.
        if ($args['panel'] == 'user' && $args['username']) {
            $this->flashVars['username'] = ($args['username'] ? $args['username'] : $this->_user->screenName);
            $this->flashVars['avatarUrl'] = XG_UserHelper::getThumbnailUrl(XG_Cache::profiles($this->flashVars['username']), 48, 48);
            $this->flashVars['iAmMemberText'] = $args['customText'];
        }
        $this->swfUrl = xg_cdn($this->_widget->buildResourceUrl('swf/badge.swf'));
        $this->flashVars['configXmlUrl'] = XG_EmbeddableHelper::addGenerationTimeParameter(xg_cdn('/xn_resources/instances/main/embeddable/badge-config.xml', FALSE));
        $this->footerLinkUrl = $args['footerLinkUrl'];
        $this->footerLinkHtml = $args['footerLinkHtml'];
        $this->internal = $args['internal'];
    }

    /**
     * Saves an uploaded image so that it can be shown in the preview swfs and
     *   potentially set permanently
     *
     * @param $name string Name of image to save
     * @param $post array Post data
     * @return string File API URL for newly saved image
     */
    protected function addCustomizationImage($name, $post) {
        //  Turn the uploaded data into a PHP image object
        $uploadedData = XN_Request::uploadedFileContents($post[$name]);
        $mimeType = $post[$name . ':type'];

        //  Save the uploaded file into a content object
        $response = XN_REST::post( '/content?binary=true&type=PlayerCustomizationImage',
                $uploadedData, 'image/png');
        $newImage = XN_AtomHelper::loadFromAtomFeed($response, 'XN_Content');
        $newImageUrl = $newImage->fileUrl('data');

        //  Ensure that the URL has correct dimensions and some additional info
        $newImageDimensions = $newImage->imageDimensions('data');
        if (is_array($newImageDimensions) && isset($newImageDimensions[0]) && isset($newImageDimensions[1])) {
            $newImageUrl = XG_HttpHelper::addParameter($newImageUrl, 'width', $newImageDimensions[0]);
            $newImageUrl = XG_HttpHelper::addParameter($newImageUrl, 'height', $newImageDimensions[1]);
        }
        $newImageUrl = XG_HttpHelper::addParameter($newImageUrl,'xn_auth','no');
        $newImageUrl = XG_HttpHelper::addParameter($newImageUrl,'id', urlencode($newImage->id));
        if (preg_match('@image/(.*)@u', $post[$key . ':type'], $matches)) {
            $newImageUrl = XG_HttpHelper::addParameter($newImageUrl,'type',$matches[1]);
        }

        return $newImageUrl;
    }

    private function getQueryValue(string $key, int $maxLength = 255): string
    {
        if (! isset($_GET[$key]) || is_array($_GET[$key])) {
            return '';
        }

        $value = trim((string) $_GET[$key]);
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        if ($value === null) {
            return '';
        }
        if ($maxLength > 0) {
            $value = mb_substr($value, 0, $maxLength);
        }

        return $value;
    }

    private function sanitizeRedirectTargetFromGet(string $key): ?string
    {
        if (! isset($_GET[$key]) || is_array($_GET[$key])) {
            return null;
        }

        return XG_HttpHelper::normalizeRedirectTarget($_GET[$key]);
    }
}
