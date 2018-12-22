<?php
/**
 * An invitation to a event
 *
 * @param $fromProfile		XN_Profile 	the person sending the invitation
 * @param $url 				string		the target URL for the message
 * @param $body				string  	the message from the sender (nl2br will be applied). If empty, default is used
 * @param $message			hash		Message common info
 * @param $event 			XN_Content  Event object
 * @param $helper			XG_MessageHelper
 */
$username = $helper->userName($fromProfile->screenName);
$appName = $message['appName'];

if (!$body) {
	$body = xg_text('CHECK_OUT_TITLE_ON_APPNAME', $event->title, $appName) . "\n\n" . $username;
}

$helper->header();
echo xg_text('USER_INVITED_YOU_TO_EVENT', $username, $event->title, $appName),"\n";
$helper->delimiter();
echo $body,"\n";
echo "\n";
echo xg_text('TIME_COLON'), " ", Events_TemplateHelper::startDate($event, true), "\n";
echo xg_text('LOCATION_COLON'), " ", Events_TemplateHelper::location($event, true, false), "\n";
echo xg_text('ORGANIZED_BY_COLON'), " ", Events_TemplateHelper::organizedBy($event, true, false), ":\n";
echo "\n";
echo xg_text('EVENT_DESCRIPTION_COLON'),"\n";
echo html_entity_decode( strip_tags($event->description), ENT_QUOTES, 'UTF-8' ), "\n";
echo "\n";
echo xg_text('SEE_DETAILS_AND_RSVP', $appName),"\n";
echo $url,"\n";
echo "\n";
echo xg_text('IF_YOUR_CLIENT_NOLINKS'),"\n";
$helper->aboutNetwork($sparse);
$helper->delimiter();
$helper->unsubscribe();
