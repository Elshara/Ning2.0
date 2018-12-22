<?php
$href = W_Cache::getWidget('video')->buildUrl('video', 'show', array('id' => $video->id));
?>
<div class="bd">
	<div class="ib">
    	<a href="<%= xnhtmlentities($href) %>"><img src="<%= xnhtmlentities(Video_VideoHelper::thumbnailUrl($video,136)) %>" width="136" alt="<%= xnhtmlentities($video->title) %>" class="xg_lightborder" /></a>
	</div>
	<div class="tb">
		<h3><a href="<%= xnhtmlentities($href) %>"><%= xnhtmlentities($video->title) %></a></h3>
	</div>
</div>
