<?php

class  XG_ActivityHelper {

    const CATEGORY_NEW_CONTENT              = 'newContent';
    const CATEGORY_NEW_COMMENT              = 'newComment';
    const CATEGORY_CONNECTION               = 'connection';
    const CATEGORY_NETWORK                  = 'network';
    const CATEGORY_UPDATE                   = 'update';
    const CATEGORY_GADGET                   = 'gadget';
    const CATEGORY_OPENSOCIAL               = 'opensocial';
    const CATEGORY_STATUS_CHANGE            = 'status_change'; // used for Events. Means the change of RSVP status.
    const CATEGORY_FEATURE                  = 'feature';
    const CATEGORY_USER_PROFILE             = 'user_profile';

    const SUBCATEGORY_GROUP                 = 'group';
    const SUBCATEGORY_TOPIC                 = 'topic';
    const SUBCATEGORY_GROUP_TOPIC           = 'group_topic';
    const SUBCATEGORY_BLOG                  = 'blog';
    const SUBCATEGORY_TRACK                 = 'track';
    const SUBCATEGORY_HOME_TRACK            = 'home_track';
    const SUBCATEGORY_VIDEO                 = 'video';
    const SUBCATEGORY_PHOTO                 = 'photo';
    const SUBCATEGORY_ALBUM                 = 'album';
    const SUBCATEGORY_EVENT                 = 'events';
    const SUBCATEGORY_NOTES                 = 'notes';
    const SUBCATEGORY_PROFILE               = 'profile';
    const SUBCATEGORY_MESSAGE               = 'message';
    const SUBCATEGORY_NETWORK_CREATED       = 'network_created';
    const SUBCATEGORY_FRIEND                = 'friend';
    const SUBCATEGORY_ADD_APP               = 'add_app';
    const SUBCATEGORY_APP_MSG               = 'app_msg';
    const SUBCATEGORY_APP_REVIEW            = 'app_review';
    const SUBCATEGORY_MEMBER                = 'user';
    const SUBCATEGORY_BLOG_POST             = 'post';
    const SUBCATEGORY_PROFILE_PHOTO         = 'profile_photo';
    const SUBCATEGORY_MUSIC                 = 'music';

    const SUBCATEGORY_MESSAGE_QUESTIONS_UPDATE  = 'questions_update';
    const SUBCATEGORY_MESSAGE_NEW_FEATURE       = 'new_feature';

    const SUBCATEGORY_FACT_MESSAGE          = 'fact_message';
    const SUBCATEGORY_FACT_TOP_PHOTO        = 'fact_top_photo';
    const SUBCATEGORY_FACT_TOP_VIDEO        = 'fact_top_video';
    const SUBCATEGORY_FACT_TOP_MUSIC        = 'fact_top_music';
    const SUBCATEGORY_FACT_TOP_TOPIC        = 'fact_top_topic';

    const SUBCATEGORY_FACT_TOP_BLOGPOST     = 'fact_top_blogpost';
    const SUBCATEGORY_FACT_TODAY_EVENT      = 'fact_today_event';
	const SUBCATEGORY_FACT_TOMORROW_EVENT   = 'fact_tomorrow_event';
    const SUBCATEGORY_FACT_PHOTO_CHAMPION   = 'fact_photo_champion';
    const SUBCATEGORY_FACT_VIDEO_CHAMPION   = 'fact_video_champion';
    const SUBCATEGORY_FACT_MUSIC_CHAMPION   = 'fact_music_champion';


