<?php

/**
 * Dispatches requests pertaining to forum management.
 */
class Forum_ManageController extends XG_GroupEnabledController {
    
    /**
     * Displays the form for managing forum options
     */
    public function action_index() {
        XG_SecurityHelper::redirectIfNotMember();
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_Filter.php');
        if (! Forum_SecurityHelper::currentUserCanManageForum()) { throw new Exception('Not allowed (689672525)'); }
        $this->topicCounts = array();
        $this->categories = Category::findAll();
        $this->threadingModel = is_null(W_Cache::current('W_Widget')->config['threadingModel']) ? 'flat' : W_Cache::current('W_Widget')->config['threadingModel'];
        $this->forumMainStyle = is_null(W_Cache::current('W_Widget')->config['forumMainStyle']) ? 'categories' : W_Cache::current('W_Widget')->config['forumMainStyle'];
        if (count($this->categories) == 0) {
            $this->categories[] = $sample = Category::create(xg_text('SAMPLE_TITLE'));
            $sample->my->membersCanAddTopics = 'Y';
            $sample->my->membersCanReply = 'Y';
            $this->topicCounts[] = 0;
            Forum_Filter::get('mostRecentDiscussions')->execute($query = XN_Query::create('Content')->end(1));
            if ($query->getTotalCount()) {
                $this->categories[] = $uncategorized = Category::create(xg_text('UNCATEGORIZED'), xg_text('EXISTING_DISCUSSIONS'));
                $this->topicCounts[] = $query->getTotalCount();
                $uncategorized->my->membersCanAddTopics = 'Y';
                $uncategorized->my->membersCanReply = 'Y';
                $uncategorized->my->alternativeIds = 'null';
                // Yes, this should be the string 'null' - see Category#alternativeIds [Jon Aquino 2007-03-30]
            }
        } else {
            foreach ($this->categories as $category) {
                $query = XN_Query::create('Content')->end(1);
                Category::addCategoryFilter($query, $category);
                Forum_Filter::get('mostRecentDiscussions')->execute($query);
                $this->topicCounts[] = $query->getTotalCount();
            }
        }
    }

    /**
     * Processes the form for editing forum options.
     */
    public function action_update() {
        XG_SecurityHelper::redirectIfNotMember();
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (! Forum_SecurityHelper::currentUserCanManageForum()) { throw new Exception('Not allowed (1496321638)'); }
        if (! $_POST['data']) { throw new Exception('Data empty'); }
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $categories = Category::buildCategories($json->decode($_POST['data']));
        if (count($categories) < 2) {
            $this->_widget->config['usingCategories'] = 0;
        } else {
            $this->_widget->config['usingCategories'] = 1;
        }
        $this->_widget->config['threadingModel'] = $_POST['threadingModel'];
        $this->_widget->config['forumMainStyle'] = $_POST['forumMainStyle'];
        $this->_widget->saveConfig();
        $this->redirectTo('index', 'manage', array('saved' => 1));
    }
    
    
}