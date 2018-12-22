<?php

class Profiles_BulkController extends W_Controller {

    /** Types that action_removeByUser's "blanket deletion" should not automatically delete */
    protected static $typesToExcludeFromRemovalByUser = array(
            'Job', 'PageLayout', 'Category', 'Group', 'GroupIcon', 'GroupInvitation', 'ProfileCustomizationImage', 'Invitation', 'User',
            'SlideshowPlayerImage', 'VideoPlayerImage', 'WatermarkImage', 'PlayerCustomizationImage', 'BlogArchive', 'EventCalendar', 'EventWidget',
            'OpenSocialApp', 'Playlist', 'SiteTabLayout');

    /**
     * Sets the privacy level of a chunk of objects created by the Profiles module.
     *
     * @param   $limit integer          Maximum number of content objects to change (approximate).
     * @param   $privacyLevel  string   Privacy level to swtich to: 'private' or 'public'.
     * @return  array                   'changed' => the number of content objects deleted,
     *                                  'remaining' => 1 or 0 depending on whether or not there are content objects remaining to set privacy of.
     */
    public function action_setPrivacy($limit = null, $privacyLevel = null) {
        XG_SecurityHelper::redirectIfNotOwner();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        if ($privacyLevel !== 'public' && $privacyLevel !== 'private') { throw new Exception("privacyLevel must be 'public' or 'private'"); }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_BulkHelper.php');
        return Profiles_BulkHelper::setPrivacy($limit, ($privacyLevel === 'private'));
    }

