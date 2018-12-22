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
$url = xnhtmlentities($url);
$appName = xnhtmlentities($message['appName']);

if (!$body) {
	$body = xg_html('CHECK_OUT_TITLE_ON_APPNAME', xnhtmlentities($event->title), $appName) . '<br /><br />' . nl2br($username);
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
									<div style="font-size:14px; font-weight:bold;"><%=xg_html('USER_INVITED_YOU_TO_EVENT', $username, xnhtmlentities($event->title), $appName)%></div>
									<%$helper->delimiter()%>
									<%=$body%>
									<br /><br />
									<table cellspacing="0" cellpadding="0" width="100%" style="padding-top:8px">
										<tr>
											<?php if ($enableImages) {?>
											<td width="96" valign="top" style="padding-right:12px; text-align:center;font-size:12px;">
												<a href="<%=$url%>"><img width="96" height="96" border="0" alt="<%=xnhtmlentities($event->title)%>" src="<%=$this->addImageByUrl(Events_TemplateHelper::photoUrl($event, 96))%>"></a>
											</td>
											<?php }?>
											<td width="*" valign="top" style="font-size:12px;">
												<strong><%=xg_html('TIME_COLON')%></strong> <%=Events_TemplateHelper::startDate($event, true)%><br />
												<strong><%=xg_html('LOCATION_COLON')%></strong> <%=Events_TemplateHelper::location($event, true)%><br />
												<strong><%=xg_html('ORGANIZED_BY_COLON')%></strong> <%=Events_TemplateHelper::organizedBy($event, true)%><br /><br />
												<?php if($event->description) {?><div><strong><%=xg_html('EVENT_DESCRIPTION_COLON')%></strong><br /><%=nl2br(strip_tags($event->description))%></div><br /><?php }?>
											</td>
										</tr>
									</table>
									<br />
									<div style="font-weight:bold;font-size:12px;"><%=xg_html('SEE_DETAILS_AND_RSVP', $appName)%></div>
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
