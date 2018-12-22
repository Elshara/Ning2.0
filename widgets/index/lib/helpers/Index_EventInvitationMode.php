<?php
/**
 * Logic specific to Event invitations.
 */
class Index_EventInvitationMode extends Index_InvitationMode {

    /** The associated Event. */
    protected $event;

    /** Whether an event invitation was created during the current request. */
    private static $invitationsCreated = false;

    /**
     * Constructor.
     *
     * @param $eventId string  the content ID of the associated Event
     */
    public function __construct($args) {
        $this->event = Event::byId($args['eventId']);
    }

    /**
     * Destructor.
     */
    function __destruct() {
        if (self::$invitationsCreated) {
            XN_Cache::invalidate(Event::cacheLabel($this->event->id));
        }
    }

    /**
     * Sends invitations to people who have not RSVPed. Ignores banned members.
     * Auto-friends; bypasses pending-friend limits.
     *
     * @param $contactList array  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param $message string  optional message for the invitation
     * @param $contentId string  not used
     */
    public function sendProper($contactList, $message, $contentId) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_InvitationHelper.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $profiles = XG_Cache::profiles(array_keys($this->keyByEmailAddress($contactList)));
        $screenNames = User::screenNames($profiles);
        $users = User::loadMultiple($screenNames);
        $eventAttendees = XN_Query::create('Content')
                ->filter('owner')
                ->filter('type', '=', 'EventAttendee')
                ->filter('my.screenName', 'in', $screenNames)
                ->filter('my.eventId', '=', $this->event->id)
                ->execute();
        $invitationRecipients = $this->classifyRecipients(array(
                'contactList' => $this->keyByEmailAddress($contactList),
                'users' => $usersKeyedByEmailAddress = $this->keyByEmailAddress($users),
                'eventAttendees' => $this->keyByEmailAddress($eventAttendees),
                'currentUserCanSendEventInvites' => Events_SecurityHelper::currentUserCanSendInvites($this->event)));
        foreach ($invitationRecipients as $emailAddress => $name) {
            $invitation = $this->createEventInvitation($emailAddress, $name, $usersKeyedByEmailAddress);
            $messageObject = new XG_Message_Event_Invitation(array(
                    'subject' => xg_text('COME_JOIN_ME_AT_EVENT', $this->event->title, XN_Application::load()->name),
                    'body' => $message,
                    'url' => W_Cache::getWidget('events')->buildUrl('event', 'show', array('id' => $this->event->id, Index_InvitationHelper::KEY => $invitation->id))));
            $this->addToQueue(array($messageObject, 'send'), array($emailAddress, XN_Profile::current()->screenName, $this->event), $emailAddress);
        }
    }

    /**
     * Determines which recipients should receive invitations. All array arguments
     * should be keyed by email address.
     *
     * @param array contactList  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param array users  User objects for the contactList, including pending and banned users
     * @param array eventAttendees  EventAttendee objects for the contactList
     * @param boolean currentUserCanSendEventInvites  whether the current user is allowed to send event invitations
     * @return array names of invitation recipients keyed by email address
     */
    protected function classifyRecipients($args) {
        if (array_keys($args) != array('contactList', 'users', 'eventAttendees', 'currentUserCanSendEventInvites')) { throw new Exception('Assertion failed (1534704753)'); }
        if (! $args['currentUserCanSendEventInvites']) { return array(); }
        $invitationRecipients = array();
        foreach ($args['contactList'] as $emailAddress => $contact) {
            $user = $args['users'][$emailAddress];
            if (User::isBanned($user)) { continue; }
            $eventAttendee = $args['eventAttendees'][$emailAddress];
            if ($eventAttendee && EventAttendee::rsvped($eventAttendee->my->status)) { continue; }
            $invitationRecipients[$emailAddress] = $contact['name'];
        }
        return $invitationRecipients;
    }

    /**
     * Creates a saved XN_Invitation, or returns an existing, equivalent one.
     * If the recipient is a member of the network, associates her User object
     * with the invitation.
     *
     * @param $emailAddress string   email address of the recipient
     * @param $name string  real name of the recipient (optional)
     * @param $usersKeyedByEmailAddress array  mapping of email address to User object
     */
    protected function createEventInvitation($emailAddress, $name, $usersKeyedByEmailAddress, $invitationHelperClass = 'Index_InvitationHelper', $eventInvitationHelperClass = 'Events_InvitationHelper', $eventAttendeeClass = 'EventAttendee') {
        if ($user = $usersKeyedByEmailAddress[$emailAddress]) {
            call_user_func(array($eventAttendeeClass, 'setStatus'), $user->title, $this->event, EventAttendee::NOT_RSVP, false, XN_Profile::current()->screenName);
        }
        self::$invitationsCreated = true;
        return call_user_func(array($invitationHelperClass, 'createInvitation'), $emailAddress, $name, call_user_func(array($eventInvitationHelperClass, 'eventInvitationLabel'), $this->event->id));
    }

}


