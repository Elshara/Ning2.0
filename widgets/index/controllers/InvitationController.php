<?php

/**
 * Dispatches requests pertaining to invitations.
 */
class Index_InvitationController extends XG_BrowserAwareController {

    /** Whether the script is deliberately exiting with a 500 HTTP status code */
    private $exitingWith500 = false;

    /**
     * Runs code before each action.
     */
    protected function _before() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationFormHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationMode.php');
        XG_HttpHelper::trimGetAndPostValues();
    }

    /**
     * Displays a form for sending invitations.
     *
     * Expected GET variables:
     *     sent - whether invitations were just sent
     *     noAddressesFound - whether the address import found 0 addresses
     *
     * @param $formToOpen string  (optional) which form to open: enterEmailAddresses, inviteFriends, webAddressBook, or emailApplication
     * @param $errors array  (optional) HTML error messages, optionally with keys field name
     */
    public function action_new($formToOpen = 'enterEmailAddresses', $errors = array()) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        XG_SecurityHelper::redirectIfNotMember();
        if (! XG_App::canSendInvites($this->_user)) { return $this->redirectTo(xg_absolute_url('/')); }
        $this->showInvitationsSentMessage = $_GET['sent'];
        $this->showNoAddressesFoundMessage = $_GET['noAddressesFound'];
        $numFriendsAcrossNing = Index_MessageHelper::numberOfFriendsAcrossNing($this->_user->screenName);
        $numFriendsOnNetwork = Index_MessageHelper::numberOfFriendsOnNetwork($this->_user->screenName);
        $this->invitationArgs = array(
                'formToOpen' => $formToOpen,
                'errors' => $errors,
                'createUrl' => $this->_buildUrl('invitation', 'create'),
                'enterEmailAddressesButtonText' => xg_text('SEND_INVITATIONS'),
                'inviteFriendsTitle' => xg_text('INVITE_FRIENDS'),
                'inviteFriendsDescription' => xg_text('INVITE_YOUR_FRIENDS_TO_APPNAME', XN_Application::load()->name),
                'friendDataUrl' => $this->_buildUrl('invitation', 'friendData', array('xn_out' => 'json')),
                'initialFriendSet' => Index_MessageHelper::ALL_FRIENDS,
                'numFriends' => $numFriendsAcrossNing,
                'numSelectableFriends' => $numFriendsAcrossNing - $numFriendsOnNetwork,
                'numSelectableFriendsOnNetwork' => 0,
                'showSelectAllFriendsLink' => TRUE,
                'showSelectFriendsOnNetworkLink' => FALSE,
                'messageParts' => XG_MessageHelper::getDefaultMessageParts());
    }

    /**
     * Displays a form for sending invitations. (iPhone-specific)
     *
     * Expected GET variables
     *  - previousUrl: the url to return to after invites are sent
     *
     * @param $errors array  (optional) HTML error messages, optionally with keys field name
     */
    public function action_new_iphone($errors = array()) {
        XG_SecurityHelper::redirectIfNotMember();
        if (! XG_App::canSendInvites($this->_user)) { return $this->redirectTo(xg_absolute_url('/')); }
        $this->previousUrl = $_GET['previousUrl'] ? $_GET['previousUrl'] : xg_absolute_url('/');
        if (count($errors) > 0) $this->errors = $errors;
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
        $friendData = Index_MessageHelper::dataForFriendsAcrossNing($_GET['start'], $_GET['end']);
        $this->friends = $friendData['friends'];
        $users = User::loadMultiple($friendData['screenNames']);
        $n = count($this->friends);
        for ($i = 0; $i < $n; $i++) {
            $user = $users[$this->friends[$i]['screenName']];
            if ($user && User::isMember($user)) {
                $this->friends[$i]['reasonToDisable'] = xg_text('ALREADY_MEMBER_OF_NETWORK');
            }
            if ($user && User::isBanned($user)) {
                $this->friends[$i]['reasonToDisable'] = xg_text('BANNED_FROM_NETWORK');
            }
        }
    }

    /**
     *  Handler for the "quick post" feature.
     *
     *  @param		$emailAddresses string	Email addresses
     *  @param		$message		string	Optional message
     *  @param		..friend-data..			Selected friends
     *  @return     void
     */
    public function action_createQuick() {
        $this->status = 'fail';
        $this->render('blank');
        if (! $this->_user->isLoggedIn()) {
            $this->message = xg_html('YOU_MUST_BE_SIGNED');
            return;
        }

        $emailResult = NULL;
        $count = 0;
        if ($_POST['emailAddresses']) {
            $emailResult = Index_InvitationFormHelper::processEnterEmailAddressesForm();
            if ($emailResult['errorHtml']) {
                $this->message = $emailResult['errorHtml'];
                return;
            }
        }

        $friendResult = Index_InvitationFormHelper::processInviteFriendsForm();
        if ($friendResult['errorHtml']) {
            // If there is a selected friends error (no selected friends) and there is no email recipients,
            // raise an error, otherwise just send emails to the explicit recipients
            if (!$emailResult) {
                $this->message = $friendResult['errorHtml'];
                return;
            }
        } else {
            $count += Index_InvitationFormHelper::send(array(
                'inviteOrShare' => 'invite',
                'friendSet' => $friendResult['friendSet'],
                'contactList' => $friendResult['contactList'],
                'screenNamesExcluded' => $friendResult['screenNamesExcluded'],
                'message' => $_POST['message'],
            ));
        }

        if ($emailResult) {
            $count += Index_InvitationFormHelper::send(array(
                'inviteOrShare' => 'invite',
                'contactList' => $emailResult['contactList'],
                'message' => $_POST['message'],
            ));
        }

        $this->status = 'ok';
        $this->message = xg_html('YOU_SENT_N_INVITES', $count);
    }

    /**
     * Processes the form for sending invitations. (iPhone-specific)
     *
     * Expected GET variables:
     *     previousUrl - the url to return to after invites are sent
     *
     * Expected POST variables:
     *     emailAddresses - email addresses separated by commas, semicolons, and whitespace
     *     message - optional message for the invitation
     */
    public function action_create_iphone() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! XG_App::canSendInvites($this->_user)) { return $this->redirectTo(xg_absolute_url('/')); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('new', 'invitation'); }
        $result = Index_InvitationFormHelper::processEnterEmailAddressesForm();
        if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array(array($result['errorHtml']))); }
        Index_InvitationFormHelper::send(array(
                'inviteOrShare' => 'invite',
                'contactList' => $result['contactList'],
                'message' => $_POST['message']));
        $notificationParameter = array('notification' => xg_html('YOUR_INVITATIONS_HAVE_BEEN_SENT'));
        $previousUrl = $_GET['previousUrl'] ? xg_url($_GET['previousUrl'], $notificationParameter) : xg_url(xg_absolute_url('/'), $notificationParameter);
        $this->redirectTo($previousUrl, null);
    }

    /**
     * Processes the form for sending invitations.
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
        if (! XG_App::canSendInvites($this->_user)) { return $this->redirectTo(xg_absolute_url('/')); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('new', 'invitation'); }
        switch ($_POST['form']) {

            case 'enterEmailAddresses':
                $result = Index_InvitationFormHelper::processEnterEmailAddressesForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('enterEmailAddresses', array($result['errorHtml']))); }
                Index_InvitationFormHelper::send(array(
                        'inviteOrShare' => 'invite',
                        'contactList' => $result['contactList'],
                        'message' => $_POST['message']));
                $this->redirectTo('new', 'invitation', array('sent' => 1));
                break;

            case 'inviteFriends':
                $result = Index_InvitationFormHelper::processInviteFriendsForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('inviteFriends', array($result['errorHtml']))); }
                Index_InvitationFormHelper::send(array(
                        'inviteOrShare' => 'invite',
                        'friendSet' => $result['friendSet'],
                        'contactList' => $result['contactList'],
                        'screenNamesExcluded' => $result['screenNamesExcluded'],
                        'message' => $_POST['inviteFriendsMessage']));
                $this->redirectTo('new', 'invitation', array('sent' => 1));
                break;

            case 'webAddressBook':
                $result = Index_InvitationFormHelper::processWebAddressBookForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('webAddressBook', array($result['errorHtml']))); }
                $this->redirectTo($result['target']);
                break;

            case 'emailApplication':
                $result = Index_InvitationFormHelper::processEmailApplicationForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('emailApplication', array($result['errorHtml']))); }
                $this->redirectTo($result['target']);
                break;
        }
    }

    /**
     * Displays an AJAX-based form for editing the list of recipients for the invitation.
     *
     * Expected GET variables:
     *     contactListId - content ID of a ContactList object
     */
    public function action_editContactList() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! XG_App::canSendInvites($this->_user)) { return $this->redirectTo(xg_absolute_url('/')); }
        if (! unserialize(ContactList::load($_GET['contactListId'])->my->contacts)) { return $this->redirectTo('new', 'invitation', array('noAddressesFound' => 1)); }
        $this->invitationArgs = array(
                'contactListId' => $_GET['contactListId'],
                'createWithContactListUrl' => $this->_buildUrl('invitation', 'createWithContactList', array('contactListId' => $_GET['contactListId'])),
                'cancelUrl' => $this->_buildUrl('invitation', 'new'),
                'inviteOrShare' => 'invite',
                'searchLabelText' => xg_text('SEARCH_FRIENDS_TO_INVITE'),
                'messageParts' => XG_MessageHelper::getDefaultMessageParts(),
                'submitButtonText' => xg_text('INVITE'));
    }

    /**
     * Processes the Contact List form.
     *
     * Expected GET variables:
     *     contactListId - content ID of a ContactList object
     *
     * Expected POST variables:
     *     contactListJson - a JSON array of contacts, each being an array with keys "name" and "emailAddress"
     *     message - optional message for the invitation
     */
    public function action_createWithContactList() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! XG_App::canSendInvites($this->_user)) { return $this->redirectTo(xg_absolute_url('/')); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('new', 'invitation'); }
        Index_InvitationFormHelper::processContactListForm('invite');
        $this->redirectTo('new', 'invitation', array('sent' => 1));
    }





    //#############################################################################
    // Common code. The actions below are shared between /main/invitation,
    // /groups/invitation, /events/invitation, and /main/sharing.
    //#############################################################################

    /**
     * Displays a form for choosing the method of inviting: Enter Email Address,
     * Web Address Book, or Email Application.
     *
     * @param $formToOpen string  which form to open: enterEmailAddresses, inviteFriends, webAddressBook, or emailApplication
     * @param $errors array  HTML error messages, optionally with keys field name
     * @param $createUrl string  the URL for processing the form for sending invitations
     * @param $enterEmailAddressesButtonText string  text for the submit button for the Enter Email Address form.
     * @param $inviteFriendsTitle string  title for the Invite Friends section
     * @param $inviteFriendsDescription string  description for the Invite Friends section
     * @param $friendDataUrl string  endpoint for retrieving friend info
     * @param $numFriendsAcrossNing integer  total number of friends
     * @param $numSelectableFriends integer  number of friends that can be selected
     * @param $numSelectableFriendsOnNetwork integer  number of friends (on the current network) that can be selected
     * @param $messageParts hash part_name => part_text. Used for warning user that his message contains potential spam.
     */
    public function action_chooseInvitationMethod($args) {
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        foreach ($args as $key => $value) { $this->{$key} = $value; }
        $this->enterEmailAddressesErrors = $this->formToOpen == 'enterEmailAddresses' ? $this->errors : array();
        $this->showInviteFriendsForm = $this->numSelectableFriends > 0;
        $this->inviteFriendsErrors = $this->formToOpen == 'inviteFriends' ? $this->errors : array();
        $this->webAddressBookErrors = $this->formToOpen == 'webAddressBook' ? $this->errors : array();
        $this->emailApplicationErrors = $this->formToOpen == 'emailApplication' ? $this->errors : array();
        $this->emailDomains = Index_InvitationFormHelper::getEmailDomains();
        $this->showWebAddressBookForm = count($this->emailDomains) > 0;
        $this->emailDomains = array_merge(
                array('' => xg_text('SELECT_ELLIPSIS')),
                $this->emailDomains,
                array('(other)' => xg_text('OTHER_ELLIPSIS')));
        $importServices = XN_ContactImportService::listServices();
        $this->showEmailApplicationForm = $importServices['csv'] || $importServices['vcf'];
        $formDefaults = array(
            'emailAddress' => $_REQUEST['emailAddresses'],
        );
        $emailParts = explode('@', $this->_user->email);
        if ($this->emailDomains[$emailParts[1]]) {
            $formDefaults['emailLocalPart'] = $emailParts[0];
            $formDefaults['emailDomain'] = $emailParts[1];
        }
        $this->form = new XNC_Form($formDefaults);
    }

    /**
     * Displays a list of contacts that can be searched and sorted.
     *
     * @param $contactListId string  content ID of a ContactList object
     * @param $createWithContactListUrl string  the URL for processing the Contact List form
     * @param $cancelUrl string  the URL to go to when Cancel is pressed
     * @param $inviteOrShare string  the current context: "invite" or "share"
     * @param $searchLabelText string  text for the label beside the search field
     * @param $submitButtonText string  text for the button that submits the selected contacts
     * @param $messageParts hash part_name => part_text. Used for warning user that his message contains potential spam.
     */
    public function action_contactList($args) {
        foreach ($args as $key => $value) { $this->{$key} = $value; }
        if (! $this->contactListId) { throw new Exception('No contact list specified (1657397187)'); }
        ContactList::cleanUp();
        $contactListObject = ContactList::load($this->contactListId);
        $this->contactList = unserialize($contactListObject->my->contacts);
        $count = count($this->contactList);
        for ($i = 0; $i < $count; $i++) {
            if (! $this->contactList[$i]['name']) {
                $this->contactList[$i]['name'] = Index_InvitationFormHelper::generateName($this->contactList[$i]['emailAddress']);
            }
        }
        usort($this->contactList, array($this, 'compareContactsByName'));
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->contactListJson = $json->encode($this->contactList);
        $this->cancelUrl = XG_HttpHelper::addParameters($this->_buildUrl('invitation', 'deleteContactList'), array('contactListId' => $this->contactListId, 'target' => $this->cancelUrl));
    }

    /**
     * Deletes a ContactList object.  ContactLists are temporary objects, used
     * when choosing recipients for an invitation..
     *
     * Expected GET variables:
     *     contactListId - content ID of a ContactList object
     *     target - the URL to redirect to afterwards
     */
    public function action_deleteContactList() {
        if (! $_GET['contactListId']) { throw new Exception('No contact list specified (1657397187)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (748876971)'); }
        try {
            XN_Content::delete(ContactList::load($_GET['contactListId']));
        } catch (Exception $e) {
            // Ignore [Jon Aquino 2007-10-25]
        }
        $this->redirectTo($_GET['target']);
    }

    /**
     * Returns -1, 0, or 1 depending on whether $a's name comes before, with, or after $b's name.
     *
     * @param $a  the first contact, with keys "name" and "emailAddress"
     * @param $b  the second contact, with keys "name" and "emailAddress"
     */
    private function compareContactsByName($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    }

    /**
     * An interstitial page with spinner, shown during a long contact-list import.
     *
     * Expected GET parameters:
     *     jobId - a key identifying the job to monitor
     *     target - URL to go to after the import (contactListId will be appended)
     */
    public function action_waitForImport() {
        $this->jobId = $_GET['jobId'];
        $this->target = $_GET['target'];
    }

    /**
     *  Checks message for strings that can be potentially marked as spam
     *
     *  @param      $messageParts	hash	part-name:part-text. Text parts to check for spam.
     *  @return     hash
     *		status:			ok|warning|error		Not a spam/Potentially a spam/Probably a spam.
     *		messageParts	hash<name:[text]>		List of exceprts
     */
    public function action_checkMessageForSpam() {
        XG_App::includeFileOnce('/lib/XG_SpamHelper.php');
        $parts = $_REQUEST['messageParts'] ? json_decode($_REQUEST['messageParts']) : array();
        $this->messageParts = array();
        $count = 0;
        foreach ($parts as $name=>$value) {
            $bad = XG_SpamHelper::checkString($value);
            $this->messageParts[$name] = $bad;
            $count += count($bad);
        }
        $this->status = (0 == $count ? 'ok' : ($count <= 5 ? 'warning' : 'error'));
    }


    /**
     * Checks the status of the job that extracts email addresses.
     *
     * Expected GET parameters:
     *     xn_out - always "json"
     *     jobId - a key identifying the job to check on
     *
     * JSON output:
     *     complete - whether the job is finished
     *     contactListId - content ID of a ContactList object, if the job is finished
     */
    public function action_checkImport() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (1013274105)'); }
        $result = XN_ContactImportResult::load($_GET['jobId']);
        if (Index_InvitationHelper::isErrorArray($result)) { throw new Exception(Index_InvitationHelper::errorMessage(key($result))); }
        $this->complete = $result->status == XN_ContactImportResult::COMPLETE;
        if ($this->complete) { $this->contactListId = ContactList::create(Index_InvitationFormHelper::importedContactsToContactList(Index_InvitationFormHelper::allImportedContacts($result)))->id; }
    }

    /**
     * Sends messages to the specified people.
     *
     * Expected POST variables:
     *     inviteOrShare - the current context: "invite" or "share"
     *     groupId - the content ID of the associated Group, or null if none.
     *     eventId - the content ID of the associated Event, or null if none.
     *     docUrl - arbitrary network URL to share
     *     docTitle - URL title
     *     contactList - JSON array of contacts, each an array with keys "name" and "emailAddress"
     *     friendStart - (if contactList is not specified) inclusive start index for a friend query
     *     friendEnd - (if contactList is not specified) exclusive end index for a friend query
     *     friendSet - (if contactList is not specified) Index_MessageHelper::ALL_FRIENDS (default) or Index_MessageHelper::FRIENDS_ON_NETWORK
     *     screenNamesExcluded - (if contactList is not specified) list of screenNames to exclude from mailing
     *     message - optional custom message
     *     contentId - the associated content ID for share messages; null for invitations
     *     retry - whether to retry if errors occur
     */
    public function action_send() {
        header('HTTP/1.0 500 Internal Error');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (1013274105)'); }
        if (! User::isMember(XN_Profile::current())) { throw new Exception('Not a member (1209116584)'); }
        $this->_widget->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        $contactList = Index_MessageHelper::instance()->createContactList($_POST);
        if (count($contactList) > 0) {
			XG_Browser::execInEmailContext(array($this,'_sendInvitation'), $contactList);
        }
        if (! $this->exitingWith500) { header('HTTP/1.0 200 OK'); }

        // Design Notes for the FriendList (BAZ-8932)
        //
        // The FriendList is used in the following places:
        //   - /profiles/messaging/new (calls /main/invitation/friendData)
        //   - /quickadd/invite (calls /main/invitation/friendData)
        //   - /quickadd/share (calls /main/sharing/friendData)
        //   - /main/sharing/new (calls /main/sharing/friendData)
        //   - /main/invitation/new (calls /main/invitation/friendData)
        //   - /groups/invitation/new (calls /groups/invitation/friendData)
        //   - /events/invitation/new (calls /events/invitation/friendData)
        //
        // When the user clicks Select None then selects some friends, the following will be set:
        //   - contactList
        //
        // When the user clicks Select All then deselects some friends, the following will be set:
        //   - friendStart
        //   - friendEnd
        //   - friendSet = Index_MessageHelper::ALL_FRIENDS
        //   - screenNamesExcluded
        //
        // When the user clicks Select Friends On This Network then selects some friends and deselects others,
        // the following will be set for friends on the network:
        //   - friendStart
        //   - friendEnd
        //   - friendSet = Index_MessageHelper::FRIENDS_ON_NETWORK
        //   - screenNamesExcluded
        // And the following will be set for friends off the network, in separate requests:
        //   - contactList
        //
        // [Jon Aquino 2008-08-14]
    }

    /**
     * Sends messages to the specified people.
     *
     * Expected POST variables:
     *     inviteOrShare - the current context: "invite" or "share"
     *     groupId - the content ID of the associated Group, or null if none.
     *     eventId - the content ID of the associated Event, or null if none.
     *     docUrl - arbitrary network URL to share
     *     docTitle - URL title
     *     contactList - JSON array of contacts, each an array with keys "name" and "emailAddress"
     *     friendStart - (if contactList is not specified) inclusive start index for a friend query
     *     friendEnd - (if contactList is not specified) exclusive end index for a friend query
     *     friendSet - (if contactList is not specified) Index_MessageHelper::ALL_FRIENDS (default) or Index_MessageHelper::FRIENDS_ON_NETWORK
     *     screenNamesExcluded - (if contactList is not specified) list of screenNames to exclude from mailing
     *     message - optional custom message
     *     contentId - the associated content ID for share messages; null for invitations
     *     retry - whether to retry if errors occur
     */
    public function action_send_iphone() {
        $this->action_send();
    }

	// callback for sending invitations
    public function _sendInvitation($contactList) { # void
		Index_InvitationMode::get($_POST)->send($contactList, $_POST['message'], $_POST['contentId'], array($this, 'onSendError'));
    }

    /**
     * Called when an error occurs in Index_InvitationMode->send().
     *
     * Expected POST variables:
     *     inviteOrShare - the current context: "invite" or "share"
     *     groupId - the content ID of the associated Group, or null if none.
     *     eventId - the content ID of the associated Event, or null if none.
     *     contactList - JSON array of contacts, each an array with keys "name" and "emailAddress"
     *     message - optional custom message
     *     contentId - the associated content ID for share messages; null for invitations
     *     retry - whether to retry if errors occur
     *
     * @param $failedContacts array  contacts that were not processed because of errors
     */
    public function onSendError($failedContacts) {
        if (! $_POST['retry']) { $this->exitWith500(); }
        $onSendErrorProper = new XG_FaultTolerantTask(array($this, 'exitWith500'));
        $onSendErrorProper->add(array($this, 'onSendErrorProper'), array($failedContacts));
        $onSendErrorProper->execute(null);
    }

    /**
     * Called when an error occurs in Index_InvitationMode->send().
     *
     * Expected POST variables:
     *     inviteOrShare - the current context: "invite" or "share"
     *     groupId - the content ID of the associated Group, or null if none.
     *     eventId - the content ID of the associated Event, or null if none.
     *     contactList - JSON array of contacts, each an array with keys "name" and "emailAddress"
     *     message - optional custom message
     *     contentId - the associated content ID for share messages; null for invitations
     *     retry - whether to retry if errors occur
     *
     * @param $failedContacts array  contacts that were not processed because of errors
     */
    public function onSendErrorProper($failedContacts) {
        Index_InvitationFormHelper::send(array(
                'inviteOrShare' => $_POST['inviteOrShare'],
                'groupId' => $_POST['groupId'],
                'eventId' => $_POST['eventId'],
                'contactList' => $failedContacts,
                'message' => $_POST['message'],
                'contentId' => $_POST['contentId'],
                'retry' => false));
    }

    /**
     * Terminates the current action, with a 500 HTTP status code.
     */
    public function exitWith500() {
        // Log, for manual recovery [Jon Aquino 2007-11-15]
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        error_log('exitWith500 ' . XN_Profile::current()->screenName . ' ' . $json->encode($_POST));
        $this->exitingWith500 = true;
        header('HTTP/1.0 500 Internal Error');
        exit;
    }

}
