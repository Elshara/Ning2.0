<?php
/**
 * Message template for notifying someone of activity on a content object they're
 *   following
 *
 * @param $activity string What the activity was
 * @param $content XN_Content Content object the activity was on
 * @param $reason string the reason for the message
 * @param $url string the target URL for the message
 * @param $thumb string optional thumbnail URL to include
 * @param $viewActivity string Text introducing a link to the activity
 * @param $unfollowLink string URL to unsubscribe from future notifications.
 */
?>
    <div class="xg_body">
        <h3><%= xnhtmlentities($activity) %></h3>
        <table width="100%">
            <tr>
                <td>
                    <p><big><%= xnhtmlentities($viewActivity) %><br />
                        <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a></big></p>
                </td>
            <?php if (isset($thumb)) {
                echo  '<td>';
                echo '<img width="150" src="'.xnhtmlentities($thumb).'" alt="'.xnhtmlentities($content->title).'" />';
                echo '</td>';
            } else {
                echo '<td></td>';
            }?>
            </tr>
        </table>
		<p class="smallprint"><small>
			<%= xg_html('TO_STOP_FOLLOWING_THIS_X_GO_TO', mb_strtolower($type)) %><br />
			<a href="<%= $unfollowLink %>"><%= $unfollowLink %></a>
		</small></p>
    </div>
