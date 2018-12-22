<?php
/**
 * Message template for a generic user feedback message
 *
 * @param $body string The message from the user
 * @param $heading string The message heading
 */
$helpUrl = 'http://help.ning.com';
$profileUrl = xg_absolute_url(User::profileUrl(XN_Profile::current()->screenName));

?>
    <div class="xg_body">
        <h3><%= xnhtmlentities($heading) %></h3>
        <table width="100%">
            <tr>
                <td>
                    <p><%= nl2br(xnhtmlentities($body)) %></p>
                    <p><%= xg_html('SEND_X_A_MESSAGE_HERE', xnhtmlentities($feedbackSenderName)) %><br />
						<a href=" <%= $profileUrl %>"><%= $profileUrl %></a></p>
                </td>
            </tr>
        </table>
        <p class="smallprint"><small><%= xg_html('FOR_ADDITIONAL_QUESTIONS_OR_TO_CONTACT_NING_PLEASE_CLICK_HERE') %><br />
			<a href="<%= $helpUrl %>"><%= $helpUrl %></a></small></p>
    </div>
