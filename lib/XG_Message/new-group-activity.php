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
        $title = xg_text('X_STARTED_DISCUSSION_IN_GROUP_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($object->contributorName)), xnhtmlentities($group->title), XN_Application::load()->name);
        break;
    case 'Comment':
        $title = xg_text('X_LEFT_COMMENT_IN_GROUP_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($object->contributorName)), xnhtmlentities($group->title), XN_Application::load()->name);
        break;
    case 'GroupMembership':
        $title = xg_text('X_JOINED_GROUP_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($object->contributorName)), xnhtmlentities($group->title), XN_Application::load()->name);
        break;
}?>
<div class="xg_body">
    <h3><%= $title %></h3>
    <table width="100%">
        <tr>
            <td>
                <p><%= xg_html('TO_VIEW_THIS_ACTIVITY_GO_TO'); %><br />
                    <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url); %></a></p>
            </td>
        </tr>
    </table>
    <p class="smallprint"><small>
        <%= xg_html('TO_STOP_FOLLOWING_GROUP_GO_TO') %><br />
            <a href="<%= $unfollowUrl %>"><%= $unfollowUrl %></a><br/>
            <?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?><br/><br/>
            <%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
            <a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
    </small></p>
</div>
