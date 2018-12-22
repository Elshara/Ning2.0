<?php xg_header(W_Cache::current('W_Widget')->dir, $this->titleText ? $this->titleText : xg_text('DISCUSSION_FORUM')); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
        <%= $this->renderPartial('fragment_navigation', '_shared') %>
		<%= xg_headline($this->titleHtml, array('count' => $this->totalCount))%>
		<?php XG_PageHelper::searchBar(array(
			'url' => XG_GroupHelper::addGroupId($this->_buildUrl('topic', 'search')),
			'buttonText' => xg_html('SEARCH_FORUM'),
		))?>
        <div class="xg_module">
        <?php if ($this->featuredTopics) {
            $this->renderPartial('fragment_featured', '_shared', array('featuredTopics' => $this->featuredTopics, 'showFeaturedViewAll' => false));
        } ?>
        </div>
        <?php XG_PaginationHelper::outputPagination($this->totalCount, $this->pageSize); ?>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>