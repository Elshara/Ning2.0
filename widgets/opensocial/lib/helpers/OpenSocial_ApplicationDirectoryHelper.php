<?php

/**
 * Helper class that deals with all things to do with the Application Directory including the central list
 * at developer.ning.com and display of the directory within the network.
 */
class OpenSocial_ApplicationDirectoryHelper {
    
    const DIRECTORY_APP = 'developer';
    
    /**
     * Contact the central app directory and get information on categories/apps.
     *
     * @param   $begin  int 0-indexed app to begin at.
     * @param   $end    int 0-indexed app to end BEFORE (or 1-indexed to end on, depending on how your mind works).
     * @param   $sort       string  One of 'popular', 'rating' or 'latest'.
     * @param   $appUrls    array   Array of URLs of apps that must be included in the results.
     */
    public static function getApplicationDirectoryInfo($q=null, $category=null, $begin=0, $end=10, $sort='latest') {
        if ($sort == 'rating' || $sort == 'popular') {
            $mainAppInfo = self::getLocalApplicationDirectoryInfo($begin, $end, $sort);
            $appUrls = self::getAppUrls($mainAppInfo['apps']);
            $additionalAppInfo = self::getRemoteApplicationDirectoryInfoFromUrls($appUrls);
        } else {
            $mainAppInfo = self::getRemoteApplicationDirectoryInfo($q, $category, $begin, $end, $sort);
            $appUrls = self::getAppUrls($mainAppInfo['apps']);
            $additionalAppInfo = self::getLocalApplicationDirectoryInfoFromUrls($appUrls);
        }
        $appInfo = self::combineAppInfo($mainAppInfo, $additionalAppInfo);
        foreach ($appInfo['apps'] as &$app) {
            if ($app['numMembers']) {
                $app['numFriends'] = self::numFriendsWithApp($app, XN_Profile::current()->screenName);
            }
        }
        $appInfo['categories'] = self::getCategories();
        return $appInfo;
    }
    
    /**
     * Get the appUrls out of a list of apps.
     *
     * @param   $apps   Array   array(array('appUrl' => <url>, ...) , array(...), ...)
     * @return          Array   array(<url> , <url>, ...)
     */
    public static function getAppUrls($apps) {
        $appUrls = array();
        foreach ($apps as $app) {
            $appUrls[] = (is_object($app) ? $app->my->appUrl : $app['appUrl']);
        }
        return $appUrls;
    }
    
    /** Duration of cache in seconds */
    const DEVELOPER_CACHE_DURATION = 600;
    
    /** If caching of developer network responses is enabled */
    const DEVELOPER_CACHE_ENABLED = true;
    
    /**
     * Manages a locking cache for developer
     *
     * @todo Extract these functions out into a general XN_Cache wrapper [dkf 2008-10-09]
     *
     * @param   $cacheKey       string      the XN_Cache key under consideration
     * @return                  mixed       the cache data if the cache is fresh, otherwise null to continue processing
     */
    private static function checkCache($cacheKey) {
        if (! self::DEVELOPER_CACHE_ENABLED) {
            return null;
        }
        $output = XN_Cache::get($cacheKey, intval(self::DEVELOPER_CACHE_DURATION * 1.1));
        if ($output === null) {
            return null;
        }
        if ((time() - $output[0]) < self::DEVELOPER_CACHE_DURATION * 0.9) {
            return $output[1];
        }
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        if (XG_LockHelper::lock('lock_' . $cacheKey, 0)) {
            return null;
        } else {
            return $output[1];
        }
    }
    
    /**
     * Put a piece of data into the locking cache
     *
     * @param   $cacheKey   string      the XN_Cache key under consideration
     * @param   $content    mixed       the data to put in the cache
     * @return              void
     */
    private static function putCache($cacheKey, $content) {
        if (! self::DEVELOPER_CACHE_ENABLED) {
            return null;
        }
        XN_Cache::put($cacheKey, array(time(), $content));
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        XG_LockHelper::unlock('lock_' . $cacheKey);
    }
    
