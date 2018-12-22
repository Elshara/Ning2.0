<?php
/** Message template for a notification that somebody you invited has joined the app
 *
 * @param $joiner XN_Profile The profile of the person that has joined the app
 */

echo xg_text('X_IS_NOW_A_MEMBER_OF_Y', xg_username($joiner), $message['appName']) . "\n\n";

echo xg_text('WANT_TO_ADD_A_WELCOME_MESSAGE_FOR_X_ON_THEIR_PROFILE_GO_TO', xg_username($joiner)) . "\n";
echo 'http://' . $_SERVER['HTTP_HOST'] . User::profileUrl($joiner->screenName) . "\n\n";

echo "\n" . $message['appName'] . "\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
