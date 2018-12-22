<?php
/** Partial template for rendering a list of chatters
 * @param $chatters array chatter objects
 * @param $permitLinks boolean optional defaults to true; set to false to
 *   hide the approve/delete links even when they would be otherwise visible
 *   (used in 'view as others see it' profile page view)
 * @param $responder string screen name of the user whose chatters should be highlighted with the "response" CSS class
 */

$permitLinks = isset($permitLinks) ? $permitLinks : true;
$showingThread = isset($showingThread) ? $showingThread : false;
 ?>
<div id="xg_profiles_chatterwall_list">
<?php if (count($chatters)) {
    $chatterCount = count($chatters);
    $counter = 1;
    foreach ($chatters as $chatter) {
        $this->renderPartial('fragment_chatter','chatter',array('comment' => $chatter, 'canDelete' => $permitLinks && Profiles_CommentHelper::userCanDeleteChatter($this->_user, $chatter),
                                                                             'canApprove' => $permitLinks && Profiles_CommentHelper::userCanApproveChatter($this->_user,$chatter),
                                                                             'isMyPage' => $permitLinks && $isMyPage,
                                                                             'showingThread' => $showingThread,
                                                                             'friendStatus' => $friendStatus[$chatter->contributorName],
                                                                             'isResponse' => $chatter->contributorName == $responder,
                                                                             'lastChild' => $counter == $chatterCount));
        $counter ++;
    }
} ?>
  <ul class="list chatter nobullets">
    <li class="sparse" id="xg_profiles_chatterwall_empty" <%= count($chatters) ? 'style="display:none;"' : '' %>><%= xg_html('NO_COMMENTS_YET') %></li>
  </ul>
</div>

