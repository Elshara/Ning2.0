<?php

/**
 * Dispatches requests pertaining to groups.
 */
class Groups_GroupController extends XG_GroupEnabledController {

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_HtmlHelper.php');
    }

    /**
     * Pokes a hole in the app-wide privacy mechanism.
     *
     * @param $action string  The name of the action
     * @return boolean  Whether to bypass the privacy mechanism for the given action
     */
    public function action_overridePrivacy($action) {
        if ($action == 'denormalizeFullName') { return true; }
        return ! XG_App::appIsPrivate() && ! XG_GroupHelper::groupIsPrivate() && $_GET['feed'] == 'yes' && $action == 'forum';
    }

    /**
     * Returns the 5 most recently featured Groups.
     *
     * @return array  the Groups data, keyed by items and totalCount
     */
    protected function getFeaturedGroups() {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (! XG_PromotionHelper::areQueriesEnabled()) { return array('items' => array(), 'totalCount' => 0); }
        $featuredGroupsQuery = XG_Query::create('Content')->end(5);
        // BAZ-6711: cache this query's results
        $featuredGroupsQuery->addCaching(XG_CacheExpiryHelper::promotedObjectsChangedCondition('Group'));
        return array('items' => Groups_Filter::get('promoted')->execute($featuredGroupsQuery), 'totalCount' => $featuredGroupsQuery->getTotalCount());
    }

    /**
     * Displays a list of groups.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecent or mostPopular
     */
    public function action_list() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        if (!$_GET['page'] || $_GET['page'] == 1) {
            $this->featuredGroups = $this->getFeaturedGroups();
        }
        $titleHtml = array('mostActive' => xg_text('MOST_ACTIVE_GROUPS'),
                'latestActivity' => xg_text('GROUPS_BY_LATEST_ACTIVITY'),
                'mostPopular' => xg_text('GROUPS_BY_MOST_MEMBERS'),
                'mostRecent' => xg_text('LATEST_GROUPS'));
        if (Groups_SecurityHelper::currentUserCanSeeCreateGroupLinks()) {
            $this->noObjectsSubtitle = xg_text('ADD_A_GROUP');
            $this->noObjectsMessageHtml = xg_html('NOBODY_HAS_CREATED_GROUPS_ADD');
            $this->noObjectsLinkUrl = $this->_buildUrl('group', 'new');
            $this->noObjectsLinkText = xg_text('ADD_A_GROUP');
        } else {
            $this->noObjectsMessageHtml = xg_html('NOBODY_HAS_CREATED_GROUPS');
        }
        self::prepareListAction(array(
                'sortNames' => array('mostActive', 'latestActivity', 'mostPopular','mostRecent'),
                'titleHtml' => $titleHtml));
		$this->titleHtml = xg_text('ALL_GROUPS');
    }

    /**
     * Displays a list of groups that share a specific location.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     location
     *     sort - mostRecent or mostPopular
     */
    public function action_listByLocation() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $titleHtml = array('mostActive' => xg_text('MOST_ACTIVE_GROUPS'),
                            'latestActivity' => xg_text('GROUPS_BY_LATEST_ACTIVITY'),
                            'mostPopular' => xg_text('GROUPS_BY_MOST_MEMBERS'),
                            'mostRecent' => xg_text('LATEST_GROUPS'));
        if (Groups_SecurityHelper::currentUserCanSeeCreateGroupLinks()) {
            $this->noObjectsSubtitle = xg_text('ADD_A_GROUP');
            $this->noObjectsMessageHtml = xg_html('NOBODY_HAS_CREATED_GROUPS_ADD');
            $this->noObjectsLinkUrl = $this->_buildUrl('group', 'new');
            $this->noObjectsLinkText = xg_text('ADD_A_GROUP');
        } else {
            $this->noObjectsMessageHtml = xg_html('NOBODY_HAS_CREATED_GROUPS');
        }
        self::prepareListAction(array(
                'sortNames' => array('mostActive', 'latestActivity', 'mostPopular','mostRecent'),
                // TODO: Do we need to urldecode here? PHP already urldecodes parameters. [Jon Aquino 2008-02-06]
                'titleHtml' => xg_text('ALL_GROUPS_IN_X', trim(urldecode($_GET['location'])))));
    }

    /**
     * Displays a list of groups started by a given person.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecent or mostPopular
     *     user - screen name of the person who started the topics
     */
    public function action_listForContributor() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        $screenName = $_GET['user'];
        if (! $screenName) {
            XG_SecurityHelper::redirectIfNotMember();
            $screenName = $this->_user->screenName;
        }
        $this->user = XG_Cache::profiles($screenName);
        $fullName = xg_username($this->user);
        $viewingOwnGroups = $screenName === XN_Profile::current()->screenName;
        self::prepareListAction(array(
                'pageTitle' => $viewingOwnGroups ? xg_text('MY_GROUPS') : xg_text('USERS_GROUPS', $fullName),
                'titleHtml' => $viewingOwnGroups ? xg_text('MY_GROUPS') : xg_html('USERS_GROUPS', $fullName),
                'sortNames' => array('joined'),
                'user' => $screenName));
        if ($screenName == XN_Profile::current()->screenName && Groups_SecurityHelper::currentUserCanSeeCreateGroupLinks()) {
            $this->noObjectsSubtitle = xg_text('ADD_A_GROUP');
            $this->noObjectsMessageHtml = xg_html('YOU_HAVE_NOT_CREATED_GROUPS');
            $this->noObjectsLinkUrl = $this->_buildUrl('group', 'new');
            $this->noObjectsLinkText = xg_text('ADD_A_GROUP');
        } elseif ($screenName == XN_Profile::current()->screenName) {
            $this->noObjectsMessageHtml = xg_html('YOU_ARE_NOT_MEMBER_OF_GROUPS');
        } else {
            $this->noObjectsMessageHtml = xg_html('USER_HAS_NOT_CREATED_GROUPS', xnhtmlentities($fullName));
        }
    }

    /**
     * Displays a list of featured groups.
     *
     * Expected GET variables:
     *     page - page number (optional)
     */
    public function action_listFeatured() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->noObjectsMessageHtml = xg_html('NO_FEATURED_GROUPS');
        $this->hideSorts = true;
        self::prepareListAction(array(
                'sortNames' => array('promoted'),
                'titleHtml' => xg_text('FEATURED_GROUPS')));
    }

    /**
     * Displays a list of recent groups with the given search keywords.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecent or mostPopularDiscussions
     *     q - search keywords (optional)
     *
     */
    public function action_search() {
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        self::prepareListAction(array(
                'titleHtml' => xg_text('SEARCH_RESULTS'),
                'useSearch' => (XG_QueryHelper::getSearchMethod() != 'content'),
                'sortNames' => array('mostRecent', 'mostPopular')));
        $this->noObjectsMessageHtml = xg_html('WE_COULD_NOT_FIND_ANY_GROUPS');
        $this->hideSorts = true;
        if (count($this->groups)) {
            $this->groupIds = Group::groupsForObjects($this->groups);
            $this->groups = self::prioritizeGroupsInResults($this->groups);
        }
    }

    /**
     * Adds the current user as a member of the specified group.
     *
     * Expected GET variables:
     *     id - content ID for the group
     *     joinGroupTarget - URL to redirect to, or null to redirect to the group page
     */
    public function action_join() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        if (! $this->_user->isLoggedIn()) { xg_echo_and_throw('Not signed in (1309613577)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { xg_echo_and_throw('Not a POST (1760614438)'); }
        $group = Group::load($_GET['id']);
        if (! Groups_SecurityHelper::currentUserCanJoin($group)) { xg_echo_and_throw('Not allowed (1845971453)'); }
        Index_NotificationHelper::startFollowing($group);
        Group::updateActivityScore($group,GROUP::ACTIVITY_SCORE_MEMBER_JOIN);
        Group::setStatus($group, $this->_user->screenName, 'member');
        if ($_GET['joinGroupTarget']) {
            header('Location: ' . $_GET['joinGroupTarget']);
            exit;
        }
        $this->redirectTo('show', 'group', array('id' => $group->id));
    }

    /**
     * Removes the current user from the group
     *
     * Expected GET variables:
     *     id - content ID for the group
     *
     * @see Groups_InvitationController::action_delete
     */
    public function action_leave() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (! $this->_user->isLoggedIn()) { xg_echo_and_throw('Not signed in (918561946)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { xg_echo_and_throw('Not a POST (1207708123)'); }
        $group = Group::load($_GET['id']);
        if (! Groups_SecurityHelper::currentUserCanLeaveGroup($group)) { xg_echo_and_throw('Not allowed (1040903248)'); }
        Group::setStatus($group, $this->_user->screenName, 'nonmember');
        self::deleteOrUpdateActivityLog($group,$this->_user->screenName);
        $this->redirectTo('show', 'group', array('id' => $group->id));
    }

    /**
     * Displays the form for a new group.
     */
    public function action_new($errors = NULL) {
        XG_SecurityHelper::redirectIfNotMember();
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if (! Groups_SecurityHelper::currentUserCanCreateGroup()) { xg_echo_and_throw('Cannot add groups'); }
        $this->errors = $errors;
        $this->group = Group::create();
        $this->form = new XNC_Form(array(
                'externalWebsiteUrl' => 'http://',
                'groupPrivacy' => $this->group->my->groupPrivacy,
                'allowInvitations' => $this->group->my->allowInvitations == 'Y' ? 'yes' : null,
                'allowInvitationRequests' => $this->group->my->allowInvitationRequests == 'Y' ? 'yes' : null,
                'allowMemberMessaging' => $this->group->my->allowMemberMessaging == 'Y' ? 'yes': null,
                'htmlActive' => 'yes',
                'forumActive' => 'yes',
                'feedActive' => null,
                'groupsActive' => 'yes'));
        $this->title = xg_text('ADD_A_GROUP');
        $this->buttonText = xg_text('ADD_GROUP');
        $this->formUrl = $this->_buildUrl('group', 'create');
        $this->cancelUrl = $this->_buildUrl('group','list');
        $this->hideStepLinks = Groups_SecurityHelper::moderatedForThisUser();
        $this->editingExistingGroup = false;
        $this->render('newOrEdit');
    }

    /**
     * Processes the form for a new group.
     */
    public function action_create() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('new', 'group'); return; }
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if (! Groups_SecurityHelper::currentUserCanCreateGroup()) { xg_echo_and_throw('Cannot add groups'); }
        $this->processForm($group = Group::create(), 'new');
        if ($group->id) {
            Group::setStatus($group, XN_Profile::current()->screenName, 'admin');
            Index_NotificationHelper::startFollowing($group);
            //  Create the group broadcast alias and add the group owner (1.11.1)
            $this->_widget->includeFileOnce('/lib/helpers/Groups_BroadcastHelper.php');
            $set = Groups_BroadcastHelper::loadOrCreateProfileSet($group);
            if ($set) {
                $set->addMembers($this->_user->screenName);
            }
        }
    }

    /**
     * Displays the form for editing a group.
     *
     * Expected GET variables:
     *     id - ID of the Group to edit
     */
    public function action_edit($errors = NULL) {
        XG_SecurityHelper::redirectIfNotMember();
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->group = Group::load($_GET['id']);
        if (! Groups_SecurityHelper::currentUserCanEditGroup($this->group)) { xg_echo_and_throw('Not allowed (50619253)'); }
        $this->errors = $errors;
        $activeModules = self::getGroupDisplayOptions($this->group);
        $formArray = array(
                'title' => $this->group->title,
                'description' => $this->group->description,
                'externalWebsiteUrl' => $this->group->my->externalWebsiteUrl ? $this->group->my->externalWebsiteUrl : 'http://',
                'groupPrivacy' => $this->group->my->groupPrivacy,
                'allowInvitations' => $this->group->my->allowInvitations == 'Y' ? 'yes' : null,
                'allowInvitationRequests' => $this->group->my->allowInvitationRequests == 'Y' ? 'yes' : null,
                'groupLocation' => $this->group->my->location,
                'allowMemberMessaging' => $this->group->my->allowMemberMessaging == 'Y' ? 'yes' : null);
            foreach ($activeModules as $module => $value) {
                $formArray[$module . 'Active'] = $value;
            }
        $this->form = new XNC_Form($formArray);
        $this->title = xg_text('EDIT_GROUP_INFO');
        $this->buttonText = xg_text('SAVE');
        $this->formUrl = $this->_buildUrl('group', 'update', array('id' => $this->group->id));
        $this->hideStepLinks = true;
        $this->editingExistingGroup = true;
        $this->cancelUrl = $this->_buildUrl('group','show', array('id' => $this->group->id));
        $this->render('newOrEdit');
    }

    /**
     * Processes the form for editing a group.
     */
    public function action_update() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! XG_SecurityHelper::userIsAdmin()) { XG_JoinPromptHelper::joinGroupOnSave(); }
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->group = Group::load($_GET['id']);
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('edit', 'group', array('id' => $this->group->id)); return; }
        if (! Groups_SecurityHelper::currentUserCanEditGroup($this->group)) { xg_echo_and_throw('Not allowed (1114717372)'); }
        $this->processForm($this->group, 'edit');
    }

    /**
     * Updates the given Group object using the posted form variables, then
     * forwards or redirects to an appropriate page.
     * Redirects to the sign-up page if the person is signed out.
     *
     * @param $group XN_Content|W_Content  The Group to update
     * @param $actionOnError string  Action to forward to if an error occurs
     */
    private function processForm($group, $actionOnError) {
        $group = $group instanceof W_Content ? $group : W_Content::create($group); // BAZ-3303 [Jon Aquino 2007-06-05]
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_MessagingHelper.php');
        XG_HttpHelper::trimGetAndPostValues();
        if (! $this->_user->isLoggedIn()) { xg_echo_and_throw('Not signed in (1205397694)'); }
        $creatingGroup = ! $group->id;
        $errors = array();
        if (! $_POST['title']) { $errors['title'] = xg_text('PLEASE_ENTER_NAME_FOR_GROUP'); }
        if (Group::nameTaken(Group::cleanTitle($_POST['title']), $group->id)) { $errors['title'] = xg_text('NAME_TAKEN'); }
        if (! $group->id) {
            if (! $_POST['url']) { $errors['url'] = xg_text('PLEASE_ENTER_URL_FOR_GROUP'); }
            if (! preg_match('@^[a-z0-9_]*$@iu', $_POST['url'])) { $errors['url'] = xg_text('URL_CAN_CONTAIN_ONLY_LETTERS'); }
            if (Group::urlTaken(Group::cleanUrl($_POST['url']))) { $errors['url'] = xg_text('URL_TAKEN'); }
        }
        if ($_POST['icon_action'] == 'add') {
            if ($_POST['icon:status']) { $errors['icon'] = XG_FileHelper::uploadErrorMessage($_POST['icon:status']); }
            else {
                try {
                    $groupIcon = GroupIcon::create('icon');
                } catch (Exception $e) {
                    $errors['icon'] = xg_text('PROBLEM_WITH_ICON'); // BAZ-3304 [JonAquino 2007-06-07]
                }
            }
        }
        if (!$groupIcon && !Group::hasIcon($group)) {
            $errors['icon'] = xg_text('PLEASE_PROVIDE_GROUP_IMAGE');
        }
        if (count($errors)) {
            $this->forwardTo($actionOnError, 'group', array($errors));
            return;
        }
        $group->title = Group::cleanTitle($_POST['title']);
        $group->description = Group::cleanDescription($_POST['description']);
        if (! $group->id) { $group->my->url = Group::cleanUrl($_POST['url']); }
        if (! $group->id) { $group->my->groupPrivacy = $_POST['groupPrivacy']; }
        if (! $group->id) { $group->my->approved = Groups_SecurityHelper::moderatedForThisUser() ? 'N' : 'Y';}
        $group->my->allowInvitations = Group::isPublic($this->group) || $_POST['allowInvitations'] == 'yes' ? 'Y' : 'N';
        $group->my->allowInvitationRequests = Group::isPublic($this->group) || $_POST['allowInvitationRequests'] == 'yes' ? 'Y' : 'N';
        $group->my->externalWebsiteUrl = Group::cleanExternalWebsiteUrl($_POST['externalWebsiteUrl']);
        $group->my->allowMemberMessaging = $_POST['allowMemberMessaging'] == 'yes' ? 'Y' : 'N';
        $group->my->location = Group::cleanLocation($_POST['groupLocation']);
        if ($groupIcon) { Group::setIcon($group, $groupIcon); }
        $group->save();
        self::setGroupDisplayOptions($group, $_POST);
        if($actionOnError == 'new' && !Groups_SecurityHelper::moderatedForThisUser()){
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_GROUP, $group->contributorName, array($group));
        }
        if ($creatingGroup && Groups_SecurityHelper::moderatedForThisUser()) {
            // Invalidate the approval-link cache
            W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
            Groups_MessagingHelper::groupAwaitingApproval($group);
        }
        if ($creatingGroup && !Groups_SecurityHelper::moderatedForThisUser()) {
            $this->redirectTo('new', 'invitation', array('groupId' => $group->id, 'creatingGroup' => 1));
        } else {
            $this->redirectTo('show', 'group', array('id' => $group->id));
        }
    }

    /**
     * Checks whether the given name or URL has already been taken by an existing group.
     * Case-insensitive.
     *
     * Expected GET parameters:
     *     id - content ID of the current group, or null if the group is new
     *     xn_out - Should always be "json"
     *
     * Expected POST parameters:
     *     title - the name to check
     *     url - the URL to check, or null to skip this check
     */
    public function action_nameOrUrlTaken() {
        if (! $this->_user->isLoggedIn()) { xg_echo_and_throw('Not signed in (819194872)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { xg_echo_and_throw('Not a POST (391349540)'); }
        XG_HttpHelper::trimGetAndPostValues();
        $this->nameTaken = Group::nameTaken(Group::cleanTitle($_POST['title']), $_GET['id']);
        $this->urlTaken = $_POST['url'] ? Group::urlTaken(Group::cleanUrl($_POST['url'])) : false;
    }

    /**
     * Displays the homepage for a specific group
     *
     * Expected GET variables:
     *     id - content ID for the group
     *     invitationRequestSent - "yes" to display a message saying that the invitation request has been sent
     */
    public function action_show() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->group = Group::load($_GET['id']);
        // process unfollows for the group
        if ($_GET['unfollow'] == '1') {
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
            Index_NotificationHelper::stopFollowing($this->group);
        }
        // process invites
        if (Group::userIsInvited($this->group)) {
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
            $this->groupInvitation = Groups_InvitationHelper::getUnusedGroupInvitation($this->group, $this->_user->screenName);
        }
        if (! Groups_SecurityHelper::currentUserCanAccessGroup($this->group)) {
            if ($this->group->my->allowInvitationRequests == "Y") {
                $this->userHasRequested = Groups_SecurityHelper::currentUserHasRequestedMembership($this->group, $this->_user->screenName);
            }
            $this->render('notAllowed', 'group');
            return;
        }
        if ($this->group->my->approved == 'N') {
            if (!Groups_SecurityHelper::currentUserCanAccessUnapprovedGroup($this->group)) {
                $this->error = array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                             'subtitle'    => '',
                             'description' => xg_text('THAT_GROUP_IS_AWAITING_APPROVAL'));
                $this->render('error', 'index');
            } else {
                $this->render('notApproved', 'group');
            }
            return;
        }
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
        XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
        //TODO in most other places we call this $xgLayout and xgLayout->getLayout is call $layout.
        // Could change here and in associated template.
        $this->layout = XG_Layout::load($this->group->id, 'groups');

        $layout = $this->layout->getLayout();
        $xpath = new DOMXPath($layout);
        $nodeList = $xpath->query('colgroup')->item(0);
        // old group layout XML? if so, update it and save it on the fly
        // TODO: conside moving this into a helper or private function
        if (count($nodeList) != 0) {
            $previousLayout = $this->layout->getLayout();
            $xpathPrev = new DOMXPath($previousLayout);
            $this->layout->updateGroupsLayout();
            $updates = array(array('//module[@widgetName="html"]/title','2'),array('//module[@widgetName="html"]/html','2'),
                            array('//module[@widgetName="forum"]/topicSet','3'),array('//module[@widgetName="forum"]/itemCount','3'));
            foreach ($updates as $updatePair) {
                $xpathUpdate = $xpathPrev->query($updatePair[0]);
                if ($xpathUpdate->length) {
                    $node = $xpathUpdate->item(0);
                    $this->layout->importElement($node, '/layout/column/column/module[' . $updatePair[1] .']');
                }
            }
        }
        $this->enabledModules = XG_ModuleHelper::getEnabledModules();
        $this->disabledModules = XG_ModuleHelper::getDisabledModules();
        $this->layoutName = $this->layout->getName();
        $this->layoutType = $this->layout->getType();
        if ($this->_user->screenName == 'NingDev' && ! in_array($this->_widget, $this->enabledModules, true)) { echo '<p style="color:black; background:yellow">Groups module is not enabled</p>'; }
        // process welcome
        if ($this->_user->isLoggedIn()) {
            $groupMembership = GroupMembership::loadOrCreate($this->group, $this->_user->screenName);
            if (Group::userIsMember($this->group) && $groupMembership->my->welcomed == 'N') {
                W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_MessagingHelper.php');
                Groups_MessagingHelper::notifyNewActivityFollowers($groupMembership, $this->group);
                $this->showWelcome = true;
                $groupMembership->my->welcomed = 'Y';
                $groupMembership->save();
            }
        }
        if ($_GET['test_welcome']) { $this->showWelcome = true; }
        if ($this->_user->isLoggedIn()) {
            // process banned
            if (Group::userIsBanned($this->group,$this->_user->screenName) && !XG_SecurityHelper::userIsAdmin()) {
                $this->userIsBanned = true;
            }
        }
        if ($_GET['test_banned']) { $this->userIsBanned = true; }
    }

    /**
     * Prepares the fields for an action that lists groups, and sets the template to render. Sets $this->title, $this->pageSize,
     * $this->groups, $this->totalCount and $this->pagePickerOptions.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - name of the current sort (optional), e.g., mostRecent
     *     q - search keywords (optional)
     *
     * @param $pageTitle string  Text for the page title
     * @param $titleHtml string|array  HTML for the page heading, or an array of HTML titles keyed by sort name
     * @param $sortNames array  Names of sorts to display in the combobox
     * @param $user string  (optional) Screen name of the person who started the groups
     * @param $useSearch boolean Whether to use a Search query (or not, use a Content query)
     */
    private function prepareListAction($args) {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        extract($args);
        $useSearch = isset($useSearch) ? $useSearch : false;
        $currentSortName = $_GET['sort'] ? $_GET['sort'] : $sortNames[0];
        $this->titleHtml = is_array($titleHtml) ? $titleHtml[$currentSortName]: $titleHtml;
        $this->pageTitle = mb_strlen($pageTitle) ? $pageTitle : xg_text('GROUPS');
        $this->pageSize = 20;
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);


        if ($useSearch) {
            $query = XN_Query::create('Search');
        }
        else {
            // Don't cache search queries as there are an infinite number of search terms but a finite number of cache files [Jon Aquino 2007-02-09]
            $query = ($_GET['q'] || (! XG_Cache::cacheOrderN())) ? XN_Query::create('Content') : XG_Query::create('Content');
        }
        $query->begin($begin);
        $query->end($begin + $this->pageSize);
        if ($_GET['q']) {
            XG_QueryHelper::addSearchFilter($query, $_GET['q'], $useSearch);
            XG_QueryHelper::addExcludeFromPublicSearchFilter($query, $useSearch);
        }
        // TODO: Pass $location as a parameter, and document [Jon Aquino 2008-02-07]
        if ($_GET['location']) {
            $query->filter('my->location','=',trim(urldecode($_GET['location'])));
        }
        if ($useSearch) {
            // TODO: Move this code to action_search [Jon Aquino 2008-02-07]
            try {
                $searchResults = Groups_Filter::get('search')->execute($query, $user);
                $this->groups = XG_QueryHelper::contentFromSearchResults($searchResults, false);
                $this->totalCount = $query->getTotalCount();
                /* If we're on the first page and all of the search results have been excluded because the
                 * matching content doesn't exist any more, then just pretend there are no results */
                if (($this->totalCount > 0) && (count($this->groups) == 0) && ($begin ==0)) {
                    $this->totalCount = 0;
                }
           } catch (Exception $e) {
                /* If the search endpoint didn't work, log it and pretend there's no results */
                error_log("Group search query ({$_GET['q']}) failed with: " . $e->getCode());
                $this->groups = array();
                $this->totalCount = 0;
            }
        }
        else {
            list($this->groups, $this->totalCount) = $this->executeQuery($currentSortName, $query, $user);
        }
        XG_Cache::profiles($this->groups, $user);
        $this->pagePickerOptions = self::pagePickerOptions($sortNames, $currentSortName, $user);
        // use a separate template for search results since we want to display multiple content types.
        if (! $_GET['q']) {
            $this->render('list');
        }
    }

    /**
     * Filters and sorts Groups. Takes care of filtering the
     * owner, type, mozzle and contributorName; alwaysReturnTotalCount is turned on.
     * If you pass in an XG_Query without invalidation keys specified, basic type-invalidation
     * keys will be added automatically.
     *
     * @param string $filterName  Name of the filter: mostRecent, mostPopular
     * @param XN_Query|XG_Query $query  The query to filter
     * @param string $user  Username to filter on (optional)
     * @return array  Group objects and the total count
     */
    protected function executeQuery($filterName, $query, $user) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (! XG_PromotionHelper::areQueriesEnabled() && $filterName == 'promoted') { return array(array(), 0); }
        return array(Groups_Filter::get($filterName)->execute($query, $user), $query->getTotalCount());
    }

    /**
     * Returns metadata for the given sorts, for initializing the combobox.
     *
     * @param $sortNames array  Names of sorts to display in the combobox
     * @param $currentSortName string  Name of the current sort
     * @param $username string  Username that will be filtered on (optional)
     * @return list
     */
    private function pagePickerOptions($sortNames, $currentSortName, $username) {
        $pagePickerOptions = array();
        foreach ($sortNames as $sortName) {
            $pagePickerOptions[] = array(
                    'displayText' => Groups_Filter::get($sortName)->getDisplayText($username),
                    'url' => XG_HttpHelper::addParameter(XG_HttpHelper::currentUrl(), 'sort', $sortName),
                    'selected' => $sortName == $currentSortName);
        }
        return $pagePickerOptions;
    }

    /**
     * Updates the group's title
     *
     * Expected GET variables:
     *     id - ID of the Group to edit
     *     xn_out - Should always be "json"
     *
     * Expected POST variables:
     *     title - the new title
     */
    public function action_setTitle() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $group = Group::load($_GET['id']);
        if (! Groups_SecurityHelper::currentUserCanEditGroup($group)) { xg_echo_and_throw('Not allowed (1516649943)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { xg_echo_and_throw('Not a POST'); }
        if (! Group::nameTaken(Group::cleanTitle($_POST['value']))) {
            $group->title = Group::cleanTitle($_POST['value']);
            $group->save();
        }
        $this->html = xnhtmlentities($group->title);
    }

    /**
     * Deletes the group, then redirects to the Groups homepage
     *
     * Expected GET variables:
     *     id - ID of the Group object to delete
     */
    public function action_delete() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        $group = Group::load($_GET['id']);
        if (! Groups_SecurityHelper::currentUserCanDeleteGroup($group)) { xg_echo_and_throw('Not allowed (1200345593)'); }
        $groupObjects = XN_Query::create('Content')->filter('owner')->filter('my.groupId', '=', $_GET['id'])->end(15)->execute();
        $groupUrl = $group->my->url;
        if (count($groupObjects) < 10) {
            // Trivial group - just delete everything in it now. This reduces the number of
            // deleted IDs we filter out with XG_QueryHelper::addNotInFilter  [Jon Aquino 2007-04-24]
            XN_Content::delete(array_merge($groupObjects, array($group)));
        } else {
            $group->my->deleted = 'Y';
            $group->my->excludeFromPublicSearch = 'Y';
            $group->isPrivate = true; // Exclude from Ning search results [Jon Aquino 2007-04-25]
            $group->save();
            $this->_widget->privateConfig['groupsMarkedAsDeleted'] = trim($this->_widget->privateConfig['groupsMarkedAsDeleted'] . ' ' . $group->id);
            $this->_widget->saveConfig();
        }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        XN_ProfileSet::delete(Index_NotificationHelper::groupLabel($_GET['id']));
        XG_Query::invalidateCache(Group::groupChangedInvalidationKey($groupUrl));
        $this->redirectTo('index', 'index');
    }

    /**
     * Displays the forum homepage.
     */
    public function action_forum() {
        // Get here because of the following URL mapping: http://devbazjon.ning.com/group/MyCoolGroup/forum
        // See XG_GroupHelper::buildUrl  [Jon Aquino 2007-05-02]
        W_Cache::getWidget('forum')->dispatch('index', 'index');
    }

    /**
     * Removes or updates any activity log items related to group membership when someone leaves a group.
     *
     * @param W_Content || XN_Content the group
     * @param string the screenName of the user
     */
     private function deleteOrUpdateActivityLog($group, $screenName) {
         XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
         try {
             $result = XG_ActivityHelper::getUserActivityLog($screenName, 0, 100, null, $group->id);
             foreach($result['items'] as $item){
                 if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_GROUP && $item->my->category == XG_ActivityHelper::CATEGORY_CONNECTION) {
                     $contents = explode(',', $item->my->contents);
                     if(count($contents) <= 1) {
                         XN_Content::delete($item);
                     } else {
                         $contents = array_diff($contents,array($group->id));
                         $item->my->contents = implode(',', $contents);
                         $item->save();
                     }
                 }
             }
         } catch (Exception $e) {
            // exceptions here should not stop processing
         }
     }

    /**
     * Retrieves the group detail options for the modules to show.
     * Used inside XG_Message_Group_Invitation.
     *
     * @param W_Content the group
     * @return $activeModules array of named options which are on.
     */
     public function getGroupDisplayOptions($group) {
        $activeModules = array('html'=>'','forum'=>'','feed'=>'','groups'=>'');
        $xglayout = XG_Layout::load($group->id, 'groups');
        $layout = $xglayout->getLayout();
        $xpath = new DOMXPath($layout);
        $xPathActive = $xpath->query('//module[@isActive="1"]');
        foreach ($xPathActive as $module) {
            $activeModules[$module->getAttribute('widgetName')] = 'yes';
        }
        return $activeModules;
     }

     /**
      * Retrieves the group detail options for the modules to show.
      *
      * @param W_Content the group
      * @return void
      */
    private function setGroupDisplayOptions($group, $_POST) {
        $activeModules = array('html'=>'','forum'=>'','feed'=>'','groups'=>'');
        $xglayout = XG_Layout::load($group->id, 'groups');
        $layout = $xglayout->getLayout();
        $xpath = new DOMXPath($layout);
        $xPathActive = $xpath->query('//module[@isActive]');
        foreach ($xPathActive as $module) {
            $isActive = $_POST[$module->getAttribute('widgetName') . 'Active'] == 'yes' ? true : false;
            if ($isActive) {
                $module->setAttribute('isActive','1');
            } else {
                $module->setAttribute('isActive','0');
            }
        }
      }

    /**
     * Denormalizes a batch of User fullNames to their GroupMembership objects.  Schedules an XN_Job to continue the work if it is not complete on this run.
     *
     * This action is designed to be the endpoint of an XN_Job.
     */
    public function action_denormalizeFullName() {
        header('HTTP/1.0 500 Internal Error');
        $json = new NF_JSON();
        error_log('denormalizeFullName: ' . XG_HttpHelper::currentUrl() . ' ' . $json->encode($_POST));
        XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        $remaining = GroupMembership::denormalizeFullName();
        if ($remaining !== 0 && XG_JobHelper::allowChaining()) {
            GroupMembership::scheduleDenormalizeFullName();
        }
        header('HTTP/1.0 200 OK');
    }

    /**
     * Endpoint for an XN_Job to set my->fullName attributes of a user's GroupMemberships.  Useful when a user belongs to so many groups that
     * it cannot be done while they wait.
     *
     * Expected $_POST vars:
     *   screenName username of the user in question.
     *   fullName   full name to update GroupMemberships with.
     */
    public function action_setFullName() {
        header('HTTP/1.0 500 Internal Error');
        // If further calls are required, they are set up in setFullName.
        GroupMembership::setFullName($_POST['screenName'], $_POST['fullName']);
        header('HTTP/1.0 200 OK');
    }


    /**
     * Provides a simple way to ensure that groups appear at the top of Group search results
     *
     * Expected $_POST vars:
     *   screenName username of the user in question.
     *   fullName   full name to update GroupMemberships with.
     */
    private function prioritizeGroupsInResults($results) {
        $groups = array();
        $nonGroups = array();
        foreach ($results as $result) {
            if ($result->type === 'Group') {
                $groups[] = $result;
            } else {
                $nonGroups[] = $result;
            }
        }
        return array_merge($groups, $nonGroups);
    }

    //TODO test for setFullName with more than 50 group mems
    //TODO test for denorm full name with more than 50 group mems
    /**
     *  Renders search result item.
     *
     *  @param	$object	XN_Content	Item to render
     *  @return	void
     */
    public function _renderSearchResult ($args) {
        $object = $args['object'];
        switch ($object->type) {
            case 'Group':
                $args['group'] = $object;
                W_Content::create($object)->render('listItem', $args);
                break;
            case 'Comment':
                $this->renderPartial('fragment_comment', 'group', array('object' => $object, 'group' => $this->groupIds[$object->my->groupId]));
                break;
        }
    }

}
