<?php
/**
 * Displays the sub-navigation.
 *
 * @param $screenName string  username of the user that the current page is focused on
 * @param $moduleLinks array an array of the enabled modules in the network, links to the user page and a participation count
 * @param $currentLink string the name of the currently selected link.  Used for setting the appropriate class
 */

if ($this->_user->isLoggedIn()) { ?>
<ul class="navigation">
	<li><a href="<%=qh($this->_buildUrl('members', ''))%>"><%=xg_html('ALL_MEMBERS')%></a></li>
	<li><a href="<%=qh(User::quickFriendsUrl($this->_user->screenName))%>"><%=xg_html('MY_FRIENDS')%></a></li>
	<?php if (XG_App::canSeeInviteLinks($this->_user)) { ?>
		<li class="right"><a href="/invite" class="add desc"><%=xg_html('INVITE_FRIENDS')%></a></li>
	<?php } ?>
</ul>
<?php } ?>