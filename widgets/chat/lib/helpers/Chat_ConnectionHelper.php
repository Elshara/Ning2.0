<?php

/**
 * Base functions used by chat loader modules to obtain information, properties, etc
 */
class Chat_ConnectionHelper {

    // TODO: Incorporate suggestions from code-review bazel-2725 [Jon Aquino 2008-09-06]

    const CHAT_STATUS_ONLINE = 'online';
    const CHAT_STATUS_OFFLINE = 'offline';
    const CHAT_STATUS_COOKIE_NAME = 'xn_chat_status';

    /**
     * Returns the user's current online status, defaulting to 'online' if no status
     *
     * @return string  current online status
     */
    public static function getUserOnlineStatus() {
        // determine online status, or set default online status
        if (array_key_exists(Chat_ConnectionHelper::CHAT_STATUS_COOKIE_NAME, $_COOKIE)) {
            $status = $_COOKIE[Chat_ConnectionHelper::CHAT_STATUS_COOKIE_NAME];
            if (! in_array($status, array(Chat_ConnectionHelper::CHAT_STATUS_ONLINE, Chat_ConnectionHelper::CHAT_STATUS_OFFLINE), true)) {
                $status = Chat_ConnectionHelper::CHAT_STATUS_ONLINE;
            }
        } else {
            $status = Chat_ConnectionHelper::CHAT_STATUS_ONLINE;
        }
        return $status;
    }

    /**
     * Returns status options for an HTML select box highlighting the current status
     *
     * @param currentStatus string  the current status, if any
     *
     * @return string  HTML select options
     */
    public static function getStatusOptions($currentStatus = null) {
        $options = '<option value="' . self::CHAT_STATUS_ONLINE . '"' . ($currentStatus === self::CHAT_STATUS_ONLINE ? ' selected="selected"' : '') . '>' . xg_text('CHAT_STATUS_ONLINE') . '</option>';
        $options .= '<option value="' . self::CHAT_STATUS_OFFLINE . '"' . ($currentStatus === self::CHAT_STATUS_OFFLINE ? ' selected="selected"' : '') . '>' . xg_text('CHAT_STATUS_OFFLINE') . '</option>';
        return $options;
    }

    //the uriHash is used to make every token unique per page 
    public static function getIFrameParamsAndPutDataInCache($xnUser, $embedType, $uriHash, $userOnlineStatus) {
        XG_App::includeFileOnce('/lib/XG_CryptoHelper.php');
        XG_App::includeFileOnce('/lib/XG_TemplateHelpers.php');
        W_Cache::getWidget('chat')->includeFileOnce('/lib/helpers/Chat_UserHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');

        // Load information to pass to the chat client/server about the current user (including signed out)
        $userArray = Chat_UserHelper::getChatUserDetails($xnUser, $userOnlineStatus);
        $app = XN_Application::load();
        $appIconUrl = $app->iconUrl(24,24);
        $appRelativeUrl = $app->relativeUrl;
        $appOwnerId = $app->ownerName;
        $currentAppHost = $_SERVER['HTTP_HOST'];

        //this will be used to pass parameters such as styles, moderators, etc, which are app-wide
        //for now, it's a default roomID and default ownerId only
        //the roomconfig contains things that are both dynamic (such as the embedtype) and page-dependent (in the future styles for
        //chat on a profile page) and 'static' such as app defaultname or defaultownerid for toom
        $roomConfigArray = array('defaultName'=>$appRelativeUrl,'defaultOwnerId'=>$appOwnerId, 'embedType'=>$embedType);//,

        $appArray = array('id' => $appRelativeUrl,
                          'host' => $currentAppHost,
                          'iconUrl' => $appIconUrl,
                          'ownerId' => $appOwnerId,
                          'roomConfig' => $roomConfigArray,
                          'adminNingIdList' => implode(',', Chat_UserHelper::getAdministrativeUserScreenNames()),
                          'commonCssUrl' => xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('css/common.css')),
                          'customCssUrl' => xg_absolute_url(Index_AppearanceHelper::getCustomCssUrl()),
                          'themeCssUrl' => xg_absolute_url(Index_AppearanceHelper::getThemeCssUrl()));

