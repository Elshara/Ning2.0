<?php
/**
 * Handles facebook promotion
 */

XG_App::includeFileOnce('/lib/XG_FacebookHelper.php');

class Index_FacebookController extends W_Controller {

    const FB_ERROR_BAD_API_KEY = 'bad-api-key';
    const FB_ERROR_BAD_SIGNATURE = 'bad-sig';
    const FB_ERROR_UNKNOWN = 'unknown';

    protected function _before() {
        XG_SecurityHelper::redirectIfNotAdmin();
    }

    public function action_setup() {
        $this->fbVideoEnabled = XG_FacebookHelper::isAppEnabled('video');
		$this->fbVideoNeedsUpgrade = XG_FacebookHelper::isAppDeprecated('video');
        $this->fbVideoType = XG_FacebookHelper::getFacebookDisplayType('video');
        $this->fbVideoUrl = XG_FacebookHelper::getFacebookEmbedAppUrl('video');

        $this->fbPhotoEnabled = XG_FacebookHelper::isAppEnabled('photo');
		$this->fbPhotoNeedsUpgrade = XG_FacebookHelper::isAppDeprecated('photo');
        $this->fbPhotoType = XG_FacebookHelper::getFacebookDisplayType('photo');
        $this->fbPhotoUrl = XG_FacebookHelper::getFacebookEmbedAppUrl('photo');

        $this->fbMusicEnabled = XG_FacebookHelper::isAppEnabled('music');
		$this->fbMusicNeedsUpgrade = XG_FacebookHelper::isAppDeprecated('music');
        $this->fbMusicType = XG_FacebookHelper::getFacebookDisplayType('music');
        $this->fbMusicUrl = XG_FacebookHelper::getFacebookEmbedAppUrl('music');

		$this->fbAppCreated = $_REQUEST['created'];
	}

    public function action_instructions($errCode = null) {
        $appType = $_REQUEST['appType'];
    	switch($appType) {
    		case 'photo':
    			$this->pageTitle = xg_html('FACEBOOK_SLIDESHOW_PLAYER_TITLE');
    			break;
			case 'video':
		        $this->pageTitle = xg_html('FACEBOOK_VIDEO_PLAYER_TITLE');
    			break;
			case 'music':
		        $this->pageTitle = xg_html('FACEBOOK_MUSIC_PLAYER_TITLE');
    			break;
			default:
	            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
		}
        $this->currentStep = $_GET['step'] ? intval($_GET['step']) : 1;
        if ($this->currentStep < 1 || $this->currentStep > 3) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
		}

		$this->appType = $appType;
                $this->upgrade = $_GET['upgrade'];
		$this->fbKey = XG_FacebookHelper::getFacebookApiKey($appType);
		$this->fbSecret = XG_FacebookHelper::getFacebookApiSecret($appType);
		$this->tabName = XG_FacebookHelper::getFacebookTabName($appType);

		if ($errCode) {
			// process errors submitting form
			switch($errCode) {
				case self::FB_ERROR_BAD_API_KEY:
					$errorMessage = xg_html('FACEBOOK_INVALID_API_KEY');
					$errorKey = 'fbApiKey';
					break;
				case self::FB_ERROR_BAD_SIGNATURE:
					$errorMessage = xg_html('FACEBOOK_INVALID_API_SECRET');
					$errorKey = 'fbApiSecret';
					break;
				default:
					$errorMessage = xg_html('FACEBOOK_UNKNOWN_ERROR_OCCURRED');
					$errorKey = 'fbApiKey';
			}

			$this->fbKey = $_REQUEST['fbApiKey'];
			$this->fbSecret = $_REQUEST['fbApiSecret'];
			$this->tabName = $_REQUEST['tabName'];

			$this->error = true;
			$this->errorMessage = $errorMessage;
			$this->errorKey = $errorKey;
		} else {
			$this->error = false;
		}
	}


	public function action_createApp() {
        $appType = $_REQUEST['appType'];
        $fbKey = trim($_POST['fbApiKey']);
        $fbSecret = trim($_POST['fbApiSecret']);
        $tabName = trim($_POST['tabName']);

        // validation (BAZ-8935) [ywh 2008-08-13]
        try {
            XG_FacebookHelper::setupApp($appType, $fbKey, $fbSecret, $tabName);
			$this->redirectTo('setup', 'facebook',"?created=$appType");
        } catch (Exception $e) {
            $message = $e->getMessage();

            if (preg_match('/incorrect\s+sig/ui', $message)) {
                $errCode = self::FB_ERROR_BAD_SIGNATURE;
            } else if (preg_match('/invalid\s+api\s+key/ui', $message)) {
                $errCode = self::FB_ERROR_BAD_API_KEY;
            } else {
                error_log('Facebook promotion - error setting up [' . $appType . ']; ' . $message);
                $errCode = self::FB_ERROR_UNKNOWN;
            }

			$_GET['step'] = 3;
            $this->forwardTo('instructions', 'facebook', array($errCode));
        }
    }

    public function action_updateEmbedOptions() {
		if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
		XG_FacebookHelper::setFacebookDisplayType($_REQUEST['appType'], $_REQUEST['displayType']);
        $this->redirectTo('setup', 'facebook');
    }

    public function action_disableEmbed() {
		if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
		XG_FacebookHelper::disableApp($_REQUEST['appType']);
        $this->redirectTo('setup', 'facebook');
    }

    public function action_postInstructions() {
    }
}
