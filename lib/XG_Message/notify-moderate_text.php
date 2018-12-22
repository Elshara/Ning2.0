<?php
/**
 * Message template for notifying the app owner that there is content to moderate
 *
 * @param $content XN_Content Content object to moderate
 * @param $reason string the reason for the message
 * @param $moderationUrl string The URL to perform moderation
 * @param $contentAdder string The XN_Profile object of the user that added the content
 * @param $thumb string optional thumbnail URL to include
 * @param $type string What type to moderate, to go in the sentence "You have a new X to moderate!"
 */
if (mb_strlen($content->title)) {
    $intro = xg_text('YOU_HAVE_A_NEW_X_TITLE_TO_APPROVE_ON_Y', $type, $content->title, $message['appName']);
} else {
    $intro = xg_text('YOU_HAVE_A_NEW_X_TO_APPROVE_ON_Y', $type, $message['appName']);
}

echo "$intro\n\n";

echo xg_text('TO_APPROVE_THIS_X_VISIT', $type) . "\n";
echo "$moderationUrl\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
