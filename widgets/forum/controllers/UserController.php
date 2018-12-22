<?php

/**
 * Dispatches requests pertaining to users.
 */
class Forum_UserController extends XG_GroupEnabledController {

    /**
     * Displays a list of discussion contributors, beginning with the most active.
     *
     * Expected GET variables:
     *     page - page number (optional)
     */
    public function action_list() {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->pageSize = 24;
        $this->searchTerm = $_GET['q'];
        $filters = array('my->' . User::widgetAttributeName('activityCount') => array('>', 0, XN_Attribute::NUMBER));
        if (mb_strlen($this->searchTerm)) { $filters['my->searchText'] = array('likeic', $this->searchTerm); }
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $result = User::find($filters, $begin, $begin + $this->pageSize, null, null, true);
        $this->users = $result['users'];
        $this->totalCount = $result['numUsers'];
        XG_Cache::profiles($this->users);
    }

}
