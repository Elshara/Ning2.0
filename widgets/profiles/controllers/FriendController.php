<?php
class Profiles_FriendController extends XG_BrowserAwareController {

    public function action_feed() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FeedHelper.php');
        if (! isset($_GET['user'])) {
            throw new Exception("No user specified");
        }
        $this->setCaching(array('profiles-friend-feed-' . md5(XG_HttpHelper::currentUrl())), 1800);
        if ($_GET['test_caching']) { var_dump('Not cached'); }
        $friendInfo = Profiles_UserHelper::findFriendsOf($_GET['user'], 0, 5);
        $this->profile = XG_Cache::profiles($_GET['user']);
        $this->friends = $friendInfo['friends'];
        $screenNames = array();
        foreach ($this->friends as $friend) {
            $screenNames[$friend->contributorName] = $friend->contributorName;
        }
        $this->profiles = XG_Cache::profiles($screenNames);
        header('Content-Type: application/atom+xml');
    }

    public function action_list() {
        if ($_GET['my']) {
            XG_SecurityHelper::redirectToSignInPageIfSignedOut(XG_HttpHelper::currentUrl());
            return $this->redirectTo(XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('my' => null, 'user' => XN_Profile::current()->screenName)));
        }
        $screenName = $_GET['user'] ? User::loadByProfileAddress($_GET['user'])->title : null;
        if (isset($screenName)) {
            $this->profile = XG_Cache::profiles($screenName);
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
            $user = User::load($this->profile);
			$this->tab = 'members';
            $this->pageTitle = $user->title == $this->_user->screenName ? xg_text('MY_FRIENDS') : xg_text('XS_FRIENDS', xg_username($user->title));
        } else {
            $this->pageTitle = xg_text('MEMBERS');
            $this->tab = 'members';
        }
        if($_GET['output']=='items'){
            $this->_widget->dispatch('friend', 'listColumn', array($this->profile));
            exit();
        }
    }

    /**
     * Update friends list dynamically
     *
     * Expected GET parameter:
     * - xn_out string  must be 'json'
     * - user string  user's whose friends list is being updated
     * - sort string  current sort method
     * - page string  page number of the current display (optional)
     */
    public function action_updateFriendList() {
        if (! XN_Profile::current()->isLoggedIn()) { throw new Exception('Not logged in (87382938742)'); }

        // refactor with above
        $screenName = $_GET['user'] ? User::loadByProfileAddress($_GET['user'])->title : null;
        if (isset($screenName)) {
            $this->profile = XG_Cache::profiles($screenName);
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
            $user = User::load($this->profile);
            $this->tab = $user->title == $this->_user->screenName ? 'profile' : 'members';
            $this->pageTitle = $user->title == $this->_user->screenName ? xg_text('MY_FRIENDS') : xg_text('XS_FRIENDS', xg_username($user->title));

            unset($_GET['xn_out']);
            list(,$this->listHtml) = $this->_widget->capture('friend', 'listColumn', array($this->profile));
            $_GET['xn_out'] = 'json';
        } else {
            $this->error = 1;
        }
    }

    public function action_list_iphone($nav = null) { # void
        if ($_GET['my']) {
            XG_SecurityHelper::redirectToSignInPageIfSignedOut(XG_HttpHelper::currentUrl());
            return $this->redirectTo(XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('my' => null, 'user' => XN_Profile::current()->screenName)));
        }
        if (!$nav) {
            $screenName = $_GET['user'] ? User::loadByProfileAddress($_GET['user'])->title : XN_Profile::current()->screenName;
        }
        if (isset($screenName)) {
            $this->profile = XG_Cache::profiles($screenName);
            $this->currentScreenName = $this->profile->screenName;
            if ($this->_user->screenName == $screenName) {
                $this->tab = 'friends';
            } else {
                $this->tab = 'other_friends';
            }
        } else {
            $this->tab = 'all_members';
        }
    }

    /**
     * Displays a grid of avatars.
     *
     * @param $profile                  XN_Profile  Profile of the person whose friends to display, or null to display all network members
     */
    public function action_listColumn($profile) {
        $screenName = ($profile ? $profile->screenName : null);
        $myFriends = mb_strlen($screenName) && $screenName == $this->_user->screenName;
        // Cache listColumn rather than list, to avoid caching the sidebar [Jon Aquino 2008-01-22]
        // But not if user is an admin - we don't want to cache the Feature links [Jon Aquino 2008-03-03]
        // And not if you are viewing your own friends - we don't want to cache the title: My Friends vs. Joe's Friends [Jon Aquino 2008-03-14]
        if (! isset($_GET['q']) && ! XG_SecurityHelper::userIsAdmin() && ! $myFriends) {
            $this->setCaching(array(
                    'profiles-friend-listColumn-' . md5(XG_HttpHelper::currentUrl()),
                    XG_CacheExpiryHelper::promotedObjectsChangedCondition('User'),
                    XG_CacheExpiryHelper::userBannedCondition(),
                    XG_CacheExpiryHelper::userUnbannedCondition()), 300);
        }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendListHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserSort.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_HtmlHelper.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $this->pageSize = 21;
        $this->page = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        if ($this->page < 1) { $this->page = 1; }
        if (! $profile && $this->page == 1) {
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
            $featuredMembers = Profiles_UserHelper::getPromotedUsers(7);
            $this->showViewAllFeaturedUrl = count($featuredMembers) == 7;
            $this->featuredMembers = array_slice($featuredMembers, 0, 6);
        }
        $this->start = ($this->page - 1) * $this->pageSize;
        $this->end = $this->start + $this->pageSize;
        $this->searchTerm = $_GET['q'];
        $currentSort = Profiles_UserSort::get($_GET['sort'] ? $_GET['sort'] : 'mostRecent');
        $helper = new Profiles_FriendListHelper();
        $this->userInfo = $this->userInfoForListAction($this->searchTerm, $screenName, $this->start, $this->end, $currentSort, $helper);
        $this->totalCount = $this->userInfo['numUsers'];
        XG_Cache::profiles($this->userInfo['users']); //Prime the cache
        $this->pageTitle = $currentSort->getPageTitle($this->userInfo['numUsers']);
        $this->profile = NULL;
        if ($profile) {
        	$this->profile = $profile;
			$this->pageTitle = $screenName == $this->_user->screenName ? xg_text('MY_FRIENDS') : xg_text('XS_FRIENDS', xg_username($screenName));
		} else {
			$this->pageTitle = xg_text('ALL_MEMBERS');
		}
        if ($myFriends) {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_HtmlHelper.php');
            $tabsHtml = Profiles_HtmlHelper::tabsHtml('friends', $this->userInfo['numUsers']);
        }
        $sortOptions = array();
        $this->isSortRandom = $_GET['sort'] == 'random';
        if (! $this->useSearchQuery($this->searchTerm, $screenName, $helper)) {
            $sortOptions = $this->sortOptions(array('mostRecent', 'alphabetical', 'random'), $currentSort, XG_HttpHelper::currentUrl());
        }
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        $this->searchUrl = xg_url(false, array('user' => $screenName));
        if (isset($screenName)) {
            XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
            $this->showInviteLink = strtolower($screenName) === strtolower(XN_Profile::current()->screenName);  /** @non-mb */
            $friendStatus = XG_ContactHelper::getFriendStatusFor(XN_Profile::current()->screenName, $screenName);
            $this->showAddAsFriendLink = ! $this->showInviteLink && ! in_array($friendStatus, array(XN_Profile::FRIEND, XN_Profile::FRIEND_PENDING));
            $this->addAsFriendLinkHtml = xg_add_as_friend_link($screenName, $friendStatus);
        }
        $this->listColumnProperArgs = array('pageTitle' => $this->pageTitle, 'users' => $this->userInfo['users'],
                'numUsers' => $this->userInfo['numUsers'],
                'searchUrl' => $this->searchUrl,
                'pageSize' => $this->pageSize,
                'emptyMessage' => $this->emptyMessage,
                'showInviteLink' => $this->showInviteLink,
                'showAddAsFriendLink' => $this->showAddAsFriendLink,
                'addAsFriendLinkHtml' => $this->addAsFriendLinkHtml,
                'inviteUrl' => $this->inviteUrl,
                'manageUrl' => $this->manageUrl,
                'sortOptions' => $sortOptions,
                'myFriends' => $myFriends,
                'showViewAllFeaturedUrl' => $this->showViewAllFeaturedUrl,
                'isSortRandom' => $this->isSortRandom,
                'searchButtonText' => mb_strlen($screenName) ? xg_text('SEARCH_FRIENDS_NO_COLON') : xg_text('SEARCH_MEMBERS'),
                'tabsHtml' => $tabsHtml);
        if($_GET['output']=='items'){
            $this->listColumnProperArgs['pageTitle'] = false;
        }
    }
    public function action_listColumn_iphone($profile) {
        $screenName = ($profile ? $profile->screenName : null);
        $myFriends = mb_strlen($screenName) && $screenName == $this->_user->screenName;
        // Cache listColumn rather than list, to avoid caching the sidebar [Jon Aquino 2008-01-22]
        // But not if user is an admin - we don't want to cache the Feature links [Jon Aquino 2008-03-03]
        // And not if you are viewing your own friends - we don't want to cache the title: My Friends vs. Joe's Friends [Jon Aquino 2008-03-14]
		if (! isset($_GET['q']) && ! XG_SecurityHelper::userIsAdmin() && ! $myFriends) {
            $this->setCaching(array(
                    'profiles-friend-listColumn-' . md5(XG_HttpHelper::currentUrl()),
                    XG_CacheExpiryHelper::promotedObjectsChangedCondition('User'),
                    XG_CacheExpiryHelper::userBannedCondition(),
                    XG_CacheExpiryHelper::userUnbannedCondition()), 300);
        }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendListHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserSort.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $this->pageSize = 20;
        $this->page = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        if ($this->page < 1) { $this->page = 1; }
        if (! $profile && $this->page == 1) {
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
            $featuredMembers = Profiles_UserHelper::getPromotedUsers(6);
            $this->showViewAllFeaturedUrl = count($featuredMembers) === 6;
            $this->featuredMembers = array_slice($featuredMembers, 0, 5);
        }
        $this->start = ($this->page - 1) * $this->pageSize;
        $this->end = $this->start + $this->pageSize;
        $this->searchTerm = $_GET['q'];
        $currentSort = Profiles_UserSort::get($_GET['sort'] ? $_GET['sort'] : 'mostRecent');
        $helper = new Profiles_FriendListHelper();
        $this->userInfo = $this->userInfoForListAction($this->searchTerm, $screenName, $this->start, $this->end, $currentSort, $helper);
        $this->totalCount = $this->userInfo['numUsers'];
        $this->showNextLink = ($this->totalCount > $this->pageSize * $this->page);
        XG_Cache::profiles($this->userInfo['users']); //Prime the cache
        if (!$myFriends) {
            $this->pageTitle = $currentSort->getPageTitle($this->userInfo['numUsers']);
        }
        $sortOptions = array();
        $this->isSortRandom = $_GET['sort'] == 'random';
        if (! $this->useSearchQuery($this->searchTerm, $screenName, $helper)) {
            $sortOptions = $this->sortOptions(array('mostRecent', 'alphabetical', 'random'), $currentSort, XG_HttpHelper::currentUrl());
        }
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        $this->searchUrl = xg_url(false, array('user' => $screenName));
        $this->userIsOwner = $myFriends;
        $this->listColumnProperArgs = array('pageTitle' => $this->pageTitle, 'users' => $this->userInfo['users'],
                'numUsers' => $this->userInfo['numUsers'],
                'searchUrl' => $this->searchUrl,
                'pageSize' => $this->pageSize,
                'emptyMessage' => $this->emptyMessage,
                'inviteUrl' => $this->inviteUrl,
                'manageUrl' => $this->manageUrl,
                'sortOptions' => $sortOptions,
                'myFriends' => $myFriends,
                'showViewAllFeaturedUrl' => $this->showViewAllFeaturedUrl,
                'isSortRandom' => $this->isSortRandom,
                'searchButtonText' => mb_strlen($screenName) ? xg_text('SEARCH_FRIENDS_NO_COLON') : xg_text('SEARCH_MEMBERS'),
                'tabsHtml' => $tabsHtml,
                'showNextLink' => $this->showNextLink);
        if($_GET['output']=='items'){
            $this->listColumnProperArgs['pageTitle'] = false;
        }
    }

    /**
     * Returns options for the sort drop-down.
     *
     * @param $sortIds array  strings identifying the sorts
     * @param $currentSort Profiles_UserSort  logic for sorting User objects.
     * @param $currentUrl string  URL for the current page
     * @return array  array of arrays, each with displayText, url, and selected;
     *         or null to hide the Sort By drop-down
     */
    protected function sortOptions($sortIds, $currentSort, $currentUrl) {
        $sortOptions = array();
        foreach ($sortIds as $sortName) {
            $sort = Profiles_UserSort::get($sortName);
            $sortOptions[] = array(
                    'displayText' => $sort->getDisplayText(),
                    'url' => XG_HttpHelper::addParameters($currentUrl, array('sort' => $sort->getId(), 'page' => null)),
                    'selected' => $sort->getId() == $currentSort->getId());
        }
        return $sortOptions;
    }

    /**
     * Displays a grid of avatars. Called by the groups widget.  Not actually visited by other routines in FriendController
     * - the listColumnProper template is called with renderPartial instead for efficiency reasons.
     *
     * @param $args array  configuration parameters:
     *     pageTitle - title of the page
     *     users - User objects for display
     *     numUsers - total number of users on all pages
     *     searchUrl - URL for searches (with query parameters and with 'q' as value of search performed)
     *     pageSize - number of avatars per page
     *     emptyMessage - message to display if there are no avatars
     *     showInviteLink - whether to show an invite link in addition to emptyMessage
     *     showAddAsFriendLink - whether to show add as friend link in addition to emptyMessage
     *     addAsFriendLinkHtml - HTML for friend link
     *     inviteUrl - URL for the "Invite More People" link, or null to hide the link
     *     manageUrl - URL for the "Manage Members" link, or null to hide the link
     *     searchButtonText - the text that appears for the search button.
     *     tabsHtml - HTML for the tabs, if any
     */
    public function action_listColumnProper($args) {
        foreach ($args as $key => $value) { $this->{$key} = $value; }
    }

    /**
     * Create a block relationship between the current user and the specified user
     *   Called via a dojo PostLink
     */
    public function action_block() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        if (!isset($_GET['blocked'])) {
            error_log('in profiles/friend/block, \'blocked\' not specified!');
            return;
        }
        Profiles_UserHelper::createRelationshipBlock($this->_user->screenName, $_GET['blocked']);
    }

    /**
     * Returns the User objects for the list action.
     *
     * @param $q string  search terms, or null if there are none
     * @param $screenName string  username of the person whose friends to display, or null to display all network members
     * @param $start integer  start index for the query (inclusive)
     * @param $end integer  end index for the query (exclusive)
     * @param $sort Profiles_UserSort  logic for sorting User objects
     * @param $helper Profiles_FriendListHelper  facade for services used by the list action
     * @return array 'users' => the User objects, 'numUsers' => the total number of User objects
     */
    protected function userInfoForListAction($q, $screenName, $start, $end, $sort, $helper) {
        if ($this->useSearchQuery($q, $screenName, $helper)) {
            return $this->listWithSearchQuery($q, $start, $end, $helper);
        } else {
            return $this->listWithContentQuery($q, $screenName, $start, $end, $sort, $helper);
        }
    }

    /**
     * Returns whether to use a Search query or a Content query for the list action.
     *
     * @param $q string  search terms, or null if there are none
     * @param $screenName string  username of the person whose friends to display, or null to display all network members
     * @param $helper Profiles_FriendListHelper  facade for services used by the list action
     * @return boolean  true to use a Search query; false to use a Content query
     */
    private function useSearchQuery($q, $screenName, $helper) {
        return isset($q) && ! isset($screenName) && $helper->getSearchMethod() == 'search';
    }

    /**
     * @param $q string  search terms, or null if there are none
     * @param $start integer  start index for the query (inclusive)
     * @param $end integer  end index for the query (exclusive)
     * @param $sort Profiles_UserSort  logic for sorting User objects
     * @param $helper Profiles_FriendListHelper  facade for services used by the list action
     */
    protected function listWithContentQuery($q, $screenName, $start, $end, $sort, $helper) {
        $cacheUserQuery = true;
        $filters = array();
        // Searching for all members or just friends of a particular user?
        if (isset($screenName)) {
            $cacheUserQuery = false; // Don't cache the friends query
            $filters['contributorName'] = array('in', XN_Query::FRIENDS($screenName));
            if ($screenName === XN_Profile::current()->screenName) {
                $this->emptyMessage = xg_text('YOU_DONT_HAVE_FRIENDS_ON_NETWORK');
            } else {
                $this->emptyMessage = xg_text('X_DOESNT_HAVE_FRIENDS_ON_NETWORK', xnhtmlentities(xg_username($screenName)));
            }
        } else {
            $this->emptyMessage = xg_text('SITE_HAS_NO_MEMBERS');
        }
        if (isset($q)) {
            $cacheUserQuery = false; // Don't cache search queries, as q is not constrained [Jon Aquino 2007-04-21]
            $filters['my->searchText'] = array('likeic', $q);
        }
        return $sort->findUsers($filters, $start, $end, $cacheUserQuery, $helper);
    }

    /**
     * @param $q string  search terms, or null if there are none
     * @param $start integer  start index for the query (inclusive)
     * @param $end integer  end index for the query (exclusive)
     * @param $helper Profiles_FriendListHelper  facade for services used by the list action
     */
    protected function listWithSearchQuery($q, $start, $end, $helper) {
        $query = $helper->createQuery('Search')
                ->begin($start)->end($end)
                ->filter('type', 'like', 'User')
                ->filter('my.approved', '!like', 'N')
                ->filter('fulltext', 'like', $q);
        XG_QueryHelper::addExcludeFromPublicSearchFilter($query, true);
        User::addBlockedFilter($query, true); // Exclude blocked/banned users (BAZ-4024)
        User::addPendingFilter($query, true); // Exclude pending users (BAZ-4427)
        User::addUnfinishedFilter($query, true); // Exclude unfinished users (BAZ-8509)
        $query->alwaysReturnTotalCount(true);
        try {
            $ids = array();
            foreach ($query->execute() as $searchResult) { $ids[] = $searchResult->id; }
            return array('numUsers' => $query->getTotalCount(), 'users' => $helper->content($ids));
        } catch (Exception $e) {
            error_log("Friend search query ({$q}) failed with: " . $e->getCode());
            return array('numUsers' => 0, 'users' => array());
        }
    }

    /**
     * Displays promoted users (only).
     */
    public function action_listFeatured() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendListHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserSort.php');
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->pageSize = 21;
        $start = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $currentSort = Profiles_UserSort::get($_GET['sort'] ? $_GET['sort'] : 'mostRecentlyFeatured');
        $userInfo = $currentSort->findUsers(array('promoted' => true), $start, $start + $this->pageSize, true, new Profiles_FriendListHelper());
        $this->searchUrl = $this->_buildUrl('members','');
        $this->users = $userInfo['users'];
        XG_Cache::profiles($this->users);
        $this->numUsers = $userInfo['numUsers'];
        $this->pageTitle = xg_text('MEMBERS');
        $this->sortOptions = $this->sortOptions(array('mostRecentlyFeatured', 'alphabetical', 'random'), $currentSort, XG_HttpHelper::currentUrl());
    }
    public function action_listFeatured_iphone() {
        $this->action_listFeatured();
        $this->showNextLink = ($this->numUsers > $this->pageSize * ($_GET['page'] + 1));
    }
}
