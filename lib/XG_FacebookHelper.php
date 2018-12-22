<?php
define('NF_APP_BASE',$_SERVER['DOCUMENT_ROOT']);
/* Content and profile caching */
XG_App::includeFileOnce('/lib/XG_Cache.php');
/* Query result caching */
XG_App::includeFileOnce('/lib/XG_Query.php');

define('FACEBOOK_DATA_DIR', NF_APP_BASE.'/xn_resources/ext/facebook');

class XG_FacebookHelper {

    const PHOTO_FULLSIZE_FEED_WIDTH = 800;
    const PHOTO_FULLSIZE_FEED_HEIGHT = 600;
    const PHOTO_LG_EMBED_ASPECT_RATIO = 0.739;
    const PHOTO_SM_EMBED_ASPECT_RATIO = 0.677;
	const FACEBOOK_MAX_CANVAS_URL_LENGTH = 20;

    /**
	 *  Sets up a facebook application. Initializes all necessary configuration, writes all necessary files.
     *
	 *  @param      $appType	string		App type: photo|music|video
	 *  @param		$fbKey		string		Facebook API key
	 *  @param		$fbSecret	string		Facebook secret
	 *  @param		$tabName	string		App name for different tabs/places on facebook (entered by NC)
     *  @return     void
     */
	public static function setupApp($appType, $fbKey, $fbSecret, $tabName) {
		if (!is_dir(FACEBOOK_DATA_DIR . '/' . $appType)) {
			@mkdir(FACEBOOK_DATA_DIR . '/' . $appType, 0750, TRUE);
		}

		$w = W_Cache::getWidget('main');
		$w->privateConfig["facebook-$appType-enabled"] = 'true';
		$w->privateConfig["facebook-$appType-apiKey"] = $fbKey;
		$w->privateConfig["facebook-$appType-secret"] = $fbSecret;
		$w->privateConfig["facebook-$appType-contentMode"] = 'default';
		$w->privateConfig["facebook-$appType-tabName"] = $tabName;

		switch($appType) {
			case 'video':
				$embedCode = self::_generateVideoEmbedCode(380,318);
				$embedAttachment = self::_generateVideoEmbedCode(316,264);
				break;
			case 'photo':
				$embedCode = self::_generatePhotoEmbedCode(380,300);
				$embedAttachment = self::_generatePhotoEmbedCode(316,248);
				break;
			case 'music':
            	$embedCode = self::_generateMusicEmbedCode(380,238);
				$embedAttachment = self::_generateMusicEmbedCode(316,238);
				break;
			default: throw new Exception("Unknown app type `$appType'");
        }
		$w->privateConfig["facebook-$appType-appFbml"] = $embedCode;
		$w->privateConfig["facebook-$appType-attachmentFbml"] = $embedAttachment;

		self::autoConfigureApp($w, $appType, $fbKey, $fbSecret, $tabName);

        $w->saveConfig();
    }

