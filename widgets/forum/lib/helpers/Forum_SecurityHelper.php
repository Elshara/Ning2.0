<?php

/**
 * Useful functions for authorizing access to pages and other resources.
 */
class Forum_SecurityHelper {

    /**
     * Returns whether the current user is allowed to delete the attachment.
     *
     * @param $attachedTo  XN_Content|W_Content  The object to which the attachment is attached
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeleteAttachments($attachedTo) {
        if (XG_GroupHelper::isBannedFromGroup()) { return false; }
        if (XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin()) { return true; }
        return XN_Profile::current()->screenName == $attachedTo->contributorName;
    }

    /**
     * Returns whether the current user is allowed to subscribe to new topics
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanFollowNewTopics() {
        // Code is similar to that in XG_SecurityHelper::currentUserCanFollowComments() [Jon Aquino 2007-08-23]
        if (XG_SecurityHelper::userIsAdmin()) { return true; }
        if (! User::isMember(XN_Profile::current())) { return false; }
        if (XG_GroupHelper::inGroupContext() && ! XG_GroupHelper::userIsMember()) { return false; }
        return true;
    }

    /**
     * Returns whether the current user is allowed to access and use forum management functions.
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanManageForum() {
        if (XG_GroupHelper::inGroupContext()) {
            return false;
        }
        return XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns whether the current user is allowed to add a comment to a topic in the given category.
     *
     * @param $topic  XN_Content|W_Content  The Topic object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSeeAddCommentLinks($topic) {
        if ($topic->type != 'Topic') { throw new Exception('Assertion failed (1309410476)'); }
        if ($topic->my->commentsClosed == 'Y') { return false; }
        if (XG_GroupHelper::isBannedFromGroup()) { return false; }
        if (XG_GroupHelper::groupIsPrivate() && ! XG_GroupHelper::userIsMember()) { return false; } // Network admins [Jon Aquino 07-05-11]
        $category = Category::findById($topic->my->categoryId);
        if (! $category) { return true; }
        if ($category->type != 'Category') { throw new Exception('Not a Category:' . $category->type); }
        return $category->my->membersCanReply == 'Y';
    }

    /**
     * Returns whether the current user is allowed to add a comment to a topic in the given category.
     *
     * @param $topic  XN_Content|W_Content  The Topic object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanAddComment($topic) {
        return XN_Profile::current()->isLoggedIn() && self::currentUserCanSeeAddCommentLinks($topic);
    }

    /**
     * Returns whether "Start a New Discussion" links and buttons should be visible to the current user,
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSeeAddTopicLinks() {
        if (XG_GroupHelper::isBannedFromGroup()) { return false; }
        if (XG_GroupHelper::groupIsPrivate() && ! XG_GroupHelper::userIsMember()) { return false; } // Network admins [Jon Aquino 07-05-11]
        if (XG_SecurityHelper::userIsAdmin()) { return true; }
        if (XG_GroupHelper::inGroupContext()) {return true; }
        if (!Category::usingCategories()) { return true; }  // No categories at all [Jon Aquino 2007-05-03]
        return count(Category::findAll(false)) > 0;  // Check whether categories are all owner-only [Jon Aquino 2007-05-03]
    }

    /**
     * Returns whether the current user is allowed to add a discussion
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanAddTopic() {
        return XN_Profile::current()->isLoggedIn() && self::currentUserCanSeeAddTopicLinks();
    }

    /**
     * Returns whether to display Add Topic links for the specified category
     *
     * @param $category  XN_Content|W_Content  The Category object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSeeAddTopicLinksForCategory($category) {
        if (XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin()) { return true; }
        if (! self::currentUserCanSeeAddTopicLinks()) { return false; }
        return $category->my->membersCanAddTopics == 'Y';
    }

    /**
     * Returns whether the current user is allowed to add a discussion to the specified category
     *
     * @param $category  XN_Content|W_Content  The Category object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanAddTopicToCategory($category) {
        if (XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin()) { return true; }
        if (! self::currentUserCanAddTopic()) { return false; }
        return $category->my->membersCanAddTopics == 'Y';
    }

    /**
     * Returns whether the current user is allowed to add tags
     *
     * @param $topic  XN_Content|W_Content  The Topic object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanTag($topic) {
        if (XG_GroupHelper::isBannedFromGroup()) { return false; }
        if (XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin()) { return true; }
        if (XN_Profile::current()->screenName == $topic->contributorName) { return true; }
        return false;
    }

    /**
     * Returns whether the current user is allowed to delete the comment.
     *
     * @param $comment  XN_Content|W_Content  The Comment object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeleteComment($comment) {
        if (XG_GroupHelper::isBannedFromGroup()) { return false; }
        if (XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin()) { return TRUE; }
        if (XN_Profile::current()->screenName == $comment->contributorName) { return TRUE; }
        return FALSE;
    }

    /**
     * Returns whether the current user is allowed to delete the comment and its child comments.
     *
     * @param $comment  XN_Content|W_Content  The Comment object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeleteCommentAndSubComments($comment) {
        return XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin();
    }

    /**
     * Returns whether the current user is allowed to edit the comment.
     *
     * @param $comment  XN_Content|W_Content  The Comment object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanEditComment($comment) {
        // Allow editing comment even if discussion is closed (BAZ-4251) [Jon Aquino 2007-09-05]
        if (XG_GroupHelper::isBannedFromGroup()) { return false; }
        if (XG_GroupHelper::groupIsPrivate() && ! XG_GroupHelper::userIsMember()) { return false; } // Network admins [Jon Aquino 07-05-11]
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        if ((XN_Profile::current()->screenName == $comment->contributorName) && Forum_CommentHelper::getEditMinutes($comment) > 0) { return TRUE; }
        return false;
    }

    /**
     * Returns whether the current user is allowed to delete the topic.
     *
     * @param $topic  XN_Content|W_Content  The Topic object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeleteTopic($topic) {
        if (XG_GroupHelper::isBannedFromGroup()) { return false; }
        if (XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin()) { return true; }
        if (XN_Profile::current()->screenName == $topic->contributorName) { return true; }
        return false;
    }

    /**
     * Returns whether the current user is allowed to edit the topic.
     *
     * @param $topic  XN_Content|W_Content  The Topic object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanEditTopic($topic) {
        if (XG_GroupHelper::isBannedFromGroup()) { return false; }
        if (XG_GroupHelper::groupIsPrivate() && ! XG_GroupHelper::userIsMember()) { return false; } // Network admins [Jon Aquino 07-05-11]
        if (XN_Profile::current()->screenName == $topic->contributorName) { return true; }
        return false;
    }

    /**
     * Returns whether the current user is allowed to end commenting on a discussion thread.
     *
     * @param $topic  XN_Content|W_Content  The Topic object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanCloseComments($topic) {
        if ($topic->my->commentsClosed == 'Y') { return false; }
        $category = Category::findById($topic->my->categoryId);
        if ($category && $category->my->membersCanReply == 'N') { return false; }
        if (XN_Profile::current()->screenName == $topic->contributorName) { return true; }
        if (XG_GroupHelper::inGroupContext()) { return XG_GroupHelper::isGroupAdmin(); }
        return XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns whether the current user is allowed to re-open commenting on a discussion thread.
     *
     * @param $topic  XN_Content|W_Content  The Topic object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanOpenComments($topic) {
        if ($topic->my->commentsClosed != 'Y') { return false; }
        $category = Category::findById($topic->my->categoryId);
        if ($category && $category->my->membersCanReply == 'N') { return false; }
        if (XN_Profile::current()->screenName == $topic->contributorName) { return true; }
        if (XG_GroupHelper::inGroupContext()) { return XG_GroupHelper::isGroupAdmin(); }
        return XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns whether the current user is allowed to change a topic's category.
     *
     * @param $topic  XN_Content|W_Content  The Topic object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSetCategory($topic) {
        if (XG_GroupHelper::isBannedFromGroup()) { return false; }
        if (XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin()) { return true; }
        if (XN_Profile::current()->screenName == $topic->contributorName) { return true; }
        return false;
    }

    /**
     * Returns whether Share This links and buttons should be visible to the current user,
     * for the specified topic. Differs from currentUserCanShare; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * topic (until they sign in).
     *
     * @param XN_Content|W_Content topic the Topic to share
     * @return whether to show Share This buttons for the topic
     * @see currentUserCanShare
     */
    public static function currentUserCanSeeShareLinks($topic) {
        if (! XG_GroupHelper::currentUserCanSeeShareLinksForGroup()) { return false; }
        // Allow signed-out people to see the Share This link [Jon Aquino 2006-12-20]
        // An invite key is included in the Share This email  [Jon Aquino 2006-10-24]
        return ! XN_Profile::current()->isLoggedIn() || XG_App::canSendInvites(XN_Profile::current());
    }

    /**
     * Returns whether the current user can in fact share the specified topic.
     * Differs from currentUserCanSeeShareLinks; for example, a
     * signed-out user can see the Share This link but cannot in fact share the
     * topic (until they sign in).
     *
     * @param XN_Content|W_Content topic the Topic to share
     * @return whether the current user is allowed to share the topic
     * @see currentUserCanSeeShareLinks
     */
    public static function currentUserCanShare($topic) {
        if (! XG_GroupHelper::currentUserCanShareGroup()) { return false; }
        return XN_Profile::current()->isLoggedIn() && self::currentUserCanSeeShareLinks($group);
    }
}