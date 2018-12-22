<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
        <%= $this->renderPartial('fragment_navigation', '_shared') %>
		<%= xg_headline($this->titleHtml, array('avatarUser' => $this->contributor, 'count' => $this->totalDiscussionCount))%>
    	<?php XG_PageHelper::searchBar(array(
			'url' => XG_GroupHelper::addGroupId($this->_buildUrl('topic', 'search')),
			'buttonText' => $this->searchButtonText,
			'viewOptions' => $this->hideSorts ? NULL : $this->sortOptions,
		)); ?>
        <div class="xg_module">
            <div class="xg_module_body">
                <?php if (count($this->topicsAndComments)) { ?>
                <table class="categories">
                  <colgroup><col width="60%"></col><col width="15%"/></col><col width="25%"/></col></colgroup>
                    <thead>
                        <tr>
                            <th class="xg_lightborder"><%= $this->subTitle %></th>
                            <th class="bignum xg_lightborder" scope="col"><%= xg_html('REPLIES') %></th>
                            <th width="136" scope="col" class="xg_lightborder"><%= xg_html('LATEST_ACTIVITY') %></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($this->topicsAndComments as $topicOrComment) {
                            if ($topicOrComment->type == 'Comment') {
                                $topic = $this->topics[$topicOrComment->my->attachedTo];
                            } else {
                                $topic = $topicOrComment;
                            }
                            $categoryLink = $this->usingCategories ? array('href="' . xnhtmlentities($this->_buildUrl('topic', 'listForCategory', array('categoryId' => $topic->my->categoryId))) . '"', xnhtmlentities($this->categories[$topic->my->categoryId]->title)) : null;
                            $this->renderPartial('fragment_discussion', 'topic', array('topic' => $topic, 'showDescription' => !$this->showingReplies, 'categoryLink' => $categoryLink, 'reply' => $this->showingReplies ? $topicOrComment : null, 'featured' => false, 'highlightReply' => $this->sort == 'recentRepliesMade'));?>
                        <?php } ?>
                    </tbody>
                </table>
                <?php XG_PaginationHelper::outputPagination($this->totalCount, $this->pageSize); ?>
                <?php } else { ?>
                    <h3><%= $this->noDiscussionsHtml %></h3>
                    <?php if($this->userCanSeeAddTopicLinks && $this->myDiscussions) { ?>
                        <p><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl()) %> class="bigdesc add"><%= xg_html('START_A_DISCUSSION') %></a></p>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="xg_module_foot">
                <?php
                if ($this->showFeedLink) {
                    xg_autodiscovery_link($this->feedUrl, $this->titleText, 'atom');?>
                    <p class="left"><a class="desc rss" href="<%= xnhtmlentities($this->feedUrl) %>"><%= xg_html('RSS') %></a></p>
                <?php
                } ?>
            </div>
        </div>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>