<?php

class Photo_UserController extends W_Controller {
    /** The number of photo thumbs on a two column view. */
    const NUM_THUMBS_TWOCOLUMNVIEW = 10; // 5 rows with 2 columns
    /** The number of photo thumbs on a six column view. */
    const NUM_THUMBS_SIXCOLUMNVIEW = 30; // 5 rows with 6 columns
    /** The number of user profile thumbs on a grid view. */
    const NUM_THUMBS_GRIDVIEW = 21; // 7 rows with 3 columns

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Photo_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_JsonHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PrivacyHelper.php');
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        Photo_PrivacyHelper::checkMembership();
        Photo_HttpHelper::trimGetAndPostValues();
    }

    /**
     * Shows the user page.
     */
    public function action_show() {
        if (array_key_exists('screenName', $_GET)) {
            $this->redirectTo('listForContributor', 'photo', $_GET);
        } else {
            $this->redirectTo('listForContributor', 'photo', array_merge($_GET, array('screenName' => $this->_user->screenName)));
        }
    }

    public function action_settings() {
        header('Location: ' . W_Cache::getWidget('profiles')->buildUrl('profile','settings'));
        exit();
    }

    public function action_isFavorite() {
        try {
            if ($this->_user->isLoggedIn()) {
                $photoId = $_GET['photoId'];
                return Photo_JsonHelper::outputAndExit(array(success=>1,msg=>(Photo_UserHelper::hasFavorite(Photo_UserHelper::load($this->_user), $photoId))));
            } else {
                return Photo_JsonHelper::outputAndExit(array(error=>1,msg=>"please login"));
            }
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_favorize() {
        try {
            session_start();
            if ((!$this->_user->isLoggedIn())&&(isset($_GET['after']))) {
                //the action to take after sign-in/up is stored on a cookie see BAZ-1492
                $_SESSION[$_GET['after']] = 'favorize_'.$_GET['photoId'];
                $target = $this->_buildUrl('user', 'favorize', '?photoId=' . $_GET['photoId'].'&after='.$_GET['after']);
            }
            XG_SecurityHelper::redirectIfNotMember($target);
            XG_JoinPromptHelper::joinGroupOnSave();
            $photoId = $_REQUEST['photoId'];
            $get_allowed = ($_SESSION[$_GET['after']] == 'favorize_'.$photoId);
            if (($_SERVER['REQUEST_METHOD'] != 'POST')&&(!$get_allowed)) {
                $this->redirectTo('show', 'photo', '?id=' . $_GET['photoId']);  // BAZ-3314 [Jon Aquino 2007-06-07]
                return;
            }
            $user = Photo_UserHelper::load($this->_user);
            if (Photo_UserHelper::hasFavorite($user, $photoId)) {
                if($get_allowed){
                    unset($_SESSION[$_GET['after']]);
                    $this->redirectTo('show', 'photo', '?id=' . $_GET['photoId']);
                    return;
                }else{
                    return Photo_JsonHelper::outputAndExit(array());
                }
            }

            $photo = Photo_ContentHelper::findByID('Photo', $photoId);
            if ($this->error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $photo)) {
                $this->render('error', 'index');
                return;
            }
            Photo_UserHelper::addFavorite($user, $photoId);
            $user->save();
            $photo->addfavorite();
            $photo->save();
            XN_Cache::invalidate(XG_CacheExpiryHelper::favoritePhotosChangedCondition($this->_user->screenName));
            if($get_allowed){
                unset($_SESSION[$_GET['after']]);
                $this->redirectTo('show', 'photo', '?id=' . $_GET['photoId']);
            }else{
                Photo_JsonHelper::outputAndExit(array(html => xg_html('PHOTO_IS_FAVORITE_OF', $photo->my->favoritedCount)));
            }
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_defavorize() {
        try {
            XG_SecurityHelper::redirectIfNotMember();
            XG_JoinPromptHelper::joinGroupOnDelete();
            if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
            $photoId = $_REQUEST['photoId'];
            // Must be logged in to defavorize
            if (! $this->_user->isLoggedIn()) {
                throw new Exception('Forbidden');
            }
            $user = Photo_UserHelper::load($this->_user);
            // Can only defavorize someting that's been favorized
            if (! Photo_UserHelper::hasFavorite($user, $photoId)) {
                return Photo_JsonHelper::outputAndExit(array());
            }
            $photo = Photo_ContentHelper::findByID('Photo', $photoId);
            if ($this->error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $photo)) {
                throw new Exception('Forbidden');
            }
            Photo_UserHelper::removeFavorite($user, $photoId);
            $photo->removeFavorite();
            $user->save();
            $photo->save();
            XN_Cache::invalidate(XG_CacheExpiryHelper::favoritePhotosChangedCondition($this->_user->screenName));
            Photo_JsonHelper::outputAndExit(array(html => $photo->my->favoritedCount ? xg_html('PHOTO_IS_FAVORITE_OF', $photo->my->favoritedCount) : ''));
        } catch (Exception $e) {
            header("HTTP/1.0 403 Forbidden");
        }
    }

    public function action_list() {
        $this->searchFor = $_GET['searchFor'];

        self::handleSortingAndPagination(array('searchFor' => $this->searchFor));
        Photo_FullNameHelper::initialize($this->users);

        $this->pageUrl = $this->_buildUrl('user', 'list');
        if (isset($_GET['searchFor'])) {
            $this->pageUrl = Photo_HtmlHelper::addParamToUrl($this->pageUrl, 'searchFor', $_GET['searchFor']);
        }
        $this->friendStatus = Photo_UserHelper::getFriendStatusFor($this->_user, $this->users);
        $this->bodyId = 'list-users';
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
        Photo_FullNameHelper::initialize($this->users);

        $this->pageUrl = $this->_buildUrl('user', 'listFriends');
        if (isset($_GET['searchFor'])) {
            $this->pageUrl = Photo_HtmlHelper::addParamToUrl($this->pageUrl, 'searchFor', $_GET['searchFor']);
        }
        $this->pageUrl = Photo_HtmlHelper::addParamToUrl($this->pageUrl, 'screenName', $this->screenName);
        $this->friendStatus = Photo_UserHelper::getFriendStatusFor($this->_user, $this->users);
        $this->bodyId = 'list-friends';
    }


    /**
     * Handles pagination and sorting for the list actions.
     *
     * @param filters    The filters for selecting the users
     *                   (see Photo_userHelper::getSortedUsers)
     * @param numPerPage The number of thumbs per page
     */
    private function handleSortingAndPagination($filters = null, $numPerPage = self::NUM_THUMBS_GRIDVIEW) {
        $begin = 0;
        if (preg_match('@^[.0-9]+$@u', $_GET['page']) && ($_GET['page'] > 0)) {
            $begin = ($_GET['page'] - 1) * $numPerPage;
        }

        if ($_GET['sort']) {
            $knownSorts = Photo_UserHelper::getKnownSortingOrders();
            $this->sort = $knownSorts[$_GET['sort']];
        }
        if (!$this->sort) {
            $this->sort = Photo_UserHelper::getMostRecentSortingOrder();
        }

        $userData = Photo_UserHelper::getSortedUsers($filters,
                                                     $this->sort,
                                                     $begin,
                                                     $begin + $numPerPage);

        // Safety measure if an invalid page number was entered
        if (($begin >= $userData['numUsers']) && ($userData['numUsers'] > 0)) {
            $begin    = ((int)($userData['numUsers'] - 1) / $numPerPage) * $numPerPage;
            $userData = Photo_UserHelper::getSortedUsers($filters,
                                                         $this->sort,
                                                         $begin,
                                                         $begin + $numPerPage);
        }

         if ($_GET['test_page_items']) {
            $userData['users'] = array();
            for ($i = 0; $i < $_GET['test_page_items']; $i++) {
                $userData['users'][] = Photo_UserHelper::loadOrCreate('Ning');
            }
            $userData['numUsers'] = $_GET['test_total_items'] ? $_GET['test_total_items'] : $_GET['test_page_items'];
        }

        $this->users    = $userData['users'];
        $this->page     = 1 + (int)($begin / $numPerPage);
        $this->numPages = $userData['numUsers'] == 0 ? 1 : 1 + (int)(($userData['numUsers'] - 1) / $numPerPage);
        $this->totalUsers = $userData['numUsers'];
    }
}
