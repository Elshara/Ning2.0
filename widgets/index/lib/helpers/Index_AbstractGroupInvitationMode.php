<?php
/**
 * Logic specific to group invitations.
 */
abstract class Index_AbstractGroupInvitationMode extends Index_InvitationMode {

    /** The associated Group. */
    protected $group;

    /**
     * Constructor.
     *
     * @param $groupId string  the content ID of the associated Group
     */
    public function __construct($args) {
        $this->group = Group::load($args['groupId']);
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
    protected function createGroupInvitation($emailAddress, $name, $usersKeyedByEmailAddress, $invitationHelperClass = 'Index_InvitationHelper', $groupInvitationHelperClass = 'Groups_InvitationHelper') {
        if ($user = $usersKeyedByEmailAddress[$emailAddress]) {
            call_user_func(array($groupInvitationHelperClass, 'addGroupInviting'), $user, $this->group->id, XN_Profile::current()->screenName);
            $user->save();
        }
        return call_user_func(array($invitationHelperClass, 'createInvitation'), $emailAddress, $name, call_user_func(array($groupInvitationHelperClass, 'groupInvitationLabel'), $this->group->id));
    }
}

