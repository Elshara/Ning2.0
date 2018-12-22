<?php

/**
 * Logic specific to each type of invitation: network invitation, network share, group invitation, group share, event invitation.
 * Uses the Strategy design pattern: the five modes are represented by five objects:
 *
 *     - Index_NetworkInvitationMode
 *     - Index_NetworkShareMode
 *     - Index_GroupInvitationMode
 *     - Index_GroupShareMode
 *     - Index_EventInvitationMode
 *
 * @see Index_InvitationModeTest
 */
abstract class Index_InvitationMode {

    /** XG_FaultTolerantTask for sending emails. */
    private $queue;

    /** A PHP callback, called when an error occurs. */
    private $errorCallback;

    /**
     * Returns the mode object specific to the given type:
     * network invitation, network share, group invitation, group share, event invitation
     *
     * @param $inviteOrShare string  the current context: "invite" or "share"
     * @param $groupId string  the content ID of the associated Group, or null if none.
     * @param $eventId string  the content ID of the associated Event, or null if none.
     * @return Index_InvitationMode  mode object for the given parameters
     */
    public static function get($args) {
        if ($args['inviteOrShare'] != 'invite' && $args['inviteOrShare'] != 'share') { throw new Exception('Assertion failed (1840982268)'); }
        $key = $args['inviteOrShare'] . ', ' . $args['groupId'] . ', ' . $args['eventId'];
        $className = 'Index_' . ($args['groupId'] ? 'Group' : ($args['eventId'] ? 'Event' : 'Network')) . ($args['inviteOrShare'] == 'invite' ? 'Invitation' : 'Share') . 'Mode';
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/' . $className . '.php');
        if (! self::$invitationModes[$key]) {
            self::$invitationModes[$key] = new $className($args);
        }
        return self::$invitationModes[$key];
    }

    /** Mapping of get() arguments to Index_InvitationMode object. */
    protected static $invitationModes = array();

    /**
     * Sends messages to the specified people.
     *
     * @param $contactList array  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param $message string  optional custom message
     * @param $contentId string  the associated content ID for share messages; null for invitations
     * @param $errorCallback mixed  a PHP callback, called when an error occurs, with an array of failed contacts as the argument.
     */
    public function send($contactList, $message, $contentId, $errorCallback) {
        $contactList = self::normalizeEmailAddresses($contactList);
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $message = mb_substr($message, 0, Index_InvitationHelper::MAX_MESSAGE_LENGTH);
        XG_App::includeFileOnce('/lib/XG_FaultTolerantTask.php');
        $startTime = time();
        $this->errorCallback = $errorCallback;
        // We use two fault-tolerant tasks: "sendProper" builds up a list of messages to send, and "queue" does the sending.
        // Each task has its own error handler: onSendProperError() and onQueueExecutionError().
        // onSendProperError() will be passed one XG_FaultTolerantOperation whose metadata will be the entire $contactList.
        // onQueueExecutionError() will be passed several XG_FaultTolerantOperations whose metadata will be an email address.
        // [Jon Aquino 2007-11-14]
        $sendProper = new XG_FaultTolerantTask(array($this, 'onSendProperError'));
        $this->queue = new XG_FaultTolerantTask(array($this, 'onQueueExecutionError'));
        $sendProper->add(array($this, 'sendProper'), array($contactList, $message, $contentId), $contactList);
        $sendProper->execute(null);
         $elapsedTime = time() - $startTime;
         // PHP timeout is 60 seconds. Set the timeout threshold to 40 seconds, to
         // allow 20 seconds for the errorCallback.  [Jon Aquino 2007-11-14]
        $this->queue->execute(40 - $elapsedTime);
    }

    /**
     * Called when errors occur during the sendProper() call.
     *
     * @param $incompleteOperations array  one XG_FaultTolerantOperation containing the contact list as its metadata
     */
    public function onSendProperError($incompleteOperations) {
        call_user_func($this->errorCallback, $incompleteOperations[0]->metadata);
    }

    /**
     * Called when errors occur in the XG_FaultTolerantTask. Each XG_FaultTolerantOperation
     * will contain an email address as its metadata.
     *
     * @param $incompleteOperations array  XG_FaultTolerantOperations that were not completed because of errors
     */
    public function onQueueExecutionError($incompleteOperations) {
        $failedEmailAddresses = XG_FaultTolerantTask::extractMetadata($incompleteOperations);
        call_user_func($this->errorCallback, Index_InvitationFormHelper::emailAddressesToContactList($failedEmailAddresses));
    }

    /**
     * Sends messages to the specified people.
     *
     * @param $contactList array  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param $message string  optional custom message
     * @param $contentId string  the associated content ID for share messages; null for invitations
     */
    public abstract function sendProper($contactList, $message, $contentId);

    /**
     * Adds an operation to the queue, for fault-tolerant execution.
     *
     * @param $callback mixed  a PHP callback
     * @param $args array  arguments to apply to the callback
     * @param $emailAddress string  the email address that this operation is processing
     */
    protected function addToQueue($callback, $args, $emailAddress) {
        $this->queue->add($callback, $args, $emailAddress);
    }

    /**
     * Returns a new array with the items keyed by their email address.
     *
     * @param $items array  an array of: profiles, User objects, GroupMembership objects, EventAttendee objects, or arrays with keys "name" and "emailAddress"
     * @return array  the items keyed by their email addresses
     */
    protected function keyByEmailAddress($items) {
        $itemsKeyedByEmailAddress = array();
        foreach ($items as $item) {
            if ($item instanceof XN_Profile) { $itemsKeyedByEmailAddress[$item->email] = $item; }
            elseif (is_array($item)) { $itemsKeyedByEmailAddress[$item['emailAddress']] = $item; }
            elseif (mb_strlen($item->my->username)) { $itemsKeyedByEmailAddress[XG_Cache::profiles($item->my->username)->email] = $item; }
            elseif (mb_strlen($item->my->screenName)) { $itemsKeyedByEmailAddress[XG_Cache::profiles($item->my->screenName)->email] = $item; }
            elseif ($item->type == 'User') { $itemsKeyedByEmailAddress[XG_Cache::profiles($item->title)->email] = $item; }
            else { throw new Exception('Assertion failed (82218674)'); }
        }
        return $itemsKeyedByEmailAddress;
    }

    /**
     * Sends a friend request from the current user to the email address.
     *
     * @param $emailAddress string  the recipient of the friend request
     */
    public function sendFriendRequest($emailAddress) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        Profiles_UserHelper::createFriendRequest(XN_Profile::current()->screenName, $emailAddress);
		XG_Browser::execInEmailContext(array($this,'_sendFriendRequestNotification'), $emailAddress);
    }

	// callback for sending comment notifications
	public function _sendFriendRequestNotification ($emailAddress) { # void
        XG_Message_Notification::create(XG_Message_Notification::EVENT_FRIEND_REQUEST, array('profile' => XN_Profile::current()))->send($emailAddress);
    }

    /**
     * Converts pseudo-email-addresses to real email addresses where possible,
     * to permit comparisons in classifyRecipients().
     *
     * @param $contactList array  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param array  the contact list with email addresses converted
     * @see XG_Message::isPseudoEmailAddress()
     */
    protected function normalizeEmailAddresses($contactList) {
        $profiles = XG_Cache::profiles(array_keys($this->keyByEmailAddress($contactList)));
        $newContactList = array();
        foreach ($contactList as $contact) {
            $profile = $profiles[$contact['emailAddress']];
            if ($profile && $profile->email) { $contact['emailAddress'] = $profile->email; }
            $newContactList[] = $contact;
        }
        return $newContactList;
    }

}
