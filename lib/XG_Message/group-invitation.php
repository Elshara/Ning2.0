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
?>
<div class="xg_body">
    <table width="100%">
        <tr>
            <td>
                <h3><%= xg_html('USER_HAS_INVITED_YOU_TO_JOIN_GROUP', xnhtmlentities($username), xnhtmlentities($groupName)) %></h3>
                <?php if ($body) { ?>
                    <p><big><%= nl2br(xnhtmlentities($body)) %></big></p>
                <?php } ?>
                <p><%= xg_html('CLICK_HERE_TO_JOIN') %><br />
                    <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a></p>
                <p><%= xnhtmlentities($username) %></p>
            </td>
            <td class="picture"><img align="right" width="96" height="96" src="<%= xnhtmlentities(XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($fromProfile,96,96), 'xn_auth','no')) %>" alt="<%= xnhtmlentities(xg_username($fromProfile)) %>"></td>
        </tr>
    </table>
    <p class="smallprint"><small>
        <?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
        <%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
        <a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
    </small></p>
</div>
