<?php

/**
 * Dispatches requests pertaining to users.
 */
class Groups_UserController extends XG_GroupEnabledController {

    /**
     * Displays the members of the specified group
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     */
    public function action_list() {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_GroupMembershipFilter.php');
        $group = Group::load($_GET['groupId']);
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $pageSize = 18);
        $inviteUrl = Groups_SecurityHelper::currentUserCanSeeInviteLinks($group) ? $this->_buildUrl('invitation','new', array('groupId' => $group->id)) : null;
        $manageUrl = Groups_SecurityHelper::currentUserCanEditMemberships($group)
                ? $this->_buildUrl('user','edit', array('groupId' => $group->id)) : NULL;
        if ($_GET['q']) {
            $userInfo = User::find(array('my.searchText' => array('likeic', $_GET['q']), 'my.' . XG_App::widgetAttributeName(W_Cache::getWidget('groups'), 'groups') => array('likeic', $group->id)), $begin, $begin + $pageSize, null, null, false);
            $users = $userInfo['users'];
            $numUsers = $userInfo['numUsers'];
        } else {
            $query = XN_Query::create('Content')->begin($begin)->end($begin + $pageSize);
            if (XG_Cache::cacheOrderN()) { $query = XG_Query::create($query); }
            $profiles = Groups_GroupMembershipFilter::get('mostActive')->profiles($query, $group->id);
            $users = User::loadMultiple($profiles);
            $numUsers = $query->getTotalCount();
        }
        $searchUrl = XG_HttpHelper::addParameters($this->_buildUrl('user','list'), array('groupId' => $group->id));
        $this->listColumnProperArgs = array('users' => $users, 'pageTitle' => $_GET['q'] ? xg_text('SEARCH_RESULTS') : xg_text('GROUPNAME_MEMBERS', $group->title),
                'numUsers' => $numUsers, 'searchUrl' => $searchUrl, 'inviteUrl' => $inviteUrl, 'searchButtonText' => xg_text('SEARCH_MEMBERS'),
                'emptyMessage' => $_GET['q'] ? xg_text('SORRY_NO_MEMBERS_MATCHING_X_WERE_FOUND', $_GET['q']) : xg_text('GROUP_HAS_NO_MEMBERS'),
                'pageSize' => $pageSize, 'manageUrl' => $manageUrl,
				'paginationUrl' => $this->_buildUrl('user', 'list', array('sort' => $_GET['sort'], 'page' => $_GET['page'], 'groupId' => $group->id )));
    }

    /** Number of people per page on the edit pages */
    const EDIT_PAGE_SIZE = 50;

    /**
     * Displays the form for editing the list of people belonging to a group
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     */
    public function action_edit() {
        $this->forwardTo('editMembers');
    }

    /**
     * Displays the form for editing the list of people belonging to a group
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     */
    public function action_editMembers() {
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $this->prepareEditAction();
        $query = $this->createEditActionQuery($this->begin, $this->end);
        $this->applySearchFilters($query);
        $this->applySortOrder($query, array('date' => array('my->dateJoined', XN_Attribute::DATE)));
        $groupMemberships = Groups_GroupMembershipFilter::get('unsorted')->execute($query, $this->group->id);
        $this->totalCount = $query->getTotalCount();
        $profiles = GroupMembership::profiles($groupMemberships); // Prime the cache [Jon Aquino 2007-04-30]
        $this->users = array();
        XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
        foreach ($groupMemberships as $groupMembership) {
            if ($groupMembership->my->username == $this->group->contributorName) {
                $statusHtml = '<td><div class="creator">' . xg_html('GROUP_CREATOR') . '</div></td>';
                $status = XG_MembershipHelper::OWNER;
            } else if (Group::userIsAdmin($this->group, $groupMembership->my->username)) {
                $statusHtml = '<td><div class="admin">' . xg_html('GROUP_ADMINISTRATOR') . '</div></td>';
                $status = XG_MembershipHelper::ADMINISTRATOR;
            } else {
                $statusHtml = '<td><div class="member">' . xg_html('MEMBER') . '</div></td>';
                $status = XG_MembershipHelper::MEMBER;
            }
            $showCheckbox =
                    Groups_SecurityHelper::currentUserCanPromoteToAdministrator($this->group, $groupMembership->my->username) ||
                    Groups_SecurityHelper::currentUserCanDemoteFromAdministrator($this->group, $groupMembership->my->username) ||
                    Groups_SecurityHelper::currentUserCanBan($this->group, $groupMembership->my->username);
            $this->users[] = array(
                    'name' => xg_username($profiles[$groupMembership->my->username]),
                    'profileUrl' => User::quickProfileUrl($groupMembership->my->username),
                    'ningId' => $groupMembership->my->username,
                    'email' => $profiles[$groupMembership->my->username]->email,
                    'checkboxName' => $showCheckbox ? 'selectedIds[]' : '',
                    'checkboxValue' => $groupMembership->my->username,
                    'date' => $groupMembership->my->dateJoined,
                    'status' => $status,
                    'statusHtml' => $statusHtml);
        }
        $this->currentTab = xg_text('MEMBERS');
        $this->dateTitle = xg_text('DATE_JOINED');
        $this->buttonTemplate = 'fragment_membersTabButtons';
    }

    /**
     * Displays the form for editing the list of people banned from a group
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     */
    public function action_editBanned() {
        $this->prepareEditAction();
        $query = $this->createEditActionQuery($this->begin, $this->end);
        $this->applySearchFilters($query);
        $this->applySortOrder($query, array('date' => array('my->dateBanned', XN_Attribute::DATE)));
        $groupMemberships = Groups_GroupMembershipFilter::get('banned')->execute($query, $this->group->id);
        $this->totalCount = $query->getTotalCount();
        if (! $this->totalCount) { $this->redirectTo('editMembers', 'user', array('groupId' => $this->group->id)); return; }
        $profiles = GroupMembership::profiles($groupMemberships); // Prime the cache [Jon Aquino 2007-04-30]
        $this->users = array();
        XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
        foreach ($groupMemberships as $groupMembership) {
            $this->users[] = array(
                    'name' => xg_username($profiles[$groupMembership->my->username]),
                    'profileUrl' => User::quickProfileUrl($groupMembership->my->username),
                    'ningId' => $groupMembership->my->username,
                    'email' => $profiles[$groupMembership->my->username]->email,
                    'checkboxName' => 'selectedIds[]',
                    'checkboxValue' => $groupMembership->my->username,
                    'date' => $groupMembership->my->dateBanned,
                    'statusHtml' => '<td><div class="blocked">' . xg_html('BANNED') . '</div></td>',
                    'status' => XG_MembershipHelper::BANNED);
        }
        $this->currentTab = xg_text('BANNED');
        $this->dateTitle = xg_text('DATE_BANNED');
        $this->buttonTemplate = 'fragment_bannedTabButtons';
    }

    /**
     * Displays the form for editing the list of people invited to a group
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     *     invitationCount - number of invitations that were re-sent
     */
    public function action_editInvitations() {
        $this->prepareEditAction();
        $this->searchable = false;
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $invitations = Groups_InvitationHelper::getUnusedGroupInvitations($this->group->id, $this->begin, $this->end, $totalCount, $profiles);
        $this->totalCount = $totalCount;
        if (! $this->totalCount) { $this->redirectTo('editMembers', 'user', array('groupId' => $this->group->id)); return; }
        $this->users = array();
        foreach ($invitations as $invitation) {
            $this->users[] = array(
                    'name' => $invitation['displayName'],
                    'ningId' => $invitation['screenName'],
                    'email' => $invitation['emailAddress'],
                    'checkboxName' => 'selectedIds[]',
                    'checkboxValue' => $invitation['id'],
                    'date' => $invitation['date'],
                    'statusHtml' => '<td><div class="invited">' . xg_html('INVITED_BY_X', xg_userlink($profiles[$invitation['inviter']])) . '</div></td>',
                    'status' => XG_MembershipHelper::INVITED);
        }
        $this->currentTab = xg_text('INVITED');
        $this->dateTitle = xg_text('DATE_INVITED');
        $this->buttonTemplate = 'fragment_invitedTabButtons';
        if ($_GET['invitationCount']) { $this->successMessage = xg_text('N_INVITATIONS_RESENT', intval($_GET['invitationCount'])); }
    }

    /**
     * Displays the form for editing the list of people requesting an invitation to a group
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     */
    public function action_editInvitationRequests() {
        $this->prepareEditAction();
        $this->searchable = false;
        $query = $this->createEditActionQuery($this->begin, $this->end);
        $groupInvitationRequests = $query->filter('type','=','GroupInvitationRequest')->execute();
        $this->totalCount = $query->getTotalCount();
        if (! $this->totalCount) { $this->redirectTo('editMembers', 'user', array('groupId' => $this->group->id)); return; }
        $profiles = XG_Cache::profiles($groupInvitationRequests, GroupInvitationRequest::userIds($groupInvitationRequests)); // Prime the cache [Jon Aquino 2007-04-30]
        $this->users = array();
        XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
        foreach ($groupInvitationRequests as $groupInvitationRequest) {
            $profile = $profiles[$groupInvitationRequest->my->requestor];
            $this->users[] = array(
                    'name' => $profile ? xg_username($profile) : $groupInvitationRequest->my->requestor,
                    'ningId' => $profile ? $profile->screenName : null,
                    'email' => $profile ? $profile->email : null,
                    'checkboxName' => 'selectedIds[]',
                    'checkboxValue' => $groupInvitationRequest->my->requestor,
                    'date' => $groupInvitationRequest->createdDate,
                    'status' => XG_MembershipHelper::REQUESTED,
                    'statusHtml' => '<td><div class="requested">' . xg_html('REQUESTED_INVITE') . '</div></td>');
        }
        $this->currentTab = xg_text('REQUESTED_INVITE');
        $this->dateTitle = xg_text('DATE_REQUESTED');
        $this->buttonTemplate = 'fragment_requestedInviteTabButtons';
    }

    /**
     * Creates an XN_Query initialized with settings common to the edit actions.
     *
     * @param $begin integer  0-based start index (inclusive)
     * @param $end integer  0-based end index (exclusive)
     * @return XN_Query  a partially initialized XN_Query
     */
    private function createEditActionQuery($begin, $end) {
        return XN_Query::create('Content')->begin($begin)->end($end)->filter('my.groupId', '=', $this->group->id)->filter('owner')->filter('my.mozzle', '=', 'groups')->alwaysReturnTotalCount(true);
    }

    /**
     * Common set-up for the edit actions
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     */
    private function prepareEditAction() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_GroupMembershipFilter.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_InvitationHelper.php');
        XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->group = Group::load($_GET['groupId']);
        // Only allow searching if all GroupMemberships for this group have had name denormalized onto them.
        $this->searchable = GroupMembership::denormalizeFullName($_GET['groupId']) == 0;
        if (! Groups_SecurityHelper::currentUserCanEditMemberships($this->group)) { return $this->redirectTo(XG_AuthorizationHelper::signInUrl()); }
        $this->render('edit');
        $this->begin = XG_PaginationHelper::computeStart($_GET['page'], self::EDIT_PAGE_SIZE);
        $this->end = $this->begin + self::EDIT_PAGE_SIZE;
        $this->memberCount = Group::getMemberCount($this->group);
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        Groups_InvitationHelper::getUnusedGroupInvitations($this->group->id, 0, 1, $totalCount, $profiles);
        $this->invitationCount = $totalCount;
        if (Group::isPrivate($this->group) && $this->group->my->allowInvitationRequests == 'Y') {
            $query = XN_Query::create('Content')->end(1)->filter('owner')->filter('my.mozzle', '=', 'groups')->filter('type','=','GroupInvitationRequest')->filter('my.groupId', '=', $this->group->id)->alwaysReturnTotalCount(true);
            $query->execute();
            $this->invitationRequestCount = $query->getTotalCount();
        }
        $query = XN_Query::create('Content')->end(1);
        Groups_GroupMembershipFilter::get('banned')->execute($query, $this->group->id);
        $this->bannedCount = $query->getTotalCount();
    }

    /**
     * Processes the form for editing the group's membership.
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     *     target - URL to redirect to after the update
     *
     * Expected POST variables:
     *     operation - the action to perform: promoteToAdministrator, demoteFromAdministrator, invite,
     *             cancelInvitation, invite, ignoreInvitationRequest, removeBan
     *     selectedIds - array of usernames, email addresses, or shadow-contact IDs selected by the user.
     *             The type of ID depends on the operation; for example, for the "promoteToAdministrator" operation,
     *             the IDs will all be usernames.
     */
    public function action_update() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (640863359)'); }
        $group = Group::load($_GET['groupId']);
        if (! Groups_SecurityHelper::currentUserCanEditMemberships($group)) { $this->redirectTo('show', 'group', array('id' => $group->id)); return; }
        if ($_POST['operation'] == 'resendInvitation') {
            Index_InvitationHelper::resendUnusedInvitations($_POST['selectedIds']);
            $invitationCount = count($_POST['selectedIds']);
        } elseif ($_POST['operation'] == 'cancelInvitation') {
            Index_InvitationHelper::deleteUnusedInvitations($_POST['selectedIds']);
        } else {
            foreach ($_POST['selectedIds'] as $id) {
                if ($_POST['operation'] == 'promoteToAdministrator' && Groups_SecurityHelper::currentUserCanPromoteToAdministrator($group, $id)) {
                    Group::setStatus($group, $id, 'admin');
                } elseif ($_POST['operation'] == 'demoteFromAdministrator' && Groups_SecurityHelper::currentUserCanDemoteFromAdministrator($group, $id)) {
                    Group::setStatus($group, $id, 'member');
                } elseif ($_POST['operation'] == 'ban' && Groups_SecurityHelper::currentUserCanBan($group, $id)) {
                    Group::setStatus($group, $id, 'banned');
                } elseif ($_POST['operation'] == 'removeBan') {
                    Group::setStatus($group, $id, 'member');
                } elseif ($_POST['operation'] == 'invite') {
                    Groups_InvitationHelper::sendGroupInvitation($group, $id);
                    XN_Content::delete(GroupInvitationRequest::loadOrCreate($group, $id));
                    $invitationCount++;
                } elseif ($_POST['operation'] == 'ignoreInvitationRequest') {
                    XN_Content::delete(GroupInvitationRequest::loadOrCreate($group, $id));
                }
            }
        }
        header('Location: ' . XG_HttpHelper::addParameter($_GET['target'], 'invitationCount', $invitationCount));
    }

    /**
     * Bans the given users. $this->contentRemaining will be set to 1 or 0
     * depending on whether or not there are content objects remaining to delete.
     * Used with the BulkActionLink widget.
     *
     * Expected GET variables:
     *     groupId - content ID for the group
     *     xn_out - "json"
     *
     * Expected POST variables:
     *     user - username of the person to ban
     */
    public function action_ban() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (224077123)'); }
        $group = Group::load($_GET['groupId']);
        if (Groups_SecurityHelper::currentUserCanBan($group, $_POST['user'])) {
            Group::setStatus($group, $_POST['user'], 'banned');
        }
        $this->contentRemaining = 0;
    }

    /**
     * Determine the appropriate search filters and add them to the supplied query.
     *
     * Optional $_GET variable: 'q'.  Used as text in search.
     * @param   $query  XN_Query|XG_Query   Query to add filters to.
     */
    private function applySearchFilters($query) {
        if (isset($_GET['q'])) {
            $query->filter('my->fullName', 'likeic', $_GET['q']);
        }
    }

    /**
     * Apply the appropriate sort order to the specified query.
     *
     * Optional $_GET variable 'sort' will be used as specified in XG_QueryHelper::sortOrder
     * to determine the correct attribute and direction for ordering.
     *
     * @param   $query   XN_Query|XG_Query  Query to apply ordering to.
     * @param   $fields               array Array of attribute name overrides.  See XG_QueryHelper::sortOrder.
     * @return  XN_Query|XG_Query   Query with sort order added, or untouched if not $_GET['sort'].
     */
    private static function applySortOrder($query, $fields=array()) {
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $groupFields = $fields;
        $groupFields['status'] = array('my->status', XN_Attribute::STRING);
        list($by, $direction, $type) = XG_QueryHelper::sortOrder($_GET['sort'], $groupFields);
        return $query->order($by, $direction, $type);
    }
}
