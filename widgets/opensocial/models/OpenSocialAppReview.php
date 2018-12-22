<?php

/**
 * Represents a member's review of an OpenSocial application.
 */
class OpenSocialAppReview extends W_Model {
    
    const MIN_RATING = 1.0;
    const MAX_RATING = 5.0;
    const MAX_BODY_CHARS = 4000;

    /**
     * Screenname of the user who created the review.
     *
     * @var XN_Attribute::STRING
     */
    public $user;
    
    /**
     * URL of the app this review is of.
     *
     * @var XN_Attribute::STRING
     */
    public $appUrl;
    
    /**
     * User's rating out of 5 for the app.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 1,5
     */
    public $rating;

    /**
     * Body of the review.
     *
     * @var XN_Attribute::STRING
     * @rule length 0,4000
     */
    public $body;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
    /**
     * Create a new OpenSocialAppReview.  It is the responsibility of the calling routine to save it.
     *
     * @param   $appUrl string              URL of app being reviewed
     * @param   $user   string              Screen name of user reviewing the app
     * @param   $rating float               Rating from 1-5
     * @param   $body   string              Body of the review
     * @return          OpenSocialAppReview Freshly minted review
     */
    public static function create($appUrl, $user, $rating, $body) {
        $app = W_Content::create('OpenSocialAppReview');
        $app->my->appUrl = $appUrl;
        $app->my->user = $user;
        $app->my->rating = $rating;
        $app->my->body = $body;
        XG_Query::invalidateCache('opensocialappreview-' . md5($appUrl));
        return $app;
    }
    
    /**
     * Find reviews of the specified application.
     *
     * @param   $appUrl string  URL of app to get reviews of
     * @param   $begin  int     Start from
     * @param   $end    int     End before
     * @return          array   array('reviews' => array(<OpenSocialAppReview>, ...), 'numReviews' => <int>)
     */
    public static function find($appUrl, $begin=0, $end=10) {
        $query = XG_Query::create('Content')->filter('owner')->filter('type', '=', 'OpenSocialAppReview')->filter('my->appUrl', '=', trim($appUrl))
            ->begin($begin)->end($end)->order('updatedDate', 'desc', XN_Attribute::DATE)->alwaysReturnTotalCount(true)->setCaching('opensocialappreview-' . md5($appUrl));
        return  array('reviews' => $query->execute(), 'numReviews' => $query->getTotalCount());
    }
    
    /**
     * Load a review from it's content store id.
     *
     * @param   $id string              Content store id of the review to load.
     * @return      OpenSocialAppReview Review requested or null if not found.
     */
    public static function loadById($id) {
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'OpenSocialAppReview')->filter('id', '=', $id)->begin(0)->end(1);
        $results = $query->execute();
        return ($results ? $results[0] : null);
    }

    /**
     * Loads app reviews from zero or more of the appUrl and user screenName.
     *
     * @param   $appUrl     string                  URL of the app to get reviews for, or NULL to get reviews for all apps.
     * @param   $screenName string                  ScreenName of the user to get reviews by, or NULL to get reviews for all users.
     * @return              OpenSocialAppReview     array('reviews' => array of first 100 <OpenSocialAppReview> objects matching the criteria, 
     *                                              'numReviews' => <int> total number of reviews in content store).
     */
    public static function load($appUrl, $user) {
        //TODO: This routine should be combined with find now that it is so similar. [Thomas David Baker 2008-10-08]
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'OpenSocialAppReview');
        if ($appUrl) {
            $query->filter('my->appUrl', '=', $appUrl);
        }
        if ($user) {
            $query->filter('my->user', '=', $user);
        }
        $query->alwaysReturnTotalCount(true);
        return array('reviews' => $query->execute(), 'numReviews' => $query->getTotalCount());
    }
    
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/
}
