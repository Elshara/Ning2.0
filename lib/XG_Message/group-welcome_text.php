<?php
/**
 * Message template for a notification that you've joined a group
 *
 * @param $profile XN_Profile  the profile of the new user
 * @param $group XN_Content|W_Content  the Group
 */

echo xg_text('WELCOME_TO_THE_GROUP_X_ON_Y', $group->title, $message['appName']) . "\n\n";

echo xg_text('TO_VIEW_THIS_GROUP_VISIT') . "\n";
echo XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id)) . "\n\n";
echo $message['appName'] . "\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
