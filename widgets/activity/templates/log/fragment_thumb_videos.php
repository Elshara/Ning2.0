<?php
/**
* @param $videos
**/
W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
?>
<div class="thumbs"><?php
foreach($videos as $video){ 
    $imgUrl = Video_VideoHelper::thumbnailUrl($video, 98, 74); ?>
	<a href="<%= xnhtmlentities(W_Cache::getWidget('video')->buildUrl('video', 'show', array('id' => $video->id))) %>"  title="<%= $video->title ? xnhtmlentities($video->title) : xg_html('UNTITLED') %>"><img class="photo thumb" src="<%= $imgUrl %>" width="98" alt="<%= $video->title ? xnhtmlentities($video->title) : xg_html('UNTITLED') %>" /></a><?php
}
?>
</div>
