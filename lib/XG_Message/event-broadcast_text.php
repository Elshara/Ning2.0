<?php
/**
 * Message template for a message sent to selected attendees of an event
 *
 * @param $body string  text of the message
 * @param $fromProfile XN_Profile  the person sending the invitation
 * @param $event XN_Content  Event object
 * @param $eventUrl string  URL of the event detail page
 * @param $message array  additional metadata for this message
 */
echo xg_text('A_MESSAGE_FROM_USERNAME_TO_EVENTNAME_ON_APPNAME',
        xg_username($fromProfile), $event->title, $message['appName']) . "\n\n";

echo "$body\n\n";

echo xg_text('VISIT_EVENT_AT', $event->title) . "\n";
echo "$eventUrl\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
