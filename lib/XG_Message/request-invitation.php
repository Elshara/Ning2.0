<?php
/** Message template for an invitation request
 *
 * @param $body string The message from the sender
 * @param $fromName string The name of the sender
 * @param $thumbUrl string optional profile thumbnail url for logged-in users that are requesting
 * @param $inviteUrl string URL for the app owner to invite the person
 */
?>
	<div class="xg_body">
		<h3><%= xg_html('X_HAS_REQUESTED_AN_INVITATION', xnhtmlentities($fromName), xnhtmlentities($message['appName'])) %></h3>
		<table width="100%">
			<tr>
				<td>
					<p><big><%= nl2br(xnhtmlentities($body)) %></big></p>
					<p><big><a href="<%= xnhtmlentities($inviteUrl) %>"><%= xg_html('SEND_AN_INVITATION_TO_X',xnhtmlentities($fromName)) %></a></big></p>
				</td>
			<?php if (isset($thumbUrl)) { ?>
				<td><img align="right" width="96" height="96" src="<%= xnhtmlentities($thumbUrl) %>" alt="<%= xnhtmlentities($fromName) %>"></td>
			<?php } ?>
			</tr>
		</table>

		<p class="smallprint"><small>
			<?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
			<%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
			<a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
		</small></p>
	</div>
