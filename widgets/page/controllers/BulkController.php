<?php
/**
 * Approves or deletes large numbers of content objects, in chunks.
 *
 * @see "Bazel Code Structure: Bulk Operations"
 */
class Page_BulkController extends W_Controller {

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
    }
    
    /**
     * Sets the privacy level of a chunk of objects created by the Page module.
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
        $this->_widget->includeFileOnce('/lib/helpers/Page_BulkHelper.php');
        return Page_BulkHelper::setPrivacy($limit, ($privacyLevel === 'private'));
    }

    /**
     * Removes a Page and its Comments, and UploadedFiles attached to
     * the Page or Comments. $this->contentRemaining will be set to
     * 1 or 0 depending on whether or not there are content objects remaining to delete
     *
     * Expected GET variables:
     *     id - ID of the Page to delete
     *     limit - maximum number of content objects to remove (approximate).
     */
    public function action_remove() {
        $this->_widget->includeFileOnce('/lib/helpers/Page_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_BulkHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_TablayoutHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        try {
            $page = W_Content::load($_GET['id']);
            if ($page->type != 'Page') { throw new Exception('Not a Page'); }
            if (! Page_SecurityHelper::currentUserCanDeletePage($page)) { throw new Exception('Not allowed'); }
            Index_TablayoutHelper::removeTabsByPageId($page->id);
            list($changed, $remaining) = Page_BulkHelper::remove($page, $_GET['limit']);
            $this->contentRemaining = $remaining;
        } catch (Exception $e) {
            $this->contentRemaining = 0;
        }
    }

    /**
     * Removes Page objects created by the specified user.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $user string  Username of the person whose content to remove.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public function action_removeByUser($limit = null, $user = null) {
        $this->_widget->includeFileOnce('/lib/helpers/Page_BulkHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_UserHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (! XG_SecurityHelper::currentUserCanDeleteUser($user)) {
            throw new Exception("Permission denied.");
        }
        return Page_BulkHelper::removeByUser($limit, $user);
    }

}
