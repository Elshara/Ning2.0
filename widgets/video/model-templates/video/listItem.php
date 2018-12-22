<?php
$videoUrl = W_Cache::getWidget('video')->buildUrl('video', 'show', array('id' => $video->id));
$tagUrl = W_Cache::getWidget('video')->buildUrl('video', 'listTagged');
$duration = Video::getDuration($video);
$tagBreak = mb_strlen($video->description) ? '<br/>' : '';
?>
<div class="bd">
    <div class="ib">
      	<a href="<%= $videoUrl %>"><img class="xg_lightborder" alt="<%= xnhtmlentities($video->title) %>" width="136" src="<%= xnhtmlentities(Video_VideoHelper::thumbnailUrl($video,136)) %>"/></a>
    </div>
    <div class="tb">
		<h3>
		    <a href="<%= $videoUrl %>" class="title"><%= $video->title ? xnhtmlentities($video->title) : xg_html('NO_TITLE'); %></a> 
		    <%= $duration ? '<span class="item_duration">' . $duration . '</span>' : '' %>
		</h3>
		<p>
			<?php if ($video->description) { ?>
				<span class="item_description"><%= Video_HtmlHelper::excerpt($video->description, 100) %></span>
			<?php } ?>
			<?php if ($video->my->topTags) { ?>
				<span class="item_tags">
				<%= xg_html('TAGS') %>
				<%= xg_tag_links($video->my->topTags, $tagUrl, 2) %>
				</span>
			<?php } ?>
			<span class="left">
				<span class="item_contributor"><a href="<%= W_Cache::getWidget('video')->buildUrl('video', 'listForContributor', array('screenName'=>$video->contributorName)) %>"><%= xnhtmlentities(XG_FullNameHelper::fullName($video->contributorName)) %></a></span>
				<span class="item_created"><%= xg_elapsed_time($video->createdDate) %></span>
			</span>
			<?php if($video->my->ratingAverage > 0 || $video->my->viewCount > 0) { ?>
				<span class="right">
					<?php if ($video->my->ratingAverage > 0) { ?>
						<span class="item_rating"><%= Video_HtmlHelper::stars($video->my->ratingAverage) %></span>
					<?php } ?>
					<?php if ($video->my->viewCount > 0) { ?>
						<span class="item_views"><%= xg_html('N_VIEWS', $video->my->viewCount) %></span>
					<?php } ?>
				</span>
			<?php } ?>
		</p>
    </div>
</div>
