<?php

/**
 * Shows about page for OpenSocial gadgets, and more to come
 */
class OpenSocial_ApplicationController extends W_Controller {

    /** The number of applications per page used for the app directory and my applications */
    const APPLICATIONS_PER_PAGE = 8;

    public function _before(){
        //TODO: BAZ-8494 is possibly relevant here.  [Thomas David Baker 2008-07-17]
        if(! XG_App::openSocialEnabled()){
            W_Cache::getWidget('main')->dispatch('error', '404');
            exit;
        }
    }

    /**
     * Display the list of installed applications for the specified user.
     *
     * Expected GET variables:
     *    'user' => screenName of user to show installed applications of.
     */
    public function action_apps() {
        if (! $_GET['user']) {
            if (XN_Profile::current()->isLoggedIn()) {
                $_GET['user'] = XN_Profile::current()->screenName;
            } else {
                XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
                $this->redirectTo(XG_HttpHelper::profileUrl($this->_user->screenName));
            }
        }
        $this->screenName = $_GET['user'];
        $this->user = User::load($this->screenName);
        if(! $this->user) {
            XG_SecurityHelper::redirectIfNotMember();
            $this->screenName = $this->_user->screenName;
            $this->user = $this->_user;
        }
        
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->pageSize = OpenSocial_ApplicationController::APPLICATIONS_PER_PAGE;
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $end = $begin + $this->pageSize;
        
        // get all the apps owned by the user
        $appInfo = OpenSocialAppData::loadMultiple(null, $this->screenName, $begin, $end);
        $this->numApps = $appInfo['total'];
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');
        $this->apps = OpenSocial_ApplicationDirectoryHelper::getAppDetails($appInfo['apps']);
        // If the central app directory is down let's just go with what we know.
        if (! $this->apps) { $this->apps = $appInfo['apps']; }
        foreach ($this->apps as $app) {
            $this->thirdPartyApps = (! $app['ningApplication']) || $this->thirdPartyApps;
        }
        if (XN_Profile::current()->screenName == $this->screenName) {
            $this->pageTitle = xg_text('MY_APPLICATIONS');
            $this->highlightedTab = 'profile';
        } else {
            $this->pageTitle = xg_text('XS_APPLICATIONS', xg_username($this->screenName));
            $this->highlightedTab = 'members';
        }
        $this->searchUrl = $this->_buildUrl('application', 'list');
    }
    
    /**
     * Display the list of gadgets that the user may add to their My Page.
     */
    public function action_list() {
        $this->pageSize = OpenSocial_ApplicationController::APPLICATIONS_PER_PAGE;
        $this->searchUrl = $this->_buildUrl('application', 'list');
        $this->addByUrlUrl = $this->_buildUrl('application', 'addByUrl');
        $this->title = xg_text('APPLICATION_DIRECTORY');
        $this->selectedTab = XN_Profile::current()->isLoggedIn() ? 'profile' : '';
        $this->searchTerm = $_GET['q'];
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $end = $begin + $this->pageSize;
        
        if ($_GET['category']) {
            $this->categoryKey = $_GET['category'];
        } else if (empty($_GET['sort'])) {
            $this->categoryKey = 'APP_CATEGORY_ALL';
        }
        $sort = $_GET['sort'] ? $_GET['sort'] : 'latest';
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');
        $popularInfo = OpenSocialApp::find(0, 1, 'popular');
        $ratingInfo = OpenSocialApp::find(0, 1, 'rating');
        $this->superCategories = array(
            array('category' => xg_text('MOST_POPULAR'),
                  'categoryUrl' => $this->_buildUrl('application', 'list', array('sort' => 'popular')),
                  'total' => $popularInfo['numApps']),
            array('category' => xg_text('HIGHEST_RATED'),
                  'categoryUrl' => $this->_buildUrl('application', 'list', array('sort' => 'rating')),
                  'total' => $ratingInfo['numApps']));
        $appInfo = OpenSocial_ApplicationDirectoryHelper::getApplicationDirectoryInfo($this->searchTerm, $this->categoryKey, $begin, $end, $sort);
        $this->numApps = $appInfo['total'];
        foreach ($appInfo['categories'] as &$category) {
            if ($category['category'] == 'APP_CATEGORY_ALL') $this->totalCount = $category['total'];
            $category['categoryUrl'] = $this->_buildUrl('application', 'list', array('category' => $category['category']));
        }
        // Next two lines transfer All, Recommended from categories -> super categories
        // TODO: clean this up somehow [dkf 2008-10-06]
        $this->categories = array_slice($appInfo['categories'], 2);
        $this->superCategories = array_merge(array_slice($appInfo['categories'], 0, 2), $this->superCategories);
        $this->apps = $appInfo['apps'];
        $this->screenName = XN_Profile::current()->screenName;
    }
    
