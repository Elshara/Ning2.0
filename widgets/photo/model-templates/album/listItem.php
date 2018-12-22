<?php
/**
 * Displays an album as a list item.
 *
 * @param $album XN_Content|W_Content  the Album object
 * @param $i integer  index of the Album
 * @param $coverPhotos array  mapping of $i to cover Photo
 * @param $column integer  the column number of the photo
 * @param $showCreator boolean  display the name of the photo creator
 * @param $thumbnailSize integer  max extent for the thumbnail; defaults to the value for the album list pages
 * @param $useLightfont boolean	whether to add xg_lightfont
 */
// This template is also used by Photo_EmbedType [Jon Aquino 2008-03-07]
if (! $thumbnailSize) { $thumbnailSize = 133; }
$commentCounts = Comment::getCounts($album);
$date = xg_date(xg_text('F_J_Y'), $album->createdDate);
$time = xg_date(xg_text('G_IA'), $album->createdDate);
if ($coverPhotos[$i] && $coverPhotos[$i]->my->approved != 'N') {
    Photo_HtmlHelper::fitImageIntoThumb($coverPhotos[$i], $thumbnailSize, $thumbnailSize, $coverPhotoUrl, $coverPhotoWidth, $coverPhotoHeight, true);
} else {
    $coverPhotoUrl = xg_cdn(W_Cache::getWidget('photo')->buildResourceUrl('gfx/albums/default_cover_120x120.gif'));
} ?>
<div class="bd">
	<div class="ib">
    	<a href="<%= xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('album', 'show') . '?id=' . $album->id) %>" class="xg_lightborder"><img src="<%= qh($coverPhotoUrl) %>" height="<%=$coverPhotoHeight%>" width="<%=$coverPhotoWidth%>" alt="<%= xnhtmlentities($album->title) %>" /></a>
    </div>
    <div class="tb">
    	<h3><a href="<%=  xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('album', 'show', array('id' => $album->id))) %>"><strong><%= xnhtmlentities($album->title) %></strong></a></h3>
      	<p<%=$useLightfont ? ' class="xg_lightfont"':''%>>
        <?php if ($showCreator) { ?>
			<span class="item_contributor"><%= xg_html('BY_X', xg_userlink(XG_Cache::profiles($album->contributorName), $useLightfont ? "class='xg_lightfont'" : '')) %></span>
        <?php } ?>
        <span class="item_added"><%= xg_html('ADDED_DATE_AT_TIME', xnhtmlentities($date), xnhtmlentities($time)); %></span>
        <?php if ($commentCounts['commentCount']) { ?>
			<span class="item_comments"><%=  xg_html('N_COMMENTS', $commentCounts['commentCount']) %></span>
        <?php } ?>
      </p>
    </div>
</div>
