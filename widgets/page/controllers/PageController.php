<?php

XG_App::includeFileOnce('/lib/XG_CommentHelper.php');

/**
 * Dispatches requests pertaining to discussion pages.
 */
class Page_PageController extends W_Controller {

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_FullNameHelper.php');
    }

    /**
     * Displays the form for a new page.
     */
    public function action_new($errors = NULL) {
        XG_SecurityHelper::redirectIfNotMember();
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $this->errors = $errors;
        $this->form = new XNC_Form();
    }

    /**
     * Displays the form for editing a page.
     */
    public function action_edit($errors = NULL) {
        XG_SecurityHelper::redirectIfNotMember();
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_CommentHelper.php');
        try {
            $this->page = W_Content::load($_GET['id']);
            // only contributor can edit (BAZ-7607) [ywh 2008-05-14]
            if (! XG_SecurityHelper::userIsContributor($this->_user, $this->page)) {
                $this->forwardTo('show');
            }
            // BAZ-2403 handle character entities for display [Phil McCluskey 2007-04-14]
            $this->page->description = str_replace('&amp;','&amp;amp;',$this->page->description);
            $this->page->description = str_replace('&lt;','&amp;lt;',$this->page->description);
            $this->page->description = str_replace('&gt;','&amp;gt;',$this->page->description);
            $this->page->description = str_replace('&quot;','&amp;quot;',$this->page->description);
            $this->page->description = str_replace('&apos;','&amp;apos;',$this->page->description);
        } catch (Exception $e) {
            W_Cache::getWidget('main')->dispatch('error','404');
            exit;
        }
        $this->errors = $errors;
    }

    /**
     * AJAX POST action that increments the view count of a specific page.
     */
    public function action_registershown() {
        try {
            $this->page = W_Content::load($_POST['id']);
            if ($this->page && (!$this->_user->isLoggedIn() || ($this->_user->screenName != $this->page->contributorName))) {
                $this->page->incrementViewCount();
                // BAZ-1507: Don't invalidate cache here, or the cache gets blown away on each detail view
                XG_App::setInvalidateFromHooks(false);
                $this->page->save();
                XG_App::setInvalidateFromHooks(true);
            }
        } catch (Exception $e) {
            // pass
        }
    }

    /**
     * Displays the detail page for a page.
     *
     * Expected GET variables:
     *     id - ID of the Page object
     *     page - page number (optional)
     */
    public function action_show() {
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_CommentHelper.php');
        try {
            $this->page = W_Content::load($_GET['id']);
        } catch (Exception $e) {
            W_Cache::getWidget('main')->dispatch('error','404');
            exit;
        }
        if ($_GET['feed'] == 'yes') {
            header('Content-Type: application/atom+xml');
            XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
            XG_FeedHelper::outputFeed(Page_CommentHelper::createQuery($this->page->id, 0, 20, 'mostRecent')->execute(), $this->page->title, NULL);
            exit;
        }
        $this->tags = XG_TagHelper::getTagNamesForObject($this->page);
        $this->feedUrl = XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('feed' => 'yes'));
        $this->feedDescription = xg_text('SUBSCRIBE_TO_DISCUSSION');
        $this->currentCommentId = $_GET['commentId'];
        // If we're not on page 1, do a normal form submit rather than an asynchronous one, so that we display the new comment on page 1. [Jon Aquino 2007-02-03]
        $this->forceNormalFormSubmission = $_GET['page'] > 1;
        // Not too many, otherwise avatar loading slows down page [Jon Aquino 2007-01-30]
        $this->pageSize = Page_CommentHelper::COMMENTS_PER_PAGE;
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $query = Page_CommentHelper::createQuery($this->page->id, $begin, $begin + $this->pageSize);
        $this->comments = $query->execute();
        $this->totalCount = $query->getTotalCount();
        $this->pageUrl = $this->_buildUrl('page', 'show', array('id' => $_GET['id'], 'page' => $_GET['page'] ? $_GET['page'] : 1));
        if (count($this->comments) == 0 && $_GET['page'] > 0) {
            // Deleted the last comment on the current page [Jon Aquino 2007-01-25]
            $this->redirectTo('show', 'page', array('id' => $this->page->id));
            return;
        }
        XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
        $this->metaDescription = $this->page->description;
        $this->metaKeywords = false;
        if (sizeof($this->tags)) {
            $metaKeywords =  array();
            foreach($this->tags as $tag) {
                $metaKeywords[] = xnhtmlentities($tag);
            }
            $this->metaKeywords = implode(', ',$metaKeywords);
        }

        XG_Cache::profiles($this->page, $this->comments);
    }

    /**
     * Processes the form for a new discussion page.
     */
    public function action_create() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_UserHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        XG_HttpHelper::trimGetAndPostValues();
        $errors = array();
        if (! $_POST['title']) { $errors['title'] = xg_text('PLEASE_ENTER_TITLE'); }
        if (! $_POST['description']) { $errors['description'] = xg_text('PLEASE_ENTER_PAGE_CONTENT'); }
        if ($_POST['file1'] && $_POST['file1:status']) { $errors['file1'] = XG_FileHelper::uploadErrorMessage($_POST['file1:status']); }
        if ($_POST['file2'] && $_POST['file2:status']) { $errors['file2'] = XG_FileHelper::uploadErrorMessage($_POST['file2:status']); }
        if ($_POST['file3'] && $_POST['file3:status']) { $errors['file3'] = XG_FileHelper::uploadErrorMessage($_POST['file3:status']); }
        if (count($errors)) {
            $this->forwardTo('new', 'page', array($errors));
            return;
        }
        $page = Page::create(page::cleanTitle($_POST['title']), page::cleanDescription($_POST['description']));
        if ($_POST['file1']) { Page_FileHelper::addAttachment('file1', $page); }
        if ($_POST['file2']) { Page_FileHelper::addAttachment('file2', $page); }
        if ($_POST['file3']) { Page_FileHelper::addAttachment('file3', $page); }
        if (isset($_POST['allowComments'])) {
            $page->my->allowComments = 'Y';
        } else {
            $page->my->allowComments = 'N';
        }
        XG_TagHelper::updateTagsAndSave($page, $_POST['tags']);
        Page_UserHelper::updateActivityCount(User::load($this->_user))->save();
        $this->redirectTo('show', 'page', array('id' => $page->id));
    }

    /**
     * Processes the form for editing a discussion page.
     *
     * Expected GET variables:
     *     id - ID of the page to edit
     */
    public function action_update() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_HtmlHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        XG_HttpHelper::trimGetAndPostValues();
        $page = W_Content::load($_GET['id']);
        if ($page->type != 'Page') { throw new Exception('Not a page'); }
        if (! XG_SecurityHelper::userIsContributor($this->_user, $page)) { throw new Exception('Not allowed'); }
        $page->title = page::cleanTitle($_POST['title']);
        $page->description = page::cleanDescription($_POST['description']);
        $page->my->searchable = strip_tags($page->description) . ' ' . $page->title;
        if (isset($_POST['allowComments'])) {
            $page->my->allowComments = 'Y';
        } else {
            $page->my->allowComments = 'N';
        }
        XG_TagHelper::updateTagsAndSave($page, $_POST['tags']);
        $this->redirectTo('show', 'page', array('id' => $page->id));
    }

    /**
     * Displays a list of recent discussion pages.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecent or mostPopular
     */
    public function action_list() {
        if (isset($_GET['q'])) {
            $this->q = $_GET['q'];
            self::prepareListAction(xg_text('SEARCH_RESULTS'), array('mostRecent', 'mostPopular'));
        } else {
            self::prepareListAction(xg_text('ALL_PAGES'), array('mostRecent', 'mostPopular'));
        }
        $this->showListForContributorLinks = TRUE;
        $this->showContributorName = TRUE;
        $this->feedDescription = xg_text('SUBSCRIBE_TO_PAGES');
        $this->showAddButtonIfNoDiscussions = TRUE;
        $this->noDiscussionsMessage = xg_text('NOBODY_HAS_ADDED_PAGES');
        $this->canCreatePage = Page_SecurityHelper::currentUserCanCreatePage();
        if ($this->canCreatePage) {
            $this->noDiscussionsMessage .= ' ' . xg_text('ADD_PAGES_CALL_TO_ACTION');
        }
		if (count($this->pages) == 1 && !$_GET['q']) {
            $pageID = array_keys($this->pages);
            $_GET['id'] = $pageID[0];
            $this->forwardTo('show');
        }
    }

    /**
     * Displays a list of recent discussion pages with a given tag
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecent or mostPopular
     *     tag - the tag
     */
    public function action_listForTag() {
        self::prepareListAction(xg_text('ALL_DISCUSSIONS_TAGGED_X', $_GET['tag']), array('mostRecent', 'mostPopular'));
        $this->showListForContributorLinks = TRUE;
        $this->showContributorName = TRUE;
        $this->feedDescription = xg_text('SUBSCRIBE_TO_DISCUSSIONS_TAGGED_X', $_GET['tag']);
        $this->showAddButtonIfNoDiscussions = TRUE;
        $this->noDiscussionsMessage = xg_text('NO_DISCUSSIONS_TAGGED_X', $_GET['tag']);
    }

    /**
     * Displays a list of recent discussion pages started by a given person.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - mostRecent or mostPopular
     *     user - screen name of the person who started the pages
     */
    public function action_listForContributor() {
        if (! $_GET['user']) {
            XG_SecurityHelper::redirectIfNotMember();
            // Redirect; otherwise Bloglines bookmarklet will hit sign-in page when looking for RSS autodiscovery elements  [Jon Aquino 2007-01-19]
            $this->redirectTo('listForContributor', 'page', array('user' => XN_Profile::current()->screenName));
            return;
        }
        $fullName = xg_username(XG_Cache::profiles($_GET['user']));
        self::prepareListAction($_GET['user'] == XN_Profile::current()->screenName ? xg_text('MY_PAGES') : xg_text('XS_PAGES', $fullName),
                array('mostRecent', 'mostPopular'));
        $this->showListForContributorLinks = FALSE;
        $this->showContributorName = FALSE;
        $this->feedDescription = xg_text('SUBSCRIBE_TO_XS_PAGES', $fullName);
        $this->canCreatePage = Page_SecurityHelper::currentUserCanCreatePage();
        if ($_GET['user'] == XN_Profile::current()->screenName) {
            $this->showAddButtonIfNoDiscussions = TRUE;
            $this->noDiscussionsMessage = xg_text('YOU_HAVE_NOT_ADDED_PAGES');
        } else {
            $this->showAddButtonIfNoDiscussions = FALSE;
            $this->noDiscussionsMessage = xg_text('X_HAS_NOT_ADDED_PAGES', $fullName);
        }
    }

    /**
     * Prepares the fields for an action that lists pages, and sets the template to render. Sets $this->title, $this->pageSize,
	 * $this->pagesAndComments, $this->totalCount, $this->feedUrl, and $this->pagePickerOptions.
     *
     * Expected GET variables:
     *     page - page number (optional)
     *     sort - name of the current sort (optional), e.g., mostRecent
     *     user - screen name of the person who started the pages (optional)
     *     feed - "yes" to output a feed (optional)
     *     tag - tag name to filter on (optional)
     *
     * @param $title string  Title of the page
     * @param $sortNames array  Names of sorts to display in the combobox
     */
    private function prepareListAction($title, $sortNames) {
        XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_Filter.php');
        if ($_GET['feed'] == 'yes') {
            header('Content-Type: application/atom+xml');
            // If a user is specified, comments will be included [Jon Aquino 2007-02-01]
            $query = XG_Cache::cacheOrderN() ? XG_Query::create('Content') : XN_Query::create('Content');
            $query->end(20);
            $pagesAndComments = Page_Filter::get('mostRecent')->execute($query, $_GET['user']);
            XG_Cache::profiles($pagesAndComments);
            XG_FeedHelper::outputFeed($pagesAndComments, $title, $_GET['user'] ? XG_Cache::profiles($_GET['user']) : NULL);
            exit;
        }
        $this->title = $title;
        $this->pageSize = 10;
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $currentSortName = $_GET['sort'] ? $_GET['sort'] : $sortNames[0];
        // Don't cache tag queries as there are an infinite number of tags but a finite number of cache files [Jon Aquino 2007-02-09]
        $query = ($_GET['tag'] || $_GET['q'] || (! XG_Cache::cacheOrderN())) ? XN_Query::create('Content') : XG_Query::create('Content');
        $query->begin($begin);
        $query->end($begin + $this->pageSize);
        if ($_GET['tag']) {
            $query->filter('tag->value', 'eic', $_GET['tag']);
        }
        if ($_GET['q']) {
            $query->filter('my->searchable','likeic',urldecode($_GET['q']));
        }
        $this->pagesAndComments = Page_Filter::get($currentSortName)->execute($query, $_GET['user']);
        $this->pages = page::pages($this->pagesAndComments);
        XG_Cache::profiles($this->pages, $this->pagesAndComments, $_GET['user']);
        $this->totalCount = $query->getTotalCount();
        $this->feedUrl = XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('sort' => 'mostRecent', 'feed' => 'yes', 'xn_auth' => 'no'));
		$this->pagePickerOptions = self::pagePickerOptions($sortNames, $currentSortName, $_GET['user']);
        if (!$this->totalCount || $_GET['test_empty']) {
            $this->render('listEmpty');
        } else {
            $this->render('list');
        }
    }
    /**
     * Returns list for the given sorts, for initializing the combobox.
     *
     * @param $sortNames array  Names of sorts to display in the combobox
     * @param $currentSortName string  Name of the current sort
     * @param $username string  Username that will be filtered on (optional)
     * @return list
     */
	private function pagePickerOptions($sortNames, $currentSortName, $username) {
        $filterMetadata = array();
        foreach ($sortNames as $sortName) {
            $filterMetadata[] = array(
                    'displayText' => Page_Filter::get($sortName)->getDisplayText($username),
                    'url' => XG_HttpHelper::addParameter(XG_HttpHelper::currentUrl(), 'sort', $sortName),
                    'selected' => $sortName == $currentSortName);
        }
        return $filterMetadata;
    }

}