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
if (isset($share_content_author)) {
	$authorName = $helper->userName($share_content_author);
}

if (!$body) {
	$body = xg_text('CHECK_OUT_TITLE_ON_APPNAME', $title, $appName) . "\n\n-" . $username;
}

// Cannot move to XG_Message, because username/title are quoted differently for text/html
switch($share_raw_type) {
	case 'photo':
		$headerTitle = $title ? xg_text('USER_WANTS_YOU_CHECK_OUT_PHOTO', $username, $title) : xg_text('USER_WANTS_YOU_CHECK_OUT_THIS_PHOTO', $username);
		$descrTitle = xg_text('PHOTO_DESCRIPTION_COLON');
		$linkTitle = xg_text('PHOTO_LINK');
		break;
	case 'album':
		$headerTitle = $title ? xg_text('USER_WANTS_YOU_CHECK_OUT_ALBUM', $username, $title) : xg_text('USER_WANTS_YOU_CHECK_OUT_THIS_ALBUM', $username);
		$descrTitle = xg_text('ALBUM_DESCRIPTION_COLON');
		$linkTitle = xg_text('ALBUM_LINK');
		break;
	case 'video':
		$headerTitle = $title ? xg_text('USER_WANTS_YOU_CHECK_OUT_VIDEO', $username, $title) : xg_text('USER_WANTS_YOU_CHECK_OUT_THIS_VIDEO', $username);
		$descrTitle = xg_text('VIDEO_DESCRIPTION_COLON');
		$linkTitle = xg_text('VIDEO_LINK');
		break;
	case 'topic':
		$headerTitle = $title ? xg_text('USER_WANTS_YOU_CHECK_OUT_DISCUSSION', $username, $title) : xg_text('USER_WANTS_YOU_CHECK_OUT_THIS_DISCUSSION', $username);
		$descrTitle = xg_text('DISCUSSION_DESCRIPTION', "", $authorName);
		$linkTitle = xg_text('DISCUSSION_LINK');
		break;
	case 'post':
		$headerTitle = $title ? xg_text('USER_WANTS_YOU_CHECK_OUT_POST', $username, $title) : xg_text('USER_WANTS_YOU_CHECK_OUT_THIS_POST', $username);
		$descrTitle = xg_text('POST_DESCRIPTION', "", $authorName);
		$linkTitle = xg_text('POST_LINK');
		break;
	case 'user':
		$headerTitle = $title ? xg_text('USER_WANTS_YOU_CHECK_OUT_PROFILE', $username, $title) : xg_text('USER_WANTS_YOU_CHECK_OUT_THIS_PROFILE', $username);
		$descrTitle = xg_text('PROFILE_HAS', $authorName);
		$linkTitle = xg_text('PROFILE_LINK');
		break;
	case 'url':
		$headerTitle = $title ?
			xg_text('USER_WANTS_YOU_CHECK_OUT_TITLE', $username, $title) :
			xg_text('USER_WANTS_YOU_CHECK_OUT_TITLE2', $username, $appName);
		$linkTitle = xg_text('LINK_COLON');
		break;
}

$helper->header();
$helper->delimiter();
echo $body,"\n";
echo "\n";
if ($share_raw_type == 'user') {
	if ($counters) {
		echo $descrTitle,"\n";
		echo join("\n", $counters),"\n";
		echo "\n";
	}
} elseif ($share_raw_description) {
	echo $descrTitle,"\n";
	echo xg_excerpt($share_raw_description, 140),"\n";
	echo "\n";
}
echo $linkTitle,"\n";
echo $url,"\n";
echo "\n";
echo xg_text('IF_YOUR_CLIENT_NOLINKS'),"\n\n";
$helper->aboutNetwork($sparse);
$helper->delimiter();
$helper->unsubscribe();
