<?php

/**
 * Dispatches requests pertaining to the Forum module, which appears
 * on the homepage and profile page.
 */
class Forum_EmbedController extends XG_GroupEnabledController {

    /**
     * Returns the Forum_Filter for the specified topic set and embed type.
     *
     * @param $topicSet string  Which topics/comments to display (popular, recent, or promoted)
     * @param $embedType string  Location of the embed (homepage or profiles)
     */
    private static function filter($topicSet, $embedType) {
        if ($topicSet == 'ownerDiscussions') { return Forum_Filter::get('mostRecent'); }
        if ($topicSet == 'groupAdminDiscussions') { return Forum_Filter::get('mostRecent'); }
        if ($topicSet == 'popular') { return Forum_Filter::get('mostPopularDiscussions'); }
        if ($topicSet == 'recentlyUpdated') { return Forum_Filter::get('mostRecentlyUpdatedDiscussions'); }
        if ($topicSet == 'recent' && $embedType == 'profiles') { return Forum_Filter::get('mostRecent'); }
        if ($topicSet == 'recent' && $embedType != 'profiles') { return Forum_Filter::get('mostRecentDiscussions'); }
        if ($topicSet == 'promoted') { return Forum_Filter::get('promoted'); }
        // When adding to this list, check that the corresponding no-data case is covered in moduleBodyAndFooter.php [Jon Aquino 2007-05-07]
        throw new Exception('Invalid topicSet: ' . $topicSet);
    }

    /**
     * Displays a module that spans 1 column.
     *
     * @param $args array  Contains the object that stores the module data ('embed' => XG_Embed)
     */
    public function action_embed1($args) { $this->renderEmbed($args['embed'], 1); }

    /**
     * Displays a module that spans 2 columns.
     *
     * @param $args array  Contains the object that stores the module data ('embed' => XG_Embed)
     */
    public function action_embed2($args) { $this->renderEmbed($args['embed'], 2); }

    /**
     * Displays a module that spans 3 columns.
     *
     * @param $args array  Contains the object that stores the module data ('embed' => XG_Embed)
     */
    public function action_embed3($args) { $this->renderEmbed($args['embed'], 3); }