    /**
	 *  Automatically configures all necessary application settings.
     *
	 *	@param		$w			W_Widget	Current widget
	 *  @param      $appType	string		App type: photo|music|video
	 *  @param		$fbKey		string		Facebook API key
	 *  @param		$fbSecret	string		Facebook secret
	 *  @param		$tabName	string		App name for different tabs/places on facebook (entered by creator)
     *  @return     void
     */
    public static function autoConfigureApp($w, $appType, $fbKey, $fbSecret, $tabName) {
		XG_App::includeFileOnce('/lib/ext/facebook/facebook.php');
		$fb = new Facebook($fbKey, $fbSecret);

		$props = $fb->api_client->admin_getAppProperties(array('canvas_name'));
		$w->privateConfig["facebook-$appType-canvasName"] = $props['canvas_name'];

		$cb = self::getCallbackUrl($appType);
		$fbCanvas = self::getFacebookEmbedAppUrl($appType);
		$data = array(
			'callback_url'			=> $cb,
			'wide_mode'				=> true,
			'default_fbml'			=> self::getDefaultPlayerEmbedCode($appType),
			'desktop' 				=> 0,
			'installable'			=> 1,
			'use_iframe'			=> 0,
			// old: message attachments
			'message_action'		=> $tabName,
			'message_url'			=> "$cb?xg_mode=attach_intf",
			// new: profile tab
			'tab_default_name'		=> $tabName,
			'profile_tab_url'		=> '?profile=1', 		// just a fake parameter, not used yet
			// new: publisher
			'publish_action'		=> $tabName,
			'publish_url'        	=> $cb, 				// uses facebook "method" parameter to route request
			'publish_self_action'	=> $tabName,
			'publish_self_url'   	=> "$cb?xg_my=1",		// uses facebook "method" parameter to route request
			'dashboard_url'			=> $fbCanvas,
			//'info_changed_url' => 'info_changed_url',
			//'see_all_url' => 'see_all_url',
			//'edit_url' => 'edit_url',
		);
		$fb->api_client->admin_setAppProperties($data);
		$app = XN_Application::load();
		switch($appType) {
			case 'photo':
				$typeName = 'photos';
				$appUrl = xg_absolute_url(W_Cache::getWidget('photo')->buildUrl('index','index'));
				break;
			case 'video':
				$typeName = 'video';
				$appUrl = xg_absolute_url(W_Cache::getWidget('video')->buildUrl('index','index'));
				break;
			case 'music':
				$typeName = 'songs';
				$appUrl = xg_absolute_url('/');
				break;
			default: throw new Exception("Unknown app type `$appType'");
		}
		$oneLineStory = array('{*actor*} {*did*} <a href="'.$fbCanvas.'">'.$typeName.'</a> from <a href="'.$appUrl.'">'.$app->name.'</a>{*recipient*}.');
		$fullStory = array(
			'template_title' => '{*actor*} {*did*} <a href="'.$fbCanvas.'">'.$typeName.'</a> from <a href="' . $appUrl . '">'.$app->name.'</a>{*recipient*}.',
			'template_body'  =>
				'{*embed*}
					{*comment_block*}
					<div style="font-size:9px; margin:6px 0 2px;">
						<a href="' . $appUrl . '">See more ' . $typeName . ' at ' . $app->name . '</a>
					</div>
				',
		);
		$oldId = $w->privateConfig["facebook-$appType-feedStoryId"];
		$w->privateConfig["facebook-$appType-feedStoryId"] = $fb->api_client->feed_registerTemplateBundle($oneLineStory, array(), $fullStory);
		if ($oldId) {
			try {
				$fb->api_client->feed_deactivateTemplateBundleByID($oldId);
			} catch (Exception $e) {
				// do nothing
			}
		}
    }

    /**
	 *  Disabled a facebook application.
     *
	 *  @param      $appType	string		App type: photo|music|video
     *  @return     void
     */
    public static function disableApp($appType) {
		$w = W_Cache::getWidget('main');
		$fbKey = $w->privateConfig["facebook-$appType-apiKey"];
		$fbSecret = $w->privateConfig["facebook-$appType-secret"];

		$w->privateConfig["facebook-$appType-enabled"] = 'false';
		$w->privateConfig["facebook-$appType-apiKey"] = '';
		$w->privateConfig["facebook-$appType-secret"] = '';
		$w->privateConfig["facebook-$appType-canvasName"] = '';
		$w->privateConfig["facebook-$appType-contentMode"] = '';
		$w->privateConfig["facebook-$appType-appFbml"] = '';
		$w->privateConfig["facebook-$appType-attachmentFbml"] = '';
		$w->privateConfig["facebook-$appType-tabName"] = '';
		if ($w->privateConfig["facebook-$appType-feedStoryId"]) {
			try {
				XG_App::includeFileOnce('/lib/ext/facebook/facebook.php');
				$fb = new Facebook($fbKey, $fbSecret);
				$fb->api_client->feed_deactivateTemplateBundleByID($w->privateConfig["facebook-$appType-feedStoryId"]);
			} catch (Exception $e) {
				// do nothing
			}
		}
		$w->privateConfig["facebook-$appType-feedStoryId"] = '';
		$w->saveConfig();
    }

//** Getters
    public static function isAppEnabled($appType) {
	   return self::_configValue($appType,'enabled') == 'true';
    }
    /**
	 *  Returns TRUE if a facebook app uses the old implementation (code generation)
	 *  instead of the new one.
     *
     *  @return     bool
     */
    public static function isAppDeprecated($appType) {
	   return self::_configValue($appType,'enabled') == 'true' && !self::_configValue($appType,'tabName');
    }
    /**
	 *  Returns the facebook application URL
     *
     *  @return     string
     */
    public static function getFacebookEmbedAppUrl($appType) {
       return 'http://apps.facebook.com/' . self::_configValue($appType, 'canvasName');
    }
    /**
	 *  Returns the application canvas name
     *
     *  @return     string
     */
	public static function getFacebookCanvas($appType) {
	   return self::_configValue($appType, 'canvasName');
    }
	public static function getFacebookTabName($appType) {
	   return self::_configValue($appType, 'tabName');
    }
    public static function getFacebookApiKey($appType) {
        return self::_configValue($appType, 'apiKey');
    }
    public static function getFacebookApiSecret($appType) {
        return self::_configValue($appType, 'secret');
    }
    public static function getCallbackUrl($appType) {
		return xg_absolute_url("/lib/scripts/facebook/$appType.php");
    }
	public static function getMessageCallbackUrl($appType) {
		return xg_absolute_url("/lib/scripts/facebook/$appType.php");
    }

