<?php
/** Message template for an invitation request
 *
 * @param $body string  the message from the sender
 * @param $fromName string  the name of the sender
 * @param $thumbUrl string  optional profile thumbnail url for logged-in users that are requesting
 * @param $inviteUrl string  URL for the group admin to invite the person TODO reinstate this once we have prettier looking invite URLs (not so long)
 * @param $groupName string  the name of the group
 * @param $profileUrl string URL of the requestor's profile.
 * @param $manageUrl string URL of the Manage Requested Invites page.
 */

echo xg_text('USER_HAS_REQUESTED_MEMBERSHIP_OF_GROUP_ON_X', $fromName, $groupName, $message['appName']) . "\n\n";

echo $body . "\n\n";

echo xg_text('TO_APPROVE_OR_DENY_USER_REQUEST', $fromName) . "\n";
echo $manageUrl . "\n\n";

echo $message['appName'] . "\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
