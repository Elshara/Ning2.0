<?php
require_once dirname(__DIR__) . '/../bootstrap.php';


if (! XN_Profile::current()->isOwner()) {
    error_log('rebuildBroadcastAliases - user is not app owner, aborting');
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// Define the base directory in this app
define('NF_APP_BASE',$_SERVER['DOCUMENT_ROOT']);

/* Load the base WWF code */
require_once XN_INCLUDE_PREFIX . '/WWF/bot.php';

W_WidgetApp::includeFileOnce('/lib/XG_App.php');
//  loop params - tweak for best performance
$findPageSize = 40;
$addPageSize = 100;

try {
    //  Collect broadcast info for all users
    W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
    //  Don't assume the groups instance exists - it might not in really old
    //    centralized apps
    $haveGroups = FALSE;
    try {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_BroadcastHelper.php');
        $haveGroups = TRUE;
    } catch (Exception $e) {}

    error_log('rebuildBroadcastAliases - gathering user info');
    $membersToAdd = array();
    $groupMembersToAdd = array();
    $start = 0;
    do {
        $userInfo = User::find(array(), $start, $start + $findPageSize);
        foreach ($userInfo['users'] as $user) {
            $screenName = $user->contributorName;
            //  Add to site broadcast list if broadcast enabled
            if (Index_NotificationHelper::canSendToUser(Index_NotificationHelper::SITE_BROADCAST_TYPE, $user)) {
                $membersToAdd[] = $screenName;
            }

            if (!$haveGroups) {
                continue;
            }
            //  If group broadcast enabled, add to broadcast list for each group
            if (Groups_BroadcastHelper::acceptingBroadcasts($user)) {
                $groupList = $user->my->raw(XG_App::widgetAttributeName(W_Cache::getWidget('groups'), 'groups'));
                if (mb_strlen($groupList)) {
                    $groups = mb_split(' ', $groupList);
                    foreach ($groups as $groupId) {
                        if (is_array($groupMembersToAdd[$groupId])) {
                            $groupMembersToAdd[$groupId][] = $screenName;
                        } else {
                            $groupMembersToAdd[$groupId] = array($screenName);
                        }
                    }
                }
            }
	    unset($user->my);
	    XN_Cache::_remove($user);
	    unset($user);
        }
        $start += $findPageSize;
    } while ($start < $userInfo['numUsers']);
    //  Delete and recreate the site broadcast alias
    $setId = Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME;
    error_log('rebuildBroadcastAliases - recreating ' . $setId);
    echo "recreating $setId<br/>\n";
    XN_ProfileSet::delete($setId);
    $set = XN_ProfileSet::loadOrCreate($setId);
    if (!$set) {
        throw new Exception('Failed to create profile set ' . $setId);
    }
    foreach (array_chunk($membersToAdd, $addPageSize) as $members) {
        $set->addMembers($members);
    }

    if ($haveGroups) {
        //  Delete and recreate each group broadcast alias
        foreach ($groupMembersToAdd as $groupId => $membersToAdd) {
            $setId = Groups_BroadcastHelper::profileSetId($groupId);
            error_log('rebuildBroadcastAliases - recreating ' . $setId);
            echo "recreating $setId<br/>\n";
            XN_ProfileSet::delete($setId);
            $set = Groups_BroadcastHelper::loadOrCreateProfileSet($groupId);
            if (!$set) {
                throw new Exception('Failed to create profile set ' . $setId);
            }
            foreach (array_chunk($membersToAdd, $addPageSize) as $members) {
                $set->addMembers($members);
            }
        }
    }
    print "All done!";
} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Error');
    error_log($e);
}