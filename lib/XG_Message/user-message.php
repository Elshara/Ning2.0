<?php
/** Message template for a notification that somebody has sent you a message
 *
 * @param $profile XN_Profile The profile of the message sender
 * @param $body string The message from the user
 */
?>
<div class="xg_body">
    <h3><%= xg_html('USER_HAS_SENT_YOU_A_MESSAGE_ON_X', xnhtmlentities(xg_username($profile)), xnhtmlentities($message['appName'])) %></h3>
    <table width="100%">
        <tr>
            <td>
                <?php
                $recipientProfile = XG_Cache::profiles($message['to']);
                if ($recipientProfile && User::isMember($recipientProfile)) {
                    $url = W_Cache::getWidget('profiles')->buildUrl('message', 'listInbox');
                } else {
                    $url = XG_AuthorizationHelper::signUpUrl(xg_absolute_url('/'), NULL, $message['to']);
                } ?>
                <p><big><%= nl2br(xnhtmlentities($body)) %></big></p>
                <p><a href="<%= $url %>"><%= $url %></a></p>
            </td>
            <td><img align="right" width="96" height="96" src="<%= xnhtmlentities(XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($profile,96,96),'xn_auth','no')) %>" alt="<%= xnhtmlentities(xg_username($profile)) %>"></td>
        </tr>
    </table>
    <p class="smallprint"><small>
            <?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
            <%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
            <a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
    </small></p>
</div>
