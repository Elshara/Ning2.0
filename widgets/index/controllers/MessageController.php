<?php
require_once dirname(__DIR__) . '/lib/helpers/Index_RequestHelper.php';

/**
 * Dispatches requests pertaining to composing messages to friends.
 */
class Index_MessageController extends W_Controller {

    /**
     * Displays a form for sending messages to friends.
     *
     * Expected GET variables:
     *     sent - whether messages were just sent
     */
    public function action_new() {
        $this->redirectTo(W_Cache::getWidget('profiles')->buildUrl('message', 'new'));
    }

    /**
     * Displays a list of friends for the current user.
     *
     * @param $args array:
     *     friendDataUrl - endpoint for retrieving friend info
     *     initialFriendSet - the initially selected set of friends: null, Index_MessageHelper::ALL_FRIENDS, or Index_MessageHelper::FRIENDS_ON_NETWORK
     *     numFriends - total number of friends that will appear in the list
     *     numSelectableFriends - number of friends that can be selected
     *     numSelectableFriendsOnNetwork - number of friends (on the current network) that can be selected (not used if showSelectFriendsOnNetworkLink is FALSE)
     *     showSelectAllFriendsLink - whether to show the "Select All Friends" link
     *     showSelectFriendsOnNetworkLink - whether to show the "Select Friends on this Network" link
     * @see FriendList.js
     */
    public function action_friendList($args) {
        foreach ($args as $key => $value) { $this->{$key} = $value; }
    }

    /**
     * Outputs JSON for "friends" (each with screenName, fullName, thumbnailUrl, isMember,
     * and optional reasonToDisable).
     *
     * Expected GET variables
     *     xn_out - "json";
     *     start - inclusive start index
     *     end - exclusive end index
     */
    public function action_friendData() {
        $this->_widget->includeFileOnce('/lib/helpers/Index_MessageHelper.php');

        [$start, $end] = Index_RequestHelper::readRange($_GET, 'start', 'end');
        $friendData = Index_MessageHelper::dataForFriendsOnNetwork($start, $end);
        $this->friends = $friendData['friends'];
    }

}


