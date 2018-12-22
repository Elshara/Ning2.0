<?php

/**
 * Useful functions for authorizing access to pages and other resources.
 */
class Groups_SecurityHelper {

    /**
     * Returns whether "Create a New Group" links and buttons should be visible to the current user,
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSeeCreateGroupLinks() {
        // Initially set by hand in widget-configuration.xml (BAZ-3945) [Jon Aquino 2007-08-16]
        // now moved into the main config
        if (W_Cache::getWidget('groups')->config['onlyAdminsCanCreateGroups'] == 'yes') { return XG_SecurityHelper::userIsAdmin();}
        if (W_Cache::getWidget('main')->config['onlyAdminsCanCreateGroups'] != 'yes') {return true;} 
        return XG_SecurityHelper::userIsAdmin(); 
    }

    /**
     * Returns whether the current user is allowed to create a group
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanCreateGroup() {
        return XN_Profile::current()->isLoggedIn() && self::currentUserCanSeeCreateGroupLinks();
    }

    /**
     * Returns whether the current user can access the Member Management pages
     * for the given group
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanEditMemberships($group) {
        // allow site admin/NC to edit membership (BAZ-6087) [ywh 2008-08-21]
        return Group::userIsAdmin($group) || XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns whether the current user is allowed to send a message to the group
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSendMessageToGroup($group) {
        return XG_SecurityHelper::userIsAdmin() || ($group->my->allowMemberMessaging == 'Y' ? Group::userIsMember($group) : Group::userIsAdmin($group));
    }

    /**
     * Returns whether the "Promote to Administrator" button should be visible to the current user.
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSeePromoteToAdministratorButton($group) {
        return Group::userIsCreator($group) || XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns whether the current user is allowed to promote the person to admin level
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @param $other string  The username of the other person
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanPromoteToAdministrator($group, $other) {
        if (! self::currentUserCanSeePromoteToAdministratorButton($group)) { return false; }
        if (! Group::userIsMember($group, $other)) { return false; }
        if (Group::userIsAdmin($group, $other)) { return false; }
        return true;
    }

    /**
     * Returns whether the "Demote from Administrator" button should be visible to the current user.
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSeeDemoteFromAdministratorButton($group) {
        return Group::userIsCreator($group) || XG_SecurityHelper::userIsAdmin();
    }
    
    /**
     * Returns whether the current user can see the admin box on a group detail page
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSeeAdminControls($group) {
        return Group::userIsAdmin($group) || XG_SecurityHelper::userIsAdmin();
    }
    

    /**
     * Returns whether the current user is allowed to demote the person from admin level
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @param $other string  The username of the other person
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDemoteFromAdministrator($group, $other) {
        if (! self::currentUserCanSeePromoteToAdministratorButton($group)) { return false; }
        if (Group::userIsCreator($group, $other)) { return false; }
        if (! Group::userIsAdmin($group, $other)) { return false; }
        return true;
    }

    /**
     * Returns whether the current user is allowed to ban the person
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @param $other string  The username of the other person
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanBan($group, $other) {
        if (! Group::userIsAdmin($group) && ! XG_SecurityHelper::userIsAdmin()) { return false; }
        if (Group::userIsCreator($group, $other)) { return false; }
        if (Group::userIsAdmin($group, $other) && ! Group::userIsCreator($group) && ! XG_SecurityHelper::userIsAdmin()) { return false; }
        return true;
    }

    /**
     * Returns whether the current user is allowed to request an invitation to the group.
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanRequestInvitation($group) {
        if (Group::userIsBanned($group)) { return false; }
        if ($group->my->allowInvitationRequests == 'N') { return false; }
        if (! XN_Profile::current()->isLoggedIn()) { return true; }
        return ! Group::userIsBanned($group) && ! Group::userIsMember($group);
        // If you are invited, you can still request an invitation (e.g., the invitation may have been
        // trapped in your spam filter) [Jon Aquino 2007-04-26]
    }

    /**
     * Returns whether the current user is allowed to leave the group
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanLeaveGroup($group) {
        return Group::userIsMember($group) && ! Group::userIsCreator($group);
    }

    /**
     * Returns whether the current user is allowed to delete the group
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeleteGroup($group) {
        // Check if creator is a member, as she may have been banned from the network (deleting her membership) then restored.
        // Otherwise she will have two links - Invite More and Delete Group - which seem incongruous. [Jon Aquino 07-05-12]
        return (Group::userIsCreator($group) && Group::userIsAdmin($group)) || XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns true if the supplied user can modify the embed settings,
     *   or if the current user can modify the embed settings if no user
     *   is supplied
     *
     * @param $embed XG_Embed  embed instance
     *
     * @return boolean  can the user modify the embed settings?
     */
    public static function currentUserCanEditGroupEmbed($embed) {
        if (is_object($embed)) {
            return $embed->isOwnedByCurrentUser() || (XG_GroupHelper::inGroupContext() && XG_SecurityHelper::userIsAdmin());
        }
        return false;
    }

