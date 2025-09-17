<?php
// Slideshow player xml config file
//

$app_url = 'http://' . $_SERVER['HTTP_HOST'];
$player_url = xg_cdn($this->_widget->buildResourceUrl('slideshowplayer/slideshowplayer.swf'));
$app_name = xnhtmlentities(urlencode(XN_Application::load()->name));
$playerImageUrl = Video_AppearanceHelper::getPlayerImageUrl('PhotoSlideshowLogo', 20, '#333333');
if ($playerImageUrl) {
    $brand_url = xnhtmlentities(urlencode($playerImageUrl));
}
//uncomment or delete the line below after the decision of if we want the text always white or not
//$textColor = $this->selectedLinkColor;
$textColor = 'ffffff';
$slideshowCss =  xnhtmlentities(urlencode( ('h1{font-family: '.$this->selectedFont.';color:#'.$textColor.';}')));

?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<config>
    <logoUrl><%= xg_xmlentities(XG_EmbeddableHelper::getPlayerLogoUrl()) %></logoUrl>
    <logoWidth><%= xg_xmlentities(XG_EmbeddableHelper::getPlayerLogoWidth()) %></logoWidth>
    <logoHeight><%= xg_xmlentities(XG_EmbeddableHelper::getPlayerLogoHeight()) %></logoHeight>
    <logoLink><%= xg_xmlentities($app_url) %></logoLink>
    <brandFormat><%= xg_xmlentities(XG_EmbeddableHelper::getPlayerBrandFormat()) %></brandFormat>
    <backgroundImageUrl><%=  xg_xmlentities(XG_EmbeddableHelper::getBackgroundImageUrl()) %></backgroundImageUrl>
    <textcolor><%=    $textColor    ;%></textcolor>
    <ning_app_url><%= $app_url      ;%></ning_app_url>
    <player_url><%=   $player_url   ;%></player_url>
    <brand_url><%=    $brand_url    ;%></brand_url>
    <bgcolor><%= XG_EmbeddableHelper::getBackgroundColor(); %></bgcolor>
    <app_name><%=     $app_name     ;%></app_name>
    <css><%=          $slideshowCss ;%></css>
    <?php /* The Flash player urldecodes all values [Jon Aquino 2007-07-06] */ ?>
    <footerHtml><%= xg_xmlentities(urlencode($this->footerHtml)) %></footerHtml>
    <?php /* localization */ ?>
    <l_play_again><%= xg_html('PLAY_AGAIN') %></l_play_again>
    <l_share><%= xg_html('SHARE') %></l_share>
    <l_embed><%= xg_html('EMBED') %></l_embed>
    <l_copy_to_clipboard><%= xg_html('COPY_TO_CLIPBOARD') %></l_copy_to_clipboard>
    <l_copied_to_clipboard><%= xg_html('COPIED_TO_CLIPBOARD') %></l_copied_to_clipboard>
    <l_see_photos_on_network><%= xg_html('SEE_PHOTOS_ON_NETWORK', XN_Application::load()->name) %></l_see_photos_on_network>
    <l_get_embed_code><%= xg_html('GET_EMBED_CODE_SENTENCE_CASE') %></l_get_embed_code>
    <l_back><%= xg_html('BACK') %></l_back>
    <l_fullscreen><%= xg_html('FULLSCREEN') %></l_fullscreen>
    <l_exit_fullscreen><%= xg_html('EXIT_FULLSCREEN') %></l_exit_fullscreen>
    <l_embed_code><%= xg_html('EMBED_CODE') %></l_embed_code>
    <l_copy_and_paste_link><%= xg_html('COPY_AND_PASTE_LINK') %></l_copy_and_paste_link>
</config>
