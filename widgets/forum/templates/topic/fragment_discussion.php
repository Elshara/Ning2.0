<?php
/**
 * Summary of a discussion topic in a table row, for the new style topic/list* pages.
 *
 * @param $topic XN_Content|W_Content  The Topic object
 * @param $showDescription boolean  Whether or not to show the Topic description snippet
 * @param $categoryLink array  The href and text for the category link as an array; if this is present then we use it
 * @param $reply XN_Content|W_Content  if present, we show the reply metadata (reply snippet, reply date and reply by) under the topic description
 * @param $featured boolean; whether or not we're showing the truncated view for the featured section
 * @param $highlightReply boolean; whether to highlight the reply rather than the discusion title in the listing.
 * @param $lastChild boolean; whether the item is the last one on the page; used to apply the last-child style to the tr
 */
$myTopic = $topic->contributorName == XN_Profile::current()->screenName;
$counts = Comment::getCounts($topic);
$lastReplyHref = $this->_buildUrl('topic', 'showLastReply', array('id' => $topic->id));
$lastReplyName = xnhtmlentities(xg_username($topic->my->lastCommentContributorName));
$featuredLatestReply = $featured ? '<a class="right" href="' . $lastReplyHref . '">' . xg_html('LATEST_REPLY') . '</a>' : '';
?>
<tr <%= $lastChild ? 'class="last-child"' : '' %>>
    <td class="xg_lightborder">
        <?php if ($highlightReply) { 
            $topicUrl = 'href="' . xnhtmlentities($this->_buildUrl('topic', 'show', array('id' => $topic->id))) . '"';
            $replyHref = 'href="http://' . $_SERVER['HTTP_HOST'] .'/xn/detail/' . $reply->id . '"';
            $replyText = '<a ' . $replyHref . '>' . xg_excerpt($reply->description, 85) . '</a>';
            ?>
            <h3><%= xg_avatar($topic->contributorName, 48) %><%= xg_html('QUOTED_TEXT', $replyText) %></h3>
            <p class="small"><%= $reply->contributorName == XN_Profile::current()->screenName ? xg_html('YOU_REPLIED_TIME_TO_Y', xg_elapsed_time($reply->createdDate), $topicUrl, xg_excerpt($topic->title, 200)) : xg_html('X_REPLIED_TIME_TO_Y', xnhtmlentities(xg_username($reply->contributorName)), xg_elapsed_time($reply->createdDate), $topicUrl, xg_excerpt($topic->title, 200));%></p>
        <?php } else { ?>
            <h3><%= xg_avatar($topic->contributorName, 48) %><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'show', array('id' => $topic->id))) %>"><%= xg_excerpt($topic->title, 200) %></a></h3>
            <?php if ($showDescription) { ?>
                <p class="small"><%= xg_excerpt($topic->description, 140) %></p>
            <?php } ?>
            <?php if (is_array($categoryLink)) { ?>
                <p class="small"><%= $myTopic ? xg_html('STARTED_BY_YOU_IN_X', $categoryLink[0], $categoryLink[1]) : xg_html('STARTED_BY_X_IN_Y', xnhtmlentities(xg_username($topic->contributorName)), $categoryLink[0], $categoryLink[1]) %><%= $featuredLatestReply %></p>
            <?php } else { ?>
                <p class="small"><%= $myTopic ? xg_html('STARTED_BY_YOU') : xg_html('STARTED_BY_X', xnhtmlentities(xg_username($topic->contributorName))) %><%= $featuredLatestReply %></p>
            <?php } ?>
        <?php } ?>

    </td>
    <?php if (!$featured) { ?>
        <td class="bignum xg_lightborder"><%= $counts['commentCount'] %></td>
        <td class="xg_lightborder">
            <?php if ($lastReplyName) { ?>
                <%= xg_elapsed_time($topic->my->lastEntryDate) %>
                <br/><a href="<%= $lastReplyHref %>"><%= xg_html('REPLY_BY_X', $lastReplyName)%></a>
            <?php } else { ?>
                <%= xg_elapsed_time($topic->my->lastEntryDate) %>
            <?php } ?>
        </td>
    <?php } ?>
</tr>
