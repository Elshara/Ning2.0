<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('DISCUSSION_FORUM')); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
        <%= $this->renderPartial('fragment_navigation', '_shared') %>
		<%= xg_headline($title, array('count' => count($this->categorySet)))%>
		<?php XG_PageHelper::searchBar(array(
			'url' => XG_GroupHelper::addGroupId($this->_buildUrl('topic', 'search')),
			'buttonText' => xg_html('SEARCH_FORUM'),
			'sortOptions' => $this->hideSorts ? NULL : $this->sortOptions,
			'viewOptions' => $this->hideSorts ? NULL : $this->pageViewOptions,
		)); ?>
		<?php if ($this->featuredTopics && (!isset($_GET['page']) || $_GET['page'] < 2)) {
			$this->renderPartial('fragment_featured', '_shared', array('featuredTopics' => $this->featuredTopics, 'showFeaturedViewAll' => $this->showFeaturedViewAll));
		} ?>
		<div class="xg_module">
            <div class="xg_module_body">
                <table class="categories">
                  <colgroup><col width="60%"></col><col width="15%"/></col><col width="25%"/></col></colgroup>
                    <thead>
                        <tr>
                            <th scope="col" class="xg_lightborder"><%= xg_html('CATEGORIES') %></th>
                            <th class="bignum xg_lightborder" scope="col"><%= xg_html('DISCUSSIONS') %></th>
                            <th width="30%" scope="col" class="xg_lightborder"><%= xg_html('LATEST_ACTIVITY') %></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        $categoryCount = count($this->categorySet);
                        foreach($this->categorySet as $category) {
                            $link = $this->_buildUrl('topic', 'listForCategory', array('categoryId' => $category->id));
                            $topic = $this->topics[$category->id];
                            if ($topic) {
                                $lastReplyName = xnhtmlentities(xg_username($topic->my->lastCommentContributorName));
                                $topicCreator = xnhtmlentities(xg_username($topic->contributorName));
                                $lastReplyHref = $this->_buildUrl('topic', 'showLastReply', array('id' => $topic->id));
                            }
                            $lastChild = $count == $categoryCount ? 'class="last-child"' : "";
                            ?>
                            <tr <%= $lastChild %>>
                                <td class="xg_lightborder"><h3><a href="<%= xnhtmlentities($link) %>"><%= xnhtmlentities($category->title) %></a></h3>
                                    <p class="cat_desc small"><%= $category->description %></p>
                                </td>
                                <td class="bignum xg_lightborder"><%= $category->my->discussionCount %></td>
                                <td class="xg_lightborder">
                                    <?php if ($topic && $category->my->discussionCount) { ?>
                                        <%= xg_elapsed_time($topic->my->lastEntryDate) %>
                                        <?php if ($lastReplyName) { ?>
                                            <br/><a href="<%= $lastReplyHref %>"><%= xg_html('REPLY_BY_X', $lastReplyName)%></a>
                                        <?php } elseif ($topicCreator) { ?>
                                            <br/><a href="<%= $lastReplyHref %>"><%= xnhtmlentities($topic->title) %></a>
                                            <br/><%= xg_html('BY_X', $topicCreator)%>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <%= xg_html('NO_RECENT_ACTIVITY') %>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                        $count ++;
                        } ?>
                    </tbody>
                </table>
                <?php XG_PaginationHelper::outputPagination($this->totalCategories, $this->numPerPage); ?>
            </div>
            <div class="xg_module_foot">
                <?php
                if ($this->showFeedLink) {
                    xg_autodiscovery_link($this->feedUrl, $this->titleText, 'atom');
                    ?>
                    <p class="left"><a class="desc rss" href="<%= xnhtmlentities($this->feedUrl) %>"><%= xg_html('RSS') %></a></p>
                <?php
                }
                if ($this->showFollowLink) {
                    $this->renderPartial('fragment_follow', '_shared');
                } ?>
            </div>
        </div>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>