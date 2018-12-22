<?php
// Renders a video thumbnail
//
// @param video       The video whose thumb to show
// @param thumbWidth  The width of the thumb
// @param imgClass


if ($video->my->conversionStatus == 'in progress' || $_GET['test_in_progress']) { ?>
    <a href="<?php echo $this->_buildUrl('video', 'show') . '?id=' . $video->id ?>"><img <%= $imgClass ? 'class="' . $imgClass . '"' : '' %> src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/placeholders/conversion_' . $thumbWidth . '.gif'))) %>" alt="<%= xg_html('CONVERSION_IN_PROGRESS') %>" width="<?php echo $thumbWidth ?>" /></a>
<?php
} else {  ?>
    <a href="<?php echo $this->_buildUrl('video', 'show') . '?id=' . $video->id ?>"><img <%= $imgClass ? 'class="' . $imgClass . '"' : '' %> src="<?php echo xnhtmlentities(Video_VideoHelper::thumbnailUrl($video, $thumbWidth)) ?>" alt="<%= xnhtmlentities(Video_HtmlHelper::alternativeText($video)) %>" width="<?php echo $thumbWidth ?>" /></a>
<?php
}