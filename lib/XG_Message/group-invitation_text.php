<?php
/**
 * An invitation to a group
 *
 * @param $url string the target URL for the message
 * @param $body string  the message from the sender
 * @param $fromProfile XN_Profile  the person sending the invitation
 * @param $groupName string  name of the group
 * @param $groupDescription string  description of the group (optional)
 */
$username = xg_username($fromProfile->screenName);

echo xg_text('USER_HAS_INVITED_YOU_TO_JOIN_GROUP', $username, $groupName) . "\n\n";

if ($body) {
    echo "$body\n\n";
}

echo xg_text('CLICK_HERE_TO_JOIN') . "\n";
echo "$url\n\n";

echo xg_username($fromProfile->screenName) . "\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
