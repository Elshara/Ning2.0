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
$userUrl = xnhtmlentities(xg_absolute_url(User::quickProfileUrl($fromProfile->screenName)));
$appName = xnhtmlentities($message['appName']);
$url = xnhtmlentities($url);

if (!$body) {
	$body = xg_html('COME_JOIN_ME_ON_X_EXCL', $appName) . '<br/><br/> - ' . $username;
} else {
	$body = nl2br(xnhtmlentities($body));
}
?>

<table cellpadding="0" cellspacing="0" border="0" width="98%" style="height: 98%">
	<tr>
	<td valign="top" style="font:12px 'lucida grande', tahoma, helvetica, arial, sans-serif; padding: 10px;">
		<table width="1" cellpadding="0" cellspacing="0" border="0" style="border:3px solid #ccc">
		  <tr>
			<td bgcolor="#<%=$message['cssDefaults']['moduleHeadBgColor']%>" style="padding:4px 12px; color:#<%=$message['cssDefaults']['moduleHeadTextColor']%>;">
	
			  <div style="font-size:18px;">
				<%=xg_html('JOIN_ME_ON_X', $appName)%>
			  </div>
			  <div style="font-size:12px;"><%=xnhtmlentities($message['appTagline'])%></div>
			</td>
		  </tr>
		  <tr>
			<td bgcolor="#ffffff" width="*" style="font-size:12px;padding: 10px;" valign="top">
			  <table cellspacing="0" cellpadding="0" width="100%">
			    <tr>
				  <?php if ($enableImages) {?>
			      <td width="96" valign="top" style="font-size: 12px; padding-right: 10px;">
					<?php if ($counters && !$sparse) {?>
				        <a href="<%=$url%>"><img height="96" width="96" border="0"
				        							 alt="<%=xnhtmlentities(xg_excerpt(xg_username($fromProfile->screenName),14))%>"
				        							 src="<%=$this->addImageByUrl(xg_url(XG_UserHelper::getThumbnailUrl($fromProfile,96,96), 'xn_auth=no'))%>"></a>
				        <div style="padding:6px 0;"><%=xg_html('PROFILE_LINK_HAS',"href=\"$url\" style=\"text-decoration:none\"", $helper->shortUserName($fromProfile->screenName))%></a></div>
				        <span style="font-size:11px;white-space:nowrap"><%=join('<br />',$counters)%></span>
					<?php } else {
						$helper->userUrl($fromProfile);
					}?>
			      </td>
				  <?php }?>
			      <td valign="top" style="font-size: 12px;">
					<div style="font-size:13px; padding:8px 16px 16px;">
						<%=$body%>
					</div>
					<div align="center">
			        <a href="<%=$url%>" class="xg_button" style="display:block; text-decoration:none; background:#ffff99; color:#432; border:1px solid; border-color:#fc0 #ca0 #ca0 #fc0; font-size:18px; font-weight:bold; text-align:center; width:220px; line-height:37px; margin:12px auto;"><%=xg_html('CLICK_TO_JOIN')%></a><br/>
					</div>
			        <?php if (!$sparse && $enableImages && count($members) > 0) {?>
					  <%=xg_html('MEMBERS_ON_X_COLON', $appName)%>
                    <?php }?>
					<table style="margin-top:6px" width="360" cellpadding="0" cellspacing="0" border="0">
                      <?php if (!$sparse && $enableImages && count($members) > 0) {?>
						<tr>
						<?php foreach($members as $m) {
							$mUrl = xnhtmlentities(xg_absolute_url(User::quickProfileUrl($m->title)));
						?>
						  <td style="text-align:center; padding-right:10px; padding-bottom: 10px" valign="top">
						  	<a href="<%=$url%>"><img height="64" width="64" border="0"
													 alt="<%=xnhtmlentities(xg_excerpt(xg_username($m->title),14))%>"
													 src="<%=$this->addImageByUrl(xg_url(XG_UserHelper::getThumbnailUrl($m,64,64),'xn_auth=no'))%>"></a>
							<a href="<%=$url%>" style="font-size:11px; text-decoration:none;display:block;"><%=$helper->shortUserName($m->title)%></a>
						  </td>
						<?php }?>
						</tr>
                      <?php }?>
				    </table>
					  
					<%$helper->aboutNetwork($sparse)%>
			      </td>
			    </tr>
			  </table>

<style type="text/css">
a.xg_button:hover {
	text-decoration:underline!important;
}
</style>
	
			  <%$helper->delimiter()%>
			  <%$helper->unsubscribe()%>
		  </td>
		</tr>
	  </table>
	</td>
	</tr>
</table>


