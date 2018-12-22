<?php
if (count($this->comments)) {
  foreach ($this->comments as $comment) {
      $this->renderPartial('fragment_comment_iphone', 'topic', array('topic' => $this->topic, 'comment' => $comment, 'highlight' => $comment->id == $this->currentCommentId, 'firstPage' => $firstPage, 'lastPage' => $lastPage, 'hasChildComments' => $this->commentIdsWithChildComments[$comment->id]));
  }
}
XG_IPhoneHelper::pagination($this->totalCount, $this->pageSize, xg_html('NEXT_REPLIES_ELLIPSIS'));
