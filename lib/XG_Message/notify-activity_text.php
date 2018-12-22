<?php
/**
 * Message template for notifying someone of activity on a content object
 *
 * @param $activity string What the activity was
 * @param $content XN_Content Content object the activity was on
 * @param $reason string the reason for the message
 * @param $url string the target URL for the message
 * @param $thumb string optional thumbnail URL to include
 */
echo "$activity\n\n";

echo "$viewActivity\n";
echo "$url\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