        $rand = rand(0,1000000000);
        $randomizer = "$rand";
        $time = time();
        $timestr = "$time";

        //the randomizer is used to make tokens unique for each request, along with the request time, since we're hashing the whole JSON array
        //we also add the requestURI since we need a unique token per page, since each page can include chat with different parameters
        if (Chat_UserHelper::isAnonymous($userArray['id'])) {
            //signedout requests should not be randomized, so we don't pass the requestTime or the randomizer
            $arr = array ('app'=>$appArray, 'user'=>$userArray,
                          'requestURI'=>$uriHash);
        } else {
            $arr = array ('app'=>$appArray, 'user'=>$userArray,
                          'randomizer'=>$randomizer, 'requestTime'=>$timestr,
                          'requestURI'=>$uriHash);
        }
        $userData = json_encode($arr);

        //we encrypt the object with the user ID. This means that even if someone requests the token
        //from the cache, they'd have to know the userID to decrypt it, and that's only known to the
        //requesting user (who will pass the user ID as a param to the chat server, so the
        //data can be decrypted when removed from the cache)
        //$encryptedData = XG_CryptoHelper::encrypt(XG_CryptoHelper::appPrivateKey(), $userData);

        $roomConfigJson = json_encode($roomConfigArray);
		$tokenSourceData = $xnUser->screenName.'_'.$appRelativeUrl.'_'.$currentAppHost.'_'.$roomConfigJson.$appArray['commonCssUrl']; 
        $token = hash('ripemd160', $tokenSourceData);
        if (Chat_UserHelper::isAnonymous($userArray['id'])) {
            //all signed out users share the same token, which never changes, and the object is never removed from cache, just refreshed
            $token = "signedout_$token";
        } else {
            $token = "signedin_$token";
        }
        XN_Cache::put($token,  $userData);

        return array('token'=>$token,'appSubdomain'=>$appRelativeUrl,'appHost'=>$currentAppHost);
    }

    public static function retrieveDataFromCache($token) {

        XG_App::includeFileOnce('/lib/XG_CryptoHelper.php');
        XG_App::includeFileOnce('/lib/XG_UserHelper.php');

        /*
            the token is of the form "signedout_0ff5e808856d21da49662a04376ea394ca76d52b" for
            signed out users and "signedin_0ff5e808856d21da49662a04376ea394ca76d52b" for signedin
            users, so we explode the string to see which is which.
            The difference is that signed out user tokens are shared for all signed
            out users, so they are never removed from the cache
        */

        $tokenParts = explode("_", $token);


        $cachedData = XN_Cache::get($token);
        if ($cachedData == NULL) {
            $cachedData = "{'returnCode':'error','message':'NOT_FOUND','tokenReceived':" . $token . "}";
        }
    //    else {
    //        $cachedData = XG_CryptoHelper::decrypt(XG_CryptoHelper::appPublicKey(), $cachedData);
    //    }

        if ($tokenParts[0] != "signedout") {
            XN_Cache::remove($token);//"0ff5e808856d21da49662a04376ea394ca76d52b");
        }

        return $cachedData;
    }

    public static function getChatServer($widget) {
        XG_App::includeFileOnce('/lib/XG_ChatHelper.php');

        if ($widget->config['chatServer']) {
            //there's an instance override of the chat server,
            //useful to change a single network to a new chat server if necessary
            //this value would something like chat01.ningim.com:8080
            $chatServer = $widget->config['chatServer'];
        }
        else {
            $app = XN_Application::load();
            $chatServer = XG_ChatHelper::getChatServer($app);
        }

        return $chatServer;
    }

}