    /**
     * Caching wrapper around @see getRemoteApplicationDirectoryInfoProper
     */
    public static function getRemoteApplicationDirectoryInfo($q=null, $category=null, $begin=0, $end=10, $sort='title') {
        $cacheKey = md5('xg-os-developer-' . implode('_', array($q, $category, $begin, $end, $sort)));
        $result = self::checkCache($cacheKey);
        if ($result === null) {
            $result = self::getRemoteApplicationDirectoryInfoProper($q, $category, $begin, $end, $sort);
            self::putCache($cacheKey, $result);
        }
        return $result;
    }
    
    /**
     * Get app directory information from the central app directory described by the specified parameters.
     *
     * @param   $q          string  Text search string.
     * @param   $category   string  Key representing a category of app.  All apps returned will be in this category.
     * @param   $begin      int     0-indexed app to begin at.
     * @param   $end        int     0-indexed app to end BEFORE (or 1-indexed to end on, depending on how your mind works).
     * @param   $sort       string  One of 'popular', 'rating', 'latest' or 'title'.
     * @return              array   array('total' => <int>, 'apps' => array(array('appUrl' => <url>, 'category' => <string>, 'title' => <string>, 'recommended' => <boolean>, 'dateApproved' => <int secs since epoch>), ...))
     */
    private static function getRemoteApplicationDirectoryInfoProper($q=null, $category=null, $begin=0, $end=10, $sort='title') {
        $json = new NF_JSON();
        $params = array('q' => $q, 'begin' => $begin, 'end' => $end, 'category' => $category, 'sort' => $sort, 'version' => 1, 'output' => 'json');
        $action = ($q ? "search" : "list");
        $url = XG_HttpHelper::addParameters("http://" . XN_AtomHelper::HOST_APP(OpenSocial_ApplicationDirectoryHelper::DIRECTORY_APP) . "/api.php?op=$action", $params);
        try {
            $s = XN_REST::post($url);
        } catch (Exception $e) {
            $s = $json->encode(array('status' => 'error', 'error' => $e->getTraceAsString()));
        }
        return self::assembleRemoteDirectoryInfo($s);
    }

    /**
     * Caching wrapper around @see getRemoteApplicationDirectoryInfoFromUrlsProper
     */
    public static function getRemoteApplicationDirectoryInfoFromUrls($appUrls) {
        $cacheKey = md5('xg-os-developer-' . implode('_', $appUrls));
        $result = self::checkCache($cacheKey);
        if ($result === null) {
            $result = self::getRemoteApplicationDirectoryInfoFromUrlsProper($appUrls);
            self::putCache($cacheKey, $result);
        }
        return $result;
    }

    /**
     * Get the remote endpoint details for the apps in $appUrls.
     *
     * @param   $appUrls    array   Array of URLs of apps to get the details of.
     * @return              array   array('total' => <int>, 'apps' => array(array('appUrl' => <url>, 'category' => <string>, 'title' => <string>, 'recommended' => <boolean>, 'dateApproved' => <int secs since epoch>), ...))
     */
    private static function getRemoteApplicationDirectoryInfoFromUrlsProper($appUrls) {
        $json = new NF_JSON();
        try {
            $s = XN_REST::post("http://" . XN_AtomHelper::HOST_APP(OpenSocial_ApplicationDirectoryHelper::DIRECTORY_APP) . "/api.php?op=apps&version=1", array('appUrls' => $json->encode($appUrls)));
        } catch (Exception $e) {
            $s = $json->encode(array('status' => 'error', 'error' => $e->getTraceAsString()));
        }
        return self::assembleRemoteDirectoryInfo($s);
    }
    
