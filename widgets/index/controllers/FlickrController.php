<?php

/**
 * Dispatches requests pertaining to obtaining a Flickr key.
 */
class Index_FlickrController extends W_Controller {

    //connect timeout in seconds
    const FLICKR_HTTP_CONNECTION_TIMEOUT = 45;

    protected function _before() {
        XG_SecurityHelper::redirectIfNotAdmin();
    }

    public function action_keys() {
        $app = XN_Application::load();
        $this->appName = $app->name;
        $this->flickrCallback = "http://" . $_SERVER['HTTP_HOST'] . "/photo/flickr/token";
        $curWidget = W_Cache::getWidget('photo');
        $flickrKey = $curWidget->privateConfig['flickrKey'];
        $flickrSecret = $curWidget->privateConfig['flickrSecret'];
        $defaults = array('flickr_api_key' => $flickrKey, 'flickr_secret' => $flickrSecret);
        $this->form = new XNC_Form($defaults);
    } // action_keys()

    public function action_save() {
        $app = XN_Application::load();
        $this->appName = $app->name;
        $this->flickrCallback = "http://" . $_SERVER['HTTP_HOST'] . "/photo/flickr/token";
        $curWidget = W_Cache::getWidget('photo');
        $key = trim($_POST['flickr_api_key']);
        $secret = trim($_POST['flickr_secret']);

        if (mb_strlen($key) == 0 && mb_strlen($secret) == 0) {
            $curWidget->privateConfig['flickrKey'] = $key;
            $curWidget->privateConfig['flickrSecret'] = $secret;
            $curWidget->privateConfig['flickrEnabled'] = 'N';
            $curWidget->saveConfig();
            $savedVars = array('saved'=>1);
            $this->redirectTo('keys','flickr',$savedVars);
            return;
        }

        $test = self::testKeys($key, $secret);
        if ($test['stat'] == 'ok') {
            $curWidget->privateConfig['flickrKey'] = $key;
            $curWidget->privateConfig['flickrSecret'] = $secret;
            $curWidget->privateConfig['flickrEnabled'] = 'Y';
            $curWidget->saveConfig();
            $savedVars = array('saved'=>1);
            $this->redirectTo('keys','flickr',$savedVars);
        } else {
            $curWidget->privateConfig['flickrEnabled'] = 'N';
            $curWidget->saveConfig();
            $this->error = xg_html('FLICKR_KEY_ERROR');
            $defaults = array('flickr_api_key' => $key, 'flickr_secret' => $secret);
            $this->form = new XNC_Form($defaults);
            $this->render('keys');
        }

    } // action_save()
    
    /**
     * A convenience redirect to {@link action_save()}.
     *
     * Used by Remove button in keys.php template
     *
     * @see action_see()
     */
    public function action_deactivate() {
        $_POST['flickr_api_key'] = '';
        $_POST['flickr_secret'] = '';
        $this->forwardTo('save');
    }

    public function action_setNotification() {
        $curWidget = W_Cache::getWidget('photo');
        if ($_POST['notification']) {
            $curWidget->privateConfig['promptOwnerForFlickr'] = trim($_POST['notification']);
            $curWidget->saveConfig();
            $json = new NF_JSON();
            $output = '(' . $json->encode(array()) . ')';
            header('Content-Type: text/javascript');
            echo $output;
            exit;
        }
    } // action_setNotification


    private static function testKeys($key,$secret) {
        $keyValues = array('api_key'=>$key, 'format'=>'php_serial', 'method'=>'flickr.photos.search', 'tags'=> 'ning');
        $xnresponse = self::signCall($secret, $keyValues);
        $xnurl = $xnresponse['url'];
        $xncall = self::makeCall($xnurl);
        $xnresult = unserialize($xncall);
        return $xnresult;
    }

    private static function makeCall($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, self::FLICKR_HTTP_CONNECTION_TIMEOUT);
        $response = curl_exec ($ch);
        curl_close ($ch);
        return $response;
    }

    private static function signCall($secret, $keyValues) {
        $signedString = $secret;
        $keyOrder = array_keys($keyValues);
        sort($keyOrder);
        $method = false;
        foreach ($keyOrder as $key) {
            $signedString .= $key;
            $signedString .= $keyValues[$key];
            if ($key == 'method') {
                $method = $keyValues[$key];
            }
        }
        $response = array('signedString'=>md5($signedString));
        $apiUrl = null;
        if (mb_strlen($method)) {
             $apiUrl = "http://api.flickr.com/services/rest/?";
             foreach ($keyOrder as $key) {
                 $apiUrl .= urlencode($key) . '=';
                 $apiUrl .= urlencode($keyValues[$key]) . '&';
             }
             $apiUrl .= 'api_sig=' . md5($signedString);
             $response['url'] = $apiUrl;
        }
        return $response;
    }
}
