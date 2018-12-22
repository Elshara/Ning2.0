<?php
/**
 * Message template for notifying someone that their pending membership has
 * been approved
 *
 * @param $profile XN_Profile The profile for the user that's trying to join
 */
 XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
?>
<div class="xg_body">
    <h3><%= xg_html('CONGRATULATIONS_BANG_YOUR_X_MEMBERSHIP_HAS_BEEN_APPROVED', xnhtmlentities($message['appName'])) %></h3>
	<table width="100%">
		<tr>
			<td>
		        <p><%= xg_html('YOU_CAN_NOW_SIGN_IN_USING_YOUR_EMAIL_ADDRESS_HERE') %><br />
					<?php $url = "http://" . $_SERVER['HTTP_HOST'] . User::profileUrl($profile->screenName); ?>
					<a href="<%= $url %>"><%= $url %></a></p>
				<p><%= xnhtmlentities($message['appName']) %></p>
			</td>
		</tr>
	</table>
	<?php if (mb_strlen($message['appDescription'])) { ?>
	    <p><%= $message['appName'] %><br />
	       <%= $message['appDescription'] %></p>
	<?php } ?>
</div>