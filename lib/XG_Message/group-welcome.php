<?php
/**
 * Message template for a notification that you've joined a group
 *
 * @param $profile XN_Profile  the profile of the new user
 * @param $group XN_Content|W_Content  the Group
 */
?>
<div class="xg_body">
    <h3><%= xg_html('WELCOME_TO_THE_GROUP_X_ON_Y', xnhtmlentities($group->title), xnhtmlentities($message['appName'])) %></h3>
    <table width="100%">
        <tr>
            <td><img align="right" width="96" height="96" src="<%= xnhtmlentities(XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($profile,96,96),'xn_auth','no')) %>" alt="<%= xnhtmlentities(xg_username($profile)) %>" /></td>
            <td>
                <p><%= xg_html('TO_VIEW_THIS_GROUP_VISIT') %><br />
                    <a href="<%= xnhtmlentities(XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id))) %>"><%= xnhtmlentities(XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id))) %></a>
                </p>
				<p><%= xnhtmlentities($message['appName']) %></p>
            </td>
        </tr>
    </table>
    <p class="smallprint"><small>
		<?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
		<%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
		<a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
    </small></p>
</div>
