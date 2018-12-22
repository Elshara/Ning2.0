<?php
/**
 * Logic specific to network share messages.
 */
class Index_NetworkShareMode extends Index_InvitationMode {

    /**
	 * Constructor. Receives all actual $_POST parameters as an input.
	 * You can check all available POST variables in InvitationController::action_send().
     *
	 * @param	$args		hash		Actual $_POST parameters passed to the action_send() method.
     */
    public function __construct($args) {
        $this->rawArgs = $args;
    }

    /**
     * Sends share message to members; invites if the network requires sign-in.
     * Ignores banned members.
     *
     * @param $contactList array  an array of contacts, each an array with keys "name" and "emailAddress"
     * @param $message string  optional message for the invitation
     * @param $contentId string  ID of the content object being shared
     */
    public function sendProper($contactList, $message, $contentId) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_SharingHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $profiles = XG_Cache::profiles(array_keys($this->keyByEmailAddress($contactList)));
        $screenNames = User::screenNames($profiles);
        $users = User::loadMultiple($screenNames);
        list($invitationRecipients, $linkRecipients) = $this->classifyRecipients(array(
                'contactList' => $this->keyByEmailAddress($contactList),
                'users' => $this->keyByEmailAddress($users),
                'currentUserCanSendInvites' => XG_App::canSendInvites(XN_Profile::current()),
                'everythingIsVisible' => XG_App::everythingIsVisible()));
        $itemInfo = Index_SharingHelper::getItemInfoFromIdOrRaw($contentId, $this->rawArgs);
        $shareUrl = XG_HttpHelper::addParameters($itemInfo['share_url'], array(Index_InvitationHelper::SHARING => 1));
        foreach ($invitationRecipients as $emailAddress => $name) {
            $invitation = Index_InvitationHelper::createInvitation($emailAddress, $name, Index_InvitationHelper::NETWORK_INVITATION);
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
     * @param boolean currentUserCanSendInvites  whether the current user is allowed to send network invitations
     * @param boolean everythingIsVisible  whether the network is public and the visibility setting is "everything"
     * @return array two arrays of names keyed by email address: invitation recipients and link recipients
     */
    protected function classifyRecipients($args) {
        if (array_keys($args) != array('contactList', 'users', 'currentUserCanSendInvites', 'everythingIsVisible')) { throw new Exception('Assertion failed (118271325)'); }
        $invitationRecipients = $linkRecipients = array();
        foreach ($args['contactList'] as $emailAddress => $contact) {
            $user = $args['users'][$emailAddress];
            if ($args['everythingIsVisible'] || User::isMember($user) || User::isPending($user)) {
                $linkRecipients[$emailAddress] = $contact['name'];
                continue;
            }
            if (User::isBanned($user)) { continue; }
            if ($args['currentUserCanSendInvites']) {
                $invitationRecipients[$emailAddress] = $contact['name'];
                continue;
            }
        }
        return array($invitationRecipients, $linkRecipients);
    }

}
