<?php
/**
 * 	The lite version of rss.php. Used from PhotoController to generate flash slideshow feed.
 * 	Doesn't include all RSS fields to make the output smaller.
 */
// @param photos
// @param title
// @param link

// pubDate and lastBuild date are required by some aggregators, like Bloglines.
// See http://jonaquino.blogspot.com/2005/02/rolling-your-own-rss-feed-be-sure-to.html
// [Jon Aquino 2005-11-09]
$pubDate = (date('r'));
if(($_GET['mini'])&&(!$_GET['photo_width'])){
    //sidebar
    $photoWidth = 204;
    $photoHeight = 153;
} else if (($_GET['small'])&&(!$_GET['photo_width'])){
    //embed, other websites
    $photoWidth = 441;
    $photoHeight = 330;
} else {
    $photoWidth = ($_GET['photo_width'])?$_GET['photo_width']:800;
    $photoHeight = ($_GET['photo_height'])?$_GET['photo_height']:600;
}
$thumbWidth = 80;
$thumbHeight = 80;

echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
<?php
	$allowedTypes = array_flip(array(
		'image/jpeg',
		'image/x-jpeg',
		'application/jpeg',
		'application/x-jpeg',
		'image/png',
		'image/x-png',
		'application/png',
		'application/x-png',
		'image/gif',
		'image/x-gif',
		'application/gif',
		'application/x-gif'
	));
    foreach ($this->photos as $photo) {
		Photo_HtmlHelper::fitImageIntoThumb($photo, $photoWidth, $photoHeight, $imgUrl, $imgWidth, $imgHeight);
        Photo_HtmlHelper::fitImageIntoThumb($photo, $thumbWidth, $thumbHeight, $thumbUrl, $imgThumbWidth, $imgThumbHeight);
		if ( !isset($allowedTypes[$photo->my->mimeType]) ) {
			$photo->my->mimeType = 'image/jpeg';
            $imgUrl = Photo_HtmlHelper::addParamToUrl($imgUrl, 'format', 'jpg');
            $thumbUrl = Photo_HtmlHelper::addParamToUrl($thumbUrl, 'format', 'jpg');
		}
		$photolink = $this->_buildUrl('photo', 'show', '?id=' . $photo->id);
        $mimeType = xnhtmlentities($photo->my->mimeType);
        $imgUrl = xnhtmlentities($imgUrl);
      ?><item>
            <link><%=$photolink;%></link>
			<enclosure url="<%=$imgUrl;%>" type="<%=$mimeType;%>" length="<%=($photo->my->length)?$photo->my->length:'1';%>" />
            <media:thumbnail url="<%=xnhtmlentities($thumbUrl);%>" width="<%=$imgThumbWidth;%>" height="<%=$imgThumbHeight;%>" />
        </item>
<?php
	}
?>
    </channel>
</rss>
