<?php

XG_App::includeFileOnce('/lib/XG_Message.php');
XG_App::includeFileOnce('/lib/XG_HttpHelper.php');

class Profiles_CommentController extends XG_BrowserAwareController {

    public function action_create() {
        // TODO: Simplify the logic of this complicated method. But back
        // any refactoring with thorough unit tests (perhaps using mock objects) [Jon Aquino 2008-02-01]
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
        try {
            if (! User::isMember($this->_user)) {
                throw new Exception("You must be a member to comment.");
            }

            if (! (isset($_POST['comment']) && (mb_strlen($comment = trim(xg_scrub($_POST['comment'])))))) {
                throw new Exception("No comment specified");
            }
            $attachedToRaw = $_POST['attachedTo'] ?? '';
            $attachedTo = is_scalar($attachedToRaw) ? trim((string) $attachedToRaw) : '';
            if ($attachedTo === '') {
                throw new Exception('Nothing specified to attach the comment to');
            }
            $attachedToTypeRaw = $_POST['attachedToType'] ?? '';
            $attachedToType = is_scalar($attachedToTypeRaw) ? trim((string) $attachedToTypeRaw) : '';
            if ($attachedToType === '') {
                throw new Exception('No attachment type specified');
            }
            $this->attachedToContent = self::getAttachedTo($attachedToType, $attachedTo);
            if ($attachedToType === 'User') {
                $cacheKeys = array();
                                $commentIsModerated = $this->getModeratedStatus($this->attachedToContent->contributorName);
                $appName = XN_Application::load()->name;
                $commentReason = xg_text('X_ADDED_A_COMMENT_TO_YOUR_PAGE_ON_Y', xg_username($this->_user), $appName);
                $activityMessageSubject= xg_text('X_ADDED_A_COMMENT_TO_YOUR_PAGE_ON_Y', xg_username($this->_user), $appName);
                $moderationType = 'comment'; //TODO: use xg_text('') with lowercase string
            }
            else {
                throw new Exception("Comments can't be attached to $attachedToType objects");
            }

            $this->comment = Comment::createAndAttachTo($this->attachedToContent, $comment, $commentIsModerated);
            $this->comment->save();
            //TODO: A lot of this code is very similar to that for blog post comments and should be shared [Thomas David Baker 2008-04-08]
            // BAZ-7121 Wait until after saving new content to call updateCommentsToApprove. [Thomas David Baker 2008-04-08]
            if ($attachedToType == 'User' && $commentIsModerated) {
            	$profileOwner = User::load($this->attachedToContent->contributorName);
                if ($profileOwner->id != $this->attachedToContent->id) {
                    $this->attachedToContent->save(); // otherwise it's saved below as $profileOwner->save();
                }
                // WHere should the content owner be sent to moderate this chatter?
                $profileAddress = User::profileAddress($this->attachedToContent->contributorName);
                $moderationUrl = "http://{$_SERVER['HTTP_HOST']}/profile/{$profileAddress}";
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
                Profiles_CommentHelper::updateChattersToApprove($profileOwner);
                $profileOwner->save();
            } else {
                $this->attachedToContent->save();
            }
            if ((!$commentIsModerated)&&($this->attachedToContent->contributorName != $this->_user->screenName)&&($attachedToType === 'User')) {
                XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
                XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_COMMENT, XG_ActivityHelper::SUBCATEGORY_PROFILE, $this->_user->screenName.','.$this->attachedToContent->contributorName, array($this->comment));
            }
        } catch (Exception $e) {
            $_GET['xn_out'] = 'json';
            $this->errorMessages = $e->getMessage();
            error_log($e->getMessage());
        }

