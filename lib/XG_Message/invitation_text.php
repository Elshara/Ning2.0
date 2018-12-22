<?php
/** Message template for an invitation (text only version)
 *
 * @param $url string the target URL for the message
 * @param $body string The message from the sender
 * @param $fromProfile XN_Profile The person sending the invitation
 * @param $title string The title of the shared object, if it's a share-with-friends message
 * @param $thumb string optional preview url of the shared object, if it's a share-with-friends message
 * @param $type string optional type of the object being shared, if it's a share-with-friends message.
 */

echo xg_text('COME_JOIN_ME_ON_X', $message['appName']) . "\n\n";

if ($body) { echo $body . "\n\n"; }

echo xg_text('CLICK_HERE_TO_JOIN') . "\n";
echo "$url\n\n";

echo xg_username($fromProfile->screenName) . "\n";
