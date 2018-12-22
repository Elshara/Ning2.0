<?php
/**
 * Notification that a discussion has been started
 *
 * @param $topic XN_Content|W_Content  the discussion topic
 * @param $url string  the URL of the discussion
 * @param $unsubscribeUrl string  the URL of the page for stopping this notification
 * @param $message array  basic predefined values
 */
echo xg_text('X_STARTED_THE_DISCUSSION_Y', XG_UserHelper::getFullName(XG_Cache::profiles($topic->contributorName)), $topic->title) . "\n\n";

echo xg_text('TO_VIEW_THIS_DISCUSSION_GO_TO') . "\n";
echo "$url\n\n";

echo "--\n";
echo xg_text('TO_STOP_BEING_NOTIFIED_OF_NEW_DISCUSSIONS_GO_TO') . "\n";
echo $unsubscribeUrl;
