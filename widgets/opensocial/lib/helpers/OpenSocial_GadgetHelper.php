<?php

class OpenSocialGadget  {

    public $index;
    public $appUrl;
    public $domain;
    public $viewerName;
    public $ownerName;

    public $secureToken;
    
    /** the base url for the gadget iframe */
    public $iframeUrl;

    public function __construct ($index, $domain, $appUrl, $viewerName, $ownerName) {
        if (! (isset($index) && $domain && $appUrl && $viewerName && $ownerName)) {
            throw new Exception("Tried to create an invalid gadget: $index, $domain, $appUrl, $viewerName, $ownerName");
        }
        $this->index = $index;
        $this->domain = $domain;
        $this->appUrl = $appUrl;
        $this->viewerName = $viewerName;
        $this->ownerName = $ownerName;

        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_SecurityHelper.php');
        $this->secureToken = OpenSocial_SecurityHelper::generateSecureToken($this);
        $this->iframeUrl =  OpenSocial_GadgetHelper::getIframeUrl($this->appUrl);
    }
}

class OpenSocial_GadgetHelper {

    /** Denotes the prefix for official ning hosted gadgets */
    const NING_HOSTED_DOMAIN = 'http://os.ning.com/apps/';

    /**
     * Determine the osoc domain
     *
     * @return          string      domain that the osoc should be accessed on
     */
    public static function getOsocDomain() {
        if (XN_AtomHelper::$DOMAIN_SUFFIX == '.ning.com') {
            return "ninggadgets.com";
        } elseif (preg_match('@^\.([^\.]+)\.ningops\.net$@', XN_AtomHelper::$DOMAIN_SUFFIX, $matches)) {
            return $matches[1] . '.ninggadgets.com';
        } else {
            return "ninggadgets.com";
        }
    }
    
    /**
     * Retrive the iframe url for the gadget
     *
     * @param   $appUrl string      URI of the gadget xml
     * @return          string      iframe url for the gadget
     */
    public static function getIframeUrl($appUrl) {
        return 'http://' . mb_substr(md5($appUrl), 0, 10) . '.' . self::getOsocDomain() . '/gadgets';
    }

    /**
     * Determine the current viewer's id for OpenSocial purposes.  This will be either their Ning
     * screenName or OpenSocial_PersonHelper::ANONYMOUS for the anonymous user.
     *
     * @return  string  OpenSocial user id.
     */
    public static function currentViewerName() {
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
        $profile = XN_Profile::current();
        return ($profile->isLoggedIn() ? $profile->screenName : OpenSocial_PersonHelper::ANONYMOUS);
    }

    /**
     * Add a module to the current user's profile page that displays the gadget referenced by $appUrl.
     * If the URL is not valid or any other error occurs, return a string key for the i18n error.
     * WARNING: because of the return type using this function naively in an if statement will
     * mess up your code ... all return values are "true" in some sense.  Check instead for === true.
     *
     * @param   $appUrl         string  URL of OpenSocial gadget.
     * @param   $installedByUrl boolean true if adding directly from URL, false if from the application directory.
     * @return                  mixed   boolean true if gadget successfully added, otherwise a string of the i18n text key for the error.
     */
    public static function addApplication($appUrl, $installedByUrl=false) {
        //TODO: pass in screenName to make this more general [Thomas David Baker 2008-08-07]
        if (! XN_Profile::current()->isLoggedIn()) { return 'ADD_APPLICATION_ERROR_NOT_LOGGED_IN'; }
        if (! $appUrl) { return 'ADD_APPLICATION_ERROR_NO_APP_URL_SUPPLIED'; }
        if (self::isApplicationInstalled($appUrl)) {
            // we don't have a good way of dealing with multiple app installs right now
            // don't change this key unless Application Controller action_add is also changed
            return 'ADD_APPLICATION_ERROR_ALREADY_INSTALLED';
        }
        $prefs = self::readGadgetUrl($appUrl);
        if (! $prefs) {
            return 'ADD_APPLICATION_ERROR_CANNOT_READ_XML';
        }
        $screenName = XN_Profile::current()->screenName;
        // load/create OpenSocialApp for this app
        $app = OpenSocialApp::load($appUrl, TRUE /* create if not found */);
        // create OpenSocialAppData object for the install
        $appData = OpenSocialAppData::create($appUrl, $screenName, $installedByUrl);
        if (! $appData) {
            return 'ADD_APPLICATION_ERROR_FAILED_TO_INSTALL';
        }
        // Try our best to get it on their My Page ...
        $appData->my->isOnMyPage = self::addApplicationToMyPage($appData);
        $appData->save();
        $app->my->numMembers = $app->my->numMembers + 1;
        OpenSocialApp::addMember($app, $screenName);
        $app->save();

        // Let the world know, if appropriate.
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_OPENSOCIAL, XG_ActivityHelper::SUBCATEGORY_ADD_APP,
                                                $appData->contributorName, array($appData), $prefs['title'] /* NOT $title */,
                                                null /* widgetName */, null /* title */, $appUrl);

