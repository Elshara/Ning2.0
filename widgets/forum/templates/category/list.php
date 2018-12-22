<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('DISCUSSION_FORUM')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_column xg_span-16 first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared') %>
			<%= xg_headline($title)%>
            <%= XG_GroupHelper::groupLink() %>
        	<?php XG_PageHelper::searchBar(array(
				'url' => XG_GroupHelper::addGroupId($this->_buildUrl('topic', 'search')),
				'buttonText' => xg_html('SEARCH_FORUM'),
			)); ?>
            <?php $this->_widget->dispatch('embed', 'sidebar'); ?>
                <div class="xg_column xg_span-10 last-child">
                    <div class="xg_module">
                        <div class="xg_module_head notitle"></div>
                        <?php
                        foreach ($this->categories as $category) {
                            $recentTopics = $this->categoryIdToRecentTopics[$category->id];
                            if (count($recentTopics) == 0 && ! Forum_SecurityHelper::currentUserCanSeeAddTopicLinksForCategory($category)) { continue; } ?>
                            <div class="xg_module_body category">
                                <h2><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'listForCategory', array('categoryId' => $category->id))) %>"><%= xnhtmlentities($category->title) %></a></h2>
                                <p>
                                    <%= $category->description %>
                                    <?php
                                    if (count($recentTopics) > 0) { ?>
                                        <a href="<%= xnhtmlentities($this->_buildUrl('topic', 'listForCategory', array('categoryId' => $category->id))) %>" class="nobr">
                                            <%= xg_html('VIEW_DISCUSSIONS') %>
                                        </a>
                                    <?php
                                    } ?>
                                </p>
                                <?php
                                foreach ($recentTopics as $topic) {
			      if (is_object($topic)) { ?>
                                    <div class="discussion vcard i1">
                                        <%= xg_avatar(XG_Cache::profiles($topic->contributorName), 36) %>
                                        <h3>
                                            <strong><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'show', array('id' => $topic->id))) %>"><%= xg_excerpt($topic->title, 200) %></a></strong>
                                            <?php $this->renderPartial('fragment_replyCount', '_shared', array('topic' => $topic)); ?>
                                        </h3>
                                        <p><small><?php $this->renderPartial('fragment_metadata', '_shared', array('topicOrComment' => $topic, 'showContributorName' => true)); ?></small></p>
                                    </div>
																						      <?php } /* is an object? */
                                }
                                if (count($recentTopics) == 0) { ?>
                                    <div class="discussion vcard i1">
                                        <p>
                                            <em><%= xg_html('NO_DISCUSSIONS_IN_CATEGORY') %></em><br />
                                            <a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl($category->id)) %>><%= xg_html('ADD_A_DISCUSSION') %></a>
                                        </p>
                                    </div>
                                <?php
                                } ?>
                            </div>
                        <?php } ?>
                        <div id="category-view-pagination" class="xg_module_body">
                            <?php XG_PaginationHelper::outputPagination($this->totalCategories, $this->numPerPage); ?>
                        </div>
                        <div class="xg_module_foot">
                        <?php
                        if ($this->showFeedLink || $this->showFollowLink) {
                            if ($this->showFeedLink) {
                                $feedUrl = $this->_buildUrl('topic', 'list', array('feed' => 'yes', 'xn_auth' => 'no'));
                                // do we still need $feedUrlReplies? it seems we are not using it? [ywh 2008-06-05]
                                $feedUrlReplies = $this->_buildUrl('topic', 'list', array('feed' => 'yes',
                                            'xn_auth' => 'no', 'sort' => 'mostRecent'));
                                xg_autodiscovery_link($feedUrlReplies,  xg_text('LATEST_REPLIES'), 'atom');
                                xg_autodiscovery_link($feedUrl, $title, 'atom'); ?>
                                <p class="left"><a class="desc rss" href="<%= xnhtmlentities($feedUrl) %>"><%= xg_html('RSS') %></a></p>
                            <?php
                            }
                            if ($this->showFollowLink) {
                                $this->renderPartial('fragment_follow', '_shared');
                            }
                        } ?>
                        </div>
                    </div>
                </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
