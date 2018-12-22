<?php

class Index_SharingController extends W_Controller {

    /**
     * Runs code before each action.
     */
    protected function _before() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationFormHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Index_SharingHelper.php');
        XG_HttpHelper::trimGetAndPostValues();
        $_GET['id'] = urldecode($_GET['id']);  // Is this needed?  [Jon Aquino 2007-10-26]
    }

    /**
     * Display the 'share this' page for the specified object
     *
     * Expected GET params:
     *     id - content object ID
     *     sent - whether messages were just sent
     *     noAddressesFound - whether the address import found 0 addresses
     *
     * @param $formToOpen string  (optional) which form to open: enterEmailAddresses, inviteFriends, webAddressBook, or emailApplication
     * @param $errors array  (optional) HTML error messages, optionally with keys field name
     */
    public function action_share($formToOpen = 'enterEmailAddresses', $errors = array()) {
        $this->_widget->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        if (! $this->_user->isLoggedIn()) { return $this->forwardTo('shareSignedOut'); }
        $this->itemInfo = self::getItemInfo($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        self::verifyUserCanShare($this->itemInfo['object'], $_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        $this->pageTitle = Index_SharingHelper::pageTitle($this->itemInfo);
        $this->showInvitationsSentMessage = $_GET['sent'];
        $this->showNoAddressesFoundMessage = $_GET['noAddressesFound'];
        $numFriendsAcrossNing = Index_MessageHelper::numberOfFriendsAcrossNing($this->_user->screenName);
        $numFriendsOnNetwork = Index_MessageHelper::numberOfFriendsOnNetwork($this->_user->screenName);
        $this->invitationArgs = array(
                'formToOpen' => $formToOpen,
                'errors' => $errors,
                'createUrl' => $this->_buildUrl('sharing', 'create', self::getItemArr($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type'])),
                'enterEmailAddressesButtonText' => xg_text('SEND_MESSAGE'),
                'inviteFriendsTitle' => xg_text('SHARE_WITH_FRIENDS'),
                'inviteFriendsDescription' => xg_text('SHARE_THIS_WITH_FRIENDS'),
                'friendDataUrl' => $this->_buildUrl('sharing', 'friendData', array('xn_out' => 'json')),
                'initialFriendSet' => Index_MessageHelper::ALL_FRIENDS,
                'numFriends' => $numFriendsAcrossNing,
                'numSelectableFriends' => $numFriendsAcrossNing,
                'numSelectableFriendsOnNetwork' => $numFriendsOnNetwork,
                'showSelectAllFriendsLink' => TRUE,
                'showSelectFriendsOnNetworkLink' => TRUE,
                'messageParts' => $this->itemInfo['message_parts']);
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
        $this->_widget->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        $friendData = Index_MessageHelper::dataForFriendsAcrossNing($_GET['start'], $_GET['end']);
        $this->friends = $friendData['friends'];
    }

    /**
     *  Handler for the "quick post" feature.
     *
     *  @param      $title		string		Document title
     *  @param		$url		string		Document URL
     *  @param		$contentId	string		If the sharing dialog is called from the content page, specifies the content id of
     *  									this object.
     *	@param		$message	string		Optional message
     *  @param		$emailAddresses string	Email addresses
     *  @param		..friend-data..			Selected friends
     *  @return     void
     */
    public function action_shareQuick() {
        $this->status = 'fail';
        $this->render('blank');
        if (! $this->_user->isLoggedIn()) {
            $this->message = xg_html('YOU_MUST_BE_SIGNED');
            return;
        }
        if (! $_POST['url']) {
            $this->message = xg_html('URL_MUST_BE_SPECIFIED');
            return;
        }

        $emailResult = NULL;
        if ($_POST['emailAddresses']) {
            $emailResult = Index_InvitationFormHelper::processEnterEmailAddressesForm();
            if ($emailResult['errorHtml']) {
                $this->message = $emailResult['errorHtml'];
                return;
            }
        }

        $args = array(
            'inviteOrShare' => 'share',
            'message' => $_POST['message'],
        );
        if ($_POST['contentId']) {
            $itemInfo = self::getItemInfo($_POST['contentId']);
            if (!Index_SharingHelper::userCanShare($itemInfo['object'])) {
                $this->message = xg_html('YOU_CANNOT_SHARE_THIS');
                return;
            }
            $args['groupId'] = Index_SharingHelper::groupId($itemInfo);
            $args['contentId'] = $_POST['contentId'];
        } else {
            $args['shareType'] = 'url';
            $args['docTitle'] = $_POST['title'];
            $args['docUrl'] = $_POST['url'];
        }

        $friendResult = Index_InvitationFormHelper::processInviteFriendsForm();
        if ($friendResult['errorHtml']) {
            // If there is a selected friends error (no selected friends) and there is no email recipients,
            // raise an error, otherwise just send emails to the explicit recipients
            if (!$emailResult) {
                $this->message = $emailResult['errorHtml'];
                return;
            }
        } else {
            Index_InvitationFormHelper::send(array_merge($args, array(
                'friendSet' => $friendResult['friendSet'],
                'contactList' => $friendResult['contactList'],
                'screenNamesExcluded' => $friendResult['screenNamesExcluded'],
            )));
        }

        if ($emailResult) {
            Index_InvitationFormHelper::send(array_merge($args, array(
                'contactList' => $emailResult['contactList'],
            )));
        }

        $this->status = 'ok';
        $this->message = xg_html('YOUR_MESSAGE_HAS_BEEN');
    }

    /**
     * Processes the form for sending invitations.
     *
     * Expected GET params:
     *     id - content object ID
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
        if (! $this->_user->isLoggedIn()) { return $this->forwardTo('shareSignedOut'); }
        $this->itemInfo = self::getItemInfo($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        self::verifyUserCanShare($this->itemInfo['object'], $_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('share', 'sharing', self::getItemArr($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type'])); }
        switch ($_POST['form']) {

            case 'enterEmailAddresses':
                $result = Index_InvitationFormHelper::processEnterEmailAddressesForm();
                if ($result['errorHtml']) { return $this->forwardTo('share', 'sharing', array('enterEmailAddresses', array($result['errorHtml']))); }
                $count = Index_InvitationFormHelper::send(array(
                        'inviteOrShare' => 'share',
                        'groupId' => Index_SharingHelper::groupId($this->itemInfo),
                        'contactList' => $result['contactList'],
                        'message' => $_POST['message'],
                        'contentId' => $_GET['id'],
                		'docUrl' => $_GET['url'],
                		'docTitle' => $_GET['title'],
                		'shareType' => $_GET['type']));
                $this->redirectTo('share', 'sharing', self::getItemArr($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type'], array('sent' => 1)));
                break;

            case 'inviteFriends':
                $result = Index_InvitationFormHelper::processInviteFriendsForm();
                if ($result['errorHtml']) { return $this->forwardTo('share', 'sharing', array('inviteFriends', array($result['errorHtml']))); }
                Index_InvitationFormHelper::send(array(
                        'inviteOrShare' => 'share',
                        'groupId' => Index_SharingHelper::groupId($this->itemInfo),
                        'friendSet' => $result['friendSet'],
                        'contactList' => $result['contactList'],
                        'screenNamesExcluded' => $result['screenNamesExcluded'],
                        'message' => $_POST['inviteFriendsMessage'],
                        'contentId' => $_GET['id'],
                		'docUrl' => $_GET['url'],
                		'docTitle' => $_GET['title'],
                		'shareType' => $_GET['type']));
                $this->redirectTo('share', 'sharing', self::getItemArr($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type'], array('sent' => 1)));
                break;

            case 'webAddressBook':
                $result = Index_InvitationFormHelper::processWebAddressBookForm();
                if ($result['errorHtml']) { return $this->forwardTo('share', 'sharing', array('webAddressBook', array($result['errorHtml']))); }
                $this->redirectTo($result['target']);
                break;

            case 'emailApplication':
                $result = Index_InvitationFormHelper::processEmailApplicationForm();
                if ($result['errorHtml']) { return $this->forwardTo('share', 'sharing', array('emailApplication', array($result['errorHtml']))); }
                $this->redirectTo($result['target']);
                break;
        }
    }

    /**
     * Displays an AJAX-based form for editing the list of recipients for the invitation.
     *
     * Expected GET params:
     *     id - content object ID
     *
     * Expected GET variables:
     *     contactListId - content ID of a ContactList object
     */
    public function action_editContactList() {
        if (! $this->_user->isLoggedIn()) { return $this->forwardTo('shareSignedOut'); }
        if (! unserialize(ContactList::load($_GET['contactListId'])->my->contacts)) { return $this->redirectTo('share', 'sharing', array('noAddressesFound' => 1, 'id' => $_GET['id'])); }
        $this->itemInfo = self::getItemInfo($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        self::verifyUserCanShare($this->itemInfo['object'], $_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        $this->pageTitle = Index_SharingHelper::pageTitle($this->itemInfo);
        $this->invitationArgs = array(
                'contactListId' => $_GET['contactListId'],
                'createWithContactListUrl' => $this->_buildUrl('sharing', 'createWithContactList', self::getItemArr($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type'], array('contactListId' => $_GET['contactListId']))),
                'cancelUrl' => Index_SharingHelper::url($this->itemInfo),
                'inviteOrShare' => 'share',
                'searchLabelText' => xg_text('SEARCH_FRIENDS'),
                'messageParts' => $this->itemInfo['message_parts'],
                'submitButtonText' => xg_text('SEND'));
    }

    /**
     * Processes the Contact List form.
     *
     * Expected GET variables:
     *     contactListId - content ID of a ContactList object
     *     id - content object ID
     *
     * Expected POST variables:
     *     contactListJson - a JSON array of contacts, each being an array with keys "name" and "emailAddress"
     *     message - optional message for the invitation
     */
    public function action_createWithContactList() {
        if (! $this->_user->isLoggedIn()) { return $this->forwardTo('shareSignedOut'); }
        $this->itemInfo = self::getItemInfo($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        self::verifyUserCanShare($this->itemInfo['object'], $_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('share', 'sharing', self::getItemArr($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type'])); }
        Index_InvitationFormHelper::processContactListForm('share', Index_SharingHelper::groupId($this->itemInfo));
        $this->redirectTo('share', 'sharing', self::getItemArr($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type'], array('sent' => 1)));
    }

    public function action_shareSignedOut() {
        XG_App::includeFileOnce('/lib/XG_Message.php');
        if ($this->_user->isLoggedIn()) { return $this->forwardTo('share'); }
        $this->itemInfo = self::getItemInfo($_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        self::verifyCanDisplayToLoggedOut($this->itemInfo['object'], $_GET['id'], $_GET['url'], $_GET['title'], $_GET['type']);
        $this->pageTitle = Index_SharingHelper::pageTitle($this->itemInfo);
        $message = Index_SharingHelper::createMessage($this->itemInfo, $message);
        $body = $message->build(null, null, false);
        $this->mailToLink = xg_mailto_url('', Index_SharingHelper::subject($this->itemInfo), $body);
    }

    private function verifyUserCanShare($obj, $objid, $url, $title) {
    	if (empty($objid) && !empty($url) && !empty($title)) 
    		return;
        if (! Index_SharingHelper::userCanShare($obj)) {
            header('Location: ' . xg_absolute_url('/'));
            exit;
        }
    }

    /**
     * can this item be displayed on the logged out share page?
     */
    private function verifyCanDisplayToLoggedOut($obj, $objid, $url, $title) {
    	if (empty($objid) && !empty($url) && !empty($title)) 
    		return;
    	if (! Index_SharingHelper::canDisplayToLoggedOut($obj)) {
            header('Location: ' . xg_absolute_url('/'));
            exit;
        }
    }

    /**
     *  Get relevant sharing information for the object with the specified ID.
     *
     *  IMPORTANT:  This function will show the user a 404 page if the object is not found.
     *
     * @param $id string
     */
    private function getItemInfo($id, $url = NULL, $title = NULL, $type = NULL) {
    	if (!is_null($url) && !is_null($title) && !is_null($type)) {
    		$itemInfo = Index_SharingHelper::getPageInfo($url, $title, $type);
    	} else {
        	$itemInfo = Index_SharingHelper::getItemInfo($id);
    	}
        if (! $itemInfo) {
            W_Cache::getWidget('main')->dispatch('error','404');
            exit;
        }
        return $itemInfo;
    }

    private function getItemArr($id, $url, $title, $type, $inarr = array()) {
    	if (!is_null($url) && !is_null($title) && !is_null($type)) {
    		$arr = array('url' => $url, 'title' => $title, 'type' => $type);
    	} else {
        	$arr = array('id' => $id);
    	}
    	return array_merge($arr, $inarr);
    }
}
