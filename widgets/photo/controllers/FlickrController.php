<?php

/**
 * Dispatches requests pertaining to Flickr imports.
 */
class Photo_FlickrController extends W_Controller {

    //connect timeout in seconds
    const FLICKR_HTTP_CONNECTION_TIMEOUT = 45;



    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Photo_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PrivacyHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_JsonHelper.php');
        Photo_PrivacyHelper::checkMembership();
        // @todo: check to see if the own has enabled a key and throw to a message screen if not
    }

    public function action_index() {


        // check to see if the user has a token


        $widget = W_Cache::current('W_Widget');
        $signedCall = self::signCall($widget->privateConfig['flickrSecret'], array('api_key'=>$widget->privateConfig['flickrKey'], 'perms'=>'read'));
        $signedString = $signedCall['signedString'];
        $url = "http://www.flickr.com/services/auth/?api_key=" . $widget->privateConfig['flickrKey'];
        $url .= "&perms=read&api_sig=" . $signedString;
        header("Location: $url");


    }

    public function action_error() {
    }

    public function action_token() {
        $widget = W_Cache::current('W_Widget');
        $frob = $_GET['frob'];
        $keyValues = array('frob'=>$frob, 'method'=>'flickr.auth.getToken', 'api_key'=>$widget->privateConfig['flickrKey'], 'format'=>'php_serial');
        $xnresponse = self::signCall($widget->privateConfig['flickrSecret'], $keyValues);
        $xnurl = $xnresponse['url'];
        $xncall = self::makeCall($xnurl);
        $xnresult = unserialize($xncall);
        if ($xnresult['stat'] == 'ok') {
            $token  = $xnresult['auth']['token']['_content'];
            $nsid   = $xnresult['auth']['user']['nsid'];
            $this->redirectTo('import','flickr', array('ns'=>$nsid, 't'=>$token));
        } else {
            echo var_dump($xnresult);
        }
    }

    public function action_runImport() {
        $widget = W_Cache::current('W_Widget');
        if (isset($_POST['type']) && isset($_POST['extras']) && mb_strlen($_POST['auth_token']) && mb_strlen($_POST['nsid'])) {
            $keyValues = array();
            switch ($_POST['type']) {
                case 'recentX':
                    $keyValues['method'] = 'flickr.photos.search';
                    $keyValues['user_id'] = $_POST['nsid'];
                    $keyValues['per_page'] = $_POST['extras'];
                    break;
                case 'getall':
                    $keyValues['method'] = 'flickr.photos.search';
                    $keyValues['user_id'] = $_POST['nsid'];
                    $keyValues['per_page'] = '500';
                    if (isset($_POST['page'])) {
                        $keyValues['page'] = $_POST['page'];
                    }
                    break;
                case 'gettagged':
                    $keyValues['method'] = 'flickr.photos.search';
                    $keyValues['user_id'] = $_POST['nsid'];
                    $keyValues['tags'] = $_POST['extras'];
                    $keyValues['per_page'] = '500';
                    if (isset($_POST['page'])) {
                        $keyValues['page'] = $_POST['page'];
                    }
                    break;
                case 'chosenset':
                    $keyValues['method'] = 'flickr.photosets.getPhotos';
                    $keyValues['photoset_id'] = $_POST['extras'];
                    $keyValues['per_page'] = '500';
                    if (isset($_POST['page'])) {
                        $keyValues['page'] = $_POST['page'];
                    }
                    break;
            }
            $keyValues['extras'] = "geo,tags";
            $keyValues['api_key'] = $widget->privateConfig['flickrKey'];
            $keyValues['format'] = 'json';
            $keyValues['auth_token'] = $_POST['auth_token'];
            $xnresponse = self::signCall($widget->privateConfig['flickrSecret'], $keyValues);
            $xnurl = $xnresponse['url'];
            $xncall = self::makeCall($xnurl);
            echo str_replace('jsonFlickrApi','',$xncall);
        }
    }

    public function action_importPhoto() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if (isset($_POST['url']) && isset($_POST['title'])) {
            $format = 'image/jpeg';
            // do we need to call out to flickr for the description or original size photo?
            if($_POST['desc'] == "true" or $_POST['orig'] == "true") {
                $widget = W_Cache::current('W_Widget');
                $this->token = urldecode($_POST['auth_token']);
                $keyValues = array('auth_token'=>$this->token, 'api_key'=>$widget->privateConfig['flickrKey'],
                                    'format'=>'php_serial', 'method'=>'flickr.photos.getInfo', 'photo_id'=>$_POST['id']);
                $xnresponse = self::signCall($widget->privateConfig['flickrSecret'], $keyValues);
                $xnurl = $xnresponse['url'];
                $xncall = self::makeCall($xnurl);
                $xnresult = unserialize($xncall);
                if (is_array($xnresult) && $xnresult['stat'] == 'ok') {
                    if ($_POST['orig'] == "true") {
                        if (mb_strlen($xnresult['photo']['originalformat']) > 0) {
                            $imageUrl = "http://farm" . $xnresult['photo']['farm'] . ".static.flickr.com/" . $xnresult['photo']['server'] . "/" . $xnresult['photo']['id'] . "_" . $xnresult['photo']['originalsecret'] . "_o." . $xnresult['photo']['originalformat'];
                            $format = 'image/' . $xnresult['photo']['originalformat'];
                        }
                    }
                } else {
                    echo var_dump($xnresult);
                }
            }
            if (! $imageUrl) {
                $imageUrl = $_POST['url'];
            }
            $response = XN_REST::post( '/content?binary=true&type=Photo', file_get_contents( $imageUrl ), $format);
            $photoObject = XN_AtomHelper::loadFromAtomFeed( $response, 'XN_Content');
            $photo = W_Content::load($photoObject);
            Photo_PhotoHelper::initialize($photo);
            $approved = Photo_SecurityHelper::passed(Photo_SecurityHelper::checkCurrentUserIsAdmin($this->_user)) || !Photo_SecurityHelper::isApprovalRequired() ? 'Y' : 'N';
            $photo->setApproved($approved);
            $user = Photo_UserHelper::load($this->_user);
            $photo->setVisibility(Photo_UserHelper::get($user, 'defaultVisibility'));
            $imageUrl = $photo->fileUrl('data') . "?height=50";
            $photo->title = mb_substr($_POST['title'], 0, 200);
            $photo->my->mimeType = $format;
            $imageUrl = $photo->fileUrl('data') . "?width=50";
            if ($_POST['lat'] != 0 && $_POST['lng'] != 0) {
                $photo->my->lat = $_POST['lat'];
                $photo->my->lng = $_POST['lng'];
            }
            if ($_POST['desc'] == "true") {
                if (is_array($xnresult) && $xnresult['stat'] == 'ok') {
                    $photo->description = $xnresult['photo']['description']['_content'];
                }
            }
            if (mb_strlen($_POST['tags'])) {
                XG_App::includeFileOnce('/lib/XG_TagHelper.php');
                XG_TagHelper::updateTagsAndSave($photo, $_POST['tags']);
            } else {
                $photo->save();
            }
            if ($approved === 'Y') {
                Photo_UserHelper::addPhotos($user)->save();
            }

            try {
                Photo_JsonHelper::outputAndExit(array('url'=>$imageUrl));
            } catch (Exception $e) {
                Photo_JsonHelper::handleExceptionInAjaxCall($e);
            }
        }
    }


    public function action_import() {
        // TODO: throw an error if the user is not signed in or not a member of this app
        $widget = W_Cache::current('W_Widget');
        // nsid is the user ID (in Flickr's terminology) [Jon Aquino 2007-02-09]
        $this->nsid = urldecode($_GET['ns']);
        $this->token = urldecode($_GET['t']);
        $this->user = Photo_UserHelper::load($this->_user);
        Photo_UserHelper::set($this->user, 'flickrAuthentication', 'Y');
        $this->user->save();
        $keyValues = array('auth_token'=>$this->token, 'api_key'=>$widget->privateConfig['flickrKey'],
                            'user_id'=>$this->nsid, 'format'=>'php_serial', 'method'=>'flickr.photosets.getList');
        $xnresponse = self::signCall($widget->privateConfig['flickrSecret'], $keyValues);
        $xnurl = $xnresponse['url'];
        $xncall = self::makeCall($xnurl);
        $xnresult = unserialize($xncall);
        if (is_array($xnresult) && $xnresult['stat'] == 'ok') {
            // @todo: move these into a helper [Phil McCluskey 2007-01-25]
            $this->numRecent = '<select id="numRecent">';
            foreach (array(10,20,50,100) as $option) {
                $this->numRecent .= '<option value="' . $option . '">' . $option . '</option>';
            }
            $this->numRecent .= '</select>';
            if (sizeof($xnresult['photosets']['photoset']) > 0) {
                $this->setOptions = '<select id="setChooser">';
                foreach ($xnresult['photosets']['photoset'] as $photoset) {
                    $this->setOptions .= '<option value="' . $photoset['id'] .'">' . mb_substr($photoset['title']['_content'],0,30) . '</option>';
                }
                $this->setOptions .= '</select>';
            }
        } else {
            $this->error == true;
        }
    }

    /**
     * Post flickr import processing to clean up any photos which haven't been initialized
     */
    public function action_postFlickr() {
        $photos = XN_Query::create('content')
            ->filter('owner')
            ->filter('type','=','Photo')
            ->filter('my->mozzle','=',null)
            ->filter('my->ratingCount','=',null)
            ->filter('my->viewCount','=',null)
            ->execute();

        foreach($photos as $photo) {
            $photo->my->mozzle = 'photo';
            Photo_PhotoHelper::initialize($photo);
            $photo->save();
        }
        $this->redirectTo('listForContributor','photo',array('screenName' => $this->_user->screenName));
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
