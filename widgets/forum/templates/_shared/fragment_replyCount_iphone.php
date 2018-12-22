<?php
/**
 * Displays the number of replies to a discussion topic.
 *
 * @param $topic XN_Content|W_Content  the Topic
 */
$counts = Comment::getCounts($topic);
if ($counts['commentCount']) { ?>
    <span class="lighter"><%= str_replace(' ', '&nbsp;', xg_html('N_REPLIES', $counts['commentCount'])) %></span>
<?php
} ?>
