<?php
/** Message template for an invitation request
 *
 * @param $body string The message from the sender
 * @param $fromName string The name of the sender
 * @param $thumbUrl string optional profile thumbnail url for logged-in users that are requesting
 * @param $unblockUrl string URL for the app owner to unblock the person
 */
?>
	<div class="xg_body">
		<h3><%= xg_html('A_BANNED_MEMBER_HAS_SENT_YOU_A_MESSAGE_ON_X_USERNAME_WRITES', xnhtmlentities($message['appName']), xnhtmlentities($fromName)) %></h3>
		<table width="100%">
			<tr>
				<td>
					<p><big><%= nl2br(xnhtmlentities($body)) %></big></p>
					<p><big><%= xg_html('TO_UNBAN_USERNAME_GO_TO', xnhtmlentities($fromName)) %><br />
						<a href="<%= xnhtmlentities($unblockUrl) %>"><%= xnhtmlentities($unblockUrl) %></a></big></p>
				</td>
			<?php if (isset($thumbUrl)) { ?>
				<td><img align="right" width="96" height="96" src="<%= xnhtmlentities($thumbUrl) %>" alt="<%= xnhtmlentities($fromName) %>"></td>
			<?php } ?>
			</tr>
		</table>
	</div>
