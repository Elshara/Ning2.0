<?php XG_IPhoneHelper::header(W_Cache::current('W_Widget')->dir,  $title = xg_text('FORUM'), NULL, NULL); ?>

<ul class="list forum">

<?php
foreach($this->categorySet as $category) {
    $link = $this->_buildUrl('topic', 'listForCategory', array('categoryId' => $category->id));
    $topic = $this->topics[$category->id];
    ?>
    <li class="category"><a href="<%= xnhtmlentities($link) %>"><%= xnhtmlentities($category->title) %></a></li>
    <?php
    if ($topic) {
        $lastReplyName = xnhtmlentities(xg_username($topic->my->lastCommentContributorName));
        $topicCreator = xnhtmlentities(xg_username($topic->contributorName));
        $lastReplyHref = $this->_buildUrl('topic', 'showLastReply', array('id' => $topic->id));
        $comment = $topic->type == 'Comment' ? $topic : NULL;
        $topic = $topic->type == 'Comment' ? $this->topics[$topic->my->attachedTo] : $topic;
        $this->renderPartial('fragment_topic_iphone', '_shared', array('topic' => $topic, 'comment' => $comment, 'showContributorName' => $this->showContributorName));
    } else { ?>
        <li class="sparse"><%= xg_html('THERE_ARE_NO_DISCUSSIONS_YET') %></li>
        <?php
    }
} ?>
<!--      <li class="more"><a class="next" href="#" target="_replace">Next 20 Discussions...</a></li> -->
      <li class="add"><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl($categoryId)) %> class=""><%= xg_html('START_A_DISCUSSION') %></a></li>
</ul>

<?php xg_footer(NULL,NULL); ?>