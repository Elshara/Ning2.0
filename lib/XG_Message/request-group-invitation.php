<?php
/** Message template for an invitation request
 *
 * @param $body string  the message from the sender
 * @param $fromName string  the name of the sender
 * @param $thumbUrl string  optional profile thumbnail url for logged-in users that are requesting
 * @param $inviteUrl string  URL for the group admin to invite the person  TODO reinstate this once we have prettier looking invite URLs (not so long)
 * @param $groupName string  the name of the group
 * @param $profileUrl string URL of the requestor's profile.
 * @param $manageUrl string URL of the Manage Requested Invites page.
 */
?>
<div class="xg_body">
    <h3><%= xg_html('USER_HAS_REQUESTED_MEMBERSHIP_OF_GROUP_ON_X', xnhtmlentities($fromName), xnhtmlentities($groupName), xnhtmlentities($message['appName'])) %></h3>
    <table width="100%">
        <tr>
            <td>
                <p><%= nl2br(xnhtmlentities($body)) %></p>
                <p><%= xg_html('TO_APPROVE_OR_DENY_USER_REQUEST', xnhtmlentities($fromName)) %><br />
                    <a href="<%= xnhtmlentities($manageUrl) %>"><%= $manageUrl %></a></p>

                <p><%= xnhtmlentities($message['appName']) %></p>
            </td>
            <?php
            if (isset($thumbUrl)) { ?>
                <td><img align="right" width="96" height="96" src="<%= xnhtmlentities($thumbUrl) %>" alt="<%= xnhtmlentities($fromName) %>"></td>
            <?php
            } ?>
        </tr>
    </table>
    <p class="smallprint"><small>
        <?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
        <%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
        <a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
    </small></p>
</div>
