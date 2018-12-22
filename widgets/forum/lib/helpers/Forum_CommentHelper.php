<?php

/**
 * Useful functions for working with comments on discussion topics.
 */
class Forum_CommentHelper {

    /** Number of comments per page when viewing a discussion topic. */
    const COMMENTS_PER_PAGE = 12;

    /** Max number of commenting levels. */
    const MAX_COMMENT_LEVEL = 9;

    /**
     * Returns whether posts on the Topic detail page are shown with the newest posts first.
     *
     * @return boolean  Whether the posts are ordered newest first or oldest first
     */
    public static function newestPostsFirst() {
        if (self::$testForceNewestPostsFirst) { return TRUE; }
        return W_Cache::current('W_Widget')->config['order'] != 'ascending';
    }

    /** Whether to force newestPostsFirst() to return TRUE for testing */
    public static $testForceNewestPostsFirst = FALSE;

    /**
     * Create a query for Comment objects. alwaysReturnTotalCount will be set to TRUE.
     * Comments will be sorted in order of appearance in the tree.
     *
     * @param $topicId string  The ID of the Topic object
     * @param $begin integer  The zero-based position of the first Comment for the query to retrieve
     * @param $end integer  The zero-based position + 1 of the last Comment for the query to retrieve
     * @param $sort string  "threaded" (ordered for display in a tree structure), "mostRecent" (RSS feed), or "flat" (oldest first or newest first
     *                                  based on config setting)
     * @return XN_Query  The new query object
     */
    public static function createQuery($topicId, $begin, $end, $sort = 'threaded') {
        if (! ($sort == 'threaded' || $sort == 'flat' || $sort == 'mostRecent')) { throw new Exception('Assertion failed'); }
        $query = XN_Query::create('Content');
        $query->filter('type', '=', 'Comment');
        $query->filter('owner');
        $query->filter('my->attachedTo', '=', (string) $topicId);
        $widget = W_Cache::current('W_Widget');
        if ($sort == 'threaded' && self::newestPostsFirst()) {
            $query->order('my->' . XG_App::widgetAttributeName($widget, 'commentTimestamps'), 'desc');
        } elseif ($sort == 'threaded') {
            $query->order('my->' . XG_App::widgetAttributeName($widget, 'commentTimestampsForAscSort'), 'asc');
            // Also see Forum_CommentHelper::page() [Jon Aquino 2007-02-23]
        } elseif ($sort == 'flat' && self::newestPostsFirst() || $sort == 'mostRecent') {
            $query->order('createdDate', 'desc', XN_Attribute::DATE);
        } else {
            // 'flat' && !self::newestPostsFirst()
            $query->order('createdDate', 'asc', XN_Attribute::DATE);
        }
        $query->filter('my.' . XG_App::widgetAttributeName($widget, 'deleted'), '=', null);
        $query->begin($begin);
        $query->end($end);
        $query->alwaysReturnTotalCount(true);
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'Comment'));
        }
        return $query;
    }

    /**
     * Creates a new Comment object attached to the given Topic.
     * Use HTML_Scrubber::scrub($commentText) to clean the comment text
     * before passing it in.
     *
     * @param $topic XN_Content|W_Content  The Topic the Comment is attached to
     * @param $description string  The scrubbed text of the Comment
     * @param W_Content|XN_Content  The Comment's parent Comment, if any; otherwise, NULL
     * @param $save bool Whether to save the comment
     * @return W_Content  The new Comment, which needs to be saved
     */
    public static function createComment($topic, $description, $parentComment, $save = true) {
        if ($parentComment && self::getAncestorCommentCount($parentComment) + 2 > self::MAX_COMMENT_LEVEL) { throw new Exception('Max comment level exceeded'); }
        $comment = Comment::createAndAttachTo($topic, $description);
        $commentCount = $topic->my->xg_forum_commentCount;
        self::setCommentTimestamps($comment, self::commentTimestamps($parentComment, $comment, true), false);
        if ($save) {
            // if $save is set to false, the $topic values relating to $comment-> values are set to null.
            $comment->save();
        }
        $topic->my->lastCommentContributorName = $comment->contributorName;
        $topic->my->lastCommentCreatedDate = $comment->createdDate;
        $topic->my->set('lastEntryDate', $comment->createdDate, XN_Attribute::DATE);
        // Workaround for NING-5370 [Jon Aquino 2007-04-04]
        $topic->my->xg_forum_commentCount = $commentCount;
        $topic->save();
        TopicCommenterLink::createLinkIfNecessary($topic->id, $topic->my->test == 'Y');
        return $comment;
    }

    /**
     * Returns the number of ancestor Comments for the given Comment.
     *
     * @param $comment W_Content|XN_Content  The comment
     * @return integer  The number of ancestor Comments: parent, grandparent, etc.
     */
    public static function getAncestorCommentCount($comment) {
        return count(self::getCommentTimestamps($comment)) - 1;
    }

    /**
     * Returns the epoch timestamps of the Comments in the path from the top-level Comment
     * to the given Comment.
     *
     * @param $parentComment W_Content|XN_Content  The Comment's parent Comment, if any; otherwise, NULL
     * @param $comment W_Content|XN_Content  The Comment
     * @param $useNow bool	Use "now" instead of $comment->createDDate
     * @return array  The parent's timestamps plus the Comment's timestamp
     */
    public static function commentTimestamps($parentComment, $comment, $useNow = false) {
        $widget = W_Cache::current('W_Widget');
        $commentTimestamps = $parentComment ? self::getCommentTimestamps($parentComment) : array();
        // Note that $useNow does not guarantee that the last commentTimestamp matches the comment's createdDate.
        // This causes occasional failures, caught by Forum_CommentHelper5Test. [Jon Aquino 2008-10-07]
        $commentTimestamps[] = $useNow ? time() : strtotime($comment->createdDate);
        return $commentTimestamps;
    }

    /**
     * Returns the timestamps of the path of Comments leading to the given Comment.
     *
     * @param W_Content|XN_Content  The Comment
     * @return array  Epoch timestamps of the Comments in the path, beginning with
     *         that of the top-level Comment and ending with that of the Comment itself
     */
    public static function getCommentTimestamps($comment) {
        $widget = W_Cache::current('W_Widget');
        $commentTimestamps = str_replace(' X', '', $comment->my->raw(XG_App::widgetAttributeName($widget, 'commentTimestamps')), $xCount);
        if ($xCount != 1) {
            // Shouldn't get here, but sometimes we do for some reason. Recover. [Jon Aquino 2007-05-09]
            self::setCommentTimestamps($comment, self::commentTimestamps(null, $comment));
        }
        return explode(' ', $commentTimestamps);
    }

    /**
     * Sets the timestamps of the path of Comments leading to the given Comment.
     * Saves the Comment.
     *
     * @param W_Content|XN_Content  The Comment to update
     * @param $commentTimestamps array  Epoch timestamps of the Comments in the path, beginning with
     *         that of the top-level Comment and ending with that of the Comment itself
     */
    public static function setCommentTimestamps($comment, $commentTimestamps, $save = true) {
        $widget = W_Cache::current('W_Widget');
        // Add "X" to achieve the following descending sort order: 111 X, 111 333 X, 111 222 X  [Jon Aquino 2007-01-24]
        $comment->my->set(XG_App::widgetAttributeName($widget, 'commentTimestamps'), implode(' ', $commentTimestamps) . ' X');
        $comment->my->set(XG_App::widgetAttributeName($widget, 'commentTimestampsForAscSort'), implode(' ', $commentTimestamps));
        if ($save) {
            $comment->save();
        }
    }

    /**
     * Scrubs, linkifies, and truncates the given comment text.
     *
     * @param $description string  The comment text
     * @return string  The cleaned up comment text
     */
    public static function cleanDescription($description) {
        return mb_substr(xg_linkify(Forum_HtmlHelper::scrub($description ? $description : xg_text('NO_DESCRIPTION'))), 0, Comment::MAX_COMMENT_LENGTH);
    }

    /**
     * returns the number of minutes a user has left to edit their comment.
     *
     * @param $comment integer  The comment object
     * @return integer  The time remaining in minutes
     */
    public static function getEditMinutes($comment) {
        $diff = (time() - strtotime($comment->createdDate)) / 60;
        return 15 - floor($diff);
    }

    /**
     * Returns the page number that the given comment is on.
     *
     * @param $comment XN_Content|W_Content  The Comment object
     * @param $commentsPerPage integer  The number of comments per page
     * @return integer  1 for the first page, 2 for the second, etc.
     */
    public static function page($comment, $commentsPerPage = self::COMMENTS_PER_PAGE) {
        $widget = W_Cache::current('W_Widget');
        $query = self::createQuery($comment->my->attachedTo, 0, 1);
        if (self::newestPostsFirst()) {
            $attributeName = XG_App::widgetAttributeName($widget, 'commentTimestamps');
            $query->filter('my->' . $attributeName, '>=', $comment->my->$attributeName);
        } else {
            $attributeName = XG_App::widgetAttributeName($widget, 'commentTimestampsForAscSort');
            $query->filter('my->' . $attributeName, '<=', $comment->my->$attributeName);
        }
        $query->execute();
        return ceil($query->getTotalCount() / $commentsPerPage);
    }

    /**
     * Returns the position where the JavaScript should insert the new comment.
     *
     * @param $hasParent boolean  Whether the new comment has a parent comment.
     * @param $firstPage boolean  Whether we are on the first page
     * @param $lastPage boolean  Whether we are on the last page
     * @param $newestPostsFirst boolean  Whether the posts are ordered newest first or oldest first
     * @return string  topOfPage, bottomOfPage, firstChild, lastChild, refreshPage
     */
    public static function positionOfNewComment($hasParent, $firstPage, $lastPage, $newestPostsFirst) {
        if ($newestPostsFirst && $hasParent) { return 'firstChild'; }
        if (! $newestPostsFirst && $hasParent) { return 'lastChild'; }
        if ($newestPostsFirst && $firstPage) { return 'topOfPage'; }
        if (! $newestPostsFirst && $lastPage) { return 'bottomOfPage'; }
        return 'refreshPage';
    }

    /**
     * Returns the short URL of the detail page showing the comment
     *
     * @param $comment XN_Content|W_Content  The comment
     * @return string  The URL of the Topic detail page showing the comment
     */
    public static function url($comment) {
        // Remove ":" from fragment, for IE [Jon Aquino 2007-03-28]
        return xg_absolute_url('/xn/detail/' . $comment->id);
    }

    /**
     * Returns the URL of the actual detail page (not the url rewrite shortcut) showing the comment
     *
     * @param $comment XN_Content|W_Content  The comment
     * @return string  The URL of the Topic detail page showing the comment
     */
    public static function urlProper($comment) {
        // Remove ":" from fragment, for IE [Jon Aquino 2007-03-28]
        // Append meaningless &x=1 to querystring to prevent IE6 thinking the anchor is part of the value of commentId.  See BAZ-4883 and BAZ-5059.
        return XG_GroupHelper::buildUrl( W_Cache::getWidget($comment->my->mozzle ? $comment->my->mozzle: 'forum')->dir, 'topic', 'show',
                array('id' => $comment->my->attachedTo, 'page' => Forum_CommentHelper::page($comment), 'commentId' => $comment->id)) . '&x=1#' . urlencode(str_replace(':', '', $comment->id));
    }


    /**
     * If the comment has child comments, clears its description and sets its "deleted"
     * widget-attribute to "Y". Otherwise, deletes the comment and, if its parent is
     * marked as deleted, repeats for the parent.
     *
     * @param $comment XN_Content|W_Content  The comment to delete
     * @return boolean  Whether the Comment object was actually deleted
     */
    public static function delete($comment) {
        // Query comments outside of deleteProper, which is recursive [Jon Aquino 2007-03-24]
        $topic = XN_Content::load($comment->my->attachedTo);
        if ($topic->my->lastEntryDate == $comment->createdDate) {
            self::updateLastEntry($topic, true);
            // Update topic before deleting comment, otherwise the following problem occurs:
            //     - comment is deleted
            //     - Category::beforeContentDeleted is called
            //     - Category::invalidateRecentTopicsCacheIfNecessary is called, clearing the category's topic cache
            //     - another user hits the homepage, rebuilding the category's topic cache
            //     - topic is updated - too late, as the category's topic cache has already been rebuilt.
            // [Jon Aquino 2007-03-29]
        }
        return self::deleteProper($comment);
    }

    /**
     * Updates the last-entry values cached in the Topic, then saves it if necessary
     *
     * @param $topic XN_Content|W_Content  the topic object
     * @param $penultimateComment boolean  whether to use the second-to-last comment instead of the last comment
     * @return boolean  whether the Topic was saved
     */
    public static function updateLastEntry($topic, $penultimateComment = false) {
        $mostRecentComments = self::createQuery($topic->id, 0, 2, 'mostRecent')->execute();
        $comment = $mostRecentComments[$penultimateComment ? 1 : 0];
        $lastEntryDate = $comment ? $comment->createdDate : $topic->createdDate;
        if ($topic->my->lastEntryDate != $lastEntryDate) {
            $topic->my->lastCommentContributorName = $comment ? $comment->contributorName : null;
            $topic->my->lastCommentCreatedDate = $comment ? $comment->createdDate : null;
            $topic->my->set('lastEntryDate', $lastEntryDate, XN_Attribute::DATE);
            $topic->save();
            return true;
        }
        return false;
    }

    /**
     * If the comment has child comments, clears its description and sets its "deleted"
     * widget-attribute to "Y". Otherwise, deletes the comment and, if its parent is
     * marked as deleted, repeats for the parent.
     *
     * @param $comment XN_Content|W_Content  The comment to delete
     * @return boolean  Whether the Comment object was actually deleted
     */
    private static function deleteProper($comment) {
        if (self::hasChildComments($comment)) {
            $comment->description = ' ';
            $comment->my->set(XG_App::widgetAttributeName(W_Cache::current('W_Widget'), 'deleted'), 'Y');
            $comment->save();
            return false;
        }
        $parentComment = self::parentComment($comment);
        Comment::remove($comment);
        if ($parentComment && self::isMarkedAsDeleted($parentComment)) { self::deleteProper($parentComment); }
        return true;
    }

    /**
     * Returns whether the given Comment is marked as being deleted.
     * It still exists because it has child Comments - it is a placeholder for the
     * "This comment has been deleted" message.
     *
     * @param $comment  XN_Content|W_Content  The Comment
     * @return boolean  Whether the comment should be considered deleted.
     */
    public static function isMarkedAsDeleted($comment) {
        $currentWidget = W_Cache::current('W_Widget');
        if (! $currentWidget) { return false; } // Get here during unit testing [Jon Aquino 2007-08-28]
        return $comment->my->raw(XG_App::widgetAttributeName($currentWidget, 'deleted')) == 'Y';
    }

    /**
     * Returns whether the Comment has sub-comments.
     *
     * @param $comment XN_Content|W_Content  The comment
     * @return boolean  Whether the comment has descendants
     */
    private static function hasChildComments($comment) {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'Comment');
        XG_GroupHelper::addGroupFilter($query);
        $query->filter('my->mozzle', '=', W_Cache::current('W_Widget')->dir);
        $query->filter('my->attachedTo', '=', (string) $comment->my->attachedTo);
        $query->filter('id', '<>', (string) $comment->id);
        $widget = W_Cache::current('W_Widget');
        $query->filter('my->' . XG_App::widgetAttributeName($widget, 'commentTimestamps'), '<=',
                $comment->my->raw(XG_App::widgetAttributeName($widget, 'commentTimestamps')));
        $query->filter('my->' . XG_App::widgetAttributeName($widget, 'commentTimestamps'), '>=',
                str_replace(' X', '', $comment->my->raw(XG_App::widgetAttributeName($widget, 'commentTimestamps'))));
        $query->end(1);
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'Comment'));
        }

        return count($query->execute());
    }

    /**
     * Returns the parent Comment of the given Comment, or null if it has no parent.
     *
     * @param $comment XN_Content|W_Content  The comment
     * @param XN_Content  The parent comment, or null
     */
    private static function parentComment($comment) {
        if (self::getAncestorCommentCount($comment) == 0) { return null; }
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'Comment');
        XG_GroupHelper::addGroupFilter($query);
        $query->filter('my->mozzle', '=', W_Cache::current('W_Widget')->dir);
        $query->filter('my->attachedTo', '=', (string) $comment->my->attachedTo);
        $widget = W_Cache::current('W_Widget');
        $query->filter('my->' . XG_App::widgetAttributeName($widget, 'commentTimestamps'), '=',
                implode(' ', array_slice(self::getCommentTimestamps($comment), 0, -1)) . ' X');
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'Comment'));
        }
        return $query->uniqueResult();
    }

    /**
     * Identifies which comments have child comments
     *
     * @param $commentsPerPage integer  The number of comments per page
     * @param $commentsPlusOne array  The comments on a page, plus the next comment (if any)
     * @return array  ID => ID
     */
    public static function commentIdsWithChildComments($commentsPerPage, $commentsPlusOne) {
        $commentIdsWithChildComments = array();
        $n = min($commentsPerPage, count($commentsPlusOne));
        for ($i = 0; $i < $n; $i++) {
            if ($commentsPlusOne[$i+1] && in_array(end(self::getCommentTimestamps($commentsPlusOne[$i])), self::getCommentTimestamps($commentsPlusOne[$i+1]))) {
                $commentIdsWithChildComments[$commentsPlusOne[$i]->id] = $commentsPlusOne[$i]->id;
            }
        }
        return $commentIdsWithChildComments;
    }

    /**
     * Extracts the ids from the given Comment objects
     *
     * @param $comments array  The Comments
     * @return array  The ids
     */
    public static function ids($comments) {
        $ids = array();
        foreach ($comments as $comment) {
            $ids[] = $comment->id;
        }
        return $ids;
    }

    /**
     * Returns the maximum widths for embeds in a comment at the given
     * level of indentation.
     *
     * @param $indentLevel integer  the number of ancestor comments, from 0 to 8
     */
    public static function maxEmbedWidth($indentLevel) {
        return max(0, 646 - 30 * $indentLevel);
    }

}