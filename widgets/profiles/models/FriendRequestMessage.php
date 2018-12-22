<?php
/**
 * A content object that stores the (optional) custom message associated with a friend request.
 */
class FriendRequestMessage extends W_Model {

    /**
     * The plain-text message body. Be sure to escape this with xnhtmlentities() when displaying it.
     *
     * @var XN_Attribute::STRING optional
     */
    public $description;
    const MAX_MESSAGE_LENGTH = 1000;

    /**
     * Whether the object is private (always TRUE)
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * The mozzle that created this object (always "profiles").
     *
     * @var XN_Attribute::STRING
     */
    public $mozzle;

    /**
     * Screen-name of the recipient of the friend request.
     *
     * @var XN_Attribute::STRING
     */
    public $recipient;

    /**
     * Screen-name of the sender of the friend request.
     *
     * @var XN_Attribute::STRING
     */
    public $sender;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here

    /**
     * Stores a custom message for a friend request.
     *
     * @param $recipient string  screen name of the recipient of the friend request
     * @param $sender string  screen name of the sender of the friend request.
     * @param $message string  the plain-text message body
     */
    public static function setMessage($recipient, $sender, $message) {
        self::deleteMessages(array($recipient), array($sender));
        if (! $message) { return; }
        $messageObject = self::instance()->createMessage();
        $messageObject->isPrivate = TRUE;
        $messageObject->my->mozzle = 'profiles';
        $messageObject->description = $message;
        $messageObject->my->sender = $sender;
        $messageObject->my->recipient = $recipient;
        $messageObject->save();
    }

    /**
     * Deletes the FriendRequestMessage (if any).
     *
     * @param $recipients array  screen names of the recipients
     * @param $senders array  screen names of the senders
     */
    public static function deleteMessages($recipients, $senders) {
        $messages = self::instance()->getMessagesProper($recipients, $senders);
        if ($messages) { XN_Content::delete($messages); }
    }

    /**
     * Creates a FriendRequestMessage content object.
     *
     * @return XN_Content  an uninitialized FriendRequestMessage
     */
    protected function createMessage() {
        return XN_Content::create('FriendRequestMessage');
    }

    /**
     * Returns the custom messages for friend requests sent by the current user to the given recipients.
     *
     * @param $recipients array  screen names of the recipients
     * @return array  message strings keyed by recipient
     */
    public static function getMessagesTo($recipients) {
        $messages = array();
        foreach (self::instance()->getMessagesProper($recipients, array(XN_Profile::current()->screenName)) as $message) {
            $messages[$message->my->recipient] = $message->description;
        }
        return $messages;
    }

    /**
     * Returns the custom messages for friend requests sent to the current user from the given senders.
     *
     * @param $senders array  screen names of the senders
     * @return array  message strings keyed by sender
     */
    public static function getMessagesFrom($senders) {
        $messages = array();
        foreach (self::instance()->getMessagesProper(array(XN_Profile::current()->screenName), $senders) as $message) {
            $messages[$message->my->sender] = $message->description;
        }
        return $messages;
    }

    /**
     * Returns the custom-message objects for friend requests sent to the given users from the given senders.
     *
     * @param $recipients array  screen names of the recipients
     * @param $senders array  screen names of the senders
     * @return array  FriendRequestObjects
     */
    protected function getMessagesProper($recipients, $senders) {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'FriendRequestMessage');
        $query->filter('my->recipient', 'in', $recipients);
        $query->filter('my->sender', 'in', $senders);
        return $query->execute();
    }

    /** FriendRequestMessage, or a mock object for unit testing. */
    protected static $instance = null;

    /**
     *  Returns an instance of this class.
     *
     *  @return mixed  FriendRequestMessage, or a mock object for unit testing.
     */
    private static function instance() {
        if (! self::$instance) { self::$instance = new FriendRequestMessage(); }
        return self::$instance;
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}

