<?php
/* Message template for a message sent to all members of a group
 */
?>
    <div class="xg_body">
        <h3><%= xg_html('A_MESSAGE_FROM_USERNAME_TO_GROUPNAME_ON_APPNAME',
                xnhtmlentities(xg_username($fromProfile)), xnhtmlentities($group->title),
                xnhtmlentities($message['appName'])) %></h3>
        <table>
            <tr>
                <td>
                    <p><big><%= nl2br(xnhtmlentities($body)) %></big></p>
                    <p><big><%= xg_html('VISIT_GROUP_AT', xnhtmlentities($group->title)) %><br />
						<a href="<%= $groupUrl %>"><%= $groupUrl %></a></big></p>
                </td>
                <td><%= xg_avatar($fromProfile, 96) %></td>
            </tr>

        </table>
        <p class="smallprint"><small>
			<?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
			<%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
			<a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
		</small></p>
    </div>