    /**
     * Returns the most recent updates for a given user screenname
     *
     * */
    public function getUserActivityLog($screenname = null, $begin = 0, $end = 100, $ids = null, $contentId = null, $order = 'desc', $onlypublic = false, $isOwnedByCurrentUser=false) {
        $query = XG_Query::create('Content')
                         ->filter('owner')
                         ->filter('type', '=', 'ActivityLogItem')
                         ->order('createdDate', $order)
                         ->begin($begin)
                         ->end($end)
                         ->setCaching('recent-activity-items')
                         ->maxAge(300); /* 300s = 5m */
        if ($screenname){
            $user = User::load($screenname);
            $query->filter('createdDate','>=',$user->createdDate); // BAZ-5496
            if ($isOwnedByCurrentUser){
                $query->filter( XN_Filter::any(
                             XN_Filter('my->category','=', self::CATEGORY_NETWORK),
                             XN_Filter('my->members',  'likeic', $screenname)
                             ));
            }else{
                $query->filter('my->members',  'likeic', $screenname);
            }
        }
        // Only show app messages on profile pages but don't prevent getting ALL related log items for deletion when deleting a particular content item.
        if (! $screenname && ! $contentId && ! $ids) {
            $query->filter('my->subcategory', '!=', self::SUBCATEGORY_APP_MSG);
        }
        if (! XG_App::openSocialEnabled()) {
            $query->filter('my->category', '!=', self::CATEGORY_OPENSOCIAL);
        }
        if($onlypublic){
            $query->filter( XN_Filter::any(
                         XN_Filter('my->visibility', '=', 'all'),
                         XN_Filter('my->visibility', '=', null)
                         ));
        }
        if ($contentId)  $query->filter('my->contents', 'likeic', $contentId);
        if ($ids)        $query->filter('id', 'in', $ids);
        $query->alwaysReturnTotalCount(true);
        $items    = $query->execute();
        return array('items'=>$items, 'numItems'=> $query->getTotalCount());
    }

    /**
     * Returns true if the provide $category and $subcategory are subject to
     * having the member info turned off
     *
     * @param string $category category of item potentially being logged
     * @param string $subcategory subcategory of item potentially being logged
     * @return bool
     *
     * @see logActivityIfEnabled(), XG_App::logProfileUpdates()
     */
    private static function memberProfileUpdatesAreOff($category, $subcategory) {
        return (
                   $category == self::CATEGORY_UPDATE
                   || ($category == self::CATEGORY_USER_PROFILE && $subcategory == self::SUBCATEGORY_PROFILE_PHOTO)
               ) && XG_App::logProfileUpdates() === false;
    }

