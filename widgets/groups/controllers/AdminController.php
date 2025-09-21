<?php
/**
* Admin dispatcher for the Groups widget; currently just used for moderation activities.
*/
class Groups_AdminController extends XG_GroupEnabledController {

    protected function _before() {
        XG_SecurityHelper::redirectIfNotAdmin();
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_HtmlHelper.php');
    }

    /**
    *  Approve or reject an individual group that's awaiting moderation. Must be a post operation.
    */
    public function action_approve() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        $groupId = $_GET['id'] ?? '';
        if (!is_scalar($groupId) || ($groupId = trim((string) $groupId)) === '') {
            throw new Exception('Missing group id');
        }

        $group = Group::load($groupId);
        $approvedFlag = $_POST['approved'] ?? '';
        $approvedFlag = is_scalar($approvedFlag) ? strtoupper(trim((string) $approvedFlag)) : '';

        if ($approvedFlag === 'Y') {
            $this->_widget->includeFileOnce('/lib/helpers/Groups_MessagingHelper.php');
            $group->my->approved = 'Y';
            $group->save();
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_GROUP, $group->contributorName, array($group));
            Groups_MessagingHelper::groupApproved($group);
        } else {
            $groupObjects = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'GroupMembership')->filter('my.groupId', '=', $group->id)->end(15)->execute();
            XN_Content::delete(array_merge($groupObjects, array($group)));
        }
        self::getModeratedGroups();
        ob_start();
        $this->renderPartial('fragment_listForApproval', 'admin', array(
                'groups' => $this->groups, 'changeUrl' => $this->_buildUrl('admin', 'listForApproval'), 'curPage' => $this->page, 'numPages' => $this->numPages));
        $this->html = trim(ob_get_contents());
        ob_end_clean();
        W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
    }



    /**
     * Returns a list of groups requiring admin approval.
     *
     */
     public function action_listForApproval() {
         self::getModeratedGroups();
     }


     /**
      * establishes variables for group moderation:
      * $this->pageSize
      * $this->groups
      * $this->totalCount
      * $this->numPages
      * $this->page
     */
     private function getModeratedGroups() {
         XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
         $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
         $query = XN_Query::create('Content');
         $this->pageSize = 20;
         $pageParam = $_GET['page'] ?? 1;
         $pageNumber = 1;
         if (is_scalar($pageParam) && ($pageValue = trim((string) $pageParam)) !== '') {
             $pageNumber = (int) $pageValue;
         }
         if ($pageNumber < 1) {
             $pageNumber = 1;
         }

         $begin = XG_PaginationHelper::computeStart($pageNumber, $this->pageSize);
         $query->begin($begin);
         $query->end($begin + $this->pageSize);
         list($this->groups, $this->totalCount) = array(Groups_Filter::get('moderation')->execute($query, null), $query->getTotalCount());
         $this->numPages = ($this->pageSize > 0) ? (int) ceil($this->totalCount / $this->pageSize) : 0;
         $this->page = $pageNumber;
     }

}
?>