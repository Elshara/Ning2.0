<?php
/** Message template for an invitation request
 *
 * @param $body string The message from the sender
 * @param $fromName string The name of the sender
 * @param $thumbUrl string optional profile thumbnail url for logged-in users that are requesting.  Ignored in plain text message.
 * @param $unblockUrl string URL for the app owner to unblock the person
 */

echo xg_text('A_BANNED_MEMBER_HAS_SENT_YOU_A_MESSAGE_ON_X_USERNAME_WRITES', $message['appName'], $fromName) . "\n\n";

echo "$body\n\n";

echo xg_text('TO_UNBAN_USERNAME_GO_TO', $fromName) . "\n";
echo $unblockUrl . "\n\n";
