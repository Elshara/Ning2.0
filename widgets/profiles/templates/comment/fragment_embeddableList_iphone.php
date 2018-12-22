<ul class="list wall">
    <li class="section"  numComments="<%= $this->commentInfo['numComments'] %>"><%= $this->commentInfo['numComments'] ? xg_html('COMMENT_WALL_N_COMMENTS', $this->commentInfo['numComments']) : xg_html('COMMENT_WALL') %></li>
	<?php
	if (count($this->commentInfo['comments'])) {
    	$this->renderPartial('fragment_comments', 'comment');
	} else { ?>
	<li class="sparse"><%= xg_html('NO_COMMENTS_YET') %></li>
	<?php
	} ?>
	<?php if ($this->showAddLink) {?>
		<li class="add"><a href="<%= xnhtmlentities($this->_widget->buildUrl('comment', 'new', array('screenName' => $this->screenName))) %>"><%= xg_html('ADD_A_COMMENT') %></a></li>
	<?php }?>
</ul>
