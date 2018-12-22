<?php

/**
 * Dispatches requests pertaining to discussion topics.
 */
class Forum_TopicController extends XG_GroupEnabledController {

    /**
     * Pokes a hole in the app-wide privacy mechanism.
     *
     * @param $action string  The name of the action
     * @return boolean  Whether to bypass the privacy mechanism for the given action
     */
    public function action_overridePrivacy($action) {
        return ! XG_App::appIsPrivate() && ! XG_GroupHelper::groupIsPrivate() && $_GET['feed'] == 'yes' && in_array($action, array('show', 'search', 'list', 'listForCategory', 'listForTag', 'listForContributor'));
    }

    /**
     * Displays the form for a new discussion topic.
     *
     * Expected GET variables:
     *     categoryId - ID of the initial category (optional)
     *     target - URL for the cancel button (optional)
     */
    public function action_new($errors = NULL) {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        if (! Forum_SecurityHelper::currentUserCanAddTopic()) { throw new Exception('Cannot add topics (1534327525)'); }
        $this->errors = $errors;
        $this->categories = Category::findAll(XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin());
        $this->form = new XNC_Form(array('categoryId' => $_REQUEST['categoryId'], 'title'=>$_REQUEST['title'], 'description'=>$_REQUEST['description']));
        $this->title = xg_text('ADD_A_DISCUSSION');
        $this->buttonText = xg_text('ADD_DISCUSSION');
        $this->formUrl = $this->_buildUrl('topic', 'create', array('target' => $_GET['target']));
		$this->cancelUrl = $_GET['target'] ? $_GET['target'] : $this->_buildUrl('index','index');
        $this->emptyAttachmentSlotCount = Topic::emptyAttachmentSlotCount(Topic::create());
		$this->editingExistingTopic = false;
        $this->render(XG_Browser::current()->template('newOrEdit'));
    }
    public function action_new_iphone($errors = NULL) {
        $this->action_new();
    }

    // handler for the "quick post" feature
    public function action_createQuick () { # void
        $this->action_create();
        $this->render('blank');
        if ($this->_topic) { // _topic is set if topic was successfully created
            $this->status = 'ok';
            $this->viewUrl = $this->_buildUrl('topic', 'show', array('id' => $this->_topic->id));
            $this->viewText = xg_html('VIEW_THIS_DISCUSSION');
            $this->message = xg_html('YOUR_DISCUSSION_WAS_ADDED');
            unset($this->_topic);
        } else {
            $this->status = 'fail';
            $this->message = xg_html('CANNOT_ADD_YOUR_DISCUSSION');
        }
    }

