<?php

/**
 * Dispatches requests pertaining to replies to discussion topics.
 */
class Forum_CommentController extends XG_GroupEnabledController {

    /**
    * A single page with a form to reply to a topic (used on the mobile version)
    */
    public function action_new_iphone() {
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['topicId']));
        if ($_GET['parentCommentId']) {
            $parentComment = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['parentCommentId']));
            if ($parentComment->type != 'Comment') { throw new Exception('Not a Comment (555433787)'); }
            $this->parentComment = $parentComment;
        }
        $this->title = xg_text('REPLY_TO_TOPIC_X', $topic->title);
        $this->topic = $topic;
        $this->tags = XG_TagHelper::getTagNamesForObject($this->topic);
    }


    /**
     * Processes the form for a new comment on a discussion topic.
     *
     * Expected GET variables:
     *     topicId - ID of the Topic object
     *     parentCommentId - ID of the parent Comment (optional)
     *     xn_out - Set this to "json" to output JSON instead of redirecting (optional)
     *     firstPage - 1 if we are on the first page; otherwise, 0
     *     lastPage - 1 if we are on the last page; otherwise, 0
     */
    public function action_create() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_UserHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (500675993)'); }
        XG_HttpHelper::trimGetAndPostValues();
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['topicId']));
        if ($topic->type != 'Topic') { throw new Exception('Not a Topic'); }
        if ($topic->my->commentsClosed == 'Y') {
            if ($_GET['xn_out'] == 'json') {
                $this->commentsClosed = true;
            } else {
                $this->redirectTo('show', 'topic', array('id' => $_GET['topicId'], 'repliedToClosedDiscussion' => 1));
            }
            return;
        }
        if (! Forum_SecurityHelper::currentUserCanAddComment($topic)) { throw new Exception('Not allowed (122304363)'); }
        $parentComment = null;
        if ($_GET['parentCommentId']) {
            $parentComment = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['parentCommentId']));
            if ($parentComment->type != 'Comment') { throw new Exception('Not a Comment (555433787)'); }
        }
        if (! $_POST['description']) {
            $this->redirectTo('show', 'topic', array('id' => $_GET['topicId']));
            return;
        }

        $comment = Forum_CommentHelper::createComment($topic, Forum_CommentHelper::cleanDescription($_POST['description']), $parentComment);
        if (XG_GroupHelper::inGroupContext()) {
            $group = XG_GroupHelper::currentGroup();
            Group::updateActivityScore($group,GROUP::ACTIVITY_SCORE_FORUM_COMMENT);
            $group->save();
        }
        // For non-AJAX form submission, ignore upload errors to simplify the implementation (which would otherwise
        // be ugly as there are dozens of forms). Anyway, most people will be using AJAX form submission. [Jon Aquino 2007-01-24]
        if ($_POST['file1'] && $_POST['file1:status'] == 0) { Forum_FileHelper::addAttachment('file1', $comment); }
        if ($_POST['file2'] && $_POST['file2:status'] == 0) { Forum_FileHelper::addAttachment('file2', $comment); }
        if ($_POST['file3'] && $_POST['file3:status'] == 0) { Forum_FileHelper::addAttachment('file3', $comment); }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        $comment->save();
        Forum_UserHelper::updateActivityCount(User::load($this->_user));
        if ($topic->my->categoryId) {
            Category::updateLatestDiscussionActivity($topic->my->categoryId, $topic, true);
        }

        //  If the commenter has chosen to automatically follow when replying, set
        //    following.  Preference defaults to true, so unset == Y
        $user = User::load($this->_user);
        if (!mb_strlen($user->my->autoFollowOnReplyPref) || $user->my->autoFollowOnReplyPref == 'Y') {
            Index_NotificationHelper::startFollowing($topic);
            $this->userIsNowFollowing = 1;
        }

		XG_Browser::execInEmailContext(array($this,'_sendCommentNotification'), $topic, $comment);
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_COMMENT, XG_ActivityHelper::SUBCATEGORY_TOPIC, $comment->contributorName, array($comment));
        if ($_GET['xn_out'] == 'json') {
            // Workaround for BAZ-1366 [Jon Aquino 2007-02-01]
            $_REQUEST['dojo_transport'] = '';
            // Provide the HTML in a separate request, as the IFrameTransport in IE has problems with returned HTML [Jon Aquino 2007-01-30]
            $this->commentHtmlUrl = $this->_buildUrl('comment', 'show', array('id' => $comment->id, 'xn_out' => 'json', 'firstPage' => $_GET['firstPage'], 'lastPage' => $_GET['lastPage']));
            return;
        }
        header('Location: ' . Forum_CommentHelper::url($comment));
    }

	// callback for sending comment notifications
	public function _sendCommentNotification($topic, $comment) { # void
        // TODO: Maybe move this into Forum_NotificationHelper::notifyTopicFollowers($topic) [Jon Aquino 2007-08-23]
        //  Send a new comment notification to the notification alias (new in 1.11)
        $app = XN_Application::load();
        $unfollowLink = $this->_buildUrl('topic', 'show',
                array('id' => $topic->id, 'unfollow' => '1'));
        $opts = array('viewActivity' => xg_text('TO_VIEW_THE_NEW_REPLY_VISIT'),
                      'activity' => xg_text('USER_REPLIED_TO_DISCUSSION_TITLE_ON_APPNAME', xg_username($this->_user), $topic->title, $app->name),
                      'content' => $topic,
                      'url' => Forum_CommentHelper::url($comment),
                      'unfollowLink' => $unfollowLink,
                      'type' => mb_strtolower(xg_text('DISCUSSION')));
        XG_Message_Notification::create(XG_Message_Notification::EVENT_FOLLOW_ACTIVITY, $opts)
                ->send(Index_NotificationHelper::contentNotificationAliasName($topic) . '@lists');
    }


    /**
     * Processes the form for a new comment on a discussion topic. (iPhone-specific)
     * If forum is set to flat mode, insert the quoted text after the reply.
     *
     * Expected GET variables:
     *     topicId - ID of the Topic object
     *     parentCommentId - ID of the parent Comment (optional)
     *     xn_out - Set this to "json" to output JSON instead of redirecting (optional)
     *     firstPage - 1 if we are on the first page; otherwise, 0
     *     lastPage - 1 if we are on the last page; otherwise, 0
     */
    public function action_create_iphone() {
        if ($_GET['parentCommentId']) {
            $parentComment = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['parentCommentId']));
            if ($parentComment->type != 'Comment') { throw new Exception('Not a Comment (555433787)'); }
            if ($this->_widget->config['threadingModel'] != 'threaded' && $_POST['description']) {
                $_POST['description'] .= '<br/><br/><cite>'. xg_html('X_SAID_COLON', xnhtmlentities(xg_username($parentComment->contributorName))) .'</cite><blockquote><div>'. $parentComment->description .'</div></blockquote>';
            }
        }
        $this->action_create();
    }

    /**
     * Returns JSON containing HTML for a Comment
     *
     * Expected GET variables:
     *     id - ID of the Comment to display
     *     xn_out - Set this to "json"
     *     firstPage - 1 if we are on the first page; otherwise, 0
     *     lastPage - 1 if we are on the last page; otherwise, 0
     */
    public function action_show() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        $threadingModel = $this->_widget->config['threadingModel'];
        $comment = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        if ($comment->type != 'Comment') { throw new Exception('Not a Comment'); }
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($comment->my->attachedTo));
        if ($topic->type != 'Topic') { throw new Exception('Not a Topic'); }
        ob_start();
        $this->renderPartial('fragment_comment', 'topic', array('topic' => $topic, 'comment' => $comment, 'firstPage' => $_GET['firstPage'], 'lastPage' => $_GET['lastPage'], 'threaded' => $threadingModel == 'threaded'));
        $this->html = trim(ob_get_contents());
        ob_end_clean();
        $this->positionOfNewComment = Forum_CommentHelper::positionOfNewComment(Forum_CommentHelper::getAncestorCommentCount($comment), $_GET['firstPage'], $_GET['lastPage'], Forum_CommentHelper::newestPostsFirst());
    }

    /**
     * Displays an error page saying that the comment has been deleted.
     *
     * Expected GET variables:
     *     topicId - ID of the Topic object
     */
    public function action_showDeleted() {
        $this->topicId = $_GET['topicId'];
    }

    /**
     * Processes the form for editing a comment
     *
     * Expected GET variables:
     *     - id - ID of the Comment to edit
     */
    public function action_update() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        $json = new NF_JSON();
        header('Content-Type: text/plain');
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        XG_HttpHelper::trimGetAndPostValues();
        $comment = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        if ($comment->type != 'Comment') { throw new Exception('Not a Comment'); }
        if (! Forum_SecurityHelper::currentUserCanEditComment($comment)) { throw new Exception('Not allowed'); }
        $description = Forum_CommentHelper::cleanDescription($_POST['value']);
        if ($description != xg_text('NO_DESCRIPTION')) {
            $comment->description = $description;
            $comment->save();
        }
        // The nl2br and other function calls should be kept in sync with the same set of calls in fragment_comment.php [Jon Aquino 2007-02-27]
        $maxEmbedWidth = Forum_CommentHelper::maxEmbedWidth(Forum_CommentHelper::getAncestorCommentCount($comment));
        echo '(' . $json->encode(array('html' => xg_nl2br(xg_resize_embeds(xg_shorten_linkText($comment->description), $maxEmbedWidth)))) . ')';
    }

    /**
     * Deletes the comment, then redirects to the discussion page.
     *
     * Expected GET variables:
     *     id - ID of the Comment object to delete
     *     xn_out - Set this to "json"
     *     firstPage - 1 if we are on the first page; otherwise, 0
     *     lastPage - 1 if we are on the last page; otherwise, 0
     */
    public function action_delete() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        $threadingModel = $this->_widget->config['threadingModel'];
        $comment = XG_GroupHelper::checkCurrentUserCanAccess(XN_Content::load($_GET['id']));
        if ($comment->type != 'Comment') { throw new Exception('Not a Comment'); }
        if (! Forum_SecurityHelper::currentUserCanDeleteComment($comment)) { throw new Exception('Not allowed'); }
        $targetUrl = Forum_CommentHelper::url($comment);
        $topicId = $comment->my->attachedTo;
        Forum_FileHelper::deleteAttachments($comment);
        $actuallyDeleted = Forum_CommentHelper::delete($comment);
        TopicCommenterLink::deleteLinkIfNecessary($topicId);
        Forum_UserHelper::updateActivityCount(User::load($this->_user));
        // Empty string rather than null, as DeleteCommentLink checks for the presence of the html property [Jon Aquino 2007-04-03]
        $this->html = '';
        if (! $actuallyDeleted) {
            ob_start();
            $this->renderPartial('fragment_comment', 'topic', array('topic' => XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($topicId)), 'comment' => $comment, 'firstPage' => $_GET['firstPage'], 'lastPage' => $_GET['lastPage'], 'threaded' => $threadingModel == 'threaded' ));
            $this->html = trim(ob_get_contents());
            ob_end_clean();
        }
    }

}
