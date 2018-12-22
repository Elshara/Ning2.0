<?php
/**
 * Message template for notifying an administrator that someone has joined
 * and needs to be moderated
 *
 * @param $joiner XN_Profile The profile for the user that's trying to join
 */
$fullName = xg_username($joiner);
?>
<div class="xg_body">
    <h3><%= xg_html('YOU_HAVE_A_NEW_MEMBER_TO_APPROVE_ON_X', xnhtmlentities($message['appName'])) %></h3>
    <table>
        <tr>
            <td>
                <p><big>
                    <strong><%= xg_html('NAME_COLON') %> <%= xnhtmlentities(xg_username($joiner)) %></strong><br />
                    <%= xg_html('EMAIL_COLON') %> <%= xnhtmlentities($joiner->email) %><br />
                </big></p>
				<?php $profileUrl = xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('profile','showPending',array('id' => $joiner->screenName))); ?>
                <p><%= xg_html('TO_APPROVE_XS_PROFILE_VISIT', xnhtmlentities($fullName))%><br />
					<a href="<%= $profileUrl %>"><%= $profileUrl %></a></p>
            </td>
            <td><img src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($joiner,98,98)) %>" alt="<%= xnhtmlentities($fullName) %>" /></td>
        </tr>
    </table>
	<p class="smallprint"><small>
		<?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
		<%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
		<a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
	</small></p>
</div>