<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('POPULAR_CONTRIBUTORS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared') %>
			<%= xg_headline($title, array('count' => $this->totalCount))%>
            <?php
            if (count($this->users) == 0 && ! mb_strlen($this->searchTerm)) { ?>
                <div class="xg_module">
                    <div class="xg_module_body">
						<p><%= xg_html('NOBODY_HAS_ADDED_PAGES') %></p>
                        <p class="buttongroup">
                            <a href="<%= xnhtmlentities($this->_buildUrl('page','new')) %>" class="button"><%= xg_html('ADD_A_PAGE') %></a>
                        </p>
                    </div>
                </div>
            <?php
            } else { ?>
				<?php XG_PageHelper::searchBar(array(
					'url' => $this->_buildUrl('user', 'list'),
					'buttonText' => xg_html('SEARCH'),
				))?>
                <div class="xg_module">
                    <div class="xg_module_body">
                        <?php
                        if (count($this->users) == 0) { ?>
                            <div><p><%= xg_html('WE_COULD_NOT_FIND_ANYONE_MATCHING') %></p></div>
                        <?php
                        } ?>
                        <div class="vcards">
                            <?php
                            foreach ($this->users as $user) { ?>
                                <dl class="vcard left">
                                    <dt><%= xg_avatar(XG_Cache::profiles($user->title), 54) %>  <a href="<%= xnhtmlentities(User::quickProfileUrl($user->title)) %>"><%= xnhtmlentities(xg_username(XG_Cache::profiles($user->title))) %></a></dt>
                                    <dd>
                                        <a href="<%= $this->_buildUrl('page', 'listForContributor', '?user=' . $user->title) %>"><%= xg_html('VIEW_DISCUSSIONS') %></a>
                                    </dd>
                                </dl>
                            <?php
                            } ?>
                        </div>
                        <p class="clear right"><a href="<%= W_Cache::getWidget('profiles')->buildUrl('members', '') %>"><%= xg_html('VIEW_ALL_PEOPLE_ON_X', xnhtmlentities(XN_Application::load()->name)) %> &#187;</a></p>
                        <%= XG_PaginationHelper::outputPagination($this->totalCount, $this->pageSize); %>
                    </div>
                </div>
            <?php
            } ?>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
