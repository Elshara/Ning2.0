<?php

/**
 * Useful functions for prompting the user to join the network (or the current group) when saving or deleting content.
 *
 * @see "How To Prompt the User to Join", Ning internal wiki
 */
class XG_JoinPromptHelper {

    /**
     * Returns whether to prompt the user to join the network (or the current group) when saving content
     *
     * @param $goesToAnotherPage string  (output) whether choosing to join will take the user to another page (sign-up or profile questions)
     * @param $ignoreGroupContext boolean  whether to skip the check to see if the current user is a member of the current group
     * @return string  text for the prompt, or an empty string if we shouldn't prompt
     */
    public static function promptToJoinOnSave(&$goesToAnotherPage = null, $ignoreGroupContext = false) {
        $goesToAnotherPage = ! User::isMember(XN_Profile::current());

        // Conditional logic is similar to promptToJoin($url) [Jon Aquino 2007-05-12]
        $groupToJoin = XG_GroupHelper::inGroupContext() && ! $ignoreGroupContext && ! XG_GroupHelper::userIsMember() ? XG_GroupHelper::currentGroup() : null;
        if ($groupToJoin) { return xg_text('LIKE_WHAT_YOU_SEE_GROUP', XG_GroupHelper::currentGroup()->title); }
        if (! User::isMember(XN_Profile::current())) { return xg_text('LIKE_WHAT_YOU_SEE', XN_Application::load()->name); }
        return '';
    }

    /**
     * Returns attributes for adding prompt-to-join behavior to an anchor tag (link),
     * if the current user is not a member of Ning, the network, or the current group. Suitable for links to pages
     * for saving content, but not deleting content (it uses promptToJoinOnSave, not promptToJoinOnDelete).
     *
     * @param $url string  URL for the anchor tag
     * @param $ignoreGroupContext boolean  whether to skip the check to see if the current user is a member of the current group
     * @return string  attributes for an <a> tag - just the href attribute if the current user is a member of Ning, the network, and the current group
     */
    public static function promptToJoin($url, $ignoreGroupContext = false) {
        $groupToJoin = XG_GroupHelper::inGroupContext() && ! $ignoreGroupContext && ! XG_GroupHelper::userIsMember() ? XG_GroupHelper::currentGroup() : null;
		$isMember = User::isMember(XN_Profile::current());

		if (!$isMember || $groupToJoin) {
	       	XG_App::ningLoaderRequire('xg.shared.PromptToJoinLink');
        	$promptAttributes = 'dojoType="PromptToJoinLink" _joinPromptText="' . xnhtmlentities(self::promptToJoinOnSave($goesToAnotherPage, $ignoreGroupContext)) . '"';
        	$promptAttributes .= self::promptAttributesForPending();
			// Conditional logic is similar to promptToJoinOnSave() [Jon Aquino 2007-05-12]
			if (!$isMember) {
        		return 'href="' . xnhtmlentities(XG_AuthorizationHelper::signUpUrl($url, XG_GroupHelper::currentGroup())) . '" ' . $promptAttributes;
			}
        	if ($groupToJoin) {
	        	return 'href="' . xnhtmlentities($url) . '" ' . $promptAttributes;
			}
		}
        return 'href="' . xnhtmlentities($url) . '"';
    }

    /**
     * Returns attributes for adding prompt-to-join behavior to a <button> element,
     * if the current user is not a member of Ning, the network, or the current group. Suitable for links to pages
     * for saving content, but not deleting content (it uses promptToJoinOnSave, not promptToJoinOnDelete).
     *
     * @param $url string  URL for the anchor tag
     * @param $ignoreGroupContext boolean  whether to skip the check to see if the current user is a member of the current group
     * @return string  attributes for a <button> tag
     */
    public static function promptToJoinButton($url, $ignoreGroupContext = false) {
        XG_App::ningLoaderRequire('xg.shared.PromptToJoinButton');
        $promptAttributes = 'dojoType="PromptToJoinButton" _joinPromptText="' . xnhtmlentities(self::promptToJoinOnSave($goesToAnotherPage, $ignoreGroupContext)) . '"';
        $promptAttributes .= self::promptAttributesForPending();
        return '_url="' . xnhtmlentities($url) . '" ' . $promptAttributes;
    }

    /**
     * Adjust PromptToJoinLink/Button attributes based on whether the current
     * user is a pending member
     */
    public static function promptAttributesForPending() {
        $curUser = XN_Profile::current();
        if ($curUser->isLoggedIn() && User::isPending($curUser)) {
            return ' _isPending="1"';
        }
        else {
            return '';
        }
    }

