<?php

/**
 * Useful functions for working with User objects.
 */
class Page_UserHelper {

    /**
     * Updates the Page activity count attribute for the given User object
     * This number indicates how active the user has been on the Pages.
     *
     * @param $user W_Content|X_Content  The User object
     * @param $testing boolean  Whether this function is currently being tested. Defaults to FALSE.
     * @return W_Content|X_Content  The User object
     */
    public static function updateActivityCount($user, $testing = FALSE) {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter(XN_Filter::any(XN_Filter('type', '=', 'page'), XN_Filter('type', '=', 'Comment')));
        $query->filter('contributorName', '=', $user->title);
        $query->begin(0);
        $query->end(1);
        $query->alwaysReturnTotalCount(TRUE);
        if ($testing) { $query->filter('my->test', '=', 'Y'); }
        $query->execute();
        User::setWidgetAttribute($user, 'activityCount', $query->getTotalCount(), XN_Attribute::NUMBER);
        return $user;
    }

}
