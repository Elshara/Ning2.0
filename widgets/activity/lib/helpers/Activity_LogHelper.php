<?php
class Activity_LogHelper {

    public static function isFact($item){
        return (

            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE)         ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_PHOTO)       ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TODAY_EVENT)     ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOMORROW_EVENT)  ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_VIDEO)       ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_MUSIC)       ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_TOPIC)       ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_BLOGPOST)    ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_PHOTO_CHAMPION)  ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_VIDEO_CHAMPION)  ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_MUSIC_CHAMPION)
        );
    }

    public static function isStatement($item){
        return (
            ($item->my->category != XG_ActivityHelper::CATEGORY_GADGET) &&
            (($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE)                  ||
             ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_NETWORK_CREATED)          ||
             ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE_QUESTIONS_UPDATE) ||
             ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE_NEW_FEATURE))
        );
    }

    public static function isFeature($item) {
        return $item->my->category == XG_ActivityHelper::CATEGORY_FEATURE;
    }

    public static function hasAvatar($item){
        return (
            ($item->my->category == XG_ActivityHelper::CATEGORY_GADGET)                 ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_GROUP)            ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_GROUP)            ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_TOPIC)            ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_GROUP_TOPIC)      ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_BLOG)             ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_TRACK)            ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_PHOTO)            ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_ALBUM)            ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_EVENT)            ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_PROFILE)          ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_HOME_TRACK)       ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_VIDEO)            ||
            ($item->my->category    == XG_ActivityHelper::CATEGORY_USER_PROFILE)        ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_ADD_APP)          ||
            ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_APP_REVIEW)
        );
    }


    /** Singleton instance of this helper. */
    private static $instance = NULL;

    /**
     * Returns the singleton instance of this helper.
     *
     * @return Activity_LogHelper  the singleton
     */
    public static function instance() {
        if (is_null(self::$instance)) { self::$instance = new Activity_LogHelper(); }
        return self::$instance;
    }

    /**
     * Returns the HTML to use for the activity log item reporting a new friendship.
     *
     * @param $screenNames array  the friend-request recipient followed by the senders
     * @param $friendCount integer  (optional) total number of new friendships
     * @param $onMyProfilePage boolean  whether the current page is the current user's profile page
     * @param $currentScreenName string  screen name of the current user
     * @return string  the HTML for the activity message
     */
    public function friendshipMessageHtml($screenNames, $friendCount, $onMyProfilePage, $currentScreenName) {
        if (!$friendCount) { $friendCount = count($screenNames) - 1; } // Backwards compatibility with pre-3.6 activity log items [Jon Aquino 2008-08-07]
        $friendScreenNameCount = count($screenNames) - 1;
        if ($onMyProfilePage && $screenNames[0] != $currentScreenName) {
            return xg_html('YOU_AND_X_ARE_NOW_FRIENDS', $this->profileLink($screenNames[0]));
        }
        if ($onMyProfilePage && $friendScreenNameCount == 1 && $screenNames[1] != $currentScreenName) {
            return xg_html('YOU_AND_X_ARE_NOW_FRIENDS', $this->profileLink($screenNames[1]));
        }
        if ($friendScreenNameCount == 1) {
            return xg_html('X_AND_Y_ARE_NOW_FRIENDS', $this->profileLink($screenNames[0]), $this->profileLink($screenNames[1]));
        }
        if ($friendScreenNameCount == 2) {
            return xg_html('X_FRIENDS_WITH_Y1_AND_Y2', $this->profileLink($screenNames[0]), $this->profileLink($screenNames[1]), $this->profileLink($screenNames[2]));
        }
        if ($friendScreenNameCount == 3) {
            return xg_html('X_FRIENDS_WITH_Y1_Y2_AND_Y3', $this->profileLink($screenNames[0]), $this->profileLink($screenNames[1]), $this->profileLink($screenNames[2]), $this->profileLink($screenNames[3]));
        }
        if ($friendScreenNameCount == 4) {
            return xg_html('X_FRIENDS_WITH_Y1_Y2_Y3_AND_Y4', $this->profileLink($screenNames[0]), $this->profileLink($screenNames[1]), $this->profileLink($screenNames[2]), $this->profileLink($screenNames[3]), $this->profileLink($screenNames[4]));
        }
        if ($friendScreenNameCount >= 5 && $friendCount == 5) {
            return xg_html('X_FRIENDS_WITH_Y1_Y2_Y3_Y4_AND_Y5', $this->profileLink($screenNames[0]), $this->profileLink($screenNames[1]), $this->profileLink($screenNames[2]), $this->profileLink($screenNames[3]), $this->profileLink($screenNames[4]), $this->profileLink($screenNames[5]));
        }
        if ($friendScreenNameCount >= 5 && $friendCount > 5) {
            return xg_html('X_FRIENDS_WITH_Y1_Y2_Y3_Y4_Y5_AND_N_OTHERS', $friendCount - 5, $this->profileLink($screenNames[0]), $this->profileLink($screenNames[1]), $this->profileLink($screenNames[2]), $this->profileLink($screenNames[3]), $this->profileLink($screenNames[4]), $this->profileLink($screenNames[5]));
        }
    }

    /**
     * Returns HTML for a link to a member's profile page.
     *
     * @param $screenName string  the username
     * @return string  HTML for the <a> element
     */
    protected function profileLink($screenName) {
        return xg_userlink(XG_Cache::profiles($screenName));
    }

    /**
     * Removes ActivityLogItems that feature this profile
     *
     * @param XN_Content $user
     * @return array (# of removed, # of remaining)
     */
    public function removeByUser($user) {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        $removed = 0;
        $remaining = 0;

        // remove any profile features
        $query = XN_Query::create('Content')
            ->filter('owner')
            ->filter('type', '=', 'ActivityLogItem')
            ->filter('my.members', 'like', $user->contributorName)
            ->alwaysReturnTotalCount(true);
        $result = $query->execute();
        $toDelete = array();
        // @todo refactor into something that is shared with XG_ActivityHelper code
        if (($resultCount = count($result)) > 0) {
            $removed =+ $resultCount;
            foreach ($result as $activityLogItem) {
                $members = explode(',', $activityLogItem->my->members);
                if (count($members) > 1 && $activityLogItem->my->category == XG_ActivityHelper::CATEGORY_FEATURE) {
                    // remove user's screenname from my.members
                    $key = array_search($user->contributorName, $members);
                    unset($members[$key]);
                    $activityLogItem->my->members = implode(',', $members);

                    // remove user ID from my.contents
                    $contents = explode(',', $activityLogItem->my->contents);
                    $key = array_search($user->id, $contents);
                    unset($contents[$key]);
                    $activityLogItem->my->contents = implode(',', $contents);

                    $activityLogItem->save();
                } else {
                    $toDelete[] = $activityLogItem;
                }
            }

            if (count($toDelete) > 0) {
                XN_Content::delete($toDelete);
            }
        }

        if (($totalCount = $query->getTotalCount()) > $resultCount) {
            $remaining = $totalCount - $resultCount;
        }

        if ($removed > 0) {
            $user->save();
        }
        return array($removed, $remaining);
    }

}
?>