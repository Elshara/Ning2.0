<?php
/** Partial template that renders a single chatter
 *
 * @param $comment The chatter comment object
 * @param $canDelete Can the current user delete the chatter?
 * @param $canApprove Can the current user approve the chatter?
 * @param $isResponse Whether to highlight the chatter with the "response" CSS class
 */

$classes = array('comment');
$isCommenter = $comment->contributorName == XN_Profile::current()->screenName;
if ($isResponse) { $classes[] = 'response'; }
/* If the chatter hasn't been approved yet, highlight it */
if ($comment->my->approved == 'N') {
    $classes[] = 'comment-new';
}
$profile = XG_Cache::profiles($comment->contributorName);
$timestamp = strtotime($comment->createdDate);
?>
<li id="chatter-<%= $comment->id %>">
<?php
    $href = xnhtmlentities($this->_widget->buildUrl('comment', 'new', array('screenName' => $comment->contributorName)));
    $contributorLink = xg_userlink(XG_Cache::profiles($comment->contributorName), '', true, $this->_buildUrl('profile',
        'show', array('id' => $comment->contributorName)));
    $time = xg_elapsed_time($comment->createdDate, $showingMonth);
    ?>
    <div class="ib"><%= xg_avatar($profile, 48, null, '', true) %></div>
    <div class="tb">
      <span class="metadata"><%= xg_html('AT_TIME_ON_DATE_USERNAME_SAID', xg_date(xg_text('G_IA'), $timestamp), xg_date(xg_text('M_J_Y'),$timestamp), xnhtmlentities(xg_username($profile))) %></span>
		  <div class="post"><%= xg_nl2br(xg_resize_embeds($comment->description, 171)) %></div>
		<?php
		if ($this->isMyPage && $this->_user->screenName !== $comment->contributorName) { ?>
		  <p class="buttongroup"><a href="<%= $href %>"><%= xg_html('COMMENT_BACK') %></a></p>
		<?php
		} ?>
	</div>
<?php /* This is used for retrieving the next earliest chatter after in-page actions */ ?>
    <span class="chatter-timestamp" id="chatter-timestamp-<%= $timestamp %>"></span>
</li>