        try {
            // If the user doesn't get "new activity" notifications and the content is moderated, send a
            // "you have something new to moderate" notification
            $moderationNotification = false;
            if ($commentIsModerated) {
                $opts = array('content' => $this->comment,
                              'type' => $moderationType,
                              'moderationUrl' => $moderationUrl,
                              'reason' => $commentReason); // 'somebody did whatever on X'
				$moderationNotification = XG_Browser::execInEmailContext(array($this, '_sendModeratedCommentNotification'), $opts);
            }
            // If we haven't sent a moderation notification (either because the comment is not moderated or because the user isn't getting moderation
            // notifications, send an activity notification unless the owner of the comment is the same as the owner of the object the comment
            // is attached to. (Someone doesn't need to be notified of their own activity)
            // HOWEVER, if the comment's target is a blog post, we send a different
            //   type of notification to the follow list!
            if (!$moderationNotification) {
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
                $opts = array('content' => $this->attachedToContent,
                              'type' => $activityType,
                              'url' => Profiles_CommentHelper::url($this->comment));
                if ($this->comment->contributorName != $this->attachedToContent->contributorName) {
					XG_Browser::execInEmailContext(array($this, '_sendCommentNotification'), $commentReason, $activityType, $activityMessageSubject);
                }
            }
        } catch (Exception $e) {
            // Don't let errors during message sending interfere with chatter rendering
            error_log('Chatter notification: ' . $e->getMessage());
        }

