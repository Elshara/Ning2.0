<?php

/**
 * Dispatches requests pertaining to invitations.
 */
class Groups_InvitationController extends XG_GroupEnabledController {

    /**
     * Runs code before each action.
     */
    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationFormHelper.php');
        XG_HttpHelper::trimGetAndPostValues();
    }

    /**
     * Displays a form for sending invitations.
     *
     * Expected GET variables:
     *     sent - whether invitations were just sent
     *     noAddressesFound - whether the address import found 0 addresses
     *     creatingGroup - whether the group has just been created
     *
     * @param $formToOpen string  (optional) which form to open: enterEmailAddresses, inviteFriends, webAddressBook, or emailApplication
     * @param $errors array  (optional) HTML error messages, optionally with keys field name
     */
    public function action_new($formToOpen = 'enterEmailAddresses', $errors = array()) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        XG_SecurityHelper::redirectIfNotMember();
        $this->group = XG_GroupHelper::currentGroup();
        if (! Groups_SecurityHelper::currentUserCanSendInvites($this->group)) { return $this->redirectTo(xg_absolute_url('/')); }
        $this->showInvitationsSentMessage = $_GET['sent'];
        $this->showNoAddressesFoundMessage = $_GET['noAddressesFound'];
        $this->creatingGroup = $_GET['creatingGroup'];
        $numFriendsAcrossNing = Index_MessageHelper::numberOfFriendsAcrossNing($this->_user->screenName);
        $numFriendsOnNetwork = Index_MessageHelper::numberOfFriendsOnNetwork($this->_user->screenName);
        $numFriendsInGroup = Index_MessageHelper::numberOfFriendsInGroup($this->_user->screenName, XG_GroupHelper::currentGroupId());
        $this->invitationArgs = array(
                'formToOpen' => $formToOpen,
                'errors' => $errors,
                'createUrl' => $this->_buildUrl('invitation', 'create', array('groupId' => XG_GroupHelper::currentGroupId(), 'creatingGroup' => $this->creatingGroup)),
                'enterEmailAddressesButtonText' => xg_text('SEND_INVITATIONS'),
                'inviteFriendsTitle' => xg_text('INVITE_FRIENDS'),
                'inviteFriendsDescription' => xg_text('INVITE_YOUR_FRIENDS_TO_GROUPNAME', XG_GroupHelper::currentGroup()->title),
                'friendDataUrl' => $this->_buildUrl('invitation', 'friendData', array('xn_out' => 'json', 'groupId' => XG_GroupHelper::currentGroupId())),
                'initialFriendSet' => Index_MessageHelper::ALL_FRIENDS,
                'numFriends' => $numFriendsAcrossNing,
                'numSelectableFriends' => $numFriendsAcrossNing - $numFriendsInGroup,
                'numSelectableFriendsOnNetwork' => $numFriendsOnNetwork - $numFriendsInGroup,
                'showSelectAllFriendsLink' => TRUE,
                'showSelectFriendsOnNetworkLink' => TRUE,
                'messageParts' => Groups_InvitationHelper::getMessageParts($this->group));
    }

    /**
     * Processes the form for sending invitations.
     *
     * Expected GET variables:
     *     creatingGroup - whether the group has just been created
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
        if (! Groups_SecurityHelper::currentUserCanSendInvites(XG_GroupHelper::currentGroup())) { return $this->redirectTo(xg_absolute_url('/')); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('new', 'invitation', array('groupId' => XG_GroupHelper::currentGroupId())); }
        switch ($_POST['form']) {

            case 'enterEmailAddresses':
                $result = Index_InvitationFormHelper::processEnterEmailAddressesForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('enterEmailAddresses', array($result['errorHtml']))); }
                Index_InvitationFormHelper::send(array(
                        'inviteOrShare' => 'invite',
                        'groupId' => XG_GroupHelper::currentGroupId(),
                        'contactList' => $result['contactList'],
                        'message' => $_POST['message']));
                if ($_GET['creatingGroup']) {
                    $this->redirectTo('show', 'group', array('id' =>XG_GroupHelper::currentGroupId()));
                } else {
                    $this->redirectTo('new', 'invitation', array('sent' => 1, 'groupId' => XG_GroupHelper::currentGroupId()));
                }
                break;

            case 'inviteFriends':
                $result = Index_InvitationFormHelper::processInviteFriendsForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('inviteFriends', array($result['errorHtml']))); }
                Index_InvitationFormHelper::send(array(
                        'inviteOrShare' => 'invite',
                        'groupId' => XG_GroupHelper::currentGroupId(),
                        'friendSet' => $result['friendSet'],
                        'contactList' => $result['contactList'],
                        'screenNamesExcluded' => $result['screenNamesExcluded'],
                        'message' => $_POST['inviteFriendsMessage']));
                if ($_GET['creatingGroup']) {
                    $this->redirectTo('show', 'group', array('id' =>XG_GroupHelper::currentGroupId()));
                } else {
                    $this->redirectTo('new', 'invitation', array('sent' => 1, 'groupId' => XG_GroupHelper::currentGroupId()));
                }
                break;

            case 'webAddressBook':
                $result = Index_InvitationFormHelper::processWebAddressBookForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('webAddressBook', array($result['errorHtml']))); }
                if ($_GET['creatingGroup']) {
                    $result['target'] = XG_HttpHelper::addParameter($result['target'],'creatingGroup',1);
                }
                $this->redirectTo($result['target']);
                break;

            case 'emailApplication':
                $result = Index_InvitationFormHelper::processEmailApplicationForm();
                if ($result['errorHtml']) { return $this->forwardTo('new', 'invitation', array('emailApplication', array($result['errorHtml']))); }
                if ($_GET['creatingGroup']) {
                    $result['target'] = XG_HttpHelper::addParameter($result['target'],'creatingGroup',1);
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
     *     creatingGroup - whether the group has just been created
     */
    public function action_editContactList() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! Groups_SecurityHelper::currentUserCanSendInvites(XG_GroupHelper::currentGroup())) { return $this->redirectTo(xg_absolute_url('/')); }
        if (! unserialize(ContactList::load($_GET['contactListId'])->my->contacts)) { return $this->redirectTo('new', 'invitation', array('noAddressesFound' => 1, 'groupId' => XG_GroupHelper::currentGroupId())); }
        $this->invitationArgs = array(
                'contactListId' => $_GET['contactListId'],
                'createWithContactListUrl' => $this->_buildUrl('invitation', 'createWithContactList', array('contactListId' => $_GET['contactListId'], 'groupId' => XG_GroupHelper::currentGroupId(), 'creatingGroup' => $_GET['creatingGroup'])),
                'cancelUrl' => $this->_buildUrl('invitation', 'new', array('groupId' => XG_GroupHelper::currentGroupId())),
                'inviteOrShare' => 'invite',
                'searchLabelText' => xg_text('SEARCH_FRIENDS_TO_INVITE'),
                'messageParts' => Groups_InvitationHelper::getMessageParts(XG_GroupHelper::currentGroup()),
                'submitButtonText' => xg_text('INVITE'));
    }

    /**
     * Processes the Contact List form.
     *
     * Expected GET variables:
     *     contactListId - content ID of a ContactList object
     *     creatingGroup - whether the group has just been created
     *
     * Expected POST variables:
     *     contactListJson - a JSON array of contacts, each being an array with keys "name" and "emailAddress"
     *     message - optional message for the invitation
     */
    public function action_createWithContactList() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! Groups_SecurityHelper::currentUserCanSendInvites(XG_GroupHelper::currentGroup())) { return $this->redirectTo(xg_absolute_url('/')); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('new', 'invitation', array('groupId' => XG_GroupHelper::currentGroupId())); }
        Index_InvitationFormHelper::processContactListForm('invite', XG_GroupHelper::currentGroupId());
        if ($_GET['creatingGroup']) {
            $this->redirectTo('show', 'group', array('id' =>XG_GroupHelper::currentGroupId()));
        } else {
            $this->redirectTo('new', 'invitation', array('sent' => 1, 'groupId' => XG_GroupHelper::currentGroupId()));
        }
    }


    /**
     * Declines the invitation to the specified group.
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     *     xn_out - set to "json" if this is an Ajax call
     *
     * @see Groups_GroupController::action_leave
     */
    public function action_delete() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (! $this->_user->isLoggedIn()) { xg_echo_and_throw('Not signed in (878075988)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { xg_echo_and_throw('Not a POST (1129774302)'); }
        $group = XG_GroupHelper::currentGroup();
        if (! Groups_SecurityHelper::currentUserCanJoin($group)) { xg_echo_and_throw('Not allowed (1956493161)'); }
        if (! Group::userIsInvited($group)) { xg_echo_and_throw('Not invited (1410696845)'); }
        Group::setStatus($group, $this->_user->screenName, 'nonmember');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        Groups_InvitationHelper::setGroupInvitationStatus($group, $this->_user->email, 'declined');
        if ($_GET['xn_out'] != 'json') {
            $this->redirectTo('show', 'group', array('id' => $group->id));
            return;
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
        $this->_widget->includeFileOnce('/lib/helpers/Groups_GroupMembershipFilter.php');
        $friendData = Index_MessageHelper::dataForFriendsAcrossNing($_GET['start'], $_GET['end']);
        $this->friends = $friendData['friends'];
        $groupMemberScreenNames = array();
        foreach (array_chunk($friendData['screenNames'], 100) as $friendScreenNames) {
            $groupMemberScreenNames += User::screenNames(Groups_GroupMembershipFilter::get('mostRecent')->execute(XN_Query::create('Content')->filter('contributorName', 'in', $friendScreenNames), XG_GroupHelper::currentGroupId()));
        }
        $n = count($this->friends);
        for ($i = 0; $i < $n; $i++) {
            if ($groupMemberScreenNames[$this->friends[$i]['screenName']]) {
                $this->friends[$i]['reasonToDisable'] = xg_text('ALREADY_MEMBER_OF_GROUP');
            }
            if (User::isBanned($this->friends[$i]['screenName'])) {
                $this->friends[$i]['reasonToDisable'] = xg_text('BANNED_FROM_NETWORK');
            }
        }
    }

}
