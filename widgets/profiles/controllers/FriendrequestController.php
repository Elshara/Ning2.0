<?php

/**
 * Dispatches requests pertaining to friend requests.
 */
class Profiles_FriendrequestController extends XG_BrowserAwareController {

    /**
     * Runs code before each action.
     */
    protected function _before() {
        XG_HttpHelper::trimGetAndPostValues();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_HtmlHelper.php');
		$this->tab = 'members';
    }

    /**
     * Displays a list of received friend requests.
     *
     * Expected GET variables:
     *     - page - page number (optional)
     *     - c - 1 to clear the friend-request cache (optional)
     */
    public function action_listReceived() {
        $this->prepareListAction('listReceived');
        $this->friendLimitExceeded = Profiles_FriendHelper::instance()->willFriendLimitBeExceeded(1);
        $this->friendLimitExceededMessage = xg_text('REACHED_LIMIT_N_FRIENDS', XG_App::constant('Profiles_FriendHelper::FRIEND_LIMIT'));
        $this->friendRequestMessages = FriendRequestMessage::getMessagesFrom(User::screenNames($this->profiles));
        $this->showAcceptAllLink = ! Profiles_FriendHelper::instance()->willFriendLimitBeExceeded($this->count);
        // TODO: Use getApproximateNumberOfFriendsOnNetworkFor() which is less expensive than numberOfFriendsOnNetwork() [Jon Aquino 2008-09-29]
        // TODO: Rename memberCount to numberOfFriendsOnNetwork [Jon Aquino 2008-09-29]
		$this->memberCount = Index_MessageHelper::numberOfFriendsOnNetwork($this->_user->screenName);
    }

    /**
     * Displays a list of sent friend requests.
     *
     * Expected GET variables:
     *     - page - page number (optional)
     */
    public function action_listSent() {
        $this->prepareListAction('listSent');
        $this->sentFriendRequestLimitExceeded = Profiles_FriendHelper::instance()->willSentFriendRequestLimitBeExceeded(1);
        $this->friendRequestMessages = FriendRequestMessage::getMessagesTo(User::screenNames($this->profiles));
        // TODO: Use getApproximateNumberOfFriendsOnNetworkFor() which is less expensive than numberOfFriendsOnNetwork() [Jon Aquino 2008-09-29]
        // TODO: Rename memberCount to numberOfFriendsOnNetwork [Jon Aquino 2008-09-29]
        $this->memberCount = Index_MessageHelper::numberOfFriendsOnNetwork($this->_user->screenName);
        $this->maxSentRequests = Profiles_FriendHelper::SENT_FRIEND_REQUEST_LIMIT;
    }

    /**
     * Initializes the listSent and listReceived actions.
     *
     * Expected GET variables:
     *     - page - page number (optional)
     *     - c - 1 to clear the friend-request cache (optional)
     *
     * @param $name string  listSent or listReceived
     */
    private function prepareListAction($actionName) {
        XG_SecurityHelper::redirectToSignInPageIfSignedOut();
        if ($_GET['c']) {
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_NetworkSpecificFriendRequestHelper.php');
            Profiles_NetworkSpecificFriendRequestHelper::instance()->invalidateFriendRequestsCache(XN_Profile::current()->screenName);
        }
        $this->pageSize = 20;
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->page = $_GET['page'];
        $start = XG_PaginationHelper::computeStart($this->page, $this->pageSize);
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        if ($actionName == 'listSent') {
            list($this->friendRequests, $this->count) = Profiles_FriendHelper::instance()->getSentFriendRequests($start, $start + $this->pageSize);
        } else {
            list($this->friendRequests, $this->count) = Profiles_FriendHelper::instance()->getReceivedFriendRequests($start, $start + $this->pageSize);
        }
        $screenNames = array();
        foreach ($this->friendRequests as $friendRequest) {
            $screenNames[] = $friendRequest['screenName'];
        }
        $this->profiles = XG_Cache::profiles($screenNames);
        if (! $this->friendRequests && $this->page > 1) { return $this->redirectTo($actionName, 'friendrequest', array('page' => $this->page - 1)); }
        if (! $this->count) { return $this->redirectTo($this->myFriendsUrl()); }
    }

    /**
     * Returns the URL for the My Friends page.
     *
     * @param string  the My Friends URL for the current user.
     */
    private function myFriendsUrl() {
        return xg_absolute_url('/friends/' . User::profileAddress($this->_user->screenName));
    }