    /**
     * Remove all content by the specified user. Arguments start as null
     * so we can make sure this action is only called by dispatch from
     * another action.
     *
     * @param $limit integer maximum number of content objects to remove
     * @param $user string user whose content to remove
     * @return array Information about content deleted and content remaining
     */
    public function action_removeByUser($limit = null, $user = null) {
        if (is_null($limit) || is_null($user)) {
            throw new Exception("This action cannot be called directly.");
        }
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (! XG_SecurityHelper::currentUserCanDeleteUser($user)) {
            throw new Exception("Permission denied.");
        }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        $changed = 0;
        $remaining = 0;

        $userObject = User::load($user);

        // First, this user's comments on blog posts
        $blogComments = Comment::getCommentsBy($user, 0, $limit, null, 'createdDate', 'asc', array('my->attachedToType' => 'BlogPost'));
        $blogCommentsRemoved = self::removeComments($blogComments);
        $changed += $blogCommentsRemoved['changed'];
        $remaining += $blogCommentsRemoved['remaining'];
        // Expire the caches on those blog posts

        // Next, this user's chatters (comments on User objects)
        if ($changed < $limit) {
            $chatters = Comment::getCommentsBy($user, 0, $limit - $changed, null, 'createdDate', 'asc', array('my->attachedToType' => 'User'));
            $chattersRemoved = self::removeComments($chatters);
            $changed += $chattersRemoved['changed'];
            $remaining += $chattersRemoved['remaining'];
            // Invalidate caches on the users' chatter embeds
        }

        // Next, comments on this user's blog posts
        if ($changed < $limit) {
            $commentsOnPosts = Comment::getCommentsForContentBy($user, 0, $limit - $changed, null, 'createdDate','asc',array('my->attachedToType' => 'BlogPost'));
            $commentsRemoved = self::removeComments($commentsOnPosts);
            $changed += $commentsRemoved['changed'];
            $remaining += $commentsRemoved['remaining'];
            // No need for cache invalidation here, since we're going to remove these
            // posts in a jiffy
        }

        // Next, chatters on this user's user object
        if ($changed < $limit) {
            $chattersOnUser = Comment::getCommentsForContentBy($user, 0, $limit - $changed, null, 'createdDate','asc',array('my->attachedToType' => 'User'));
        // error_log("remove($user,$limit,$changed,$remaining) 4: {$chattersOnUser['numComments']}, " . count($chattersOnUser['comments']));
            $chattersRemoved = self::removeComments($chattersOnUser);
            $changed += $chattersRemoved['changed'];
            $remaining += $chattersRemoved['remaining'];
            // No need for cache invalidation here, since we're going to remove
            // this user object in a sec.
        }

        // Next, this user's blog posts
        if ($changed < $limit) {
            $filters = array('contributorName' => $user, 'ignoreVisibility' => true);
            $blogPosts = BlogPost::find($filters, 0, $limit - $changed);
        // error_log("remove($user,$limit,$changed,$remaining) 5: {$blogPosts['numPosts']}, " . count($blogPosts['posts']));
            $postsRemoved = 0;
            foreach ($blogPosts['posts'] as $post) {
                // No need to remove the post from the user's archive list, since we're going
                // to remove the user object soon
                BlogPost::remove($post, false);
                $postsRemoved++;
            }
            $changed += $postsRemoved;
            $remaining += ($blogPosts['numPosts'] - $postsRemoved);
        }

        //  Next, any appearance customization related content objects
        if ($changed < $limit) {
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
            list($newChanged, $newRemaining) = Index_AppearanceHelper::removeByUser($userObject);
            $changed += $newChanged;
            $remaining += $newRemaining;
        }

        // Next, Playlists
        if ($changed < $limit) {
             W_Cache::getWidget('music')->includeFileOnce('/lib/helpers/Music_PlaylistHelper.php');
                list($newChanged, $newRemaining) = Music_PlaylistHelper::removeByUser($userObject);
                $changed += $newChanged;
                $remaining += $newRemaining;
        }

        // Next, featured items
        if ($changed < $limit) {
            W_Cache::getWidget('activity')->includeFileOnce('/lib/helpers/Activity_LogHelper.php');
            list($newChanged, $newRemaining) = Activity_LogHelper::instance()->removeByUser($userObject);
            $changed += $newChanged;
            $remaining += $newRemaining;
        }
        
        // Next, remove OpenSocialAppReview scores from the aggregates on OpenSocialApp, and remove the reviews themselves
        if ($changed < $limit) {
            W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_UserHelper.php');
            list($newChanged, $newRemaining) = OpenSocial_UserHelper::removeReviewsByUser($userObject);
            $changed += $newChanged;
            $remaining += $newRemaining;
        }
        
        // Next, remove from numMembers and members properties on OpenSocialApp objects, and remove OpenSocialAppData objects.
        if ($changed < $limit) {
            W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_UserHelper.php');
            list($newChanged, $newRemaining) = OpenSocial_UserHelper::removeAppsByUser($userObject);
            $changed += $newChanged;
            $remaining += $newRemaining;
        }

        // Any remaining objects (except for the User object) that have not yet been deleted
        if ($changed < $limit) {
            $query = XN_Query::create('Content')
                    ->filter('owner')
                    ->filter('type','!=','User')
                    ->filter('contributorName','=', $user)
                    ->end($limit - $changed);
            // BAZ-6560 [Jon Aquino 2008-03-12]
            if ($userObject->my->thumbnailId) { $query->filter('id', '!=', $userObject->my->thumbnailId); }
            foreach(self::$typesToExcludeFromRemovalByUser as $type) {
                $query->filter('type', '!=', $type);
            }
            $results = $query->execute();
            foreach ($results as $result) {
                XN_Content::delete($result);
                $objectsRemoved++;
            }
            $changed += $objectsRemoved;
            $remaining += ($query->getTotalCount() - $objectsRemoved);
        }

        // Clear the approval link counts
        $userObject->my->commentsToApprove = 0;
        $userObject->my->chattersToApprove = 0;

        if ($changed < $limit) {
            //  Remove the user from all notification lists
            W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_BroadcastHelper.php');
            W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_BroadcastHelper.php');
            W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_NotificationHelper.php');
            Index_NotificationHelper::stopAllFollowing($userObject->contributorName);
            XN_ProfileSet::removeMemberByLabel($userObject->contributorName,
                    Groups_BroadcastHelper::GROUP_BROADCAST_LABEL);
            XN_ProfileSet::removeMemberByLabel($userObject->contributorName,
                    Events_BroadcastHelper::EVENT_BROADCAST_LABEL);
            XN_ProfileSet::removeMemberByLabel($userObject->contributorName,
                    Forum_NotificationHelper::NEW_TOPIC_LABEL);
            $siteBroadcastProfileSet = XN_ProfileSet::load(Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME);
            if ($siteBroadcastProfileSet) { $siteBroadcastProfileSet->removeMember($userObject->contributorName); }
            $changed += 4;
        }

        if ($changed < $limit) {
            //  Now deal with the User object
            //  Is the user deleting himself?
            if ($this->_user->screenName == $userObject->contributorName) {
                //  Delete the user object
                XN_Content::delete(W_Content::unwrap($userObject));
                $changed++;
                // Since the user is NOT being banned, remove it from the
                // USERS alias (BAZ-4606)
                try {
                    $set = XN_ProfileSet::load(XN_ProfileSet::USERS);
                    if ($set) { $set->removeMember($this->_user->screenName); }
                } catch (Exception $e) {
                }
            }
            else {
                //  User is being banned
                //  DON'T delete the user object - mark it as blocked (banned) and leave it
                User::setStatus($userObject, 'blocked');
                // Clear the layout, as it may contain references to objects now deleted (BAZ-3106) [Jon Aquino 2007-05-30]
                $userObject->my->{XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'layout')} = null;
                // @todo Maybe clear xg_*_* attributes to remove references to deleted content objects. See Music_BulkController::action_removeByUser() [Jon Aquino 2007-05-30]
                $userObject->save();
                $changed++;
            }
            XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
            XG_EmbeddableHelper::generateResources();

            //invalidate ActivityLogItem cache so that the homepage dashboard refreshes after user/contents removal
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            XG_ActivityHelper::invalidateCache();
        } else {
            // Ensure that if the user object is not yet updated, the remaining count is not 0
            $remaining++;
        }