        try {
            // chatter form submited before page load or from user with javascript turned off BAZ-2458
            if (($attachedToType === 'User') && ($_GET['xn_out'] != 'htmljson')) {
                $redirectTarget = null;
                if (isset($_POST['successTarget']) && ! is_array($_POST['successTarget'])) {
                    $redirectTarget = XG_HttpHelper::normalizeRedirectTarget($_POST['successTarget']);
                }
                if ($redirectTarget !== null) {
                    header('Location: ' . $redirectTarget);
                    return;
                }
            }

            $renderInfo = $this->getRenderingInfo($attachedToType, $this->_user, $this->comment);
            $this->partialTemplate = $renderInfo['template'];
            $this->partialController = $renderInfo['controller'];
            $this->partialArgs = $renderInfo['args'];
        } catch (Exception $e) {
            $_GET['xn_out'] = 'json';
            $this->errorMessages = $e->getMessage();
            error_log($e->getMessage());
        }
    }
    public function action_create_iphone() {
        XG_SecurityHelper::redirectIfNotMember(null, true);
        $this->action_create();
		if ($this->errorMessages) {
			unset($_GET['xn_out']); // Thank you guys. [Andrey 2008-09-12]
			$this->forwardTo('new','comment', array($this->errorMessages));
		}
    }

	// callback for sending comment moderation notifications
    public function _sendModeratedCommentNotification($opts) {
		return XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_NEW, $opts)->send($this->attachedToContent->contributorName);
    }

	// callback for sending comment moderation notifications
	public function _sendCommentNotification($commentReason, $activityType, $activityMessageSubject) { # void
		// Profile comment (chatter)
		$opts = array('viewActivity' => xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT'),
					'activity' => mb_strtoupper(mb_substr($commentReason,0,1)) . mb_substr($commentReason,1),
					'content' => $this->attachedToContent,
					'type' => $activityType,
					'subject' => $activityMessageSubject,
					'url' => XG_HttpHelper::addParameter(Profiles_CommentHelper::url($this->comment), XG_App::SIGN_IN_IF_SIGNED_OUT, 1),
					'reason' => $commentReason);
		$activityNotification = XG_Message_Notification::create(XG_Message_Notification::EVENT_ACTIVITY, $opts);
		$activityNotification->send($this->attachedToContent->contributorName);
    }


    /**
     * Renders a profile comment form
     *
     * Expected GET variables:
     * 	- screenName - Target user profile to post message on
     */
	public function action_new_iphone($error = NULL) {
        XG_SecurityHelper::redirectIfNotMember(null, true);
        if (!(isset($_GET['screenName']) && mb_strlen($_GET['screenName']))) {
            throw new Exception('No user profile specified');
        }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $this->user = User::load($_GET['screenName']);
        $this->profile = XG_Cache::profiles($this->user->contributorName);
        $this->errors = $error ? array($error) : array();
        list($this->userAgeSex, $this->userLocation) = Profiles_UserHelper::getPrivateUserInfo($this->profile);
    }

    /**
     * Creates a comment on a blog post.
     *
     * If xn_out=json, outputs JSON for an object with the following properties: html (for the comment),
     * and approved (whether the comment is approved), userIsNowFollowing (whether submitting
     * the comment caused the user to start following the comment thread)
     *
     * Otherwise, redirects to target URL.
     *
     * Expected GET variables:
     *         - xn_out - "json" if this is an Ajax request; otherwise, null
     *         - attachedTo - the content ID of the blog post
     *         - target - the URL to redirect to if this is not an Ajax request
     *
     * Expected POST variables:
     *         - comment - the comment text
     *
     * @see XG_CommentHelper
     */
    public function action_createForBlogPost() {
        // TODO: Simplify the logic of this complicated method. But back
        // any refactoring with thorough unit tests (perhaps using mock objects) [Jon Aquino 2008-02-01]
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
        try {
            if (! User::isMember($this->_user)) {
                throw new Exception("You must be a member to comment.");
            }

            if (! (isset($_POST['comment']) && (mb_strlen($comment = trim(xg_scrub($_POST['comment'])))))) {
                throw new Exception("No comment specified");
            }
            $attachedToRaw = $_GET['attachedTo'] ?? '';
            $attachedTo = is_scalar($attachedToRaw) ? trim((string) $attachedToRaw) : '';
            if ($attachedTo === '') {
                throw new Exception('Nothing specified to attach the comment to');
            }
            $attachedToType = 'BlogPost';
            $attachedToContent = self::getAttachedTo($attachedToType, $attachedTo);
            if ($attachedToType === 'BlogPost') {
                // Who is the owner of this post?
                $postOwner = User::load($attachedToContent->contributorName);

                // Who does the owner of this post allow to comment (all, friends, me)
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
                $isFriend = 'friend' == Profiles_UserHelper::getFriendStatusFor($attachedToContent->contributorName, $this->_user);
                if (! Profiles_CommentHelper::canCurrentUserSeeAddCommentSection($attachedToContent, $isFriend)) {
                    throw new Exception('You are not allowed to comment on this post. Perhaps the owner recently changed commenting permissions.');
                }

                //  If the commenter has chosen to automatically follow when replying, set
                //    following.  Preference defaults to true, so unset == Y
                $user = User::load($this->_user);
                if (!mb_strlen($user->my->autoFollowOnReplyPref) || $user->my->autoFollowOnReplyPref == 'Y') {
                    Index_NotificationHelper::startFollowing($attachedToContent);
                    $this->userIsNowFollowing = 1;
                }

                // Does the owner of this post want to moderate?
                $moderationAttributeName = XG_App::widgetAttributeName($this->_widget, 'moderateBlogComments');
                // Comments on your own content do not need approval
                $commentIsModerated = (($postOwner->my->{$moderationAttributeName} == 'Y') && ($attachedToContent->contributorName != $this->_user->screenName));
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_CacheHelper.php');

                $commentReason = xg_text('SOMEBODY_COMMENTED_BLOGPOST_ADDED_TO_X', XN_Application::load()->name);
                $activityType = 'blog post';
                $activityMessageSubject = null;
                $moderationType = null;
            }
            $comment = Comment::createAndAttachTo($attachedToContent, $comment, $commentIsModerated);
            $comment->save();
            $attachedToContent->save();
            // BAZ-7121 Wait until after saving new content to call updateCommentsToApprove. [Thomas David Baker 2008-04-07]
            if ($attachedToType == 'BlogPost' && $commentIsModerated) {
                // Where should the content owner be sent to moderate this comment?
                $moderationUrl = $this->_buildUrl('blog','show',array('id' => $attachedToContent->id));
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
                Profiles_CommentHelper::updateCommentsToApprove($postOwner);
                $postOwner->save();
            }
            if ((!$commentIsModerated)&&($attachedToType === 'BlogPost')&&($attachedToContent->my->visibility == 'all')) {
                XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
                XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_COMMENT, XG_ActivityHelper::SUBCATEGORY_BLOG, $comment->contributorName, array($comment,$attachedToContent));
            }
        } catch (Exception $e) {
            $_GET['xn_out'] = 'json';
            $this->errorMessages = $e->getMessage();
            error_log($e->getMessage());
        }

        try {
            // If the user doesn't get "new activity" notifications and the content is moderated, send a
            // "you have something new to moderate" notification
            $moderationNotification = false;
            if ($commentIsModerated) {
                $opts = array('content' => $comment,
                              'type' => $moderationType,
                              'moderationUrl' => $moderationUrl,
                              'reason' => $commentReason); // 'somebody did whatever on X'

                $moderationNotification = XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_NEW, $opts)->send($attachedToContent->contributorName);
            }
            // If we haven't sent a moderation notification (either because the comment is not moderated or because the user isn't getting moderation
            // notifications, send an activity notification unless the owner of the comment is the same as the owner of the object the comment
            // is attached to. (Someone doesn't need to be notified of their own activity)
            // HOWEVER, if the comment's target is a blog post, we send a different
            //   type of notification to the follow list!
            if (!$moderationNotification) {
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
                $opts = array('content' => $attachedToContent,
                              'type' => $activityType,
                              'url' => Profiles_CommentHelper::url($comment));
                if ($attachedToType === 'BlogPost') {
                    // Blog post
                    $opts['viewActivity'] = xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT');
                    $opts['activity'] = xg_text('X_ADDED_A_COMMENT_TO_THE_BLOG_POST_Y_ON_Z',
                            xg_username($this->_user), $attachedToContent->title,
                            XN_Application::load()->name);
                    $opts['unfollowLink'] = XG_HttpHelper::addParameter($opts['url'], 'unfollow', '1');
                    $activityNotification = XG_Message_Notification::create(XG_Message_Notification::EVENT_FOLLOW_ACTIVITY, $opts);
                    $activityNotification->send(Index_NotificationHelper::contentNotificationAliasName(
                            $attachedToContent) . '@lists');
                }
            }
        } catch (Exception $e) {
            // Don't let errors during message sending interfere with chatter rendering
            error_log('Chatter notification: ' . $e->getMessage());
        }

        if ($_GET['xn_out'] == 'json') {
            XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
            $this->approved = $comment->my->approved == 'Y';
            ob_start();
            XG_CommentHelper::outputComment(array(
                    'comment' => $comment,
                    'canDelete' => Profiles_CommentHelper::userCanDeleteComment($this->_user, $comment),
                    'deleteEndpoint' => $this->_buildUrl('comment','delete', array('xn_out' => 'json')),
                    'canApprove' => Profiles_CommentHelper::userCanApproveComment($this->_user, $comment),
                    'approveEndpoint' => $this->_buildUrl('comment','approve', array('xn_out' => 'json'))));
            $this->html = trim(ob_get_contents());
            ob_end_clean();
        } else {
            $this->redirectTo($_GET['target']);
        }
    }

    /**
     * Deletes a comment. Note that this action should only be called by doing a POST via AJAX as it returns nothing.
     */
    public function action_delete() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
        try {
            if (! isset($_POST['id'])) { throw new Exception("No comment specified"); }
            // Is the current user allowed to delete this comment?
            $comment = Comment::load($_POST['id']);
            $attachedToType = $comment->my->attachedToType;
            $attachedToAuthor = $comment->my->attachedToAuthor;
            if ($attachedToType === 'BlogPost') {
                if (! Profiles_CommentHelper::userCanDeleteComment($this->_user, $comment)) {
                    throw new Exception("You're not allowed to delete this comment.");
                }
            } elseif ($attachedToType === 'User') {
                if (! Profiles_CommentHelper::userCanDeleteChatter($this->_user, $comment)) {
                    throw new Exception("You're not allowed to delete this comment.");
                }
            } else {
                throw new Exception("Unknown comment attachedTo type");
            }
            if ($comment->my->approved == 'N') {
                // If the comment was moderated (and not yet approved), the count of things-to-moderate has changed
                W_Controller::invalidateCache(XG_Cache::key('moderation', $attachedToAuthor, W_Cache::current('W_Widget')));
                // No notification when blog comments or chatters are approved/denied (BAZ-1473)
            }
            if (Comment::remove($comment, $attachedToType == 'User' ? false : true) === FALSE) {
                throw new Exception("Comment::remove failed!");
            }
            $user = User::load($attachedToAuthor);
            if ($attachedToType == 'User') { Profiles_CommentHelper::updateChattersToApprove($user); }
            else { Profiles_CommentHelper::updateCommentsToApprove($user); }
            $user->save();
            $this->success = true;
        } catch (Exception $e) {
            header('HTTP/1.0 403 Forbidden');
            $this->errorMessages = $e->getMessage();
        }
    }

    /**
     * Approves a comment. Note that this action should only be called by doing a POST via AJAX as it returns nothing.
     */
    public function action_approve() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
        try {
            if (! isset($_POST['id'])) { throw new Exception("No comment specified"); }
            // Is the current user allowed to delete this comment?
            $comment = Comment::load($_POST['id']);
            if ($comment->my->attachedToType == 'BlogPost') {
                if (! Profiles_CommentHelper::userCanApproveComment($this->_user, $comment)) {
                    throw new Exception("You're not allowed to approve this comment.");
                }
            } elseif ($comment->my->attachedToType == 'User') {
                if (! Profiles_CommentHelper::userCanApproveChatter($this->_user, $comment)) {
                    throw new Exception("You're not allowed to approve this comment.");
                }
            } else {
                throw new Exception("Unknown comment attachedTo type");
            }
            Comment::approve($comment, $comment->my->attachedToType == 'User' ? false : true);
            $this->approved = $comment->id . ' ' . $comment->my->approved;

            // The count of things-to-moderate has changed
            $user = User::load($comment->my->attachedToAuthor);
            if ($comment->my->attachedToType == 'User') {
                Profiles_CommentHelper::updateChattersToApprove($user);
            }
            else {
                Profiles_CommentHelper::updateCommentsToApprove($user);
            }
            $user->save();
            $this->success = true;
            // No notification when blog comments or chatters are approved/denied (BAZ-1473)
            if (($comment->my->approved=='Y')&&($comment->my->attachedToType == 'BlogPost')) {
                $this->attachedToContent = self::getAttachedTo($comment->my->attachedToType, $comment->my->attachedTo);
                if ($this->attachedToContent->my->visibility == 'all') {
                    XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
                    XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_COMMENT, XG_ActivityHelper::SUBCATEGORY_BLOG, $comment->contributorName, array($comment,$this->attachedToContent));
                }
            }
        } catch (Exception $e) {
                header('HTTP/1.0 403 Forbidden');
                $this->errorMessages = $e->getMessage();
        }
    }

    /**
     * Shows a list of comments of a particular type
     *
     */
    public function action_list($args=NULL) {
        try {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
            if (! (isset($_GET['attachedTo']) && isset($_GET['attachedToType']))) {
                throw new Exception("No attachedTo & attachedToType specified");
            }

            if ($_GET['attachedToType'] != 'User') {
                throw new Exception("Don't know how to show a list of comments attached to a {$_GET['attachedToType']}");
            }
            $template = 'list';
            $controller = 'chatter';

            $approved = 'Y'; // Only retrieve approved comments
            $attachedTo = self::getAttachedTo($_GET['attachedToType'], $_GET['attachedTo']);
            $screenName = $attachedTo->contributorName;
            $isMyPage = $this->_user->isLoggedIn()
                    && ($this->_user->screenName == $screenName);
            if ($isMyPage || XG_SecurityHelper::userIsAdmin()) {
                $approved = null; // Retrieve unapproved comments, too

		// we'll keep my.chattersToApprove synchronized with actual number (BAZ-10147) [ywh 2008-09-24]
		if ($isMyPage && ($_GET['attachedToType'] == 'User')) {
		    $unapprovedCommentInfo = Comment::getCommentsFor($attachedTo->id, 0, 1, 'N');
		    $numUnapproved = $unapprovedCommentInfo['numComments'];
		    $user = User::load(XN_Profile::current());
		    if ($numUnapproved != $user->my->chattersToApprove) {
			$user->my->chattersToApprove = $numUnapproved;
			$user->save();
		    }
		}
            }

            // How many comments on each page
            $this->pageSize = 20;
            // Pages start at 1, not 0
            $this->page = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
            if ($this->page < 1) { $this->page = 1; }
            $this->start = ($this->page - 1) * $this->pageSize;
            $this->end = $this->start + $this->pageSize;
            $this->paginationTargetParams = array('attachedToType' => $_GET['attachedToType'], 'attachedTo' => $_GET['attachedTo']);
            $this->commentInfo = Comment::getCommentsFor($attachedTo->id, $this->start, $this->end, $approved, 'createdDate', 'desc');

            if ($_GET['test_many_comments']) {
                $max = $_GET['test_many_comments'];
                $numCommentsThisPage = ($this->end > $max) ? ($max - $this->end) : $this->pageSize;
                if ($numCommentsThisPage < 0) { $numCommentsThisPage = $this->pageSize + $numCommentsThisPage; }
                $this->commentInfo = Comment::getCommentsFor($attachedTo->id, 0, $numCommentsThisPage, $approved, 'createdDate', 'desc');
                $this->commentInfo['numComments'] = $max;
                while (count($this->commentInfo['comments']) < $numCommentsThisPage) {
                    $i = mt_rand(1, count($this->commentInfo['comments'])-1);
                    $this->commentInfo['comments'][] = $this->commentInfo['comments'][$i];
                }
                $this->paginationTargetParams['test_many_comments'] = $_GET['test_many_comments'];
            }

            // If we're not on the first page (where new comments would appear),
            // set $this->showCommentUrl to where the JS should redirect the
            // user after submitting a new comment
            if ($this->page != 1) {
                $showCommentUrlParams = $this->paginationTargetParams;
                $showCommentUrlParams['page'] = 1;
                $this->showCommentUrl = $this->_buildUrl('comment','list', $showCommentUrlParams) . '#xg_profiles_chatterwall_list';
            } else {
                // No redirection after comment submission
                $this->showCommentUrl = '';
            }

            $this->numComments = $this->commentInfo['numComments'];
            $this->numPages = ceil($this->commentInfo['numComments'] / $this->pageSize);
            $this->profile = XG_Cache::profiles($attachedTo->contributorName);
            $this->pageTitle = ($this->profile->screenName == XN_Profile::current()->screenName) ? xg_text('MY_COMMENTS') : xg_text('USERNAMES_COMMENTS', ucfirst(xg_username($this->profile)));
            $this->render($template, $controller);
            if ($isMyPage && $this->commentInfo['numComments']) {
                //  Get friend status for all commenters on the page
                XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
                $commenters = array();
                foreach ($this->commentInfo['comments'] as $comment) {
                    $commenters[$comment->contributorName] = TRUE;
                }
                $this->friendStatus = XG_ContactHelper::getFriendStatusFor($screenName,
                        array_keys($commenters));
            }

            if($args['output']=='embed'){
                $this->render(XG_Browser::current()->template('fragment_embeddableList'));
                return;
            }
            if($_GET['output']=='items'){
                $this->render(XG_Browser::current()->template('fragment_comments'));
                return;
            }
        } catch (Exception $e) {
            error_log("Can't render comment list: " . $e->getMessage());
            $this->redirectTo('index','index');
        }
    }
    //
    public function action_list_iphone($args) { # void
        try {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
            if (! (isset($_GET['attachedTo']) && isset($_GET['attachedToType']))) {
                throw new Exception("No attachedTo & attachedToType specified");
            }

            if ($_GET['attachedToType'] != 'User') {
                throw new Exception("Don't know how to show a list of comments attached to a {$_GET['attachedToType']}");
            }

            $approved = 'Y'; // Only retrieve approved comments
            $attachedTo = self::getAttachedTo($_GET['attachedToType'], $_GET['attachedTo']);
            $this->screenName = $attachedTo->contributorName;
            $this->isMyPage = $this->_user->isLoggedIn()
                    && ($this->_user->screenName == $this->screenName);
			/*if ($this->isMyPage || XG_SecurityHelper::userIsAdmin()) {  // Commented out because of BAZ-9782 [Andrey 2008-09-12]
                $approved = null; // Retrieve unapproved comments, too
			}*/

            $this->commentInfo = Comment::getCommentsFor($attachedTo->id, 0, 5, $approved, 'createdDate', 'desc');
            $this->numComments = $this->commentInfo['numComments'];
            $this->profile = XG_Cache::profiles($attachedTo->contributorName);
            $this->pageTitle = ($this->profile->screenName == XN_Profile::current()->screenName) ? xg_text('MY_COMMENTS') : xg_text('USERNAMES_COMMENTS', ucfirst(xg_username($this->profile)));
            $this->showAddLink = !$this->getModeratedStatus($this->screenName);
            $this->render('fragment_embeddableList');
        } catch (Exception $e) {
            error_log("Can't render comment list: " . $e->getMessage());
            $this->redirectTo('index','index');
        }
    }

    /**
     * Convert fragment parameter to URL-fragment
     *
     */
    public function action_show() {
        $fragment = $_GET['commentid'];
        $attachedTo = $_GET['attachedTo'];
        $url = User::profileUrl($attachedTo)."#".$fragment;
        $this->redirectTo($url);
    }


    /**
     * Retrieves the first comment before a particular time attached to a particular object. This action
     * should only be called by doing a GET via AJAX
     */
	public function action_previous() {
         try {
             if (! (isset($_GET['attachedTo']) && isset($_GET['when']) && isset($_GET['attachedToType']))) {
                 throw new Exception("No attachedTo & when specified");
             }
             $when = preg_match('/^\d+$/u', $_GET['when']) ? $_GET['when'] : time();
             $timestamp = gmdate('Y-m-d\TH:i:s\Z', $when);
             $filters['createdDate'] = array('<', $timestamp, XN_Attribute::DATE);
             $approved = 'Y'; // Only retrieve approved comments
             $attachedTo = self::getAttachedTo($_GET['attachedToType'], $_GET['attachedTo']);
             if ($this->_user->isLoggedIn()) {
                 if (($this->_user->screenName == $attachedTo->contributorName) || XG_SecurityHelper::userIsAdmin()) {
                     $approved = null; // Retrieve unapproved comments, too
                 }
             }
             $commentInfo = Comment::getCommentsFor($attachedTo->id, 0, 1, $approved, 'createdDate', 'desc', $filters);
             if (count($commentInfo['comments']) == 1) {
                 $renderInfo = $this->getRenderingInfo($attachedTo->type, $this->_user, $commentInfo['comments'][0]);
                 $this->partialTemplate = $renderInfo['template'];
                 $this->partialController = $renderInfo['controller'];
                 $this->partialArgs = $renderInfo['args'];
                 $this->render('create');
             }
         } catch (Exception $e) {
             header('HTTP/1.0 403 Forbidden');
             $this->errorMessages = $e->getMessage();
         }
     }

     /**
      * Sets a user's moderation preference for a type of comment.
      *
      * @deprecated  3.6  Use Profiles_EmbedController::action_setValues instead
      */
	public function action_setModeration() {
         try {
             if (! (isset($_POST['attachedToType']) && isset($_POST['moderate']))) {
                 throw new Exception('No type + moderation preference specified.');
             }
             if ($_POST['attachedToType'] == 'BlogPost') {
                 $moderationAttributeName = XG_App::widgetAttributeName($this->_widget, 'moderateBlogComments');
             }
             else if ($_POST['attachedToType'] == 'User') {
                 $moderationAttributeName = XG_App::widgetAttributeName($this->_widget, 'moderateChatters');
             }
             else {
                 throw new Exception("Unknown type: {$_POST['attachedToType']}");
             }
             if ($_POST['moderate'] == 'Y') {
                 $moderate = 'Y';
             }
             else if ($_POST['moderate'] == 'N') {
                 $moderate = 'N';
             }
             else {
                 throw new Exception("moderate value must be Y or N");
             }
             $user = User::load($this->_user);
             $user->my->set($moderationAttributeName, $moderate);
             $user->save();
             // Invalidate this user's cached pages to take care of embeds, etc.
         } catch (Exception $e) {
             header('HTTP/1.0 403 Forbidden');
             $this->errorMessages = $e->getMessage();
         }
     }

    public function action_thread() {
        if (!$_GET['screenName']) {
            error_log("No screenName parameter supplied for comment thread");
            $this->redirectTo('index', 'index');
            return;
        }

        //  Get profile and friend status for the other user
        $this->otherProfile = XG_Cache::profiles($_GET['screenName']);
        if (!$this->otherProfile) {
            $this->redirectTo('index', 'index');
            return;
        }

        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        //  Put the friend status in an array just because that's how
        //  fragment_chatter_list.php expects it
        $this->friendStatus = array($this->otherProfile->screenName =>
                XG_ContactHelper::getFriendStatusFor(
                $this->_user->screenName, $this->otherProfile));

        // How many comments on each page
        $this->pageSize = 20;
        // Pages start at 1, not 0
        $this->page = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        if ($this->page < 1) { $this->page = 1; }
        $this->start = ($this->page - 1) * $this->pageSize;
        $this->end = $this->start + $this->pageSize;

        //  Get his comments on my page and my comments on his page
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
        $this->commentInfo = Comment::getCommentThread($this->_user, $_GET['screenName'],
                $this->start, $this->end);
        $this->numPages = ceil($this->commentInfo['numComments'] / $this->pageSize);
        $this->paginationTargetParams = array('screenName' => $_GET['screenName']);
    }

	private function getRenderingInfo($attachedToType, $user, $comment) {
         $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
         $args = array('comment' => $comment);
         if ($attachedToType == 'BlogPost') {
            $args['canDelete'] = Profiles_CommentHelper::userCanDeleteComment($user, $comment);
            $args['canApprove'] = Profiles_CommentHelper::userCanApproveComment($user, $comment);
            $template = 'comment';
            $controller = 'blog';
         } elseif ($attachedToType == 'User') {
            $args['canDelete'] = Profiles_CommentHelper::userCanDeleteChatter($user, $comment);
            $args['canApprove'] = Profiles_CommentHelper::userCanApproveChatter($user, $comment);
            $template = 'fragment_chatter';
            $controller = 'chatter';
         }
         return array('template' => $template, 'controller' => $controller, 'args' => $args);
     }

	private static function getAttachedTo($attachedToType, $attachedTo) {
         if ($attachedToType == 'BlogPost') {
            $postInfo = BlogPost::find(array('id' => $attachedTo));
            if (count($postInfo['posts'][0]) != 1) {
                throw new Exception("Couldn't find the post to attach the comment to");
            }
            return $postInfo['posts'][0];
         } else if ($attachedToType == 'User') {
            $user = User::load($attachedTo);
            if (! $user) {
                throw new Exception("Couldn't find the user to attach the comment to");
            }
            return $user;
         }
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
        if ($attachedTo->type != 'BlogPost') { throw new Exception('Expected BlogPost but found ' . $attachedTo->type . ' (702791781)'); }
        if (! XG_CommentHelper::feedAvailable($attachedTo)) { throw new Exception('No feed available for ' . $attachedTo->id); }
        $commentData = Comment::getCommentsFor($attachedTo->id, 0, 10, 'Y', 'createdDate', 'desc');
        XG_FeedHelper::outputFeed($commentData['comments'], xg_text('COMMENTS_TITLE', $attachedTo->title));
    }

    /**
	 *  Returns the "moderated" status for new comments. Returns TRUE if new comments must be approved and FALSE otherwise
     *
	 *  @param      $owner		string		Owner's screenName
     *  @return     bool
     */
	protected function getModeratedStatus($owner) { # void
		// If you're giving yourself a chatter, it is not moderated.
		// If you're giving someone else a chatter, it's only moderated if their 'moderateChatters'
		// setting is 'Y'
		if ($owner == $this->_user->screenName) {
			return false;
		}
		$moderationAttributeName = XG_App::widgetAttributeName($this->_widget, 'moderateChatters');
		return User::load($owner)->my->raw($moderationAttributeName) == 'Y';
    }

}
