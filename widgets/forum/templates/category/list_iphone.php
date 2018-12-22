<?php XG_IPhoneHelper::header('forum', $title = xg_text('FORUM'), NULL, NULL); ?>
<ul class="list forum">
<?php
XG_IPhoneHelper::previousPage($this->numPerPage);
foreach ($this->categories as $category) {
	$recentTopics = $this->categoryIdToRecentTopics[$category->id];
	if (count($recentTopics) == 0 && ! Forum_SecurityHelper::currentUserCanSeeAddTopicLinksForCategory($category)) { continue; } ?>
	<li class="category"><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'listForCategory', array('categoryId' => $category->id))) %>"><%= xnhtmlentities($category->title) %></a></li>
	<?php
    foreach ($recentTopics as $topic) {
    	 $this->renderPartial('fragment_topic_iphone', '_shared', array('topic' => $topic, 'comment' => null, 'showContributorName' => true));
    }
	if (count($recentTopics) == 0) { ?>
    	<li class="sparse"><%= xg_html('NO_DISCUSSIONS_IN_CATEGORY') %></li>
    <?php
	}
}
XG_IPhoneHelper::nextPage($this->showNextLink, $this->numPerPage); ?>
<li class="add"><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl($categoryId)) %> class=""><%= xg_html('START_A_DISCUSSION') %></a></li>
</ul>
<?php xg_footer(); ?>
