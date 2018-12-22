<?php
/** Message template for a notification that somebody has sent you a message
 *
 * @param $profile XN_Profile The profile of the message sender
 * @param $body string The message from the user
 */

echo xg_text('USER_HAS_SENT_YOU_A_MESSAGE_ON_X', xg_username($profile), $message['appName']) . "\n\n";

echo "$body\n\n";

$recipientProfile = XG_Cache::profiles($message['to']);
if ($recipientProfile && User::isMember($recipientProfile)) {
    echo xg_text('TO_REPLY_CLICK_HERE_COLON'), ' ', W_Cache::getWidget('profiles')->buildUrl('message', 'listInbox') . "\n\n";
} else {
    echo xg_text('TO_JOIN_CLICK_HERE'), ' ', XG_AuthorizationHelper::signUpUrl(xg_absolute_url('/'), NULL, $message['to']) . "\n\n";
}

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
