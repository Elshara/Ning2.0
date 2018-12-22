<?php

XG_App::includeFileOnce('/lib/XG_CommentController.php');

/**
 * Dispatches requests pertaining to event comments.
 */
class Events_CommentController extends XG_CommentController {
	protected $firstPageInNotification = true;

    /**
     * Returns the content type of the objects that comments are attached to.
     *
     * @return string  the content type, e.g., Event
     * @override
     */
    protected function getAttachedToType() { return 'Event'; }

    /**
     * Returns the name of the type of objects that comments are attached to,
     * for use in the subject line "Your X has a new comment".
     *
     * @return string  the type to display, e.g., event
     * @override
     */
    protected function getContentTypeForEmailNotification() { return xg_text('EVENT'); }

    /**
     * Returns a description for email notifications.
     *
     * @param $attachedToTitle string  the title of the object that comments are attached to
     * @return string  the description, e.g., Jonathan Aquino added a comment to the blog post "Wish You Were Here"
     * @override
     */
    protected function getDescriptionForEmailNotification($attachedToTitle) {
        return xg_text('X_ADDED_A_COMMENT_TO_THE_EVENT_Y_ON_Z', xg_username($this->_user), $attachedToTitle, XN_Application::load()->name);
    }

    /**
     * Returns a subcategory from XG_ActivityHelper. Be sure to update fragment_logItem.php
     * for this subcategory, so that new comments appear correctly in the Latest Activity box
     * on the homepage and profile page.
     *
     * @return string  the subcategory, e.g. XG_ActivityHelper::SUBCATEGORY_EVENT
     * @override
     */
     protected function getActivitySubCategory() {
         return XG_ActivityHelper::SUBCATEGORY_EVENT;
     }

    /**
     * Returns whether the current user is allowed to add a comment.
     * Assumes that the current user is a member of the network.
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @return boolean  Whether permission is granted
     * @override
     */
    protected function canCurrentUserAddCommentTo($attachedTo) {
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_SecurityHelper.php');
        return Events_SecurityHelper::currentUserCanAddComment(EventAttendee::getStatuses(XN_Profile::current()->screenName, $attachedTo), $attachedTo);
    }

    /**
     * Returns whether the current user is allowed to delete the comment.
     * Assumes that the current user is a member of the network.
     *
     * @param $comment  XN_Content|W_Content  The Comment object
     * @return boolean  Whether permission is granted
     * @override
     */
    protected function canCurrentUserDeleteComment($comment) {
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        return XG_CommentHelper::currentUserCanDeleteComment($comment);
    }

    /**
     * Returns whether a feed is available for the comments on the given object.
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @return boolean  whether this controller publishes a feed
     */
    protected function feedAvailable($attachedTo) {
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_SecurityHelper.php');
        return Events_SecurityHelper::commentFeedAvailable($attachedTo) && parent::feedAvailable($attachedTo);
    }

    /**
     * Returns whether the new comment should be displayed in the Latest Activity
     * box on the homepage.
     *
     * @param $comment XN_Content|W_Content  the Comment object
     * @param $attachedTo XN_Content|W_Content  the content object that the comment is attached to
     */
    protected function shouldLogActivity($comment, $attachedTo) {
        return $attachedTo->my->privacy == Event::ANYONE && parent::shouldLogActivity($comment, $attachedTo);
    }
}
