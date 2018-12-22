<?php
/*  $Id: $
 *
 *  Display the block of featured events
 *      $noAddLink		suppress the "add event" link
 */
?>
<ul class="navigation">
	<li><a href="<%=$this->_buildUrl('event', 'listUpcoming')%>"><%= xg_html('ALL_EVENTS') %></a></li>
	<li><a href="<%=$this->_buildUrl('event', 'listUserEvents', array('user' => $this->_user->screenName))%>"><%= xg_html('MY_EVENTS') %></a></li>
	<?php if (Events_SecurityHelper::currentUserCanCreateEvent() && !$noAddLink) { ?>
		<li class="right"><a href="<%=$this->_buildUrl('event', 'new', array('cancelTarget' => XG_HttpHelper::currentUrl()))%>" class="desc add"><%= xg_html('ADD_AN_EVENT') %></a></li>
	<?php } ?>
</ul>
