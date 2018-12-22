<?php
/**
 * Message template for notifying someone of activity on a content object
 *
 * @param $viewActivity string Phrase telling the user to click the following URL to view the activity
 * @param $activity string What the activity was
 * @param $content XN_Content Content object the activity was on
 * @param $reason string the reason for the message
 * @param $url string the target URL for the message
 * @param $thumb string optional thumbnail URL to include
 */
?>
	<div class="xg_body">
		<h3><%= xnhtmlentities($activity) %></h3>
		<table width="100%">
			<tr>
				<td>
					<p><%= xnhtmlentities($viewActivity) %><br />
						<a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a></p>
				</td>
				<td>
				<?php if (isset($thumb)) {
					echo '<img width="150" src="'.xnhtmlentities($thumb).'" alt="'.xnhtmlentities($content->title).'" />';
				} ?>
				</td>
			</tr>
		</table>
		<p class="smallprint"><small>
			<?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
			<%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
			<a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
		</small></p>
	</div>
