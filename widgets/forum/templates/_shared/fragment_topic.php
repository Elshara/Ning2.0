<?php
/**
 * Summary of a discussion topic; used in main page embeds
 *
 * @param $topic XN_Content|W_Content  The Topic object
 * @param $comment XN_Content|W_Content A Comment whose text to display instead of the Topic's text (optional).
 * @param $showListForContributorLink boolean  Whether to show the "X's discussions" link
 * @param $showContributorName boolean  Whether to show the name of the contributor
 * @param $showAvatar boolean  Whether to show the contributor of the Topic/Comment (defaults to true);
 * @param $avatarSize integer  Width and height of the avatar, in pixels (optional)
 * @param $lineBreakAfterTitle boolean  Whether the comment count should appear on the line after the title
 * @param $showExcerptAndTags boolean  Whether to show the discussion except and tags (defaults to true)
 * @param $categoryLink array  The href and text for the category link as an array; if this is present then we use it
 */
if ($topic) { // Workaround for BAZ-2471 [Jon Aquino 2007-04-10]
    $showAvatar = is_null($showAvatar) ? true : $showAvatar;
    $showExcerptAndTags = is_null($showExcerptAndTags) ? true : $showExcerptAndTags;
    $avatarSize = $avatarSize ? $avatarSize : 48;
    $tags = XN_Tag::parseTagString($topic->my->topTags);
    $focus = $comment ? $comment : $topic;
    $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
    $detailUrl = $comment ? Forum_CommentHelper::url($comment) : XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'topic', 'show', array('id' => $topic->id));
    $contributor = XG_Cache::profiles($focus->contributorName); ?>
    <div class="discussion vcard">
        <?php
        if ($showAvatar) { ?>
            <div class="author"><%= xg_avatar($contributor, $avatarSize) %></div>
        <?php
        } ?>
        <div class="topic <%= $showAvatar ? 'indent' : '' %>">
            <h3>
                <a href="<%= xnhtmlentities($this->_buildUrl('topic', 'show', array('id' => $topic->id))) %>"><%= xg_excerpt($topic->title, 200) %></a>
                <?php
                if (! $comment) {
                    $this->renderPartial('fragment_replyCount', '_shared', array('topic' => $topic, 'lineBreakBefore' => $lineBreakAfterTitle));
                } ?>
            </h3>
            <?php if ($showExcerptAndTags) { ?>
                <p class="small <%= $showAvatar ? 'indent' : '' %>"><%= xg_excerpt($focus->description, 200, null, $excerpted, true, 50, true) %> <?php if (mb_strlen($focus->description) > 200) { ?><?php } ?></p>
                <?php
                if (count($tags)) { ?>
                    <p><%= xg_html('TAGGED_X', Forum_HtmlHelper::tagLinks($tags)) %></p>
                <?php
                } ?>
            <?php } ?>
            <p class="small xg_lightfont <%= $showAvatar ? 'indent' : '' %> ">
                    <?php
                    $this->renderPartial('fragment_metadata', '_shared', array('topicOrComment' => $comment ? $comment : $topic, 'showContributorName' => $showContributorName, 'hideUserLinks' => true, 'categoryLink' => $categoryLink));
                    if ($showListForContributorLink) { ?>
                        &ndash; <a href="<%= xnhtmlentities($this->_buildUrl('topic', 'listForContributor', array('user' => $contributor->screenName))) %>"><%= xg_html('XS_DISCUSSIONS_LOWERCASE', xnhtmlentities(xg_username($contributor))) %> &#187;</a>
                    <?php
                    } ?>
            </p>
        </div>
    </div>
<?php
}