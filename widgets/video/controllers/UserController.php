<?php

class Video_UserController extends W_Controller {
    /** The number of video thumbs on a two column view. */
    const NUM_THUMBS_TWOCOLUMNVIEW = 10; // 5 rows with 2 columns
    /** The number of user profile thumbs on a grid view. */
    const NUM_THUMBS_GRIDVIEW = 24; // 8 rows with 3 columns

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Video_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_JsonHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_TrackingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_PrivacyHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        Video_PrivacyHelper::checkMembership();
        Video_TrackingHelper::insertHeader();
        Video_HttpHelper::trimGetAndPostValues();
    }

    /**
     * Shows the user page.
     * TODO: Right now it simply redirects to the the listForContributor page.
     *       The question is, do we need both of these pages ?
     */
    public function action_show() {
        $this->redirectTo('listForContributor', 'video', $_GET);
    }

    public function action_settings() {
        header('Location: ' . W_Cache::getWidget('profiles')->buildUrl('profile','settings'));
        exit();
    }

    public function action_list() {
        $this->searchFor = $_GET['searchFor'];

        self::handleSortingAndPagination(array('searchFor' => $this->searchFor));

        $this->pageUrl      = $this->_buildUrl('user', 'list');
        if (isset($_GET['searchFor'])) {
            $this->pageUrl = Video_HtmlHelper::addParamToUrl($this->pageUrl, 'searchFor', $_GET['searchFor'], false);
        }
        $this->friendStatus = Video_UserHelper::getFriendStatusFor($this->_user, $this->users);
        $this->bodyId = 'list-users';
        Video_FullNameHelper::initialize($this->users);
    }

    public function action_listFriends() {
        $this->searchFor = $_GET['searchFor'];
        $this->screenName = $_GET['screenName'];
        if (! $this->screenName) {
            XG_SecurityHelper::redirectIfNotMember();
            $this->screenName = $this->_user->screenName;
        }

        // Note that we're querying only for friends (not pending), and only
        // those friends that have used this app
        self::handleSortingAndPagination(array('searchFor' => $this->searchFor,
                                               'friendsOf' => $this->screenName));

        $this->pageUrl = $this->_buildUrl('user', 'listFriends');
        if (isset($_GET['searchFor'])) {
            $this->pageUrl = Video_HtmlHelper::addParamToUrl($this->pageUrl, 'searchFor', $_GET['searchFor'], false);
        }
        $this->pageUrl = Video_HtmlHelper::addParamToUrl($this->pageUrl, 'screenName', $this->screenName, false);
        $this->friendStatus = Video_UserHelper::getFriendStatusFor($this->_user, $this->users);
        $this->bodyId = 'list-friends';
        Video_FullNameHelper::initialize($this->users);
    }

    /**
     * Handles pagination and sorting for the list actions.
     *
     * @param filters    The filters for selecting the users
     *                   (see Video_userHelper::getSortedUsers)
     * @param numPerPage The number of thumbs per page
     */
    private function handleSortingAndPagination($filters = null, $numPerPage = self::NUM_THUMBS_GRIDVIEW) {
        $begin = 0;
        if (preg_match('@^[.0-9]+$@u', $_GET['page']) && ($_GET['page'] > 0)) {
            $begin = ($_GET['page'] - 1) * $numPerPage;
        }

        if ($_GET['sort']) {
            $knownSorts = Video_UserHelper::getKnownSortingOrders();
            $this->sort = $knownSorts[$_GET['sort']];
        }
        if (!$this->sort) {
            $this->sort = Video_UserHelper::getMostRecentSortingOrder();
        }

        $userData = Video_UserHelper::getSortedUsers($filters,
                                                     $this->sort,
                                                     $begin,
                                                     $begin + $numPerPage);

        // Safety measure if an invalid page number was entered
        if (($begin >= $userData['numUsers']) && ($userData['numUsers'] > 0)) {
            $begin    = ((int)($userData['numUsers'] - 1) / $numPerPage) * $numPerPage;
            $userData = Video_UserHelper::getSortedUsers($filters,
                                                         $this->sort,
                                                         $begin,
                                                         $begin + $numPerPage);
        }
        if ($_GET['test_page_items']) {
            $userData['users'] = array();
            for ($i = 0; $i < $_GET['test_page_items']; $i++) {
                $userData['users'][] = Video_UserHelper::loadOrCreate('Ning');
            }
            $userData['numUsers'] = $_GET['test_total_items'] ? $_GET['test_total_items'] : $_GET['test_page_items'];
        }

        $this->users    = $userData['users'];
        $this->page     = 1 + (int)($begin / $numPerPage);
        $this->numPages = $userData['numUsers'] == 0 ? 1 : 1 + (int)(($userData['numUsers'] - 1) / $numPerPage);
        $this->numUsers = $userData['numUsers'];
    }
}
