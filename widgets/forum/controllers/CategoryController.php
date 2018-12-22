<?php

/**
 * Dispatches requests pertaining to categories.
 */
class Forum_CategoryController extends XG_GroupEnabledController {

    /**
     * Displays a list of recent discussions in specific categories (paginated).
     *
     * Optional $_GET variable: 'page' for which page of discussions to view.  Defaults to 1.
     */
    public function action_list() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_Filter.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $CATEGORIES_PER_PAGE = 5;
        $TOPICS_PER_CATEGORY = 5;
        $pageNum = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        //TODO more efficient to change findAll to call a new find function that can partition and then call that new function here.
        $this->numPerPage = $CATEGORIES_PER_PAGE;
        $offset = ($pageNum - 1) * $CATEGORIES_PER_PAGE;
        $allCategories = Category::findAll();
        $this->totalCategories = count($allCategories);
        $this->categories = array_slice($allCategories, $offset, $CATEGORIES_PER_PAGE);
        $this->categoryIdToRecentTopics = array();
        $fetchTopicsCount = 5;
        $i = 0;
        $topics = array();
        foreach ($this->categories as $category) {
            $i++;
            if ($i <= $fetchTopicsCount) {
                $this->categoryIdToRecentTopics[$category->id] = Category::recentTopics($category, $TOPICS_PER_CATEGORY);
                $topics = array_merge($topics, $this->categoryIdToRecentTopics[$category->id]);
	        } else {
	            $this->categoryIdToRecentTopics[$category->id] = array(-1);
	        }
        }
        $this->showFeedLink = ! XG_App::appIsPrivate() && ! XG_GroupHelper::groupIsPrivate();
        $this->showFollowLink = Forum_SecurityHelper::currentUserCanFollowNewTopics();
        // Pre-load profiles and User objects [Jon Aquino 2007-10-07]
        XG_Cache::profiles($topics, Topic::lastCommentContributorNames($topics));
    }

    /**
     * Displays a list of all forum categories (paginated, iPhone-specific)
     * along with recent discussions in each category
     *
     * Optional $_GET variable: 'page' for which page of discussions to view.  Defaults to 1.
     */
    public function action_list_iphone() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_Filter.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $CATEGORIES_PER_PAGE = 5;
        $TOPICS_PER_CATEGORY = 3;
        $pageNum = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        //TODO more efficient to change findAll to call a new find function that can partition and then call that new function here.
        $this->numPerPage = $CATEGORIES_PER_PAGE;
        $offset = ($pageNum - 1) * $CATEGORIES_PER_PAGE;
        $allCategories = Category::findAll();
        $this->totalCategories = count($allCategories);
        $this->categories = array_slice($allCategories, $offset, $CATEGORIES_PER_PAGE);
        $this->categoryIdToRecentTopics = array();
        $topics = array();
        foreach ($this->categories as $category) {
            $this->categoryIdToRecentTopics[$category->id] = Category::recentTopics($category, $TOPICS_PER_CATEGORY);
            $topics = array_merge($topics, $this->categoryIdToRecentTopics[$category->id]);
        }
        // Pre-load profiles and User objects [Jon Aquino 2007-10-07]
        XG_Cache::profiles($topics, Topic::lastCommentContributorNames($topics));
        $this->showNextLink = ($this->totalCategories > $pageNum * $CATEGORIES_PER_PAGE);
	}

    /**
     * Paginated list of all categories.
     *
     * Optional $_GET variable: 'page' for which page of categories to view.  Defaults to 1.
     */
    public function action_listByTitle() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $CATEGORIES_PER_PAGE = 20;
        $pageNum = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        $this->numPerPage = $CATEGORIES_PER_PAGE;
        $offset = ($pageNum - 1) * $CATEGORIES_PER_PAGE;
        $allCategories = Category::find($offset, $CATEGORIES_PER_PAGE, array('my->order','asc',XN_Attribute::STRING));
        $this->totalCategories = $allCategories['totalCount'];
        $this->categorySet = $allCategories['objects'];
        $topicsToQuery = array();
        $this->topics = array();
        $categoryIds = array();
        $alternativeIdToCategoryIdMap = array();
        foreach ($this->categorySet as $category) {
            // BAZ-7996 fix [ywh 2008-06-06]
            if (! mb_strlen($category->my->discussionCount) && XG_Cache::lock(md5($category->id))) {
                // XG_CacheHelper::lock is non-blocking; we prevent multiple concurrent executions of this
                // however the result is that some users may still see the bad data whereas the user
                // that triggered the category initialization will not.  since the user experience is no
                // worse than not having run this update, i think it's okay.  on subsequent page loads all
                // users should see the correct data. [ywh 2008-06-06]
                $category = Category::updateDiscussionCountAndActivity($category, null, true);
            }
            if (mb_strlen($category->my->latestDiscussionActivity)) {
                $timestampsAndTopics = explode(',', $category->my->latestDiscussionActivity);
                $topic = explode(' ', $timestampsAndTopics[0]);
                $topicsToQuery[] = $topic[1];
            }
            $categoryIds[$category->id] = $category->id;
            if (mb_strlen($category->my->alternativeIds)) {
                foreach(explode(' ', $category->my->alternativeIds) as $alternativeID) {
                    $alternativeIdToCategoryIdMap[$alternativeID] = $category->id;
                }
            }
        }
        if ($topicsToQuery) {
            $query = XN_Query::create('Content');
            $query->filter('owner');
            $query->filter('type', '=', 'Topic');
            XG_GroupHelper::addGroupFilter($query);
            $query->filter('id', 'in', $topicsToQuery);
            foreach ($query->execute() as $topic) {
                if (is_null($topic->my->categoryId)) {
                    $topic->my->categoryId = 'null';
                }
                if (! $categoryIds[$topic->my->categoryId]) {
                    $this->topics[$alternativeIdToCategoryIdMap[$topic->my->categoryId]] = $topic;
                } else {
                    $this->topics[$topic->my->categoryId] = $topic;
                }
            }
        }
        if ($this->_widget->config['hideFeaturedSection'] != 'Y') {
            $featured = Topic::getFeaturedTopics();
            if ($featured['totalCount'] > 0) {
                $this->usingCategories = true;
                $this->categories = Category::titlesAndIds(true);
                $this->featuredTopics = $featured['items'];
                $this->showFeaturedViewAll = $featured['totalCount'] > count($this->featuredTopics);
            }
        }
        $this->feedUrl = XG_HttpHelper::addParameters($this->_buildUrl('topic', 'list'), array('feed' => 'yes', 'xn_auth' => 'no'));
        $this->feedUrl = XG_HttpHelper::addParameters($this->feedUrl, array('sort' => 'mostRecent'));
        $this->pageViewOptions = Forum_HtmlHelper::getPageViewOptions('category');
        $this->showFeedLink = ! XG_App::appIsPrivate() && ! XG_GroupHelper::groupIsPrivate();
        $this->showFollowLink = Forum_SecurityHelper::currentUserCanFollowNewTopics();
    }
	public function action_listByTitle_iphone() {
		$this->action_listByTitle();
    }

    /**
     * Paginated list of all categories.
     *
     * Optional $_GET variable: 'page' for which page of categories to view.  Defaults to 1.
     */
    public function action_listCategories() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $CATEGORIES_PER_PAGE = 20;
        $pageNum = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        $this->numPerPage = $CATEGORIES_PER_PAGE;
        $offset = ($pageNum - 1) * $CATEGORIES_PER_PAGE;
        $allCategories = Category::find($pageNum - 1, $CATEGORIES_PER_PAGE);
        $this->totalCategories = $allCategories['totalCount'];
        $this->categories = $allCategories['objects'];
    }

}