    /**
     * Returns whether the current user is allowed to edit the group.
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanEditGroup($group) {
        // allow site admin/NC to edit group (BAZ-6087) [ywh 2008-08-21]
        return Group::userIsAdmin($group) || XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns whether Share This links and buttons should be visible to the current user,
     * for the specified group. Differs from currentUserCanShare; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * group (until they sign in).
     *
     * @param XN_Content|W_Content group the Group to share
     * @return whether to show Share This buttons for the group
     * @see currentUserCanShare
     */
    public static function currentUserCanSeeShareLinks($group) {
        // Allow signed-out people to see the Share This link [Jon Aquino 2006-12-20]
        // An invite key is included in the Share This email  [Jon Aquino 2006-10-24]
        if (Group::isPrivate($group) && ! self::currentUserCanSeeInviteLinks($group)) { return false; }
        return ! XN_Profile::current()->isLoggedIn() || XG_App::canSendInvites(XN_Profile::current());
    }

    /**
     * Returns whether the current user can in fact share the specified group.
     * Differs from currentUserCanSeeShareLinks; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * group (until they sign in).
     *
     * @param XN_Content|W_Content group the Group to share
     * @return whether the current user is allowed to share the group
     * @see currentUserCanSeeShareLinks
     */
    public static function currentUserCanShare($group) {
        if (Group::isPrivate($group) && ! self::currentUserCanSendInvites($group)) { return false; }
        return XN_Profile::current()->isLoggedIn() && self::currentUserCanSeeShareLinks($group);
    }

    /**
     * Returns whether Invite links and buttons should be visible to the current user,
     * for the specified group. Differs from currentUserCanSendInvites; for example, a
     * signed-out user can see the Invite link but cannot in fact invite someone to the
     * group (until they sign in).
     *
     * @param XN_Content|W_Content group the Group to invite people to
     * @return whether to show Invite buttons
     * @see currentUserCanSendInvites
     */
    public static function currentUserCanSeeInviteLinks($group) {
        if (! XG_App::canSendInvites(XN_Profile::current())) { return false; }
        if ($group->my->approved == 'N') {return false; }
        if (Group::userIsAdmin($group)) { return true; }
        return Group::userIsMember($group) && $group->my->allowInvitations == 'Y';
    }

    /**
     * Returns whether the current user can in fact invite someone to the specified group.
     * Differs from currentUserCanSeeInviteLinks; for example, a
     * signed-out user can see the Invite link but cannot in fact invite someone to the
     * group (until they sign in).
     *
     * @param XN_Content|W_Content group the Group to invite people to
     * @return whether the current user is allowed to invite people to the group
     * @see currentUserCanSeeInviteLinks
     */
    public static function currentUserCanSendInvites($group) {
        return self::currentUserCanSeeInviteLinks($group);
    }

