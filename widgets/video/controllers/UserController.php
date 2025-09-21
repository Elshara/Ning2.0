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
        $this->redirectTo('listForContributor', 'video', $this->buildForwardParameters());
    }

    public function action_settings() {
        header('Location: ' . W_Cache::getWidget('profiles')->buildUrl('profile', 'settings'));
        exit();
    }

    public function action_list() {
        $this->searchFor = $this->getQueryString('searchFor');

        $this->handleSortingAndPagination(array('searchFor' => $this->searchFor));

        $this->pageUrl = $this->_buildUrl('user', 'list');
        if (array_key_exists('searchFor', $_GET)) {
            $this->pageUrl = Video_HtmlHelper::addParamToUrl($this->pageUrl, 'searchFor', $this->searchFor, false);
        }
        $this->friendStatus = Video_UserHelper::getFriendStatusFor($this->_user, $this->users);
        $this->bodyId = 'list-users';
        Video_FullNameHelper::initialize($this->users);
    }

    public function action_listFriends() {
        $this->searchFor = $this->getQueryString('searchFor');
        $this->screenName = $this->getQueryString('screenName');
        if (! $this->screenName) {
            XG_SecurityHelper::redirectIfNotMember();
            $this->screenName = $this->_user->screenName;
        }

        // Note that we're querying only for friends (not pending), and only
        // those friends that have used this app
        $this->handleSortingAndPagination(array('searchFor' => $this->searchFor,
                                               'friendsOf' => $this->screenName));

        $this->pageUrl = $this->_buildUrl('user', 'listFriends');
        if (array_key_exists('searchFor', $_GET)) {
            $this->pageUrl = Video_HtmlHelper::addParamToUrl($this->pageUrl, 'searchFor', $this->searchFor, false);
        }
        if (array_key_exists('screenName', $_GET)) {
            $this->pageUrl = Video_HtmlHelper::addParamToUrl($this->pageUrl, 'screenName', $this->screenName, false);
        }
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
    private function handleSortingAndPagination(?array $filters = null, int $numPerPage = self::NUM_THUMBS_GRIDVIEW): void {
        $begin = 0;
        $page = $this->getPositiveIntParam('page');
        if (! is_null($page)) {
            $begin = ($page - 1) * $numPerPage;
        }

        $knownSorts = Video_UserHelper::getKnownSortingOrders();
        $requestedSortKey = $this->resolveSortKey($knownSorts);
        if (! is_null($requestedSortKey)) {
            $this->sort = $knownSorts[$requestedSortKey];
        }
        if (! $this->sort) {
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

        $testPageItems = $this->getNonNegativeIntParam('test_page_items');
        if (! is_null($testPageItems)) {
            $userData['users'] = array();
            for ($i = 0; $i < $testPageItems; $i++) {
                $userData['users'][] = Video_UserHelper::loadOrCreate('Ning');
            }
            $testTotalItems = $this->getNonNegativeIntParam('test_total_items');
            $userData['numUsers'] = ! is_null($testTotalItems) ? $testTotalItems : $testPageItems;
        }

        $this->users    = $userData['users'];
        $this->page     = 1 + (int)($begin / $numPerPage);
        $this->numPages = $userData['numUsers'] == 0 ? 1 : 1 + (int)(($userData['numUsers'] - 1) / $numPerPage);
        $this->numUsers = $userData['numUsers'];
    }

    private function buildForwardParameters(): array {
        $params = array();
        if (array_key_exists('searchFor', $_GET)) {
            $params['searchFor'] = $this->getQueryString('searchFor');
        }
        $page = $this->getPositiveIntParam('page');
        if (! is_null($page)) {
            $params['page'] = $page;
        }
        $knownSorts = Video_UserHelper::getKnownSortingOrders();
        $requestedSortKey = $this->resolveSortKey($knownSorts);
        if (! is_null($requestedSortKey)) {
            $params['sort'] = $requestedSortKey;
        }
        if (array_key_exists('screenName', $_GET)) {
            $params['screenName'] = $this->getQueryString('screenName');
        }
        return $params;
    }

    private function getQueryString(string $key, int $maxLength = 255): string {
        if (! array_key_exists($key, $_GET)) { return ''; }
        $value = trim((string) $_GET[$key]);
        if ($value === '') { return ''; }
        if ($maxLength > 0) {
            $value = mb_substr($value, 0, $maxLength);
        }
        return $value;
    }

    private function getPositiveIntParam(string $key): ?int {
        return $this->getIntParam($key, 1, null);
    }

    private function getNonNegativeIntParam(string $key): ?int {
        return $this->getIntParam($key, 0, null);
    }

    private function getIntParam(string $key, int $min, ?int $max): ?int {
        if (! array_key_exists($key, $_GET)) { return null; }
        $options = array('options' => array('min_range' => $min));
        if (! is_null($max)) {
            $options['options']['max_range'] = $max;
        }
        $value = filter_var($_GET[$key], FILTER_VALIDATE_INT, $options);
        if ($value === false) { return null; }
        return (int) $value;
    }

    private function resolveSortKey(array $knownSorts): ?string {
        if (! array_key_exists('sort', $_GET)) { return null; }
        $requested = $this->getQueryString('sort', 64);
        if ($requested === '') { return null; }
        return array_key_exists($requested, $knownSorts) ? $requested : null;
    }
}
