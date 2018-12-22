<?php
/** When someone uses OpenSocial to send a message to a user - the message is sent to the user's alerts folder in Bazel.
 * As a result, a notification is sent to the user's external mailbox.  And this file (application-message.txt) contains
 * the text for that notification email.
 *
 * @param $profile XN_Profile The profile of the message sender
 * @param $body string The message from the user
 * @param $osAppTitle string The title of the OpenSocial Application
 * @param $viewOSAppUrl string Url to view the OpenSocial Application
 */
?>
<div class="xg_body">
    <table width="100%">
        <tr><td><big><%= nl2br(xnhtmlentities($body)) %></big></td></tr>
        <tr><td><%= xg_html('MESSAGE_SENT_ON_BEHALF_BY_APP', xnhtmlentities(xg_username($profile)), xnhtmlentities($osAppTitle)) %></td></tr>
        <tr><td><%= xg_text('TO_VIEW_CLICK_HERE_COLON') %> <%= W_Cache::getWidget('profiles')->buildUrl('message', 'listAlerts')  %></td></tr>
    </table>
</div>
