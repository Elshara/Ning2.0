<?php
/** Message template for an invitation request
 *
 * @param $body string The message from the sender
 * @param $fromName string The name of the sender
 * @param $thumbUrl string optional profile thumbnail url for logged-in users that are requesting
 * @param $inviteUrl string URL for the app owner to invite the person
 */

echo xg_text('X_HAS_REQUESTED_AN_INVITATION', $fromName, $message['appName']) . "\n\n";

echo $body . "\n\n";

echo xg_text('SEND_AN_INVITATION_TO_X', $fromName) . "\n";
echo $inviteUrl . "\n\n";

echo "--\n";
echo xg_text('YOU_HAVE_RECEIVED_BECAUSE_X', xg_text('SOMEBODY_REQUESTED_JOIN_X', $message['appName'])) . ".\n";
echo xg_text('IF_YOU_NO_LONGER_WISH_FROM_X_CLICK_Y', $message['appName'], '') . "\n";
echo $message['unsubscribeUrl'];