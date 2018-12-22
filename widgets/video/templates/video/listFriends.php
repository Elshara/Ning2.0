<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('VIDEOS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline(xg_text('MY_FRIENDS_VIDEOS'), array('count' => $this->numVideos))%>
            <div class="xg_colgroup">
                <?php
                $showSidebar = count($this->friends);
                if ($showSidebar) { ?>
                    <div class="xg_1col first-child">
                        <?php $this->renderPartial('fragment_friends', 'user', array('screenName' => $this->_user->screenName, 'friends' => $this->friends, 'showViewFriendsLink' => $this->numFriends > count($this->friends))); ?>
                    </div>
                <?php
                } ?>
                <div class="xg_2col<%= $showSidebar ? '' : ' first-child' %>">
                    <?php
                    if (count($this->videos) == 0) { ?>
                        <div class="xg_module">
                            <div class="xg_module_head notitle"></div>
                            <div class="xg_module_body">
                                <p><%= xg_html('YOUR_FRIENDS_DO_NOT_HAVE_VIDEOS') %></p>
                            </div>
                        </div>
                    <?php
                    } else {
                        $this->renderPartial('fragment_grid_onecolumn', 'video', array(
                                'videos' => $this->videos, 'changeUrl' => $this->pageUrl, 'sortParamName' => 'sort',
                                'selectedSorting' => $this->sort, 'pageParamName' => 'page',
                                'curPage' => $this->page, 'numPages' => $this->numPages));
                    } ?>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.video.index._shared'); ?>
<?php xg_footer(); ?>