    /**
     * Take the JSON response of the remote directory and assemble it into our standard array format.
     *
     * @param   $s  string  JSON representing applications returned by the remote endpoint.
     * @return              array   array('total' => <int>, 'apps' => array(array('appUrl' => <url>, 'category' => <string>, 'title' => <string>, 'recommended' => <boolean>, 'dateApproved' => <int secs since epoch>), ...))
     */
    public static function assembleRemoteDirectoryInfo($s) {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $v = $json->decode($s);
        if (! $v || $v['status'] != 'ok') {
            return array('status' => 'error', 'error' => $v['error'], 'apps' => array());
        }
        $v['apps'] = $v['data'];
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        foreach ($v['apps'] as &$app) {
            $app['prefs'] = OpenSocial_GadgetHelper::readGadgetUrl($app['appUrl']);
        }
        return $v;
    }
    
    /**
     * Get app directory information from the content store described by the specified parameters.
     *
     * @param   $begin      int     0-indexed app to begin at.
     * @param   $end        int     0-indexed app to end BEFORE (or 1-indexed to end on, depending on how your mind works).
     * @param   $sort       string  One of 'popular', 'rating', 'latest' or null for alphabetical order.
     * @return              array   array('total' => <optional, int>, 'apps' => array(array('appUrl' => <url>, 'numMembers' => <int>, 'numReviews' => <int>, 'avgRating' => <int>), ...))
     */
    public static function getLocalApplicationDirectoryInfo($begin=0, $end=10, $sort='title') {
        $appInfo = OpenSocialApp::find($begin, $end, $sort); 
        $apps = $appInfo['apps'];
        $r = self::assembleLocalApplicationDirectoryInfo($apps);
        $r['total'] = $appInfo['numApps'];
        return $r;
    }
    
    /**
     * Get the local details (number of installs on this network, etc.) for the apps specified in $appUrls.
     *
     * @param   array   $appUrls    Array of URLs of apps to get details of.
     * @return  array               array('apps' => array('appUrl' => <url>, 'numMembers' => <int>, 'numReviews' => <int>, 'avgRating' => <float>), ...)
     */
    public static function getLocalApplicationDirectoryInfoFromUrls($appUrls) {
        $apps = OpenSocialApp::loadMultiple($appUrls);
        return self::assembleLocalApplicationDirectoryInfo($apps);
    }
    
    /**
     * Assemble specified OpenSocialApp objects into standard array format.
     *
     * @param   $apps   array   Array of OpenSocialApp objects to assemble into standard array format.
     * @return          array   array('apps' => array('appUrl' => <url>, 'numMembers' => <int>, 'numReviews' => <int>, 'avgRating' => <float>), ...)
     */
    public static function assembleLocalApplicationDirectoryInfo($apps) {
        $r = array('apps' => array(), 'status' => 'ok');
        foreach ($apps as $app) {
            $r['apps'][] = array('appUrl' => $app->my->appUrl, 'numMembers' => $app->my->numMembers, 'numReviews' => $app->my->numReviews, 'avgRating' => $app->my->avgRating);
        }
        return $r;
    }
    
