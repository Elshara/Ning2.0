<?php

/**
 * Useful functions for working with Group objects.
 */
class Groups_GroupHelper {

    /**
     * Populates the xg_groups_groupCount field on the User object.
     *
     * @param $user XN_Content|W_Content  the User object to update
     * @param $save boolean  whether to save the User object
     */
    public static function updateGroupCount($user, $save = TRUE) {
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        if (! XG_LockHelper::lock('update-group-count-' . $user->title, 0)) { return; }
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_Filter.php');
        Groups_Filter::get('joined')->execute($query = XG_Query::create('Content')->filter('my.groupPrivacy', '=', 'public')->begin(0)->end(1), $user->title);
        $user->my->set('xg_groups_groupCount', $query->getTotalCount(), XN_Attribute::NUMBER);
        if ($save) { $user->save(); }
    }

}
