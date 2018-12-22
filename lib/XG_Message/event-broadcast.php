<?php
/**
 * Message template for a message sent to selected attendees of an event
 *
 * @param $body string  text of the message
 * @param $fromProfile XN_Profile  the person sending the invitation
 * @param $event XN_Content  Event object
 * @param $eventUrl string  URL of the event detail page
 * @param $message array  additional metadata for this message
 */ ?>
<div class="xg_body">
    <h3><%= xg_html('A_MESSAGE_FROM_USERNAME_TO_EVENTNAME_ON_APPNAME',
            xnhtmlentities(xg_username($fromProfile)), xnhtmlentities($event->title),
            xnhtmlentities($message['appName'])) %></h3>
    <table>
        <tr>
            <td>
                <p><big><%= nl2br(xnhtmlentities($body)) %></big></p>
                <p><big><%= xg_html('VISIT_EVENT_AT', xnhtmlentities($event->title)) %><br />
                    <a href="<%= xnhtmlentities($eventUrl) %>"><%= xnhtmlentities($eventUrl) %></a></big></p>
            </td>
            <td><%= xg_avatar($fromProfile, 96) %></td>
        </tr>
    </table>
    <p class="smallprint"><small>
        <?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
        <%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
        <a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
    </small></p>
</div>
