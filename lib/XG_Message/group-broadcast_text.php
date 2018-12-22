<?php
/* Message template for a message sent to all members of a group
 */

echo xg_text('A_MESSAGE_FROM_USERNAME_TO_GROUPNAME_ON_APPNAME',
        xg_username($fromProfile), $group->title, $message['appName']) . "\n\n";

echo "$body\n\n";

echo xg_text('VISIT_GROUP_AT', $group->title) . "\n";
echo "$groupUrl\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
