<div class="bd">
	<div class="ib">
		<a href="<%= xnhtmlentities(XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id))) %>">
			<img src="<%= xnhtmlentities(Group::iconUrl($group, 82)) %>" width="82" height="82" alt="<%= xnhtmlentities($group->title) %>" class="xg_lightborder" />
		</a>
	</div>
	<div class="tb">
		<h3><a href="<%= xnhtmlentities(XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id))) %>"><%= xnhtmlentities($group->title) %></a></h3>
		<p>
			<span class="item_members"><%= xg_html('N_MEMBERS', $group->my->memberCount) %></span>
			<?php if ($group->my->lastActivityOn) { ?>
				<span class="item_time"><%= xg_html('LATEST_ACTIVITY_COLON_TIME', xg_elapsed_time($group->my->lastActivityOn)) %></span>
			<?php } ?>
			<?php if ($group->description) { ?>
				<span class="item_description"><%= xnhtmlentities($group->description) %></span>
			<?php } ?>
		</p>
	</div>
</div>