    /**
     * Processes the form for a new discussion topic.
     */
    public function action_create() {
        // Used from action_createQuick()
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('new', 'topic'); return; }
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        if (! Forum_SecurityHelper::currentUserCanAddTopic()) { throw new Exception('Cannot add topics'); }
        $this->processForm($topic = Topic::create(), 'new');
        // BAZ-4128: Subscribe the creator to the notification alias unless he
        //   has opted out of notifications for comments on his content
        $user = User::load(XN_Profile::current());
        if ($user && $user->my->emailActivityPref !== 'none') {
            Index_NotificationHelper::startFollowing($topic);
        }
        Category::invalidateRecentTopicsCache(Category::findById($topic->my->categoryId));
        $this->_widget->includeFileOnce('/lib/helpers/Forum_NotificationHelper.php');
        XG_Browser::execInEmailContext('Forum_NotificationHelper::notifyNewTopicFollowers',$topic);
        if (XG_GroupHelper::inGroupContext()) {
            W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_MessagingHelper.php');
            Groups_MessagingHelper::notifyNewActivityFollowers($topic, XG_GroupHelper::currentGroup());
        }
    }
    public function action_create_iphone() {
        $this->action_create();
    }

    /**
     * Sets the tags for the given object for the current user.
     *
     * Expected GET parameters:
     *     xn_out  Should always be "json"
     */
    public function action_tag() {
        $x = $this->_buildPath('lib/helpers/Forum_SecurityHelper.php');  include_once $x;
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $x = $this->_buildPath('lib/helpers/Forum_HtmlHelper.php');  include_once $x;
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (498350803)'); }
        XG_HttpHelper::trimGetAndPostValues();
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        if (! Forum_SecurityHelper::currentUserCanTag($topic)) { throw new Exception('Not allowed (1928669282)'); }
        if ($topic->type != 'Topic') { throw new Exception('Not a Topic'); }
        XG_TagHelper::updateTagsAndSave($topic, $_POST['tags']);
        $this->html = Forum_HtmlHelper::tagHtmlForDetailPage(XG_TagHelper::getTagNamesForObject($topic));
    }

    /**
     * Displays the detail page for a discussion topic.
     *
     * Expected GET variables:
     *     id - ID of the Topic object
     *     page - page number (optional)
     */
    public function action_show() {
        // If the user clicked an 'unfollow' link, make sure he's logged in,
        //   otherwise we can't remove him from the follow list
        if ($_GET['unfollow']) {
            XG_SecurityHelper::redirectIfNotMember();
            $this->unFollow = true;
        }
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        try {
            $this->topic = W_Content::load($_GET['id']);
        } catch (Exception $e) {
            W_Cache::getWidget('main')->dispatch('error','404');
            exit;
        }
        XG_GroupHelper::checkCurrentUserCanAccess($this->topic);

        if ($this->topic->my->groupId && !XG_GroupHelper::inGroupContext()) {
            $group = Group::load($this->topic->my->groupId);
            $this->redirectTo(XG_GroupHelper::buildUrl('forum','topic','show',array('id'=>$this->topic->id), $group->my->url));
            return;
        }
        if (!XG_GroupHelper::inGroupContext() && $this->_widget->privateConfig['allCategoriesDeletedOn'] && strtotime($this->topic->createdDate) < strtotime($this->_widget->privateConfig['allCategoriesDeletedOn'])) {
            $this->render(XG_Browser::current()->template('showDeleted'));
            return;
        }
        if ($_GET['feed'] == 'yes') {
            header('Content-Type: application/atom+xml');
            XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
            XG_FeedHelper::cacheFeed(array('id' => 'forum-topic-show-' . md5(XG_HttpHelper::currentUrl())));
            XG_FeedHelper::outputFeed(Forum_CommentHelper::createQuery($this->topic->id, 0, 10, 'mostRecent')->execute(), $this->topic->title, NULL);
            exit;
        }
        // Was there an unfollow GET parameter from link in follow notification email?
        if ($this->unFollow) {
            Index_NotificationHelper::stopFollowing($this->topic);
        }
        $this->currentCommentId = $_GET['commentId'];
        // Not too many, otherwise avatar loading slows down page [Jon Aquino 2007-01-30]
        $this->pageSize = Forum_CommentHelper::COMMENTS_PER_PAGE;
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);

        $this->threadingModel = $this->_widget->config['threadingModel'];

        // default to flat unless explicitly set to threaded
        if ($this->threadingModel == 'threaded') {
            $query = Forum_CommentHelper::createQuery($this->topic->id, $begin, $begin + $this->pageSize
                    + 1); // Query has one extra result so we can determine if each comment has child comments [Jon Aquino 2007-04-04]
            $commentsPlusOne = $query->execute();
            $this->comments = array_slice($commentsPlusOne, 0, $this->pageSize);
            $this->commentIdsWithChildComments = Forum_CommentHelper::commentIdsWithChildComments($this->pageSize, $commentsPlusOne);
        } else {
            $query = Forum_CommentHelper::createQuery($this->topic->id, $begin, $begin + $this->pageSize, 'flat');
            $this->comments = $query->execute();
        }

        if ($this->currentCommentId && ! in_array($this->currentCommentId, Forum_CommentHelper::ids($this->comments))) {
            try {
                $comment = XG_Cache::content($this->currentCommentId);
            } catch (Exception $e) {
            }
            if (! $comment) {
                $this->redirectTo('showDeleted', 'comment', '?topicId=' . $this->topic->id);
                return;
            }
            $components = parse_url($url = Forum_CommentHelper::url($comment));
            parse_str($components['query'], $parameters);
            // Check page, just in case, to prevent infinite redirection loop [Jon Aquino 2007-04-05]
            if (XG_PaginationHelper::computeStart($parameters['page'], $this->pageSize) != XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize)) {
                header('Location: ' . $url);
                exit;
            }
        }

        if (!XG_GroupHelper::inGroupContext()) {
            $this->category = Category::findById($this->topic->my->categoryId);
            if ($this->category) { $this->categoryUrl = XG_GroupHelper::buildUrl($this->_widget->dir, 'topic', 'listForCategory', array('categoryId' => $this->category->id)); }
            if (count(Category::titlesAndIds()) && ! $this->category) {
                $this->render(XG_Browser::current()->template('showDeleted'));
                return;
            }
        }

        $this->tags = XG_TagHelper::getTagNamesForObject($this->topic);
        $this->feedUrl = XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('feed' => 'yes', 'xn_auth' => 'no'));
        $this->forumFeedUrl = XG_HttpHelper::addParameters($this->_buildUrl('index','index'), array('sort' => 'mostRecent', 'feed' => 'yes', 'xn_auth' => 'no'));
        $this->feedDescription = xg_text('SUBSCRIBE_TO_DISCUSSION');
        $this->totalCount = $query->getTotalCount();
        if (count($this->comments) == 0 && $_GET['page'] > 0) {
            // Deleted the last comment on the current page [Jon Aquino 2007-01-25]
            $this->redirectTo('show', 'topic', array('id' => $this->topic->id));
            return;
        }
        XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
        $this->metaDescription = $this->topic->description;
        $this->metaKeywords = false;
        if (sizeof($this->tags)) {
            $metaKeywords =  array();
            foreach($this->tags as $tag) {
                $metaKeywords[] = xnhtmlentities($tag);
            }
            $this->metaKeywords = implode(', ',$metaKeywords);
        }
        $this->showDiscussionClosedModule = $_GET['repliedToClosedDiscussion'];
        // get tags for user if they're admin/NC or contributor
        if (XG_SecurityHelper::userIsAdminOrContributor($this->_user, $this->topic)) {
            $this->currentUserTagString = XG_TagHelper::implode(XG_TagHelper::getTagNamesForObjectAndUser($this->topic, $this->_user->screenName));
        }

        $categories = Category::findAll(XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin());
        if (Forum_SecurityHelper::currentUserCanSetCategory($this->topic) && count($categories) > 1) {
            $this->categoryPickerOptionsJson = $this->categoryPickerOptionsJson($categories, $this->category);
        }
        XG_Cache::profiles($this->topic, $this->comments);
        if($_GET['output']=='items'){
            $this->render(XG_Browser::current()->template('fragment_comments'));
            return;
        }
        $this->render(XG_Browser::current()->template('show'));
    }

    /**
     * Displays the detail page for a discussion topic. (iPhone-specific)
     *
     * Expected GET variables:
     *     id - ID of the Topic object
     *     page - page number (optional)
     */
    public function action_show_iphone() {
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        try {
            $this->topic = W_Content::load($_GET['id']);
        } catch (Exception $e) {
            W_Cache::getWidget('main')->dispatch('error','404');
            exit;
        }
        if (!XG_GroupHelper::inGroupContext() && $this->_widget->privateConfig['allCategoriesDeletedOn'] && strtotime($this->topic->createdDate) < strtotime($this->_widget->privateConfig['allCategoriesDeletedOn'])) {
            $this->render('showDeleted');
            return;
        }
        // Not too many, otherwise avatar loading slows down page [Jon Aquino 2007-01-30]
        $this->pageSize = Forum_CommentHelper::COMMENTS_PER_PAGE;
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);

        $this->threadingModel = $this->_widget->config['threadingModel'];
        // default to flat unless explicitly set to threaded
        if ($this->threadingModel == 'threaded') {
            $query = Forum_CommentHelper::createQuery($this->topic->id, $begin, $begin + $this->pageSize
                    + 1); // Query has one extra result so we can determine if each comment has child comments [Jon Aquino 2007-04-04]
            $commentsPlusOne = $query->execute();
            $this->comments = array_slice($commentsPlusOne, 0, $this->pageSize);
            $this->commentIdsWithChildComments = Forum_CommentHelper::commentIdsWithChildComments($this->pageSize, $commentsPlusOne);
        } else {
            $query = Forum_CommentHelper::createQuery($this->topic->id, $begin, $begin + $this->pageSize, 'flat');
            $this->comments = $query->execute();
        }

        if (!XG_GroupHelper::inGroupContext()) {
            $this->category = Category::findById($this->topic->my->categoryId);
            if ($this->category) { $this->categoryUrl = XG_GroupHelper::buildUrl($this->_widget->dir, 'topic', 'listForCategory', array('categoryId' => $this->category->id)); }
            if (count(Category::titlesAndIds()) && ! $this->category) {
                $this->render('showDeleted');
                return;
            }
        }

        $this->tags = XG_TagHelper::getTagNamesForObject($this->topic);
        $this->totalCount = $query->getTotalCount();
        if (count($this->comments) == 0 && $_GET['page'] > 0) {
            // Deleted the last comment on the current page [Jon Aquino 2007-01-25]
            $this->redirectTo('show', 'topic', array('id' => $this->topic->id));
            return;
        }
        XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
        $this->metaDescription = $this->topic->description;
        $this->metaKeywords = false;
        if (sizeof($this->tags)) {
            $metaKeywords =  array();
            foreach($this->tags as $tag) {
                $metaKeywords[] = xnhtmlentities($tag);
            }
            $this->metaKeywords = implode(', ',$metaKeywords);
        }
        XG_Cache::profiles($this->topic, $this->comments);
        $this->showNextLink = ($this->totalCount > $this->pageSize * ($_GET['page'] ? $_GET['page'] : 1));
    }

    /**
     * Displays an error page saying that the discussion has been deleted.
     *
     * @param $showNavigation boolean  whether to show the navigation links
     */
    public function action_showDeleted($showNavigation = true) {
        $this->showNavigation = $showNavigation;
    }

    /**
     * Redirects to the page showing the last reply for the given topic
     *
     * Expected GET variables:
     *     id - ID of the Topic object
     */
    public function action_showLastReply() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        $results = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Comment')->filter('my->attachedTo', '=', $_GET['id'])->order('createdDate', 'desc', XN_Attribute::DATE)->end(1)->execute();
        $url = $results[0] ? Forum_CommentHelper::urlProper($results[0]) : $this->_buildUrl('topic', 'show', array('id' => $_GET['id']));
        header('Location: ' . $url);
    }
    public function action_showLastReply_iphone() {
		$this->action_showLastReply();
	}

    /**
     * Displays the form for editing a discussion topic.
     *
     * Expected GET variables:
     *     id - ID of the Topic to edit
     */
    public function action_edit($errors = NULL) {
        XG_SecurityHelper::redirectIfNotMember();
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        if ($topic->type != 'Topic') { throw new Exception('Not a Topic'); }
        if (! Forum_SecurityHelper::currentUserCanEditTopic($topic)) { throw new Exception('Cannot edit topic (1277898673)'); }
        $this->errors = $errors;
        $tags = XG_TagHelper::getTagsForObjectAndUser($topic->id, $this->_user->screenName);
        $tagString = XG_TagHelper::implode(XN_Tag::tagNamesFromTags($tags));
        $this->categories = Category::findAll(XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin());
        $category = Category::findById($topic->my->categoryId);
        $this->form = new XNC_Form(array('title' => $topic->title, 'description' => $topic->description, 'tags' => $tagString, 'categoryId' => $category ? $category->id : null));
        $this->title = xg_text('EDIT_DISCUSSION');
        $this->buttonText = xg_text('SAVE');
        $this->formUrl = $this->_buildUrl('topic', 'update', array('id' => $topic->id));
        $this->cancelUrl = $this->_buildUrl('topic', 'show', array('id' => $topic->id));
        $this->emptyAttachmentSlotCount = Topic::emptyAttachmentSlotCount($topic);
		$this->editingExistingTopic = true;
        $this->render('newOrEdit');
    }

    /**
     * Processes the form for editing a discussion topic.
     *
     * Expected GET variables:
     *     id - ID of the Topic to edit
     */
    public function action_update() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('edit', 'topic', array('id' => $topic->id)); return; }
        if ($topic->type != 'Topic') { throw new Exception('Not a Topic'); }
        if (! Forum_SecurityHelper::currentUserCanEditTopic($topic)) { throw new Exception('Cannot edit topic (502036165)'); }
        $this->processForm($topic, 'edit');
    }

    /**
     * Updates the given Topic object using the posted form variables, then
     * forwards or redirects to an appropriate page.
     * Redirects to the sign-up page if the person is signed out.
     *
     * @param $topic W_Content  The Topic to update
     * @param $actionOnError string  Action to forward to if an error occurs
     */
    private function processForm(W_Content $topic, $actionOnError) {
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_UserHelper.php');
        if ($_POST['categoryId'] && ! Forum_SecurityHelper::currentUserCanAddTopicToCategory(Category::findById($_POST['categoryId']))) { xg_echo_and_throw('Not allowed (1650948551)'); }
        XG_HttpHelper::trimGetAndPostValues();
        $topic->title = Topic::cleanTitle($_POST['title']);
        $topic->description = Topic::cleanDescription($_POST['description']);
        $errors = array();
        if (! $topic->title) { $errors['title'] = xg_text('PLEASE_ENTER_TITLE'); }
        if (! $topic->description) { $errors['description'] = xg_text('PLEASE_ENTER_FIRST_POST'); }
        for ($i = 1; $i <= Topic::emptyAttachmentSlotCount($topic); $i++) {
            if ($_POST["file$i"] && $_POST["file$i:status"]) { $errors["file$i"] = XG_FileHelper::uploadErrorMessage($_POST["file$i:status"]); }
        }
        if (count($errors)) {
            $this->forwardTo($actionOnError, 'topic', array($errors));
            return;
        }
        if ($_POST['file1']) { Forum_FileHelper::addAttachment('file1', $topic); }
        if ($_POST['file2']) { Forum_FileHelper::addAttachment('file2', $topic); }
        if ($_POST['file3']) { Forum_FileHelper::addAttachment('file3', $topic); }

        if ($_POST['categoryId'] && $actionOnError == "edit") {
            $oldCategory = $topic->my->categoryId;
        }
        Category::setCategoryId($topic, $_POST['categoryId'] ? $_POST['categoryId'] : null);

        if (!$topic->my->lastEntryDate) {
            $topic->my->set('lastEntryDate', date('c'), XN_Attribute::DATE);
        }
        if ($_POST['featureOnMain']) {
            XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
            if (XG_PromotionHelper::currentUserCanPromote($topic)) {
                XG_PromotionHelper::promote($topic);
            }
        }
        XG_TagHelper::updateTagsAndSave($topic, $_POST['tags']);
        if ($topic->my->categoryId || $oldCategory) {
            $category = Category::updateDiscussionCountAndActivity($topic->my->categoryId, null, true);
            if ($oldCategory && $oldCategory != $category->id) {
                $oldCategory = Category::updateDiscussionCountAndActivity(is_null($oldCategory) ? 'null' : $oldCategory, null, true);
            }
        }
        Forum_UserHelper::updateActivityCount(User::load($this->_user));
        if($actionOnError == 'new'){
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            if ($topic->my->groupId) {
                XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_GROUP_TOPIC, $topic->contributorName, array($topic,XG_GroupHelper::currentGroup()));
            } else {
                XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_TOPIC, $topic->contributorName, array($topic));
            }
        }
        if (XG_GroupHelper::inGroupContext()) {
            $group = XG_GroupHelper::currentGroup();
            Group::updateActivityScore($group,GROUP::ACTIVITY_SCORE_FORUM_TOPIC);
            $group->save();
        }
        $this->_topic = $topic;  // save topic for reuse
        $this->redirectTo('show', 'topic', array('id' => $topic->id));
    }

    /**
     * Displays a list of recent discussion topics.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecentDiscussions, mostPopularDiscussions, or mostRecentlyUpdatedDiscussions
     */
    public function action_list() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->showSidebar = ! XG_GroupHelper::inGroupContext();
        self::prepareListAction(array(
                'titleHtml' => XG_GroupHelper::inGroupContext() ? xg_text('GROUP_FORUM', XG_GroupHelper::currentGroup()->title) : xg_text('DISCUSSION_FORUM'),
                'templateForNoDiscussions' => 'listEmpty',
                'sortNames' => array('mostRecentlyUpdatedDiscussions', 'mostRecentDiscussions', 'mostPopularDiscussions')));
        $this->showListForContributorLinks = FALSE;
        $this->showContributorName = TRUE;
        $this->feedDescription = xg_text('SUBSCRIBE_TO_DISCUSSIONS');
        $this->userCanSeeAddTopicLinks = Forum_SecurityHelper::currentUserCanSeeAddTopicLinks();
        $this->noDiscussionsHtml = Forum_SecurityHelper::currentUserCanSeeAddTopicLinks() ? xg_html('NOBODY_HAS_ADDED_DISCUSSIONS_ADD') : xg_html('NOBODY_HAS_ADDED_DISCUSSIONS');
        $this->showFollowLink = Forum_SecurityHelper::currentUserCanFollowNewTopics();
        self::prepareCategories();
        if ($this->_widget->config['hideFeaturedSection'] != 'Y') {
            $featured = Topic::getFeaturedTopics();
            if ($featured['totalCount'] > 0) {
                $this->featuredTopics = $featured['items'];
                $this->showFeaturedViewAll = $featured['totalCount'] > count($this->featuredTopics);
            }
        }
        if($_GET['output']=='items'){
            $this->render(XG_Browser::current()->template('fragment_items'));
            return;
        }
        $this->render(XG_Browser::current()->template('list'));
    }
    //
    public function action_list_iphone() { # void
        $this->action_list();
    }


    /**
     * Displays a list of featured discussion topics ordered latest featured -> first shown.
     *
     * Expected GET variables:
     *     page - page number (optional)
     */
    public function action_featured() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->showSidebar = ! XG_GroupHelper::inGroupContext();
        self::prepareCategories();
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->pageSize = 10;
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $featured = Topic::getFeaturedTopics(null, $begin, $begin + $this->pageSize);
        if ($featured['totalCount'] > 0) {
            $this->featuredTopics = $featured['items'];
            $this->showFeaturedViewAll = false;
            $this->totalCount = $featured['totalCount'];
            $this->titleText = $this->titleHtml = xg_text('FEATURED_DISCUSSIONS');
        } else {
            // send them to the main forum page if there aren't any featured topics.
            $this->redirectTo('index','index');
        }
    }

    /**
     * Displays a list of recent discussion topics in a given category.
     *
     * Expected GET variables:
     *     categoryId - the ID of the Category
     *     page - page number (optional)
     *     sort - mostRecentDiscussions, mostPopularDiscussions, or mostRecentlyUpdatedDiscussions
     */
    public function action_listForCategory() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $_GET['categoryId'] = urldecode($_GET['categoryId']); // Workaround for BAZ-4133 [Jon Aquino 2007-09-27]
        $this->category = Category::findById($_GET['categoryId']);
        if (! $this->category) {
            W_Cache::getWidget('main')->dispatch('error','404');
            exit;
        }
        self::prepareListAction(array(
                'titleHtml' => $this->category->title,
                'templateForNoDiscussions' => 'listEmpty',
                'sortNames' => array('mostRecentlyUpdatedDiscussions', 'mostRecentDiscussions', 'mostPopularDiscussions'),
                'category' => $this->category));
        $this->showListForContributorLinks = FALSE;
        $this->showContributorName = TRUE;
        $this->feedDescription = xg_text('SUBSCRIBE_TO_DISCUSSIONS');
        $this->noDiscussionsHtml = Forum_SecurityHelper::currentUserCanSeeAddTopicLinks() ? xg_html('NOBODY_HAS_ADDED_DISCUSSIONS_ADD') : xg_html('NOBODY_HAS_ADDED_DISCUSSIONS');
        if ($this->category->description && $_GET['page'] < 2) {
            $this->description = $this->category->description;
        }
        $this->skipTitleCount = true;
        if ($this->_widget->config['hideFeaturedSection'] != 'Y') {
            $featured = Topic::getFeaturedTopics($this->category->id);
            if ($featured['totalCount'] > 0) {
                $this->featuredTopics = $featured['items'];
                $this->showFeaturedViewAll = $featured['totalCount'] > count($this->featuredTopics);
            }
        }
        if($_GET['output']=='items'){
            $this->render(XG_Browser::current()->template('fragment_items'));
            return;
        }
    }
    public function action_listForCategory_iphone() {
        $_GET['pageSize'] = ($_GET['pageSize'] ? $_GET['pageSize'] : 10);
        $this->action_listForCategory();
        $this->showNextLink = ($this->totalCount > $this->pageSize * ($_GET['page'] ? $_GET['page'] : 1));
    }

    /**
     * Displays a list of recent discussion topics with the given search keywords.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecent or mostPopularDiscussions
     *     q - search keywords (optional)
     *
     */
    public function action_search() {
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        self::prepareListAction(array(
                'titleHtml' => xg_text('SEARCH_RESULTS'),
                'user' => $_GET['user'] ? $_GET['user'] : null,
                'useSearch' => (XG_QueryHelper::getSearchMethod() != 'content'),
                'templateForNoDiscussions' => 'listEmptyForSearch',
                'sortNames' => array('mostRecentlyUpdatedDiscussions', 'mostRecentDiscussions', 'mostPopularDiscussions')));
        $this->showListForContributorLinks = FALSE;
        $this->showContributorName = TRUE;
        $this->feedDescription = xg_text('SUBSCRIBE_TO_DISCUSSIONS');
        $this->noDiscussionsHtml = xg_html('WE_COULD_NOT_FIND_ANY_DISCUSSIONS');
        $this->hideSorts = true;
        $this->searchResults = true;
        $this->showingReplies = true;
        self::prepareCategories();
    }

    /**
     * Displays a list of recent discussion topics with a given tag
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecentDiscussions, mostPopularDiscussions, or mostRecentlyUpdatedDiscussions
     *     tag - the tag
     */
    public function action_listForTag() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        self::prepareListAction(array(
                'titleHtml' => xg_text('ALL_DISCUSSIONS_TAGGED_X', $_GET['tag']),
                'templateForNoDiscussions' => 'listEmpty',
                'sortNames' => array('mostRecentlyUpdatedDiscussions', 'mostRecentDiscussions', 'mostPopularDiscussions')));
        $this->showListForContributorLinks = FALSE;
        $this->showContributorName = TRUE;
        $this->feedDescription = xg_text('SUBSCRIBE_TO_DISCUSSIONS_TAGGED_X', $_GET['tag']);
        $this->noDiscussionsHtml = Forum_SecurityHelper::currentUserCanSeeAddTopicLinks()
            ? xg_html('NO_DISCUSSIONS_TAGGED_X_CHECK_ADD', xnhtmlentities($_GET['tag']), 'href="' . xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('photo', 'listTagged', array('tag' => $_GET['tag']))) . '"', 'href="' . xnhtmlentities(W_Cache::getWidget('video')->buildUrl('video', 'listTagged', array('tag' => $_GET['tag']))) . '"', 'href="' . xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('blog', 'list', array('tag' => $_GET['tag']))) . '"')
            : xg_html('NO_DISCUSSIONS_TAGGED_X_CHECK', xnhtmlentities($_GET['tag']), 'href="' . xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('photo', 'listTagged', array('tag' => $_GET['tag']))) . '"', 'href="' . xnhtmlentities(W_Cache::getWidget('video')->buildUrl('video', 'listTagged', array('tag' => $_GET['tag']))) . '"', 'href="' . xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('blog', 'list', array('tag' => $_GET['tag']))) . '"');
        $this->showNoDiscussionsHeading = FALSE;
        if($_GET['output']=='items'){
            $this->render(XG_Browser::current()->template('fragment_items'));
            return;
        }
    }
    public function action_listForTag_iphone() {
        $this->action_listForTag();
        $this->tag = $_GET['tag'];
    }

    /**
     * Displays a list of recent discussion topics started by a given person.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecent or mostPopularDiscussions
     *     user - screen name of the person who started the topics
     * 	   pageSize - number of results to return per page
     */
	public function action_listForContributor($args=null) {
     $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
     $this->_widget->includeFileOnce('/lib/helpers/Forum_Filter.php');
     $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
     if (! $_GET['user']) {
         XG_SecurityHelper::redirectIfNotMember();
         // Redirect; otherwise Bloglines bookmarklet will hit sign-in page when looking for RSS autodiscovery elements  [Jon Aquino 2007-01-19]
         $this->redirectTo('listForContributor', 'topic', array('user' => XN_Profile::current()->screenName));
         return;
     }
     $fullName = xg_username(XG_Cache::profiles($_GET['user']));
     $this->contributor = XG_Cache::profiles($_GET['user']);
     $sortNames = array('recentRepliesMade','discussionsStarted');
     self::prepareListAction(array(
             'sortNames' => $sortNames,
             'overrideTemplate' => true,
             'user' => $_GET['user']));

     // if we're looking at replies as the default page, and the user hasn't made any, swap to discussions
     if (!isset($GET['feed']) && !isset($_GET['sort']) && $this->currentSortName == "recentRepliesMade" && count($this->topicsAndComments) == 0) {
         $_GET['sort'] = "discussionsStarted";
         self::prepareListAction(array(
                 'sortNames' => $sortNames,
                 'overrideTemplate' => true,
                 'user' => $_GET['user']));
     }

     $this->sort = $_GET['sort'] ? $_GET['sort'] : $sortNames[0];
     self::prepareCategories();
     $this->showingReplies = $this->sort == 'recentRepliesMade' ? true : false;
     if ($_GET['user'] == XN_Profile::current()->screenName) {
         $this->myDiscussions = true;
         $this->pageTitle = xg_text('MY_DISCUSSIONS');
		 $this->titleHtml = xg_text('MY_DISCUSSIONS');
         $this->noDiscussionsHtml = xg_html('YOU_HAVE_NOT_STARTED_DISCUSSIONS');
         if ($this->showingReplies) {
             $this->noDiscussionsHtml = xg_html('YOU_HAVE_NOT_MADE_ANY_REPLIES');
         }
         $this->searchButtonText = xg_html('SEARCH_FORUM');
         if ($this->showingReplies) {
             $this->noDiscussionsHtml = xg_html('YOU_HAVE_NOT_MADE_ANY_REPLIES');
         }
     } else {
         $this->myDiscussions = false;
         $contributorLink = xg_userlink(XG_Cache::profiles($_GET['user']));
         $this->titleHtml = $this->pageTitle = xg_text('XS_DISCUSSIONS2', $fullName);
         $this->noDiscussionsHtml = xg_html('X_HAS_NOT_STARTED_DISCUSSIONS', $contributorLink);
         if ($this->showingReplies) {
             $this->noDiscussionsHtml = xg_html('X_HAS_NOT_MADE_ANY_REPLIES', $contributorLink);
         }
         $this->searchButtonText = xg_html('SEARCH_FORUM', xnhtmlentities($fullName));
     }
     $this->user = User::load($_GET['user']);
     $this->totalDiscussionCount = $this->user->my->{XG_App::widgetAttributeName(W_Cache::getWidget('forum'), 'activityCount')};
     $this->subTitle = $this->showingReplies ? xg_html('DISCUSSIONS_REPLIED_TO_COUNT', $this->totalCount) : xg_html('DISCUSSIONS_STARTED_COUNT', $this->totalCount);
     $this->feedDescription = xg_text('SUBSCRIBE_TO_XS_DISCUSSIONS', $fullName);
     $this->userCanSeeAddTopicLinks = Forum_SecurityHelper::currentUserCanSeeAddTopicLinks();
     $this->showFeedLink = count($this->topicsAndComments) > 0 ? true : false;
     if ($args['output'] == 'embed') {
         $this->render(XG_Browser::current()->template('fragment_embeddableList'));
         return;
     }
     if($_GET['output']=='items'){
         $this->render(XG_Browser::current()->template('fragment_items'));
         return;
     }
     $this->render(XG_Browser::current()->template('listForContributor'));
}

    /*
     * Displays a list of recent forum posts for a given user (paginated, iPhone-specific)
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecent or mostPopularDiscussions
     *     user - screen name of the person who started the topics
     * 	   pageSize - number of results to return per page
     */
    public function action_listForContributor_iphone($args=null) {
        $_GET['pageSize'] = ($_GET['pageSize'] ? $_GET['pageSize'] : 10);
        $this->action_listForContributor($args);
        $this->showNextLink = ($this->totalCount > $this->pageSize * ($_GET['page'] ? $_GET['page'] : 1));
    }

    /**
     * Prepares the fields for an action that lists topics, and sets the template to render. Sets $this->title, $this->pageSize,
     * $this->topicsAndComments, $this->totalCount, $this->feedUrl, and $this->sortOptions.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - name of the current sort (optional), e.g., mostRecent
     *     feed - "yes" to output a feed (optional)
     *     tag - tag name to filter on (optional)
     *     q - search keywords (optional)
     * 	   pageSize - number of results to return per page
     *
     * @param $titleHtml string  HTML for the page heading
     * @param $templateForNoDiscussions string  Name of the template to use if there are no Topics or Comments
     * @param $sortNames array  Names of sorts to display in the combobox
     * @param $user string  (optional) Screen name of the person who started the topics
     * @param $category XN_Content|W_Content  (optional) Category to filter on
     * @param $useSearch boolean Whether to use a Search query (or not, use a Content query)
     * @param $overrideTemplate boolean True if the request should render the normal template rather than list
     */
    private function prepareListAction($args) {
        extract($args);
        XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_Filter.php');
        $useSearch = isset($useSearch) ? $useSearch : false;
        $this->titleHtml = $titleHtml;
        $this->titleText = strip_tags(html_entity_decode($titleHtml, ENT_QUOTES, 'UTF-8'));
        $this->pageSize = $_GET['pageSize'] ? $_GET['pageSize'] : 10;
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $this->currentSortName = $_GET['sort'] ? $_GET['sort'] : $sortNames[0];

        /* If we're generating a feed, set the content type header up here before
         * we (possibly) get out early due to caching */
        if ($_GET['feed'] == 'yes') {
            header('Content-Type: application/atom+xml');
            $begin = 0;
        }

        if ($useSearch) {
            $query = XN_Query::create('Search');
        }
        else {
            // Don't cache tag queries as there are an infinite number of tags but a finite number of cache files [Jon Aquino 2007-02-09]
            $query = ($_GET['tag'] || $_GET['q'] || (! XG_Cache::cacheOrderN())) ? XN_Query::create('Content') : XG_Query::create('Content');
            if ($_GET['tag']) { $query->filter('tag->value', 'eic', $_GET['tag']); }
            if ($category) { Category::addCategoryFilter($query, $category); }
            /* If we want a feed and there's no tag query or no term query, then cache the feed */
            if ($_GET['feed'] && (mb_strlen($_GET['tag']) == 0) && (mb_strlen($_GET['q']) == 0)) {
                XG_FeedHelper::cacheFeed(array('id' => 'forum-topic-list-' . md5(XG_HttpHelper::currentUrl())));
            }
        }
        $query->begin($begin);
        $query->end($begin + $this->pageSize);
        // filter to ensure that forum topics which have been 'deleted' via a deleted category aren't included [pcm 2008-06-03]
        if (!$useSearch) {
            Category::addDeletedCategoryFilter($query);
        }
        if ($_GET['q']) { XG_QueryHelper::addSearchFilter($query, $_GET['q'], $useSearch); }
        // No need to call addExcludeFromPublicSearchFilter, as checkCurrentUserCanAccessGroup handles authorization [Jon Aquino 2007-04-25]
        if ($useSearch) {
            try {
                $searchResults = Forum_Filter::get('search')->execute($query, $user);
                $this->topicsAndComments = XG_QueryHelper::contentFromSearchResults($searchResults, false);
                $this->totalCount = $query->getTotalCount();
                /* If we're on the first page and all of the search results have been excluded because the
                 * matching content doesn't exist any more, then just pretend there are no results */
                if (($this->totalCount > 0) && (count($this->topicsAndComments) == 0) && ($begin ==0)) {
                    $this->totalCount = 0;
                }
            } catch (Exception $e) {
                if ($e->getMessage() == "Date range searching currently unsupported (BAZ-2459)") {
                    /* If we can't do a search query because we needed a date range, then fall back to content query */
                    $newArgs = $args;
                    $newArgs['useSearch'] = false;
                    return $this->prepareListAction($newArgs);
                } else {
                    /* Otherwise, just pretend there's no results and log that info */
                    error_log("Forum Topic search query ({$_GET['q']}) failed with: " . $e->getCode());
                    $this->topicsAndComments = array();
                    $this->totalCount = 0;
                }
            }
        }
        else {
            $this->topicsAndComments = Forum_Filter::get($this->currentSortName)->execute($query, $user);
            $this->totalCount = $query->getTotalCount();
        }
		$this->showNextLink = $this->totalCount > ($begin + $this->pageSize); // iPhone hack: BAZ-9872 [Andrey 2008-09-14]
        $this->topics = Topic::topics($this->topicsAndComments);
        XG_Cache::profiles($this->topics, $this->topicsAndComments, $user, Topic::lastCommentContributorNames($this->topics));
        if ($_GET['feed'] == 'yes') {
            foreach ($this->topicsAndComments as $topicOrComment) {
                if ($topicOrComment->type == 'Comment') { $topicOrComment->title = $this->topics[$topicOrComment->my->attachedTo]->title; }
            }
            XG_FeedHelper::outputFeed($this->topicsAndComments, $this->titleText, $user ? XG_Cache::profiles($user) : NULL);
            exit;
        }
        $this->feedUrl = XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('feed' => 'yes', 'xn_auth' => 'no'));
        if ($this->currentSortName == 'mostRecentlyUpdatedDiscussions' && $category == null) {
            $this->feedUrlReplies = XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('sort' => 'mostRecent', 'feed' => 'yes', 'xn_auth' => 'no'));
        } else {
            $this->feedUrl = XG_HttpHelper::addParameters($this->feedUrl, array('sort' => 'mostRecent'));
        }
        $this->sortOptions = self::getSortOptions($sortNames, $this->currentSortName, $user);
        $this->showFeedLink = ! XG_App::appIsPrivate() && ! XG_GroupHelper::groupIsPrivate();
        if (!$overrideTemplate) {
            if (!$this->totalCount || $_GET['test_empty']) {
                $this->render(XG_Browser::current()->template($templateForNoDiscussions));
            } else {
                $this->render(XG_Browser::current()->template('list'));
            }
        }
    }

    /**
     * Returns list of sort options, for initializing the combobox.
     *
     * @param $sortNames array  Names of sorts to display in the combobox
     * @param $currentSortName string  Name of the current sort
     * @param $username string  Username that will be filtered on (optional)
	 * @return list
     */
    private function getSortOptions($sortNames, $currentSortName, $username) {
        $filterMetadata = array();
        foreach ($sortNames as $sortName) {
            $filterMetadata[] = array(
                    'displayText' => Forum_Filter::get($sortName)->getDisplayText($username),
                    'url' => XG_HttpHelper::addParameter(XG_HttpHelper::currentUrl(), 'sort', $sortName),
                    'selected' => $sortName == $currentSortName);
        }
        return $filterMetadata;
    }

    /**
     * Returns JSON for the given categories, for initializing the combobox.
     *
     * @param $categories array  The Category objects
     * @param $currentCategory XN_Content|W_Content  Category for the current Topic
     * @return string  JSON to pass to the CategoryPicker
     */
    private function categoryPickerOptionsJson($categories, $currentCategory) {
        $filterMetadata = array();
        foreach ($categories as $category) {
            $filterMetadata[] = array('displayText' => $category->title, 'id' => $category->id);
        }
        $json = new NF_JSON();
        return $json->encode($filterMetadata);
    }

    /**
     * Adds category data for results if categories are in use
	 * sets: $this->usingCategories, $this->pageViewOptions and $this->categories
     *
     * @param $categories array  The Category objects
     */
    private function prepareCategories() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $this->usingCategories = Category::usingCategories();
        if ($this->usingCategories) {
			$this->pageViewOptions = Forum_HtmlHelper::getPageViewOptions('topic');
            $this->categories = Category::titlesAndIds(true);
        }
    }

    /**
     * Updates the topic's category
     *
     * Expected GET variables:
     *     id - ID of the Topic to edit
     *
     * Expected POST variables:
     *     categoryId - ID of the new Category
     */
    public function action_setCategory() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        if ($topic->type != 'Topic') { throw new Exception('Not a Topic'); }
        if (! Forum_SecurityHelper::currentUserCanSetCategory($topic)) { throw new Exception('Not allowed'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        $category = Category::findById($_POST['categoryId']);
        $oldCategoryID = is_null($topic->my->categoryId) ? 'null' : $topic->my->categoryId;
        if (! $category) { throw new Exception('Category not found'); }
        Category::setCategoryId($topic, $category->id);
        $topic->save();
        Category::updateDiscussionCountAndActivity($category, null, true);
        Category::updateDiscussionCountAndActivity($oldCategoryID, null, true);
        $this->redirectTo('show', 'topic', array('id' => $topic->id));
    }

    /**
     * Ends commenting on the discussion thread.
     *
     * Expected GET variables:
     *     id - ID of the Topic for which to close comments
     *     target - URL to redirect to afterwards
     */
    public function action_closeComments() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        if ($topic->type != 'Topic') { throw new Exception('Not a Topic (2047548834)'); }
        if (! Forum_SecurityHelper::currentUserCanCloseComments($topic)) { throw new Exception('Not allowed (2123475459)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (630114808)'); }
        $topic->my->commentsClosed = 'Y';
        $topic->save();
        $this->redirectTo($_GET['target']);
    }

    /**
     * Re-opens commenting on the discussion thread.
     *
     * Expected GET variables:
     *     id - ID of the Topic for which to open comments
     *     target - URL to redirect to afterwards
     */
    public function action_openComments() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        $topic = XG_GroupHelper::checkCurrentUserCanAccess(W_Content::load($_GET['id']));
        if ($topic->type != 'Topic') { throw new Exception('Not a Topic (1576619427)'); }
        if (! Forum_SecurityHelper::currentUserCanOpenComments($topic)) { throw new Exception('Not allowed (1668656309)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (720688204)'); }
        $topic->my->commentsClosed = 'N';
        $topic->save();
        $this->redirectTo($_GET['target']);
    }

}
