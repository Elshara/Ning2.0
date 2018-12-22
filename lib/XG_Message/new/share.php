<?php
/**
 * An invitation to a group.
 *
 * @param $fromProfile		XN_Profile 	the person sending the invitation
 * @param $url 				string		the target URL for the message
 * @param $body				string  	the message from the sender (nl2br will be applied). If empty, default is used
 * @param $message			hash		Message common info
 * @param $sparse			bool		Display "sparse" view
 * @param $helper			XG_MessageHelper
 */
$username = $helper->userName($fromProfile->screenName);
$title = xnhtmlentities($title);
$url = xnhtmlentities($url);
$urlTitle = $title ? $title : $url;
if (isset($share_content_author)) {
	$authorUrl = xnhtmlentities(xg_absolute_url(User::quickProfileUrl($share_content_author)));
	$authorName = $helper->userName($share_content_author);
}

$appName = xnhtmlentities($message['appName']);
$thumbWidth = 96;
$thumbHeight = 96;

if (!$body) {
    // do nothing!  let's keep the body as empty.  BAZ-9673
//	$body = $title ? xg_html('CHECK_OUT_TITLE_ON_APPNAME', $title, $appName) : xg_html('CHECK_OUT_UNTITLED_ON_APPNAME', $appName);
//	$body .= '<br />-' . nl2br($username) . '<br />';
} else {
	$body = nl2br(xnhtmlentities($body));
}

