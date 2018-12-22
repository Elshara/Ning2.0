<?php

XG_App::includeFileOnce('/lib/XG_Message.php');
XG_App::includeFileOnce('/lib/XG_CommentHelper.php');

class Groups_CommentController extends XG_GroupEnabledController  {

    public function action_create() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_MessagingHelper.php');

        if (! User::isMember($this->_user)) {
            throw new Exception("You must be a member to comment.");
        }
        if (! (isset($_POST['comment']) && (mb_strlen($comment = trim(xg_scrub($_POST['comment'])))))) {
            throw new Exception("No comment specified");
        }
        if (! isset($_POST['attachedTo']) && mb_strlen($_POST['attachedTo'])) {
            throw new Exception('Nothing specified to attach the comment to');
        }
        $this->group = Group::load($_GET['attachedTo']);
        $this->comment = Comment::createAndAttachTo($this->group, $comment);
        $this->comment->my->groupId = $this->group->id;
        if (Group::isPrivate($this->group)) {
            $this->comment->my->excludeFromPublicSearch = 'Y';
        }
        $this->comment->save();
        Group::updateActivityScore($this->group,GROUP::ACTIVITY_SCORE_COMMENT);
        $this->group->save();
        Groups_MessagingHelper::notifyNewActivityFollowers($this->comment, $this->group);
        
        XG_App::includeFileOnce('/lib/XG_GroupHelper.php');
        if (!Group::isPrivate($this->group)) {
	        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
	        XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_COMMENT, XG_ActivityHelper::SUBCATEGORY_GROUP, $this->_user->screenName, array($this->comment, $this->group));
        }
        
        try {
            $args = array('comment' => $this->comment,
                            'canDelete' => Groups_CommentHelper::userCanDeleteComment($this->_user,$this->comment),
                            'deleteEndpoint' => $this->_buildUrl('comment','delete', array('xn_out' => 'json')));
            ob_start();
            XG_CommentHelper::outputComment($args);
            $this->html = trim(ob_get_contents());
            ob_end_clean();
            $this->approved = true;
            $this->userIsNowFollowing = false;
            if ($_GET['xn_out'] != 'json') {
                // redirect to the front page of the group.
                $url = $this->_buildUrl('group', 'show', array('id' => $this->group->id));
                header('Location: ' . $url);
                exit;
            }
        } catch (Exception $e) {
            $_GET['xn_out'] = 'json';
            $this->errorMessages = $e->getMessage();
            error_log($e->getMessage());
        }
    }


    /**
     * Deletes a comment. Note that this action should only be called by doing a POST via AJAX as it returns nothing.
     */
    public function action_delete() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_CommentHelper.php');
        try {
            if (! isset($_POST['id'])) { throw new Exception("No comment specified"); }
            // Is the current user allowed to delete this comment?
            $comment = Comment::load($_POST['id']);
            if (Groups_CommentHelper::userCanDeleteComment($this->_user,$comment)) {
                if (Comment::remove($comment) === FALSE) {
                    throw new Exception("Comment::remove failed!");
                }
            }
            $this->success = true;
        } catch (Exception $e) {
            header('HTTP/1.0 403 Forbidden');
            $this->errorMessages = $e->getMessage();
        }
    }

}
