<?php

W_Cache::getWidget('opensocial')->includeFileOnce('/controllers/EndpointController.php');

/**
 * Controller that manages arbitrary key/value pairs per user/app.
 */
class OpenSocial_PersistenceController extends OpenSocial_EndpointController {
    
    /**
     * v1.0 Persistence endpoint.
     *
     * GET to read data.
     * PUT to write data.
     * DELETE to delete data.
     *
     * Expected variables in querystring (GET) or message body (other methods):
     *     'st' => a secure token that when decrypted contains:
     *         'v' => viewer Ning username
     *         'o' => owner Ning username
     *         'd' => domain this request is for
     *         'u' => url this app is served from
     *         'm' => index number for the gadget
     *     'ids' => a comma-delimited list of Ning screenNames.  Maximum of 100.
     *
     * Optional variables:
     *     'keys' (GET only) => Contains the name of a key, a list of keys or '*' for all keys.
     *     'body' (PUT only) => Contains JSON array of data to write.
     */
    public function action_10_persistence() {
        $data = $this->initRequest();
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if (! isset($_GET['ids']) || ! isset($_GET['keys'])) { $this->badRequest(); } 
                OpenSocial_PersistenceController::get($data->u, $data->v, $data->o, explode(",", $_GET['ids']), $_GET['keys']);
                break;
            case 'PUT':
                W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
                if ($data->v == OpenSocial_PersonHelper::ANONYMOUS) { $this->forbidden(); }
                $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
                $body = $json->decode(http_get_request_body());
                if (! $body) { $this->badRequest(); }
                OpenSocial_PersistenceController::put($data->u, $data->v, $body);
                break;
            case 'DELETE':
                OpenSocial_PersistenceController::delete($data->u, $data->v, $_GET['keys']);
                break;
            default:
                header("HTTP/1.0 405 Method not allowed");
                exit;
        }
    }
    
    /**
     * Retrieve the specified app data.
     *
     * @param   $appUrl     string  URL of app XML, used as unique identifier of app making the request.
     * @param   $viewerId   string  The Ning username of the current viewer or OpenSocial_PersonHelper::ANONYMOUS for the anonymous user.
     * @param   $ownerId    string  The Ning username of the app owner.
     * @param   $ids        array   Array of Ning usernames to retreive data for.  Maximum of 100.
     * @param   $keys       string  Comma-delmited list of keys to retrieve values for.  "*" for all keys.
     * @return              void    A JSON array of the requested data is output to the response as a side effect.
     */
    public static function get($appUrl, $viewerId, $ownerId, $ids, $keys) {
        $actualIds = self::validIds($viewerId, $ownerId, $ids);
        $appData =  OpenSocialAppData::load($appUrl, $actualIds);
        $ret = self::assembleData($appData, $keys);
        $json = new NF_JSON();
        echo $json->encode($ret);
    }
    
    /**
     * Retrieve a string key to be used in locking OpenSocialAppData Objects while editing
     *
     * @param   $appUrl     string      URL of app XML, used as unique identifier of app.
     * @param   $viewerId   string      Ning screenName of the viewer
     * @param               string      string key to be used while obtaining a lock
     */
    public static function getLockKey($appUrl, $viewerId){
        return "xg-opensocial-data-" . $appUrl . "-" . $viewerId;
    }
    
    /**
     * Length of time, in seconds, to wait getting a write lock to OpenSocialAppData
     */
    private static $secondsToLock = 50;
    
    /**
     * Write the specified app data.
     *
     * @param   $appUrl     string      URL of app XML, used as unique identifier of app.
     * @param   $viewerId   string  The Ning username of the current viewer.
     * @param   $body       JSON    Containing key=>value pair to update.
     * @return              void    Data is written to the content store and a success/failure code written to the response as a side effect.
     */
    public static function put($appUrl, $viewerId, $body) {
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        if(! XG_LockHelper::lock(self::getLockKey($appUrl, $viewerId), self::$secondsToLock)){
            header("HTTP/1.0 400 Bad Request");
            exit(); 
        }
        $appData = OpenSocialAppData::load($appUrl, $viewerId);
        if (! $appData) {
            XG_LockHelper::unlock(self::getLockKey($appUrl, $viewerId));
            header("HTTP/1.0 400 Bad Request");
            exit();            
        }
        try {
            $changed = false;
            foreach ($body as $k => $v) {
                if ($appData->get($k) != $v) {
                    $appData->set($k, $v);
                    $changed = true;
                }
            }
            if ($changed) {
                $appData->save();
            }
        } catch (OpenSocial_InvalidKeyException $e) {
            //TODO: We should create helper functions such as error403(); or error503(); - for these header responses - instead of having to type out each header string, as it is prone to errors.
            // and elsewhere [Thomas David Baker 2008-07-17]
            XG_LockHelper::unlock(self::getLockKey($appUrl, $viewerId));
            header("HTTP/1.0 400 Bad Request");
            exit();            
        } catch (Exception $e) {
            // too much data
            XG_LockHelper::unlock(self::getLockKey($appUrl, $viewerId));
            header("HTTP/1.0 403 Forbidden");
            exit();
        }
        XG_LockHelper::unlock(self::getLockKey($appUrl, $viewerId));
        echo "{}";
        //TODO: throttle apps that are reading/writing data too fast [Thomas David Baker 2008-07-03]
    }

    /**
     * Remove the keys specified from the app data for the app-user combination supplied.  The operation is idempotent.
     *
     * @param   $appUrl     string      URL of app XML, used as unique identifier of app.
     * @param   $viewerId   string  Ning screenName for user to delete data for.
     * @param   $keys       string  Comma-delimited list of keys to remove.
     * @return              void    Data is removed from the content store and a success/failure code written to the response as a side effect.
     */
    public static function delete($appUrl, $viewerId, $keys) {
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        if(! XG_LockHelper::lock(self::getLockKey($appUrl, $viewerId), self::$secondsToLock)){
            header("HTTP/1.0 400 Bad Request");
            exit(); 
        }

        $appData = OpenSocialAppData::load($appUrl, $viewerId);
        if (! $appData) {
            XG_LockHelper::unlock(self::getLockKey($appUrl, $viewerId));
            echo "{}"; exit;
        }
        if ($keys === '*') {
            XN_Content::delete($appData);
            XG_LockHelper::unlock(self::getLockKey($appUrl, $viewerId));
            echo "{}"; exit;
        }
        foreach (explode(",", $keys) as $key) {
            $appData->deleteKey($key);
        }
        $appData->save();
        XG_LockHelper::unlock(self::getLockKey($appUrl, $viewerId));
        echo "{}";
    }
    
    /**
     * Marshall the data in $appData into an array of format: array('screenName' => array('k' => 'v', ...), ...)
     * Only include keys in $keys (unless $keys is '*' in which case include all those found).
     *
     * @param   $appData    array   Array of the format array('screenName' => OpenSocialAppData, ...)
     * @param   $keys       string  Comma-delimited list of keys to assemble, or '*' to include all keys found.
     * @return              array   Array of format: array('screenName' => array('k' => 'v', ...), ...)
     */
    public static function assembleData($appData, $keys) {
        $ret = array();
        foreach ($appData as $u => $d) {
            if (! is_null($d)) {
                $extracted = self::extractData($d->getData(), $keys);
                if ($extracted) {
                    $ret[$u] = $extracted;
                }
            }
        }
        return $ret;
    }

    /**
     * Get a projection of the data in $data with only the keys in $keys and the associated values present.
     *
     * @param   $data   array   Associative array containing data to get projection from.
     * @param   $keys   string  Keys to use.  "*" for "all keys".
     * @return          array   $data with only the keys specified by $keys.
     */
    public static function extractData($data, $keys) {
        if ($keys === '*') { return $data; }
        $ret = array();
        foreach (explode(",", $keys) as $key) {
            if ($data[$key]) {
                $ret[$key] = $data[$key];
            }
        }
        return $ret;
    }
}
