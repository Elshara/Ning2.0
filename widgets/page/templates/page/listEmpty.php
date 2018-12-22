<?php xg_header(W_Cache::current('W_Widget')->dir, $this->title); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared') %>
            <%= xg_headline($this->title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                            <div class="xg_module_body">
                                <p class="clear"><%= xnhtmlentities($this->noDiscussionsMessage) %></p>
                                <?php if ($this->canCreatePage) { ?>
                                    <p><a href="<%= xnhtmlentities($this->_buildUrl('page', 'new')) %>" class="desc add"><%= xg_html('ADD_A_PAGE') %></a></p>
                                <?php } ?>
                            </div>
                        <?php
                        if (! XG_App::appIsPrivate()) {
                            xg_autodiscovery_link($this->feedUrl, $this->title, 'atom');
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
