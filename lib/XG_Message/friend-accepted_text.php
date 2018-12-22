<?php
/** Message template for a notification that somebody has accepted one of your friend requests
 *
 * @param $profile XN_Profile The profile of the friend request accepter
 */

echo xg_text('X_HAS_ACCEPTED_YOUR_FRIEND_REQUEST', xg_username($profile), $message['appName']) . "\n\n";

echo xg_text('VIEW_XS_PAGE_ON_Y', xg_username($profile), $message['appName']) . "\n";
echo 'http://' . $_SERVER['HTTP_HOST'] . User::profileUrl($profile->screenName) . "\n\n";

if (mb_strlen($message['appDescription'])) {
    echo xg_text('ABOUT_X', $message['appName']) . "\n";
    echo $message['appDescription'] . "\n\n";
}

echo "--\n";
echo xg_text('YOU_HAVE_RECEIVED_BECAUSE_X', xg_text('MEMBER_OF_X_ACCEPTED', $message['appName'])) . ".\n";
echo xg_text('IF_YOU_NO_LONGER_WISH_FROM_X_CLICK_Y', $message['appName'], '') . "\n";
echo xg_text('IF_NO_LONGER_WISH_FRIEND_NOTIFICATION_EMAIL_FROM_X_CLICK_Y', $message['appName'], '') . "\n";
echo $message['unsubscribeUrl'];
