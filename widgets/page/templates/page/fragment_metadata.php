<?php
/**
 * Displays the date (and, optionally, the author) of a Page or Comment
 *
 * @param $pageOrComment XN_Content|W_Content  The Page or Comment object
 * @param $showContributorName boolean  Whether to show the name of the contributor
 * @param $terse boolean  Whether to use shorter strings
 */
$contributor = XG_Cache::profiles($pageOrComment->contributorName);
if ($showContributorName) {
    if ($pageOrComment->type == 'Comment') { throw new Exception('Assertion failed'); }
    echo  xg_html('STARTED_BY_X_ON', xg_userlink($contributor), xg_date(xg_text('M_J_Y'), $pageOrComment->createdDate));
} elseif ($pageOrComment->type == 'Page') {
    echo  xg_html($terse ? 'STARTED_ON' : 'STARTED_DISCUSSION_ON',  xg_date(xg_text('M_J_Y'), $pageOrComment->createdDate));
} elseif (Page_CommentHelper::getAncestorCommentCount($pageOrComment)) {
    echo  xg_html('ADDED_REPLY_ON',  xg_date(xg_text('M_J_Y'), $pageOrComment->createdDate));
} else {
    echo  xg_html('ADDED_POST_ON',  xg_date(xg_text('M_J_Y'), $pageOrComment->createdDate));
}