    /**
     * Returns whether to prompt the user to join the network (or the current group) when deleting content
     *
     * @return string  text for the prompt, or an empty string if we shouldn't prompt
     */
    public static function promptToJoinOnDelete() {
        if (XG_SecurityHelper::userIsAdmin()) { return ''; }  // Network admins don't have to join group to delete content [Jon Aquino 2007-05-11]
        return self::promptToJoinOnSave();
    }

    /**
     * Adds the current user to the current group if appropriate, when saving content.
     */
    public static function joinGroupOnSave() {
        if (! self::promptToJoinOnSave()) { return; }
        self::joinGroupOnSaveOrDelete();
    }

    /**
     * Adds the current user to the current group if appropriate, when saving content.
     */
    public static function joinGroupOnDelete() {
        if (! self::promptToJoinOnDelete()) { return; }
        self::joinGroupOnSaveOrDelete();
    }

    /**
     * Adds the current user to the current group if appropriate, when deleting or saving content.
     *
     * @param $deleting boolean  whether the user is deleting content
     */
    private static function joinGroupOnSaveOrDelete($delete = false) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if (XG_GroupHelper::inGroupContext() && Groups_SecurityHelper::currentUserCanJoin(XG_GroupHelper::currentGroup())) {
            Group::setStatus(XG_GroupHelper::currentGroup(), XN_Profile::current()->screenName, 'member');
        }
    }

    /**
     * Adds the current user to the network (or the current group) if appropriate, when saving content.
     * Although this should not be needed for signed-out users or Ning members who are new to the network
     * (who will join via /main/index/join), it is needed for network members who are new to a group
     * (and hackers who bypass /main/index/join).
     *
     * @deprecated 2.0  Use joinGroupOnSave instead
     */
    public static function joinOnSave() {
        self::joinGroupOnSave();
    }

    /**
     * Adds the current user to the network (and the current group) if appropriate, when saving content.
     *
     * @deprecated 2.0  Use joinGroupOnDelete instead
     */
    public static function joinOnDelete() {
        self::joinGroupOnDelete();
    }

    /**
     * Tries to make the current user a member of the group with the given url.
     *
     * @param $groupUrl string  the Group url, e.g., mycoolgroup
     * @param $username string  the screen name of the person, or null to specify the current user
     */
    public static function joinGroupWithUrl($groupUrl, $username = null) {
        if (! $username) { $username = XN_Profile::current()->screenName; }
        /* If the user is being created in this request, it may not have been saved yet */
        $user = User::loadOrRetrieveIfLoaded($username);
        /* Now if there's no user, don't bother */
        if (! $user) { return; }
        if (User::isPending($username)) {
            self::setPendingJoinGroup($user, $groupUrl);
        } else {
            self::joinGroupWithUrlProper($groupUrl, $username);
        }
    }

    /**
     * Join a user to a group if they had a pending join group saved for them when
     * they applied for membership
     *
     * @param $username string the screen name of the person, or null to specify the current user
     */
    public static function joinGroupIfPending($username) {
        if (! $username) { $username = XN_Profile::current()->screenName; }
        $user = User::loadOrRetrieveIfLoaded($username);
        $groupToJoin = self::getPendingJoinGroup($user);
        if (mb_strlen($groupToJoin)) {
            self::joinGroupWithUrlProper($groupToJoin, $username);
            self::setPendingJoinGroup($user, '');
        }
    }

    /**
     * Actually join a specified user to a group
     *
     * @param $groupUrl string group to join
     * @param $username string screen name of user
     */
    protected static function joinGroupWithUrlProper($groupUrl, $username) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if ($groupUrl && Groups_SecurityHelper::userCanJoin($group = Group::load(Group::urlToId($groupUrl)), $username)) {
            Group::setStatus($group, $username, 'member');
        }
    }

    /**
     * Set the group a pending user should join when the membership is approved
     *
     * @param $user User the user object of the pending member
     * @param $group string the group to join
     */
    protected static function setPendingJoinGroup($user, $group) {
        $attr = XG_App::widgetAttributeName(W_Cache::getWidget('main'), 'pendingJoinGroup');
        $user->my->$attr = $group;
    }

    /**
     * Find the group (if any) a pending user should join when the membership is approved
     *
     * @param $user User the user object of the pending member
     */
    protected static function getPendingJoinGroup($user) {
        $attr = XG_App::widgetAttributeName(W_Cache::getWidget('main'), 'pendingJoinGroup');
        return $user->my->$attr;
    }

}
