<?php
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AbstractGroupInvitationMode.php');

/**
 * Logic specific to group share messages.
 */
class Index_GroupShareMode extends Index_AbstractGroupInvitationMode {

    /**
     * Sends share message to group members; invites if the network requires sign-in or the group requires joining.
     * Ignores banned members and banned group members.
     *
     * @param $contactList array  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param $message string  optional message for the invitation
     * @param $contentId string  ID of the content object being shared
     */
    public function sendProper($contactList, $message, $contentId) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_SharingHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $profiles = XG_Cache::profiles(array_keys($this->keyByEmailAddress($contactList)));
        $screenNames = User::screenNames($profiles);
        $users = User::loadMultiple($screenNames);
        $groupMemberships = XN_Query::create('Content')
                ->filter('owner')
                ->filter('type', '=', 'GroupMembership')
                ->filter('my.username', 'in', $screenNames)
                ->filter('my.groupId', '=', $this->group->id)
                ->execute();
        list($invitationRecipients, $linkRecipients) = $this->classifyRecipients(array(
                'contactList' => $this->keyByEmailAddress($contactList),
                'users' => $usersKeyedByEmailAddress = $this->keyByEmailAddress($users),
                'groupMemberships' => $this->keyByEmailAddress($groupMemberships),
                'currentUserCanSendGroupInvites' => Groups_SecurityHelper::currentUserCanSendInvites($this->group),
                'groupIsPublic' => Group::isPublic($this->group),
                'everythingIsVisible' => XG_App::everythingIsVisible()));
        $itemInfo = Index_SharingHelper::getItemInfo($contentId);
        $shareUrl = XG_HttpHelper::addParameters($itemInfo['share_url'], array(Index_InvitationHelper::SHARING => 1));
        foreach ($invitationRecipients as $emailAddress => $name) {
            $invitation =  $this->createGroupInvitation($emailAddress, $name, $usersKeyedByEmailAddress);
            $itemInfo['share_url'] = XG_HttpHelper::addParameters($shareUrl, array(Index_InvitationHelper::KEY => $invitation->id));
            $messageObject = Index_SharingHelper::createMessage($itemInfo, $message);
            $this->addToQueue(array($messageObject, 'send'), array($emailAddress, XN_Profile::current()->screenName), $emailAddress);
        }
        foreach ($linkRecipients as $emailAddress => $name) {
            $itemInfo['share_url'] = $shareUrl;
            $messageObject = Index_SharingHelper::createMessage($itemInfo, $message);
            $this->addToQueue(array($messageObject, 'send'), array($emailAddress, XN_Profile::current()->screenName), $emailAddress);
        }
    }

    /**
     * Organizes the recipients into those that should receive invitations and those that
     * should receive friend requests. All array arguments should be keyed by email address.
     *
     * @param array contactList  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param array users  User objects for the contactList, including pending and banned users
     * @param array groupMemberships  GroupMembership objects for the contactList, including users who are pending and who are banned from the network or group
     * @param boolean currentUserCanSendGroupInvites  whether the current user is allowed to send group invitations
     * @param boolean groupIsPublic  whether the group is accessible to the public
     * @param boolean everythingIsVisible  whether the network is public and the visibility setting is "everything"
     * @return array two arrays of names keyed by email address: invitation recipients and link recipients
     */
    protected function classifyRecipients($args) {
        if (array_keys($args) != array('contactList', 'users', 'groupMemberships', 'currentUserCanSendGroupInvites', 'groupIsPublic', 'everythingIsVisible')) { throw new Exception('Assertion failed (258258477)'); }
        $invitationRecipients = $linkRecipients = array();
        foreach ($args['contactList'] as $emailAddress => $contact) {
            $user = $args['users'][$emailAddress];
            if ($args['groupIsPublic'] && $args['everythingIsVisible']) {
                $linkRecipients[$emailAddress] = $contact['name'];
                continue;
            }
            if (User::isBanned($user)) { continue; }
            $groupMembership = $args['groupMemberships'][$emailAddress];
            if ($groupMembership && $groupMembership->my->status == 'banned') { continue; }
            if ($args['groupIsPublic'] && (User::isMember($user) || User::isPending($user))) {
                $linkRecipients[$emailAddress] = $contact['name'];
                continue;
            }
            if ($groupMembership && GroupMembership::isMember($groupMembership)) {
                $linkRecipients[$emailAddress] = $contact['name'];
                continue;
            }
            if ($args['currentUserCanSendGroupInvites']) {
                $invitationRecipients[$emailAddress] = $contact['name'];
                continue;
            }
        }
        return array($invitationRecipients, $linkRecipients);
    }

}
