<?php
/**
 * XML data used by the slideshow player.
 *
 * @param $this->photos The Photo XN_Content objects
 */
$this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
echo '<?xml version="1.0" encoding="UTF-8" ?><gallery time="'.microtime(true).'">';
echo '<album>';
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

foreach($this->photos as $photo){
    Photo_HtmlHelper::fitImageIntoThumb($photo, $thumbWidth, $thumbHeight, $thumbUrl, $imgWidth, $imgHeight);
    Photo_HtmlHelper::fitImageIntoThumb($photo, $photoWidth, $photoHeight, $imgUrl, $imgWidth, $imgHeight);
    $allowedTypes = array(
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
         );
    if(! in_array($photo->my->mimeType, $allowedTypes)){
        $photo->my->mimeType = 'image/jpeg';
        $imgUrl = Photo_HtmlHelper::addParamToUrl($imgUrl, 'format', 'jpg');
        $thumbUrl = Photo_HtmlHelper::addParamToUrl($thumbUrl, 'format', 'jpg');
    }
    $photoUrl = $this->_buildUrl('photo', 'show') . '?id=' . $photo->id;
    echo '<img src="'.xnhtmlentities($imgUrl).'" tn="'.xnhtmlentities($thumbUrl).'" caption="'.xnhtmlentities($photo->title).'"  target="_self" link="'.xnhtmlentities($photoUrl).'"/>';
}
echo '</album></gallery>';
?>
