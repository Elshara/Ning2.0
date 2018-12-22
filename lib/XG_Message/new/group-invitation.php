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
$groupName = xnhtmlentities($group->title);
$appName = xnhtmlentities($message['appName']);
$url = xnhtmlentities($url);

if (!$body) {
	$body = xg_html('COME_JOIN_ME_ON_X_ON_Y', $groupName, $appName) . '<br /><br />' . nl2br($username);
} else {
	$body = nl2br(xnhtmlentities($body));
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
								<?php if($enableImages) {?>
								<td width="96" valign="top" style="padding-right:16px;">
									<%$helper->userUrl($fromProfile)%>
								</td>
								<?php }?>
								<td width="*" valign="top" style="font-size:12px; padding-bottom: 10px;">
									<div style="font-size:14px; font-weight:bold; padding-bottom:8px"><%= xg_html('USER_HAS_INVITED_YOU_TO_JOIN_GROUP_ON_X', $username, $groupName, $appName) %></div>
									<%$helper->delimiter()%>
									<%=$body%>
									<br /><br />
									<table cellspacing="0" cellpadding="0" border="0" width="100%">
										<tr>
											<?php if ($enableImages && $groupUrl = Group::iconUrl($group,96)) {?>
												<td width="96" style="text-align:center; padding-right:12px;font-size:12px" valign="top"><a href="<%=$url%>"><img height="96" width="96" border="0" alt="<%=$groupName%>" src="<%=$this->addImageByUrl($groupUrl)%>"></a></td>
											<?php }?>
											<td width="*" valign="top" style="font-size:12px">
												<%#=xg_html('ABOUT_X_ON_Y', $groupName, $appName)%>
												<%=xnhtmlentities($group->description)%><br />
<?php if (!$sparse && $counters) { ?>
												<br />
													<?php foreach($counters as $name) {
														echo $name,"<br />";
													} ?>
<?php }?>
												<br />
												<%=xg_html('CREATED_BY_COLON')%> <a style="text-decoration:none" href="<%=xnhtmlentities(xg_absolute_url(User::quickProfileUrl($group->contributorName)))%>"><%=$helper->userName($group->contributorName)%></a>
											</td>
										</tr>
									</table>
									<br />
									<div style="font-weight:bold;font-size:12px"><%=xg_html('CHECK_OUT_X_ON_Y_COLON', $groupName, $appName)%></div>
									<a href="<%=$url%>"><%=$url%></a>
									<div style="padding-top: 10px">
										<%$helper->aboutNetwork($sparse)%>
									</div>
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
