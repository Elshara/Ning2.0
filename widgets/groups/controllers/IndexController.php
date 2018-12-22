<?php
/**
* Primary dispatcher for the Groups widget.
*/
class Groups_IndexController extends XG_GroupEnabledController {

    /**
    * Displays the Groups homepage.
    */
    public function action_index() {
        $this->forwardTo('list', 'group');
    }
    
    
    /**
    * Displays an error page.
    */
    public function action_error() {
    }

    public function action_approvalLink() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        if (! XG_SecurityHelper::userIsAdmin()) { return null; }
        if (! XG_App::groupsAreModerated()) { return null; }
        // Caching Approach: approval-link
        $this->setCaching(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
        $query = XN_Query::create('Content')->begin(0)->end(1);
        list($this->groups, $this->numGroups) = array(Groups_Filter::get('moderation')->execute($query, null), $query->getTotalCount());
    } 

    /**
     * Displays the detail page for the specified content object.
     * Used in search results, and other pages that refer to detail pages
     * using /xn/detail/12345 (where 12345 is the content-object ID).
     *
     * @param $content XN_Content  The content object to display
     * @see "Bazel Code Structure: /xn/detail URL Handling"
     */
    public function action_detail($content = null) {
        // If content is supplied because this action is dispatched from the main
        // /xn/detail handler, then use that. Otherwise, redirect to the homepage.
        if (is_null($content)) {
            header("Location: http://{$_SERVER['HTTP_HOST']}/");
            exit();
        }
        switch ($content->type) {
            case 'Group' :
                $this->redirectTo('show', 'group', array('id' => $content->id));
                break;
            case 'Comment' : // Group comment wall comment
                $this->_widget->includeFileOnce('/lib/helpers/Groups_CommentHelper.php');
                $location = XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $content->my->groupId, 'page' => Groups_CommentHelper::page($content))) . '#comments';
                header('Location: ' . $location);
                break;
            default:
                header("Location: http://{$_SERVER['HTTP_HOST']}/");
                exit();
        }
    }

    /**
     * The SearchController in the main widget is interested to know what this 
     * widget has to say about app-wide search queries. @see BAZ-3821
     * 
     * @param $query XN_Query The query object to modify
     */
    public function action_annotateSearchQuery($query) {
        /* Exclude Groups that have my.deleted = Y */
        $query->filter(XN_Filter::any(XN_Filter('type','!like','Group'),
                                      XN_Filter::all(XN_Filter('type','like','Group'),
                                                     XN_Filter('my.deleted','!like','Y'))));
    }
    
}