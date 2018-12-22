<?php
/**
 * An invitation to a group.
 *
 * @param $fromProfile		XN_Profile 	the person sending the invitation
 * @param $url 				string		the target URL for the message
 * @param $body				string  	the message from the sender (nl2br will be applied). If empty, default is used
 * @param $members			list<W_Content> Members to include into the email
 * @param $counters			list<string>
 * @param $features			list<string>
 * @param $message			hash		Message common info
 * @param $sparse			bool		Display "sparse" view
 * @param $helper			XG_MessageHelper
 */
$username = $helper->userName($fromProfile->screenName);
$appName = $message['appName'];

if (!$body) {
	$body = xg_text('COME_JOIN_ME_ON_X_EXCL', $appName) . "\n\n" . $username;
}
$helper->header();
$helper->delimiter();
echo $body,"\n";
echo "\n";
echo xg_text('CLICK_LINK_BELOW_TO_JOIN_COLON'),"\n";
echo $url,"\n";
echo "\n";
echo xg_text('IF_YOUR_CLIENT_NOLINKS'),"\n\n";
if (!$sparse && count($members) > 0) {
	$helper->delimiter();
	echo xg_text('MEMBERS_ALREADY_ON_X', $appName),"\n";
	$i = 0; foreach($members as $m) {
		if ($i) { echo ", "; }
		echo $helper->userName($m->title);
		$i++;
	}
	echo "\n\n";
}
$helper->aboutNetwork($sparse);
$helper->delimiter();
$helper->unsubscribe();
