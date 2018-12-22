<?php

/**
 * Useful functions for making widgets groups-aware.
 * In a group context, $_GET['groupId'] will be set to the Group's content ID.
 */
class XG_GroupHelper {

    // This class provides a layer of functionality at a higher level than the functions in the Group class.
    // These are utility functions for making it easy for Forum, Photos, Videos, and other widgets
    // to be "group-aware". In contrast, Group contains general functions for working with group objects.
    //
    // The key idea behind the functions in this class is that the current group is identified by $_GET['groupId'].

    /**
     * Redirects if the current user lacks sufficient group privileges to access the current group
     */
    public static function checkCurrentUserCanAccessGroup() {
        if (! self::inGroupContext()) { return; }
        $route = XG_App::getRequestedRoute();
        if ($route == array('widgetName' => 'groups', 'controllerName' => 'group', 'actionName' => 'show')) { return; }
        if ($route == array('widgetName' => 'groups', 'controllerName' => 'group', 'actionName' => 'join')) { return; }
        if ($route == array('widgetName' => 'groups', 'controllerName' => 'invitationrequest', 'actionName' => 'create')) { return; }
        if ($route == array('widgetName' => 'groups', 'controllerName' => 'invitation', 'actionName' => 'delete')) { return; }
        self::checkCurrentUserCanAccessGroupProper(self::currentGroup());
    }

