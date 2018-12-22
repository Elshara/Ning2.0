<?php
/**
 * @param $feed  Urlencoded url for the rss slideshow photo feed
 * @param $slideshow_width  Width for the slideshow player, in pixels
 * @param $slideshow_height  Height for the slideshow player, in pixels
 * @param $fullsize
 * @param $brand
 * @param $title
 * @param $fullsize_url  The (urlencoded) URL to go to when the user clicks the Full Screen button on the player
 * @param $start
 * @param $bgColor (Optional) Background color to override the value in showPlayerConfig, e.g., 333333
 * @param $wmode (Optional) The wmode value. Set it to "opaque" to ensure that divs appear
 *         above the player. However, note that opaque can cause problems, e.g., no cursor in text fields in Firefox 2.
 * @param $externalPreview  Whether this is for a preview of how the player will look on an external site.
 *         Do not use this for the actual external embed code.
 * @param $layout (Optional) if the player code is for embed, internal or fullscreen usage, possible values: fullscreen, within_app, or external_site (default)
 * @param $includeFooterLink Whether to add a link back to the app
 * @param $noPhotosMessage  (Optional) Text to display if no photos are available
 */
if ($externalPreview) {
    // Work around caching of showPlayerConfig (BAZ-3631) [Jon Aquino 2007-07-02]
    if (! $bgcolor) { $bgcolor = XG_EmbeddableHelper::getBackgroundColor(); }
    if (! $bgimage) { $bgimage = XG_EmbeddableHelper::getBackgroundImageUrl(); }
    if (! $brand) { $brand = XG_EmbeddableHelper::getPlayerBrandFormat(); }
    if (! $logoImage) { $logoImage = XG_EmbeddableHelper::getPlayerLogoUrl(); }
    if (! $logoImageWidth) { $logoImageWidth = XG_EmbeddableHelper::getPlayerLogoWidth(); }
    if (! $logoImageHeight) { $logoImageHeight = XG_EmbeddableHelper::getPlayerLogoHeight(); }
}
// @todo choose bgcolor or bgColor [Jon Aquino 2007-07-02]
if ($bgcolor) { $bgColor = $bgcolor; }
$feed .= urlencode('&x=' . Photo_SecurityHelper::embeddableAccessCode());
$app_url = 'http://' . $_SERVER['HTTP_HOST'];
$currentpage = xnhtmlentities(urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
$title = xnhtmlentities($title);
XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
$slideshowplayer_url = xg_cdn($this->_widget->buildResourceUrl('slideshowplayer/slideshowplayer.swf'));
if($embed) $feed .= urlencode('&xn_auth=no');
if (! $slideshow_width) { $slideshow_width = 800; }
if (! $slideshow_height) { $slideshow_height = 627; }
$feed .= urlencode('&photo_width=' . ($slideshow_width));
$feed .= urlencode('&photo_height=' . ($slideshow_height-23));
$config_url = xnhtmlentities(urlencode($this->_buildUrl('photo','showPlayerConfig','?x='.Photo_SecurityHelper::embeddableAccessCode()).(($embed)?'&xn_auth=no':'')));
?>
<embed class="xg_slideshow" src="<?php echo $slideshowplayer_url?>" quality="high" bgcolor="<%= $bgColor %>"
    width="<?php echo $slideshow_width;?>"
    height="<?php echo $slideshow_height;?>"
    allowFullScreen="true"
    allowScriptAccess="always"
    <%= $wmode ? 'wmode="' . $wmode . '"' : '' %>
    scale="noscale"
    FlashVars="feed_url=<%=$feed%>&config_url=<%= $config_url;%>&backgroundColor=<%= $bgColor %><%=
    ($bgimage)?"&backgroundImageUrl=".xnhtmlentities(urlencode($bgimage)):''%><%=
    ($brand)?"&brandFormat=".xnhtmlentities(urlencode($brand)):''%><%=
    ($logoImage)?"&logoUrl=".xnhtmlentities(urlencode($logoImage)):''%><%=
    ($logoImageHeight)?"&logoHeight=".xnhtmlentities(urlencode($logoImageHeight)):''%><%=
    ($logoImageWidth)?"&logoWidth=".xnhtmlentities(urlencode($logoImageWidth)):''%><%=
    ($start)?"&start_slide=$start":''%><%=
    ($layout)?"&layout=$layout":''%><%=
    ($noPhotosMessage)?"&noPhotosMessage=".xnhtmlentities(urlencode($noPhotosMessage)):''%><%=
    '&slideshow_title='.urlencode($title).'&fullsize_url='.($fullsize_url?$fullsize_url:$currentpage); %>"
    type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
</embed>
<?php
if ($includeFooterLink) {
    $this->renderPartial('fragment_embeddableFooter');
}