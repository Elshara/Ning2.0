<?php
/**
 * Acts on large numbers of content objects, in chunks.
 *
 * @see "Bazel Code Structure: Bulk Operations"
 */
class Forum_BulkController extends XG_GroupEnabledController {

    /**
     * Sets the privacy level of a chunk of objects created by the Forums module.
     *
     * @param   $limit integer          Maximum number of content objects to change (approximate).
     * @param   $privacyLevel  string   Privacy level to swtich to: 'private' or 'public'.
     * @return  array                   'changed' => the number of content objects deleted,
     *                                  'remaining' => 1 or 0 depending on whether or not there are content objects remaining to set privacy of.
     */
    public function action_setPrivacy($limit = null, $privacyLevel = null) {
        XG_SecurityHelper::redirectIfNotOwner();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        if ($privacyLevel !== 'public' && $privacyLevel !== 'private') { throw new Exception("privacyLevel must be 'public' or 'private'"); }
        $this->_widget->includeFileOnce('/lib/helpers/Forum_BulkHelper.php');
        return Forum_BulkHelper::setPrivacy($limit, ($privacyLevel === 'private'));
    }

    /**
     * Removes a Topic and its Comments, and UploadedFiles attached to
     * the Topic or Comments. $this->contentRemaining will be set to
     * 1 or 0 depending on whether or not there are content objects remaining to delete
     *
     * Expected GET variables:
     *     id - ID of the Topic to delete
     *     limit - maximum number of content objects to remove (approximate).
     */
    public function action_remove() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_BulkHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        if ($topic->type != 'Topic') { throw new Exception('Not a Topic'); }
        if (! Forum_SecurityHelper::currentUserCanDeleteTopic($topic)) { throw new Exception('Not allowed'); }
        list($changed, $remaining) = Forum_BulkHelper::remove($topic, $_GET['limit']);
        $this->contentRemaining = $remaining;
    }

    /**
     * Removes Forum objects created by the specified user.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $user string  Username of the person whose content to remove.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public function action_removeByUser($limit = null, $user = null) {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_BulkHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (! XG_SecurityHelper::currentUserCanDeleteUser($user)) {
            throw new Exception("Permission denied.");
        }
        return Forum_BulkHelper::removeByUser($limit, $user);
    }

    /**
     * Removes a Comment and its SubComments, and UploadedFiles attached to them.
     * $this->contentRemaining will be set to 1 or 0 depending on whether or not there are content objects remaining to delete
     *
     * Expected GET variables:
     *     id - ID of the Comment to delete
     *     limit - maximum number of content objects to remove (approximate).
     */
    public function action_removeCommentAndSubComments() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_BulkHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_UserHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        try {
            $comment = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        } catch (Exception $e) {
            // Comment has been deleted. Get here if the number of items to delete is a multiple of limit. [Jon Aquino 2007-06-08]
            $this->contentRemaining = 0;
            return;
        }
        if ($comment->type != 'Comment') { throw new Exception('Not a Comment'); }
        if (! Forum_SecurityHelper::currentUserCanDeleteCommentAndSubComments($comment)) { throw new Exception('Not allowed'); }
        $results = Forum_BulkHelper::removeCommentAndSubComments($comment, $_GET['limit']);
        $this->contentRemaining = $results['remaining'];
    }

}
