<?php

/**
 * Represents the current network's information about an OpenSocial application.
 */
class OpenSocialApp extends W_Model {

    /**
     * URL of the app.  Used as the unique identifier of an application.
     *
     * @var XN_Attribute::STRING
     */
    public $appUrl;
    
    /**
     * Number of times this application has been reviewed.
     *
     * @var XN_Attribute::NUMBER
     */
    public $numReviews;
    
    /**
     * The average rating given in the reviews of this application.
     *
     * @var XN_Attribute::NUMBER
     */
    public $avgRating;

    /**
     * Number of members that have this application installed.
     *
     * @var XN_Attribute::NUMBER
     */
    public $numMembers;
    
    /**
     * Serialized array of screenNames of members that have this app installed.
     *
     * @var XN_Attribute::STRING
     */
    public $members;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/

    /**
     * Add a member to the list of members that have this application installed.  The 
     * calling routine has the responsibility to save the content object to store changes.
     *
     * @param   $app        OpenSocialApp   App to update.
     * @param   $screenName string          Screen name of user to add to list.
     * @param               void
     */
    public static function addMember($app, $screenName) {
        // We store members as keys not values for O(1) lookup.
        $members = array_keys(unserialize($app->my->members));
        $members[] = $screenName;
        $app->my->members = serialize(array_flip($members));
    }
    
    /**
     * Remove a member from the list of members that have this application installed.  The 
     * calling routine has the responsibility to save the content object to store changes.
     *
     * @param   $app        OpenSocialApp   App to update.
     * @param   $screenName string          Screen name of user to remove from list.
     * @param               void
     */
    public static function removeMember($app, $screenName) {
        $members = array_keys(unserialize($app->my->members));
        $newMembers = array();
        foreach ($members as $member) {
            if ($member != $screenName) {
                $newMembers[] = $member;
            }
        }
        $app->my->members = serialize(array_flip($newMembers));
    }

    /**
     * Return the list of members with an application
     *
     * @param   $app    OpenSocialApp       the app to retrieve members for
     * @return          array               array with screennames as keys for use with array_key_exists
     */
    public static function getMembers($app) {
        return unserialize($app->my->members);
    }

    private static $secondsToLock = 10;

    /**
     * Load the OpenSocialApp for the specified url.
     *
     * @param   $appUrl             string  URL of app.
     * @param   $createIfNecessary  boolean TRUE to create if not found, FALSE to return null in that scenario.
     */
    public static function load($appUrl, $createIfNecessary=FALSE) {
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'OpenSocialApp')->filter('my->appUrl', '=', $appUrl);
        $app = $query->end(1)->uniqueResult();
        if (! $app && $createIfNecessary) {
            $app = OpenSocialApp::create($appUrl);
        }
        return $app;
    }
    
    /**
     * Load the OpenSocialApp objects for the specified URLs.
     *
     * @param   $appUrls    array   Array of app urls to load data for.
     * @return              array   Array of OpenSocialApp objects.
     */
    public static function loadMultiple($appUrls) {
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'OpenSocialApp')->filter('my->appUrl', 'in', $appUrls);
        return $query->execute();
    }
    
    /**
     * Find the specified OpenSocialApp objects using the supplied parameters.
     *
     * @param   $begin  int     0-indexed start point.
     * @param   $end    int     Non-inclusive end point.
     * @param   $sort   string  One of ('latest', 'rating', 'popular').
     * @return          array   array('apps' => array(<OpenSocialApp>, ...), 'numApps' => <int>)
     */
    public static function find($begin=0, $end=10, $sort='latest') {
        if ($sort == 'popular') {
            $filter = XN_Filter('my->numMembers', '>', 0, XN_Attribute::NUMBER);
            $order = 'my->numMembers';
            $orderType = XN_Attribute::NUMBER;
        } else if ($sort == 'rating') {
            $filter = XN_Filter('my->numReviews', '>', 0, XN_Attribute::NUMBER);
            $order = 'my->avgRating';
            $orderType = XN_Attribute::NUMBER;
        } else if ($sort == 'latest') {
            $order = 'createdDate';
            $orderType = XN_Attribute::DATE;
        }
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'OpenSocialApp')->begin($begin)->end($end)->order($order, 'desc', $orderType);
        if ($filter) {
            $query->filter(XN_Filter::all($filter));
        }
        $query->alwaysReturnTotalCount(true);
        return array('apps' => $query->execute(), 'numApps' => $query->getTotalCount());
    }
    
    protected static function create($appUrl) {
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        // There should only be one of these on the network per appUrl.
        if (! XG_LockHelper::lock(self::getLockKey($appUrl), self::$secondsToLock)) {
            return self::load($appUrl);
        }
        $app = W_Content::create('OpenSocialApp');
        $app->my->appUrl = $appUrl;
        $app->my->numReviews = 0;
        $app->my->avgRating = 0.0;
        $app->my->numMembers = 0;
        $app->my->members = serialize(array());
        return $app;
    }
    
    protected static function getLockKey($appUrl) {
        return "xg-opensocial-app-" . $appUrl;
    }
    
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/
}
