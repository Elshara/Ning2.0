<?php

/**
 * Helper functions to do with users and OpenSocial.
 */
class OpenSocial_UserHelper {

    /**
     * Do all the necessary work to remove the maximum number of OpenSocialAppReview objects associated with a specific user.
     *
     * @param   $userObject     User    User object representing the user being removed.
     * @return                  array   array(<int> number of objects removed, <int> number of objects remaining to be removed)
     */
    public static function removeReviewsByUser($userObject) {
        $changed = 0;
        $reviewsInfo = OpenSocialAppReview::load(null, $userObject->title);
        foreach ($reviewsInfo['reviews'] as $review) {
            $app = OpenSocialApp::load($review->my->appUrl);
            if ($app->my->numReviews > 1) {
                $app->my->avgRating = (($app->my->avgRating * $app->my->numReviews) - $review->my->rating) / ($app->my->numReviews - 1);
            } else {
                $app->my->avgRating = 0.0;
            }
            $app->my->numReviews = $app->my->numReviews - 1;
            $app->save();
            XN_Content::delete($review);
            XG_Query::invalidateCache('opensocialappreview-' . md5($app->my->appUrl));
            $changed++;
        }
        return array($changed, $reviewsInfo['numReviews'] - $changed);
    }

    /**
     * Do all the necessary work to remove the maximum number of OpenSocialAppData objects associated with a specific user.
     *
     * @param   $userObject     User    User object representing the user being removed.
     * @return                  array   array(<int> number of objects removed, <int> number of objects remaining to be removed)
     */
    public static function removeAppsByUser($userObject) {
        $changed = 0;
        $appInfo = OpenSocialAppData::loadMultiple(null, array($userObject->title));
        foreach ($appInfo['apps'] as $appData) {
            $app = OpenSocialApp::load($appData->my->appUrl);
            $app->my->numMembers = $app->my->numMembers - 1;
            OpenSocialApp::removeMember($app, $userObject->title);
            $app->save();
            XN_Content::delete($appData);
            XG_Query::invalidateCache('opensocial-num-users-' . md5($app->my->appUrl));
            XG_Query::invalidateCache('action-people-members-' . md5($app->my->appUrl));
            XG_Query::invalidateCache('action-people-friends-' . md5($app->my->appUrl));
            $changed++;
        }
        return array($changed, $appInfo['total'] - $changed);
    }
}
