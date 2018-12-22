<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('MY_FRIENDS_PHOTOS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline($title, array('count' => $this->numPhotos))%>
            <div class="xg_colgroup">
                <?php
                if ($showSidebar = count($this->photos) || count($this->friends)) { ?>
                    <div class="xg_1col first-child">
                        <?php
                        if (count($this->photos)) {
                            list($slideshowUrl, $feed_url) = Photo_SlideshowHelper::urls(array('friends' => true, 'sort' => $_GET['sort'])); ?>
                            <div class="xg_module">
                                <div class="xg_module_body">
                                    <p><%= xg_html('VIEW_PHOTOS_IN_SLIDESHOW') %></p>
                                    <p class="slideshow"><a class="play desc" href="<%= $slideshowUrl %>"><%= xg_html('START_SLIDESHOW') %></a></p>
                                </div>
                            </div>
                        <?php
                        }
                        if (count($this->friends)) {
                            $this->renderPartial('fragment_friends', 'user', array('screenName' => $this->_user->screenName, 'friends' => $this->friends));
                        } ?>
                    </div>
                <?php
                } ?>
                <div class="xg_2col<%= $showSidebar ? '' : ' first-child' %>">
                    <?php
                    if (count($this->photos) == 0 || $_GET['test_sparse']) { ?>
                            <div class="xg_module">
                                <div class="xg_module_body">
                                    <p><%= xg_html('YOUR_FRIENDS_DO_NOT_HAVE_PHOTOS') %></p>
                                </div>
                            </div>
                    <?php
                    } else {
                        $this->renderPartial('fragment_grid_ncolumns', 'photo', array(
                                'changeUrl' => $this->pageUrl, 'sortParamName' => 'sort', 'selectedSorting' => $this->sort,
                                'pageParamName' => 'page', 'photos' => $this->photos, 'curPage' => $this->page, 'numPages' => $this->numPages));
                    } ?>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.photo.photo.list') ?>
<?php xg_footer(); ?>
