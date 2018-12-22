<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('VIDEOS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline(xg_text('APPROVE_VIDEOS'), array('count' => $this->query->getTotalCount()))%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child" id="column">
                    <?php
                    if (count($this->videos) == 0) {
                        $this->renderPartial('fragment_noVideosToApprove');
                    } else { ?>
                        <div class="xg_module">
                            <div class="xg_module_body approve">
                                <?php if (XG_SecurityHelper::userIsOwner()) { ?>
                                    <?php
                                    if (XG_App::contentIsModerated()) { ?>
                                        <p><%= xg_html('YOUR_SITE_REQUIRES_YOU', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('privacy', 'edit')) . '"') %></p>
                                    <?php
                                    } else { ?>
                                        <p><%= xg_html('YOUR_SITE_ALLOWS_USERS', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('privacy', 'edit')) . '"') %></p>
                                    <?php
                                    } ?>
                                <?php } ?>
                                <p>
                                      <?php XG_App::ningLoaderRequire('xg.index.bulk'); ?>
                                      <?php $limit = 20 ?>
                                    <a href="#" class="button button-primary" dojoType="BulkActionLink"
                                        title ="<%= xg_html('APPROVE_ALL_VIDEOS') %>"
                                        _verb="<%= xg_html('APPROVE') %>"
                                        _confirmMessage="<%= xg_html('ARE_YOU_SURE_APPROVE_ALL_VIDEOS') %>"
                                        _url="<%= xnhtmlentities($this->_buildUrl('bulk', 'approveAll', array('limit' => $limit, 'xn_out' => 'json'))) %>"
                                        _successUrl="<%= $this->_buildUrl('video', 'listForApproval') %>"
                                        _progressTitle="<%= xg_html('APPROVING') %>"
                                        ><%= xg_html('APPROVE_ALL') %>
                                    </a>
                                    <a href="#" class="button" dojoType="BulkActionLink"
                                        title ="<%= xg_html('DELETE_ALL_VIDEOS') %>"
                                        _verb="<%= xg_html('DELETE') %>"
                                        _confirmMessage="<%= xg_html('ARE_YOU_SURE_DELETE_ALL_VIDEOS') %>"
                                        _url="<%= xnhtmlentities($this->_buildUrl('bulk', 'removeUnapprovedVideos', array('limit' => $limit, 'xn_out' => 'json'))) %>"
                                        _successUrl="<%= $this->_buildUrl('video', 'listForApproval') %>"
                                        _progressTitle="<%= xg_html('DELETING') %>"
                                        ><%= xg_html('DELETE_ALL') %>
                                    </a>
                                </p>
                                <?php
                                foreach ($this->videos as $video) {
                                    $this->renderPartial('fragment_approvalListPlayer', array('video' => $video, 'currentUrl' => Video_HttpHelper::currentUrl()));
                                } ?>
                                <?php
                                $this->renderPartial('fragment_pagination', Video_HtmlHelper::pagination($this->query->getTotalCount(), $this->pageSize)); ?>
                            </div>
                        </div>
                    <?php
                    } ?>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.video.index._shared', 'xg.video.video.listForApproval'); ?>
<?php xg_footer(); ?>