    /**
     * Caching wrapper around @see getCategoriesProper
     */
    public static function getCategories() {
        $cacheKey = md5('xg-os-developer-categories');
        $result = self::checkCache($cacheKey);
        if ($result === null) {
            $result = self::getCategoriesProper($appUrls);
            self::putCache($cacheKey, $result);
        }
        return $result;
    }
    
    
    /**
     * Get the current list of categories and number of apps in each category from the remote endpoint.
     *
     * @return  array   array(array('category' => <string>, 'count' => <int>), ...)
     */
    public static function getCategoriesProper() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        try {
            $s = XN_REST::post("http://" . XN_AtomHelper::HOST_APP(OpenSocial_ApplicationDirectoryHelper::DIRECTORY_APP) . "/api.php?op=listCategories&version=1");
        } catch (Exception $e) {
            return array();
        }
        $v = $json->decode($s);
        if ($v['status'] != 'ok') {
            return array();
        }
        return $v['data'];
    }
    
    /**
     * Merge the main info (be it from local content store or remote app dir) with the additional information required from the other source.
     * Possibly surprisingly, main will be overwritten by additional if the keys are the same.
     * 
     * @param   $mainInfo           Array   array('total' => <int>, 'apps' => array(array('appUrl' => <url>, ...), array(...), ...))
     * @param   $additionalInfo     Array   array('total' => <int>, 'apps' => array(array('appUrl' => <url>, ...), array(...), ...))
     * @return                      Array   array('total' => <int>, 'apps' => array(array('appUrl' => <url>, ...), array(...), ...))
     */
    public static function combineAppInfo($mainInfo, $additionalInfo) {
        if (isset($additionalInfo['total'])) {
            $mainInfo['total'] = $additionalInfo['total'];
        }
        $found = array();
        foreach ($mainInfo['apps'] as &$mainApp) {
            foreach ($additionalInfo['apps'] as $additionalApp) {
                if ($mainApp['appUrl'] === $additionalApp['appUrl']) {
                    $mainApp = array_merge($mainApp, $additionalApp);
                    $found[] = $mainApp['appUrl'];
                }
            }
        }
        foreach ($additionalInfo['apps'] as $app) {
            if (! in_array($app['appUrl'], $found)) {
                $mainInfo['apps'][] = $app;
            }
        }
        return $mainInfo;
    }
    
    /**
     * Get the combined local and remote directory info for one or more apps.
     *
     * @param   $apps       mixed   URL of app to query for, or array of appUrls, or array of OpenSocialApp objects or array of OpenSocialAppData objects.
     * @return              array   array('appUrl' => <url>, 'approved' => <boolean>, 'numMembers' => <int>, etc.) if string provided for $appUrl
     *                              array(array(...), ...) if array provided for $appUrl
     */
    public static function getAppDetails($apps) {
        $urls = (is_array($apps) ? $apps : array($apps));
        $urls = ($urls && is_object($urls[0]) ? self::getAppUrls($urls) : $urls);
        $local = self::getLocalApplicationDirectoryInfoFromUrls($urls);
        $remote = self::getRemoteApplicationDirectoryInfoFromUrls($urls);
        $combined = self::combineAppInfo($local, $remote);
        return (is_array($apps) ? $combined['apps'] : $combined['apps'][0]);
    }
    
    /**
     * Determine if the specified app is currently approved and in the central application directory.
     *
     * @param   $appUrl string  URL of app to check.
     * @return          boolean
     */
    public static function isAppApproved($appUrl) {
        $app = self::getAppDetails($appUrl);
        return ($app ? $app['approved'] : false);
    }
    
    /**
     * Find the number of friends of the specified user with the specified app installed.
     *
     * @param   $app        array   array('appUrl' => <string>, ...)
     * @param   $screenName string  Screenname of the user to check for friends with the app.
     * @return              int     Number of friends of the specified user with the specified app installed.
     */
    public static function numFriendsWithApp($app, $screenName) {
        if (! $screenName) { return 0; }
        //TODO Possibly do this in OpenSocialAppData as ::find [Thomas David Baker 2008-09-29]
        $query = XG_Query::create('Content')->filter('owner')->filter('type', '=', 'OpenSocialAppData')
            ->filter('my->appUrl', '=', $app['appUrl'])->filter('contributorName', 'in', XN_Query::FRIENDS($screenName))
            ->alwaysReturnTotalCount(true);
        $query->execute();
        return $query->getTotalCount();
    }
    
    /**
     * Determine if the specified app is currently blocked (and should not be displayed).
     *
     * @param   $appUrl         string  URL of app in question.
     * @param   $installedByUrl boolean Whether this app was installed by URL (TRUE) or from the application directory (FALSE).
     * @return                  boolean TRUE if the app is currently blocked, FALSE if it is ok to render it.
     */
    public static function isBlocked($appUrl, $installedByUrl) {
       if ($installedByUrl) { return false; }
       $appDetails = OpenSocial_ApplicationDirectoryHelper::getAppDetails($appUrl);
       return ($appDetails && ! $appDetails['approved']);
    }
}
