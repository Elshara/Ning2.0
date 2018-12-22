<?php 
$href = xnhtmlentities(User::quickProfileUrl($user->title));
$details = xg_age_and_location($user->title); ?>
<li _url="<%= $href %>" onclick="javascript:void(0)">
    <div class="ib"><a href="<%= $href %>"><img height="48" width="48" alt="" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($user,48,48)) %>" /></a></div>
    <div class="tb"><a href="<%= $href %>"><%= xnhtmlentities(xg_username($user)) %></a><%= ($details) ? ('<div>' . $details . '</div>') : '' %></div>
</li>
