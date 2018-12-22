<?php

class Index_MembershipController extends W_Controller {

    protected static $profileQuestionTypes;
    protected static $nonregVisibilityChoices;

    public function __construct(W_BaseWidget $widget) {
        parent::__construct($widget);
        self::$profileQuestionTypes = array(
                'text' => xg_text('ONE_LINE_ANSWER'),
                'textarea' => xg_text('LONGER_ANSWER'),
                'select' => xg_text('MULTIPLE_CHOICE'),
                'date' => xg_text('DATE'),
                'url' => xg_text('WEBSITE_ADDRESS'));
        self::$nonregVisibilityChoices = array(
                'everything' => xg_text('EVERYTHING'),
                'homepage' => xg_text('JUST_THE_HOMEPAGE'),
                'message' => xg_text('JUST_THE_SIGN_UP_PAGE'));
    }

    public function action_questions() {
        XG_SecurityHelper::redirectIfNotAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            /* @todo: validate */
            $this->forwardTo('saveQuestions');
            return;
        }
        if (! XG_App::appIsLaunched()) { return $this->redirectTo($this->_buildUrl('admin', 'launch')); } // BAZ-7608 [Jon Aquino 2008-05-13]

        $profilesWidget = W_Cache::getWidget('profiles');

        $profilesWidget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Index_ProfileInfoFormHelper.php');
        $this->form = new XNC_Form(array(
                'showGenderFieldOnCreateProfilePage' => Index_ProfileInfoFormHelper::isShowingGenderFieldOnCreateProfilePage(),
                'showLocationFieldOnCreateProfilePage' => Index_ProfileInfoFormHelper::isShowingLocationFieldOnCreateProfilePage(),
                'showCountryFieldOnCreateProfilePage' => Index_ProfileInfoFormHelper::isShowingCountryFieldOnCreateProfilePage()));

        // How many question blanks to display by default?
        $this->profileQuestions = Profiles_ProfileQuestionHelper::getQuestions($profilesWidget);
        $this->initialQuestionCount = count($this->profileQuestions);
        if ($this->initialQuestionCount == 0) { $this->initialQuestionCount = 3; }

