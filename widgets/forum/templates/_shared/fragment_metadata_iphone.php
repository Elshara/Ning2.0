<?php
/**
 * Displays the date (and, optionally, the author) of a Topic or Comment
 *
 * @param $topic XN_Content|W_Content  The Topic object (or associated Topic object if $post is a Comment)
 * @param $post XN_Content|W_Content  The Topic or Comment object
 * @param $showContributorName boolean  Whether to show the name of the contributor
 * @param $terse boolean  Whether to use shorter strings
 * @param $hideUserLinks boolean Whether to hide links to the Contributor and Latest Replier
 * @param $categoryLink array  The href and text for the category link as an array; if this is present then we use it
 */
if ($hideUserLinks) {
    $contributorLink = xg_username(XG_Cache::profiles($topic->contributorName));
} else {
    $contributorLink = xg_userlink(XG_Cache::profiles($topic->contributorName), NULL, TRUE, $this->_buildUrl('topic', 'listForContributor', array('user' => $topic->contributorName)));
}

if ($topic->my->lastCommentContributorName) {
    $lastReplyHref = 'href="' . $this->_buildUrl('topic', 'showLastReply', array('id' => $post->id)) . '"';
    if ($hideUserLinks) {
        $lastReplyContributorLink = xg_username(XG_Cache::profiles($topic->my->lastCommentContributorName));
    } else {
        $lastReplyContributorLink = xg_userlink(XG_Cache::profiles($topic->my->lastCommentContributorName), NULL, TRUE, $this->_buildUrl('topic', 'listForContributor', array('user' => $topic->my->lastCommentContributorName)));
    }
    if ($showContributorName && !is_null($categoryLink)) {
        echo  xg_html('STARTED_BY_X_IN_Y_LAST_REPLY_LINK_BY_X_T', $contributorLink, $categoryLink[0], $categoryLink[1], $lastReplyHref, $lastReplyContributorLink, xg_elapsed_time($topic->my->lastCommentCreatedDate));
    } elseif ($showContributorName) {
        echo  xg_html('STARTED_BY_X_LAST_REPLY_LINK_BY_X_T', $contributorLink, $lastReplyHref, $lastReplyContributorLink, xg_elapsed_time($topic->my->lastCommentCreatedDate));
    } else {
        echo  xg_html('STARTED_DISCUSSION_LAST_REPLY_LINK_BY_X_T',  $lastReplyHref, $lastReplyContributorLink, xg_elapsed_time($topic->my->lastCommentCreatedDate));
    }
} elseif ($showContributorName && !is_null($categoryLink)) {
    echo  xg_html('STARTED_BY_X_IN_Y_T', $contributorLink, $categoryLink[0], $categoryLink[1], xg_elapsed_time($post->createdDate));
} elseif ($showContributorName) {
    echo  xg_html('STARTED_BY_X_T', $contributorLink, xg_elapsed_time($post->createdDate));
} else {
    echo  xg_html('STARTED_T',  xg_elapsed_time($post->createdDate));
}
?>