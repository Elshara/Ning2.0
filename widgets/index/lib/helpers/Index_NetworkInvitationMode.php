<?php
/**
 * Logic specific to network invitations.
 */
class Index_NetworkInvitationMode extends Index_InvitationMode {

    /**
     * Sends invitations to non-members and sends friend-requests to members.
     * Ignores banned members, pending members, and members who are already your friends.
     * Auto-friends; bypasses pending-friend limits.
     *
     * @param $contactList array  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param $message string  optional message for the invitation
     * @param $contentId string  not used
     */
    public function sendProper($contactList, $message, $contentId) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $profiles = XG_Cache::profiles(array_keys($this->keyByEmailAddress($contactList)));
        $screenNames = User::screenNames($profiles);
        $users = User::loadMultiple($screenNames);
        $friendUsers = XN_Query::create('Content')
                ->filter('owner')
                ->filter('type', '=', 'User')
                ->filter('title', 'in', $screenNames)
                ->filter('contributorName', 'in', XN_Query::FRIENDS())
                ->execute();
        list($invitationRecipients, $friendRequestRecipients) = $this->classifyRecipients(array(
                'contactList' => $this->keyByEmailAddress($contactList),
                'users' => $this->keyByEmailAddress($users),
                'friendUsers' => $this->keyByEmailAddress($friendUsers),
                'currentUserCanSendInvites' => XG_App::canSendInvites(XN_Profile::current())));
        foreach ($invitationRecipients as $emailAddress => $name) {
            $invitation = Index_InvitationHelper::createInvitation($emailAddress, $name, Index_InvitationHelper::NETWORK_INVITATION);
            $url = XG_HttpHelper::addParameters(xg_absolute_url('/'), array(Index_InvitationHelper::KEY => $invitation->id));
            $url = XG_HttpHelper::removeParameter($url, 'xg_browser');  // Remove iPhone parameter - BAZ-9928
            $messageObject = new XG_Message_Invitation(array(
                    'subject' => xg_text('COME_JOIN_ME_ON_X', XN_Application::load()->name),
                    'body' => $message,
                    'url' => $url));
            $this->addToQueue(array($messageObject, 'send'), array($emailAddress, XN_Profile::current()->screenName, false), $emailAddress);
        }
        foreach ($friendRequestRecipients as $emailAddress => $name) {
            $this->addToQueue(array($this, 'sendFriendRequest'), array($emailAddress), $emailAddress);
        }
    }

    /**
     * Organizes the recipients into those that should receive invitations and those that
     * should receive friend requests. All array arguments should be keyed by email address.
     *
     * @param array contactList  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param array users  User objects for the contactList, including pending and banned users
     * @param array friendUsers  those User objects representing friends of the current user
     * @param boolean currentUserCanSendInvites  whether the current user is allowed to send network invitations
     * @return array two arrays of names keyed by email address: invitation recipients and friend-request recipients
     */
    protected function classifyRecipients($args) {
        if (array_keys($args) != array('contactList', 'users', 'friendUsers', 'currentUserCanSendInvites')) { throw new Exception('Assertion failed (1442885021)'); }
        $invitationRecipients = $friendRequestRecipients = array();
        foreach ($args['contactList'] as $emailAddress => $contact) {
            $user = $args['users'][$emailAddress];
            if (User::isBanned($user)) { continue; }
            if (!$user && $args['currentUserCanSendInvites']) {
                $invitationRecipients[$emailAddress] = $contact['name'];
                continue;
            }
            if (!$user || $args['friendUsers'][$emailAddress]) { continue; }
            $friendRequestRecipients[$emailAddress] = $contact['name'];
        }
        return array($invitationRecipients, $friendRequestRecipients);
    }

}