        return TRUE;
    }

    /**
     * Add the specified app to the "My Page" of the specified user.
     *
     * @param   $appData    OpenSocialAppData   XN_Content object for the app to add.
     * @return              boolean             true for success, false for failure.
     */
    public static function addApplicationToMyPage($appData) {
        //TODO We could transform these to show these messages to the user as we have done with addApplication. [Thomas David Baker 2008-09-29]
        XG_App::includeFileOnce('/lib/XG_Layout.php');
        $pageLayout = XG_Layout::load($appData->my->user, 'profiles');
        if (! self::canAddApplicationOnMyPage($pageLayout)) {
            error_log("Not adding app to My Page (" .  $appData->my->appUrl . ") because the " . $appData->my->appUrl . " cannot add applications on their page.");
            return FALSE;
        }
        $prefs = self::readGadgetUrl($appData->my->appUrl);
        if ($prefs === FALSE) {
            error_log("Not installing app at " . $appData->my->appUrl . " because the gadget URL cannot be read.");
            return FALSE;
        }
        $title = ($prefs['title'] == "" ? xg_text('OPENSOCIAL_APPLICATION') : $prefs['title']);

        //TODO: This will fail if the middle column has no modules in it.  That is not currently possible.  [Thomas David Baker 2008-07-02]
        $instanceId = $pageLayout->insertModule('opensocial', 'embed2', '/layout/colgroup/column[1]/colgroup/column[2]/module[last()]',
            null /* no attributes */, TRUE /* insert at end of column */);
        if ($instanceId === FALSE) {
            error_log("Not adding app to My Page at " . $appData->my->appUrl . " because insertModule failed.");
            return FALSE;
        }
        $pageLayout->setEmbedInstanceProperty('title', $title, $instanceId);
        $pageLayout->setEmbedInstanceProperty('appUrl', $appData->my->appUrl, $instanceId);

        return TRUE;
    }

    /**
     *  Is the current user able to add a application?  The only restriction currently is if they are under the embed limit
     *
     * @param $pageLayout  XG_Layout     the current page layout (passed as a param to cut down on page layout loads)
     * @param $limit       int           the current limit on number of applications
     * @return             boolean       true if the user is able to add the application, otherwise false
     */
    public static function canAddApplicationOnMyPage($pageLayout, &$limit = 0) {
        XG_App::includeFileOnce('/lib/XG_Layout.php');
        XG_App::includeFileOnce('/lib/XG_LayoutEditHelper.php');
        $embeds = XG_LayoutEditHelper::getEmbedList($pageLayout->getType());
        $openSocialEmbedDetails = $embeds['opensocial'];
        $limit = $openSocialEmbedDetails['embedLimit'];
        if($pageLayout->query("//module[@widgetName='opensocial']")->length < $limit){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the details of apps installed for the specified user.
     *
     * @param $screenName   string   Ning ID of the user
     * @return              array    Array with one entry for each installed app of the form
     *                                  array(array('appData' => <OpenSocialAppData>, 'prefs' => XML Node of gadget prefs), ...)
     */
    public static function getInstalledApps($screenName) {
        $appInfo = OpenSocialAppData::loadMultiple(null /* any app */, $screenName);
        $results = array();
        foreach ($appInfo['apps'] as $appData) {
            $results[] = array('appData' => $appData, 'prefs' => self::readGadgetUrl($appData->my->appUrl));
        }
        return $results;
    }

    /**
     * Remove all traces of an OpenSocial gadget from a user's account
     * this current means: profile box, OpenSocialAppData xns objects
     *
     * @param $appUrl string The Url of the gadget (which is used to calculate its unique id)
     * TODO: return something meaningful
     */
    public static function removeApplication($appUrl){
        //TODO: pass in screenName as well (or appData instead of both) to make this more general [Thomas David Baker 2008-08-07]
        if (! XN_Profile::current()->isLoggedIn()) { return FALSE; }
        if (! $appUrl) { return FALSE; }

        $screenName = XN_Profile::current()->screenName;
        $appData = OpenSocialAppData::load($appUrl, $screenName);

        // remove from my page
        if (self::isApplicationInstalledOnMyPage($appUrl)) {
            self::removeApplicationFromMyPage($appData);
        }
        
        // decrement install counts and delete the data if the app is properly installed.
        if ($appData) {
            $app = OpenSocialApp::load($appUrl);
            OpenSocialApp::removeMember($app, $screenName);
            $app->my->numMembers = $app->my->numMembers - 1;
            $app->save();
            XN_Content::delete($appData);
        }
    }

    /**
     * Remove an OpenSocial gadget from a user's profile page.
     *
     * @param   $appData    OpenSocialAppData   The user-app data for the app to remove from the user's profile page.
     * TODO: return something meaningful
     */
    public static function removeApplicationFromMyPage($appData){
        XG_App::includeFileOnce('/lib/XG_Layout.php');
        $pageLayout = XG_Layout::load($appData->my->user, 'profiles');
        $query = "//module[@widgetName='opensocial']/appUrl[.='" . $appData->my->appUrl . "']/parent::*";
        $pageLayout->removeElement($query);
        $appData->my->isOnMyPage = false;
        $appData->save();
    }

    /**
     * Check layout to determine if a user has installed a specific application, referenced by $appUrl
     * on their profile page.
     *
     * @param   $appUrl    string URL of OpenSocial gadget
     * @return          boolean: true if gadget has been installed, false if it is not installed
     */
    public static function isApplicationInstalledOnMyPage($appUrl){
        if (! XN_Profile::current()->isLoggedIn()) { return FALSE; }
        if (! $appUrl) { return FALSE; }
        XG_App::includeFileOnce('/lib/XG_Layout.php');
        $pageLayout = XG_Layout::load(XN_Profile::current()->screenName, 'profiles');
        return ( $pageLayout->query("//module[@widgetName='opensocial']/appUrl[.='$appUrl']")->length > 0 );
    }

    /**
     * Is the application installed for the current user?
     * Checks for OpenSocialAppData objects
     *
     * @param   $appUrl    string   Url of the OpenSocial gadget
     * @param   $user   string   optional ning screenName to check, otherwise check the current user
     * @return          boolean  true if the gadget has been installed
     */
    public static function isApplicationInstalled($appUrl, $user = null){
        if (! XN_Profile::current()->isLoggedIn()) { return FALSE; }
        if (! $appUrl) { return FALSE; }
        if ($user == null) {
            $user = XN_Profile::current()->screenName;
        }
        return (OpenSocialAppData::load($appUrl, $user) !== null);
    }

    /**
     * Is the application installed by URL (not the application directory)?
     *
     * @param   $app        OpenSocialAppData object      The app to check
     * @return              boolean                     true if the app was installed by url
     */
    public static function installedByUrl($app) {
        return $app->my->installedByUrl;
    }

    /**
     * Length of time to cache osoc ModulePrefs responses
     */
    const MODULE_PREFS_CACHE_TIME = 300;

    /**
     * Return a string used to cache module prefs from the osoc
     *
     * @param   $appUrl         string      URL to generate the key for
     * @param   $getChecksum    boolean     Is the spec endpoint request asking for a checksum?
     * @return                  string      cache key
     */
    public static function getGadgetCacheKey($appUrl, $getChecksum = false){
        return "xg-opensocial-appdata-" . ($getChecksum ? 'checksum' : 'nochecksum') . '-' . md5($appUrl);
    }

    /**
     * Store previously loaded app data, so we minimize trips to the cache
     */
    private static $readGadgetStore = array();

    /**
     * Get ModulePrefs, utilizing the cache to keep down requests
     *
     * @param   $appUrl         string      URL to check
     * @param   $getChecksum    boolean     Request a checksum of gadget xml from the osoc?
     * @return                  mixed       array of module prefs, otherwise false
     */
    public static function readGadgetUrl($appUrl, $getChecksum = false){
        $cacheKey = self::getGadgetCacheKey($appUrl, $getChecksum);
        if (array_key_exists($cacheKey, self::$readGadgetStore)) {
            return $readGadgetStore[$cacheKey];
        }
        
        $appData = XN_Cache::get($cacheKey, self::MODULE_PREFS_CACHE_TIME);
        if($appData === null){
            $appData = self::readGadgetUrlProper($appUrl, $getChecksum);
            if($appData){
                XN_Cache::put($cacheKey, $appData);
            }
        }
        $readGadgetStore[$cacheKey] = $appData;
        return $appData;
    }

    /**
     * Get ModulePrefs from the osoc spec endpoint.  The osoc result will throw an exception if the gadget doesn't exist or is invalid,
     * it is caught and FALSE is returned.
     *
     * @param   $appUrl         string      URL to check.
     * @param   $getChecksum    boolean     Request a checksum of gadget xml from the osoc?
     * @return                  mixed       array of module prefs, otherwise false
     */
    private static function readGadgetUrlProper($appUrl, $getChecksum = false) {
        list($lang, $country) = explode("_", XG_LOCALE);
        $infoUrl = "http://". self::getOsocDomain();
        $infoUrl .= "/xn/rest/1.0/spec?lang=$lang&country=$country&url=" . urlencode($appUrl);
        if ($getChecksum) {
            $infoUrl .= '&checksum=1';
        } else {
            $infoUrl .= '&checksum=0';
        }

        try {
            $page = XN_REST::get($infoUrl);
        } catch (Exception $e) {
            return FALSE;
        }
        $responseCode = XN_REST::getLastResponseCode();
        //TODO: better error messages based on response code
        if (! $page || $responseCode != "200") { return FALSE; }
        $xml = simplexml_load_string($page);
        if (! is_object($xml) || sizeof($xml) <= 0) {
            return FALSE;
        }

        $nodeList = $xml->xpath("/Module/ModulePrefs");
        $prefs = $nodeList[0]->attributes();
        $results = array();
        foreach ($prefs as $key => $value) {
            $results[$key] = (string) $value;
        }
        
        if ($getChecksum) {
            $checksumResult = $xml->xpath('/Module/Checksum');
            $results['checksum'] = (string) $checksumResult[0];
        }
        
        if (mb_strpos($appUrl, self::NING_HOSTED_DOMAIN) !== FALSE) {
            $results['ningApplication'] = true;
        } else {
            $results['ningApplication'] = false;
        }
        
        return $results;

    }

    /**
     * Determines the number of members of the current network that have the specified app installed.
     * Includes the current user.
     *
     * @param   $appUrl                     string  URL of the app to get number of users for.
     * @return                              int     Number of members with the specified app installed.
     */
    public static function numMembers($appUrl) {
        return self::numUsers($appUrl);
    }

    /**
     * Determines the number of friends of the current user that have the specified app installed.
     * Does not include the current user.
     *
     * @param   $appUrl                     string  URL of the app to get number of users for.
     * @return                              int     Number of friends with the specified app installed.
     */
    public static function numFriends($appUrl) {
        return self::numUsers($appUrl, XN_Profile::current()->screenName);
    }

    /**
     * Determines the number of users that have the specified app installed.
     * Will restrict to the friends of $screenNameToFindFriendsFor if given.
     *
     * @param   $appUrl                     string  URL of the app to get number of users for.
     * @param   $screenNameToFindFriendsFor string  Screen name of user to find friends with the app
     *                                              installed.  Finds all users on network if not specified.
     * @return                              int     Number of users with the specified app installed.
     */
    protected static function numUsers($appUrl, $screenNameToFindFriendsFor=null) {
        $query = ($screenNameToFindFriendsFor ? XN_Query::create('Content') : XG_Query::create('Content')->setCaching('opensocial-num-users-' . md5($appUrl)));
        $query->filter('owner')->filter('type', '=', 'OpenSocialAppData')
            ->filter('my->appUrl', '=', $appUrl)->begin(0)->end(1)->alwaysReturnTotalCount(true);
        if ($screenNameToFindFriendsFor) {
            $query->filter('contributorName', 'in', XN_Query::FRIENDS($screenNameToFindFriendsFor));
        }
        $query->execute();
        return $query->getTotalCount();
    }

    /**
     * Has Opensocial javascript been included yet?
     *
     */
     private static $openSocialJavascriptIncluded = false;

    /**
    * Return opensocial javascript html if it has not been included yet
    * otherwise, return nothing
    *
    * @param   $renderUrl  string   root path to static files on the core
    * @return              string   either script tags to include opensocial javascript or the empty string
    */
    public static function requireOpenSocialJavascript($renderUrl){
        if(! self::$openSocialJavascriptIncluded){
            self::$openSocialJavascriptIncluded = true;
            return '<script type="text/javascript" src="' . xnhtmlentities($renderUrl) . '/gadgets/js/rpc.js?c=1"></script>' .
                   '<script type="text/javascript" src="' . xg_cdn('/xn_resources/widgets/opensocial/container/util.js') . '"></script>' .
                   '<script type="text/javascript" src="' . xg_cdn('/xn_resources/widgets/opensocial/container/gadgets.js') . '"></script>';
        } else {
            return "";
        }
    }
}

?>
