<?php
if ($this->chattersToApprove) {
    echo '<li><a href="'.xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('comment','list',array('attachedToType' => 'User','attachedTo' => $this->_user->screenName))).'">' . xg_html('N_COMMENTS_ON_PAGE', $this->chattersToApprove) . '</a></li>';
}
if ($this->commentsToApprove) {
    echo '<li><a href="'.xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('blog','manageComments')).'">' . xg_html('N_BLOG_COMMENTS', $this->commentsToApprove) . '</a></li>';
}
if ($this->usersToApprove) { ?>
<li><a href="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('membership','listPending')) %>"><%= xg_html('N_NEW_MEMBERS', $this->usersToApprove) %></a></li>
<?php } ?>

