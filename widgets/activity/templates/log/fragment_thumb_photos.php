<?php
/**
* @param $photos
**/
W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
?>
<div class="thumbs"><?php
foreach($photos as $photo){
    Photo_HtmlHelper::fitImageIntoThumb($photo, 80, 80, $imgUrl, $width, $height);?>
    <a href="<%= xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('id' => $photo->id))) %>"  title="<%= $photo->title ? xnhtmlentities($photo->title) : xg_html('UNTITLED') %>"><img class="photo thumb" src="<%= $imgUrl %>" width="<%= $width %>" height="<%= $height %>" alt="<%= $photo->title ? xnhtmlentities($photo->title) : xg_html('UNTITLED') %>" /></a><?php
}

?>
</div>