<?php
XG_App::includeFileOnce('/lib/XG_LockingCacheController.php');

/**
 * Dispatches requests made to the /xn/members/1.0 endpoint.
 */
class Index_MembersController extends XG_LockingCacheController {

    /**
     * Returns a feed of member avatars to show on the sign-in pages.
     * Handles requests made to the /xn/members/1.0/featured endpoint.
     */
    public function action_10_featured() {
        header("Content-Type: text/xml");
        $minute = 60;
        $this->setLockingCaching(__METHOD__, md5(implode(',', array(XG_HttpHelper::currentUrl(), XG_App::appIsPrivate() ? 'Y' : 'N'))), 30 * $minute);
        if (XG_App::appIsPrivate()) {
            $this->profiles = array();
        } else {
            $n = 6;
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
            if (count($users = Profiles_UserHelper::getPromotedUsers($n)) < $n) {
                $users = array_merge($users, Profiles_UserHelper::getActiveUsers($n));
            }
            $this->profiles = array_slice(XG_Cache::profiles($users), 0, $n, TRUE);
        }
    }

}
