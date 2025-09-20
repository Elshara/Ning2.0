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
        $params = $this->buildForwardParameters();
        if (! array_key_exists('screenName', $params)) {
            $params['screenName'] = $this->_user->screenName;
        }
        $this->redirectTo('listForContributor', 'photo', $params);
    }

    public function action_settings() {
        header('Location: ' . W_Cache::getWidget('profiles')->buildUrl('profile', 'settings'));
        exit();
    }

    public function action_isFavorite() {
        try {
            if ($this->_user->isLoggedIn()) {
                $photoId = $this->getPhotoIdFromQuery();
                if ($photoId === '') {
                    throw new InvalidArgumentException('Missing photoId parameter.');
                }
                return Photo_JsonHelper::outputAndExit(array(
                    'success' => 1,
                    'msg' => Photo_UserHelper::hasFavorite(Photo_UserHelper::load($this->_user), $photoId),
                ));
            } else {
                return Photo_JsonHelper::outputAndExit(array('error' => 1, 'msg' => 'please login'));
            }
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_favorize() {
        try {
            session_start();
            $target = null;
            $afterKey = $this->getAfterParam();
            $queryPhotoId = $this->getPhotoIdFromQuery();
            $requestPhotoId = $this->getPhotoIdFromRequest();
            if (! $this->_user->isLoggedIn() && ($afterKey !== '') && ($queryPhotoId !== '')) {
                $_SESSION[$afterKey] = 'favorize_' . $queryPhotoId;
                $target = $this->_buildUrl('user', 'favorize', '?' . http_build_query(array(
                    'photoId' => $queryPhotoId,
                    'after' => $afterKey,
                )));
            }
            XG_SecurityHelper::redirectIfNotMember($target);
            XG_JoinPromptHelper::joinGroupOnSave();
            if ($requestPhotoId === '') {
                throw new InvalidArgumentException('Missing photoId parameter.');
            }
            $getAllowed = ($afterKey !== ''
                && isset($_SESSION[$afterKey])
                && $_SESSION[$afterKey] === 'favorize_' . $requestPhotoId);
            if (($_SERVER['REQUEST_METHOD'] !== 'POST') && (! $getAllowed)) {
                $this->redirectTo('show', 'photo', array('id' => $requestPhotoId));
                return;
            }
            $user = Photo_UserHelper::load($this->_user);
            if (Photo_UserHelper::hasFavorite($user, $requestPhotoId)) {
                if ($getAllowed) {
                    if ($afterKey !== '' && isset($_SESSION[$afterKey])) {
                        unset($_SESSION[$afterKey]);
                    }
                    $this->redirectTo('show', 'photo', array('id' => $requestPhotoId));
                    return;
                }
                Photo_JsonHelper::outputAndExit(array());
                return;
            }

            $photo = Photo_ContentHelper::findByID('Photo', $requestPhotoId);
            if ($this->error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $photo)) {
                $this->render('error', 'index');
                return;
            }
            Photo_UserHelper::addFavorite($user, $requestPhotoId);
            $user->save();
            $photo->addfavorite();
            $photo->save();
            XN_Cache::invalidate(XG_CacheExpiryHelper::favoritePhotosChangedCondition($this->_user->screenName));
            if ($getAllowed) {
                if ($afterKey !== '' && isset($_SESSION[$afterKey])) {
                    unset($_SESSION[$afterKey]);
                }
                $this->redirectTo('show', 'photo', array('id' => $requestPhotoId));
            } else {
                Photo_JsonHelper::outputAndExit(array('html' => xg_html('PHOTO_IS_FAVORITE_OF', $photo->my->favoritedCount)));
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
            $photoId = $this->getPhotoIdFromRequest();
            if ($photoId === '') {
                throw new InvalidArgumentException('Missing photoId parameter.');
            }
            // Must be logged in to defavorize
            if (! $this->_user->isLoggedIn()) {
                throw new Exception('Forbidden');
            }
            $user = Photo_UserHelper::load($this->_user);
            // Can only defavorize something that's been favorized
            if (! Photo_UserHelper::hasFavorite($user, $photoId)) {
                Photo_JsonHelper::outputAndExit(array());
                return;
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
            Photo_JsonHelper::outputAndExit(array('html' => $photo->my->favoritedCount ? xg_html('PHOTO_IS_FAVORITE_OF', $photo->my->favoritedCount) : ''));
        } catch (Exception $e) {
            header('HTTP/1.0 403 Forbidden');
        }
    }

    public function action_list() {
        $this->searchFor = $this->getQueryString('searchFor');

        $this->handleSortingAndPagination(array('searchFor' => $this->searchFor));
        Photo_FullNameHelper::initialize($this->users);

        $this->pageUrl = $this->_buildUrl('user', 'list');
        if (array_key_exists('searchFor', $_GET)) {
            $this->pageUrl = Photo_HtmlHelper::addParamToUrl($this->pageUrl, 'searchFor', $this->searchFor);
        }
        $this->friendStatus = Photo_UserHelper::getFriendStatusFor($this->_user, $this->users);
        $this->bodyId = 'list-users';
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
        Photo_FullNameHelper::initialize($this->users);

        $this->pageUrl = $this->_buildUrl('user', 'listFriends');
        if (array_key_exists('searchFor', $_GET)) {
            $this->pageUrl = Photo_HtmlHelper::addParamToUrl($this->pageUrl, 'searchFor', $this->searchFor);
        }
        if (array_key_exists('screenName', $_GET)) {
            $this->pageUrl = Photo_HtmlHelper::addParamToUrl($this->pageUrl, 'screenName', $this->screenName);
        }
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
    private function handleSortingAndPagination(?array $filters = null, int $numPerPage = self::NUM_THUMBS_GRIDVIEW): void {
        $begin = 0;
        $page = $this->getPositiveIntParam('page');
        if (! is_null($page)) {
            $begin = ($page - 1) * $numPerPage;
        }

        $knownSorts = Photo_UserHelper::getKnownSortingOrders();
        $requestedSortKey = $this->resolveSortKey($knownSorts);
        if (! is_null($requestedSortKey)) {
            $this->sort = $knownSorts[$requestedSortKey];
        }
        if (! $this->sort) {
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
        $testPageItems = $this->getNonNegativeIntParam('test_page_items');
        if (! is_null($testPageItems)) {
            $userData['users'] = array();
            for ($i = 0; $i < $testPageItems; $i++) {
                $userData['users'][] = Photo_UserHelper::loadOrCreate('Ning');
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
        $knownSorts = Photo_UserHelper::getKnownSortingOrders();
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

    private function getAfterParam(): string {
        if (! array_key_exists('after', $_GET)) { return ''; }
        $value = trim((string) $_GET['after']);
        if ($value === '') { return ''; }
        $value = mb_substr($value, 0, 128);
        return preg_replace('/[^A-Za-z0-9_.:-]/u', '', $value);
    }

    private function getPhotoIdFromQuery(): string {
        if (! array_key_exists('photoId', $_GET)) { return ''; }
        return $this->sanitizeId($_GET['photoId']);
    }

    private function getPhotoIdFromRequest(): string {
        if (array_key_exists('photoId', $_POST)) {
            return $this->sanitizeId($_POST['photoId']);
        }
        if (array_key_exists('photoId', $_GET)) {
            return $this->sanitizeId($_GET['photoId']);
        }
        return '';
    }

    private function sanitizeId($value): string {
        $value = trim((string) $value);
        if ($value === '') { return ''; }
        return mb_substr($value, 0, 64);
    }
}
