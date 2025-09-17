<?php
/**
 * Dispatches requests pertaining to replies to discussion pages.
 */
XG_App::includeFileOnce('/lib/XG_CommentHelper.php');

class Page_CommentController extends W_Controller {

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
    }

    /**
     * Processes the form for a new comment on a page.
     *
     * Expected GET variables:
     *     page - ID of the Page object
     */
    public function action_create() {
        XG_SecurityHelper::redirectIfNotMember();
        if (! $_POST['comment']) { throw new Exception('Empty comment (1175663871)'); }
        $page = W_Content::load($_GET['page']);
        if ($page->type != 'Page') { throw new Exception('Not a Page'); }
        $comment = Comment::createAndAttachTo($page, xg_scrub(mb_substr($_POST['comment'], 0, 4000)));
        $comment->save();
        if ($_GET['xn_out'] != 'json') { return $this->redirectTo('show', 'page', array('id' => $page->id)); }
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        ob_start();
        XG_CommentHelper::outputComment(array(
                'comment' => $comment,
                'canDelete' => Page_SecurityHelper::currentUserCanDeleteComment($comment),
                'deleteEndpoint' => $this->_buildUrl('comment','delete', array('xn_out' => 'json')),
                'canApprove' => false,
                'approveEndpoint' => null));
        $this->html = trim(ob_get_contents());
        $this->approved = true;
        $this->userIsNowFollowing = false;
        ob_end_clean();
    }

    /**
     * Deletes the comment, then redirects to the discussion page.
     *
     * Expected GET variables:
     *     id - ID of the Comment object to delete
     */
    public function action_delete() {
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_UserHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        $comment = XN_Content::load($_POST['id']);
        if ($comment->type != 'Comment') { throw new Exception('Not a Comment'); }
        if (! Page_SecurityHelper::currentUserCanDeleteComment($comment)) { throw new Exception('Not allowed'); }
        $pageId = $comment->my->attachedTo;
        Comment::remove($comment);
        Page_UserHelper::updateActivityCount(User::load($this->_user))->save();
        $this->success = true;
    }

}
