<?php
// Renders the Flash player for an uploaded video
//
// @param video  the Video to display in the player
// @param autoplay  whether to start the movie immediately. Defaults to true.
// @param layout  within_app, on_detail_page, dummy_external_site, or external_site (default)
XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
if ($_GET['test_info']) { var_dump(array('isPrivate' => $video->isPrivate)); }
if ($video->my->conversionStatus == 'in progress') { ?>
    <img class="in-progress" src="<%= xg_cdn($this->_widget->buildResourceUrl('gfx/placeholders/conversion_420.gif')) %>" alt="<%= xg_html('CONVERSION_IN_PROGRESS') %>" width="420" />
<?php
}
elseif ($video->my->conversionStatus == 'failed') { ?> <img src="/images/placeholders/conversion-failed-450.png" alt="<%= xg_html('CONVERSION_FAILED') %>" width="450" /> <?php }
elseif ($video->my->conversionStatus == 'complete') {
    $videoMaxWidth  = W_Cache::getWidget('video')->privateConfig['videoMaxWidth']  ? W_Cache::getWidget('video')->privateConfig['videoMaxWidth']  : XG_EmbeddableHelper::VIDEO_WIDTH;
    $videoMaxHeight = W_Cache::getWidget('video')->privateConfig['videoMaxHeight'] ? W_Cache::getWidget('video')->privateConfig['videoMaxHeight'] : XG_EmbeddableHelper::VIDEO_HEIGHT;
    list($videoWidth, $videoHeight) = Video_VideoHelper::previewFrameDimensionsScaled($video, $videoMaxWidth, $videoMaxHeight, true);
    if (W_Cache::getWidget('video')->privateConfig['playerVerticalResize']=='N') { $videoHeight = $videoMaxHeight; }
    $playerHeight = $videoHeight + XG_EmbeddableHelper::VIDEO_PLAYER_CONTROLS_HEIGHT_INTERNAL;
    $embedPlayerHeight = $videoHeight + XG_EmbeddableHelper::VIDEO_PLAYER_CONTROLS_HEIGHT_EXTERNAL;
    $playerWidth = (W_Cache::getWidget('video')->privateConfig['playerHorizontalResize']=='Y') ? $videoWidth : $videoMaxWidth;
    $this->_widget->dispatch('video', 'embeddableProper', array(array('id' => $video->id, 'width' => $playerWidth, 'height' => $playerHeight,

            'autoplay' => $autoplay, 'layout' => $layout, 'includeFooterLink' => false, 'embedVisible' => $embedVisible )));
    ob_start();
    $this->_widget->dispatch('video', 'embeddableProper', array(array('id' => $video->id, 'width' => $playerWidth, 'height' => $embedPlayerHeight,
            'autoplay' => false, 'layout' => 'external_site', 'includeFooterLink' => true)));
    $this->embedCode = trim(ob_get_contents());
    ob_end_clean();
}
else {
    // Set wmode to opaque so action dialogs will appear above the player  [Jon Aquino 2006-07-19]
    echo Video_VideoHelper::disableScriptAccess(Video_VideoHelper::opaqueEmbedCode($video->my->embedCode));
    $this->embedCode = $video->my->embedCode;
}
$this->embedCode = preg_replace('/\s+/u', ' ', $this->embedCode);