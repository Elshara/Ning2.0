<?php
/**
 * Message template for notifying the content creator that their content has been approved.
 *
 * @param $content XN_Content Content object that has been moderated
 * @param $type string What type has been moderated
 * @param $thumb string optional thumbnail URL to include
 */
if (mb_strlen($content->title) > 0) {
    $msg = xg_text('YOUR_X_Y_HAS_BEEN_APPROVED_ON_Z', $type, $content->title, $message['appName']);
} else {
    $msg = xg_text('YOUR_X_HAS_BEEN_APPROVED_ON_Y', $type, $message['appName']);
}

echo $msg . "\n\n";

echo xg_text('TO_VIEW_YOUR_X_VISIT', $type) . "\n";
echo 'http://' . $_SERVER['HTTP_HOST'] . '/xn/detail/' . $content->id . "\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
