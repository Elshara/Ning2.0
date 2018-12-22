<?php
W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');

class Profiles_ProfileController extends XG_BrowserAwareController {

    protected function _before() {
		W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_HtmlHelper.php');
	}

    /**
     * Displays a form for editing the answers to the profile questions
     *
     * Expected GET variables:
     *     saved - 1 if the email was successfully changed
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_edit($errors = array()) {
        XG_SecurityHelper::redirectIfNotMember(null, true);
    }


    /**
     * removes the friend relationship between the current user and a named user
     * @param   $user   A screen name, XN_Profile object,XN_Contact object or an array of those things in any combination
     * @return  true or an array of errors
     */
    public function action_unfriend() {
        $this->status = 0;
        $current = XN_Profile::current();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $_GET['user'];
            if (! is_null($user)) {
                // see if we have any activity log items that need removing
                W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ActivityLogHelper.php');
                // TODO: Fix removeFriendActivityLog to work with activity log items showing multiple friends [Jon Aquino 2008-08-07]
                Profiles_ActivityLogHelper::removeFriendActivityLog(array($user,$current->screenName));
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
                Profiles_FriendHelper::instance()->setContactStatus($user, XN_Profile::NOT_FRIEND, XN_Profile::FRIEND);
                $this->status = 1;
                $this->htmlfrag = xg_add_as_friend_link($user, XN_Profile::NOT_FRIEND);
            }
        }
    }

    /**
     * Block messages for the current logged-in user, with a given user
     * @param $_GET['user']  screenName with whom the messages need to be blocked.
     */
    public function action_blockMessage() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        Profiles_FriendHelper::blockMessages($_GET['user']);
        echo "1";
    }

    /**
     * Unblock messages for the current logged-in user, with a given user
     * @param $_GET['user']  screenName with whom the messages need to be unblocked.
     */
    public function action_unblockMessage() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        Profiles_FriendHelper::unblockMessages($_GET['user']);
        echo "1";
    }

    /**
     * Removes the friend relationship between the current user and a named user (iPhone-specific)
     * Returns the user back to the profile screen
     * @param   $user   A screen name, XN_Profile object,XN_Contact object or an array of those things in any combination
     * @return  true or an array of errors
     */
    public function action_unfriend_iphone() {
        $current = XN_Profile::current();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $_GET['user'];
            if (! is_null($user)) {
                // see if we have any activity log items that need removing
                W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ActivityLogHelper.php');
                Profiles_ActivityLogHelper::removeFriendActivityLog(array($user,$current->screenName));
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
                Profiles_FriendHelper::instance()->setContactStatus($user, XN_Profile::NOT_FRIEND, XN_Profile::FRIEND);
            }
        }
        $this->redirectTo('show', 'profile', array('screenName' => $_GET['user']));
    }

    /** @see XG_HttpHelper::isMyPage */
    public function action_show() {
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        XG_App::includeFileOnce('/lib/XG_Layout.php');
        XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');

        // if the user is not signed in and is checking a message, throw them straight to the sign in; BAZ-4935
        if (mb_strpos(XG_HttpHelper::currentUrl(), 'xgp=') !== false) {
            XG_SecurityHelper::redirectToSignInPageIfSignedOut(XG_HttpHelper::currentUrl());
        }

        $this->_widget->includeFileOnce('/lib/helpers/Profiles_PrivacyHelper.php');

        if (! mb_strlen($_GET['screenName']) && ! mb_strlen($_GET['id'])) {
            $mainWidget = W_Cache::getWidget('main');
            $mainWidget->dispatch('error','404');
            exit();
        }
        $this->user = mb_strlen($_GET['screenName']) ? User::load($_GET['screenName']) : User::loadByProfileAddress($_GET['id']);
        if (! ($this->user && User::isMember($this->user))) {
            $mainWidget = W_Cache::getWidget('main');
            $mainWidget->dispatch('error','404');
            exit();
        }
        $this->app = XN_Application::load();

        $this->enabledModules = XG_ModuleHelper::getEnabledModules();
        $this->disabledModules = XG_ModuleHelper::getDisabledModules();

        $layoutOpts = array();
        $layoutOpts['viewAsOther'] = (isset($_GET['viewAsOther']) && $_GET['viewAsOther']);

        $this->xgLayout = XG_Layout::load($this->user->title, 'profiles', $layoutOpts);
        //  Use the contents of the 'layout' element within the document
        $this->layout = $this->xgLayout->getLayout()->documentElement;
        $this->layoutName = $this->xgLayout->getName();
        $this->layoutType = $this->xgLayout->getType();
        $this->isMemberProfilePage = XG_LayoutEditHelper::viewingOwnProfilePage($this->xgLayout);
        $this->isProfilePage = true;
        $this->userIsOwner = ($layoutOpts['viewAsOther'] === false) && ($this->_user->screenName == $this->user->contributorName);
        $this->pageOwner = $this->user;
        $profile = XG_Cache::profiles($this->user->contributorName);
        $this->showFacebookMeta = array_key_exists('from', $_GET) && ($_GET['from'] === 'fb');
        if ($this->showFacebookMeta) {
            XG_App::includeFileOnce('/lib/XG_UserHelper.php');
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
            $this->facebookPreviewImage = XG_UserHelper::getThumbnailUrl($profile, Profiles_UserHelper::SMALL_BADGE_AVATAR_SIZE, Profiles_UserHelper::SMALL_BADGE_AVATAR_SIZE);
        }
        $this->metaDescription = xg_text('XS_PAGE_ON_Y', xg_username($profile), $this->app->name);

        if ($this->_user->isLoggedIn() && XG_Cache::lock('profile-page-update-' . $profile->screenName)) {
            //  Check for any programmatic updates to be performed on this layout (BAZ-2659)
            $this->xgLayout->checkForUpdate();
        }

        if ($_GET['newAppUrl']) {
            W_Cache::getWidget('opensocial')->includeFileOnce("/lib/helpers/OpenSocial_GadgetHelper.php");
            $this->newAppUrl = $_GET['newAppUrl'];
            $this->newAppData = OpenSocial_GadgetHelper::readGadgetUrl($this->newAppUrl);
        } else if ($_GET['oldAppUrl']) {
            W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
            $this->oldAppUrl = $_GET['oldAppUrl'];
            $this->oldAppData = OpenSocial_GadgetHelper::readGadgetUrl($this->oldAppUrl);
        }
    }
    public function action_show_iphone() {
        // if the user is not signed in and is checking a message, throw them straight to the sign in; BAZ-4935
        if (mb_strpos(XG_HttpHelper::currentUrl(), 'xgp=') !== false) {
            XG_SecurityHelper::redirectToSignInPageIfSignedOut(XG_HttpHelper::currentUrl());
        }
        if (! mb_strlen($_GET['screenName']) && ! mb_strlen($_GET['id'])) {
            $mainWidget = W_Cache::getWidget('main');
            $mainWidget->dispatch('error','404');
            exit();
        }
        $this->user = mb_strlen($_GET['screenName']) ? User::load($_GET['screenName']) : User::loadByProfileAddress($_GET['id']);
        if (! ($this->user && User::isMember($this->user))) {
            $mainWidget = W_Cache::getWidget('main');
            $mainWidget->dispatch('error','404');
            exit();
        }

        $this->userIsOwner = $this->_user->screenName == $this->user->contributorName;
        $this->metaDescription = xg_text('XS_PAGE_ON_Y', xg_username($profile), $this->app->name);

        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        list($this->userAgeSex, $this->userLocation) = Profiles_UserHelper::getPrivateUserInfo($this->user);

        if (!$this->userIsOwner && $this->_user->isLoggedIn()) {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
            $status = Profiles_FriendHelper::instance()->getContactStatus($this->user->title);

            if ($status == XN_Profile::FRIEND || XG_SecurityHelper::userIsAdmin()) {
                $this->sendMessageUrl = $this->_buildUrl('message', 'new', array('screenName' => $this->user->title));
            }
            if ($status == XN_Profile::FRIEND) {
                $this->removeFriendUrl = $this->_buildUrl('profile','unfriend', array('user' => $this->user->title));
            } else if ($status == XN_Profile::FRIEND_PENDING) {
				$this->friendRequestSent = true;
			} else if ($status != XN_Profile::BLOCK) {
                $this->addFriendUrl = $this->_buildUrl('friendrequest','create', array('screenName' => $this->user->title));
            }
		}
    }

    /**
     * Display a pending user's profile to an admin to allow the admin to
     * accept, decline, or ban the user
     */
    public function action_showPending() {
        /* Make sure the currently logged in user is a network admin */
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_SecurityHelper::redirectIfNotAdmin();

        /* Make sure a user is provided */
        if (! (isset($_GET['id']) && mb_strlen($_GET['id']))) {
            $mainWidget = W_Cache::getWidget('main');
            $mainWidget->dispatch('error','404');
            exit();
        }

        /* And make sure that user's really pending */
        $this->user = User::load($_GET['id']);
        if (! ($this->user && User::isPending($this->user))) {
            $mainWidget = W_Cache::getWidget('main');
            $mainWidget->dispatch('error','404');
            exit();
        }

        /* The list-pending-members page may have passed over a page number */
        $this->page = isset($_GET['page']) ? max(1,intval($_GET['page'])) : 1;

        $this->listPendingUrl = W_Cache::getWidget('main')->buildUrl('membership','listPending',array('page' => $this->page));

        /* Load the profile for the pending user */
        $this->profile = XG_Cache::profiles($this->user->contributorName);

        /* Load the profile questions for the pending user */
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionFormHelper.php');
        $qa = Profiles_ProfileQuestionFormHelper::read($this->user);
        $this->questions = $qa['questions'];
        $this->questionsAndAnswers = $qa['answers'];

    }

    public function action_settings() {
        $this->forwardTo('privacySettings');
    }

    public function action_clearFollowList() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        Index_NotificationHelper::stopAllFollowing();
        $this->redirectTo('emailSettings', 'profile', array('cleared' => true));
    }

    public function action_privacySettings() {
        XG_SecurityHelper::redirectIfNotMember(null, true);
        $this->moderationAttributeName = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'moderateBlogComments');
        $this->commentWallModerationAttributeName = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'moderateChatters');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Make sure the submitted data is sane
            $this->errors = array();
            $errorCatalog = array('defaultVisibility' => xg_html('PICK_VALID_DEFAULT_VISIBILITY'),
                            'addCommentPermission' => xg_html('PICK_VALID_WHO_CAN_COMMENT'),
                            $this->moderationAttributeName => xg_html('PICK_VALID_BLOG_COMMENT_MODERATION'),
                            $this->commentWallModerationAttributeName => xg_html('PICK_VALID_COMMENTWALL_COMMENT_MODERATION'),
                            );

            foreach (array('defaultVisibility','addCommentPermission') as $f) {
                if (isset($_POST[$f]) && (! in_array($_POST[$f], array('all','friends','me')))) {
                    $this->errors[$f] = $errorCatalog[$f];
                }
            }
            if (isset($_POST[$this->moderationAttributeName]) && (! in_array($_POST[$this->moderationAttributeName], array('Y','N')))) {
                    $this->errors[$this->moderationAttributeName] = $errorCatalog[$this->moderationAttributeName];
            }
            if (isset($_POST[$this->commentWallModerationAttributeName]) && (! in_array($_POST[$this->commentWallModerationAttributeName], array('Y','N')))) {
                    $this->errors[$this->commentWallModerationAttributeName] = $errorCatalog[$this->commentWallModerationAttributeName];
            }

            if (count($this->errors) == 0) {
                $this->forwardTo('privacySettingsSave', 'profile', array(true));
                return;
            }
        }
        $user = User::load($this->_user);
        foreach (array('defaultVisibility','addCommentPermission','blogPingPermission', 'viewEventsPermission',
                $this->moderationAttributeName, $this->commentWallModerationAttributeName) as $f){
            $defaults[$f] = $user->my->{$f};
        }
        if (!$defaults['viewEventsPermission']) {
            $defaults['viewEventsPermission'] = 'all';
        }
        foreach (array('activityNewContent', 'activityNewComment', 'activityProfileUpdate','activityEvents','activityFriendships') as $f) {
            $defaults[$f] = ($user->my->{$f} != 'N')?'Y':'N';
        }
        $this->form = new XNC_Form($defaults);
        $this->app = XN_Application::load();
        $this->displaySavedNotification = $_GET['saved'] && count($this->errors) == 0;
    }

    public function action_emailSettings() {
        XG_SecurityHelper::redirectIfNotMember(null, true);
        $this->moderationAttributeName = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'moderateBlogComments');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Make sure the submitted data is sane
            $this->errors = array();
            $errorCatalog = array(
                            'emailActivityPref' => xg_html('PICK_VALID_ACTIVITY_NOTIFICATION'),
                            'emailModeratedPref' => xg_html('PICK_VALID_MODERATION_NOTIFICATION'),
                            'emailApprovalPref' => xg_html('PICK_VALID_APPROVAL_NOTIFICATION'),
                            'emailInviteeJoinPref' => xg_html('PICK_VALID_INVITEE_NOTIFICATION'),
                            'emailNeverPref' => xg_html('PICK_VALID_NEVER_NOTIFICATION'),
                            'emailAllFriendsPref' => xg_html('PICK_VALID_ALL_FRIENDS_NOTIFICATION'),
                            'emailViaApplicationsPref' => xg_html('PICK_VALID_VIA_APPLICATIONS_NOTIFICATION'),
                            'emailFriendRequestPref' => xg_html('PICK_VALID_FRIEND_REQUEST_NOTIFICATION'),
                            'emailNewMessagePref' => xg_html('PICK_VALID_NEW_MESSAGE_NOTIFICATION'),
                            'emailAdminMessagesPref' => xg_html('PICK_VALID_ADMIN_MESSAGES_NOTIFICATION')
                            );

            if (isset($_POST['emailActivityPref']) && (! in_array($_POST['emailActivityPref'], array('activity','')))) {
                $this->errors['emailActivityPref'] = $errorCatalog['emailActivityPref'];
                $this->errors['emailPref'] = true;
            }
            if (isset($_POST['emailModeratedPref']) && (! in_array($_POST['emailModeratedPref'], array('each','')))) {
                $this->errors['emailModeratedPref'] = $errorCatalog['emailModeratedPref'];
                $this->errors['emailPref'] = true;
            }
            // TODO: Carefully remove duplicate arrays [Jon Aquino 2008-01-01]
            foreach (array('emailApprovalPref','emailInviteeJoinPref','emailNeverPref','emailAllFriendsPref','emailViaApplicationsPref',
                    'emailFriendRequestPref','emailNewMessagePref', 'emailSiteBroadcastPref', 'emailGroupBroadcastPref',
                    'emailEventBroadcastPref', 'emailAdminMessagesPref') as $pref) {
                if (isset($_POST[$pref]) && (! in_array($_POST[$pref], array('Y','')))) {
                    $this->errors[$pref] = $errorCatalog[$pref];
                    $this->errors['emailPref'] = true;
                }
            }

            if (count($this->errors) == 0) {
                $this->forwardTo('emailSettingsSave', 'profile', array(true));
                return;
            }
        }

        if ($_GET['saved']) {
            $this->successMessage = xg_html('PROFILE_SAVED_GO', 'href="' . xnhtmlentities($this->_buildUrl('index','index')) . '"');
        } else if ($_GET['cleared']) {
            $this->successMessage = xg_html('YOUR_FOLLOW_LIST_HAS_BEEN_CLEARED_GO', 'href="' . xnhtmlentities($this->_buildUrl('index','index')) . '"');
        }

        $user = User::load($this->_user);
        foreach (array('emailActivityPref','emailModeratedPref', 'emailApprovalPref',
                'emailInviteeJoinPref','emailNeverPref','emailFriendRequestPref',
                'emailNewMessagePref', 'emailSiteBroadcastPref', 'emailGroupBroadcastPref', 'emailEventBroadcastPref',
                'autoFollowOnReplyPref', 'emailCommentApprovalPref', 'emailAllFriendsPref', 'emailViaApplicationsPref',
                'emailAdminMessagesPref') as $f) {
            $defaults[$f] = $user->my->{$f};
        }

        //  Use defaults for new preferences added in 1.11 (DC)
        // TODO: This code duplicates code elsewhere that sets defaults,
        // e.g., acceptingBroadcasts(), acceptingMessagesSentToAllFriends().
        // Eliminate the duplication so they won't get out of sync [Jon Aquino 2008-01-01]
        $newPrefMapping = array('emailSiteBroadcastPref' => 'emailNewMessagePref',
                'emailCommentApprovalPref' => 'emailApprovalPref',
                'emailGroupBroadcastPref' => 'emailNewMessagePref',
                'emailEventBroadcastPref' => 'emailNewMessagePref',
                'emailAllFriendsPref' => 'emailNewMessagePref',
                'emailViaApplicationsPref' => 'emailNewMessagePref',
                'autoFollowOnReplyPref' => 'emailActivityPref');
        foreach ($newPrefMapping as $newPref => $oldPref) {
            if (!mb_strlen($defaults[$newPref])) {
                $defaults[$newPref] = $defaults[$oldPref];
            }
        }

        // Default for new preference added in 3.1
        if (! mb_strlen($defaults['emailAdminMessagesPref'])) {
            $defaults['emailAdminMessagesPref'] = 'Y';
        }

        // If emailNeverPref is set, clear out the other email choices
        if ($defaults['emailNeverPref'] == 'Y') {
            $defaults['emailActivityPref'] = $defaults['emailModeratedPref'] = 'none';
            $defaults['emailApprovalPref'] = $defaults['emailInviteeJoinPref']
                    = $defaults['emailFriendRequestPref']
                    = $defaults['emailNewMessagePref']
                    = $defaults['emailSiteBroadcastPref']
                    = $defaults['emailGroupBroadcastPref']
                    = $defaults['emailEventBroadcastPref']
                    = $defaults['emailAllFriendsPref']
                    = $defaults['emailViaApplicationsPref']
                    = $defaults['autoFollowOnReply']
                    = $defaults['emailAdminMessagesPref']
                    = 'N';
        }

        $this->form = new XNC_Form($defaults);

        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        $this->enabledModules = XG_ModuleHelper::getEnabledModules();
    }

    public function action_privacySettingsSave($fromSettings = false) {
        XG_SecurityHelper::redirectIfNotMember();
        if (! $fromSettings) {
            $this->redirectTo('privacySettings');
            return;
        }
        $this->moderationAttributeName = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'moderateBlogComments');
        $this->commentWallModerationAttributeName = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'moderateChatters');
        $user = User::load($this->_user);
        foreach (array('defaultVisibility','addCommentPermission',$this->moderationAttributeName, $this->commentWallModerationAttributeName) as $f) {
            if (isset($_POST[$f])) { $user->my->{$f} = $_POST[$f]; }
        }
        foreach (array('activityNewContent','activityNewComment','activityProfileUpdate','blogPingPermission','activityFriendships') as $f) {
            $user->my->{$f} = (isset($_POST[$f]) && mb_strlen($_POST[$f])) ? $_POST[$f] : 'N';
        }

        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        $enabledModules = XG_ModuleHelper::getEnabledModules();
        if ($enabledModules['events']) {
            $user->my->viewEventsPermission = $_POST['viewEventsPermission'];
            $user->my->activityEvents = $_POST['activityEvents'] ? 'Y' : 'N';
        }

        $user->save();

        // Invalidate pages (such as the blog post detail page) that depend on user settings

        $this->redirectTo('privacySettings', 'profile', array('saved' => 1));
    }

    public function action_emailSettingsSave($fromSettings = false) {
        XG_SecurityHelper::redirectIfNotMember();
        if (! $fromSettings) {
            $this->redirectTo('emailSettings');
            return;
        }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        $user = User::load($this->_user);

        foreach (array('emailActivityPref','emailModeratedPref') as $f) {
            $user->my->{$f} = (isset($_POST[$f]) && mb_strlen($_POST[$f])) ? $_POST[$f] : 'none';
        }

        $oldEmailGroupBroadcastPref = $user->my->emailGroupBroadcastPref;
        $oldEmailEventBroadcastPref = $user->my->emailEventBroadcastPref;
        
        // Use the preference if it is defined, default is N
        foreach (array('emailApprovalPref','emailInviteeJoinPref','emailNeverPref',
                'emailFriendRequestPref','emailNewMessagePref', 'emailAllFriendsPref',
                'emailSiteBroadcastPref', 'emailCommentApprovalPref',
                'emailGroupBroadcastPref', 'emailEventBroadcastPref', 'autoFollowOnReplyPref',
                'emailAdminMessagesPref') as $f) {
            $user->my->{$f} = (isset($_POST[$f]) && mb_strlen($_POST[$f])) ? $_POST[$f] : 'N';
        }

        // Use the preference if it is defined, default is Y
        foreach (array('emailViaApplicationsPref') as $f) {
            $user->my->{$f} = (isset($_POST[$f]) && mb_strlen($_POST[$f])) ? $_POST[$f] : 'Y';
        }
        
        $user->save();

        // If the site broadcast alias exists, adjust it
        if ($set = XN_ProfileSet::load(Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME)) {
            if ($user->my->emailSiteBroadcastPref == 'Y') {
                $set->addMembers($user->contributorName);
            } else {
                $set->removeMember($user->contributorName);
            }
        }

        if ($user->my->emailGroupBroadcastPref !== $oldEmailGroupBroadcastPref) {
            W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_BroadcastHelper.php');
            if ($user->my->emailGroupBroadcastPref == 'Y') {
                // TODO: Do this in an async job. Get the groupIds by querying the person's GroupMemberships. [Jon Aquino 2008-04-01]
                foreach (GroupMembership::groupIds($user) as $groupId) {
                    $groupBroadcastProfileSet = XN_ProfileSet::load(Groups_BroadcastHelper::profileSetId($groupId));
                    if ($groupBroadcastProfileSet) { $groupBroadcastProfileSet->addMembers($user->contributorName); }
                }
            } else {
                XN_ProfileSet::removeMemberByLabel($user->contributorName, Groups_BroadcastHelper::GROUP_BROADCAST_LABEL);
            }
        }

        if ($user->my->emailEventBroadcastPref !== $oldEmailEventBroadcastPref) {
            EventWidget::init();
            if ($user->my->emailEventBroadcastPref == 'Y') {
                Events_BroadcastHelper::allowBroadcasts($user->title);
            } else {
                XN_ProfileSet::removeMemberByLabel($user->title, Events_BroadcastHelper::EVENT_BROADCAST_LABEL);
            }
        }

        if ($_POST['emailNeverPref'] == 'Y') {
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
            Index_NotificationHelper::stopAllFollowing();
        }

        // Invalidate pages (such as the blog post detail page) that depend on user settings

        $this->redirectTo('emailSettings', 'profile', array('saved' => 1));
    }

    /**
     * This action should only be invoked via xn_out=json
     */
    public function action_newUploadEmailAddress() {
        // Make sure the current user is a member
        XG_App::enforceMembership('profile','settings');

        // Make sure this is a POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->_user->getNewUploadEmailAddress();
        }

        $this->uploadEmailAddress = $this->_user->uploadEmailAddress;
    }

    public function action_acceptEmailContent() {
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        foreach(XG_ModuleHelper::getEnabledModules() as $module) {
            if (in_array('index', $module->getControllerNames()) && $module->controllerHasAction('index', 'acceptEmailContent')) {
                if ($module->dispatch('index', 'acceptEmailContent')) { break; }
            }
		}
    }

    public function action_setPageTitle() {
        if (!isset($_POST['screenName']) || $_POST['screenName'] != $this->_user->screenName) {
            $this->error = xg_text('NOT_ALLOWED');
            return;
        }
        $title = trim($_POST['value']);
        $user = User::load($_POST['screenName']);
        $user->my->{XG_App::widgetAttributeName($this->_widget, 'pageTitle')} = $title;
        $user->save();
        $this->html = $title === '' ? xg_text('WELCOME_ADD_YOUR_TITLE') : qh($title);
    }

    /**
     * Returns the embed code for the user badge, and the embed code for its preview.
     *
     * Expected GET parameters:
     *     xn_out - set this to xn_json
     *     customText - replacement for the "I'm a member of" text
     */
    public function action_embeddableWithPreview() {
        ob_start();
        $this->_widget->dispatch('profile', 'embeddable', array(array('username' => XN_Profile::current()->screenName, 'customText' => $_GET['customText'],'includeFooterLink' => true)));
        $this->embedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
        ob_end_clean();
        ob_start();
        $this->_widget->dispatch('profile', 'embeddable', array(array('username' => XN_Profile::current()->screenName, 'customText' => $_GET['customText'])));
        $this->previewEmbedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
        ob_end_clean();
    }

    /**
     * Displays the Flash object for the profile badge
     *
     * Expected GET parameters:
     *     Any of the $args parameters can also be passed as GET parameters
     *
     * @param $args array  parameters:
     *     username - screen name of the person to focus on
     *     customText - optional replacement for the "I'm a member of" text (applies to the user badge)
     *     fgColor - optional network-name color to override the value in badge-config.xml, e.g., FF0000
     *     fgImage - optional brand-logo URL to override the value in badge-config.xml; use "none" to specify no image
     *     fgImageWidth - optional brand-logo width to override the value in badge-config.xml
     *     fgImageHeight - optional brand-logo height to override the value in badge-config.xml
     *     bgColor - optional background color to override the value in badge-config.xml, e.g., 333333
     *     bgImage - optional background image URL to override the value in badge-config.xml; use "none" to specify no image
     *     includeFooterLink - whether to add a link back to the app
     */
    public function action_embeddable($args = array()) {
        $this->args = array_merge($_GET, $args);
        $this->args['panel'] = 'user';
        if ($this->args['includeFooterLink']) {
            $this->args['footerLinkUrl'] = xg_absolute_url(User::quickProfileUrl($args['username']));
            $this->args['footerLinkHtml'] = xg_html('VIEW_PAGE_ON_APPNAME', XN_Application::load()->name);
        }
    }

    /**
     * Displays the form for answering profile questions. Called by the Create Profile and Edit Profile pages.
     *
     * @param $onlyShowRequired boolean  whether to hide optional questions
     */
    public function action_profileQuestionForm($onlyShowRequired = false) {
        $this->onlyShowRequired = $onlyShowRequired;
        $user = User::loadOrRetrieveIfLoaded($this->_user);
        $this->months = array(xg_text('MONTH'), xg_text('JANUARY'), xg_text('FEBRUARY'), xg_text('MARCH'), xg_text('APRIL'), xg_text('MAY'), xg_text('JUNE'), xg_text('JULY'), xg_text('AUGUST'), xg_text('SEPTEMBER'), xg_text('OCTOBER'), xg_text('NOVEMBER'), xg_text('DECEMBER'));
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
        $this->questions = Profiles_ProfileQuestionHelper::getQuestions(W_Cache::getWidget('profiles'));
        $defaults = array();
        foreach ($this->questions as $question) {
            $attrName = Profiles_ProfileQuestionHelper::attributeNameForQuestion($question, W_Cache::getWidget('profiles'));
            $fieldName = 'question_' . $question['questionCounter'];
            if (is_array($user->my->{$attrName})) {
                $defaults[$fieldName] = $user->my->{$attrName};
            }
            else if (mb_strlen($user->my->{$attrName})) {
                if ($question['answer_type'] == 'date') {
                    list($year,$month,$day) = explode('-',mb_substr($user->my->{$attrName}, 0, 10));
                    $defaults[$fieldName.'_year'] = $year;
                    $defaults[$fieldName.'_month'] = (integer) $month;
                    $defaults[$fieldName.'_day'] = (integer) $day;
                } else if (($question['answer_type'] == 'select') && ($question['answer_multiple'] == 'on')) {
                    //  Multiple choice answers should be serialized for BAZ-2144
                    //    Older ones might not be yet
                    $unserial = @unserialize($user->my->{$attrName});
                    if ($unserial) {
                        $defaults[$fieldName] = $unserial;
                    }
                    else {
                        $defaults[$fieldName] = array($user->my->{$attrName});
                    }
                } else {
                    // for BAZ-7387 avoid double encoding entities in the form.
                    $defaults[$fieldName] = html_entity_decode($user->my->{$attrName});
                }
            } else {
                if ($question['answer_type'] == 'date') {
                    $defaults[$fieldName.'_year'] = 'yyyy';
                    $defaults[$fieldName.'_day'] = 'dd';
                }
            }
        }
        $this->form = new XNC_Form($defaults);
    }
    public function action_profileQuestionForm_iphone($onlyShowRequired = false) {
        $this->action_profileQuestionForm($onlyShowRequired);
        list($this->years, $this->months, $this->days) = xg_date_options(true);
    }

    /**
     * Reset the theme and layout of the current user's profile page to the network's default.
     */
    public function action_resetProfilePage() {
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_SecurityHelper::redirectToSignInPageIfSignedOut();
        $user = User::load($this->_user);
        // Reset theme
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
        Index_AppearanceHelper::removeByUser($user);
        // Reset layout
        $currentLayout = XG_Layout::load($user->title, 'profiles');
        XG_App::includeFileOnce('/lib/XG_LayoutEditHelper.php');
        XG_LayoutEditHelper::determineNewLayout($currentLayout, "{}");
        // Redirect to profile page
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        $url = XG_HttpHelper::profileUrl($this->_user->screenName);
        header("Location: $url");
        exit;
    }

    /**
     * Accepts a new layout in JSON and a userName and returns a JSON success/failure message.
     *
     * Expected $_POST vars:
     *
     *  newLayout - describing the current user's new profile page layout.
     *  userName - userName of profile being updated.
     *
     * @return  string  JSON string.  Either:
     *                                  ({ 'result': 'success', 'iteration': <new iteration value> })
     *                                or
     *                                  ({ 'result': 'failure', errMsg: <error message> })
     */
    public function action_saveLayout() {
        header('Content-Type: text/json');
        //TODO: could check if it has actually been changed here and short circuit the whole process if so. [Thomas David Baker 2008-05-21]
        $_GET['xn_out'] = 'json';
        try {
            if (! XN_Profile::current()->isLoggedIn()) { throw new Exception("Not logged in"); }
            if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
            if ($_POST['userName'] !== XN_Profile::current()->screenName) {
                throw new Exception("Got " . $_POST['userName'] . " as userName and expected " . XN_Profile::current()->screenName) ;
            }
            $user = User::loadOrRetrieveIfLoaded($this->_user);
            $currentLayout = XG_Layout::load($user->title, 'profiles');
            XG_App::includeFileOnce('/lib/XG_LayoutEditHelper.php');
            list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($currentLayout, $_POST['newLayout']);
            if ($embedsAdded === 0) { throw new Exception("Failed to update layout"); }
            $this->result = 'success';
            $this->iteration = XG_Layout::getIteration($newLayout->getLayout());
        } catch (Exception $e) {
            $this->result = 'failure';
            $this->errMsg = $e->getMessage();
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
