<?php
/** Message template for a notification that somebody has accepted one of your friend requests
 *
 * @param $profile XN_Profile The profile of the friend request accepter
 */
?>
    <div class="xg_body">
        <h3><%= xg_html('X_HAS_ACCEPTED_YOUR_FRIEND_REQUEST', xnhtmlentities(xg_username($profile)), xnhtmlentities($message['appName'])) %></h3>
        <table width="100%">
            <tr>
                <td>
                    <p><big><a href="http://<%= $_SERVER['HTTP_HOST'] %><%= xnhtmlentities(User::profileUrl($profile->screenName)) %>"><%= xg_html('VIEW_XS_PAGE_ON_Y',xnhtmlentities(xg_username($profile)),xnhtmlentities($message['appName'])) %></a></big></p>
                <?php if (mb_strlen($message['appDescription'])) { ?>
                    <h4><%= xg_html('ABOUT_X', xnhtmlentities($message['appName'])) %></h4>
                    <p><%= nl2br(xnhtmlentities($message['appDescription'])) %></p>
                <?php } ?>
                </td>
                <td><img align="right" width="96" height="96" src="<%= xnhtmlentities(XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($profile,96,96),'xn_auth','no')) %>" alt="<%= xnhtmlentities(xg_username($profile)) %>"></td>
            </tr>
        </table>
        <p class="smallprint"><small>
            <%= xg_html('YOU_HAVE_RECEIVED_BECAUSE_X', xg_text('MEMBER_OF_X_ACCEPTED', xnhtmlentities($message['appName']))) %>.
            <%= xg_html('IF_NO_LONGER_WISH_FRIEND_NOTIFICATION_EMAIL_FROM_X_CLICK_Y', xnhtmlentities($message['appName']), 'href="'.xnhtmlentities($message['unsubscribeUrl']).'"') %>
        </small></p>
    </div>
