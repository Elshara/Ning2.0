<?php
/**
 * Message template for a "report this" user report message
 *
 * @param $category string The report category (Adult, etc.)
 * @param $body string The message from the user
 * @param $url string The URL on which the issue occurred
 */
if (XN_Profile::current()->isLoggedIn()) {
    $profileUrl = xnhtmlentities(xg_absolute_url(User::profileUrl(XN_Profile::current()->screenName)));
    $name = xnhtmlentities(xg_username(XN_Profile::current()));
} else {
    $name = xg_html('SOMEBODY');
}
?>
<div class="xg_body">
    <h3><%= xg_html('X_REPORTED_THE_FOLLOWING_ISSUE_ON_Y', xnhtmlentities($name), $message['appName']) %></h3>
    <table width="100%">
        <tr>
            <td>
                <p><strong><%= xg_html('URL_REPORTED_COLON') %></strong> <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a></p>
                <p><strong><%= xg_html('ISSUE_COLON') %></strong> <%= xnhtmlentities($category)%></p>
                <p><strong><%= xg_html('ADDITIONAL_INFORMATION_COLON') %></strong></p>
                <p><%= nl2br(xnhtmlentities($body)) %></p>
                <?php if (isset($profileUrl)) { ?>
                    <p><%= xg_html('SEND_X_A_MESSAGE_HERE', xnhtmlentities($name)) %><br />
                        <a href="<%= $profileUrl %>"><%= $profileUrl %></a></p>
                <?php } ?>
                <p><%= xg_html('FOR_ADDITIONAL_QUESTIONS_OR_TO_CONTACT_NING_PLEASE_CLICK_HERE') %><br />
                    <a href="http://help.ning.com/">http://help.ning.com</a></p>

            </td>
        </tr>
    </table>
</div>

