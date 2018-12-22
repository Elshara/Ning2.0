<?php
/**
 * Displays the date (and, optionally, the author) of a Topic or Comment
 *
 * @param $topicOrComment XN_Content|W_Content  The Topic or Comment object
 * @param $showContributorName boolean  Whether to show the name of the contributor
 * @param $terse boolean  Whether to use shorter strings
 * @param $hideUserLinks boolean Whether to hide links to the Contributor and Latest Replier
 * @param $categoryLink array  The href and text for the category link as an array; if this is present then we use it
 */
if ($hideUserLinks) {
    $contributorLink = qh(xg_username(XG_Cache::profiles($topicOrComment->contributorName)));
} else {
    $contributorLink = xg_userlink(XG_Cache::profiles($topicOrComment->contributorName), NULL, TRUE, $this->_buildUrl('topic', 'listForContributor', array('user' => $topicOrComment->contributorName)));
}

if ($topicOrComment->type == 'Topic') {
    $topic = $topicOrComment;
    if ($topic->my->lastCommentContributorName && ! $terse) {
        $lastReplyHref = 'href="' . $this->_buildUrl('topic', 'showLastReply', array('id' => $topic->id)) . '"';
        if ($hideUserLinks) {
            $lastReplyContributorLink = qh(xg_username(XG_Cache::profiles($topic->my->lastCommentContributorName)));
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
        echo  xg_html('STARTED_BY_X_IN_Y_T', $contributorLink, $categoryLink[0], $categoryLink[1], xg_elapsed_time($topic->createdDate));
    } elseif ($showContributorName) {
        echo  xg_html('STARTED_BY_X_T', $contributorLink, xg_elapsed_time($topic->createdDate));
    } else {
        echo  xg_html('STARTED_T',  xg_elapsed_time($topic->createdDate));
    }
    if ($_GET['test_lastEntryDate']) { echo '<p style="background:yellow">lastEntryDate: ' . $topic->my->lastEntryDate . '</p>'; }
} else {
    $comment = $topicOrComment;
    if ($showContributorName) {
        echo  xg_html('ADDED_BY_X_T', $contributorLink, xg_elapsed_time($comment->createdDate));
    } else {
        $quickLink = 'href="http://' . $_SERVER['HTTP_HOST'] .'/xn/detail/' . $comment->id . '"';
        echo  xg_html('REPLIED_T',$quickLink, xg_elapsed_time($comment->createdDate));
    }
}
