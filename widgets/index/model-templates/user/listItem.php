<?php
$href = xnhtmlentities(User::quickProfileUrl($user->title));
$details = xg_age_and_location($user->title);
XG_App::includeFileOnce('/lib/XG_PresenceHelper.php');
$online = XG_PresenceHelper::canShowPresenceIndicator(XG_Cache::profiles($user->title));
?>
<div class="bd">
	<div class="ib">
		<a href="<%= $href %>">
			<img width="96" height="96" alt="<%= xnhtmlentities(xg_username($user->title)) %>" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($user,96,96)) %>" class="xg_lightborder" />
		</a>
	</div>
	<div class="tb">
		<h3><a href="<%= $href %>"><%= xnhtmlentities(xg_username($user->title)) %></a></h3>
		<?php if($details || $online || $myFriends || XG_SecurityHelper::userIsAdmin()) { ?>
		<p>
			<?php
			if ($details) {
				echo '<span class="memberinfo">' . $details . '</span>';
			}
			if ($online) {
				echo '<span class="online">'.xg_html('ONLINE').'</span>';
			}
			if (XG_SecurityHelper::userIsAdmin()) {
				W_Cache::getWidget('main')->dispatch('promotion','link',array($user, null, null, true));
			}
			if ($myFriends) { ?>
				<span><a class="smalldelete" href="#" _url="<%= XG_HttpHelper::addParameters($deleteUrl, array('user' => $user->title)) %>" _updateurl="<%= xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('friend','updateFriendList',array_merge($_GET, array('xn_out' => 'json')))) %>" dojoType="UnfriendLink" _username="<%= xnhtmlentities(xg_username($this->title)) %>"><%= xg_html('DELETE') %></a></span>
			<?php } ?>
		</p>
		<?php } ?>
	</div>
</div>
