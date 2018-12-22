<?php
// Renders the Flash music player
//
// @param $autoplay  whether the player should start playing immediately
// @param $width  the width of the player
// @param $height the height of the player, if not passed it will use 295 pixels or less than that if few tracks
// @param $showplaylist  whether the player should display the list of tracks and artwork
// @param $playlist_url  the xspf playlist to play
// @param $placeholder_url  (optional) an image to use as default placeholder when no track artwork is present
// @param $logo_link  the url that clicking on the network name takes you to (required for external embeds)
// @param $display_add_links  "on" or "off", indicating whether to display the 'Add To My Page' link for any track
// @param $display_contributor  whether the player should display the contributor links or not (two-line track entries)
// @param $embed  whether the code is for an external embed or not
// includeFooterLink - whether to add a link back to the app
// @param $bgcolor  optional background color to override the value in music-config.xml, e.g., 333333
// @param $bgimage  optional background image URL to override the value in music-config.xml; use "none" to specify no image
// @param $brand  optional brand setting to override the value in music-config.xml: name, logo, or none
// @param $logoImage  optional brand-logo URL to override the value in music-config.xml
// @param $logoImageWidth  optional brand-logo width to override the value in music-config.xml
// @param $logoImage  optional brand-logo to override the value in music-config.xml, or 'none' to show none
// @param $noMusicMessage - optional text to display if no music is available
// @param $containerWidth - the width of the column where the player is (example 455 for the center column)
// @param $ratings - if the player should display the ratings stars or not (off/on)
// @param $display_feature_btn - if the feature link should appear for the tracks that allow it

XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
W_Cache::getWidget('music')->includeFileOnce('/lib/helpers/Music_SecurityHelper.php');
$app_url    = 'http://' . $_SERVER['HTTP_HOST'];
if (!$placeholder_url)    $placeholder_url = $app_url . xg_cdn($this->_widget->buildResourceUrl('gfx/placeholder.png'));
if ($embed) {
    $playlist_url = XG_HttpHelper::addParameter($playlist_url, 'xn_auth', 'no');
    $playlist_url = XG_HttpHelper::addParameter($playlist_url, 'x', Music_SecurityHelper::embeddableAccessCode());
}
$display_opacity = isset(W_Cache::getWidget('music')->privateConfig['playerDisplayOpacity']) ?
                        W_Cache::getWidget('music')->privateConfig['playerDisplayOpacity'] : 50;
$flashVars = 'configXmlUrl=' . xnhtmlentities(urlencode(XG_EmbeddableHelper::addGenerationTimeParameter(xg_absolute_url('/xn_resources/instances/music/playlist/music-config.xml')))).
                '&playlist_url=' . xnhtmlentities(urlencode($playlist_url)).
                ($autoplay ? "&autoplay=true" : '') .
                (($showplaylist==false) ? "&showplaylist=false" : '') .
                (($shuffle) ? "&shuffle=true" : '') .
                (($repeat) ? "&repeat=true" : '') .
                (($select_track) ? "&select_track=" . $select_track : '') .
                '&placeholder_url=' . xnhtmlentities(urlencode($placeholder_url)) .
                (($app_url)?('&xn_app_url=' . $app_url) : '') .
                (($display_feature_btn)?('&display_feature_btn='. $display_feature_btn) : '') .
                '&display_download_btn='. ((XG_App::musicDownloadIsDisabled()) ? 'off' : 'on') .
                (($display_opacity)?('&display_opacity=' . $display_opacity) : '') .
                (($ratings)?('&ratings='.$ratings) : '') .
                (($detach_btn)?('&detach_btn='.$detach_btn) : '') .
                (($bgcolor)?('&backgroundColor=' . $bgcolor) : '') . 
                (($bgimage)?('&backgroundImageUrl=' . xnhtmlentities(urlencode($bgimage))) : '') . 
                (($brand)?('&brandFormat=' . $brand) : '') . 
                (($logoImage)?('&logoUrl=' . $logoImage) : '') . 
                (($logoImageWidth)?('&logoWidth=' . $logoImageWidth) : '') . 
                (($logoImageHeight)?('&logoHeight=' . $logoImageHeight) : '') . 
                (($networkNameCss)?('&networkNameCss=' . xnhtmlentities(urlencode($networkNameCss))) : '') . 
                (($logo_link)?('&logo_link='.xnhtmlentities(urlencode($logo_link))) : '') .
                (($display_add_links)?('&display_add_links='.$display_add_links) : '').
                (($display_contributor)?('&display_contributor=on') : '').
                (($embed)?('&display_logo='.$embed) : '') .
                (($noMusicMessage)?('&noMusicMessage=' . rawurlencode($noMusicMessage)) : '');
XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
$player_url = xg_cdn($this->_widget->buildResourceUrl('swf/xspf_player.swf'));
// XG_FacebookHelper duplicates this code - remember to make updates there as well. [Jon Aquino 2007-06-29]

//shrink player height if few tracks (BAZ-4916) and artwork not visible (left or right column size)
if (($this->trackCount) && ($this->trackCount < 9) && ($containerWidth <= 362 )){
    if (($this->playlistSet)&&(($this->playlistSet == 'userplaylist')||($this->playlistSet == 'homeplaylist'))){
        $trackLineHeight = 18;
    } else {
        $trackLineHeight = 38;
    }
    $playerHeight = 144 + $trackLineHeight * $this->trackCount;
} else {
    $playerHeight = 295;
}
if ($playerHeight < 176) $playerHeight = 176;
if ($playerHeight > 295) $playerHeight = 295;
if ($height) $playerHeight = $height;

?>
<embed
    src="<%= $player_url %>"
    FlashVars="<%= $flashVars %>"
    width="<%= $width %>"
    height="<%= ($showplaylist)?$playerHeight:'130' %>"
    wmode="transparent"
    bgcolor="#000000"
    scale="noscale"
    allowscriptaccess="always"
    type="application/x-shockwave-flash"
    pluginspage="http://www.macromedia.com/go/getflashplayer">
</embed>
<?php
if ($includeFooterLink) { ?>
    <br /><small><a href="<%= xnhtmlentities(xg_absolute_url('/')) %>"><%= xg_html('FIND_MORE_MUSIC_LIKE_THIS', preg_replace('/&#039;/u', "'", xnhtmlentities(XN_Application::load()->name))) %></a></small><br />
<?php
}
