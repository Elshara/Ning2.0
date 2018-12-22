<?php

/**
 * This class is for things that happen when someone joins a network
 */
class Index_MembershipHelper {

    /**
     * Do whatever housekeeping is required when a new user attempts to join
     * the network. Saves the User object.
     *
     * @param $profile XN_Profile XN_Profile object of the about-to-join user
     * @param $user User User object of the about-to-join user
     * @param $invitation XN_Invitation  the invitation for the current user, if any
     */
    public static function onJoin($profile, $user, $invitation) {
        // no changes to user object allowed here until XN_Content reload issue is solved. [Andrey 2008-07-23]
        $address = User::generateProfileAddress($user); // this will reset all changed User attrs if User exists.

        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        /* If the network is set up for admins to approve members, mark this User object as 'pending' */
        // Generate profile address here rather than User::load. Otherwise this expensive function
        // may get called multiple times, only to have its results discarded.  [Jon Aquino 2007-09-18]
        if (XG_App::membersAreModerated()) {
            User::setStatus($user, 'pending');
		} else {
            User::setStatus($user, 'member'); // clear the "unfinished" status set inside User::loadOrCreate()
		}
        User::setProfileAddress($user, $address);
        // TODO: Pass groupToJoin in as a parameter rather than accessing it via $_GET  [Jon Aquino 2007-09-19]
        /* This does the right thing for regular or pending users */
        XG_JoinPromptHelper::joinGroupWithUrl($_GET['groupToJoin'], $profile->screenName);
        if ($invitation && $invitation->label !== Index_InvitationHelper::NETWORK_BULK_INVITATION) { $user->my->inviter = $invitation->inviter; }
        /* The save() should happen after setStatus() and after the potential group-joining */
        $user->save();
        if ($invitation) {
            Index_InvitationHelper::consume($invitation);
        } else {
            Index_InvitationHelper::deleteInvitations($profile->email, Index_InvitationHelper::NETWORK_INVITATION);
        }

        // Add the user to the USERS profile set
        // This happens even for pending members, since we need access to the
        // email address (BAZ-4606)
        try {
            XN_ProfileSet::addMembersToSets($profile->screenName, XN_ProfileSet::USERS);
        } catch (Exception $e) {
            error_log("Couldn't add {$profile->screenName} to USERS profile set: {$e->getMessage()}");
        }

        /* If member moderation is on, notify admins of a new pending member */
        if (XG_App::membersAreModerated()) {
            if (!$invitation || $invitation->label != Index_InvitationHelper::NETWORK_BULK_INVITATION) {
                try {
					XG_Browser::execInEmailContext(array(__CLASS__,'_sendNotification'),
						XG_Message_Notification::EVENT_MODERATION_MEMBER,
						array('joiner' => $profile),
						XN_Application::load()->ownerName, true);
                } catch (Exception $e) {
                      error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                }
            }
        }
        /* Otherwise, complete the rest of the on-join actions */
        else {
            self::onAccept($profile, $user);
        }
    }

