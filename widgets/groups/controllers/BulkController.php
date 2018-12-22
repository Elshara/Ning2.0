<?php
/**
 * Approves or deletes large numbers of content objects, in chunks.
 *
 * @see "Bazel Code Structure: Bulk Operations"
 */
class Groups_BulkController extends XG_GroupEnabledController {

    /**
     * Sets the privacy level of a chunk of objects created by the groups module.
     *
     * @param   $limit integer          Maximum number of content objects to change (approximate).
     * @param   $privacyLevel  string   Privacy level to swtich to: 'private' or 'public'.
     * @return  array                   'changed' => the number of content objects deleted,
     *                                  'remaining' => 1 or 0 depending on whether or not there are content objects remaining to set privacy of.
     */
    public function action_setPrivacy($limit = null, $privacyLevel = null) {
        XG_SecurityHelper::redirectIfNotOwner();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        if ($privacyLevel !== 'public' && $privacyLevel !== 'private') { throw new Exception("privacyLevel must be 'public' or 'private'"); }
        $this->_widget->includeFileOnce('/lib/helpers/Groups_BulkHelper.php');
        return Groups_BulkHelper::setPrivacy($limit, ($privacyLevel === 'private'));
    }

    /**
     * Bans the user from the group, and deletes her group content.
     * $this->contentRemaining will be set to 1 or 0 depending on whether or not there are content objects remaining to delete
     *
     * Expected GET variables:
     *     groupId - ID of the Group
     *     limit - maximum number of content objects to remove (approximate).
     *     xn_out - "json"
     *
     * Expected POST variables:
     *     user - username of the person
     */
    public function action_banAndRemoveContent() {
        W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_BulkHelper.php');
        W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_UserHelper.php');
        W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (473509034)'); }
        $group = Group::load($_GET['groupId']);
        if (! Groups_SecurityHelper::currentUserCanBan($group, $_POST['user'])) { throw new Exception('Not allowed (1424160840)'); }
        // Ensure W_Cache::current('W_Widget') returns the correct value [Jon Aquino 2007-05-01]
        W_Cache::push(W_Cache::getWidget('forum'));
        list($changed, $remaining) = Forum_BulkHelper::removeByUser($_GET['limit'], $_POST['user'], $group->id);
        W_Cache::pop(W_Cache::current('W_Widget'));
        $this->contentRemaining = $remaining;
        Group::setStatus($group, $_POST['user'], 'banned');
    }

