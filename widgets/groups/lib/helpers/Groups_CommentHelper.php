<?php

class Groups_CommentHelper {

    /** Number of comments per page when viewing a group page. */
    const COMMENTS_PER_PAGE = 10;

    /**
     * Can the specified user delete the specified comment?
     *
     * @param $user XN_Profile
     * @param $comment Comment
     * @return boolean
     */
    public static function userCanDeleteComment($user, $comment) {
        return (XG_SecurityHelper::userIsAdmin($user) || ($user->screenName == $comment->my->attachedToAuthor) || ($user->screenName == $comment->contributorName));
    }
    
    /**
     * Returns the page number that the given comment is on.
     *
     * @param $comment XN_Content|W_Content  The Comment object
     * @param $commentsPerPage integer  The number of comments per page
     * @return integer  1 for the first page, 2 for the second, etc.
     */
    public static function page($comment, $commentsPerPage = self::COMMENTS_PER_PAGE) {
        $commentInfo = Comment::getCommentsFor($comment->my->groupId, 0, 1, null, 'createdDate', 'desc', array('createdDate' => array('>', $comment->createdDate)), true);
        return ceil($commentInfo['numComments'] / $commentsPerPage);
    }


}
