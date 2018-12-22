<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('DISCUSSION_DELETED')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <%= $this->showNavigation ? $this->renderPartial('fragment_navigation', '_shared') : '' %>
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_head notitle"></div>
                        <div class="xg_module_body">
                            <p><%= xg_html('DISCUSSION_HAS_BEEN_DELETED') %></p>
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
