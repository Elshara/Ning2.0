<?php
/**
 * Displays a photo as a list item.
 *
 * @param $photo XN_Content|W_Content  the Photo object
 * @param $column integer  the column number of the photo
 * @param $context string  optional name of the context: album, user, location, featured
 * @param $showCreator boolean  display the name of the photo creator
 * @param $thumbnailSize integer  max extent for the thumbnail; defaults to the value for the photo list pages
 * @param $useLightfont boolean	whether to add xg_lightfont
 */
// This template is also used by Photo_EmbedType [Jon Aquino 2008-03-07]
if (! $thumbnailSize) { $thumbnailSize = 139; }
Photo_HtmlHelper::fitImageIntoThumb($photo, $thumbnailSize, $thumbnailSize, $imgUrl, $imgWidth, $imgHeight);
$href = W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('id' => $photo->id, 'context' => $context)); ?>
<div class="bd">
    <div class="ib">
		<a href="<%= xnhtmlentities($href) %>"><img width="<%= $imgWidth %>" height="<%= $imgHeight %>" src="<%= xnhtmlentities($imgUrl) %>" alt="<%= xnhtmlentities($photo->title) %>" class="xg_lightborder" /></a>
    </div>
    <div class="tb">
		<h3><a href="<%= xnhtmlentities($href) %>"><%= xnhtmlentities($photo->title) %></a></h3>
        <?php if ($showCreator) { ?>
			<p<%=$useLightfont ? ' class="xg_lightfont"':''%>><span class="item_contributor"><%= xg_html('BY_X', xg_userlink(XG_Cache::profiles($photo->contributorName), $useLightfont ? "class='xg_lightfont'" : '')) %></span></p>
        <?php } ?>
	</div>
</div>
