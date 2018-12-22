<?php

class Profiles_CommentHelper {

    /** Number of comments per page when viewing a blog post. */
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
     * Can the specified user approve the specified comment?
     *
     * @param $user XN_Profile
     * @param $comment Comment
     * @return boolean
     */
    public static function userCanApproveComment($user, $comment) {
        return (XG_SecurityHelper::userIsAdmin($user) || ($user->screenName == $comment->my->attachedToAuthor));
    }

    /**
     * Can the specified user delete the specified chatter?
     *
     * @param $user XN_Profile
     * @param $chatter Comment
     * @return boolean
     */
    public static function userCanDeleteChatter($user, $chatter) {
        return (XG_SecurityHelper::userIsAdmin($user) || ($user->screenName == $chatter->my->attachedToAuthor) || ($user->screenName == $chatter->contributorName));
    }

    /**
     * Can the specified user approve the specified chatter?
     *
     * @param $user XN_Profile
     * @param $chatter Comment
     * @return boolean
     */
    public static function userCanApproveChatter($user, $chatter) {
        return ($user->screenName == $chatter->my->attachedToAuthor);
    }

    /**
     * Returns the page number that the given comment is on.
     *
     * @param $comment XN_Content|W_Content  The Comment object
     * @param $commentsPerPage integer  The number of comments per page
     * @return integer  1 for the first page, 2 for the second, etc.
     */
    public static function page($comment, $commentsPerPage = self::COMMENTS_PER_PAGE) {
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        return XG_CommentHelper::page($comment, $commentsPerPage);
    }

    /**
     * Returns the URL of the detail page showing the comment
     *
     * @param $comment XN_Content|W_Content  The comment
     * @return string  The URL
     */
    public static function url($comment) {
        // If it's a comment on a blog post, redirect to the right anchor
        // on the blog post detail page
        if ($comment->my->attachedToType == 'BlogPost') {
            $page = self::page($comment);
            return W_Cache::getWidget('profiles')->buildUrl('blog','show',"?id={$comment->my->attachedTo}&page={$page}#comment-{$comment->id}");
        }
        elseif ($comment->my->attachedToType == 'User') {
            return W_Cache::current('W_Widget')->buildUrl('comment','show',"?attachedTo={$comment->my->attachedToAuthor}&attachedToType={$comment->my->attachedToType}&commentid=chatter-{$comment->id}");
        }
        return "http://{$_SERVER['HTTP_HOST']}/";
    }

    /**
     * Returns whether the current user is allowed to leave a comment on the given BlogPost.
     *
     * @param $blogPost XN_Content|W_Content  the BlogPost object
     * @param $isFriend boolean  whether the current user is a
     * @return boolean  whether permission is granted
     */
    public static function canCurrentUserSeeAddCommentSection($blogPost, $isFriend) {
        if ($blogPost->type != 'BlogPost') { throw new Exception('Assertion failed (253907783)'); }
        if (! XN_Profile::current()->isLoggedIn()) {
            $relationship = null;
        } elseif (XN_Profile::current()->screenName == $blogPost->contributorName) {
            $relationship = 'me';
        } elseif ($isFriend) {
            $relationship = 'friend';
        } else {
            $relationship = 'member';
        }
        return self::canCurrentUserSeeAddCommentSectionProper($blogPost, User::load($blogPost->contributorName), $relationship);
    }

    /**
     * Returns whether the current user is allowed to leave a comment on the given BlogPost.
     *
     * @param $blogPost XN_Content|W_Content  the BlogPost object
     * @param $blogPostOwner XN_Content|W_Content  the User object for the author of the BlogPost
     * @param $relationship string  relationship of the current user to the blog post owner:
     *         "me", "friend", "member" (of the network - possibly pending), null (not signed in)
     */
    protected static function canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, $relationship) {
        $addCommentPermission = BlogPost::getAddCommentPermission($blogPost, $blogPostOwner);
        switch ($relationship) {
            case 'me': return true;
            case 'friend': return $addCommentPermission == 'all' || $addCommentPermission == 'friends';
            case 'member': return $addCommentPermission == 'all';
            case null: return $addCommentPermission == 'all';
            default: throw new Exception('Assertion failed (1280913839)');
        }
    }

    /**
     * Sets the User's commentsToApprove to the current value.
     * Does not save the User object.
     *
     * @param $user W_Content|XN_Content  the User object to update
     */
    public static function updateCommentsToApprove($user) {
          $commentInfo = Comment::getCommentsForContentBy($user, 0, 1, 'N',
                  'createdDate', 'desc', array('my->attachedToType' => 'BlogPost'));
          $user->my->commentsToApprove = $commentInfo['numComments'];
    }

    /**
     * Sets the User's chattersToApprove to the current value.
     * Does not save the User object.
     *
     * @param $user W_Content|XN_Content  the User object to update
     */
    public static function updateChattersToApprove($user) {
          $commentInfo = Comment::getCommentsForContentBy($user, 0, 1, 'N',
                  'createdDate', 'desc', array('my->attachedToType' => 'User'));
          $user->my->chattersToApprove = $commentInfo['numComments'];
    }

}
