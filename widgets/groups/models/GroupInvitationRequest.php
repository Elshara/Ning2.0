<?php
/**
 * A request for an invitation to a group
 */
class GroupInvitationRequest extends W_Model {

    /**
     * The e-mail address or screen name of the requesting user
     *
     * @var XN_Attribute::STRING
     * @rule length 1,200
     */
    public $requestor;
    const MAX_EMAIL_ADDRESS_LENGTH = 200;

    /** Length limit for the name specified in the invitation request */
    const MAX_NAME_LENGTH = 200;

    /** Length limit for the message body of the invitation request */
    const MAX_MESSAGE_LENGTH = 10000;

    /**
     * ID of the Group object
     *
     * @var XN_Attribute::STRING
     */
    public $groupId;

    /**
     * Whether this object is visible to cross-app queries.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * The mozzle that created this object.  (always groups)
     *
     * @var XN_Attribute::STRING
     */
    public $mozzle;

/** xn-ignore-start eb5ed8a22fc43c42953de408115d934c **/
// Everything other than instance variables goes below here

    /**
    * Returns the GroupInvitationRequest for the given requestor,
    * or a new unsaved one if none yet exists.
    *
    * @param $group XN_Content|W_Content  The Group object
    * @param $requestor string Screen name or email of requestor
    * @return W_Content  The GroupInvitationRequest, or a new unsaved one if none exists yet.
    */
    public static function loadOrCreate($group, $requestor) {
        if (! $requestor) { xg_echo_and_throw('Requestor unspecified (1190170389)'); }
        if (! is_string($requestor)) { xg_echo_and_throw('Requestor not string (659694078)'); }
        $key = mb_strtolower($group->id . '+' . $requestor);
        static $groupInvitationRequests = array();
        if (! $groupInvitationRequests[$key]) {
            $query = XN_Query::create('Content');
            $query->filter('type', '=', 'GroupInvitationRequest');
            $query->filter('owner');
            $query->filter('my.requestor', 'eic', $requestor);
            $query->filter('my.groupId', '=', $group->id);
            $query->end(1);
            if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
            $results = $query->execute();
            $groupInvitationRequests[$key] = $results[0] ? W_Content::create($results[0]) : self::create($group, $requestor);
        }
        return $groupInvitationRequests[$key];
    }

    /**
     * Creates an invitation request.
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $requestor string Screen name or email of requestor
     * @return W_Content  An unsaved content object of type GroupInvitationRequest
     */
    private static function create($group, $requestor) {
        $groupInvitationRequest = W_Content::create('GroupInvitationRequest');
        $groupInvitationRequest->my->requestor = $requestor;
        $groupInvitationRequest->my->mozzle = 'groups';
        $groupInvitationRequest->my->groupId = $group->id;
        $groupInvitationRequest->isPrivate = true;
        return $groupInvitationRequest;
    }

    /**
     * Extracts the requestor ids from the given array of GroupInvitationRequests
     *
     * @param $groupInvitationRequests array  the GroupInvitationRequest objects
     * @return array  the e-mail addresses or screen names of the invitees
     */
    public static function userIds($groupInvitationRequests) {
        $userIds = array();
        foreach ($groupInvitationRequests as $groupInvitationRequest) {
            $userIds[] = $groupInvitationRequest->my->requestor;
        }
        return $userIds;
    }

    /** Server-side validation errors */
    private static $validationErrors = array();

    /**
     * Sets the server-side validation errors.
     *
     * @param $validationErrors array  HTML error messages, keyed by field name
     */
    public static function setValidationErrors($validationErrors) {
        self::$validationErrors = $validationErrors;
    }

    /**
     * Returns the server-side validation errors.
     *
     * @return array  HTML error messages, keyed by field name
     */
    public static function getValidationErrors() {
        return self::$validationErrors;
    }

/** xn-ignore-end eb5ed8a22fc43c42953de408115d934c **/
}
