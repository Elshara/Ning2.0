<?php
/**
* @param $photos
**/
W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_AlbumHelper.php');
$coverPhotos = Photo_AlbumHelper::getCoverPhotos($albums);
?>
<div class="thumbs"><?php
$counter = 0;
foreach($albums as $album){
    if ($coverPhotos[$counter] && $coverPhotos[$counter]->my->approved != 'N') {
        Photo_HtmlHelper::fitImageIntoThumb($coverPhotos[$counter], 80, 80, $coverPhotoUrl, $width, $height);
    } else {
        $coverPhotoUrl = xg_cdn(W_Cache::getWidget('photo')->buildResourceUrl('gfx/albums/default_cover_120x120.gif'));
        $width = $height = 80;
    }
    $counter++; ?>
    <a href="<%= xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('album', 'show', array('id' => $album->id))) %>"  title="<%= $album->title ? xnhtmlentities($album->title) : xg_html('UNTITLED') %>"><img class="photo thumb" src="<%= $coverPhotoUrl %>" width="<%= $width %>" height="<%= $height %>" alt="<%= $album->title ? xnhtmlentities($album->title) : xg_html('UNTITLED') %>" /></a>
<?php
}

?>
</div>