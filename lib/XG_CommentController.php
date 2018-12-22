<?php

/**
 * A Controller with support functions for comments. Subclasses are responsible
 * for checking that the user is authorized to access each action.
 *
 * Be sure to test the links in the feed. You will need to update
 * IndexController::action_detail to handle them.
 */
abstract class XG_CommentController extends XG_BrowserAwareController {
    /**
	 * 	If true, points to the first comments page in the notification email.
	 * 	Set it to true for "reverse" comment order
	 *
     *  @var boolean
     */
	protected $firstPageInNotification = false;

    /**
     * Returns the content type of the objects that comments are attached to.
     *
     * @return string  the content type, e.g., Album
     */
    protected abstract function getAttachedToType();

    /**
     * Returns the name of the type of objects that comments are attached to,
     * for use in the subject line "Your X has a new comment".
     *
     * @return string  the type to display, e.g., album
     */
    protected abstract function getContentTypeForEmailNotification();

    /**
     * Returns a description for email notifications.
     *
     * @param $attachedToTitle string  the title of the object that comments are attached to
     * @return string  the description, e.g., Jonathan Aquino added a comment to the blog post "Wish You Were Here"
     */
    protected abstract function getDescriptionForEmailNotification($attachedToTitle);

    /**
     * Returns a subcategory from XG_ActivityHelper.
     *
     * @return string  the subcategory, e.g. XG_ActivityHelper::SUBCATEGORY_ALBUM
     */
    protected abstract function getActivitySubCategory();

    /**
     * Returns whether the current user is allowed to add a comment.
     * Assumes that the current user is a member of the network.
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @return boolean  Whether permission is granted
     */
    protected abstract function canCurrentUserAddCommentTo($attachedTo);

    /**
     * Returns whether the current user is allowed to delete the comment.
     * Assumes that the current user is a member of the network.
     *
     * @param $comment  XN_Content|W_Content  The Comment object
     * @return boolean  Whether permission is granted
     */
    protected abstract function canCurrentUserDeleteComment($comment);

    /**
     * Code that is run before each action.
     */
    protected function _before() {
        XG_HttpHelper::trimGetAndPostValues();
    }

