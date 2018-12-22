<?php
/**
 * A post or reply in a discussion.
 *
 * @param $topic XN_Content|W_Content  The Topic object (the discussion)
 * @param $comment XN_Content|W_Content  The Comment object (the post or reply)
 * @param $highlight boolean  Whether to apply a visual highlight
 * @param $firstPage boolean  Whether this comment is on the first page
 * @param $lastPage boolean  Whether this comment is on the last page
 * @param $hasChildComments boolean  Whether this comment has child comments;
 *         used only for comments that haven't been marked as deleted
 * @param $threaded boolean Whether comments are threaded or flat
 * 
 */
$contributor = XG_Cache::profiles($comment->contributorName);
$href = xnhtmlentities(User::quickProfileUrl($comment->contributorName));
$contributorLink = xg_userlink(XG_Cache::profiles($comment->contributorName), '', true, $this->_buildUrl('topic', 
    'listForContributor', array('user' => $comment->contributorName)));
$linkAttributes = 'name="' . xnhtmlentities(str_replace(':', '', $comment->id)) . '" href="' . Forum_CommentHelper::url($comment) . '" title="' . xg_html('PERMALINK_TO_REPLY') . '';
$time = xg_elapsed_time($comment->createdDate, $showingMonth);
$avatarSize = 48;
if ($threaded) {
	$commentCount = Forum_CommentHelper::getAncestorCommentCount($comment);
	if ($commentCount > 0) {
		$avatarSize = 36;
	}
	$threadedClass = 'class="i'. $commentCount .'"';
}
?>
<li <%= $threadedClass %>>
    <a name="<%= xnhtmlentities(str_replace(':', '', $comment->id)) %>"></a>
    <div class="ib">
    	<a href="<%= $href %>"><img width="<%= $avatarSize %>" height="<%= $avatarSize %>" alt="" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($contributor,$avatarSize,$avatarSize)) %>"/></a>
    	<?php
    	if ($threaded) { ?>
    		<div class="dots"></div>
    	<?php
    	} ?>
    </div>
    <div class="tb">
      <span class="metadata">
        <?php echo xg_html('NO_PERMALINK_REPLY_BY_USER_WHEN', $contributorLink, '', xnhtmlentities($time));?>
      </span>
    <div class="post"><%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($comment->description), 171)) %></div>
<?php
if (Forum_SecurityHelper::currentUserCanSeeAddCommentLinks($topic) && (!$threaded || ($threaded && $commentCount + 1 < Forum_CommentHelper::MAX_COMMENT_LEVEL))) { ?>
    <p class="buttongroup"><a href="<%= xnhtmlentities($this->_buildUrl('comment', 'new', array('topicId' => $topic->id,'parentCommentId'=> $comment->id))) %>"><%= xg_html('REPLY_TO_THIS') %></a></p>
<?php
} ?>
</div>
</li>