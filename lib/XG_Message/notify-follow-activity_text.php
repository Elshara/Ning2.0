<?php
/**
 * Message template for notifying someone of activity on a content object they're
 *   following
 *
 * @param $activity string What the activity was
 * @param $content XN_Content Content object the activity was on
 * @param $reason string the reason for the message
 * @param $url string the target URL for the message
 * @param $thumb string optional thumbnail URL to include
 * @param $viewActivity string Text introducing a link to the activity
 */

echo $activity . "\n\n";

echo $viewActivity . "\n";
echo "$url\n\n";

echo "--\n";
echo xg_text('TO_STOP_FOLLOWING_THIS_X_GO_TO', mb_strtolower($type)) . "\n";
echo $unfollowLink;