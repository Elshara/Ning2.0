<?php
/** Message template for a notification that somebody you invited has joined the app
 *
 * @param $joiner XN_Profile The profile of the person that has joined the app
 */
$profileUrl = "http://" . $_SERVER['HTTP_HOST'] . xnhtmlentities(User::profileUrl($joiner->screenName));
?>
<div class="xg_body">
    <h3><%= xg_html('X_IS_NOW_A_MEMBER_OF_Y', xnhtmlentities(xg_username($joiner)), xnhtmlentities($message['appName'])) %></h3>
    <table width="100%">
        <tr>
            <td>
                <p><big><%= xg_html('WANT_TO_ADD_A_WELCOME_MESSAGE_FOR_X_ON_THEIR_PROFILE_GO_TO', xnhtmlentities(xg_username($joiner))) %><br />
					<a href="<%= $profileUrl %>"><%= $profileUrl %></a></big></p>
				<p><%= xnhtmlentities($message['appName']) %></p>
            </td>
            <td><img align="right" width="96" height="96" src="<%= xnhtmlentities(XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($joiner,96,96),'xn_auth','no')) %>" alt="<%= xnhtmlentities(xg_username($joiner)) %>"></td>
        </tr>
    </table>
    <p class="smallprint"><small>
		<?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
		<%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
		<a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
    </small></p>
</div>