    public static function getFacebookDisplayType($appType) {
        return self::_configValue($appType, 'contentMode');
    }
    public static function setFacebookDisplayType($appType, $displayType) {
		$w = W_Cache::getWidget('main');
		$w->privateConfig["facebook-$appType-contentMode"] = $displayType;
        $w->saveConfig();
    }

//** Deprecated: will be removed soon
	// Old names used in previous versions.
	protected static $oldNames = array(
		'enabled' => 'facebookEnabled',
	    'apiKey' => 'facebookApiKey',
		'secret' => 'facebookApiSecret',
    	'canvasName' => 'facebookAppUrl',
    	'contentMode' => 'facebookDisplayType',
	);

    protected static function _configValue($appType, $name) { # string
    	$w = W_Cache::getWidget('main');
		if (!$value = $w->privateConfig["facebook-$appType-$name"]) {
			$value = $w->config[self::$oldNames[$name] . '-' . $appType];
		}
		return $value;
    }

    /**
	 *  BAZ-7488: Migrate configuration variables into private section. Return TRUE
     *
     *  @return     bool
     */
	public static function migrateConfig() {
		$w = W_Cache::getWidget('main');
		$c = 0;
		foreach(array('photo','video','music') as $appType) {
			foreach(self::$oldNames as $new=>$old) {
				$o = "$old-$appType";
				$n = "facebook-$appType-$new";
				if ($w->config[$o]) {
					$w->privateConfig[$n] = $w->config[$o];
					$w->config[$o] = null;
					$c++;
				} else if ($w->privateConfig[$o]) {
					$w->privateConfig[$n] = $w->privateConfig[$o];
					$w->privateConfig[$o] = null;
					$c++;
				}
			}
		}
		if ($c) {
			$w->saveConfig();
		}
		return $c != 0;
    }