    /**
     * Processes the form for a new comment. If xn_out is "json", the JSON returned will contain:
     *     - html - HTML for the comment
     *     - approved - whether the comment is approved
     *     - userIsNowFollowing - whether submitting the comment caused the user to start following comments
     *
     * Expected GET variables:
     *     - xn_out - "json" to return JSON for an Ajax call; null to redirect to the target
     *     - target - the URL to redirect to, if xn_out is null
     *     - attachedTo - the content ID of the object to attach the comment to
     *     - pageSize - the number of comments per page
     *     - pageParamName - the name of the url parameter for the page number
     *
     * Expected POST variables:
     *     - comment - the comment text
     */
    public function action_create() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (1524171958)'); }
        if (mb_strlen($_POST['comment']) == 0) { throw new Exception('Empty comment (451643356)'); }
        $attachedTo = W_Content::load($_GET['attachedTo']);
        if ($attachedTo->type != $this->getAttachedToType()) { throw new Exception('Expected ' . $this->getAttachedToType() . ' but found ' . $attachedTo->type . ' (1233376324)'); }
        if (! $this->canCurrentUserAddCommentTo($attachedTo)) { throw new Exception('Current user is not allowed to add a comment to ' . $attachedTo->id); }
        $comment = Comment::createAndAttachTo($attachedTo, xg_scrub(xg_linkify(mb_substr($_POST['comment'], 0, 4000))));
        $comment->save();
        $attachedTo->save();
        if ($_GET['xn_out'] != 'json') { return $this->redirectTo($_GET['target']); }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        $route = XG_App::getRequestedRoute();
        ob_start();
        XG_CommentHelper::outputComment(array(
                'comment' => $comment,
                'canDelete' => true,
                'deleteEndpoint' => $this->_buildUrl($route['controllerName'], 'delete', array('xn_out' => 'json')),
                'canApprove' => false,
                'approveEndpoint' => null));
        $this->html = trim(ob_get_contents());
        ob_end_clean();
        $this->approved = true;
        if (! $this->approved) { throw new Exception('TODO: If comment is not approved, don\'t send follow notifications or log activity until the comment is approved [Jon Aquino 2008-02-21]'); }
        $user = User::load($this->_user);
        if ($user->my->autoFollowOnReplyPref != 'N') {
            Index_NotificationHelper::startFollowing($attachedTo);
            $this->userIsNowFollowing = true;
        }
        $notification = XG_Message_Notification::create(XG_Message_Notification::EVENT_FOLLOW_ACTIVITY, array(
                'viewActivity' => xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT'),
                'activity' => $this->getDescriptionForEmailNotification($attachedTo->title),
                'content' => $attachedTo,
				'url' => XG_CommentHelper::url($comment, $_GET['pageParamName'], $this->firstPageInNotification ? 0 : $_GET['pageSize']),
                'unfollowLink' => XG_HttpHelper::addParameters($_GET['target'], array('unfollow' => '1')),
                'type' => $this->getContentTypeForEmailNotification()));
        $notification->send(Index_NotificationHelper::contentNotificationAliasName($attachedTo) . '@lists');
        if ($this->shouldLogActivity($comment, $attachedTo)) {
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_COMMENT, $this->getActivitySubCategory(), $comment->contributorName, array($attachedTo, $comment));
        }
    }

    /**
     * Deletes a comment. If deletion succeeds, the JSON response will contain
     * success: true.
     *
     * Expected GET variables:
     *     - xn_out - Set this to "json"
     *
     * Expected POST variables:
     *     - id - ID of the Comment to delete
     */
    public function action_delete() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (74040886)'); }
        $comment = Comment::load($_POST['id']);
        if ($comment->my->attachedToType != $this->getAttachedToType()) { throw new Exception('Expected ' . $this->getAttachedToType() . ' but found ' . $comment->my->attachedToType . ' (653967397)'); }
        if (! $this->canCurrentUserDeleteComment($comment)) { throw new Exception('Current user is not allowed to delete ' . $comment->id); }
        Comment::remove($comment);
        $this->success = true;
    }

    /**
     * Displays an RSS feed for comments.
     *
     * Expected GET variables:
     *     - attachedTo - the content ID of the object to attach the comment to
     *     - xn_auth - set this to "no", as feeds do not need authentication
     */
    public function action_feed() {
        header('Content-Type: application/atom+xml');
        XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
        XG_FeedHelper::cacheFeed(array('id' => 'comment-feed-' . md5(XG_HttpHelper::currentUrl())));
        $attachedTo = W_Content::load($_GET['attachedTo']);
        if ($attachedTo->type != $this->getAttachedToType()) { throw new Exception('Expected ' . $this->getAttachedToType() . ' but found ' . $attachedTo->type . ' (1233376324)'); }
        if (! $this->feedAvailable($attachedTo)) { throw new Exception('No feed available for ' . $attachedTo->id); }
        $commentData = Comment::getCommentsFor($attachedTo->id, 0, 10, 'Y', 'createdDate', 'desc');
        XG_FeedHelper::outputFeed($commentData['comments'], xg_text('COMMENTS_TITLE', $attachedTo->title));
    }

    /**
     * Returns whether a feed is available for the comments on the given object.
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @return boolean  whether this controller publishes a feed
     */
    protected function feedAvailable($attachedTo) {
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        return XG_CommentHelper::feedAvailable($attachedTo);
    }

    /**
     * Returns whether the new comment should be displayed in the Latest Activity
     * box on the homepage.
     *
     * @param $comment XN_Content|W_Content  the Comment object
     * @param $attachedTo XN_Content|W_Content  the content object that the comment is attached to
     */
    protected function shouldLogActivity($comment, $attachedTo) {
        return $comment->my->approved == 'Y' && in_array($attachedTo->my->visibility, array('all', null));
    }

}