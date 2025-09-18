<?php

/**
 * Helper functions related to users in chat
 */
class Chat_UserHelper {

    const CHAT_ANONYMOUS_SCREENNAME = '_xn_anonymous';
    const MAX_ADMIN_USERS = 100; /* users */
    const USER_QUERY_CACHE_TTL = 300; /* seconds */

    /**
     * Returns an array of administrative users - limited to the first 100 admins
     * Query is cached for 5 minutes.
     *
     * @return Array(screenName)  screenNames of all administrative users on the app
     */
    public static function getAdministrativeUserScreenNames() {
        $users = XG_Query::create('Content')
                    ->filter('owner')
                    ->filter('type','eic','User')
                    ->filter(XN_Filter::any(
                                XN_Filter('my->isAdmin','=','Y'),
                                XN_Filter('contributorName','=',XN_Application::load()->ownerName)))
                    ->begin(0)
                    ->end(self::MAX_ADMIN_USERS)
                    ->setCaching('chat-admin-users')
                    ->maxAge(self::USER_QUERY_CACHE_TTL)
                    ->alwaysReturnTotalCount(true)
                    ->execute();

        return User::screenNames($users);
    }

    /**
     * Returns true if the current chat user is anonymous
     *
     * @param userId string  the user's id as returned by getChatUserDetails
     *
     * @return boolean  true if the current user is anonymous
     */
    public static function isAnonymous($userId) {
        return $userId === self::CHAT_ANONYMOUS_SCREENNAME;
    }

    /**
     * Returns an associative array of information about the specified user, or the current
     * user if none is specified
     *
     * @param profile XN_Profile  the user for which to generate chat user details
     * @param userOnlineStatus string  the current status 'online' or 'offline'
     *
     * @return Array(k=>v)  key=value pairs of chat user information
     */
    public static function getChatUserDetails($profile = null, $userOnlineStatus = null) {
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_TemplateHelpers.php');
        XG_App::includeFileOnce('/lib/XG_UserHelper.php');

        if (is_null($profile) || ! ($profile instanceof XN_Profile)) {
            $profile = XN_Profile::current();
        }

        $thumbnailUrlSmall = null;
        $profileUrl = null;
        $fullName = null;
        $gender = null;
        $age = null;
        $location = null;
        $country = null;
        $isPending = false;

        $screenName = $profile->screenName;
        W_Cache::getWidget('chat')->includeFileOnce('/lib/helpers/Chat_ConnectionHelper.php');
        if ($userOnlineStatus === null) {
            $userOnlineStatus = Chat_ConnectionHelper::CHAT_STATUS_OFFLINE;
        }

        if (User::isMember($profile) && ($userOnlineStatus === Chat_ConnectionHelper::CHAT_STATUS_ONLINE)) {
            $thumbnailUrlSmall = XG_UserHelper::getThumbnailUrl($profile, 16, 16);
            $fullName = XG_UserHelper::getFullName($profile);
            $profileUrl = xg_absolute_url(User::profileUrl($screenName));

            if (XG_UserHelper::canDisplayGender($profile)) {
                $gender = XG_UserHelper::getGender($profile);
            } else {
                $gender = "N/A";
            }

            if (XG_UserHelper::canDisplayAge($profile)) {
                $age = XG_UserHelper::getAge($profile);
            } else {
                $age = "N/A";
            }

            $location = XG_UserHelper::getLocation($profile);
            $country = XG_UserHelper::getCountry($profile);
            $isPending = User::isPending($profile);
        } else {
            $xnVisitor = $_COOKIE['xn_visitor'];
            if ($xnVisitor == NULL) {
                $rand = rand(0,1000000000);
                $xnVisitor = "$rand";
            }
            //the id is used in the URL and the page is cached by the resolvers
            $screenName = self::CHAT_ANONYMOUS_SCREENNAME;
        }
        return array('id' => $screenName,
                     'thumbnailUrlSmall' => $thumbnailUrlSmall,
                     'profileUrl' => $profileUrl,
                     'fullName' => $fullName,
                     'gender' => $gender,
                     'age' => $age,
                     'location' => $location,
                     'country' => $country,
                     'isPending' => $isPending,
                     'isAdmin' => XG_SecurityHelper::userIsAdmin(),
                     'isNC' => XG_SecurityHelper::userIsOwner());
    }

}

