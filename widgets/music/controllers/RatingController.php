<?php

class Music_RatingController extends W_Controller {
    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Music_TrackHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_ContentHelper.php');
    }

    public function action_updateFromPlayer() {
        if (!$this->_user->isLoggedIn()) {
            return;
        }
        try {
            $track = Track::load($_REQUEST['trackId']);
            $newRating = intval($_POST['rating']);
            if (!isset($_POST['rating'])) {
                throw new Exception("Missing rating");
            }
            if (($newRating < 1) || ($newRating > 5)) {
                throw new Exception("Rating must be between 1 and 5");
            }
            $user = Music_UserHelper::load($this->_user);
            $oldRating = Music_UserHelper::getRating($user, $track->id);
            Music_TrackHelper::addRating($track, $oldRating, $newRating);
            $track->save();
            Music_UserHelper::setRating($user, $track->id, $newRating);
            $user->save();
            header('Content-Type: text/plain');
            $this->ratingAverage = $track->my->ratingAverage;
        } catch (Exception $e) {
            error_log('(4648228798249686) action_updateFromPlayer, rating failed:'.$e->getMessage());
        }
    }
}
