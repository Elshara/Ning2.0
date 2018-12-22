<?php

/** @todo: this is probably useful app-wide */
class Profiles_UserHelper {

     const SMALL_BADGE_AVATAR_SIZE = 183;

    /**
     * Determines the friend status for the given user list.
     *
     * @param screenName The screenName to test
     * @param users   The user objects
     * @return An array screen name => status string (contact | friend | pending | requested |
     *         groupie | blocked | not-friend)
     */
    public static function getFriendStatusFor($screenName, $users) {
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        return XG_ContactHelper::getFriendStatusFor($screenName, $users);
    }

    /**
     * Finds friends of a particular user that are members of the site
     *
     * @param $screenName string User to find friends of
     * @param $begin integer optional result set start. Defaults to 0
     * @param $end integer optional result set end. Defaults to 20
     */
    public static function findFriendsOf($screenName, $begin = 0, $end = 20) {
         $query = XN_Query::create('Content')
                                 ->filter('type', '=', 'User')
                                 ->filter('owner')
                                 ->filter('contributorName', 'in', XN_Query::FRIENDS($screenName))
                                 ->begin($begin)
                                 ->end($end)
                                 ->alwaysReturnTotalCount(true);
         User::addBlockedFilter($query);
         User::addPendingFilter($query);
         User::addUnfinishedFilter($query);
         $friends = $query->execute();
         $numFriends = $query->getTotalCount();
         XG_UserHelper::addToSyncMap($friends);
         User::insertIntoUserMap($friends);
         return array('friends' => $friends, 'numFriends' => $numFriends);
    }

