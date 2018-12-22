<?php
Photo_HtmlHelper::fitImageIntoThumb($photo, 139, 139, $imgUrl, $imgWidth, $imgHeight);
$href = W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('id' => $photo->id, 'context' => 'featured')); ?>
<div class="bd">
	<div class="ib"><a href="<%= xnhtmlentities($href) %>"><img width="<%= $imgWidth %>" height="<%= $imgHeight %>" src="<%= xnhtmlentities($imgUrl) %>" alt="<%= xnhtmlentities($photo->title) %>" class="xg_lightborder" /></a></div>
    <div class="tb">
		<h3><a href="<%= xnhtmlentities($href) %>"><%= xnhtmlentities($photo->title) %></a></h3>
		<p><span class="item_contributor"><%= xg_html('BY_X', xg_userlink(XG_Cache::profiles($photo->contributorName))) %></span></p>
	</div>
</div>