        if ($changed < $limit) {
            // BAZ-9909 [Jon Aquino 2008-09-15]
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_NetworkSpecificFriendRequestHelper.php');
            $sentFriendRequests = Profiles_NetworkSpecificFriendRequestHelper::instance()->getFriendRequests($user, XN_Profile::FRIEND_PENDING);
            $receivedFriendRequests = Profiles_NetworkSpecificFriendRequestHelper::instance()->getFriendRequests($user, XN_Profile::GROUPIE);
            // Invalidate up to 5 contacts; others will be invalidated after the 30-minute TTL (FRIEND_REQUESTS_CACHE_MAX_AGE) [Jon Aquino 2008-09-15]
            XG_App::includeFileOnce('/lib/XG_LangHelper.php');
            foreach (array_slice(array_merge($sentFriendRequests, $receivedFriendRequests), 0, 5) as $friendRequest) {
                Profiles_NetworkSpecificFriendRequestHelper::instance()->invalidateFriendRequestsCache($friendRequest['screenName']);
                $changed++;
            }
        }

        return array('changed' => $changed, 'remaining' => $remaining);

    }

    public function action_approveByUser($limit, $user) {

    }

    public function action_approveAll($limit) {

    }

    /**
     * @deprecated Use Comment::removeComments instead.
     */
    protected static function removeComments($comments) {
        $changed = $remaining = 0;
        if (count($comments['comments'])) {
            // Load all of the content that the comments are attached to, so they can be saved after their counts are updated
            $ids = array();
            foreach ($comments['comments'] as $comment) {
                $ids[] = $comment->my->attachedTo;
            }
            $content = XG_Cache::content($ids);
            // Now that all the content is loaded, we can delete each comment
            $status = Comment::removeComments($comments['comments']);
            $changed += $status['changed'];
            // And now save the content that the comments are attached to, since
            // the comment counts have been updated
            foreach ($content as $c) {
                $c->save();
            }
            $remaining += ($comments['numComments'] - $changed);
        }
        if (! is_array($content)) { $content = array(); }
        return array('changed' => $changed, 'remaining' => $remaining, 'content' => $content);
    }

}
