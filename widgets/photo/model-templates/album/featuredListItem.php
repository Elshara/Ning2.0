<?php
if ($featuredCoverPhotos[$i] && $featuredCoverPhotos[$i]->my->approved != 'N') {
    Photo_HtmlHelper::fitImageIntoThumb($featuredCoverPhotos[$i], 133, 133, $coverPhotoUrl, $coverPhotoWidth, $coverPhotoHeight, true);
} else {
    $coverPhotoUrl = xg_cdn(W_Cache::getWidget('photo')->buildResourceUrl('gfx/albums/default_cover_120x120.gif'));
} ?>
<div class="bd">
	<div class="ib">
		<a href="<%= xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('album', 'show') . '?id=' . $album->id) %>" class="xg_lightborder">
			<img src="<%= qh($coverPhotoUrl) %>" height="<%= $coverPhotoHeight %>" width="<%= $coverPhotoWidth %>" alt="<%= xnhtmlentities($album->title) %>" />
		</a>
	</div>
	<div class="tb">
		<h3><a href="<%=  xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('album', 'show', array('id' => $album->id))) %>"><%= xnhtmlentities($album->title) %></a></h3>
		<p>
			<span class="item_contributor"><%= xg_html('BY_X', xg_userlink(XG_Cache::profiles($album->contributorName))) %></span>
		</p>
	</div>
</div>
