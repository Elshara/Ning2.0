<?php

/**
 * Useful functions for working with User objects.
 */
class Forum_UserHelper {

    /**
     * Updates the forum activity count attribute for the given User object
     * This number indicates how active the user has been on the forums.
     *
     * @param $user W_Content|X_Content  The User object
     * @param $testing boolean  Whether this function is currently being tested. Defaults to FALSE.
     */
    public static function updateActivityCount($user, $testing = FALSE) {
        /* Important Note:
         /widgets/profiles/controllers/EmbedController.php assumes that activityCount is
         topics + comments and use the value of this attribute to display the count with the link to user's discussions
         remember to update there if you plan to change what "activity" means for forums [Zuardi-05-Apr-2007] */
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter(XN_Filter::any(XN_Filter('type', '=', 'Topic'), XN_Filter('type', '=', 'Comment')));
        XG_GroupHelper::addGroupFilter($query);
        $query->filter('contributorName', '=', $user->title);
        $query->filter('my->mozzle', '=', 'forum');
        $query->filter('my.xg_forum_deleted', '=', null);
        $query->begin(0);
        $query->end(1);
        $query->alwaysReturnTotalCount(TRUE);
        if ($testing) { $query->filter('my->test', '=', 'Y'); }
        $query->execute();
        // setWidgetAttribute may return a User or a GroupMembership [Jon Aquino 2007-05-10]
        User::setWidgetAttribute($user, 'activityCount', $query->getTotalCount(), XN_Attribute::NUMBER)->save();
    }

}