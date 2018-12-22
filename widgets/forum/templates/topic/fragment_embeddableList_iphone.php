<?php
if ($this->myDiscussions || count($this->topicsAndComments) > 0) { ?>
<ul class="list forum">
<li class="section"><a href="<%=$this->_buildUrl('topic', 'listForContributor', array('user' => $_GET['user'])) %>"><%= xg_html('FORUM_POSTS') %></a></li>
<?php
$this->renderPartial('fragment_items_iphone');
if ($this->myDiscussions) {
?>
<li class="add"><a href="<%=$this->_buildUrl('topic', 'new') %>"><%= xg_html('START_A_DISCUSSION') %></a></li>
<?php
} ?>
</ul>
<?php
} ?>