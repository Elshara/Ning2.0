<?php

class Video_CommentController extends W_Controller {
    
    public function action_overridePrivacy($action) {
        return ! XG_App::appIsPrivate() && $action == 'feed';
    }    

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Video_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_JsonHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_MessagingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_TrackingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_PrivacyHelper.php');
        Video_PrivacyHelper::checkMembership();
        Video_TrackingHelper::insertHeader();
        Video_HttpHelper::trimGetAndPostValues();
    }


    public function action_create() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! $_POST['comment']) { throw new Exception('Empty comment (1175663871)'); }
        $video = Video_ContentHelper::findByID('Video', $_GET['videoId']);
        if ($this->error = Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $video)) { return $this->render('error', 'index'); }
        $comment = Comment::createAndAttachTo($video, Video_HtmlHelper::cleanText(mb_substr($_POST['comment'], 0, 4000)));
        $comment->save();
        $video->save();
        self::logCommentCreation($comment,$video);
        $user = Video_UserHelper::load($this->_user);
        Video_UserHelper::addComment($user);
        $user->save();
        Video_MessagingHelper::commentCreated($comment, $video);
        if ($_GET['xn_out'] != 'json') { return $this->redirectTo('show', 'video', array('id' => $video->id)); }
        Video_FullNameHelper::initialize(array($video, $comment));
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        ob_start();
        XG_CommentHelper::outputComment(array(
                'comment' => $comment,
                // TODO: Put this check in Video_SecurityHelper::currentUserCanDeleteComment($comment)
                'canDelete' => $this->_user->screenName == $comment->my->attachedToAuthor || Video_SecurityHelper::passed(Video_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $comment)),
                'deleteEndpoint' => $this->_buildUrl('comment','delete', array('xn_out' => 'json')),
                'canApprove' => false,
                'approveEndpoint' => null));
        $this->html = trim(ob_get_contents());
        $this->approved = true;
        $this->userIsNowFollowing = false;
        ob_end_clean();
    }
    
    /**
     * Deletes a comment. If deletion succeeds, the JSON response will contain
     * success: true.
     *
     * Expected GET variables:
     *         - xn_out - Set this to "json"
     *
     * Expected POST variables:
     *         - id - ID of the Comment to delete
     */
    public function action_delete() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        $comment = Video_CommentHelper::load($_POST['id']);
        $video   = Video_VideoHelper::load($comment->my->attachedTo);
        if (! Video_SecurityHelper::userCanDeleteComment($this->_user, $comment)) {
            header("HTTP/1.0 403 Forbidden");
            return;
        }
        $user = Video_UserHelper::load($comment->contributorName);
        if (! is_null($user)) {
            Video_UserHelper::removeComment($user);
            $user->save();
        }
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
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        XG_FeedHelper::cacheFeed(array('id' => 'comment-feed-' . md5(XG_HttpHelper::currentUrl())));
        $attachedTo = W_Content::load($_GET['attachedTo']);
        if ($attachedTo->type != 'Video') { throw new Exception('Expected Video but found ' . $attachedTo->type . ' (702791781-1)'); }
        if (! XG_CommentHelper::feedAvailable($attachedTo)) { throw new Exception('No feed available for ' . $attachedTo->id); }
        $commentData = Comment::getCommentsFor($attachedTo->id, 0, 10, 'Y', 'createdDate', 'desc');
        XG_FeedHelper::outputFeed($commentData['comments'], xg_text('COMMENTS_TITLE', $attachedTo->title));
    }    

    private function logCommentCreation($comment,$video) {
        //create activity log item
        if ($comment->my->approved == 'Y' && $video->my->visibility == 'all' && $video->my->approved == 'Y') {
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_COMMENT, XG_ActivityHelper::SUBCATEGORY_VIDEO, $comment->contributorName, array($video,$comment));
        }
    }


}
