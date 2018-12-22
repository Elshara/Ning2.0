<?php
if (count($this->friendProfiles)) { ?>
<ul class="list friends">
	<li class="section"><%= xg_html('FRIENDS') %></li>
	<?php 
	foreach ($this->friendProfiles as $friend) { ?>
      <li class="simple simple-48"><a href="<%= User::quickProfileUrl($friend->screenName) %>"><%= xg_avatar($friend, 48, null, '', true) %></a></li>
	<?php 
	} 
	if (count($this->friendProfiles) > $this->maxFriends) {	?>
	<li class="more"><a href="<%= $this->_buildUrl('friend', 'list', array('user' => $this->profile->screenName)) %>"><%= xg_html('VIEW_MORE') %></a></li>
	<?php
	} ?>
</ul>
<?php
} ?>