    public static function getDefaultPlayerEmbedCode($appType) {
		switch($appType) {
			case 'photo':
				return '<fb:wide>' . self::_generatePhotoEmbedCode(380,300) . '</fb:wide>' .
						'<fb:narrow>' . self::_generatePhotoEmbedCode(180,142) . '</fb:narrow>';
			case 'video':
				return '<fb:wide>' . self::_generateVideoEmbedCode(380,318) . '</fb:wide>' .
						'<fb:narrow>' . self::_generateVideoEmbedCode(180,150) . '</fb:narrow>';
			case 'music':
				return '<fb:wide>' . self::_generateMusicEmbedCode(380,238) . '</fb:wide>' .
						'<fb:narrow>' . self::_generateMusicEmbedCode(180,238) . '</fb:narrow>';
			default: throw new Exception("Unknown app type `$appType'");
		}
    }

//** Implementation
	// Returns the Photo embed code
	protected static function _generatePhotoEmbedCode($width, $height) { # string
        $codetype = 'photo';
        $width = intval($width);
		$height = intval($height);

        $photo = W_Cache::getWidget('photo');
        $photo->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');

        $feedParams = array('x' => Photo_SecurityHelper::embeddableAccessCode());
        // must add flash=true to the URLs called from within the embed because flash adds & variables
        $configUrl = urlencode(XG_HttpHelper::addParameters(xg_absolute_url($photo->buildUrl('photo', 'showPlayerConfig')), array('flash' => 'true', 'x' => $feedParams['x'])));

        $contentMode = self::_configValue($codetype, 'contentMode');
        $ratio = ($width < 200) ? self::PHOTO_SM_EMBED_ASPECT_RATIO : self::PHOTO_LG_EMBED_ASPECT_RATIO;
        $feedParams['photo_width'] = $width;
        $feedParams['photo_height'] = intval($width * $ratio);

        $feedUrl = urlencode(xg_absolute_url($photo->buildUrl('photo', 'slideshowFacebookFeed', $feedParams)));
        $feedParams['photo_width'] = self::PHOTO_FULLSIZE_FEED_WIDTH;
        $feedParams['photo_height'] = self::PHOTO_FULLSIZE_FEED_HEIGHT;
        $fullsizeFeedUrl = xg_absolute_url($photo->buildUrl('photo', 'slideshowFacebookFeed', $feedParams));
        $fullsizeUrl = urlencode(xg_absolute_url($photo->buildUrl('photo','slideshow',array(
			'feed_url' => $fullsizeFeedUrl
		))));
		$flashvars = "config_url=$configUrl&autoplay=on&feed_url=$feedUrl&fullsize_url=$fullsizeUrl";
		$image = xg_cdn("/xn_resources/widgets/index/gfx/facebook/$codetype-$width-still.png");
		$flashPlayer = xg_cdn("/xn_resources/widgets/$codetype/slideshowplayer/slideshowplayer.swf");

		$fbmlEmbed = '<fb:swf imgsrc="'.$image.'" swfsrc="'.$flashPlayer.'" flashvars="'.$flashvars.'" width="'.$width.'" height="'.$height.'" scale="noscale"></fb:swf>';
		return $fbmlEmbed;
    }

	// Returns the Music embed code
	protected static function _generateMusicEmbedCode($width, $height) { # string
		$codetype = 'music';

		$placeholderUrl = urlencode(xg_cdn("/xn_resources/widgets/$codetype/gfx/placeholder.png")); // ?flash=true

        $music = W_Cache::getWidget('music');

        $feedUrl = urlencode(xg_absolute_url($music->buildUrl('track', 'listFacebook', array('fmt' => 'xspf'))));
        // this xml file is kept updated by bazel but due to it being static content is subject to akamai/rslv caching
		$configUrl = urlencode(xg_cdn('/xn_resources/instances/music/playlist/music-config.xml'));
		$appUrl = xg_absolute_url('');
        $flashvars = "configXmlUrl=$configUrl&placeholder_url=$placeholderUrl&playlist_url=$feedUrl&autoplay=on&xn_app_url=$appUrl&display_add_links=off&display_logo=true";

		$image = xg_cdn("/xn_resources/widgets/index/gfx/facebook/$codetype-$width-still.png");
		$flashPlayer = xg_cdn("/xn_resources/widgets/$codetype/swf/xspf_player.swf");

		$fbmlEmbed = '<fb:swf imgsrc="'.$image.'" swfsrc="'.$flashPlayer.'" flashvars="'.$flashvars.'" width="'.$width.'" height="'.$height.'" scale="noscale"></fb:swf>';
		return $fbmlEmbed;
    }

	// Returns the VIdeo embed code. Video doesn't use feeds -- video is included in the config file
	protected static function _generateVideoEmbedCode($width,$height) { # void
		$codetype = 'video';

        $video = W_Cache::getWidget('video');
        $video->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');

        $params = array('x' => Video_SecurityHelper::embeddableAccessCode(), 'dispType' => self::_configValue($codetype,'contentMode'));
		$configUrl = urlencode(xg_absolute_url($video->buildUrl('video', 'showFacebookPlayerConfig', $params)));
		$flashvars = "config_url=$configUrl&autoplay=on&embed_btn=on&share_btn=on&app_link=on&fullscreen_btn=off";

		$image = xg_cdn("/xn_resources/widgets/index/gfx/facebook/$codetype-$width-still.png");
		$flashPlayer = xg_cdn("/xn_resources/widgets/$codetype/flvplayer/flvplayer.swf");

		$fbmlEmbed = '<fb:swf imgsrc="'.$image.'" swfsrc="'.$flashPlayer.'" flashvars="'.$flashvars.'" width="'.$width.'" height="'.$height.'" scale="noscale"></fb:swf>';
		return $fbmlEmbed;
    }

}
