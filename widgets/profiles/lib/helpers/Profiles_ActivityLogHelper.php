<?php

XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');

/**
 * These methods help with profile-related activity log items
 */
class Profiles_ActivityLogHelper {

    /**
     * Removes an activity log item for friendship if one exists
     *
     * @param $array array of two user screennames
     * @return void
     */
    public static function removeFriendActivityLog($members) {
        // TODO: Fix this to work with activity log items showing multiple friends [Jon Aquino 2008-08-07]
        if (count($members) == 2) {
            $query = XN_Query::create('content')
                    ->filter('owner')
                    ->filter('type', '=', 'ActivityLogItem')
                    ->filter('my->category', '=', XG_ActivityHelper::CATEGORY_CONNECTION)
                    ->filter('my->subcategory', '=', XG_ActivityHelper::SUBCATEGORY_FRIEND)
                    ->filter( XN_Filter::any(
                         XN_Filter('my->members','eic', $members[0] . ',' . $members[1]),
                         XN_Filter('my->members','eic', $members[1] . ',' . $members[0])
                    ));
            $results = $query->execute();
            if ($results) {
                XN_Content::delete($results);
            }
        }
    }

    /**
     * Creates activity log item for profile updates
     *
     * @param user string||W_Content screenName User object
     * @return void
     */
    public static function createProfileUpdateItem($user) {
        if (is_string($user)) {
            if (!mb_strlen($user)) {return;}
            $user = User::load($user);
        }
        XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_UPDATE, XG_ActivityHelper::SUBCATEGORY_PROFILE, $user->contributorName, array($user));
    }

}