    /**
     * Redirects if the current user lacks sufficient group privileges to access the group
     *
     * @param $group XN_Content|W_Content  the group against whose membership to check the current user
     */
    private static function checkCurrentUserCanAccessGroupProper($group) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if (! Groups_SecurityHelper::currentUserCanAccessGroup($group)) {
            header('Location: ' . XG_HttpHelper::addParameter(XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id)), 'joinGroupTarget', XG_HttpHelper::currentUrl()));
            exit;
        }
    }

    /**
     * Redirects to an error page if the group has been deleted
     *
     * @param $showingForumTopic boolean  Whether the current request is trying to show the detail page of a discussion
     */
    public static function checkGroupNotDeleted($showingForumTopic) {
        if (! self::inGroupContext()) { return; }
        try {
            $group = self::currentGroup();
        } catch (Exception $e) {
        }
        if (! $group || $group->my->deleted == 'Y') {
            if ($showingForumTopic) {
                W_Cache::getWidget('forum')->dispatch('topic','showDeleted', array(false));
            } else {
                W_Cache::getWidget('main')->dispatch('error','404');
            }
            exit;
        }
    }

    /**
     * Redirects if the current user lacks sufficient group privileges to access the given content object.
     * Put this in actions that load objects by an ID passed as a GET parameter. Otherwise attackers can
     * access the action by removing the groupId parameter from the URL.
     *
     * @param XN_Content|W_Content  The content object, which may or may not belong to a Group; or null
     * @return XN_Content|W_Content  The content object
     */
    public static function checkCurrentUserCanAccess($content) {
        if (is_null($content)) { return $content; }
        if (! ($content instanceof XN_Content || $content instanceof W_Content)) { xg_echo_and_throw('Not XN_Content or W_Content (419084488)'); }
        if (! $content->my->groupId) { return $content; }
        if ($content->type == 'GroupInvitation') { return true; }
        self::checkCurrentUserCanAccessGroupProper(Group::load($content->my->groupId));
        return $content;
    }

    /**
     * Filters the query by the current group's ID
     *
     * @param XN_Query|XG_Query  The query to which to add the filter
     * @return XN_Query|XG_Query  The query
     */
    public static function addGroupFilter($query) {
        if (is_null($query)) { xg_echo_and_throw('Null query (1297879999)'); }
        $query->filter('my.groupId', '=', self::inGroupContext() ? self::currentGroupId() : null);
        return $query;
    }

    /**
     * Filters the search query by the current group's ID, if any
     *
     * @param XN_Query|XG_Query  The query to which to add the filter
     * @return XN_Query|XG_Query  The query
     */
    public static function addGroupSearchFilter($query) {
        if (is_null($query)) { xg_echo_and_throw('Null query (1297879899)'); }
        if (self::inGroupContext()) {
            $query->filter('my.groupId', 'like', '"' . mb_strtolower(self::currentGroupId()) . '"');
        }
        else {
            $query->filter('my.groupId','=',null);
        }
        return $query;
    }


    /**
     * Returns whether the current group is a private group.
     * Returns false if we are not in a group context.
     *
     * @return boolean  Whether the group is private
     */
    public static function groupIsPrivate() {
        return self::inGroupContext() && Group::isPrivate(self::currentGroup());
    }

    /**
     * Returns a URL for the given widget, controller, action, and query parameters.
     *
     * @param $widgetInstanceName W_Widget the name of the widget's instance directory
     * @param $controller string  the name of the controller
     * @param $action string  the name of the action
     * @param $parameters string|array  the query parameters: 'key=value' or array('key' => 'value')
     * @param $groupUrl string  the custom group URL, or null to determine it automatically from the GET variables
     * @return string  the URL
     */
    public static function buildUrl($widgetInstanceName, $controller, $action, $parameters = null, $groupUrl = null) {
        $parametersProper = array();
        if (! $parameters) { }
        elseif (is_string($parameters)) { parse_str(preg_replace('@^\?@u', '', $parameters), $parametersProper); }
        else { $parametersProper = $parameters; }
        // http://networkname.ning.com/group/MyCoolGroup [Jon Aquino 2007-05-02]
        if (($groupUrl || $parametersProper['id']) && $widgetInstanceName == 'groups' && $controller == 'group' && $action == 'show') {
            $firstPart = 'http://' . $_SERVER['HTTP_HOST'] . '/group/' . ($groupUrl ? $groupUrl : Group::load($parametersProper['id'])->my->url);
            unset($parametersProper['id']);
            return $firstPart . ($parametersProper ? '?' . http_build_query($parametersProper) : '');
        }
        // http://networkname.ning.com/group/MyCoolGroup/action [Jon Aquino 2007-05-02]
        if (($groupUrl || $parametersProper['id']) && $widgetInstanceName == 'groups' && $controller == 'group') {
            $firstPart = 'http://' . $_SERVER['HTTP_HOST'] . '/group/' . ($groupUrl ? $groupUrl : Group::load($parametersProper['id'])->my->url);
            unset($parametersProper['id']);
            return $firstPart . '/' . $action . ($parametersProper ? '?' . http_build_query($parametersProper) : '');
        }
        // http://networkname.ning.com/group/MyCoolGroup/controller/action [Jon Aquino 2007-05-02]
        if (($groupUrl || $parametersProper['groupId']) && $widgetInstanceName == 'groups') {
            $firstPart = 'http://' . $_SERVER['HTTP_HOST'] . '/group/' . ($groupUrl ? $groupUrl : Group::load($parametersProper['groupId'])->my->url);
            unset($parametersProper['groupId']);
            return $firstPart . '/' . $controller . '/' . $action . ($parametersProper ? '?' . http_build_query($parametersProper) : '');
        }
        // Change /forum/index/index to /forum  [Jon Aquino 2007-05-02]
        if (($groupUrl || self::inGroupContext()) && $widgetInstanceName == 'forum' && $controller == 'index' && $action == 'index') {
            $firstPart = 'http://' . $_SERVER['HTTP_HOST'] . '/group/' . ($groupUrl ? $groupUrl : self::currentGroup()->my->url);
            unset($parametersProper['id']);
            return $firstPart . '/forum' . ($parametersProper ? '?' . http_build_query($parametersProper) : '');
        }
        // http://networkname.ning.com/group/MyCoolGroup/widget/controller/action [Jon Aquino 2007-05-02]
        if ($groupUrl) {
            $firstPart = 'http://' . $_SERVER['HTTP_HOST'] . '/group/' . $groupUrl;
            unset($parametersProper['groupId']);
            return $firstPart . '/' . $widgetInstanceName . '/' . $controller . '/' . $action . ($parametersProper ? '?' . http_build_query($parametersProper) : '');
        }
        // @todo change in_array to isGroupEnabled [Jon Aquino 2007-05-10]
        if (self::inGroupContext() && in_array($widgetInstanceName, self::groupEnabledWidgetInstanceNames())) {
            $firstPart = 'http://' . $_SERVER['HTTP_HOST'] . '/group/' . self::currentGroup()->my->url;
            unset($parametersProper['groupId']);
            return $firstPart . '/' . $widgetInstanceName . '/' . $controller . '/' . $action . ($parametersProper ? '?' . http_build_query($parametersProper) : '');
        }
        return W_Cache::getWidget($widgetInstanceName)->buildUrl($controller, $action, $parameters);
    }

    /**
     * Returns whether the given user has administrator privileges for the current group.
     * Returns false if there is no current group.
     *
     * @param $username string  The screen name to check, or null to check the current user
     * @return  Whether the user is a group administrator
     */
    public static function isGroupAdmin($username = null) {
        return self::inGroupContext() && Group::userIsAdmin(self::currentGroup(), $username);
    }

    /**
     * Returns whether the given user has been banned from the group
     * Returns false if there is no current group.
     *
     * @param $username string  The screen name to check, or null to check the current user
     * @return  Whether the user is banned
     */
    public static function isBannedFromGroup($username = null) {
        return self::inGroupContext() && Group::userIsBanned(self::currentGroup(), $username);
    }

    /**
     * Returns the current Group
     *
     * @return XN_Content  The current Group, or null if there is no current group
     */
    public static function currentGroup() {
        if (! self::inGroupContext()) { return null; }
        if (! self::currentGroupId()) { return null; }
        $group = Group::load(self::currentGroupId());
        return $group;
    }

    /**
     * Returns the content ID for the current Group
     *
     * @return XN_Content  The content ID, or null if there is no current group
     */
    public static function currentGroupId() {
        $route = XG_App::getRequestedRoute();
        if ($route['widgetName'] == 'groups' && $route['controllerName'] == 'group') { return $_GET['id']; }
        return $_GET['groupId'];
    }

    /**
     * Sets the current group ID according to the current group URL, if provided.
     * Called once at the start of the page load.
     */
    public static function convertGroupUrlToGroupId() {
        if (! $_GET['groupUrl']) { return; }
        $route = XG_App::getRequestedRoute();
        if ($route['widgetName'] == 'groups' && $route['controllerName'] == 'group') {
            $_GET['id'] = Group::urlToId($_GET['groupUrl']);
            return;
        }
        if ($route['widgetName'] == 'groups') {
            $_GET['groupId'] = Group::urlToId($_GET['groupUrl']);
            return;
        }
        $_GET['groupId'] = Group::urlToId($_GET['groupUrl']);
    }

    /**
     * Returns whether the specified widget is known to be group-enabled.
     *
     * @param $widgetInstanceName string  the name of the widget instance, given by its directory name
     * @return boolean  whether the widget is group-aware
     */
    private static function isGroupEnabled($widgetInstanceName) {
        return in_array($widgetInstanceName, self::groupEnabledWidgetInstanceNames());
    }

    /**
     * Returns whether the current widget (e.g. Forum) is running in a group context
     *
     * @return boolean  Whether the current page belongs to a group
     */
    public static function inGroupContext() {
        return $_GET['groupUrl'] || self::currentGroupId();
    }

    /**
     * Returns whether the current user is a member of the current group.
     *
     * @return boolean  Whether the person is a member
     */
    public static function userIsMember() {
        return Group::userIsMember(self::currentGroup());
    }

    /**
     * Called before a content object is saved.
     *
     * @param $content XN_Content  The content object
     */
    public static function beforeSave($content) {
        if (self::markedAsDeleted($content)) { return self::beforeDeleteProper($content); }
        if (! self::signedInUserChangingObjectInGroupEnabledWidget($content)) { return; }
        if (! $content->id) {
            $content->my->groupId = self::currentGroupId();
            if (self::groupIsPrivate()) { $content->my->excludeFromPublicSearch = 'Y'; }
            self::changeActivityCount(+1, XN_Profile::current()->screenName);
        }
    }

    /**
     * Called before a content object has been deleted.
     *
     * @param $object mixed  The content object, an array, or possibly some other thing if the XN_Event API changes
     */
    public static function beforeDelete($object) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        foreach (is_array($object) ? $object : array($object) as $content) {
            if (! ($content instanceof XN_Content || $content instanceof W_Content)) { continue; }
            if (! self::markedAsDeleted($content)) { self::beforeDeleteProper($content); }
        }
    }

    /**
     * Called before a content object has been deleted.
     *
     * @param $content XN_Content  The content object
     */
    public static function beforeDeleteProper($content) {
        if (! self::signedInUserChangingObjectInGroupEnabledWidget($content)) { return; }
        self::changeActivityCount(-1, $content->contributorName);
    }

    /**
     * Returns whether the content object has an attribute that indicates it should be considered deleted.
     */
    private static function markedAsDeleted($content) {
        return $content->my->deleted == 'Y' || $content->my->{XG_App::widgetAttributeName(W_Cache::getWidget('forum'), 'deleted')} == 'Y';
    }

    /**
     * Returns whether the current user is signed in, and the given content object belongs
     * to a group-enabled widget (like Forum), and we are currently in a group context.
     *
     * @param $content  a content object
     * @return boolean  whether the conditions are met
     */
    private static function signedInUserChangingObjectInGroupEnabledWidget($content) {
        return XN_Profile::current()->isLoggedIn() && self::inGroupContext() && self::isGroupEnabled($content->my->mozzle);
    }

    /**
     * Adjust's the user's activity count in the current group.
     *
     * @param $activityCountChange integer  amount by which to change the activity count
     * @param $username string  the screen name of the user
     */
    public static function changeActivityCount($activityCountChange, $username) {
        $groupMembership = GroupMembership::loadOrCreate(self::currentGroup(), $username);
        $groupMembership->my->activityCount = $groupMembership->my->activityCount + $activityCountChange;
        $groupMembership->save();
    }

    /**
     * Returns the names of widget instances that are group-aware
     *
     * @return array  widget directory names
     */
    public static function groupEnabledWidgetInstanceNames() {
        return array('forum', 'html', 'feed');
    }

    /**
	 * Adds the current group ID to a url
     *
     * @return string
     */
	public static function addGroupId($url) {
        return self::inGroupContext() ? XG_HttpHelper::addParameter($url, 'groupId', self::currentGroupId()) : $url;
    }

    /**
     * Returns whether Share This links and buttons should be visible to the current user,
     * for the current group. If we are not in a group context, returns true.
     *
     * @return whether to show Share This buttons for the group
     */
    public static function currentUserCanSeeShareLinksForGroup() {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        return ! XG_GroupHelper::inGroupContext() || Groups_SecurityHelper::currentUserCanSeeShareLinks(XG_GroupHelper::currentGroup());
    }

    /**
     * Returns whether the current user can in fact share the current group.
     * If we are not in a group context, returns true.
     *
     * @return whether the current user is allowed to share the group
     */
    public static function currentUserCanShareGroup() {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        return ! XG_GroupHelper::inGroupContext() || Groups_SecurityHelper::currentUserCanShare(XG_GroupHelper::currentGroup());
    }

    /**
     * Outputs a link to the homepage of the current group, if we are in a group context.
	 * @param	$return		bool		Return html instead of printing it
     */
    public static function groupLink($return = false) {
        if (! self::inGroupContext()) { return; }
        $html = '<a href="' . xnhtmlentities(XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => XG_GroupHelper::currentGroupId()))) . '">' . xg_html('BACK_TO_GROUP', xnhtmlentities(self::currentGroup()->title)) . '</a>';
        if ($return) {
			return $html;
		}
		echo $html;
    }

}

XN_Event::listen('xn/content/save/before', array('XG_GroupHelper', 'beforeSave'));
XN_Event::listen('xn/content/delete/before', array('XG_GroupHelper', 'beforeDelete'));
