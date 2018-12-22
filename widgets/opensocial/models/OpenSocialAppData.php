<?php

/**
 * Represents a user's install of an OpenSocial application.
 *
 * Contains a small (10KB) per-user, per-app OpenSocial data store for key-value pairs.
 */
class OpenSocialAppData extends W_Model {

    /**
     * Screenname of the user this data belongs to.
     *
     * @var XN_Attribute::STRING
     */
    public $user;
    
    /**
     * URL of the app this data belongs to.  Used as the unique identifier of an application.
     *
     * @var XN_Attribute::STRING
     */
    public $appUrl;
    
    /**
     * Data for this user/app combination as a serialized PHP array.
     * Attempting to place more than 10KB of data in here will cause the data to not be saved and an exception thrown.
     *
     * @var XN_Attribute::STRING
     * @rule length 0,10000
     */
    public $data;

    /**
     * Is the application displayed on 'My Page'?
     *
     * @var XN_Attribute::BOOLEAN
     */
    public $isOnMyPage;

    /**
     * Should the gadget be able to add activity items?
     *
     * @var XN_Attribute::BOOLEAN
     */
    public $canAddActivities;

    /**
     * Should the gadget be able to send messages to the user?
     *
     * @var XN_Attribute::BOOLEAN
     */
    public $canSendMessages;

    /**
     * Should the gadget prompt before sending message to anyone?
     *
     * @var XN_Attribute::BOOLEAN optional
     */
    public $promptBeforeSending;
    
    /**
     * Was the gadget installed by Url?
     *
     * @var XN_Attribute::BOOLEAN
     */
    public $installedByUrl;

    /**
     * Rate limit information - stores count as well as last updated time.
     * 
     * @var XN_Attribute::STRING optional
     */
    public $rateLimitBlob;
    
/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/

    //TODO: we do a lot of serializing and unserializing in the class.  It would be better to unserialize on construction and serialize on save 
    // but overriding save is not possibly because it does not live on W_Model but is actually added dynamically or something crazy like that.
    // Should look into this and do it the better way if possible. [Thomas David Baker 2008-07-03]
    
    /**
     * Retrieve the app data for the specified app/users.  Possibly create if not found.
     *
     * @param   $appUrl             string  URL of the app.
     * @param   $screenNames        mixed   Either an array of Ning screenNames or a single string of a screenName.
     * @return                      mixed   Array of ('screenName' => OpenSocialAppData, ...) if array passed in for $screenNames, 
     *                                      single OpenSocialAppData otherwise.  OpenSocialAppData replaced by null if not found and $createIfNecessary is FALSE.
     */
    public static function load($appUrl, $screenNames) {
	    $multiple = is_array($screenNames);
        //TODO: If enough screen names are passed in this query will fail to return all expected results
        // so we should add totalCount as above. [Thomas David Baker 2008-08-13]
        $appInfo = OpenSocialAppData::loadMultiple($appUrl, $screenNames);
        $ret = array();
        foreach ($appInfo['apps'] as $appData) {
            $ret[$appData->my->user] = $appData;
        }
        return ($multiple ? $ret : $ret[$screenNames]);
    }
    