    /**
     *  Finds promoted users
     *
     * @param $numUsers integer Number of users to return (default 8)
     * @return array screen names of promoted posters
     */
    public static function getPromotedUsers($numUsers = 8) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        $attribute = XG_PromotionHelper::attributeName();
        $users = User::find(array('promoted' => true),0,$numUsers, array('my->' . $attribute,XN_Attribute::DATE), 'desc', array('keys' => XG_CacheExpiryHelper::promotedObjectsChangedCondition('User')));
        return $users['users'];
    }


    /**
     *  Finds users most recently posting content
     *
     * @param $numUsers integer Number of users to return (default 8)
     * @return array screen names of recent posters
     */
    public static function getActiveUsers($numUsers = 8, $order = 'createdDate') {
         // @todo Just delegate to User::find()  [Jon Aquino 2007-06-07]
         $activeUsers = array();
         $query = XG_Query::create('Content')
                 ->filter('owner')
                 ->filter('type','eic','User')
                 ->filter('contributorName','<>', null)
                 ->end($numUsers)
                 ->order($order, 'desc');
         if ($order == 'createdDate' && ($lookupPeriod = W_Cache::getWidget('main')->privateConfig['newUserLookupPeriod'])) {
            // The presence of the newUserLookupPeriod parameter means the high traffic app,
            // so we don't cache it to avoid the interference with the members grid caching.
            // newUserLookupPeriod sets the lookup period in seconds.
            $query->filter('createdDate','>',date('c',time()-$lookupPeriod));
         } else {
            $query->maxAge(1200)->setCaching(XG_Cache::key('type','User'));
         }
         User::addBlockedFilter($query);
         User::addPendingFilter($query);
         User::addUnfinishedFilter($query);
         $contents = $query->execute();
         XG_UserHelper::addToSyncMap($contents);
         foreach ($contents as $content) {
             $activeUsers[$content->contributorName] = $content->contributorName;
             User::insertIntoUserMap($content);
         }
         //  Now return only the number requested
         if (count($activeUsers) > $numUsers) {
             $activeUsers = array_slice($activeUsers, 0, $numUsers,
                     TRUE /* preserve keys */);
         }
         return $activeUsers;
     }

    /**
      *  Create a friend request.  This will connect two users if the target has
      *    already sent a friend request to the source.
      *
      *  NOTE: Will fail if the sender is not currently logged in!
      *
      *  @param $from - screen name or email address of invitation source user
      *  @param $to - screen name or email address of invitation target user
      */
    public static function createFriendRequest($from, $to) {
        $url = 'http://' . XN_AtomHelper::HOST_APP(XN_Application::load()->relativeUrl)
                . '/xn/rest/1.0/profile:' . rawurlencode($from) . '/contact';
        $url .= '?contact_relationship=friend&contact_id=' . rawurlencode($to)
                . '&xn_out=xml';
        self::doCreateContactQuery($url);
        XG_ContactHelper::clearContactsCache($from,$to);
    }

    /**
      *  Block relationship with a user - block friend requests and messages.
      *
      *  NOTE: Will fail if no blocking user is logged in!
      *
      *  @param $blocking - screen name of user blocking
      *  @param $blocked - screen name of user to be blocked
      */
    public static function createRelationshipBlock($blocking, $blocked) {
        $url = 'http://' . XN_AtomHelper::HOST_APP(XN_Application::load()->relativeUrl)
                . '/xn/rest/1.0/profile:' . rawurlencode($blocking) . '/contact';
        $url .= '?contact_relationship=block&contact_id=' . rawurlencode($blocked)
                . '&xn_out=xml';
        self::doCreateContactQuery($url);
        XG_ContactHelper::clearContactsCache($from,$to);
    }

    /**
     * Returns the age, sex, and location of the given user profile
     *
     * @param XN_Profile $profile the user
     * @return array(string of the user's age and sex, string of the user's location)
     */
    public static function getPrivateUserInfo($profile) {
        if (XG_UserHelper::canDisplayAge($profile) || $forceDisplayAge) { $age = XG_UserHelper::getAge($profile); }
        if (XG_UserHelper::canDisplayGender($profile)) { $gender = (string) XG_UserHelper::getGender($profile); }
        $gender = ($gender ? ($gender == 'f' ? xg_html('FEMALE') : xg_html('MALE')) : null);
        $location = XG_UserHelper::getLocation($profile);
        $country = XG_UserHelper::getCountry($profile);
        $userAgeSex = ($age && $gender ? $age . ', ' . $gender : ($age ? $age : ($gender ? $gender : null)));
        $userLocation = ($location && $country ? $location . ', ' . $country : ($location ? $location : ($country ? $country : null)));
        return array($userAgeSex, $userLocation);

    }

    /**
     * Returns an array of navigational links to a user's content across a network
     *
     * @param XN_Content | W_Content $user the user
     * @return array of navigational links and whether the user is using the feature
     */
    public static function getUserProfileNavigation($user) {
        if (is_null($user->my->xg_photo_albumCount)) {
            W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_AlbumHelper.php');
            Photo_AlbumHelper::updateAlbumCount($user, FALSE); // BAZ-10150 [Jon Aquino 2008-09-19]
            $userUpdated = TRUE;
        }
        if (is_null($user->my->xg_groups_groupCount)) {
            W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_GroupHelper.php');
            Groups_GroupHelper::updateGroupCount($user, FALSE); // BAZ-10191 [Jon Aquino 2008-09-19]
            $userUpdated = TRUE;
        }
        if (!User::isInternalFlagSet($user, 'baz-10144-fix-applied')) {
            W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
            Video_VideoHelper::updateVideoCount($user, FALSE); // BAZ-10144 [Jon Aquino 2008-09-19]
            User::setInternalFlag($user, 'baz-10144-fix-applied');
            $userUpdated = TRUE;
        }
        if (!User::isInternalFlagSet($user, 'baz-10466-fix-applied')) {
            W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
            Photo_PhotoHelper::updatePhotoCount($user, FALSE); // BAZ-10466 [Jon Aquino 2008-09-27]
            User::setInternalFlag($user, 'baz-10466-fix-applied');
            $userUpdated = TRUE;
        }
        if (!User::isInternalFlagSet($user, 'baz-10434-fix-applied')) {
            XG_App::includeFileOnce('/widgets/events/lib/helpers/Events_UserHelper.php');
            Events_UserHelper::updateEventCount($user, FALSE); // BAZ-10434 [Jon Aquino 2008-09-26]
            User::setInternalFlag($user, 'baz-10434-fix-applied');
            $userUpdated = TRUE;
        }
        if ($userUpdated) { $user->save(); }
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        $enabledModules = XG_ModuleHelper::getEnabledModules();
        $screenName = $user->title;
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_SecurityHelper.php');
        // list of mozzles we want to display the link if enabled and if the user has contributed content for
        $modulesLinkList = array(
            'photo' => array (
                'attributeName' => XG_App::widgetAttributeName(W_Cache::getWidget('photo'), 'photoCount'),
                'linkText'      => xg_text('PHOTOS'),
                'viewUrl'       => W_Cache::getWidget('photo')->buildUrl('photo', 'listForContributor', array('screenName' => $screenName)),
                'addUrl'		=> W_Cache::getWidget('photo')->buildUrl('photo', XG_MediaUploaderHelper::action()),
            ),
            'photo_albums' => array (
                'attributeName' => XG_App::widgetAttributeName(W_Cache::getWidget('photo'), 'albumCount'),
                'linkText'      => xg_text('PHOTO_ALBUMS'),
                'viewUrl'       => W_Cache::getWidget('photo')->buildUrl('album', 'listForOwner', array('screenName' => $screenName)),
                'addUrl'		=> W_Cache::getWidget('photo')->buildUrl('album', 'new'),
            ),
            'video' => array (
                'attributeName' => XG_App::widgetAttributeName(W_Cache::getWidget('video'), 'videoCount'),
                'linkText'      => xg_text('VIDEOS'),
                'viewUrl'       => W_Cache::getWidget('video')->buildUrl('video', 'listForContributor', array('screenName' => $screenName)),
                'addUrl'		=> W_Cache::getWidget('video')->buildUrl('video', XG_MediaUploaderHelper::action()),
            ),
            'groups' => array (
                'attributeName' => XG_App::widgetAttributeName(W_Cache::getWidget('groups'), 'groupCount'),
                'linkText'      => xg_text('GROUPS'),
                'viewUrl'       => $viewGroupUrl = W_Cache::getWidget('groups')->buildUrl('group', 'listForContributor', array('user' => $screenName)),
                'addUrl'		=> Groups_SecurityHelper::currentUserCanCreateGroup() ? W_Cache::getWidget('groups')->buildUrl('group', 'new') : $viewGroupUrl,
            ),
            'discussions' => array (
                'module'		=> 'forum',
                'attributeName' => XG_App::widgetAttributeName(W_Cache::getWidget('forum'), 'activityCount'),
                'linkText'      => xg_text('DISCUSSIONS'),
                'viewUrl'       => W_Cache::getWidget('forum')->buildUrl('topic', 'listForContributor', array('user' => $screenName)),
                'addUrl'		=> W_Cache::getWidget('forum')->buildUrl('topic', 'new', array('target' => XG_HttpHelper::currentUrl())),
            ),
            'events' => array (
                'linkText'		=> xg_text('EVENTS'),
                'viewUrl'		=> $viewEventsUrl = W_Cache::getWidget('events')->buildUrl('event', 'listUserEvents', array('user' => $screenName)),
                'addUrl'		=> Events_SecurityHelper::currentUserCanCreateEvent() ? W_Cache::getWidget('events')->buildUrl('event', 'new') : $viewEventsUrl,
            ),
        );

        $postCounts = mb_strlen($user->my->xg_profiles_blogPostArchive) ? unserialize($user->my->xg_profiles_blogPostArchive) : array();
        $count = 0;
        if (is_array($postCounts) && $postCounts['all']) {
            foreach ($postCounts['all'] as $year => $info) {
                $count += array_sum($info);
            }
        }

        $profileLinks = array();
        if (XG_App::openSocialEnabled()) {
            XG_App::includeFileOnce('/widgets/opensocial/models/OpenSocialAppData.php');
            XG_App::includeFileOnce('/widgets/opensocial/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');
            $arr = OpenSocialAppData::loadMultiple(null, $screenName);
            $arr2 = OpenSocial_ApplicationDirectoryHelper::getAppDetails($arr['apps']);
            $profileLinks['applications'] = array(
                'module' => 'opensocial',
                'name' => xg_html('APPLICATIONS'),
                'viewUrl' => W_Cache::getWidget('opensocial')->buildUrl('application','apps',array('user' => $screenName)),
                'addUrl' => W_Cache::getWidget('opensocial')->buildUrl('application','list'),
                'count' => $arr['total']
            );
        }
        $profileLinks['blogs'] = array(
            'module' => $module,                // Whoa!? $module is undefined!  [2008-09-25 Mohan Gummalam]
            'name' => xg_text('BLOG_POSTS'),
            'viewUrl' => W_Cache::getWidget('profiles')->buildUrl('blog','list',array('user' => $screenName)),
            'addUrl' => W_Cache::getWidget('profiles')->buildUrl('blog','new'),
            'count' => $count,
        );
        foreach ($modulesLinkList as $key => $moduleEntry) {
            $moduleName = $moduleEntry['module'] ? $moduleEntry['module'] : $key;
            if ( !array_key_exists($moduleName == 'photo_albums' ? 'photo' : $moduleName, $enabledModules)) {
                continue;
            }
            if ($moduleName == 'events') {
                XG_App::includeFileOnce('/widgets/events/lib/helpers/Events_UserHelper.php');
                $count = Events_UserHelper::determineUpcomingEventCount($user);
            } else {
                $count = $user->my->{$moduleEntry['attributeName']};
            }
            $profileLinks[$key] = array (
                'module' => $moduleName,
                'name' => $moduleEntry['linkText'],
                'viewUrl' => $moduleEntry['viewUrl'],
                'addUrl' => $moduleEntry['addUrl'],
                'count' => $count,
            );
        }
        // BAZ-10026: "Photo Albums" must be after "Photos". So we cannot sort it in the lexical order and we cannot rely on
        // the language-dependent sorting. So we always sort by our module keys. [Andrey 2008-09-23]
        ksort($profileLinks);
        return array_values($profileLinks);
    }

    private static function doCreateContactQuery($url) {
        //  NOTE:  The PHP API should contain or at least facilitate this
        //    functionality.  For now we'll just contact the contact endpoint
        //    directly but this should be changed to a more developer-friendly
        //    approach.
        try {
            $response = XN_REST::post($url);
            $r1 = XN_AtomHelper::XPath($response);
        }
        catch (XN_Exception $e) {
            //  POST fails if the contact object exists
        }
        if (!isset($r1)
                || !is_null($error = $r1->textContent('/errors/element/error', null, true))) {
            //  POST fails if the contact object exists - try PUT:
            try {
                $response = XN_REST::put($url, NULL);
                $r2 = XN_AtomHelper::XPath($response);
            }
            catch (XN_Exception $e) {
                //  Okay, the PUT really shouldn't fail...
                error_log("Exception in PUT to $url: " . $e->getMessage);
            }
            if (!isset($r2)
                    || !is_null($error = $r2->textContent('/errors/element/error', null, true))) {
                throw new Exception('Error in REST call: ' . $error);
            }
        }
    }

}