// Cannot move to XG_Message, because username/title are quoted differently for text/html
switch($share_raw_type) {
	case 'photo':
		$headerTitle = $title ? xg_html('CHECK_OUT_PHOTO', $title) : xg_html('CHECK_OUT_THIS_PHOTO');
		$descrTitle = xg_html('PHOTO_DESCRIPTION_COLON');
		$linkTitle = xg_html('PHOTO_LINK');
		$showThumb = 1;
		break;
	case 'album':
		$headerTitle = $title ? xg_html('CHECK_OUT_ALBUM', $title) : xg_html('CHECK_OUT_THIS_ALBUM');
		$descrTitle = xg_html('ALBUM_DESCRIPTION_COLON');
		$linkTitle = xg_html('ALBUM_LINK');
		$showThumb = 1;
		break;
	case 'video':
		$headerTitle = $title ? xg_html('CHECK_OUT_VIDEO', $title) : xg_html('CHECK_OUT_THIS_VIDEO');
		$descrTitle = xg_html('VIDEO_DESCRIPTION_COLON');
		$linkTitle = xg_html('VIDEO_LINK');
		$showThumb = 1;
		break;
	case 'topic':
		$headerTitle = $title ? xg_html('CHECK_OUT_DISCUSSION', $title) : xg_html('CHECK_OUT_THIS_DISCUSSION');
		$descrTitle = xg_html('DISCUSSION_DESCRIPTION', "href=\"$authorUrl\" style=\"text-decoration:none;\"", $authorName);
		$linkTitle = xg_html('DISCUSSION_LINK');
		$showThumb = 0;
		break;
	case 'post':
		$headerTitle = $title ? xg_html('CHECK_OUT_POST', $title) : xg_html('CHECK_OUT_THIS_POST');
		$descrTitle = xg_html('POST_DESCRIPTION', "href=\"$authorUrl\" style=\"text-decoration:none;\"", $authorName);
		$linkTitle = xg_html('POST_LINK');
		$showThumb = 0;
		break;
	case 'user':
		$headerTitle = $title ? xg_html('CHECK_OUT_PROFILE', $title) : xg_html('CHECK_OUT_THIS_PROFILE');
		$descrTitle = xg_html('PROFILE_HAS', $authorName);
		$linkTitle = xg_html('PROFILE_LINK');
		$showThumb = 1;
		break;
	case 'url':
		$headerTitle = $title ?
			xg_html('CHECK_OUT_TITLE', $title) :
			xg_html('CHECK_OUT_TITLE2', $appName);
		$linkTitle = xg_html('LINK_COLON');
		$showThumb = 0;
		break;
    case 'opensocialapp':
        $headerTitle = $title ? xg_html('CHECK_OUT_APPLICATION', xnhtmlentities($title)) : xg_html('CHECK_OUT_THIS_APPLICATION');
        $descrTitle = xg_html('APPLICATION_DESCRIPTION_COLON');
        $linkTitle = xg_html('APPLICATION_LINK');
        $showThumb = 1;
        $thumbWidth = 120;
        $thumbHeight = 60;
        break;
}
?>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td bgcolor="#FFFFFF" width="100%">
			<%$helper->header()%>
			<table cellpadding="0" cellspacing="0" border="0" width="600">
				<tr>
					<td width="*" style="font-size:12px;padding-top:8px" valign="top">
						<table cellspacing="0" cellpadding="0" width="100%">
							<tr>
								<?php if ($enableImages) {?>
								<td width="96" valign="top" style="padding-right:16px;">
									<%$helper->userUrl($fromProfile)%>
								</td>
								<?php }?>
								<td width="*" valign="top" style="font-size:12px; padding-bottom: 10px;">
									<?php if (!empty($headerTitle)) { ?>
										<div style="font-size:14px; font-weight:bold; padding-bottom:8px"><%=$headerTitle%></div>
									<?php } ?>
									<?php if (!empty($body)) { ?>
										<%=$body%><br />
									<?php } ?>
									<?php if ($showThumb) {?>
										<br />
										<table cellspacing="0" cellpadding="0" width="100%" style="padding-top:8px">
											<tr>
												<?php if ($enableImages) {?>
												<td width="<%=$thumbWidth%>" valign="top" style="padding-right:16px; padding-bottom: 8px;">
													<a href="<%=$url%>"><img width="<%=$thumbWidth%>" height="<%=$thumbHeight%>" border="0"
													                       src="<%=$this->addImageByUrl(xg_url($thumb,'width='.$thumbWidth.'&height='.$thumbHeight.'&crop=1:1&xn_auth=no'))%>"
                                                                           alt="<%=$title%>"></a>
												</td>
												<?php }?>
												<td valign="top" style="font-size:12px;" width="100%">
													<?php if ($share_raw_type == 'user') {
														if ($counters) {
															if (!empty($descrTitle)) {
																echo "$descrTitle<br /><br />";
															}
                											echo '<table cellspacing="0" cellpadding="0" border="0" width="100%">';
                											echo '<tr>';
                											$i = 0;
                											foreach($counters as $c) {
																if ($i && !($i%2)) { echo '</tr><tr>';}
																echo ($i%2) ? '<td style="font-size:12px">' : '<td width="40%" style="font-size:12px">',$c,'</td>';
																$i++;
															}
                											echo '<tr>';
                											echo '</table>';
														}
													} elseif ($share_raw_description) {
													  if (!empty($title)) { echo '<div style="font-weight:bold">' . $title . '</div>'; }
														echo xg_excerpt($share_raw_description, 140);
													} elseif (!$share_raw_description && $title) {
													  echo '<div style="font-weight:bold">' . $title . '</div>';
													}?>
												</td>
											</tr>
											<tr>
												<td colspan="2" style="font-size:12px">
													<div style="font-weight:bold;font-size:12px"><%=$linkTitle%></div>
													<a href="<%=$url%>"><%=$urlTitle%></a><br /><br />
												</td>
											</tr>
										</table>
									<?php } else {?>
										<?php if (!empty($descrTitle)) { ?>
	        								<br />
	        								<div style="font-weight:bold;"><%=$descrTitle%></div>
	        								<br />
	        							<?php } ?>
										<?php if (!empty($share_raw_description)) { ?>
											<%=xg_excerpt($share_raw_description, 140)%><br /><br />
	        							<?php } ?>
										<?php if (!empty($linkTitle)) { ?>
            								<div style="font-weight:bold;"><%=$linkTitle%></div>
	        							<?php } ?>
            							<a href="<%=$url%>"><%=$urlTitle%></a><br /><br />
									<?php }?>
									<%$helper->aboutNetwork($sparse)%>
								</td>
							</tr>
						</table>
						<%$helper->delimiter()%>
						<%$helper->unsubscribe()%>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
