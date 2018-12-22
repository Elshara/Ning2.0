<?php

/**
* Contains utility functions for dealing with security and privacy issues.
*/
class Music_SecurityHelper {
    
    private static function assertIsXnProfile($curUser) {
        if (!($curUser instanceof XN_Profile)) {
            throw new Exception('$curUser must be an XN_Profile');
        }
    }
    
    public static function checkCurrentUserCanAddMusic($curUser) {
        XG_SecurityHelper::assertIsXnProfile($curUser);
        if (XG_SecurityHelper::failed(XG_SecurityHelper::checkCurrentUserIsSignedIn($curUser))) {
            return XG_SecurityHelper::checkCurrentUserIsSignedIn($curUser);
        }
        $widget = W_Cache::current('W_Widget');
        switch ($widget->config['addMusicPermission']) {
            case 'me':
                return XG_SecurityHelper::checkCurrentUserIsAppOwner($curUser);
            case 'friends':
                return XG_SecurityHelper::checkCurrentUserIsAppOwnerOrFriendOf($curUser, XN_Application::load()->ownerName);
            case 'all':
                return null;
            default:
                throw new Exception("Shouldn't get here");
        }
    }
    
    public static function isApprovalRequired() {
        return XG_App::contentIsModerated();
    }
    
    /**
     * Returns whether the current user can in fact share the specified track.
     *
     * @param XN_Content|W_Content track the Track to share
     * @return whether the current user is allowed to share the topic
     */
    public static function currentUserCanShare($track) {
        return ! XN_Profile::current()->isLoggedIn() || XG_App::canSendInvites(XN_Profile::current());
    }
    
    public static function addVisibilityFilter($curUser, $query) {
        self::assertIsXnProfile($curUser);
        if (XG_SecurityHelper::checkCurrentUserIsAdmin($curUser) == null) {
            return;
        }
        if ($curUser->isLoggedIn()) {
            $query->filter(XN_Filter::any(
                    XN_Filter('my.visibility','=','all'),
                    XN_Filter::all(XN_Filter('my.visibility','=','friends'),
                                   XN_Filter('contributor', 'in', XN_Query::FRIENDS())),
                    XN_Filter::all(XN_Filter('my.visibility','in',array('me', 'friends')),
                                   XN_Filter('contributorName', '=', $curUser->screenName))));
        } else {
            $query->filter('my.visibility', '=', 'all');
        }
        return $query;
    }

    private static function randomString($length){
       $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
       for (; mb_strlen($randomString) < $length;){
           $randomString .= $characters[mt_rand(0, count($characters)-1)];
       }
       return $randomString;
    }

    public static function embeddableAccessCode() {
        $widget = W_Cache::current('W_Widget');
        if (! $widget->privateConfig['embeddableAccessCode']) {
            $widget->privateConfig['embeddableAccessCode'] = self::randomString(32);
            $widget->saveConfig();
        }
        return $widget->privateConfig['embeddableAccessCode'];
    }

    public static function canAccessEmbeddableData($urlParameters) {
        return !XG_App::appIsPrivate() || $urlParameters['x'] == self::embeddableAccessCode();
    }
    
}