    /**
     * Displays a module that spans the given number of columns.
     *
     * @param $embed XG_Embed  Stores the module data.
     * @param $columnCount integer  The number of columns that the module will span
     */
    private function renderEmbed($embed, $columnCount) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->userCanEdit = $embed->isOwnedByCurrentUser() || Groups_SecurityHelper::currentUserCanEditGroupEmbed($embed);
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_Filter.php');
        $this->view_options = array(
            array('label' => xg_text('CATEGORIES'), 'value' => 'categories'),
            array('label' => xg_text('DISCUSSIONS'), 'value' => 'discussions'),
        );
        $this->display_options = array(
            array('label' => xg_text('DETAIL_VIEW'), 'value' => 'detail'),
            array('label' => xg_text('TITLES_ONLY'), 'value' => 'titles')
        );
        $this->columnCount = $columnCount;
        $this->setValuesUrl = $this->_buildUrl('embed', 'setValues', array('id' => $embed->getLocator(), 'xn_out' => 'json', 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        if ($embed->getType() == 'profiles') {
            $this->options = array(
                    array('label' => xg_text('MOST_RECENT'), 'value' => 'recent'),
                    array('label' => xg_text('MOST_POPULAR'), 'value' => 'popular'));
            $numOptions = array(0,1,2,3,4,5,10);
        } else {
            $this->options = array(
                    array('label' => xg_text('LATEST_ACTIVITY'), 'value' => 'recentlyUpdated'),
                    array('label' => xg_text('NEWEST_DISCUSSIONS'), 'value' => 'recent'),
                    array('label' => xg_text('MOST_REPLIES'), 'value' => 'popular'),
                    array('label' => xg_text('FEATURED'), 'value' => 'promoted'));
            $numOptions = array(0,1,2,3,4,5,10,20);
            if (XG_GroupHelper::inGroupContext()) { $this->options[] = array('label' => xg_text('GROUP_ADMIN_DISCUSSIONS'), 'value' => 'groupAdminDiscussions'); }
            elseif (XG_SecurityHelper::userIsOwner()) { $this->options[] = array('label' => xg_text('MY_DISCUSSIONS_ONLY'), 'value' => 'ownerDiscussions'); }
            else { $this->options[] = array('label' => xg_text('OWNER_DISCUSSIONS_ONLY'), 'value' => 'ownerDiscussions'); }
            if ($this->userCanEdit) {
                $this->categories = Category::findAll(XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin());
                foreach($this->categories as $category) {
                    $this->options[] = array('label' => $category->title, 'value' => 'category_' . $category->id);
                }
            }
        }
        $this->numOptions = array();
        foreach ($numOptions as $i) {
            $this->numOptions[] = array('label' => (string)($i), 'value' => (string)($i));
        }
        $embed->set('viewSet', is_null($embed->get('viewSet')) ? 'discussions' : $embed->get('viewSet'));
        $embed->set('displaySet', is_null($embed->get('displaySet')) ? 'titles' : $embed->get('displaySet'));
        $embed->set('topicSet', $embed->get('topicSet') ? $embed->get('topicSet') : ($embed->getType() == 'profiles' ? 'recent' : 'recentlyUpdated'));
        $embed->set('itemCount', is_null($embed->get('itemCount')) ? 3 : $embed->get('itemCount'));
        
        $this->embed = $embed;
        
        $this->categoriesEnabled = $this->embed->getType() == 'homepage' && Category::usingCategories();
        
        if ($this->embed->get('viewSet') == 'discussions') {
            $this->topicsAndComments = $this->topicsAndComments($embed);
            if ((! $this->topicsAndComments && $this->embed->getType() == 'profiles') || (! $this->topicsAndComments && !$this->userCanEdit) && $this->embed->getType() != 'groups' || (!$this->userCanEdit && $embed->get('itemCount') == 0)) { return $this->renderNothing(); }
            $this->topics = Topic::topics($this->topicsAndComments);
            XG_Cache::profiles($this->topicsAndComments, $this->topics, Topic::lastCommentContributorNames($this->topics));
            if ($this->categoriesEnabled) {
                $this->categories = Category::titlesAndIds(true);
            }
        } else {
            $end = $embed->get('itemCount') == 0 ? 1 : $embed->get('itemCount');
            $categories = Category::find(0,$end, array('my->order','asc',XN_Attribute::NUMBER));
            $this->categories = $categories['objects'];
            $this->categoryCount = $categories['totalCount'];
            if ($this->categoryCount == 0 && !$this->userCanEdit) {
                return $this->renderNothing();
            }
        }
        
        $this->showFeedLink = $this->embed->getType() == 'groups' && ! XG_GroupHelper::groupIsPrivate() && ! XG_App::appIsPrivate();
        $this->title = $this->_widget->title == xg_text('FORUM') ? xg_text('FORUM') : $this->_widget->title;
        if (XG_GroupHelper::inGroupContext()) { $this->title = xg_text('DISCUSSION_FORUM'); }
        if ($embed->getType() == 'profiles') { $this->title = $this->userCanEdit ? xg_text('MY_DISCUSSIONS') : xg_text('XS_DISCUSSIONS', xg_username(XG_Cache::profiles($this->embed->getOwnerName()))); }
        $this->render('embed');
    }

    /**
     * Retrieves the Topic and Comment objects for the module.
     *
     * @param $embed XG_Embed  Stores the module data.
     * @return array  The Topic and Comment content objects for the module
     */
    protected function topicsAndComments($embed) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $userCanEdit = $embed->isOwnedByCurrentUser() || Groups_SecurityHelper::currentUserCanEditGroupEmbed($embed);
        if (! XG_PromotionHelper::areQueriesEnabled() && $embed->get('topicSet') == 'promoted') { return array(); }
        if (preg_match('/category_(.+)/u', $embed->get('topicSet'), $matches)) {
            try {
                return $embed->get('itemCount') ? Category::recentTopics(Category::findById($matches[1]), $embed->get('itemCount')) : array();
            }catch (Exception $e) {
                //the category was deleted or failed to load
                return array();
            }
        }
        // TODO: It is confusing that we sometimes return 1 item if $embed->get('itemCount') is 0. [Jon Aquino 2008-04-11]
        $itemCount = $embed->get('itemCount') == 0 && $userCanEdit ? 1 : $embed->get('itemCount');
        if ($itemCount == 0) {
            return array();
        }
        $query = XG_Cache::cacheOrderN() ? XG_Query::create('Content') : XN_Query::create('Content');
        $query->end($itemCount);
        if ($embed->getType() == 'profiles') { $query->filter('contributorName', '=', $embed->getOwnerName()); }
        elseif ($embed->get('topicSet') == 'ownerDiscussions') { $query->filter('contributorName', '=', XN_Application::load()->ownerName); }
        elseif ($embed->get('topicSet') == 'groupAdminDiscussions') { $query->filter('contributorName', 'in', Group::adminUsernames(XG_GroupHelper::currentGroup())); }
        return self::filter($embed->get('topicSet'), $embed->getType())->execute($query);
    }

