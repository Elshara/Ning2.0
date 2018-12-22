<?php
/** Partial template that renders a single chatter
 *
 * @param $comment W_Content | XN_Content The chatter comment object
 * @param $canDelete boolean Can the current user delete the chatter?
 * @param $canApprove boolean Can the current user approve the chatter?
 * @param $isResponse boolean Whether to highlight the chatter with the "response" CSS class
 * @param $lastChild boolean Whether the chatter is the last item in the list and should use the last-child class
 */

$classes = array('comment', 'vcard', 'xg_lightborder');
$isCommenter = $comment->contributorName == XN_Profile::current()->screenName;
if ($isResponse) { $classes[] = 'response'; }
/* If the chatter hasn't been approved yet, highlight it */
if ($comment->my->approved == 'N') {
    $classes[] = 'comment-new';
}
if ($lastChild) {
    $classes[] = 'last-child';
}
$profile = XG_Cache::profiles($comment->contributorName);
$timestamp = strtotime($comment->createdDate);
?>
<dl id="chatter-<%= $comment->id %>" class="<%= implode(' ', $classes) %>">
<?php
/* If the chatter is in the list to be rendered, is not approved, and the current user can't approve it,
 * assume it was just added, so show the user a message saying it has to be approved first before it can be seen */
if (($comment->my->approved == 'N') && (! $canApprove)) { ?>
    <dd><%= xg_html('YOUR_COMMENT_HAS_BEEN_ADDED_BUT'); %></dd>
<?php } else { ?>
  <dt><%= xg_html('AT_TIME_ON_DATE_USERNAME_SAID', xg_date(xg_text('G_IA'), $timestamp), xg_date(xg_text('F_JS_Y'),$timestamp), '<a class="fn url" href="' . xnhtmlentities(User::quickProfileUrl($profile->screenName)) . '"><img class="photo" src="' . XG_UserHelper::getThumbnailUrl($profile,48,48) . '" height="48" width="48" alt=""/>' . xnhtmlentities(xg_username($profile)) . '</a>') %></dt>
  <dd><%= xg_nl2br(xg_resize_embeds($comment->description, 475)) %>
  <?php if ($isMyPage && !$isCommenter) { ?>
    <br /><small>
        <?php if (!$showingThread) { ?>
            <a href="<%= User::quickProfileUrl($comment->contributorName) %>#add_comment" class="nobr"><%= xg_html('COMMENT_BACK') %></a> &nbsp;
            <a href="<%= $this->_widget->buildUrl('comment', 'thread', array('screenName' => $comment->contributorName)) %>" class="nobr"><%= xg_html('VIEW_THREAD') %></a> &nbsp;
        <?php } ?>
		<span><%= $friendStatus == 'friend'
			? xg_send_message_link($comment->contributorName, null, xg_text('SEND_MESSAGE'),'nobr')
			: xg_add_as_friend_link($comment->contributorName, $friendStatus, 'nobr') %></span>
    </small>
  <?php }
  if ($canDelete && $comment->my->approved == 'Y') {
      echo '<a href="javascript:void(0)" id="chatter-remove-'.$comment->id.'" class="chatter-remove icon delete">'.xg_html('DELETE_COMMENT').'</a>';
  }
  ?></dd>
  <?php if ($comment->my->approved == 'N') {
      $actionLinks = array();
        if ($canApprove) {
            $actionLinks[] = '<a href="javascript:void(0)" class="chatter-approve" id="chatter-approve-'.$comment->id.'">' . xg_html('APPROVE') . '</a>';
        }
        if ($canDelete) {
            $actionLinks[] = '<a href="javascript:void(0)" class="chatter-remove" id="chatter-remove-'.$comment->id.'">' . xg_html('DELETE') .'</a>';
        }
        if (count($actionLinks)) {
            /* The span needs and ID so it can be removed dynamically if the comment is approved in-page */
            echo '<dd class="item_approval"><div class="pad5 right notification" id="chatter-spacer-'.$comment->id.'"><small><strong>' . xg_html('CONTENT_AWAITING_APPROVAL') . ":</strong> " . implode(" " . xg_html('OR') . " ", $actionLinks) . '</small></div></dd>';
        }
  }?>
<?php } /* show chatter or not-yet-approved message? */ ?>
<?php /* This is used for retrieving the next earliest chatter after in-page actions */ ?>
    <span class="chatter-timestamp" id="chatter-timestamp-<%= $timestamp %>"></span>
</dl>
