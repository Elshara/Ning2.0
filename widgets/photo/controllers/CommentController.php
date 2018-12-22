<?php

class Photo_CommentController extends W_Controller {
    
    public function action_overridePrivacy($action) {
        return ! XG_App::appIsPrivate() && $action == 'feed';
    }    
    
    
    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Photo_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_JsonHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_MessagingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PrivacyHelper.php');
        Photo_PrivacyHelper::checkMembership();
        Photo_HttpHelper::trimGetAndPostValues();
    }

    public function action_create() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if (! $_POST['comment']) { throw new Exception('Empty comment (1175663871)'); }
        $photo = Photo_ContentHelper::findByID('Photo', $_GET['photoId']);
        if ($this->error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $photo)) { return $this->render('error', 'index'); }
        $comment = Comment::createAndAttachTo($photo, Photo_HtmlHelper::cleanText(mb_substr($_POST['comment'], 0, 4000)));
        $comment->save();
        $photo->save();
        self::logCommentCreation($comment,$photo);
        $user = Photo_UserHelper::load($this->_user);
        Photo_UserHelper::addComment($user);
        $user->save();
        Photo_MessagingHelper::commentCreated($comment, $photo);
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        if ($_GET['xn_out'] != 'json') {
            $photoComments = Photo_CommentHelper::getCommentsFor($photo->id, 0, 1);
            $totalPhotoComments = $photoComments['numComments'];
            $lastPage = $totalPhotoComments / XG_CommentHelper::DEFAULT_PAGE_SIZE;
            $queryString = '?' . http_build_query(array(
                'id' => $photo->id,
                'page' => $lastPage,
            )) . '#' . XG_CommentHelper::commentAnchorId($comment);
            return $this->redirectTo('show', 'photo', $queryString);
        }
        Photo_FullNameHelper::initialize(array($photo, $comment));
        ob_start();
        XG_CommentHelper::outputComment(array(
                'comment' => $comment,
                // TODO: Put this check in Photo_SecurityHelper::currentUserCanDeleteComment($comment) [Jon Aquino 2008-02-08]
                'canDelete' => $this->_user->screenName == $comment->my->attachedToAuthor || XG_SecurityHelper::userIsAdminOrContributor($this->_user, $comment),
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
        $comment = Photo_CommentHelper::load($_POST['id']);
        $photo   = Photo_PhotoHelper::load($comment->my->attachedTo);
        if (! Photo_SecurityHelper::checkCurrentUserCanDeleteComment($this->_user, $comment, $photo) == null) {
            header("HTTP/1.0 403 Forbidden");
            return;
        }
        $user = Photo_UserHelper::load($comment->contributorName);
        if ($user) {
            Photo_UserHelper::removeComment($user);
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
        if ($attachedTo->type != 'Photo') { throw new Exception('Expected Photo but found ' . $attachedTo->type . ' (702791781-1)'); }
        if (! XG_CommentHelper::feedAvailable($attachedTo)) { throw new Exception('No feed available for ' . $attachedTo->id); }
        $commentData = Comment::getCommentsFor($attachedTo->id, 0, 10, 'Y', 'createdDate', 'desc');
        XG_FeedHelper::outputFeed($commentData['comments'], xg_text('COMMENTS_TITLE', $attachedTo->title));
    }

    private function logCommentCreation($comment,$photo) {
        //create activity log item
        if ($comment->my->approved == 'Y' && $photo->my->visibility == 'all' && $photo->my->approved == 'Y') {
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_COMMENT, XG_ActivityHelper::SUBCATEGORY_PHOTO, $comment->contributorName, array($photo,$comment));
        }
    }

}
