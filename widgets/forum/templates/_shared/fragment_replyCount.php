<?php
/**
 * Displays the number of replies to a discussion topic.
 *
 * @param $topic XN_Content|W_Content  the Topic
 * @param $lineBreakBefore boolean  whether to insert a <br/> before the reply count
 */
$counts = Comment::getCounts($topic);
if ($counts['commentCount']) {
    if ($lineBreakBefore) { echo '<br />'; } ?>
    <small <%= $lineBreakBefore ? '' : 'class="nobr"' %> ><%= str_replace(' ', '&nbsp;', xg_html('N_REPLIES', $counts['commentCount'])) %></small>
<?php
} ?>
