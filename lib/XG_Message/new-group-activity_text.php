<?php
/**
 * Notification of new group activity
 *
 * @param $object XN_Content|W_Content  the new activity object for the group; a topic, comment wall comment, or new group member
 * @param $group XN_Content|W_Content the group object
 * @param $url string  the URL of the activity
 * @param $unsubscribeUrl string  the URL of the page for stopping this notification
 * @param $message array  basic predefined values
 */
switch($object->type) {
    case 'Topic':
        $title = xg_text('X_STARTED_DISCUSSION_IN_GROUP_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($object->contributorName)), $group->title, XN_Application::load()->name);
        break;
    case 'Comment':
        $title = xg_text('X_LEFT_COMMENT_IN_GROUP_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($object->contributorName)), $group->title, XN_Application::load()->name);
        break;
    case 'GroupMembership':
        $title = xg_text('X_JOINED_GROUP_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($object->contributorName)), $group->title, XN_Application::load()->name);
        break;
}
echo $title . "\n\n";

echo xg_text('TO_VIEW_THIS_ACTIVITY_GO_TO') . "\n";
echo "$url\n\n";

echo "--\n";
echo xg_text('TO_STOP_FOLLOWING_GROUP_GO_TO') . "\n";
echo $unfollowUrl . "\n\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