    /**
    * check to see if the network should log a particular activity or not and creates one if yes
    **/
    public function logActivityIfEnabled($category, $subcategory=null, $members=null, $contents=null, $message=null, $widgetName=null, $title=null, $link=null, $save = true){
        // TODO: Perhaps in the future, instead of calling this function from various places,
        // call it from one place: ActivityLogItem::beforeSave().  [Jon Aquino 2007-08-29]

        // To protect people's privacy, avoid logging activities inside private groups,
        // e.g., joining, starting discussions, replying [Jon Aquino 2007-08-29]
        if (XG_GroupHelper::groupIsPrivate()) { return null; }
        if (($category == self::CATEGORY_NEW_CONTENT)   &&(!XG_App::logNewContent())    ) { return null;}
        if (($category == self::CATEGORY_NEW_COMMENT)   &&(!XG_App::logNewComments())   ) { return null;}
        if (($category == self::CATEGORY_CONNECTION)    &&(!XG_App::logNewMembers())    ) { return null;}
        if (($subcategory == self::SUBCATEGORY_FRIEND)  &&(!XG_App::logFriendships())   ) { return null;}
        if (self::memberProfileUpdatesAreOff($category, $subcategory)) { return null;}
        if (($subcategory == self::SUBCATEGORY_EVENT)   &&(!XG_App::logNewEvents()))      { return null;}
        if (($category == self::CATEGORY_OPENSOCIAL) &&(!XG_App::logOpenSocial()))     { return null;}
        try{ //an error logging the activity should not prevent any other function to work
            XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
            $enabledModules = XG_ModuleHelper::getEnabledModules();
            $loadedUsers = array();
            if($enabledModules['activity']!=null) {
                // for CATEGORY_USER_PROFILE/SUBCATEGORY_PROFILE_PHOTO the log item stores a reference to the User object
                // so it never makes sense to have more than one of these per user. (BAZ-9237) [ywh 2008-08-25]
                $removeActivityItems = array();
                if (($category == self::CATEGORY_USER_PROFILE) && ($subcategory == self::SUBCATEGORY_PROFILE_PHOTO)) {
                    $results = XN_Query::create('content')->filter('type', 'eic', 'ActivityLogItem')->filter('my->category', 'eic', self::CATEGORY_USER_PROFILE)->filter('my->subcategory', 'eic', self::SUBCATEGORY_PROFILE_PHOTO)->filter('my->contents', 'eic', $contents[0]->id)->execute();
                    foreach ($results as $result) {
                        $removeActivityItems[] = $result->id;
                    }
                }
                // Remove items relating to reviews for the same app from the same user (fires when a user updates their review) so there is only one activity log item per user review.
                if ($subcategory == self::SUBCATEGORY_APP_REVIEW) {
                    $results = XN_Query::create('Content')->filter('owner')->filter('type', 'eic', 'ActivityLogItem')
                        ->filter('my->subcategory', '=', self::SUBCATEGORY_APP_REVIEW)->filter('my->members', '=', $members)->filter('my->link', '=', $link)->execute();
                    foreach ($results as $result) {
                        $removeActivityItems[] = $result->id;
                    }
                }
                $widget = W_Cache::getWidget('activity');
                $logItem = W_Content::create('ActivityLogItem',$title);
                $contentIds = array();
                $titles     = array();
                if(count($contents) > 0){
                    foreach($contents as $content){
                        if ($content->type == 'User') {
                            $loadedUsers[$content->my->lowercaseScreenName] = $content;
                        }
                        $contentIds[]   = $content->id;
                        //topic replies should store the topic title as part of the activity log item so we dont have to query it on listing time
                        // if the topic is within a group then we'll also store the group name for display
                        if($content->my->attachedToType == 'Topic'){
                            $attachedTo         = XN_Content::load($content->my->attachedTo);
                            $titles[]           = urlencode($attachedTo->title);
                        }
                        if($content->type == 'Group') {
                            $titles[]           = urlencode($content->title);
                        }
                        if ($content->my->visibility){
                            $logItem->my->visibility = $content->my->visibility;
                        }
                    }
                }

                /**
                 * @internal Do this check after iterating over $contents in order to avoid
                 *           a double iteration.  {@link XG_Query::execute()}, which is used
                 *           by {@link User::load()} in this loop will effectively reset any
                 *           changes made to the user's XN_Content in memory.  See BAZ-8966
                 *           for more information.
                 */
                if(!is_null($members)) {
                    $users = explode(',', $members);
                    foreach($users as $screenName) {
                       $user = isset($loadedUsers[$screenName]) ? $loadedUsers[$screenName] : User::load($screenName);
                       //one of the members involved does not want this update to be logged
                       if (
                             (($user->my->activityNewContent    == 'N') && ($category == self::CATEGORY_NEW_CONTENT))   ||
                             (($user->my->activityNewComment    == 'N') && ($category == self::CATEGORY_NEW_COMMENT))   ||
                             (($user->my->activityProfileUpdate == 'N') && ($category == self::CATEGORY_UPDATE))        ||
                             (($user->my->activityFriendships   == 'N') && ($subcategory == self::SUBCATEGORY_FRIEND))
                            ){
                             return null;
                         }
                    }
                }

                $logItem->isPrivate                     = XG_App::appIsPrivate();
                $logItem->my->category                  = $category;
                $logItem->my->subcategory               = $subcategory;
                $logItem->my->members                   = $members;
                $logItem->my->contents                  = implode(',', $contentIds);
                $logItem->my->titles                    = implode(',', $titles);
                $logItem->my->link                      = $link;
                $logItem->set('description', $message, XN_Attribute::STRING);
                $logItem->my->widgetName                = $widgetName;
                $logItem->my->excludeFromPublicSearch   = "Y";

                // remove redundant activity items (BAZ-9237) [ywh 2008-08-25]
                if (count($removeActivityItems) > 0) {
                    // do not allow exceptions here to halt execution
                    try {
                        XN_Content::delete($removeActivityItems);
                    } catch (Exception $e) {
                        // do nothing
                    }
                }

                /* invalidate the cache results of recent-activity-items */
                self::invalidateCache();

                /* log item ready to be saved, search for the latest 5 network activities
                and if it is possible to combine the new one with any of the recent do that instead of adding a new one */
                $recentNetworkLogItemsData = self::getUserActivityLog(null, 0, 5);
                $canBeCombined = false;
                foreach ($recentNetworkLogItemsData['items'] as $networkLogItem) {
                    if ($combinedItem = self::combineItems($logItem, $networkLogItem)) {
                        //can be combined
                        $canBeCombined = true;
                        $networkLogItem = $combinedItem;
                        if ($save) {
                            $networkLogItem->save();
                        }
                        return $networkLogItem;
                    }
                }
                if(!$canBeCombined) {
                    if ($save) {
                        $logItem->save();
                    }
                    return $logItem;
                }
            }

        } catch(Exception $e){
            error_log($e->getMessage());
        }
        return null;
    }