    /**
     * Configures the module to display the topics and comments specified in $_POST['topicSet'] (popular, recent, or promoted).
     * The new HTML will be in the moduleBodyAndFooterHtml property of the JSON output.
     *
     * Expected GET parameters:
     *     id - The embed instance ID, used to retrieve the module data
     *     columnCount - The number of columns that the module spans
     *
     * Expected POST parameters:
     *     displaySet - How to display the topics and comments ('titles' or 'detail')
     *     topicSet - Which topics/comments to display (popular, recent, or promoted)
     */
    public function action_setValues() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_Filter.php');
        XG_HttpHelper::trimGetAndPostValues();
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser() && !XG_SecurityHelper::userIsAdmin()) { throw new Exception('Not embed owner.'); }
        $embed->set('viewSet', $_POST['viewSet']);
        $embed->set('displaySet', $_POST['displaySet']);
        $embed->set('topicSet', $_POST['topicSet']);
        $embed->set('itemCount', $_POST['itemCount']);
        
        if ($_POST['viewSet'] == 'discussions') {
            $topicsAndComments = $this->topicsAndComments($embed);
            $topics = Topic::topics($topicsAndComments);
            XG_Cache::profiles($topicsAndComments, $topics, Topic::lastCommentContributorNames($topics));
            $this->categoriesEnabled = $embed->getType() == 'homepage' && Category::usingCategories();
            if ($this->categoriesEnabled) {
                $this->categories = Category::titlesAndIds(true);
            }
            ob_start();
            $this->_widget->dispatch('embed', 'moduleBodyAndFooter', array(array('topicsAndComments' => $topicsAndComments, 'topics' => $topics, 'columnCount' => $_GET['columnCount'], 'embed' => $embed, 'showContributorName' => $embed->getType() != 'profiles', 'categoryIds' => $this->categories)));
            $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
            ob_end_clean();
        } else {
            $end = $embed->get('itemCount') == 0 ? 1 : $embed->get('itemCount');
            $categories = Category::find(0,$end, array('my->order','asc',XN_Attribute::NUMBER));
            $this->categories = $categories['objects'];
            $this->categoryCount = $categories['totalCount'];
            ob_start();
            $this->_widget->dispatch('embed', 'categories', array(array('categories' => $this->categories, 'showViewAll' => $this->categoryCount > $embed->get('itemCount'), 'embed' => $embed)));
            $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
            ob_end_clean();  
        }

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    public function action_error() {
        $this->render('blank');
    }

    /**
     * Displays the left sidebar for the homepage.
     */
    public function action_sidebar() {
        $CATEGORIES_TO_SHOW = 10;
        /* TODO perhaps want to do this in a more lightweight way */
        $categories = Category::findAll();
        $this->totalCategories = count($categories);
        $this->showCategories = array();
        foreach (array_slice($categories, 0, $CATEGORIES_TO_SHOW) as $category) {
            $link = $this->_buildUrl('topic', 'listForCategory', array('categoryId' => $category->id));
            $this->showCategories[$link] = $category->title;
        }
        $this->showAllCategoriesLink = count($categories) > $CATEGORIES_TO_SHOW;
        $this->mostActiveUsers = User::getMostActiveUsersForCurrentWidget(5, $numActiveUsers);
        $this->numActiveUsers = $numActiveUsers;
        XG_Cache::profiles($this->mostActiveUsers);
    }

    /**
     * Displays the body and footer of the Forum module, which displays recent or popular topics
     * and comments on the homepage and profile page.
     *
     * @param $topicsAndComments array  The Topic and Comment objects to display
     * @param $topics array  A mapping of topic IDs to Topic objects
     * @param $columnCount integer  The number of columns that the module will span
     * @param $embed XG_Embed  Stores the module data.
     * @param $showContributorName boolean  Whether to show the name of the contributor
     * @param $feedAutoDiscoveryTitle string  The title for the feed-autodiscovery element, or null to
     *         skip adding the feed-autodiscovery element to the head of the document
     */
    public function action_moduleBodyAndFooter($args) {
        foreach ($args as $key => $value) { $this->{$key} = $value; }
        $this->groupHasTopics = $this->embed->getType() == 'groups' && ($this->topicsAndComments || Forum_Filter::get('mostRecentDiscussions')->execute(XG_Query::create('Content')->end(1)));
    }
    
    /**
     * Displays the body and footer of the Forum module for the category listing
     *
     * @param $categories array  The Category objects to display
     * @param $displayCount number  The number of categories to display
     */
    public function action_categories($args) {
        foreach ($args as $key => $value) { $this->{$key} = $value; }
    }    
}
