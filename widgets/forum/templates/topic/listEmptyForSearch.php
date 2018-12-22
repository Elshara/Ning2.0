<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('DISCUSSION_FORUM')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared') %>
			<%= xg_headline($this->titleHtml, array('count' => 0)) %>
            <%= XG_GroupHelper::groupLink() %>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                	<?php XG_PageHelper::searchBar(array(
						'url' => XG_GroupHelper::addGroupId($this->_buildUrl('topic', 'search')),
						'buttonText' => xg_html('SEARCH_AGAIN'),
					))?>
                    <div class="xg_module">
                        <div class="xg_module_body">
                            <h3><%= $this->noDiscussionsHtml %></h3>
                            <h4><%= xg_html('SUGGESTIONS') %></h4>
                            <ul>
                                <li><%= xg_html('MAKE_SURE_WORDS_SPELLED') %></li>
                                <li><%= xg_html('TRY_DIFFERENT_KEYWORDS') %></li>
                            </ul>
                        </div>
                        <div class="xg_module_foot">
                            <p class="left">
                                <?php
                                if (Forum_SecurityHelper::currentUserCanSeeAddTopicLinks()) { ?>
                                    <%= xg_html('VIEW_DISCUSSIONS_OR_START_DISCUSSION', 'href="' . xnhtmlentities($this->_buildUrl('index', 'index')) . '"', XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl())) %>
                                <?php
                                } else { ?>
                                    <a href="<%= xnhtmlentities($this->_buildUrl('index', 'index')) %>"><%= xg_html('VIEW_ALL_DISCUSSIONS') %></a>
                                <?php
                                } ?>
                            </p>
                        </div>
                        <?php
                        if (! XG_App::appIsPrivate() && ! XG_GroupHelper::groupIsPrivate()) {
                            xg_autodiscovery_link($this->feedUrl, $this->titleText, 'atom');
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