    /**
     * Returns whether Join links and buttons should be visible to the current user,
     * for the specified group. Differs from currentUserCanJoin; for example, a
     * signed-out user can see the Join link but cannot in fact join the
     * group (until they sign in).
     *
     * @param XN_Content|W_Content group the Group to join
     * @return whether to show Join buttons
     * @see currentUserCanJoin
     */
    public static function currentUserCanSeeJoinLinks($group) {
        // Don't show Join Group box if Join Network box is shown (BAZ-2891) [Jon Aquino 2007-05-11]
        if (! User::isMember(XN_Profile::current())) { return false; }
        // Don't allow signed-out users to see Join Group link; otherwise some pages will
        // have both Join Group and Join Bazel links, which is confusing [Jon Aquino 2007-04-20]
        return self::currentUserCanJoin($group);
    }

    /**
     * Returns whether the current user can in fact join the specified group.
     * Differs from currentUserCanSeeJoinLinks; for example, a
     * signed-out user can see the Join link but cannot in fact join the
     * group (until they sign in).
     *
     * @param XN_Content|W_Content group the Group to join
     * @return whether the current user is allowed to join the group
     * @see currentUserCanSeeJoinLinks
     */
    public static function currentUserCanJoin($group) {
        return self::userCanJoin($group);
    }

    /**
     * Returns whether the user can join the specified group.
     * per BAZ-6835 network admins can join private groups
     *
     * @param XN_Content|W_Content group the Group to join
     * @param $username string  the screen name of the person, or null to specify the current user
     * @return whether the current user is allowed to join the group
     */
    public static function userCanJoin($group, $username = null) {
        if (! $username && ! XN_Profile::current()->isLoggedIn()) { return false; }
        if ($group->my->approved == 'N') {return false;}
        if (XG_SecurityHelper::userIsAdmin() && !Group::userIsMember($group, $username)) {return true;}
        if (Group::userIsBanned($group, $username) || Group::userIsMember($group, $username)) { return false; }
        if (Group::isPrivate($group)) { return Group::userIsInvited($group, $username); }
        return true;
    }

    /**
     * Returns whether the current user is allowed to view the group's pages
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @param $forceTrueForNetworkAdmins boolean  automatically allow network admins access
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanAccessGroup($group, $forceTrueForNetworkAdmins = true) {
        if (! $forceTrueForNetworkAdmins) {
            return Group::userIsAdmin($group) || Group::isPublic($group) || Group::userIsMember($group);  
        } else {
            return XG_SecurityHelper::userIsAdmin() || Group::userIsAdmin($group) || Group::isPublic($group) || Group::userIsMember($group);  
        }
    }
    
    /**
     * Whether the current user is allowed to view an unapproved group
     *
     * @param $group  XN_Content|W_Content  The Group object
     * @return boolean
     */
    public static function currentUserCanAccessUnapprovedGroup($group) {
        if (Group::userIsAdmin($group) || XG_SecurityHelper::userIsAdmin()) {
            return true;
        }
        return false;
    }
    
    /**
     * A wrapper for XG_App::groupsAreModerated() that determines whether moderation should be applied for the current user
     * Used to allow admins to bypass moderation rules.
     *
     * @return boolean
     */
    public static function moderatedForThisUser() {
        if (XG_SecurityHelper::userIsAdmin()) {
            return false;
        } else {
            return XG_App::groupsAreModerated();
        }
    }
    
    /**
     * @param $group  XN_Content|W_Content  The Group object
     * @param $screenName  string  The screenName of the user we're checking for requests
     * 
     * @return boolean
     */
    public static function currentUserHasRequestedMembership($group, $screenName) {
        $query = XN_Query::create('Content')->begin(0)->end(1)->filter('my.groupId', '=', $group->id)->filter('owner')->filter('my.mozzle', '=', 'groups')->filter('type','=','GroupInvitationRequest')->filter('my->requestor', '=', $screenName);
        $requests = $query->execute();
        return count($requests) == 1;
    }    
    

}

