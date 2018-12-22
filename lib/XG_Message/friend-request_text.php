<?php
/** Message template for a notification that somebody has sent you a friend request
 *
 * @param $body string  the message from the sender
 * @param $profile XN_Profile The profile of the friend request sender
 * @param $isMember boolean Whether or not the user is a member of the network the request is sent from
 */
$url = W_Cache::getWidget('profiles')->buildUrl('friendrequest', 'listReceived', array('c' => 1));
echo xg_text('X_HAS_ADDED_YOU_AS_A_FRIEND_ON_Y', xg_username($profile), $message['appName']) . "\n\n";

if ($body) {
    echo "$body\n\n";
}

echo xg_text('TO_ACCEPT_THIS_FRIEND_REQUEST_VISIT') . "\n";
echo $url . "\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
