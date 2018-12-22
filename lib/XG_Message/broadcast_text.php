<?php
/**
 * Message template for a generic message
 *
 * @param $body string
 *
 * The message can also use the message properties set in $message
 */

echo xg_text('MESSAGE_TO_ALL_MEMBERS_OF_X', $message['appName']) . "\n\n";

echo "$body\n\n";

echo xg_text('VISIT_X_AT_Y', $message['appName'], "http://" . $_SERVER['HTTP_HOST']) . "\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
