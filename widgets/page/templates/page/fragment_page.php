<?php
/**
 * Summary of a discussion page, for the page/list* pages.
 *
 * @param $page XN_Content|W_Content  The Page object
 * @param $comment XN_Content|W_Content A Comment whose text to display instead of the Page's text (optional).
 * @param $showListForContributorLink boolean  Whether to show the "X's discussions" link
 * @param $showContributorName boolean  Whether to show the name of the contributor
 */
$tags = XN_Tag::parseTagString($page->my->topTags);
$focus = $comment ? $comment : $page;
$this->_widget->includeFileOnce('/lib/helpers/Page_CommentHelper.php');
$detailUrl = $comment ? Page_CommentHelper::url($comment) : W_Cache::current('W_Widget')->buildUrl('page', 'show', array('id' => $page->id));
$contributor = XG_Cache::profiles($focus->contributorName); ?>
<div class="wpage vcard">
    <h3>
        <strong><a href="<%= xnhtmlentities($this->_buildUrl('page', 'show', array('id' => $page->id))) %>"><%= xg_excerpt($page->title, 200) %></a></strong>
        <?php
        // Keep this in sync with fragment_moduleBodyAndFooter.php  [Jon Aquino 2007-02-03]
        $counts = Comment::getCounts($page);
        if (!$comment && $counts['commentCount']) { ?>
            <small class="desc comment"><%= str_replace(' ', '&nbsp;', xg_html('N_COMMENTS', $counts['commentCount'])) %></small>
        <?php
        } ?>
    </h3>
    <p><%= xg_excerpt($focus->description, 200, null, $toss, true) %> <a href="<%= xnhtmlentities($detailUrl) %>"><%= str_replace(' ', '&nbsp;', xg_html('READ_MORE')) %>&nbsp;&#187;</a></p>
    <?php
    if (count($tags)) { ?>
        <p><small><%= xg_html('TAGGED_X', Page_HtmlHelper::tagLinks($tags)) %></small></p>
    <?php
    } ?>
</div>
