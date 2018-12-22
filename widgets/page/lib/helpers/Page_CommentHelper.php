<?php

/**
 * Useful functions for working with comments on discussion pages.
 */
class Page_CommentHelper {

    /** Number of comments per page when viewing a discussion page. */
    const COMMENTS_PER_PAGE = 20;

    /**
     * Create a query for Comment objects. alwaysReturnTotalCount will be set to TRUE.
     * Comments will be sorted in order of appearance in the tree.
     *
     * @param $pageId string  The ID of the page object
     * @param $begin integer  The zero-based position of the first Comment for the query to retrieve
     * @param $end integer  The zero-based position + 1 of the last Comment for the query to retrieve
     * @param $sort string  "threaded" (ordered for display in a tree structure) or "mostRecent"
     * @return XN_Query  The new query object
     */
    public function createQuery($pageId, $begin, $end, $sort = 'threaded') {
        // TODO: Use XG_CommentHelper::createQuery, if it is equivalent [Jon Aquino 2008-02-23]
        if (! ($sort == 'threaded' || $sort == 'mostRecent')) { throw new Exception('Assertion failed'); }
        $query = XN_Query::create('Content');
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'Comment'));
        }
        $query->filter('type', '=', 'Comment');
        $query->filter('owner');
        $query->filter('my->attachedTo', '=', (string) $pageId);
        $query->order('createdDate', 'asc', XN_Attribute::DATE);
        $query->begin($begin);
        $query->end($end);
        $query->alwaysReturnTotalCount(true);
        return $query;
    }

    /**
     * Creates a new Comment object attached to the given page.
     * Use HTML_Scrubber::scrub($commentText) to clean the comment text
     * before passing it in.
     *
     * @param $page XN_Content|W_Content  The page the Comment is attached to
     * @param $description string  The scrubbed text of the Comment
     * @param W_Content|XN_Content  The Comment's parent Comment, if any; otherwise, NULL
     * @return W_Content  The new, saved Comment
     */
    public static function createComment($page, $description) {
        $comment = Comment::createAndAttachTo($page, $description);
        $comment->save();
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
     * @param W_Content|XN_Content  The Comment's parent Comment, if any; otherwise, NULL
     * @param W_Content|XN_Content  The Comment
     * @return array  The parent's timestamps plus the Comment's timestamp
     */
    public static function commentTimestamps($parentComment, $comment) {
        $widget = W_Cache::current('W_Widget');
        $commentTimestamps = $parentComment ? self::getCommentTimestamps($parentComment) : array();
        $commentTimestamps[] = strtotime($comment->createdDate);
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
        if ($xCount != 1) { throw new Exception('Assertion failed'); }
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
    public static function setCommentTimestamps($comment, $commentTimestamps) {
        $widget = W_Cache::current('W_Widget');
        // Add "X" to achieve the following descending sort order: 111 X, 111 333 X, 111 222 X  [Jon Aquino 2007-01-24]
        $comment->my->set(XG_App::widgetAttributeName($widget, 'commentTimestamps'), implode(' ', $commentTimestamps) . ' X');
        $comment->save();
    }

    /**
     * Scrubs, linkifies, and truncates the given comment text.
     *
     * @param $description string  The comment text
     * @return string  The cleaned up comment text
     */
    public static function cleanDescription($description) {
        return mb_substr(xg_linkify(Page_HtmlHelper::scrub($description ? $description : xg_text('NO_DESCRIPTION'))), 0, Comment::MAX_COMMENT_LENGTH);
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
        // TODO: Use XG_CommentHelper::page, if it is equivalent [Jon Aquino 2008-02-23]
        $query = self::createQuery($comment->my->attachedTo, 0, 1);
        $query->filter('createdDate', '>=', $comment->createdDate, XN_Attribute::DATE);
        $query->execute();
        return ceil($query->getTotalCount() / $commentsPerPage);
    }

    /**
     * Returns the URL of the detail page showing the comment
     *
     * @param $comment XN_Content|W_Content  The comment
     * @return string  The URL of the Page detail page showing the comment
     */
    public static function url($comment) {
        return W_Cache::current('W_Widget')->buildUrl('page', 'show', '?id=' . urlencode($comment->my->attachedTo) . '&page=' . Page_CommentHelper::page($comment) . '&commentId=' . urlencode($comment->id) . '#' . urlencode($comment->id));
    }


}