    public static function onAccept($profile, $user) {
        // Ignore exceptions (BAZ-1829) [Jon Aquino 2007-03-07]
        XG_App::includeFileOnce('/lib/XG_Message.php');
        /* Re-save the User object if this is a (formerly) pending member
         * being accepted. */
        if (User::isPending($user)) {
            User::setStatus($user, 'member');
            /* If there was a group you wanted to join when you first signed up,
             * join it now. */
            XG_JoinPromptHelper::joinGroupIfPending($profile->screenName);
            $user->save();
            try {
                /* Since you were just pending, you get the "accepted" message */
				XG_Browser::execInEmailContext(array(__CLASS__,'_sendNotification'),
					XG_Message_Notification::EVENT_PENDING_ACCEPTED,
					array('profile' => $profile),
					$profile->screenName);
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
        }
        /* If you weren't just pending, you get the regular welcome message */
        else {
            try {
                // And send you your very own welcome message (BAZ-1591)
				XG_Browser::execInEmailContext(array(__CLASS__,'_sendNotification'),
					XG_Message_Notification::EVENT_WELCOME,
					array('profile' => $profile),
					$profile);
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            }
        }
        // Since you've just joined, notify them that's invited you (BAZ-1169)
        // This happens onAccept, not in onJoin -- the difference is relevant
        // when member moderation is on (BAZ-4332)
        try {
            if ($user->my->inviter) {
				XG_Browser::execInEmailContext(array(__CLASS__,'_sendNotification'),
					XG_Message_Notification::EVENT_JOIN,
					array('joiner' => $profile),
					$user->my->inviter);
			}
        } catch (Exception $e) {
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        }
        try {
            //generate activity log item for the new member join
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            $activityLogItem = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_CONNECTION, XG_ActivityHelper::SUBCATEGORY_PROFILE, $profile->screenName, array($user), NULL, NULL, NULL, NULL, false);
			if ($activityLogItem) {
				$memberNumber = $user->my->raw(XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'memberNumber'));
				$activityLogItem->my->set(XG_App::widgetAttributeName(W_Cache::getWidget('main'), 'memberNumber'), $memberNumber, XN_Attribute::NUMBER);
				$activityLogItem->save();
			}
        } catch (Exception $e) {
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        }
        try {
            //  If the site broadcast list exists add this user to it
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
            if ($set = XN_ProfileSet::load(Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME)) {
                $set->addMembers($profile->screenName);
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    /**
     * To what file is member data exported?
     * @return string
     */
    public static function memberDataExportFile() {
        return $_SERVER['DOCUMENT_ROOT'] . '/xn_private/memberdata.csv';
    }

    //TODO There is now an awkward half-crossover between my->status and my->memberStatus.  Is there any way they can be combined?
    //TODO There's a third status field as well: xg_index_status, which may be 'blocked' or 'pending'. [Jon Aquino 2008-01-25]
    /**
     * Adds a my->memberStatus field to the first 50 Administrators (or Network Creator) that do not have one.
     * Designed to be called repeatedly until all have the attribute.
     *
     * @return  int Number of members still remaining who need a memberStatus field.
     */
    public static function addMemberStatus() {
        XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
        $max = 50;
        $ownerName = XN_Application::load()->ownerName;
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'User')->begin(0)->end($max);
        $query->filter('my->memberStatus', '=', null);
        $query->filter(XN_Filter::any(XN_Filter('title', '=', $ownerName), XN_Filter('my->isAdmin', '=', 'Y')));
        $query->filter('my->xg_index_status', '<>', 'pending');
        $query->filter('my->xg_index_status', '<>', 'blocked');
        $query->filter('my->status', '<>', 'banned');
        if (defined('UNIT_TESTING')) { $query->filter('my->test', '=', 'Y'); }
        $query->alwaysReturnTotalCount(true);
        foreach ($query->execute() as $user) {
            if ($user->title == $ownerName) {
                $user->my->memberStatus = XG_MembershipHelper::OWNER;
            } else {
                $user->my->memberStatus = XG_MembershipHelper::ADMINISTRATOR;
            }
            $user->save();
        }
        return max(0, $query->getTotalCount() - $max);
    }

    /**
     * Create an XN_Job to add a memberStatus to administrator and network creator Users in the network.
     */
    public static function scheduleAddMemberStatus() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        // TODO: Use XG_SequencedjobController [Jon Aquino 2008-04-28]
        $job = XN_Job::create();
        $job->addTask(XN_Task::create(XG_SecurityHelper::addCsrfToken(W_Cache::getWidget('main')->buildUrl('membership', 'addMemberStatus')), array()));
        $result = $job->save();
        if (Index_InvitationHelper::isErrorArray($result)) { throw new Exception(Index_InvitationHelper::errorMessage(key($result))); }
    }

	// callback for sending notifications
	public static function _sendNotification($type, $createArgs/*, ... */) { # void
		XG_App::includeFileOnce('/lib/XG_Message.php');
		$msg = XG_Message_Notification::create($type, $createArgs);
		$args = func_get_args();
		array_shift($args);
		array_shift($args);
		call_user_func_array(array($msg,'send'),$args);
    }
}
