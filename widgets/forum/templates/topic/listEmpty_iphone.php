<?php XG_IPhoneHelper::header('forum', $this->titleText, NULL, NULL); ?>
<ul class="list forum">
	<li class="category"><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'listForCategory', array('categoryId' => $this->category->id))) %>"><%= xnhtmlentities($this->category->title) %></a></li>
	<li class="sparse"><%= xg_html('THERE_ARE_NO_DISCUSSIONS_YET') %></li>
	<li class="add"><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl($categoryId)) %> class=""><%= xg_html('START_A_DISCUSSION') %></a></li>
</ul>
<?php xg_footer(NULL,NULL); ?>