<?php

class Photo_RatingController extends W_Controller {
    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Photo_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_JsonHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PrivacyHelper.php');
        Photo_PrivacyHelper::checkMembership();
        Photo_HttpHelper::trimGetAndPostValues();
    }

    public function action_update() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnSave();
        if (!$this->_user->isLoggedIn()) {
            return;
        }
        try {
            $photo = Photo_PhotoHelper::load($_GET['photoId']);
            if ($this->error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $photo)) {
                $this->render('error', 'index');
                return;
            }
            $newRating = intval($_POST['rating']);
            if (!isset($_POST['rating'])) {
                throw new Exception("Missing rating");
            }
            if (($newRating < 1) || ($newRating > 5)) {
                throw new Exception("Rating must be between 1 and 5");
            }
            $user = Photo_UserHelper::load($this->_user);
            $photo->addRating(Photo_UserHelper::getRating($user, $photo->id), $newRating);
            $photo->save();
            Photo_UserHelper::setRating($user, $photo->id, $newRating);
            $user->save();
            Photo_JsonHelper::outputAndExit(array('html' => xg_rating_image($photo->my->ratingAverage)));
        } catch (Exception $e) {
            Photo_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }
}
