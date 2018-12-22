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
            if ((!$this->_user->isLoggedIn())&&(isset($_GET['after']))) {
                //the action to take after sign-in/up is stored on a cookie see BAZ-1492
                $_SESSION[$_GET['after']] = 'favorize_'.$_GET['videoId'];
                $target = $this->_buildUrl('favorite', 'create', '?videoId=' . $_GET['videoId'].'&after='.$_GET['after']);
            }
            XG_SecurityHelper::redirectIfNotMember($target);
            XG_JoinPromptHelper::joinGroupOnSave();
            $videoId = $_REQUEST['videoId'];
            $get_allowed = ($_SESSION[$_GET['after']] == 'favorize_'.$videoId);
            if (($_SERVER['REQUEST_METHOD'] != 'POST')&&(!$get_allowed)) {
                $this->redirectTo('show', 'video', '?id=' . $_GET['videoId']);  // BAZ-3314 [Jon Aquino 2007-06-07]
                return;
            }
            if (Video_UserHelper::hasFavorite($user, $_GET['videoId'])) {
                if($get_allowed){
                    unset($_SESSION[$_GET['after']]);
                    $this->redirectTo('show', 'video', '?id=' . $_GET['videoId']);
                    return;
                }else{
                    return Video_JsonHelper::outputAndExit(array());
                }
            }
            $video = Video_ContentHelper::findByID('Video', $_GET['videoId']);
            if ($this->error = Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $video)) {
                $this->render('error', 'index');
                return;
            }
            $user = Video_UserHelper::load($this->_user);
            Video_UserHelper::addFavorite($user, $video->id);
            $user->save();
            $video->my->favoritedCount = $video->my->favoritedCount + 1;
            $video->addFavoriter($this->_user->screenName);
            $video->save();
            if($get_allowed){
                unset($_SESSION[$_GET['after']]);
                $this->redirectTo('show', 'video', '?id=' . $_GET['videoId']);
            }else{
                Video_JsonHelper::outputAndExit(array(html => xg_html('FAVORITE_OF_N_PEOPLE', $video->my->favoritedCount)));
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
            $user = Video_UserHelper::load($this->_user);
            if (! Video_UserHelper::hasFavorite($user, $_GET['videoId'])) {
                return Video_JsonHelper::outputAndExit(array());
            }
            $video = Video_ContentHelper::findByID('Video', $_GET['videoId']);
            Video_UserHelper::removeFavorite($user, $video->id);
            $user->save();
            $video->my->favoritedCount = ($video->my->favoritedCount > 0 ? $video->my->favoritedCount - 1 : 0);
            $video->removeFavoriter($this->_user->screenName);
            $video->save();
            Video_JsonHelper::outputAndExit(array(html => $video->my->favoritedCount ? xg_html('FAVORITE_OF_N_PEOPLE', $video->my->favoritedCount) : ''));
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }



}
