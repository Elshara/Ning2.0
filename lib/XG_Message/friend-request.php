<?php
/** Message template for a notification that somebody has sent you a friend request
 *
 * @param $body string  the message from the sender
 * @param $profile XN_Profile The profile of the friend request sender
 * @param $isMember boolean Whether or not the user is a member of the network the request is sent from
 */
$url = W_Cache::getWidget('profiles')->buildUrl('friendrequest', 'listReceived', array('c' => 1)); ?>
    <div class="xg_body">
        <h3><%= xg_html('X_HAS_ADDED_YOU_AS_A_FRIEND_ON_Y', xnhtmlentities(xg_username($profile)),xnhtmlentities($message['appName'])) %></h3>
        <?php if ($body) { ?>
            <p><big><%= nl2br(xnhtmlentities($body)) %></big></p>
        <?php } ?>
        <table width="100%">
            <tr>
                <td>
                <p><big><%= xg_html('TO_ACCEPT_THIS_FRIEND_REQUEST_VISIT') %><br />
                    <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a></big></p>
                </td>
                <td><img align="right" width="96" height="96"  src="<%= xnhtmlentities(XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($profile,96,96),'xn_auth','no')) %>" alt="<%= xnhtmlentities(xg_username($profile)) %>"></td>
            </tr>
        </table>
        <p class="smallprint"><small>
            <?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
            <%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
            <a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
        </small></p>
    </div>