        // If we're in the prelaunch ('gyo') sequence, various display elements
        // are different than manage (post-launch) mode
        $this->manage = XG_App::appIsLaunched();
        $this->displayPrelaunchButtons = !XG_App::appIsLaunched();
        if ($this->displayPrelaunchButtons) {
            $this->backLink = XG_App::getPreviousStepUrl();
            $this->nextLink = XG_App::getNextStepUrl();
        }
    }

    public function action_saveQuestions() {
        XG_SecurityHelper::redirectIfNotAdmin();

        $profilesWidget = W_Cache::getWidget('profiles');
        $profilesWidget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
        // When examining the User shape for the largest question counter,
        // ensure that it is at least as high as the largest question counter
        // in profiles/widget-configuration.xml (BAZ-7041) [Jon Aquino 2008-04-07]
        $minCounter = Profiles_ProfileQuestionHelper::maxCounter(Profiles_ProfileQuestionHelper::getQuestions($profilesWidget));
        // @todo should questions be private in a private app?
        $existingQuestions = array();
        foreach (Profiles_ProfileQuestionHelper::getQuestions($profilesWidget) as $question) {
            $existingQuestions[$question['questionCounter']] = $question;
        }
        $submittedQuestions = Profiles_ProfileQuestionHelper::prepareQuestionsFromSubmittedArray($_POST);
		$changed = ( count($existingQuestions) != count($submittedQuestions) );
        $deleted = ( count($existingQuestions) >  count($submittedQuestions) );
        foreach ($submittedQuestions as $position => $question) {
			if (!$changed) { // check until the first change.
				if ( !mb_strlen($question['questionCounter']) ) {
					$changed = true;
				} else {
					$sample = $existingQuestions[$question['questionCounter']];
					$sample['position'] = $question['position'];
					$changed = ($sample != $question);
				}
			}
            /* If the question is new or not compatible with the existing version, get a new
             * question counter */
             if ((! mb_strlen($question['questionCounter'])) ||
                 (! Profiles_ProfileQuestionHelper::areQuestionsCompatible($question, $existingQuestions[$question['questionCounter']]))) {
             $submittedQuestions[$position]['questionCounter'] = Profiles_ProfileQuestionHelper::updateUserShapeForQuestion($question, $profilesWidget, $minCounter);
            }
        }
        Profiles_ProfileQuestionHelper::putQuestions($profilesWidget, $submittedQuestions);

        $this->_widget->config['showGenderFieldOnCreateProfilePage'] = $_POST['showGenderFieldOnCreateProfilePage'] ? '1' : '0';
        $this->_widget->config['showLocationFieldOnCreateProfilePage'] = $_POST['showLocationFieldOnCreateProfilePage'] ? '1' : '0';
        $this->_widget->config['showCountryFieldOnCreateProfilePage'] = $_POST['showCountryFieldOnCreateProfilePage'] ? '1' : '0';
        $this->_widget->saveConfig();
        $profilesWidget->saveConfig();

        try {
            if($changed && XG_App::appIsLaunched() && !$deleted){
                XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
                XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NETWORK, XG_ActivityHelper::SUBCATEGORY_MESSAGE_QUESTIONS_UPDATE);
            }
        } catch (Exception $e) {
            error_log("Couldn't generate actiovity log item after profile question change: " . $e->getMessage());
        }
        /* Since (potentially) new profile questions have been saved, it's time to
         * update the searchability of the User object to take that into account */
        XG_App::includeFileOnce('/lib/XG_ShapeHelper.php');
        try {
            XG_ShapeHelper::setStandardIndexingForModel('User');
        } catch (Exception $e) {
            error_log("Couldn't update User searchability after profile question change: " . $e->getMessage());
        }
        if (! XG_App::appIsLaunched()) { return $this->redirectTo($this->_buildUrl('admin', 'launch')); } // BAZ-7608 [Jon Aquino 2008-05-13]
        //  Check for an explicit success target (e.g. launch)
        if (isset($_POST['successTarget']) && mb_strlen(trim($_POST['successTarget'])) > 0) {
            header('Location: ' . $_POST['successTarget']);
            exit;
        }
        else {
            $this->redirectTo('questions', 'membership', '?saved=1');
        }
    }

    public function action_getQuestion() {
        if (!XG_SecurityHelper::userIsAdmin()) {
            throw new XN_Exception("Not allowed");
        }
        $this->counter = isset($_GET['counter']) ? (integer) $_GET['counter'] : 1;
    }

    public function action_list() {
        $this->redirectTo('listMembers', 'membership');
    }

    public function action_listMembers() {
        XG_SecurityHelper::redirectIfNotAdmin();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $this->statusSortable = (Index_MembershipHelper::addMemberStatus() === 0);
        $this->setupPagination();
        $filters = $this->searchFilters();
        if (!$_GET['sort']) {
            $_GET['sort'] = 'date_d';
        }
        list($sortBy, $sortOrder, $sortType) =  XG_QueryHelper::sortOrder($_GET['sort']);
        $this->memberInfo = User::find($filters, $this->start, $this->end,
                array($sortBy, $sortType), $sortOrder, TRUE);
        // TODO: Eliminate $screenNames and for loop by passing User objects directly into XG_Cache::profiles()  [Jon Aquino 2007-10-29]
        $screenNames = array();
        foreach ($this->memberInfo['users'] as $user) {
            $screenNames[$user->title] = $user->title;
        }
        $this->memberProfiles = XG_Cache::profiles($screenNames);
        $this->tabs = $this->tabs();
    }

    public function action_listAdministrators() {
        XG_SecurityHelper::redirectIfNotAdmin();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $this->statusSortable = (Index_MembershipHelper::addMemberStatus() === 0); //TODO repeated code, share somehow
        $this->setupPagination();
        $filters = array_merge($this->searchFilters(), array('admin' => true));
        list($sortBy, $sortOrder, $sortType) = XG_QueryHelper::sortOrder($_GET['sort']);
        $this->administratorInfo = User::find($filters, $this->start, $this->end, array($sortBy, $sortType), $sortOrder, TRUE);
        $this->administratorProfiles = XG_Cache::profiles($this->administratorInfo['users']);
        $this->tabs = $this->tabs();
    }

    public function action_listBanned() {
        XG_SecurityHelper::redirectIfNotAdmin();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $this->statusSortable = (Index_MembershipHelper::addMemberStatus() === 0);
        $this->setupPagination();
        $filters = array_merge($this->searchFilters(), array('blocked' => TRUE));
        list($sortBy, $sortOrder, $sortType) = XG_QueryHelper::sortOrder($_GET['sort']);
        $this->bannedInfo = User::find($filters, $this->start, $this->end,
                array($sortBy, $sortType), $sortOrder, TRUE /* cache */);
        if ($this->bannedInfo['numUsers'] == 0 && $_GET['q'] == '') {
            $opts = array();
            if ($_GET['saved']) {
                $opts['saved'] = 1;
            }
            $this->redirectTo('listMembers', 'membership', $opts);
            return;
        }
        // TODO: Eliminate $screenNames and for loop by passing User objects directly into XG_Cache::profiles()  [Jon Aquino 2007-10-29]
        $screenNames = array();
        foreach ($this->bannedInfo['users'] as $user) {
            $screenNames[$user->contributorName] = $user->contributorName;
        }
        $this->bannedProfiles = XG_Cache::profiles($screenNames);
        $this->tabs = $this->tabs();
    }

    /**
     * Displays people who have been invited to this network.
     *
     * Expected GET parameters:
     *     resendCount - number of invitations that were re-sent
     */
    public function action_listInvited() {
        XG_SecurityHelper::redirectIfNotAdmin();
        $this->setupPagination();
        $filters = array();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $invitations = Index_InvitationHelper::getUnusedInvitations($filters, $this->start, $this->end, $totalNumInvites, $profiles);
        $this->totalNumInvites = $totalNumInvites;
        if ($this->totalNumInvites == 0 && $_GET['q'] == '') { return $this->redirectTo('listMembers', 'membership'); }
        $this->users = array();
        XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
        foreach ($invitations as $invitation) {
            $this->users[] = array(
                    'name' => $invitation['displayName'],
                    'ningId' => $invitation['screenName'],
                    'email' => $invitation['emailAddress'],
                    'checkboxName' => 'inv_' . $invitation['id'],
                    'date' => $invitation['date'],
                    'statusHtml' => '<td><div class="invited">' . xg_html('INVITED_BY_X', xg_userlink($profiles[$invitation['inviter']])) . '</div></td>',
                    'status' => XG_MembershipHelper::INVITED);
        }
        $this->tabs = $this->tabs();
        $this->resendCount = intval($_GET['resendCount']);
        $this->cancelCount = intval($_GET['cancelCount']);
    }

    public function action_listRequested() {
        XG_SecurityHelper::redirectIfNotAdmin();

        if (!XG_App::appIsPrivate() || !XG_App::allowInviteRequests()) {
            $this->redirectTo('listMembers', 'membership');
            return;
        }
        $this->setupPagination();
        $this->searchable = false;
        $filters = $this->searchFilters();
        $requestInfo = InvitationRequest::find($filters, $this->start,
                $this->end, 'createdDate', 'desc', FALSE /* cache */);
        $this->requests = $requestInfo['requests'];
        $this->totalNumRequests = $requestInfo['numRequests'];
        if ($this->totalNumRequests == 0 && $_GET['q'] == '') {
            $opts = array();
            if ($_GET['saved']) {
                $opts['saved'] = 1;
            }
            $this->redirectTo('listMembers', 'membership', $opts);
            return;
        }
        $screenNames = array();
        if (count($this->requests)) {
            foreach ($this->requests as $req) {
                if (InvitationRequest::requestedByNingId($req)) {
                    $screenNames[preg_replace("/[^a-zA-Z0-9s]/u", "", $req->my->requestor)] = $req->my->requestor;
                }
            }
            $this->profiles = XG_Cache::profiles($screenNames);
        }
        $this->tabs = $this->tabs();
    }

    public function action_listPending() {
        XG_SecurityHelper::redirectIfNotAdmin();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $this->setupPagination();
        $this->statusSortable = (Index_MembershipHelper::addMemberStatus() === 0);
        $filters = array_merge($this->searchFilters(), array('pending' => TRUE));
        list($sortBy, $sortOrder, $sortType) = XG_QueryHelper::sortOrder($_GET['sort']);
        $this->pendingInfo = User::find($filters, $this->start, $this->end,
                array($sortBy, $sortType), $sortOrder, TRUE /* cache */);
        if ($this->pendingInfo['numUsers'] == 0 && $_GET['q'] == '') {
            $opts = array();
            if ($_GET['saved']) {
                $opts['saved'] = 1;
            }
            $this->redirectTo('listMembers', 'membership', $opts);
            return;
        }
        $screenNames = array();
        $this->pendingUsers = array();
        foreach ($this->pendingInfo['users'] as $user) {
            $screenNames[$user->title] = $user->title;
            $this->pendingUsers[$user->title] = $user;
        }
        $this->pendingProfiles = XG_Cache::profiles($screenNames);
        $this->tabs = $this->tabs();

        $this->extraColumns = array('viewProfile' => true);
    }


    public function action_saveMembers() {
        //  Currently, this method only adds and removes administrators
        //  So only the owner should be allowed to use it, not admins
        XG_SecurityHelper::redirectIfNotOwner();

        $operation = $_POST['operation'];
        $idsToProcess = array();
        foreach ($_POST as $name => $value) {
            if (mb_substr($name, 0, 5) == 'user_') {
                $idsToProcess[] = mb_substr($name, 5);
            }
        }
        $filters = array('contributorName' => array('in', $idsToProcess));
        $userInfo = User::find($filters, 0, 100);
        foreach ($userInfo['users'] as $user) {
            error_log('Setting admin status for ' . $user->contributorName . ' to ' . ($operation == 'promote' ? 'TRUE' : 'FALSE'));
            User::setAdminStatus($user, ($operation == 'promote'));
            $user->save();
        }

        $this->redirectTo('listMembers', 'membership', array('page' => $_POST['page'], 'q' => $_POST['q'], 'saved' => 1));
    }

    /**
     * Processes the Administrators form.
     */
    public function action_saveAdministrators() {
        // Currently, this method only removes administrators.
        // So only the owner should be allowed to use it, not admins
        XG_SecurityHelper::redirectIfNotOwner();
        $operation = $_POST['operation'];
        $idsToProcess = array();
        foreach ($_POST as $name => $value) {
            if (mb_substr($name, 0, 5) == 'user_') {
                $idsToProcess[] = mb_substr($name, 5);
            }
        }
        $userInfo = User::find(array('contributorName' => array('in', $idsToProcess)), 0, 100);
        foreach ($userInfo['users'] as $user) {
            if ($operation == 'demote' && $user->title != XN_Application::load()->ownerName) {
                User::setAdminStatus($user, false);
                $user->save();
            }
        }
        $this->redirectTo('listAdministrators', 'membership', array('page' => $_POST['page'], 'saved' => 1));
    }

    public function action_saveBanned() {
        XG_SecurityHelper::redirectIfNotAdmin();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');

        $idsToUnblock = array();
        foreach ($_POST as $name => $value) {
            if (mb_substr($name, 0, 5) == 'user_') {
                $idsToUnblock[] = mb_substr($name, 5);
            }
        }
        foreach (XN_Content::load($idsToUnblock) as $user) {
            User::setStatus($user, '');
            User::clearBannedMessageCounter($user);
            $user->save();

            //  Add the user to the site broadcast list unless he's chosen not
            //    to receive site broadcast messages (he was removed when banned)
            if ($user->my->emailSiteBroadcastPref !== 'N') {
                if ($set = XN_ProfileSet::load(Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME)) {
                    $set->addMembers($user->contributorName);
                }
            }
        }
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        XG_EmbeddableHelper::generateResources();
        $this->redirectTo('listBanned', 'membership', array('page' => $_POST['page'], 'saved' => 1));
    }

    public function action_saveInvited() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        XG_SecurityHelper::redirectIfNotAdmin();
        $idsToProcess = array();
        foreach ($_POST as $name => $value) {
            if (mb_substr($name, 0, 4) == 'inv_') {
                $idsToProcess[$name] = mb_substr($name, 4);
            }
        }
        if ($_POST['operation'] == 'cancel') { 
            Index_InvitationHelper::deleteUnusedInvitations($idsToProcess); 
            $cancelCount = count($idsToProcess);
        }
        if ($_POST['operation'] == 'resend') {
            Index_InvitationHelper::resendUnusedInvitations($idsToProcess);
            $resendCount = count($idsToProcess);
        }
        $this->redirectTo('listInvited', 'membership', array(
	    'page' => $_POST['page'],
	    'resendCount' => $resendCount,
	    'cancelCount' => $cancelCount,
	    'saved' => 1,
	));
    }

    public function action_saveRequested() {
        XG_SecurityHelper::redirectIfNotAdmin();

        $idsToProcess = array();
        foreach ($_POST as $name => $value) {
            if (mb_substr($name, 0, 4) == 'req_') {
                $idsToProcess[] = mb_substr($name, 4);
            }
        }
        foreach (XN_Content::load($idsToProcess) as $req) {
            if ($_POST['operation'] == 'invite') {
                throw new Exception('Unsupported operation (779423558)');
            }
            XN_Content::delete($req);
        }

        $this->redirectTo('listRequested', 'membership', array('page' => $_POST['page'], 'saved' => 1));
    }

    public function action_savePending() {
        XG_SecurityHelper::redirectIfNotAdmin();

        $this->_widget->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $idsToProcess = array();
        foreach ($_POST as $name => $value) {
            if (mb_substr($name, 0, 5) == 'user_') {
                $id = mb_substr($name, 5);
                $idsToProcess[$id] = $id;
            }
        }
        $profiles = XG_Cache::profiles($idsToProcess);
        foreach ($profiles as $screenName => $profile) {
            $user = User::load($screenName);
            if ($_POST['operation'] == 'accept') {
                Index_MembershipHelper::onAccept($profile, $user);
            }
            else if ($_POST['operation'] == 'decline') {
                // TODO: Move this code into Index_MembershipHelper::onDecline(), to be consistent [Jon Aquino 2007-12-03]
                /* If a user is declined, it's as if they never applied */
                XN_Content::delete(W_Content::unwrap($user));
                /* Remove them from the USERS alias (BAZ-4606) */
                try {
                    $set = XN_ProfileSet::load(XN_ProfileSet::USERS);
                    if ($set) { $set->removeMember($screenName); }
                } catch (Exception $e) {
                }
            }
        }

        $this->redirectTo('listPending', 'membership', array('page' => $_POST['page'], 'saved' => 1));
    }

    protected function getMemberCounts() {
        $counts = array();
        $userInfo = User::find(array(), 0, 1, NULL, NULL, TRUE /* cache */);
        $counts['members'] = $userInfo['numUsers'];
        $userInfo = User::find(array('admin' => TRUE), 0, 1, NULL, NULL, TRUE /* cache */);
        $counts['administrators'] = $userInfo['numUsers'];
        $userInfo = User::find(array('blocked' => TRUE), 0, 1, NULL, NULL, TRUE /* cache */);
        $counts['banned'] = $userInfo['numUsers'];
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        Index_InvitationHelper::getUnusedInvitations(array(), 0, 1, $totalCount, $profiles);
        $counts['invited'] = $totalCount;
        if (XG_App::appIsPrivate() && XG_App::allowInviteRequests()) {
            $requestInfo = InvitationRequest::find(array(), 0, 1, NULL, NULL, FALSE /* cache */);
            $counts['requested'] = $requestInfo['numRequests'];
        } else {
            $counts['requested'] = 0;
        }
        $userInfo = User::find(array('pending' => TRUE), 0, 1, NULL, NULL, TRUE /* cache */);
        $counts['pending'] = $userInfo['numUsers'];
        return $counts;
    }

    protected function setupPagination() {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->pageSize = 50;
        // Pages start at 1, not 0
        $this->page = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        $this->q = (isset($_GET['q']) ? $_GET['q'] : null);
        if ($this->page < 1) { $this->page = 1; }
        $this->start = ($this->page - 1) * $this->pageSize;
        $this->end = $this->start + $this->pageSize;
    }

    /**
     * Displays tabs for the member-management page. Called by the index widget and the groups widget.
     *
     * @param $currentTab string  text of the current tab
     * @param $tabs array  array of properties for each tab: text, count, url
     * @param $groupInviteLink string	group invite URI, if not provided the invite link will be the main invite link
     */
    public function action_tabs($currentTab, $tabs, $groupInviteLink = null) {
        $this->currentTab = $currentTab;
        $this->tabs = $tabs;
	$this->groupInviteLink = $groupInviteLink;
    }

    /**
     * Displays a table of users. Called by the index widget and the groups widget.
     *
     * @param $dateTitle string  title for the date column
     * @param $users array  array of properties for each user:
     *     name - text for the Name column
     *     profileUrl - URL of the person's profile page, or null to suppress linking to it
     *     ningId - the person's Ning ID, if available
     *     checkboxName - name for the checkbox, or null to hide the checkbox
     *     checkboxValue - value for the checkbox (defaults to "on")
     *     date - value for the date column, either in seconds since the epoch or ISO-8601 format
     *     statusHtml - HTML for the <td> element
     * @param $extraColumns array optional array of extra columns to display
     */
    public function action_table($dateTitle, $users, $extraColumns = array()) {
        $widget = W_Cache::getWidget('main');
        $this->showEmails = XG_SecurityHelper::userIsAdmin(XN_Profile::current()) || $widget->config['allowGroupAdminsEmailAccess'] == 'Y';
        $this->dateTitle = $dateTitle;
        $this->users = $users;
        $this->extraColumns = $extraColumns;
        $ningIds = array();
        foreach ($users as $user) {
            if ($user['ningId']) { $ningIds[] = $user['ningId']; }
        }
        XG_Cache::profiles($ningIds); // Prime the cache [Jon Aquino 2007-04-30]
    }

    /**
     * Serves up the memberdata that has been previously exported
     * @see Index_BulkController::action_exportMemberData()
     */
    public function action_downloadMemberData() {
        if (! XG_SecurityHelper::userIsAdmin()) {
            throw new Exception("Permission denied.");
        }
        $this->_widget->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
        $file = Index_MembershipHelper::memberDataExportFile();
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . basename($file).'"');
        @readfile($file);
    }

    /**
     * Add a memberStatus property to a batch of users that do not have it already, and schedule an asynchronous job to do the next batch if any left.
     *
     * This action is designed to be the endpoint of an XN_Job.
     */
    public function action_addMemberStatus() {
        header('HTTP/1.0 500 Internal Error');
        XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
        $remaining = Index_MembershipHelper::addMemberStatus();
        if ($remaining !== 0 && XG_JobHelper::allowChaining()) {
            Index_MembershipHelper::scheduleAddMemberStatus();
        }
        header('HTTP/1.0 200 OK');
    }

    /**
     * Converts the current member counts to an array of tab properties.
     *
     * @return array  array of properties for each tab: text, count, url
     */
    private function tabs() {
        $counts = $this->getMemberCounts();
        return array(
                array('text' => xg_text('MEMBERS'), 'count' => $counts['members'], 'url' => $this->_widget->buildUrl('membership', 'listMembers')),
                array('text' => xg_text('ADMINISTRATORS'), 'count' => $counts['administrators'], 'url' => $this->_widget->buildUrl('membership', 'listAdministrators')),
                array('text' => xg_text('PENDING'), 'count' => $counts['pending'], 'url' => $this->_widget->buildUrl('membership', 'listPending')),
                array('text' => xg_text('INVITED'), 'count' => $counts['invited'], 'url' => $this->_widget->buildUrl('membership', 'listInvited')),
                array('text' => xg_text('REQUESTED_INVITE'), $counts['requested'], 'url' => $this->_widget->buildUrl('membership', 'listRequested')),
                array('text' => xg_text('BANNED'), 'count' => $counts['banned'], 'url' => $this->_widget->buildUrl('membership', 'listBanned')));
    }

    /**
     * Determine the appropriate search filters for a member query.
     *
     * Optional $_GET variable: 'q'.  Used as text in search.
     */
    private function searchFilters() {
        if (isset($_GET['q'])) {
            return array('my->searchText' => array('likeic', $_GET['q']));
        } else {
            return array();
        }
    }
}
