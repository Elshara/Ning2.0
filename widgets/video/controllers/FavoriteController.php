<?php

class Video_FavoriteController extends W_Controller {

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Video_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_JsonHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_TrackingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_PrivacyHelper.php');
        Video_PrivacyHelper::checkMembership();
        Video_TrackingHelper::insertHeader();
        Video_HttpHelper::trimGetAndPostValues();
    }

    // TODO: Move this to user/favorite  [Jon Aquino 2006-07-31]
    public function action_create() {
        try {
            session_start();
            $target = null;
            $afterKey = $this->getAfterParam();
            $queryVideoId = $this->getVideoIdFromQuery();
            $requestVideoId = $this->getVideoIdFromRequest();

            if (! $this->_user->isLoggedIn() && ($afterKey !== '') && ($queryVideoId !== '')) {
                $_SESSION[$afterKey] = 'favorize_' . $queryVideoId;
                $target = $this->_buildUrl('favorite', 'create', '?' . http_build_query(array(
                    'videoId' => $queryVideoId,
                    'after' => $afterKey,
                )));
            }
            XG_SecurityHelper::redirectIfNotMember($target);
            XG_JoinPromptHelper::joinGroupOnSave();

            if ($requestVideoId === '') {
                throw new InvalidArgumentException('Missing videoId parameter.');
            }

            $getAllowed = ($afterKey !== ''
                && isset($_SESSION[$afterKey])
                && $_SESSION[$afterKey] === 'favorize_' . $requestVideoId);
            if (($_SERVER['REQUEST_METHOD'] !== 'POST') && (! $getAllowed)) {
                $this->redirectTo('show', 'video', array('id' => $requestVideoId));
                return;
            }

            $user = Video_UserHelper::load($this->_user);
            if (Video_UserHelper::hasFavorite($user, $requestVideoId)) {
                if ($getAllowed) {
                    if ($afterKey !== '' && isset($_SESSION[$afterKey])) {
                        unset($_SESSION[$afterKey]);
                    }
                    $this->redirectTo('show', 'video', array('id' => $requestVideoId));
                    return;
                }
                Video_JsonHelper::outputAndExit(array());
                return;
            }

            $video = Video_ContentHelper::findByID('Video', $requestVideoId);
            if ($this->error = Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $video)) {
                $this->render('error', 'index');
                return;
            }
            Video_UserHelper::addFavorite($user, $video->id);
            $user->save();
            $video->my->favoritedCount = $video->my->favoritedCount + 1;
            $video->addFavoriter($this->_user->screenName);
            $video->save();
            if ($getAllowed) {
                if ($afterKey !== '' && isset($_SESSION[$afterKey])) {
                    unset($_SESSION[$afterKey]);
                }
                $this->redirectTo('show', 'video', array('id' => $requestVideoId));
            } else {
                Video_JsonHelper::outputAndExit(array('html' => xg_html('FAVORITE_OF_N_PEOPLE', $video->my->favoritedCount)));
            }
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    public function action_delete() {
        try {
            XG_SecurityHelper::redirectIfNotMember();
            XG_JoinPromptHelper::joinGroupOnDelete();
            if ($this->error = Video_SecurityHelper::checkCurrentUserIsSignedIn($this->_user)) {
                $this->render('error', 'index');
                return;
            }
            $videoId = $this->getVideoIdFromQuery();
            if ($videoId === '') {
                throw new InvalidArgumentException('Missing videoId parameter.');
            }
            $user = Video_UserHelper::load($this->_user);
            if (! Video_UserHelper::hasFavorite($user, $videoId)) {
                Video_JsonHelper::outputAndExit(array());
                return;
            }
            $video = Video_ContentHelper::findByID('Video', $videoId);
            Video_UserHelper::removeFavorite($user, $video->id);
            $user->save();
            $video->my->favoritedCount = ($video->my->favoritedCount > 0 ? $video->my->favoritedCount - 1 : 0);
            $video->removeFavoriter($this->_user->screenName);
            $video->save();
            Video_JsonHelper::outputAndExit(array('html' => $video->my->favoritedCount ? xg_html('FAVORITE_OF_N_PEOPLE', $video->my->favoritedCount) : ''));
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }

    private function getAfterParam(): string {
        if (! array_key_exists('after', $_GET)) { return ''; }
        $value = trim((string) $_GET['after']);
        if ($value === '') { return ''; }
        $value = mb_substr($value, 0, 128);
        return preg_replace('/[^A-Za-z0-9_.:-]/u', '', $value);
    }

    private function getVideoIdFromQuery(): string {
        if (! array_key_exists('videoId', $_GET)) { return ''; }
        return $this->sanitizeId($_GET['videoId']);
    }

    private function getVideoIdFromRequest(): string {
        if (array_key_exists('videoId', $_POST)) {
            return $this->sanitizeId($_POST['videoId']);
        }
        if (array_key_exists('videoId', $_GET)) {
            return $this->sanitizeId($_GET['videoId']);
        }
        return '';
    }

    private function sanitizeId($value): string {
        $value = trim((string) $value);
        if ($value === '') { return ''; }
        return mb_substr($value, 0, 64);
    }
}
