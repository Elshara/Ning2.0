<?php
/**
 * An invitation to a event
 *
 * @param $url string the target URL for the message
 * @param $body string  the message from the sender
 * @param $fromProfile XN_Profile  the person sending the invitation
 * @param $event XN_Content  Event object
 */
$username = xg_username($fromProfile->screenName);

echo xg_text('USER_HAS_INVITED_YOU_TO_JOIN_EVENT', $username, $event->title, Events_TemplateHelper::startDate($event,true)) . "\n\n";

if ($body) {
    echo "$body\n\n";
}

echo xg_text('EVENT_COLON'),' ', $event->title, "\n";
echo xg_text('TIME_COLON'), ' ', Events_TemplateHelper::startDate($event, true), "\n";
echo xg_text('LOCATION_COLON'), ' ', strip_tags(Events_TemplateHelper::location($event)), "\n";
echo xg_text('EVENT_TYPE_COLON'), ' ', strip_tags(Events_TemplateHelper::type($event)), "\n";
echo xg_text('ORGANIZED_BY_COLON'), ' ', strip_tags(Events_TemplateHelper::organizedBy($event)), "\n";
echo xg_text('DESCRIPTION_COLON'), " ", html_entity_decode( strip_tags($event->description), ENT_QUOTES, 'UTF-8' ), "\n";

echo "\n";

echo xg_text('CLICK_HERE_TO_RSVP_COLON') . "\n";
echo "$url\n\n";

echo xg_username($fromProfile->screenName) . "\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