    /**
     * Display reviews for the specified app.
     *
     * Expected GET variables:
     *     'appUrl' => URL for application to show reviews for.
     *
     * Optional GET variables:
     *      'page' => Page number for pagination.
     */
    public function action_reviews($errors=NULL, $appUrl=NULL) {
        $this->appUrl = $_GET['appUrl'] ? $_GET['appUrl'] : $appUrl;
        if (! $this->appUrl) { $this->redirectTo('list'); }
        $this->errors = $errors;
        $this->pageSize = 10;
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $begin = XG_PaginationHelper::computeStart($_GET['page'], $this->pageSize);
        $end = $begin + $this->pageSize;
        $this->setupReviews($this->appUrl, $begin, $end);
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        $prefs = OpenSocial_GadgetHelper::readGadgetUrl($this->appUrl);
        $this->appName = $prefs['title'];
        $this->aboutPageUrl = $this->_buildUrl('application', 'about', array('appUrl' => $this->appUrl));
    }
    
    /**
     * Add the specified review to the content store.  Current user must be logged in.
     *
     * Expected POST variables:
     *     'appUrl' => URL of the application being reviewed
     *     'rating' => Float of rating for the application
     *     'body' => Body of the review
     */
    public function action_addReview() {
        $appUrl = $_POST['appUrl'];
        if (! $appUrl) { $this->redirectTo('list', 'application'); }
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_SecurityHelper::redirectToSignInPageIfSignedOut($this->_buildUrl('reviews', 'application', array('appUrl' => $appUrl)));
        if ($this->reviewErrors($_POST)) {
            $json = new NF_JSON();
            echo $json->encode(array('errors' => $this->reviewErrors($_POST), 'appUrl' => $appUrl));
            return;
        }
        $app = OpenSocialApp::load($appUrl, TRUE /* create if does not exist */);
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        $prefs = OpenSocial_GadgetHelper::readGadgetUrl($appUrl);
        $reviewsInfo = OpenSocialAppReview::load($appUrl, XN_Profile::current()->screenName);
        if ($reviewsInfo['numReviews']) {
            $reviewsInfo = OpenSocialAppReview::load($appUrl, XN_Profile::current()->screenName);
            $reviews = $reviewsInfo['reviews'];
            $review = $reviews[0];
            $oldrating = $review->my->rating;
            $review->my->rating = (int)$_POST['rating'];
            $review->my->body = $_POST['body'];
            $review->save();
            XG_Query::invalidateCache('opensocialappreview-' . md5($appUrl));
            $app->my->avgRating = (($app->my->avgRating * $app->my->numReviews) + $_POST['rating'] - $oldrating) / $app->my->numReviews;
            $app->save();
        } else {
            $review = OpenSocialAppReview::create($appUrl, XN_Profile::current()->screenName, $_POST['rating'], $_POST['body']);
            $app->my->avgRating = (($app->my->avgRating * $app->my->numReviews) + $_POST['rating']) / ($app->my->numReviews + 1);
            $app->my->numReviews = $app->my->numReviews + 1;
            $review->save();
            $app->save();
        }
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        // BAZ-10775 This will remove previous log items for the same user/app combo before adding the new activity log item [Thomas David Baker 2008-10-08]
        XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_OPENSOCIAL, XG_ActivityHelper::SUBCATEGORY_APP_REVIEW,
                                                $review->contributorName, array($review), $prefs['title'],
                                                null /* widgetName */, null /* title */, $appUrl);

        $reviewReread = OpenSocialAppReview::loadById($review->id);  // to get the new updatedDate for the review
        ob_start();
        $this->renderPartial('fragment_eachReview', 'application', array('review' => $reviewReread));
        $reviewHtml = ob_get_contents();
        ob_end_clean();

