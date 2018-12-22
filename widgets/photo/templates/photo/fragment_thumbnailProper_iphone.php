<?php
/**
 * @param $photo
 * @param $thumbWidth
 * @param $thumbHeight
 */
Photo_HtmlHelper::fitImageIntoThumb($photo, $thumbWidth, $thumbHeight, $imgUrl, $imgWidth, $imgHeight); ?>
<img class="photo thumb" src="<%= xnhtmlentities($imgUrl) %>" alt="<%= xnhtmlentities($photo->title) %>" title="<%= xnhtmlentities($photo->title) %>" />