    /**
     * Checks if the current user has too many friends on this network.
     * Returns a JSON object with friendLimitExceeded and sentFriendRequestLimitExceeded.
     *
     * Expected GET variables:
     *     - xn_out - "json"
     */
    public function action_friendLimitExceeded() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        $this->friendLimitExceeded = Profiles_FriendHelper::instance()->willFriendLimitBeExceeded(1);
        $this->sentFriendRequestLimitExceeded = Profiles_FriendHelper::instance()->willSentFriendRequestLimitBeExceeded(1);
    }

    /**
     * Accepts, withdraws, or ignores the given friend request.
     *
     * Expected GET variables:
     *     - screenName - the name of the other user
     *     - page - page number (optional)
     *     - xn_out - "json" if this is an Ajax request
     *
     * Expected REQUEST variables:
     *     - accept - whether to accept the other user's friend request
     *     - ignore - whether to ignore the other user's friend request
     *     - withdraw - whether to withdraw the friend request to the other user
     *     - acceptAll - whether to accept all friend requests received from other users
     *     - ignoreAll - whether to ignore all friend requests received from other users
     *     - withdrawAll - whether to withdraw all friend requests sent to other users
     */
    public function action_process() {
        if (! XN_Profile::current()->isLoggedIn()) { throw new Exception('Not signed in (398807651)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (529908148)'); }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        $this->success = TRUE;
        if ($_REQUEST['accept']) {
            if (Profiles_FriendHelper::instance()->willFriendLimitBeExceeded(1)) {
                $this->success = FALSE;
                $this->friendLimitExceeded = TRUE;
            } else {
                Profiles_FriendHelper::instance()->acceptFriendRequests(array($_GET['screenName']));
            }
            $redirectTo = 'listReceived';
        } elseif ($_REQUEST['ignore']) {
            Profiles_FriendHelper::instance()->ignoreFriendRequests(array($_GET['screenName']));
            $redirectTo = 'listReceived';
        } elseif ($_REQUEST['withdraw']) {
            Profiles_FriendHelper::instance()->withdrawFriendRequests(array($_GET['screenName']));
            $redirectTo = 'listSent';
        } elseif ($_REQUEST['acceptAll']) {
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
            if (Profiles_FriendHelper::instance()->willFriendLimitBeExceeded(Profiles_CachedCountHelper::instance()->getApproximateReceivedFriendRequestCount())) { throw new Exception('Friend limit exceeded (1555038006)'); }
            Profiles_FriendHelper::instance()->acceptAllFriendRequests();
            return $this->redirectTo($this->myFriendsUrl());
        } elseif ($_REQUEST['ignoreAll']) {
            Profiles_FriendHelper::instance()->ignoreAllFriendRequests();
            return $this->redirectTo($this->myFriendsUrl());
        } elseif ($_REQUEST['withdrawAll']) {
            Profiles_FriendHelper::instance()->withdrawAllFriendRequests();
            return $this->redirectTo($this->myFriendsUrl());
        }
        if ($_GET['xn_out'] == 'json') {
            ob_start();
            $this->renderPartial('fragment_accepted', 'friendrequest', array('profile' => XG_Cache::profiles($_GET['screenName'])));
            $this->html = trim(ob_get_contents());
            ob_end_clean();
        } else {
            $this->redirectTo($redirectTo, 'friendrequest', array('page' => $_GET['page']));
        }
    }

    /**
     * Creates a friend request from the current user to the given recipient.
     *
     * Expected GET parameters:
     *     - screenName - screen name of the user
     *     - xn_out - "json"
     *
     * Expected POST parameters:
     *     - message - optional plain-text message body
     */
    public function action_create() {
        if (! XN_Profile::current()->isLoggedIn()) { throw new Exception('Not signed in (1148743520)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (160014621)'); }
        if (mb_strpos($_GET['screenName'], '@') !== FALSE) { throw new Exception('Not a screen name (1501762795)'); }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        if (Profiles_FriendHelper::instance()->willFriendLimitBeExceeded(1)) { throw new Exception('Friend limit exceeded (2107976589)'); }
        Profiles_FriendHelper::instance()->createFriendRequest($_GET['screenName'], $_POST['message']);
        $this->success = TRUE;
    }

    /**
     * Creates a friend request from the current user to the given recipient. (iPhone-specific)
     * Returns the user back to the profile screen
     *
     * Expected GET parameters:
     *     - screenName - screen name of the user
     *
     * Expected POST parameters:
     *     - message - optional plain-text message body
     */
    public function action_create_iphone() {
        $this->action_create();
        $this->redirectTo('show', 'profile', array('screenName' => $_GET['screenName']));
    }

}
