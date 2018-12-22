<?php

/**
 * @deprecated  We no longer expose invitation requests in the UI
 */
class InvitationRequest extends W_Model {
    /**
     * The e-mail address or screen name of the requesting user
     *
     * @var XN_Attribute::STRING
     */
    public $requestor;

    /**
     * The name supplied by the requesting user (if one was supplied)
     *
     * @var XN_Attribute::STRING optional
     */
    public $requestorName;

    /**
     * The message provided with the request (if provided)
     *
     * @var XN_Attribute::STRING optional
     */
    public $description;

    /**
     * Always private
     *
     * @var XN_Attribute::BOOLEAN
     */
    public $isPrivate;

    /**
     * Which mozzle created this object?  (always main)
     *
     * @var XN_Attribute::STRING
     */
    public $mozzle;

/** xn-ignore-start eb5ed8a22fc43c42953de408115d934c **/
// Everything other than instance variables goes below here

    /**
     * Constructor for new invitation request object creation.
     *
     * @param $requestor string Screen name or email of requestor
     * @param $requestorName string optional Name of requestor, default NULL
     * @param $message string optional Message to creator, default NULL
     * @return W_Content  An unsaved content object of type 'InvitationRequest'
     */
    public static function create($requestor, $requestorName = NULL, $message = NULL) {
        $request = W_Content::create('InvitationRequest');
        $request->my->requestor = $requestor;
        if ($requestorName) {
            $request->my->requestorName = $requestorName;
        }
        if ($message) {
            $request->my->description = $message;
        }
        $request->my->mozzle = W_Cache::current('W_Widget')->dir;
        $request->isPrivate = TRUE;
        return $request;
    }

    /**
     * Query for Invitation Requests
     *
     * @param $filters array An array of filters keyed by attribute name k. Each array element is either:
     *              'v' to filter on k = v
     *              array('op','v') to filter on k op v
     *              array('op','v','type') to filter on k op v type
     * @param $begin integer optional result set start. Defaults to 0
     * @param $end integer   optional result set end.   Defaults to 10
     * @param $order string  optional field to order on Defaults to null
     * @param $dir string    optional ordering direction Defaults to null if $order is not specified, asc if order is specified
     * @param $caching mixed optional caching control information:
     *                       true: cache, use default max age and no additional invalidation keys
     *                       integer: cache, use provided integer as max age and no invalidation keys
     *                       array: cache, use optional 'maxAge' key as max age
     *                                     use optional 'keys' key as invalidation keys
     * @return array A two element array: 'requests' => the request objects
     *                                    'numRequests' => the total number of requests that match
     */
     public static function find($filters, $begin = 0, $end = 10, $order = null, $dir = null, $caching = null) {
         XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
         $query = XN_Query::create('Content')
                    ->filter('owner')
                    ->filter('type','eic','InvitationRequest');
        $query = XG_QueryHelper::applyFilters($query, $filters);
        $query->begin($begin);
        $query->end($end);
        if (! is_null($order)) {
            $dir = is_null($dir) ? 'asc' : $dir;
            $query->order($order, $dir);
        }
        $query->alwaysReturnTotalCount(true);

        /* If caching is desired, use it */
        if (! (is_null($caching) || ($caching === false))) {
            if ($caching === true) {
                $query = XG_Query::create($query);
            }
            else if (is_integer($caching)) {
                $query = XG_Query::create($query);
                $query->maxAge($caching);
            }
            else if (is_array($caching)) {
                $query = XG_Query::create($query);
                if (isset($caching['maxAge'])) {
                    $query->maxAge($caching['maxAge']);
                }
                if (isset($caching['keys'])) {
                    $query->setCaching($caching['keys']);
                }
            }
            // If we're caching add an invalidation key for the content
            // type user, for easy invalidation when any user object changes
            $query->addCaching(XG_Cache::key('type','InvitationRequest'));
        }

        $requests = $query->execute();
        $count    = $query->getTotalCount();
        return array('requests' => $requests, 'numRequests' => $count);
    }

    /**
      *  Returns true if the specified request was sent with an email address
      *    or false if with a Ning id
      *
      * @param $request InvitationRequest
      *
      * @return boolean
      */
     public static function requestedByEmail($request) {
         return (preg_match('/.+@.+\..+/u', $request->my->requestor) ? true : false);
     }

     /**
      * Returns true if the specified request was sent with a Ning ID
      * (not an e-mail address)
      *
      * @param $request InvitationRequest
      * @return boolean
      */
    public static function requestedByNingId($request) {
        return (! self::requestedByEmail($request));
    }

/** xn-ignore-end eb5ed8a22fc43c42953de408115d934c **/
}