        $ret = array('headerHtml' => $this->reviewsHeader($app->my->numReviews), 'avgRatingHtml' => xg_rating_image($app->my->avgRating),
                     'reviewHtml' => $reviewHtml, 'reviewId' => $review->id);
        
        $json = new NF_JSON();
        echo $json->encode($ret);
    }

    /**
     * Lookup the user's in the content store.  Current user must be logged in.
     *
     * Expected POST variables:
     *     'appUrl' => URL of the application being reviewed
     */
    public function action_lookupReview() {
        $appUrl = $_GET['appUrl'];
        if (! $appUrl) { $this->redirectTo('list', 'application'); }
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        XG_SecurityHelper::redirectToSignInPageIfSignedOut($this->_buildUrl('reviews', 'application', array('appUrl' => $appUrl)));
        //XXX will this work? the return value of OpenSocialAppReview::load is array('numReviews' => blah, 'reviews' => blah) ...
        $reviews = OpenSocialAppReview::load($appUrl, XN_Profile::current()->screenName);
        $json = new NF_JSON();
        echo $json->encode($reviews);  // count should be 1 or 0
    }
    
    /**
     * Validates the specified parameters for creating a review.
     *
     * @param   $args   Array containing 'body' and 'rating' keys.
     * @return          Array of i18n-ized string errors for display to user.  Empty array if no errors.
     */
    public function reviewErrors($args) {
        extract($args);
        $errors = array();
        if (! $body) {
            $errors['body'] = xg_html('MUST_SUPPLY_REVIEW');
        } else if (mb_strlen($body) > OpenSocialAppReview::MAX_BODY_CHARS) {
            $errors['body'] = xg_html('REVIEW_IS_TOO_LONG', mb_strlen($body));
        }
        if (! $rating || $rating < OpenSocialAppReview::MIN_RATING || $rating > OpenSocialAppReview::MAX_RATING) {
            $errors['rating'] = xg_html('MUST_SUPPLY_RATING');
        }
        return $errors;
    }
    
    public function reviewsHeader($numReviews) {
        return $numReviews ? xg_html('REVIEWS_N', xg_number($numReviews)) : xg_html('NO_REVIEWS');
    }
    
    /**
     * Delete the specified review.  Must be submitted via POST (destructive operation) but with vars in the querystring (see below).
     *
     * Expected POST variables:
     *     'reviewId' => ID of review to delete.
     */
    public function action_deleteReview() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a post (827589)'); }
        $review = OpenSocialAppReview::loadById($_POST['reviewId']);
        $appUrl = $review->my->appUrl;
        $app = OpenSocialApp::load($appUrl);
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_SecurityHelper.php');
        if (! $review || ! $app || !OpenSocial_SecurityHelper::currentUserCanDeleteReview($review)) {
            throw new Exception("Unable to delete review with ID '" . $_POST['reviewId'] . "' as " . XN_Profile::current()->screenName);
        }
        if ($app->my->numReviews - 1 == 0) {
            $newRating = 0;
        } else {
            $newRating = (($app->my->numReviews * $app->my->avgRating) - $review->my->rating) / ($app->my->numReviews - 1);
        }
        $app->my->numReviews = $app->my->numReviews - 1;
        $app->my->avgRating = $newRating;
        $app->save();
        XN_Content::delete($review);
        XG_Query::invalidateCache('opensocialappreview-' . md5($appUrl));
        
        $ret = array('headerHtml' => $this->reviewsHeader($app->my->numReviews), 'avgRatingHtml' => xg_rating_image($app->my->avgRating));
        $json = new NF_JSON();
        echo $json->encode($ret);
    }

    /**
     * Page that allows addition of OpenSocial Gadget to My Page via URL.
     */
    public function action_addByUrl() {
        $this->pageOwner = $this->_user;
        $this->title = xg_text('ADD_APPLICATION_BY_URL');
        $this->addAppUrl = $this->_buildUrl('application', 'add');
        $this->form = new XNC_Form(array('appUrl' => $_GET['appUrl'], 'installedByUrl' => true));
    }
    
    /**
     * Add the specified gadget to the current user's My Page.
     *
     * Expected POST vars:
     *
     *  'appUrl' => URL of application to add.
     *
     * Passing appUrl as a GET variable is also supported, to allow new users to sign up and add a gadget.
     * Bazel should only POST into this action however.
     */
    public function action_add() {
        $appUrl = trim($_REQUEST['appUrl']);
        if (! XN_Profile::current()->isLoggedIn()) {
            XG_App::includeFileOnce('/lib/XG_AuthorizationHelper.php');
            $this->redirectTo(XG_AuthorizationHelper::signUpUrl($this->_buildUrl('application','add', array("appUrl" => $appUrl))));
            return;
        } else if (! $appUrl) {
            $errMsg = ($_REQUEST['installedByUrl'] ? 'ADD_APPLICATION_ERROR_NO_APP_URL_SUPPLIED' : 'ADD_APPLICATION_ERROR_FAILED_TO_INSTALL');
            $this->errorRedirect($errMsg);
        }
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php');
        $addApp = OpenSocial_GadgetHelper::addApplication($appUrl, isset($_REQUEST['installedByUrl']));
        if ($addApp === TRUE) {
            //XXX are we expecting this to create the app data?  because it doesn't fulfil that function any more i don't think
            $app = OpenSocialAppData::load($appUrl, XN_Profile::current()->screenName);
            XG_Query::invalidateCache('opensocial-num-users-' . md5($appUrl));
            XG_Query::invalidateCache('action-people-members-' . md5($appUrl));
            XG_Query::invalidateCache('action-people-friends-' . md5($appUrl));
            $this->redirectTo(OpenSocial_LinkHelper::getInstalledApplicationRedirect($app));
        } else if ($addApp == "ADD_APPLICATION_ERROR_ALREADY_INSTALLED") {
            $app = OpenSocialAppData::load($appUrl, XN_Profile::current()->screenName);
            $this->redirectTo(OpenSocial_LinkHelper::getAlreadyInstalledApplicationRedirect($app));
        } else {
            $this->errorRedirect($addApp);
        }
    }
    
    /**
     * Generic error page for displaying application-related errors when displaying on the page they relate to is inappropriate.
     */
    public function action_error() {}
    
    /**
     * Redirect the user to the appropriate screen with specified error message.
     *
     * @param   $errMsgKey  string  The key for the message catalog to the error message to display on the target page.
     * @param   $useReferer boolean true if the http_referer should be used as the page to redirect too, otherwise use the generic error page (action_error).
     * @return              void    Called for the redirection side effect.
     */
    public function errorRedirect($errMsgKey, $useReferer=true) {
        $url = ($_SERVER['HTTP_REFERER'] && $useReferer ? $_SERVER['HTTP_REFERER'] : $this->_buildUrl('application', 'error'));
        $url = XG_HttpHelper::addParameter($url, 'error', $errMsgKey);
        $this->redirectTo($url);
    }

    /**
     * Remove the specified gadget from the current user's My Page
     *
     * Expected POST vars:
     *
     * 'appUrl' => url of application to remove
     */
    public function action_removeFromMyPage() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (49123)'); }
        $this->checkRemoveOk($_POST['appUrl']);
        $appData = OpenSocialAppData::load($_POST['appUrl'], XN_Profile::current()->screenName);
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        OpenSocial_GadgetHelper::removeApplicationFromMyPage($appData);
        //TODO: pass message to the profile page
        $this->redirectTo(XG_HttpHelper::profileUrl($this->_user->screenName));
    }

    /**
     * Remove the specified application for the current user.
     *
     * Expected POST vars:
     *
     * 'appUrl' => url of the application to remove.
     */
    public function action_remove() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (47890123)'); }
        $this->checkRemoveOk($_POST['appUrl']);
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        OpenSocial_GadgetHelper::removeApplication($_POST['appUrl']);
        //TODO: pass message to the profile page
        $this->redirectTo(XG_HttpHelper::profileUrl($this->_user->screenName));
    }
    
    /**
     * Check that the current user is able to remove the specified app.  If not, redirect to the appropriate error screen.
     *
     * @param   $appUrl string  URL of app to be removed.
     * @return          void    Called for possible redirection side effect only.
     */
    public function checkRemoveOk($appUrl) {
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        if (! $appUrl) {
            $this->errorRedirect('CANNOT_REMOVE_APPLICATION_AT_THIS_TIME');
        } else if (! XN_Profile::current()->isLoggedIn()) {
            //TODO: Do we want to redirect to sign in page here instead, with appropriate remove or removeFromMyPage target? [Thomas David Baker 2008-10-07] 
            $this->errorRedirect('MUST_BE_LOGGED_IN_TO_REMOVE_APP');            
        } else if (! OpenSocial_GadgetHelper::isApplicationInstalled($_REQUEST['appUrl'])) {
            $this->errorRedirect('APP_IS_NOT_INSTALLED');
        }
    }

    /**
     * Displays information about a gadget
     *
     * Expected GET variables
     *     appUrl - url of the gadget
     */
    public function action_about(){
        $this->_widget->includeFileOnce("/lib/helpers/OpenSocial_GadgetHelper.php");
        $this->gadgetPrefs = OpenSocial_GadgetHelper::readGadgetUrl($_GET['appUrl']);
        
        // this could happen from a user manipulating the query string or from an xml not being served any more
        if ($this->gadgetPrefs === false) {
            return $this->redirectTo(W_Cache::getWidget('opensocial')->buildUrl('application', 'error', array('error' => 'UNABLE_TO_DISPLAY_APP')));
        }

        $this->appUrl = $_GET['appUrl'];
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');
        $this->appDetails = OpenSocial_ApplicationDirectoryHelper::getAppDetails($this->appUrl);
        $this->currentUserHasApp = OpenSocialAppData::load($this->appUrl, XN_Profile::current()->screenName);
        $this->showStats = $this->currentUserHasApp || ($this->appDetails && $this->appDetails['approved']);
        
        if ($this->showStats) {
            $this->numFriends = OpenSocial_GadgetHelper::numFriends($this->appUrl);
            $this->numMembers = OpenSocial_GadgetHelper::numMembers($this->appUrl);
            $this->membersUrl = $this->_buildUrl('application', 'people', array('show' => 'members', 'appUrl' => $this->appUrl));
            $this->friendsUrl = $this->_buildUrl('application', 'people', array('show' => 'friends', 'appUrl' => $this->appUrl));
            $this->author = $this->gadgetPrefs['author'];
            $this->authorLink = $this->gadgetPrefs['title_url'];
            $this->showAddToMyPageLink = false;
            if ($this->appDetails) {
                $this->category = $this->appDetails['category'];
                $this->averageRating = $this->appDetails['averageRating'];
                $this->showAddToMyPageLink = $this->appDetails['approved'];
            }
            $this->showAddToMyPageLink = $this->showAddToMyPageLink && (! $this->currentUserHasApp);
            $app = OpenSocialApp::load($this->appUrl);
            $this->avgRating = $app->my->avgRating;
            $this->setupReviews($this->appUrl, 0, 3);
            $this->allReviewsUrl = $this->_buildUrl('application', 'reviews', array('appUrl' => $this->appUrl));
        }

        $this->reportUrl = W_Cache::getWidget('main')->buildUrl('index', 'reportAbuse', array('appUrl' => $this->appUrl, 'appTitle' => $this->gadgetPrefs['title']));
        $this->appName = $this->gadgetPrefs['title'];
    }
    
    /**
     * Canvas view of a gadget
     *
     * Expected GET variables
     *    appUrl - url of gadget
     *    owner - name of the owner of the gadget
     */
    public function action_show(){
        $this->openSocialView = "canvas";
        
        $this->appData = OpenSocialAppData::load($_GET['appUrl'], $_GET['owner']);
        if($this->appData == null){ $this->redirectTo('about', 'application', array('appUrl' => $_GET['appUrl'])); }

        $this->setValuesUrl = $this->_buildUrl('application', 'updateSettings', array('appUrl' => $this->appData->my->appUrl));
        
        $ownerName = XN_Profile::load($_GET['owner'])->screenName;
        
        //TODO: refactor this code and EmbedController out to a helper
        $this->_widget->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        // URL for the OpenSocial Rendercore
        $this->renderUrl = "http://". OpenSocial_GadgetHelper::getOsocDomain();
        $this->baseUrl   = $this->renderUrl."/gadgets";
        $this->socialDataUrl = $this->renderUrl."/social/data";
        $this->appUrl = $this->appData->my->appUrl;
        $this->aboutUrl = $this->_widget->buildUrl("application", "about", "?id=" . urlencode($this->appUrl));
        $this->canvasUrl = $this->_widget->buildUrl("application", "show", array("appUrl" => $this->appUrl, "owner" => $ownerName));
        $this->localDomain = $_SERVER['HTTP_HOST'];
        $this->gadgetPrefs = OpenSocial_GadgetHelper::readGadgetUrl($this->appUrl);
        $this->title = strval($this->gadgetPrefs['title']);
        $this->viewParams = isset($_GET['view-params']) ? $_GET['view-params'] : '';
        $this->_widget->includeFileOnce('/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');
        $this->blocked = OpenSocial_ApplicationDirectoryHelper::isBlocked($this->appUrl, $this->appData->my->installedByUrl);
                                                    
        // Gadget constructor.
        $this->gadget = new OpenSocialGadget(0, $this->localDomain, $this->appUrl, OpenSocial_GadgetHelper::currentViewerName(), $ownerName);
    }
    
    /**
     * Update the settings for a particular OpenSocial app.
     *
     * Expected GET variables:
     *     appUrl - URL of gadget XML for the app.
     * 
     * Expected POST variables:
     *     isOnMyPage - boolean for if the app should be appear on My Page.
     *     canAddActivities - boolean for if the app is allowed to add activities on behalf of the current user. 
     *     canSendMessages - boolean for if the app is allowed to send messages on behalf of the current user.
     */
    public function action_updateSettings() {
        $appUrl = $_GET['appUrl'];
        $userName = XN_Profile::current()->screenName;
        $this->result = OpenSocialAppData::updateSettings($appUrl, $userName, $_POST);
    }
    
    /**
     * Display members that have the specified application installed.
     *
     * Expected GET variables:
     *     'appUrl' => URL of application in question
     *     'show' => Either 'friends' or 'members' to show friends with the app installed, or all members with the app installed.
     */
    public function action_people() {
        $keyword = ($_GET['show'] == 'friends' ? 'FRIENDS' : 'MEMBERS');
        if (! $_GET['appUrl']) {
            $this->errorRedirect('UNABLE_TO_DISPLAY_' . $keyword . '_AT_THIS_TIME');
        }
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        $prefs = OpenSocial_GadgetHelper::readGadgetUrl($_GET['appUrl']);
        $query = XG_Query::create('Content')->filter('owner')->filter('type', '=', 'OpenSocialAppData')
            ->filter('my->appUrl', '=', $_GET['appUrl'])->setCaching('action-people-' . $_GET['show'] . '-' . md5($_GET['appUrl']));
        if ($_GET['show'] == 'friends') {
            $query->filter('contributorName', 'in', XN_Query::FRIENDS(XN_Profile::current()->screenName));
        }
        $results = $query->execute();
        $screenNames = array();
        foreach ($results as $result) {
            $screenNames[] = $result->my->user;
        }
        $this->users = User::loadMultiple($screenNames);
        $this->numUsers = $query->getTotalCount();
        if ($prefs && $prefs['title']) {
            $this->pageTitle = xg_text($keyword . '_WITH_APPNAME', $prefs['title']);
        } else {
            $this->pageTitle = xg_text($keyword . '_WITH_APPLICATION');
        }
        $this->pageSize = 30;
        $this->highlightedTab = 'members';
    }
    
    /**
     * Setup the basic vars required to show some reviews.
     *
     * @param   $appUrl string  URL of app to setup reviews of.
     * @param   $begin  int     0-based index for review to start from.
     * @param   $end    int     1-based index for review to end at.
     * @return          void
     */
    public function setupReviews($appUrl, $begin, $end) {
        $reviewInfo = OpenSocialAppReview::find($appUrl, $begin, $end);
        $this->numReviews = $reviewInfo['numReviews'];
        $this->reviews = $reviewInfo['reviews'];
        $this->deleteReviewUrl = $this->_buildUrl('application', 'deleteReview');
        $this->addReviewUrl = $this->_buildUrl('application', 'addReview');
        $this->lookupReviewUrl = $this->_buildUrl('application', 'lookupReview', array('appUrl' => $appUrl));
        $this->form = new XNC_Form(array('appUrl' => $appUrl));
    }
}

?>
