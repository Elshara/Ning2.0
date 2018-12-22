<?php
/**
 * Message template for notifying the app owner that there is content to moderate
 *
 * @param $content XN_Content Content object to moderate
 * @param $reason string the reason for the message
 * @param $moderationUrl string The URL to perform moderation
 * @param $contentAdder string The XN_Profile object of the user that added the content
 * @param $thumb string optional thumbnail URL to include
 * @param $type string What type to moderate, to go in the sentence "You have a new X to moderate!"
 */
if (mb_strlen($content->title)) {
	$intro = xg_html('YOU_HAVE_A_NEW_X_TITLE_TO_APPROVE_ON_Y', xnhtmlentities($type), xnhtmlentities($content->title), xnhtmlentities($message['appName']));
} else {
	$intro = xg_html('YOU_HAVE_A_NEW_X_TO_APPROVE_ON_Y', xnhtmlentities($type), xnhtmlentities($message['appName']));
}
?>
    <div class="xg_body">
        <h3><%= $intro %></h3>
        <table width="100%">
            <tr>
                <td>
                    <h4><%= xg_html('ADDED_BY') %></h4>
                    <p><a href="http://<%= xnhtmlentities($_SERVER['HTTP_HOST']) %><%= xnhtmlentities(User::profileUrl($contentAdder->screenName)) %>"><%= xnhtmlentities(xg_username($contentAdder)) %></a></p>
                    <p><%= xg_html('TO_APPROVE_THIS_X_VISIT', xnhtmlentities($type)) %><a href="<%= xnhtmlentities($moderationUrl) %>"><%= xnhtmlentities($moderationUrl) %></a></p>
                </td>
            <?php if (isset($thumb)) {
                echo '<td>';
                echo '<img  width="150" src="'.xnhtmlentities($thumb).'" alt="'.xnhtmlentities($content->title).'" />';
                echo '</td>';
            } else {
                echo '<td></td>';
            }?>
            </tr>
        </table>
        <p class="smallprint"><small>
			<?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
			<%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
			<a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
        </small></p>
    </div>
