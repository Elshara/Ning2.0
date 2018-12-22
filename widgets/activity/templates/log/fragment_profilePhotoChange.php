<?php

$content = XG_Cache::content($contentIds[0]);
echo '<span class="message">';
if (XG_App::onMyProfilePage()) {
	echo xg_html('YOU_UPDATED_PROFILE_PHOTO');
} else {
	echo xg_html('S_UPDATED_PROFILE_PHOTO', qh(XG_FullNameHelper::fullName($content->contributorName)),
		'href="'.qh('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($content->contributorName)).'"');
}
echo '</span>';
echo $timeStamp;
?>
<div class="thumbs"><%= xg_avatar(XG_Cache::profiles($content->contributorName), 64); %></div>
