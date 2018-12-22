<?php
/**
 * Summary of a discussion topic, for the topic/list* pages.
 *
 * @param $topic XN_Content|W_Content  The Topic object
 * @param $comment XN_Content|W_Content A Comment whose text to display instead of the Topic's text (optional).
 * @param $showContributorName boolean  Whether to show the name of the contributor
 */
if ($topic) { // Workaround for BAZ-2471 [Jon Aquino 2007-04-10]
    $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
    $starter = XG_Cache::profiles($topic->contributorName);
    $link = xnhtmlentities($this->_buildUrl('topic', 'show', array('id' => $topic->id)));
    ?>
    <li _url="<%= $link %>" onclick="javascript:void(0)">
      <div class="ib">
    	  <%= xg_avatar($starter, 48, null, '', true) %>
    	</div>
      <div class="tb">
        <a href="<%= $link %>" class="title"><%= xg_excerpt($topic->title, 200) %></a> <?php
        if (! $comment) {
            $this->renderPartial('fragment_replyCount_iphone', '_shared', array('topic' => $topic)); 
        } ?>
        <span class="metadata"><?php
        $this->renderPartial('fragment_metadata', '_shared', array('topic' => $topic, 'post' => $comment ? $comment : $topic, 'showContributorName' => $showContributorName));?></span>
      </div>
    </li>
<?php
} ?>