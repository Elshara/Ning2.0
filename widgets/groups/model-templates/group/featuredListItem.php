<div class="bd">
	<div class="ib">
		<a href="<%= xnhtmlentities(XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id))) %>">
			<img src="<%= xnhtmlentities(Group::iconUrl($group, 139)) %>" width="139" height="139" alt="<%= xnhtmlentities($group->title) %>" class="xg_lightborder" />
		</a>
	</div>
	<div class="tb">
		<h3><a href="<%= xnhtmlentities(XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id))) %>"><%= xnhtmlentities($group->title) %></a></h3>
		<p>
			<span class="item_members"><%= xg_html('N_MEMBERS', $group->my->memberCount) %></span>
		</p>
	</div>
</div>
