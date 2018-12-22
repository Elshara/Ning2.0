<?php $href = xnhtmlentities(User::quickProfileUrl($user->title)); ?>
<div class="bd">
	<div class="ib">
    	<a href="<%= $href %>">
        	<img class="xg_lightborder" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($user,118,118)) %>" height="118" width="118" alt="<%= xnhtmlentities(xg_username($user)) %>"/>
		</a>
	</div>
	<div class="tb">
		<h3><a href="<%= $href %>"><%= xnhtmlentities(xg_username($user)) %></a></h3>
	</div>
</div>