    /**
     * Retrieve app data for the specified app/users.
     *
     * @param   $appUrls        array or string One or more app URLs to look for.  null to search all apps.
     * @param   $screenNames    array or string One or more screen names to look for.  null to search all apps.
     * @param   $begin          int             Where to begin search.  Same as XN_Query param.
     * @param   $end            int             Where to end search.  Same as XN_Query param.
     * @return                  array           array('apps' => array(<OpenSocialAppData>, ...), 'total' => <int>)
     */
    public static function loadMultiple($appUrls, $screenNames, $begin=0, $end=50) {
        //TODO: Can we sensibly add any kind of caching to this, perhaps by checking cacheOrderN? [Thomas David Baker 2008-07-03]
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'OpenSocialAppData')
            ->begin($begin)->end($end)->order('createdDate', 'desc', XN_Attribute::DATE)->alwaysReturnTotalCount(true);
        $urls = (is_array($appUrls) ? $appUrls : array($appUrls));
        $users = (is_array($screenNames) ? $screenNames : array($screenNames));
        if ($urls && $urls[0]) {
            $query->filter('my->appUrl', 'in', $urls);
        }
        if ($users && $users[0]) {
            $query->filter('my->user', 'in', $users);
        }
        $ret = array();
        $ret['apps'] = $query->execute();
        foreach ($ret['apps'] as &$app) {
            $app = W_Content::create($app);
        }
        //TODO: This should probably be 'numApps' or 'numAppData' by analogy with OpenSocialAppReview, OpenSocialApp and others.  [Thomas David Baker 2008-10-08]
        $ret['total'] = $query->getTotalCount();
        return $ret;
    }

    /**
     * Create a new OpenSocialAppData for this app/user combination.  The returned object is NOT saved
     * to the content store.  Call the ->save() method to store it.
     *
     * @param   $appUrl string              URL of the application
     * @param   $user   string              Ning screenName.
     * @param   $installedByUrl boolean             true if adding direct from a URL, false if from the application directory.
     * @param           OpenSocialAppData  New app data object that has been saved to the content store.
     */
    public static function create($appUrl, $user, $installedByUrl) {
        $appData = W_Content::create('OpenSocialAppData');
        $appData->my->appUrl = $appUrl;
        $appData->my->user = $user;
        $appData->my->isOnMyPage = true;
        $appData->my->canAddActivities = true;
        $appData->my->canSendMessages = true;
        $appData->my->promptBeforeSending = true;
        $appData->my->installedByUrl = $installedByUrl;
        $appData->my->data = serialize(array());
        $appData->my->rateLimitBlob = serialize(array());
        $appData->save();
        return $appData;
    }
    
    /**
     * Retrieve the data stored in this object as an associative array.
     *
     * @param   array   Array of key=>value pairs.
     */
    public function getData() {
        return unserialize($this->data);
    }
    
    /**
     * Get the value currently stored for $key, or null if not found.
     *
     * @param   $key    string  Key to retrieve.
     * @return          mixed   Value against that key, or null if not found.
     */
    public function get($key) {
        $unserializedData = unserialize($this->data);
        return $unserializedData[$key];
    }
    
    /**
     * Set the value stored for $key to $value. Throws an exception if key is not valid according to OpenSocial spec.
     *
     * @param   $key    string  Key for value to set.
     * @param   $value  mixed   Value to store.
     * @return          boolean true for success.
     */
    public function set($key, $value) {
        // Note: As currently implemented invalid keys are removed by the osoc core and never actually reach this endpoint.
        // Nevertheless this is a legitimate check and should remain. [Thomas David Baker 2008-08-03]
        if (! preg_match('/^[-_\.a-zA-Z0-9]+$/u', $key)) {
            W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_InvalidKeyException.php');
            throw new OpenSocial_InvalidKeyException("OpenSocial app data key may only use alphanumerics, dash (-), underscore (_) and dot (.)");
        }
        $unserializedData = unserialize($this->data);
        $unserializedData[$key] = $value;
        $this->data = serialize($unserializedData);
        return true;
    }
    
    /**
     * Remove the specified key and its associated value from the data stored in this object.
     * Idempotent operation (deleting a key that does not exist is allowed).
     *
     * @param   $key    string  Key to delete.
     * @param           boolean true for success.
     */
    public function deleteKey($key) {
        $unserializedData = unserialize($this->data);
        unset($unserializedData[$key]);
        $this->data = serialize($unserializedData);
        return true;
    }
    
    /**
     * Get all the properties for a given app & user
     *
     * @return array of the user properties
     */
    public function getSettings() {
        $ret = array();
        foreach (array('canAddActivities', 'canSendMessages', 'promptBeforeSending', 'isOnMyPage') as $key) {
            $ret[$key] = (empty($this->$key) || !$this->$key) ? 0 : 1;
        }
        return $ret;
    }

    /**
     * Default rate-limit - if not 'SENDMESSAGE'.  Other types could be 'SHAREAPP', etc. that are not yet supported.
     */
    const OPENSOC_RATELIMIT_DEFAULT_LIMIT = 35;
    const OPENSOC_RATELIMIT_SENDMESSAGE = 'requestSendMessage';
    /**
     * Rate limit for 'SENDMESSAGE'.  It could be different than the default.
     */
    const OPENSOC_RATELIMIT_SENDMESSAGE_LIMIT = 35;  // -1 => unlimited

    /**
     * Get the rateLimitInfo object
     *
     * @param  $appData  string   appData for which the rateLimitInfo needs to be determined
     * @return           object   rateLimitInfo, unserialized from existing data, array() object otherwise.
     */
    private static function getRateLimitInfo($appData) { 
        return ($appData->my->rateLimitBlob) ? unserialize($appData->my->rateLimitBlob) : array();
    }
    
    /**
     * Get rate limit info for a given msgType
     * 
     * @param  $rateLimitInfo  array    array of per-msgType-rate-limit-entries
     * @param  $msgType        string   type of message - requestSendMessage, ...
     * @return                 object   rateLimitEntry from the rateLimitInfo array, or a newly created entry.
     */
    private static function getRateLimitEntry($rateLimitInfo, $msgType) {
        if (!$msgType || $msgType === "") $msgType = self::OPENSOC_RATELIMIT_SENDMESSAGE;
        return ($rateLimitInfo[$msgType]) ? $rateLimitInfo[$msgType] : array('timestamp' => time(), 'count' => 0);
    }

    /**
     * Check to see if rateLimit has been exceeded for a given msgType
     *
     * @param  $rateLimitEntry  array    rateLimitEntry ('timestamp' => ..., 'count' => ...) for a given msgType
     * @return                  boolean  True if the rateLimit has been hit or exceeded, false otherwise
     */
    private static function rateLimitExceeded($rateLimitEntry, $msgType) { 
        $ratelimit = ($msgType === self::OPENSOC_RATELIMIT_SENDMESSAGE) ? self::OPENSOC_RATELIMIT_SENDMESSAGE_LIMIT : self::OPENSOC_RATELIMIT_DEFAULT_LIMIT;
        $count = $rateLimitEntry['count'];
        return (($ratelimit >= 0) && ($count >= $ratelimit));
    }

    /**
     * Check rateLimit for a given appUrl, screenName, and msgType - but do not update
     * 
     * @param   $appUrl         string   URL of the app to update settings for.
     * @param   $screenName     string   Screen name of the user to update settings for.
     * @param   $msgType        string   type of message - requestSendMessage, ...
     * @return                  boolean  True if the rateLimit has been hit or exceeded, false otherwise
     */
    public static function rateLimitCheck($appUrl, $screenName, $msgType) {
        if (!$msgType || $msgType === "") $msgType = self::OPENSOC_RATELIMIT_SENDMESSAGE;
        $appData = OpenSocialAppData::load($appUrl, $screenName);
        $rateLimitInfo = self::getRateLimitInfo($appData);
        $rateLimitEntry = self::getRateLimitEntry($rateLimitInfo, $msgType);
        return self::rateLimitExceeded($rateLimitEntry, $msgType);
    }

    /**
     * Check and update rateLimit for a given appUrl, screenName, and msgType - no need to guard with locks
     * 
     * @param   $appUrl         string   URL of the app to update settings for.
     * @param   $screenName     string   Screen name of the user to update settings for.
     * @param   $msgType        string   type of message - requestSendMessage, ...
     * @return                  boolean  True if the rateLimit has been hit or exceeded, false otherwise
     */
    public static function rateLimitCheckAndUpdate($appUrl, $screenName, $msgType) {
        if (!$msgType || $msgType === "") $msgType = self::OPENSOC_RATELIMIT_SENDMESSAGE;
        $appData = OpenSocialAppData::load($appUrl, $screenName);
        if (!$appData) return false;
        $rateLimitInfo = self::getRateLimitInfo($appData);
        $rateLimitEntry = self::getRateLimitEntry($rateLimitInfo, $msgType);
        $rateLimitExceeded = self::rateLimitExceeded($rateLimitEntry, $msgType);
		$timestamp = $rateLimitEntry['timestamp'];
		$count = $rateLimitEntry['count'];
		$now = time();
		$count = ((int)($timestamp/86400) === (int)($now/86400)) /* is it the same day */ ? ++$count : 0;
		$rateLimitInfo[$msgType] = array('timestamp' => $now, 'count' => $count);
		$appData->my->rateLimitBlob = serialize($rateLimitInfo);
		$appData->save();
        return $rateLimitExceeded;
    }

    /**
     * Update one setting of the specified gadget for the specified user with the specified value.
     * Ignored if the specified user does not have the specified gadget installed.
     *
     * @param   $appUrl         string  URL of the app to update settings for.
     * @param   $screenName     string  Screen name of the user to update settings for.
     * @param   $key            string  Parameter to be updated
     * @param   $value          string  Value to be set
     * @return                  void    (Called for side effect of making changes to content store.)
     */
    // is this function obsolete by updateSettings below? can we remove it? [dkf 2008-10-3]
    public static function updateSetting($appUrl, $screenName, $key, $value) {
        $appData = OpenSocialAppData::load($appUrl, $screenName);
        if (! $appData) { error_log("Tried to update $key setting for $appUrl for $screenName which is not installed."); return; }
        if (in_array($key, array('canAddActivities', 'canSendMessages', 'promptBeforeSending', 'isOnMyPage'))) {
            $appData->my->$key = (boolean) $value;
            $appData->save();
        }
    }

    /**
     * Update the settings of the specified gadget for the specified user with the specified values.
     * Ignored if the specified user does not have the specified gadget installed.
     *
     * @param   $appUrl         string  URL of the app to update settings for.
     * @param   $screenName     string  Screen name of the user to update settings for.
     * @param   $values         array   Array of the format array(
     *                                      'canAddActivities' => <boolean>, 'canSendMessages' => <boolean>,
     *                                      'isOnMyPage' => <boolean>).
     * @return                  array    Array with keys representing various operations and their values representing success/failure
     */
    public static function updateSettings($appUrl, $screenName, $values) {
        $results = array();
        $appData = OpenSocialAppData::load($appUrl, $screenName);
        if (! $appData) { error_log("Tried to update settings for $appUrl for $screenName which is not installed."); return; }
        if ($appData->my->isOnMyPage && ! $values['isOnMyPage']) {
            W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
            OpenSocial_GadgetHelper::removeApplicationFromMyPage($appData);
            $appData->my->isOnMyPage = false;
        } else if (! $appData->my->isOnMyPage && $values['isOnMyPage']) {
            W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
            $addToMyPage = OpenSocial_GadgetHelper::addApplicationToMyPage($appData);
            $results['addToMyPage'] = $addToMyPage;
            $appData->my->isOnMyPage = $addToMyPage;
        }
        foreach (array('canAddActivities', 'canSendMessages') as $key) {
            $appData->my->$key = (boolean) $values[$key];
            $results[$key] = (boolean) $values[$key];
        }
        
        // BAZ-9085 - if one 'unsets' canSendMessages - then promptBeforeSending is set to true
        if (!$appData->my->canSendMessages) $appData->my->promptBeforeSending = true;
        $appData->save();
        return $results;
    }
    
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/
}
