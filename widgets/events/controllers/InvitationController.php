<?php

/**
 * Dispatches requests pertaining to invitations.
 */
class Events_InvitationController extends W_Controller {

    /**
     * Constructor.
     *
     * @param   $widget     W_BaseWidget    The Events widget
     */
    public function __construct(W_BaseWidget $widget) {
        parent::__construct($widget);
        EventWidget::init();
    }

    /**
     * Runs code before each action.
     */
    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Events_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Events_InvitationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Events_RequestHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationFormHelper.php');
        XG_HttpHelper::trimGetAndPostValues();
    }

    /**
     * Displays a form for sending invitations.
     *
     * Expected GET variables:
     *     sent - whether invitations were just sent
     *     noAddressesFound - whether the address import found 0 addresses
     *     creatingEvent - whether the event has just been created
     *     eventId - the content ID of the associated Event.
     *
     * @param $formToOpen string  (optional) which form to open: enterEmailAddresses, inviteFriends, webAddressBook, or emailApplication
     * @param $errors array  (optional) HTML error messages, optionally with keys field name
     */
    public function action_new($formToOpen = 'enterEmailAddresses', $errors = array()) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Events_TemplateHelper.php');
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        XG_SecurityHelper::redirectIfNotMember();
        $eventId = Events_RequestHelper::readEventId($_GET, 'eventId');
        if ($eventId === null || !($this->event = Event::byId($eventId))) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if (! Events_SecurityHelper::currentUserCanSendInvites($this->event)) { return $this->redirectTo(xg_absolute_url('/')); }
        $this->showInvitationsSentMessage = Events_RequestHelper::readBoolean($_GET, 'sent');
        $this->showNoAddressesFoundMessage = Events_RequestHelper::readBoolean($_GET, 'noAddressesFound');
        $this->creatingEvent = Events_RequestHelper::readBoolean($_GET, 'creatingEvent');
        $numFriendsAcrossNing = Index_MessageHelper::numberOfFriendsAcrossNing($this->_user->screenName);
        $numFriendsOnNetwork = Index_MessageHelper::numberOfFriendsOnNetwork($this->_user->screenName);
        $createParams = array('eventId' => $this->event->id);
        if ($this->creatingEvent) {
            $createParams['creatingEvent'] = 1;
        }
        $this->invitationArgs = array(
                'formToOpen' => $formToOpen,
                'errors' => $errors,
                'createUrl' => $this->_buildUrl('invitation', 'create', $createParams),
                'enterEmailAddressesButtonText' => xg_text('SEND_INVITATIONS'),
                'inviteFriendsTitle' => xg_text('INVITE_FRIENDS'),
                'inviteFriendsDescription' => xg_text('INVITE_YOUR_FRIENDS_TO_EVENTNAME', $this->event->title),
                'friendDataUrl' => $this->_buildUrl('invitation', 'friendData', array('xn_out' => 'json', 'eventId' => $this->event->id)),
                'initialFriendSet' => Index_MessageHelper::ALL_FRIENDS,
                'numFriends' => $numFriendsAcrossNing,
                // TODO: Specify 'numSelectableFriends' and 'numSelectableFriendsOnNetwork'
                // as we do for network and group invitations [Jon Aquino 2008-07-10]
                'numSelectableFriends' => $numFriendsAcrossNing,
                'numSelectableFriendsOnNetwork' => $numFriendsOnNetwork,
                'showSelectAllFriendsLink' => TRUE,
                'showSelectFriendsOnNetworkLink' => TRUE,
                'messageParts' => Events_InvitationHelper::getMessageParts($this->event));
    }

    /**
     * Processes the form for sending invitations.
     *
     * Expected GET variables:
     *     creatingEvent - whether the event has just been created
     *     eventId - the content ID of the associated group.
     *
     * Expected POST variables:
     *
     *     form - "enterEmailAddresses"
     *     emailAddresses - email addresses separated by commas, semicolons, and whitespace
     *     message - optional message for the invitation
     *
     * or
     *
     *     form - "inviteFriends"
     *     friendSet - base set of friends: null, Index_MessageHelper::ALL_FRIENDS, or Index_MessageHelper::FRIENDS_ON_NETWORK
     *     screenNamesExcluded - JSON array of screen names of friends to exclude from the base set
     *     screenNamesIncluded - JSON array of screen names of friends to include with the base set
     *     inviteFriendsMessage - optional message for the invitation
     *
     * or
     *
     *     form - "webAddressBook"
     *     emailLocalPart - the part of the email address before the "@"
     *     emailDomain - the part of the email address after the "@"
     *     password - the password for the email address
     *
     * or
     *
     *     form - "emailApplication"
     *     file - a file containing CSV or VCF data
     */
    public function action_create() {
        XG_SecurityHelper::redirectIfNotMember();
        $eventId = Events_RequestHelper::readEventId($_GET, 'eventId');
        if ($eventId === null || !($event = Event::byId($eventId))) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if (! Events_SecurityHelper::currentUserCanSendInvites($event)) { return $this->redirectTo(xg_absolute_url('/')); }
        $creatingEvent = Events_RequestHelper::readBoolean($_GET, 'creatingEvent');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('new', 'invitation', array('eventId' => $event->id)); }
        switch ($_POST['form']) {

            case 'enterEmailAddresses':
                $result = Index_InvitationFormHelper::processEnterEmailAddressesForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('enterEmailAddresses', array($result['errorHtml']))); }
                Index_InvitationFormHelper::send(array(
                        'inviteOrShare' => 'invite',
                        'eventId' => $event->id,
                        'contactList' => $result['contactList'],
                        'message' => $_POST['message']));
                if ($creatingEvent) {
                    $this->redirectTo('show', 'event', array('id' => $event->id));
                } else {
                    $this->redirectTo('new', 'invitation', array('sent' => 1, 'eventId' => $event->id));
                }
                break;

            case 'inviteFriends':
                $result = Index_InvitationFormHelper::processInviteFriendsForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('inviteFriends', array($result['errorHtml']))); }
                Index_InvitationFormHelper::send(array(
                        'inviteOrShare' => 'invite',
                        'eventId' => $event->id,
                        'friendSet' => $result['friendSet'],
                        'contactList' => $result['contactList'],
                        'screenNamesExcluded' => $result['screenNamesExcluded'],
                        'message' => $_POST['inviteFriendsMessage']));
                if ($creatingEvent) {
                    $this->redirectTo('show', 'event', array('id' => $event->id));
                } else {
                    $this->redirectTo('new', 'invitation', array('sent' => 1, 'eventId' => $event->id));
                }
                break;

            case 'webAddressBook':
                $result = Index_InvitationFormHelper::processWebAddressBookForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('webAddressBook', array($result['errorHtml']))); }
                if ($creatingEvent) {
                    $result['target'] = XG_HttpHelper::addParameter($result['target'],'creatingEvent', 1);
                }
                $this->redirectTo($result['target']);
                break;

            case 'emailApplication':
                $result = Index_InvitationFormHelper::processEmailApplicationForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('emailApplication', array($result['errorHtml']))); }
                if ($creatingEvent) {
                    $result['target'] = XG_HttpHelper::addParameter($result['target'],'creatingEvent', 1);
                }
                $this->redirectTo($result['target']);
                break;
        }
    }

    /**
     * Displays an AJAX-based form for editing the list of recipients for the invitation.
     *
     * Expected GET variables:
     *     contactListId - content ID of a ContactList object
     *     creatingEvent - whether the event has just been created
     */
    public function action_editContactList() {
        XG_SecurityHelper::redirectIfNotMember();
        $eventId = Events_RequestHelper::readEventId($_GET, 'eventId');
        if ($eventId === null || !($this->event = Event::byId($eventId))) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if (! Events_SecurityHelper::currentUserCanSendInvites($this->event)) { return $this->redirectTo(xg_absolute_url('/')); }
        $contactListId = Events_RequestHelper::readEventId($_GET, 'contactListId');
        if ($contactListId === null) {
            return $this->redirectTo('new', 'invitation', array('eventId' => $this->event->id));
        }
        $contactList = ContactList::load($contactListId);
        if (! unserialize($contactList->my->contacts)) { return $this->redirectTo('new', 'invitation', array('noAddressesFound' => 1, 'eventId' => $this->event->id)); }
        $creatingEvent = Events_RequestHelper::readBoolean($_GET, 'creatingEvent');
        $createWithContactListParams = array('contactListId' => $contactListId, 'eventId' => $this->event->id);
        if ($creatingEvent) {
            $createWithContactListParams['creatingEvent'] = 1;
        }
        $this->invitationArgs = array(
                'contactListId' => $contactListId,
                'createWithContactListUrl' => $this->_buildUrl('invitation', 'createWithContactList', $createWithContactListParams),
                'cancelUrl' => $this->_buildUrl('invitation', 'new', array('eventId' => $this->event->id)),
                'inviteOrShare' => 'invite',
                'messageParts' => Events_InvitationHelper::getMessageParts($this->event),
                'searchLabelText' => xg_text('SEARCH_FRIENDS_TO_INVITE'),
                'submitButtonText' => xg_text('INVITE'));
    }

    /**
     * Processes the Contact List form.
     *
     * Expected GET variables:
     *     contactListId - content ID of a ContactList object
     *     creatingEvent - whether the event has just been created
     *
     * Expected POST variables:
     *     contactListJson - a JSON array of contacts, each being an array with keys "name" and "emailAddress"
     *     message - optional message for the invitation
     */
    public function action_createWithContactList() {
        XG_SecurityHelper::redirectIfNotMember();
        $eventId = Events_RequestHelper::readEventId($_GET, 'eventId');
        if ($eventId === null || !($event = Event::byId($eventId))) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if (! Events_SecurityHelper::currentUserCanSendInvites($event)) { return $this->redirectTo(xg_absolute_url('/')); }
        $creatingEvent = Events_RequestHelper::readBoolean($_GET, 'creatingEvent');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('new', 'invitation', array('eventId' => $event->id)); }
        Index_InvitationFormHelper::processContactListForm('invite', null, $event->id);
        if ($creatingEvent) {
            $this->redirectTo('show', 'event', array('id' => $event->id));
        } else {
            $this->redirectTo('new', 'invitation', array('sent' => 1, 'eventId' => $event->id));
        }
    }

    /**
     * Outputs JSON for "friends" (each with screenName, fullName, thumbnailUrl, isMember,
     * and optional reasonToDisable).
     *
     * Expected GET variables
     *     xn_out - "json";
     *     start - inclusive start index
     *     end - exclusive end index
     */
    public function action_friendData() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        $eventId = Events_RequestHelper::readEventId($_GET, 'eventId');
        if ($eventId === null) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $start = Events_RequestHelper::readInt($_GET, 'start', 0, 0);
        $end = Events_RequestHelper::readInt($_GET, 'end', $start, $start);
        $friendData = Index_MessageHelper::dataForFriendsAcrossNing($start, $end);
        $this->friends = $friendData['friends'];
        $friendScreenNames = array();
        foreach ($friendData['friends'] as $friend) {
            $friendScreenNames[] = $friend['screenName'];
        }
        $rsvpedScreenNames = array();
        foreach (array_chunk($friendScreenNames, 100) as $chunk) {
            $rsvpedScreenNames += EventAttendee::getRsvpedScreenNames($chunk, $eventId);
        }
        $n = count($this->friends);
        for ($i = 0; $i < $n; $i++) {
            if (!empty($rsvpedScreenNames[$this->friends[$i]['screenName']])) {
                $this->friends[$i]['reasonToDisable'] = xg_text('ALREADY_RSVPED');
            }
        }
    }

}
