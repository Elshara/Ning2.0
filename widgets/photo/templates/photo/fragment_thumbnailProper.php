<?php
/**
 * @param $photo
 * @param $thumbWidth
 * @param $thumbHeight
 */
Photo_HtmlHelper::fitImageIntoThumb($photo, $thumbWidth, $thumbHeight, $imgUrl, $imgWidth, $imgHeight); ?>
<a href="<%= xnhtmlentities($this->_buildUrl('photo', 'show', array('id' => $photo->id, 'context' => $context, 'albumId' => $albumId)  )) %>"><img src="<%= xnhtmlentities($imgUrl) %>" alt="<%= xnhtmlentities($photo->title) %>" title="<%= xnhtmlentities($photo->title) %>" /></a>