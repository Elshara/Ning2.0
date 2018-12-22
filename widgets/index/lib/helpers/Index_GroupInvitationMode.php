<?php
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AbstractGroupInvitationMode.php');

/**
 * Logic specific to group invitations.
 */
class Index_GroupInvitationMode extends Index_AbstractGroupInvitationMode {

    /**
     * Sends invitations to members and non-group-members.
     * Ignores banned network members and banned group members.
     *
     * @param $contactList array  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param $message string  optional message for the invitation
     * @param $contentId string  not used
     */
    public function sendProper($contactList, $message, $contentId) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
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
        $groupMemberships = XN_Query::create('Content')
                ->filter('owner')
                ->filter('type', '=', 'GroupMembership')
                ->filter('my.username', 'in', $screenNames)
                ->filter('my.groupId', '=', $this->group->id)
                ->execute();
        $invitationRecipients = $this->classifyRecipients(array(
                'contactList' => $this->keyByEmailAddress($contactList),
                'users' => $usersKeyedByEmailAddress = $this->keyByEmailAddress($users),
                'friendUsers' => $this->keyByEmailAddress($friendUsers),
                'groupMemberships' => $this->keyByEmailAddress($groupMemberships),
                'currentUserCanSendGroupInvites' => Groups_SecurityHelper::currentUserCanSendInvites($this->group)));
        foreach ($invitationRecipients as $emailAddress => $name) {
            $invitation =  $this->createGroupInvitation($emailAddress, $name, $usersKeyedByEmailAddress);
            $messageObject = new XG_Message_Group_Invitation(array(
                    'subject' => xg_text('COME_JOIN_ME_ON_X_ON_Y', $this->group->title, XN_Application::load()->name),
                    'body' => $message,
                    'url' => XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $this->group->id, Index_InvitationHelper::KEY => $invitation->id))));
            $this->addToQueue(array($messageObject, 'send'), array($emailAddress, XN_Profile::current()->screenName, $this->group), $emailAddress);
        }
    }

    /**
     * Returns an array of users to send inviations to, keyed by email address.
     *
     * @param array contactList  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param array users  User objects for the contactList, including pending and banned users
     * @param array friendUsers  those User objects representing friends of the current user
     * @param array groupMemberships  GroupMembership objects for the contactList, including users who are pending and who are banned from the network or group
     * @param boolean currentUserCanSendGroupInvites  whether the current user is allowed to send group invitations
     * @return array an array of names keyed by email address
     */
    protected function classifyRecipients($args) {
        if (array_keys($args) != array('contactList', 'users', 'friendUsers', 'groupMemberships', 'currentUserCanSendGroupInvites')) { throw new Exception('Assertion failed (181215778)'); }
        $invitationRecipients = $friendRequestRecipients = array();
        foreach ($args['contactList'] as $emailAddress => $contact) {
            $user = $args['users'][$emailAddress];
            if (User::isBanned($user)) { continue; }
            $groupMembership = $args['groupMemberships'][$emailAddress];
            if ($groupMembership && $groupMembership->my->status == 'banned') { continue; }
            if ((!$groupMembership || $groupMembership->my->status == 'nonmember') && $args['currentUserCanSendGroupInvites']) {
                $invitationRecipients[$emailAddress] = $contact['name'];
                continue;
            }
            // BAZ-5785; send group invite even if the invitee is already a member
            if ($groupMembership && $args['currentUserCanSendGroupInvites']) {
                $invitationRecipients[$emailAddress] = $contact['name'];
                continue;
            }
            if (!$user || $args['friendUsers'][$emailAddress]) { continue; }
        }
        return $invitationRecipients;
    }

}


