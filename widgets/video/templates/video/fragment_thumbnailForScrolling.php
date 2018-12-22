<?php
/**
 * @param $video
 * @param $context
 * @param $current
 */

$imgUrl = Video_VideoHelper::thumbnailUrl($video, 74, 55); ?>
<li<%= $current? ' class="this"':''%>>
    <a href="<%= xnhtmlentities($this->_buildUrl('video', 'show', array('id' => $video->id, 'context' => $context))) %>" title="<%= $video->title ? xnhtmlentities($video->title) : xg_html('UNTITLED') %>">
        <img src="<%= $imgUrl %>" alt="<%= $video->title ? xnhtmlentities($video->title) : xg_html('UNTITLED') %>" />
    </a>
</li>