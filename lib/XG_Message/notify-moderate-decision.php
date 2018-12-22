<?php
/**
 * Message template for notifying the content creator that their content has been approved
 *
 * @param $content XN_Content Content object that has been moderated
 * @param $type string What type was moderated
 * @param $thumb string optional thumbnail URL to include
 */
if (mb_strlen($content->title) > 0) {
    $msg = xg_html('YOUR_X_Y_HAS_BEEN_APPROVED_ON_Z', xnhtmlentities($type), xnhtmlentities($content->title), xnhtmlentities($message['appName']));
} else {
    $msg = xg_html('YOUR_X_HAS_BEEN_APPROVED_ON_Y', xnhtmlentities($type), xnhtmlentities($message['appName']));
}
$url = "http://" . $_SERVER['HTTP_HOST'] . "/xn/detail/" . $content->id;
?>
	<div class="xg_body">
		<h3><%= $msg %></h3>
		<table width="100%">
			<tr>
				<td>
					<p><%= xg_html('TO_VIEW_YOUR_X_VISIT', xnhtmlentities($type))%><br />
					    <a href="<%= $url %>"><%= $url %></a>
				</td>
			<td><?php if (isset($thumb)) {
				echo '<td>';
				echo '<img  width="150" src="'.xnhtmlentities($thumb).'" alt="'.xnhtmlentities($content->title).'" />';
				echo '</td>';
			} else {
				echo '<td></td>';
			} ?>
			</tr>
		</table>
		<p class="smallprint"><small>
			<?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
			<%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
			<a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
		</small></p>
	</div>