    /**
     * Removes GroupMembership objects created by the specified user.
     * Not removed are group discussions and other objects created for the group
     * by other widgets - those objects are left to those widgets' BulkControllers.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $user string  Username of the person whose content to remove.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public function action_removeByUser($limit = null, $user = null) {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_BulkHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (53031671)'); }
        if (! XG_SecurityHelper::currentUserCanDeleteUser($user)) { xg_echo_and_throw('Not allowed (187808677)'); }
        return Groups_BulkHelper::removeGroupMemberships($limit, $user);
    }

    /**
     * Send a message to all members of a group.  Called from dojo with xn_out=json.
     *
     * Expected GET variables:
     *     groupId - ID of the group
     *
     * Expected POST variables:
     *     subject - subject for the email
     *     body - text of message
     *     counter - should start at zero and be incremented on each call
     */
    public function action_broadcast() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        try {
            if (! (isset($_GET['groupId']))) { throw new Exception('No group ID specified'); }
            $group = Group::load($_GET['groupId']);
            if (! isset($group)) { throw new Exception('Invalid group ID'); }
            if (! Groups_SecurityHelper::currentUserCanSendMessageToGroup($group)) { throw new Exception("Permission denied."); }
            if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception("Only POST requests permitted"); }
            if (! (isset($_POST['subject']) && mb_strlen(trim($_POST['subject'])))) { throw new Exception("No subject specified"); }
            if (! (isset($_POST['body']) && mb_strlen(trim($_POST['body'])))) { throw new Exception("No body specified"); }
            if (! (isset($_POST['counter']) && mb_strlen($_POST['counter']) && ctype_digit($_POST['counter']))) { throw new Exception("No counter supplied"); }
            $this->_widget->includeFileOnce('/lib/helpers/Groups_BroadcastHelper.php');
            $helper = new Groups_BroadcastHelper();
            $subject = mb_substr(strip_tags(trim($_POST['subject'])), 0, 200);
            $body = mb_substr(strip_tags(trim($_POST['body'])), 0, 2000);
            list($changed, $contentRemaining) = $helper->broadcast($group, $subject, $body, $_POST['counter']);
            $this->contentSent = $changed;
            $this->contentRemaining = $contentRemaining;
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }
    
    
    /**
     * Approves Groups created by the specified user.  If no user is specified, all pending
     * Groups will be approved.
     *
     * @param $limit integer maximum number of content objects to remove (approximate). Can also be specified with $_GET['limit'].
     * @param $user string username of the person whose content to remove. Can also be specified with $_GET['user'].
     * @return array 'changed' => the number of content objects approved,
     *     'remaining' => a positive number if there are content objects that remain to be approved for the user; otherwise, zero.
     *     The latter is also stored in $this->contentRemaining.
     * @throws Exception if the current user is not the site owner
     */
    public function action_approveByUser($limit = null, $user = null) {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception(); }
        if (! XG_SecurityHelper::userIsAdmin()) { throw new Exception(); }
        $limit = $limit ? $limit : $_GET['limit'];
        $user = $user ? $user : $_GET['user'];
        $query = XN_Query::create('Content');
        $query->begin(0);
        $query->end($limit);
        $groups = Groups_Filter::get('moderation')->execute($query, $user);
        foreach ($groups as $group) {
            $group->my->approved = 'Y';
            $group->save();
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_GROUP, $group->contributorName, array($group));
            //TODO: messaging for user
        }
        // Invalidate the approval-link cache
        W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
        $this->contentRemaining = count($groups) >= $limit ? 1 : 0;
        return array('changed' => count($groups), 'remaining' => $this->contentRemaining);
    }
    
    /**
     * Approves all Groups waiting to be moderated.
     *
     * @param $limit integer maximum number of content objects to remove (approximate). Can also be specified with $_GET['limit'].
     * @return array 'changed' => the number of content objects approved,
     *     'remaining' => a positive number if there are content objects that remain to be approved; otherwise, zero.
     *     The latter is also stored in $this->contentRemaining.
     * @throws Exception if the current user is not the site owner
     */
    public function action_approveAll($limit = null) {
        return $this->action_approveByUser($limit, null);
    }
    
    /**
     * Removes Groups that have not yet been approved.
     *
     * $_GET['limit'] maximum number of content objects to remove (approximate).
     * $_GET['user'] username of the person whose unmoderated photos to remove, or null to remove all unmoderated photos.
     * @return null. $this->contentRemaining will be set to a positive number
     *         if there are content objects that remain to be deleted; otherwise, zero.
     * @throws Exception if the current user is not the site owner
     */
    public function action_removeUnapprovedGroups() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception(); }
        if (! XG_SecurityHelper::userIsAdmin()) { throw new Exception(); }
        $limit = $limit ? $limit : $_GET['limit'];
        $user = $user ? $user : $_GET['user'];
        $query = XN_Query::create('Content');
        $query->begin(0);
        $query->end($limit);
        $groups = Groups_Filter::get('moderation')->execute($query, $user);
        $groupIds = array();
        foreach($groups as $group) {
            $groupIds[$group->id];
        }
        $groupObjects = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'GroupMembership')->filter('my.groupId', 'in', $groupIds)->end(20)->execute();
        XN_Content::delete(array_merge($groupObjects, $groups));
        // Invalidate the approval-link cache
        W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
        $this->contentRemaining = count($groups) >= $limit ? 1 : 0;
    }

}