    /**
    * Take two ActivityLogItem contents as input and try to combine them into one, following the rules described at the
    * example matrix of the document "Activity log (Dashboard) Implementation" (clearspace)
    * Returns the combined item if the merge was possible or FALSE if the two items cannott be grouped
    **/
    public function combineItems($item1, $item2) {
        if ((time() - strtotime($item2->createdDate)) > 86400) {
            return false;
        }

        if (($item1->my->category == $item2->my->category)&&($item1->my->subcategory == $item2->my->subcategory)) {
            if( (($item1->my->category == self::CATEGORY_NEW_CONTENT)   && ($item1->my->subcategory == self::SUBCATEGORY_GROUP))    ||
                (($item1->my->category == self::CATEGORY_NEW_CONTENT)   && ($item1->my->subcategory == self::SUBCATEGORY_TOPIC))    ||
                (($item1->my->category == self::CATEGORY_NEW_CONTENT)   && ($item1->my->subcategory == self::SUBCATEGORY_BLOG))    ||
                (($item1->my->category == self::CATEGORY_NEW_CONTENT)   && ($item1->my->subcategory == self::SUBCATEGORY_VIDEO))    ||
                (($item1->my->category == self::CATEGORY_NEW_CONTENT)   && ($item1->my->subcategory == self::SUBCATEGORY_PHOTO))    ||
                (($item1->my->category == self::CATEGORY_NEW_CONTENT)   && ($item1->my->subcategory == self::SUBCATEGORY_ALBUM))    ||
                (($item1->my->category == self::CATEGORY_NEW_CONTENT)   && ($item1->my->subcategory == self::SUBCATEGORY_TRACK))    ||
                (($item1->my->category == self::CATEGORY_NEW_CONTENT)   && ($item1->my->subcategory == self::SUBCATEGORY_HOME_TRACK))    ||
                (($item1->my->category == self::CATEGORY_UPDATE)        && ($item1->my->subcategory == self::SUBCATEGORY_PROFILE))  ||
                (($item1->my->category == self::CATEGORY_CONNECTION)    && ($item1->my->subcategory == self::SUBCATEGORY_GROUP))    ||
                (($item1->my->category == self::CATEGORY_FEATURE))
                    ){
                //combine if the contents have the same creator
                    if($item1->my->members == $item2->my->members || self::_isFeaturedProfile($item1)) {
                        $item2->my->contents = $item1->my->contents.','.$item2->my->contents;
                        // currently just doing this for profile updates; may not be safe for all types [Phil McCluskey 2008-04-06]
                        if (($item1->my->category == self::CATEGORY_UPDATE) && ($item1->my->subcategory == self::SUBCATEGORY_PROFILE)) {
                            $item2->my->contents = implode(',',array_unique(explode(',',$item2->my->contents)));
                        }
                        if (self::_isFeaturedProfile($item1) && self::_isFeaturedProfile($item2)) {
                            $item2->my->members = $item1->my->members . ',' . $item2->my->members;
                        }
                        self::createTempIdList($item1,$item2);
                        return $item2;
                    }
            }
            // photos and videos comments on the same item
            if ($item1->my->category == self::CATEGORY_NEW_COMMENT && ($item1->my->subcategory == self::SUBCATEGORY_PHOTO || $item1->my->subcategory == self::SUBCATEGORY_VIDEO)) {
                $contents1 = explode(',',$item1->my->contents);
                $contents2 = explode(',',$item2->my->contents);
                if ($contents1[0] == $contents2[0]) {
                    $item2->my->members = $item1->my->members . ',' . $item2->my->members;
                    self::createTempIdList($item1,$item2);
                    return $item2;
                }
            }
            // weed out multiple new profile annoucements if any have crept in BAZ-5384
            if ($item1->my->category == self::CATEGORY_CONNECTION && $item1->my->subcategory == self::SUBCATEGORY_PROFILE && $item1->my->members == $item2->my->members) {
                return $item2;
            }
            // identical friend requests BAZ-8016
            if ($item1->my->category == self::CATEGORY_CONNECTION && $item1->my->subcategory == self::SUBCATEGORY_FRIEND && $item1->my->members == $item2->my->members) {
                return $item2;
            }
            // topics in groups
            if (($item1->my->category == self::CATEGORY_NEW_CONTENT)   && ($item1->my->subcategory == self::SUBCATEGORY_GROUP_TOPIC) && array_intersect(explode(',',$item1->my->contents), explode(',',$item2->my->contents)) && $item1->my->members == $item2->my->members) {
                $item2->my->contents = $item1->my->contents.','.$item2->my->contents;
                self::createTempIdList($item1,$item2);
                return $item2;
            }
        }
        //default
        return FALSE;
    }

