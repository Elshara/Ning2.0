<?php

/**
 * Useful functions for working with bulk operations.
 */
class Forum_BulkHelper {

    /**
     * Changes the privacy level of a chunk of objects from the content store.
     * To be used as part of a network's switch from public->private or private->public.
     *
     * @param   $limit integer          Maximum number of items to switch privacy level before returning.
     * @param   $toPrivate boolean      true if switching to private, false if switching to public.
     * @return  array                   ['changed'] The number of content objects changed,
     *                                  ['remaining'] 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public static function setPrivacy($limit, $toPrivate) {
        XG_App::includeFileOnce('/lib/XG_PrivacyHelper.php');
        $excludedTypes = array('Comment', 'TopicCommenterLink', 'PageCommenterLink');
        $changed = XG_PrivacyHelper::setContentPrivacy($limit, $toPrivate, 'forum', $excludedTypes);
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }

    /**
     * Removes a Topic and its Comments, and UploadedFiles attached to
     * the Topic or Comments.
     *
     * @param $topic XN_Content|W_Content  The Topic to delete
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @return array  [0] The number of content objects deleted (approximate), and [1] 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public static function remove($topic, $limit) {
        $changed = 0;
        $x = Comment::getCommentsFor($topic->id, 0, $limit - $changed);
        foreach($x['comments'] as $comment) {
            Forum_FileHelper::deleteAttachments($comment);
            XN_Content::delete(W_Content::unwrap($comment));
        }
        $changed += count($x['comments']);
        if ($changed < $limit) {
            foreach (TopicCommenterLink::links($topic->id, $limit - $changed) as $topicCommenterLink) {
                XN_Content::delete($topicCommenterLink);
                $changed++;
            }
        }
        if ($changed < $limit) {
            $categoryId = $topic->my->categoryId;
            Forum_FileHelper::deleteAttachments($topic);
            XN_Content::delete(W_Content::unwrap($topic));
            if ($categoryId) {
                Category::updateDiscussionCountAndActivity($categoryId, null, true);
            }
            $changed++;
        }
        Forum_UserHelper::updateActivityCount(User::load(XN_Profile::current()->screenName));
        return array($changed, $changed >= $limit ? 1 : 0);
    }

    /**
     * Removes Forum objects created by the specified user.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $user string  Username of the person whose content to remove.
     * @param $groupId string  Group ID to filter on, or null to include all groups and the main Forum
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public static function removeByUser($limit, $user, $groupId = null) {
        $changed = 0;
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('my->mozzle', '=', W_Cache::current('W_Widget')->dir);
        $query->filter('contributorName', '=', $user);
        $query->filter('type', '=', 'Topic');
        if (defined('UNIT_TESTING')) { $query->filter('my->test', '=', 'Y'); }
        if ($groupId) { $query->filter('my->groupId', '=', $groupId); }
        $query->begin(0);
        $query->end($limit - $changed);
        foreach ($query->execute() as $topic) {
            if ($changed < $limit) {
                $results = self::remove($topic, $limit - $changed);
                $changed += $results[0];
            }
        }
        if ($changed < $limit) {
            $results = self::removeByUserProper($limit - $changed, $user, $groupId);
            $changed += $results['changed'];
        }
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }

    /**
     * Removes Forum objects created by the specified user.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $user string  Username of the person whose content to remove.
     * @param $groupId string  Group ID to filter on, or null to include all groups and the main Forum
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    private static function removeByUserProper($limit, $user, $groupId = null) {
        $changed = 0;
        // Even if Comments have already been deleted by the top-level bulk-deletion logic,
        // we must still delete the UploadedFiles that were attached to them [Jon Aquino 2007-01-29]
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('my->mozzle', '=', W_Cache::current('W_Widget')->dir);
        $query->filter('contributorName', '=', $user);
        if (defined('UNIT_TESTING')) { $query->filter('my->test', '=', 'Y'); }
        if ($groupId) { $query->filter('my->groupId', '=', $groupId); }
        $query->begin(0);
        $query->end($limit - $changed);
        $idsOfTopicsToUpdate = array();
        foreach ($query->execute() as $object) {
            if ($object->type == 'Comment' && $object->my->attachedToAuthor != $user) {
                $idsOfTopicsToUpdate[$object->my->attachedTo] = $object->my->attachedTo;
                Comment::remove($object, false);
            } else {
                XN_Content::delete($object);
            }
            $changed++;
        }
        try {
            $topicsToUpdate = XG_Cache::content($idsOfTopicsToUpdate);
        } catch(Exception $e) {
            // Should no longer get here, but just in case, we don't want to prevent a user from being banned (BAZ-3239) [Jon Aquino 2007-06-08]
            $topicsToUpdate = array();
        }
        foreach ($topicsToUpdate as $topic) {
            W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
            // Make sure we save the Topic, as its comment count has changed [Jon Aquino 2007-05-11]
            if (! Forum_CommentHelper::updateLastEntry($topic)) { $topic->save(); }
        }
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }

    /**
     * Removes a Comment and its SubComments, and UploadedFiles attached to them.
     *
     * @param $comment XN_Content|W_Content  The Comment to delete
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @return array  [0] The number of content objects deleted (approximate), and [1] 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public static function removeCommentAndSubComments($comment, $limit) {
        $topic = XG_Cache::content($comment->my->attachedTo);
        $changed = 0;
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'Comment');
        $query->filter('my->mozzle', '=', W_Cache::current('W_Widget')->dir);
        $query->filter('my->attachedTo', '=', (string) $topic->id);
        $widget = W_Cache::current('W_Widget');
        // Ascending, so the parent comment is deleted last [Jon Aquino 2007-02-27]
        $query->order('my->' . XG_App::widgetAttributeName($widget, 'commentTimestamps'), 'asc');
        $query->filter('my->' . XG_App::widgetAttributeName($widget, 'commentTimestamps'), '<=',
                $comment->my->raw(XG_App::widgetAttributeName($widget, 'commentTimestamps')));
        $query->filter('my->' . XG_App::widgetAttributeName($widget, 'commentTimestamps'), '>=',
                str_replace(' X', '', $comment->my->raw(XG_App::widgetAttributeName($widget, 'commentTimestamps'))));
        $query->begin(0);
        $query->end($limit - $changed);
        $contributorNames = array();
        foreach ($query->execute() as $c) {
            $contributorNames[] = $c->contributorName;
            Comment::remove($c, false);
            Forum_FileHelper::deleteAttachments($c);
            $changed++;
        }
        W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        // Make sure we save the Topic, as its comment count has changed [Jon Aquino 2007-05-11]
        if (! Forum_CommentHelper::updateLastEntry($topic)) { $topic->save(); }
        foreach ($contributorNames as $contributorName) {
            Forum_UserHelper::updateActivityCount(User::load($contributorName));
        }
        if ($changed < $limit) {
            TopicCommenterLink::deleteLinkIfNecessary($topic->id);
        }
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }

}
