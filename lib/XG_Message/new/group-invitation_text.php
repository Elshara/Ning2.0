<?php
/**
 * An invitation to a group.
 *
 * @param $fromProfile		XN_Profile 	the person sending the invitation
 * @param $url 				string		the target URL for the message
 * @param $body				string  	the message from the sender (nl2br will be applied). If empty, default is used
 * @param $group			W_Content	Group object
 * @param $members			list<W_Content> Members to include into the email
 * @param $counters			list<string>
 * @param $message			hash		Message common info
 * @param $sparse			bool		Display "sparse" view
 * @param $helper			XG_MessageHelper
 */
$username = $helper->userName($fromProfile->screenName);
$groupName = $group->title;
$appName = $message['appName'];

if (!$body) {
	$body = xg_text('COME_JOIN_ME_ON_X_ON_Y', $groupName, $appName) . "\n\n" . $username;
}

$helper->header();
echo xg_text('USER_HAS_INVITED_YOU_TO_JOIN_GROUP_ON_X', $username, $groupName, $appName),"\n";
$helper->delimiter();
echo $body,"\n";
echo "\n";
echo xg_text('CHECK_OUT_X_ON_Y_COLON', $groupName, $appName),"\n";
echo $url,"\n";
echo "\n";
echo xg_text('IF_YOUR_CLIENT_NOLINKS'),"\n";
$helper->delimiter();
echo xg_text('ABOUT_X_ON_Y', $groupName, $appName),"\n";
echo $group->description,"\n";
if (!$sparse && $counters) {
	echo "\n";
	foreach ($counters as $name) {
		echo $name,"\n";
	}
}
echo "\n";
echo xg_text('CREATED_BY_COLON'), " ", $helper->userName($group->contributorName), "\n";
/*if (!$sparse) {
	$helper->delimiter();
	echo xg_text('USERNAME_AND_OTHER_ARE_ALREADY_MEMBERS', $username, $groupName, $appName),"\n";
	echo $username;
	foreach($members as $m) {
		echo ", ",$helper->userName($m->title);
	}
	echo "\n";
}*/
$helper->aboutNetwork($sparse);
$helper->delimiter();
$helper->unsubscribe();
