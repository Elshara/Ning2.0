<?php
/**
 * Notification that a discussion has been started
 *
 * @param $topic XN_Content|W_Content  the discussion topic
 * @param $url string  the URL of the discussion
 * @param $unsubscribeUrl string  the URL of the page for stopping this notification
 * @param $message array  basic predefined values
 */ ?>
<div class="xg_body">
    <h3><%= xg_html('X_STARTED_THE_DISCUSSION_Y', xnhtmlentities(XG_UserHelper::getFullName(XG_Cache::profiles($topic->contributorName))), xnhtmlentities($topic->title)) %></h3>
    <table width="100%">
        <tr>
            <td>
                <p><%= xg_html('TO_VIEW_THIS_DISCUSSION_GO_TO'); %><br />
					<a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url); %></a></p>
            </td>
        </tr>
    </table>
    <p class="smallprint"><small>
        <%= xg_html('TO_STOP_BEING_NOTIFIED_OF_NEW_DISCUSSIONS_GO_TO') %><br />
			<a href="<%= $unsubscribeUrl %>"><%= $unsubscribeUrl %></a>
    </small></p>
</div>
