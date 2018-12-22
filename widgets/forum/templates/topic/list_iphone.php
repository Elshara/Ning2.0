<?php
XG_IPhoneHelper::header('forum', $this->titleText, NULL, NULL);
?>
<ul class="list forum">
<?php
XG_IPhoneHelper::previousPage($this->pageSize);
if($this->category){ ?>
        <li class="category"><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'listForCategory', array('categoryId' => $this->category->id))) %>"><%= xnhtmlentities($this->category->title) %></a></li>
    <?php
}
if($this->tag){ ?>
        <li class="category"><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'listForTag', array('tag' => $this->tag))) %>"><%= xg_html('TAGGED_X',xnhtmlentities($this->tag)) %></a></li>
    <?php
}
foreach ($this->topicsAndComments as $topicOrComment) {
    $comment = $topicOrComment->type == 'Comment' ? $topicOrComment : NULL;
    $topic = $topicOrComment->type == 'Comment' ? $this->topics[$topicOrComment->my->attachedTo] : $topicOrComment;
    $this->renderPartial('fragment_topic_iphone', '_shared', array('topic' => $topic, 'comment' => $comment, 'showContributorName' => $this->showContributorName));
}
XG_IPhoneHelper::nextPage($this->showNextLink, $this->pageSize);
?>
      <li class="add"><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl($categoryId)) %> class=""><%= xg_html('START_A_DISCUSSION') %></a></li>
</ul>

<?php xg_footer(NULL,NULL); ?>