    /**
     * Returns true if the provided $item is a featured member profile
     *
     * @param XN_Content $item1 An ActivityLogItem
     * @return bool
     * @see combineItems()
     */
    private static function _isFeaturedProfile($item1) {
        return $item1->my->category == self::CATEGORY_FEATURE &&
            $item1->my->subcategory == self::SUBCATEGORY_MEMBER;
    }

    /**
    * run through the items and combine consecutive similar entries (2 or more photos from the same user in a row for instance)
    **/
    public function mergeSimilar($logItems) {
        if (count($logItems) > 1){
            $combinedItems = array(current($logItems));
            while( $nextItem = next($logItems) ) {
                $lastCombinedItem = end($combinedItems);
                if($newItem = XG_ActivityHelper::combineItems($lastCombinedItem, $nextItem)){
                    $combinedItems[(count($combinedItems)-1)] = $newItem;
                } else {
                    array_push($combinedItems, $nextItem);
                }
            }
            return $combinedItems;
        } else {
            return $logItems;
        }
    }

    /**
    *  creates a tempIdList from item1 and item2 and returns it
    *  tempIdList is not part of the ActivityLogItem model and is not meant to be ever saved
    *  this is a temporary list of the combined items ids to be used in case the owner wants
    *  to delete the combined activity item (delete all the individual items contained in the combined item)
    **/
    public function createTempIdList(&$item1, &$item2) {
        try{
            $item1->my->tempIdList  = ($item1->my->tempIdList) ? $item1->my->tempIdList : $item1->id;
            $item2->my->tempIdList  = ($item2->my->tempIdList) ? $item2->my->tempIdList : $item2->id;
            $item2->my->tempIdList  = $item1->my->tempIdList.','.$item2->my->tempIdList;
        }catch (Exception $e){}
        // TODO: I think the Exception occurs when the items are W_Content. Anyway, we should
        // redesign this so that tempIdList is not needed. [Jon Aquino 2008-03-01]
    }

    /**
     * Remove activity item related to this content
     *
     *
     * @param $content XN_Content|W_Content 
     *
     * @todo refactor actual delete to allow deleting more than one item
     *       via removeFromAllActivityItems()
     */
    public static function removeFromActivityLogItem($content) {
        $resultSet = XN_Query::create('Content')
            ->filter('owner')
            ->filter('type', '=', 'ActivityLogItem')
            ->filter('my.contents', 'like', $content->id)
            ->begin(0)
            ->end(1)
            ->execute();
        $activityLog = $resultSet[0];
        $featuredIds = explode(',', $activityLog->my->contents);
        if (count($featuredIds) > 1) {
            $key = array_search($content->id, $featuredIds);
            unset($featuredIds[$key]);
            $activityLog->my->contents = implode(',', $featuredIds);
            $activityLog->save();
        } else {
            XN_Content::delete($activityLog);
        }
        self::invalidateCache();
    }

    /**
     * Invalidate the recent activity cache
     */
    public static function invalidateCache() {
        XG_Query::invalidateCache('recent-activity-items');
    }
}
