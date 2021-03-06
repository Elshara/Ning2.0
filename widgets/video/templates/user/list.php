<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('POPULAR_CONTRIBUTORS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline($title, array('count' => $this->numUsers))%>
            <?php
            if (count($this->users) == 0 && !isset($this->searchFor)) { ?>
                <div class="xg_module">
                    <div class="xg_module_body">
                        <p><%= xg_html('NOBODY_HAS_ADDED_VIDEOS') %></p>
                        <p class="buttongroup">
                            <a <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('video', XG_MediaUploaderHelper::action())) %> class="button"><%= xg_html('ADD_VIDEOS') %></a>
                        </p>
                    </div>
                </div>
            <?php
            } else {
                $this->renderPartial('fragment_list', 'user', array(
                        'searchLabel' => xg_text('SEARCH_PEOPLE'), 'paginationUrl' => Video_HtmlHelper::addParamToUrl($this->pageUrl, 'sort', $this->sort['code']),
                        'sort' => $this->sort, 'page' => $this->page, 'numPages' => $this->numPages, 'searchFor' => $this->searchFor, 'users' => $this->users));
            } ?>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.video.index._shared'); ?>
<?php xg_footer(); ?>
