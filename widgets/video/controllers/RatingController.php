<?php

class Video_RatingController extends W_Controller {



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



    public function action_update() {
        try {
            XG_SecurityHelper::redirectIfNotMember();
            XG_JoinPromptHelper::joinGroupOnSave();
            $video = Video_ContentHelper::findByID('Video', $_GET['videoId']);
            if ($this->error = Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $video)) {
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
            $user = Video_UserHelper::load($this->_user);
            $oldTotal = $video->my->ratingAverage * $video->my->ratingCount;
            if ($oldRating = Video_UserHelper::getRating($user, $video->id)) {
                $newTotal = $oldTotal - $oldRating + $newRating;
            } else {
                $newTotal = $oldTotal + $newRating;
                $video->my->ratingCount = $video->my->ratingCount + 1;
                $video->addRater($this->_user->screenName);
            }
            Video_UserHelper::setRating($user, $video->id, $newRating);
            $user->save();
            $video->my->ratingAverage = $newTotal / $video->my->ratingCount;
            // Rating average may get out of sync because of write contention  [Jon Aquino 2006-08-24]
            $video->my->ratingAverage = min(5, max(0, $video->my->ratingAverage));
            $video->save();
            Video_JsonHelper::outputAndExit(array('html' => xg_rating_image($video->my->ratingAverage)));
        } catch (Exception $e) {
            Video_JsonHelper::handleExceptionInAjaxCall($e);
        }
    }



}
