<?php

/**
 * Dispatches requests pertaining to invitation requests.
 */
class Groups_InvitationrequestController extends XG_GroupEnabledController {

    /**
     * Requests an invitation to the specified group, for the current user.
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     *
     * Expected POST variables:
     *     name - the name specified in the invitation request
     *     emailAddress - the email address specified in the invitation request
     *     message - the optional text for the invitation request
     */
    public function action_create() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_ValidationHelper.php');
        XG_App::includeFileOnce('/lib/XG_Message.php');
        XG_HttpHelper::trimGetAndPostValues();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('show', 'group', array('id' => $_GET['groupId'])); return; }
        $group = Group::load($_GET['groupId']);
        if (! Groups_SecurityHelper::currentUserCanRequestInvitation($group)) { xg_echo_and_throw('Not allowed (530993861)'); }
        $errors = array();
        if (! $this->_user->isLoggedIn()) {
            if (! $_POST['name']) { $errors['name'] = xg_text('PLEASE_ENTER_NAME'); }
            if (! $_POST['emailAddress']) { $errors['emailAddress'] = xg_text('PLEASE_ENTER_EMAIL_ADDRESS'); }
            if ($_POST['emailAddress'] && ! XG_ValidationHelper::isValidEmailAddress($_POST['emailAddress'])) { $errors['emailAddress'] = xg_text('X_IS_NOT_VALID_EMAIL_ADDRESS', $_POST['emailAddress']); }
        }
        if (count($errors)) {
            $_GET['id'] = $group->id;
            GroupInvitationRequest::setValidationErrors($errors);
            $this->forwardTo('show', 'group');
            return;
        }
        $name = $this->_user->isLoggedIn()  ? xg_username($this->_user) : mb_substr($_POST['name'], 0, GroupInvitationRequest::MAX_NAME_LENGTH);
        $usernameOrEmailAddress = $this->_user->isLoggedIn() ? $this->_user->screenName : mb_substr($_POST['emailAddress'], 0, GroupInvitationRequest::MAX_EMAIL_ADDRESS_LENGTH);
        $message = mb_substr($_POST['message'], 0, GroupInvitationRequest::MAX_MESSAGE_LENGTH);
        GroupInvitationRequest::loadOrCreate($group, $usernameOrEmailAddress)->save();
        XG_Message_Request_Group_Invitation::create()->send($group, $name, $usernameOrEmailAddress, $message);
        $this->redirectTo('show', 'group', array('id' => $group->id, 'invitationRequestSent' => 'yes'));
    }

}
