<?php
/**
 * Message template for a generic message
 *
 * @param $body string
 *
 * The message can also use the message properties set in $message
 */
?>
	<div class="xg_body">
		<h3><%= xg_html('MESSAGE_TO_ALL_MEMBERS_OF_X', xnhtmlentities($message['appName'])) %></h3>
		<table width="100%">
			<tr>
				<td>
					<p><big><%= nl2br($body) %></big></p>
				</td>
			</tr>
		</table>
		<?php $url = 'http://' . $_SERVER['HTTP_HOST']; ?>
		<p><%= xg_html('VISIT_X_AT_Y', $message['appName'], '<a href="' . $url . '">' . $url . '</a>') %></p>
		<p class="smallprint"><small>
			<?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
			<%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
			<a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
		</small></p>
	</div>
