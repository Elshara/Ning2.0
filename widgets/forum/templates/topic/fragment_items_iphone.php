<?php
foreach ($this->topicsAndComments as $topicOrComment) {
    $comment = $topicOrComment->type == 'Comment' ? $topicOrComment : NULL;
    $topic = $topicOrComment->type == 'Comment' ? $this->topics[$topicOrComment->my->attachedTo] : $topicOrComment;
    $this->renderPartial('fragment_topic_iphone', '_shared', array('topic' => $topic, 'comment' => $comment, 'showContributorName' => true));
}
?>