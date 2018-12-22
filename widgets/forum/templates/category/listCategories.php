<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('DISCUSSION_FORUM')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared') %>
			<%= xg_headline($title)%>
            <%= XG_GroupHelper::groupLink() %>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body category">
                            <?php foreach ($this->categories as $category) {
                                $link = $this->_buildUrl('topic', 'listForCategory', array('categoryId' => $category->id)); ?>
                                <h2><a href="<%= xnhtmlentities($link) %>"><%= xnhtmlentities($category->title) %></a></h2>
    							<p><%= $category->description %> <a href="<%= xnhtmlentities($link) %>"><%= xg_html('VIEW_DISCUSSIONS') %></a></p>
                            <?php }
                            XG_PaginationHelper::outputPagination($this->totalCategories, $this->numPerPage);
                            ?>
                        </div>
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
