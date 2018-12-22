<?php XG_IPhoneHelper::header('forum', $this->topic->title, NULL,  array('metaDescription' => $this->metaDescription, 'metaKeywords' => $this->metaKeywords)); ?>
<ul class="list detail forum">
	<li class="sparse">
		<%= xg_html('DISCUSSION_HAS_BEEN_DELETED') %>
	</li>
    <li class="add"><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl($this->category->id)) %> class=""><%= xg_html('START_A_DISCUSSION') %></a></li>
</ul>
<?php xg_footer(NULL,NULL); ?>