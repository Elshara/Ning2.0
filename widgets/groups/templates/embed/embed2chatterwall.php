<?php
$commentData = array();
foreach ($this->commentInfo['comments'] as $comment) {
    $commentData[] = array(
        'comment' => $comment,
        'canDelete' => Groups_CommentHelper::userCanDeleteComment($this->_user, $comment),
        'deleteEndpoint' => $this->_buildUrl('comment','delete', array('xn_out' => 'json')),
        'canApprove' => false);
}
XG_CommentHelper::outputComments(array(
        'commentData' => $commentData,
        'numComments' => $this->commentInfo['numComments'],
        'pageSize' => $this->pageSize,
        'attachedTo' => $this->group->id,
		'addCommentsHeader' => true,
        'currentUserCanSeeAddCommentSection' => true,
        'commentsClosedText' => null,
        'createCommentEndpoint' => $this->_buildUrl('comment','create', array('attachedTo' => $this->group->id)),
        'showFollowLink' => false,
        'feedUrl' => $this->feedUrl,
        'feedTitle' => xg_text('XS_POSTS', ucfirst(xg_username($this->profile))),
        'feedFormat' => 'atom',
        'newestCommentsFirst' => true));
?>
