<?php

W_Cache::getWidget('opensocial')->includeFileOnce('/controllers/EndpointController.php');

/**
 * Controller allowing core access to read/write Activity information.
 */
class OpenSocial_ActivityController extends OpenSocial_EndpointController {
    
    const ACTIVITY_LOG_ITEMS_ALLOWED_PER_APP_PER_USER_PER_DAY = 5;
    
    /**
     * v1.0 Activity endpoint.
     *
     * Expected $_GET variables:
     *     'st' => a secure token that when decrypted contains:
     *         'v' => viewer Ning username
     *         'o' => owner Ning username
     *         'd' => domain this request is for
     *         'u' => url this app is served from
     *         'm' => index number for the gadget
     *
     * With GET request:
     *      'ids' => Comma-delimited list of Ning screenNames to retrieve activities for.
     *      'activity' => Specific activity id to retrieve (optional).
     *
     * With POST request:
	 *      HTTP body:  => JSON representation of activity to add to stream for viewer.
     */
    public function action_10_activity() {
        $data = $this->initRequest();
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if (! isset($_GET['ids'])) { $this->badRequest(); }
                OpenSocial_ActivityController::get($data->u, $data->v, $data->o, explode(",", $_GET['ids']), $_GET['activity']);
                break;
            case 'POST':
                $postBody = http_get_request_body();
                if (! $postBody) { $this->badRequest(); }
                $json = new NF_JSON();
                $activity = $json->decode($postBody);
                if (! $activity) { $this->badRequest(); }
                //TODO: Add constants and an explanatory comment for these 1000 limits (it's just to make display manageable).
                if (mb_strlen($activity->title) > 1000) { $this->badRequest(); }
                if (mb_strlen($activity->body) > 1000) { $this->badRequest(); }
                W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
                if ($data->v == OpenSocial_PersonHelper::ANONYMOUS) { $this->badRequest(); }
                OpenSocial_ActivityController::post($data->u, $data->v, $activity);
                break;
            default:
                header("HTTP/1.0 405 Method not allowed");
                exit;
        }
    }
    
    /**
     * Write JSON of some recent activity log entries to the response.
     *
     * @param   $appUrl     string  URL of app XML, used as unique identifier of app making the request.
     * @param   $viewerId   string  Ning screenName of current viewer.
     * @param   $ownerId    string  Ning screenName of app owner.
     * @param   $ids        array   Array of Ning screenNames to retrieve activities for.
     * @param   $activityId int     Unique identifier of a specific activity.
     * @return              void    JSON array of events is written to the response as a side effect.
     */
    public static function get($appUrl, $viewerId, $ownerId, $ids, $activityId) {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        $validIds = self::validIds($viewerId, $ownerId, $ids);
        $logItems = self::getActivities($appUrl, $validIds, $activityId);
        $ret = self::assembleActivities($logItems);
        $json = new NF_JSON();
        echo $json->encode($ret);
    }
    
    /**
     * Write an activity to the activity log.
     *
     * @param   $appUrl     string  URL of app XML, used as unique identifier of app making the request.
     * @param   $viewerId   string  Ning screenName to attach the activity to.
     * @param   $activity   object  Object with title and (optional) text attributes.
     * @return              void    JSON success/failure is written to the response a side effect of calling this function.
     */
    public static function post($appUrl, $viewerId, $activity) {
        $json = new NF_JSON();
        $appData = OpenSocialAppData::load($appUrl, $viewerId);
        if (! $appData || ! $appData->my->canAddActivities){
            echo $json->encode(array('errorCode' => 'UNAUTHORIZED', 'errorMessage' => xg_text('CANNOT_CREATE_ACTIVITY')));
        } else if (self::isRateLimited($appData->my->appUrl, $viewerId)) {
            echo $json->encode(array('errorCode' => 'limitExceeded', 'errorMessage' => xg_text('TOO_MANY_LOG_ITEMS')));
        } else if (self::postActivity($appData, $viewerId, $activity)) {
            echo $json->encode(array());
        } else {
            echo $json->encode(array('errorCode' => 'INTERNAL_ERROR', 'errorMessage' => xg_text('UNABLE_TO_CREATE_ACTIVITY_LOG_ITEM')));
        }
    }
    
    /**
     * Get recent ActivityLogItems matching the specified criteria.
     *
     * @param   $appUrl     string  URL of app XML, used as unique identifier of app making the request.
     * @param   $ids        array   Array of Ning screenNames to retrieve activities for.
     * @param   $activityId int     Unique identifier of a specific activity, or null to get all log items matching other params.
     * @return              array   Array of ActivityLogItems.
     */
    public static function getActivities($appUrl, $ids, $activityId) {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'ActivityLogItem')
            ->filter('category', '=', XG_ActivityHelper::CATEGORY_OPENSOCIAL)->begin(0)->end(20)
            ->order('createdDate', 'desc', XN_Attribute::DATE);
        $query->filter('my->members', 'in', $ids);
        $query->filter('my->link', '=', $appUrl);
        if ($activityId) {
            $query->filter('id', '=', $activityId);
        }
        return $query->execute();     
    }
        
    /**
     * Create an ActivityLogItem with the specified parameters.
     *
     * @param   $appData    OpenSocialAppData	Object that the activity log item is being attached to
     * @param   $viewerId   string  		Ning screenName to attach the activity to.
     * @param   $activity   object  		Object with title and (optional) text attributes.
     * @return              mixed   		See XG_ActivityHelper::logActivityIfEnabled.
     */
    public static function postActivity($appData, $viewerId, $activity) {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        //TODO: Instead of strip_tags we should safely allow a, b, i and span tags only (but check js exploits, etc.) in body
        // See: http://code.google.com/apis/opensocial/docs/0.7/reference/opensocial.Activity.Field.html#BODY
        return XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_OPENSOCIAL, XG_ActivityHelper::SUBCATEGORY_APP_MSG,
                                                       $viewerId, array($appData) /* related content objects */, strip_tags($activity->body), 
                                                       null /* widgetName */, strip_tags($activity->title), $appData->my->appUrl);
    }
    
    /**
     * Determine if the combination of app (represented by $appUrl) and user (represented by $viewerId) is currently rate-limited
     * and may not post activity log items.
     *
     * @param   $appUrl     string  URL of the application in question.
     * @param   $viewerId   string  Screen name of the user in question.
     * @return              boolean true if app may NOT add an activity log item, otherwise false.
     */
    public static function isRateLimited($appUrl, $viewerId) {
        //TODO: Move this to some kind of helper, possibly an existing activity-related one [Thomas David Baker 2008-09-11]
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        $query = XN_Query::create('Content')
            ->filter('owner')
            ->filter('type', '=', 'ActivityLogItem')
            ->filter('my->category', '=', XG_ActivityHelper::CATEGORY_OPENSOCIAL)
            ->filter('my->subcategory', '=', XG_ActivityHelper::SUBCATEGORY_APP_MSG)
            ->filter('my->link', '=', $appUrl)
            ->filter('my->members', '=', $viewerId)
            ->filter('createdDate', '>', gmdate('c', time() - 60 * 60 * 24))
            ->begin(0)
            ->end(1)
            ->alwaysReturnTotalCount(true);
            $query->execute();
        return ($query->getTotalCount() >= self::ACTIVITY_LOG_ITEMS_ALLOWED_PER_APP_PER_USER_PER_DAY);

    }
    
    /**
     * Assemble the specified log items into an associative array suitable for transforming to
     * OpenSocial JSON output.
     *
     * @param   $logItems   array   Array of ActivityLogItem objects.
     * @return              array   Associative array suitable for passing to NF_JSON->encode.
     */
    public static function assembleActivities($logItems) {
        $ret = array();
        foreach ($logItems as $logItem) {
            $ret[] = array('id' => $logItem->id, 'userId' => $logItem->my->members, 'body' => $logItem->description,
                           'title' => $logItem->title, 'postedTime' => $logItem->createdDate,
                           'appUrl' => $logItem->my->link);
        }
        return $ret;
    }    
    
    /**
     * Comparison function for log items that uses createdDate to determine which is of higher/lower value.
     * 
     * @param   $a  ActivityLogItem     First item to compare.
     * @param   $b  ActivityLogItem     Second item to compare.
     * @return      int                 -1 if $a is "lower", 1 if $a is "higher", 0 if $a and $b have the same value.
     */
    public static function cmpLogItems($a, $b) {
        if ($a->createdDate == $b->createdDate) { return 0; }
        return ($a->createdDate < $b->createdDate ? -1 : 1);
    }
}
