<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('PAGES')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared') %>
			<%= xg_headline($this->title, array('count' => $this->totalCount))%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
					<?php XG_PageHelper::searchBar(array(
						'url' => $this->_buildUrl('page', 'list'),
						'buttonText' => xg_html('SEARCH_PAGES'),
						'sortOptions' => $this->pagePickerOptions,
					))?>
                    <div class="xg_module">
                        <div class="xg_module_body">
                            <?php
                            foreach ($this->pagesAndComments as $pageOrComment) {
                                $comment = $pageOrComment->type == 'Comment' ? $pageOrComment : NULL;
                                $page = $pageOrComment->type == 'Comment' ? $this->pages[$pageOrComment->my->attachedTo] : $pageOrComment;
                                $this->renderPartial('fragment_page', 'page', array('page' => $page, 'comment' => $comment, 'showListForContributorLink' => $this->showListForContributorLinks, 'showContributorName' => $this->showContributorName));
                            }
                            XG_PaginationHelper::outputPagination($this->totalCount, $this->pageSize); ?>
                        </div>
                        <?php
                        if (! XG_App::appIsPrivate()) {
                            xg_autodiscovery_link($this->feedUrl, $this->title, 'atom'); ?>
                            <div class="xg_module_foot">
                                <p class="left"><%= xg_html('RSS_FEED_LABEL', 'class="desc rss" href="' . xnhtmlentities($this->feedUrl) . '"', xnhtmlentities($this->feedDescription)) %></p>
                            </div>
                        <?php
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