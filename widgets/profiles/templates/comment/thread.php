<?php xg_header('profile', xg_html('COMMENT_THREAD'), null) ?>
<?php XG_App::ningLoaderRequire('xg.profiles.embed.chatterwall','xg.shared.SimpleToolbar'); ?>

    <div id="xg_body">
        <div class="xg_colgroup">

            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <%=xg_headline(xg_text('COMMENT_THREAD'))%>
                    <div class="easyclear">
                        <ul class="backlink navigation">
                            <li><a href="<%= User::quickProfileUrl($this->_user->screenName) %>"><%= xg_html('BACK_TO_MY_PAGE') %></a></li>
                        </ul>
                    </div>
                    <div class="xg_module">
                        <?php $this->renderPartial('fragment_chatter_head', 'chatter', array('numComments' => $this->commentInfo['numComments'])) ?>
                        <div class="xg_module_body">
                            <?php
                                $addArgs = array('profile' => $this->otherProfile);
                                if ($this->page > 1) {
                                    $addArgs['showCommentUrl'] = $this->_buildUrl('comment', 'thread', $this->paginationTargetParams);
                                }
                                $this->renderPartial('fragment_chatter_add', 'chatter', $addArgs)
                            ?>
                        </div>
                        <div class="xg_module_body">
                            <?php $this->renderPartial('fragment_chatter_list', 'chatter',array('chatters' => $this->commentInfo['comments'],
                                    'responder' => $this->_user->screenName, 'isMyPage' => TRUE, 'showingThread' => TRUE,
                                    'friendStatus' => $this->friendStatus)); ?>
                            <?php $this->renderPartial('fragment_pagination', 'blog',
                                    array('targetUrl' => $this->_buildUrl('comment','thread',
                                                      $this->paginationTargetParams),
                                              'pageParamName' => 'page',
                                              'curPage' => $this->page,
                                              'numPages' => $this->numPages)); ?>
                        </div>
                    </div>
                </div>

                <div class="xg_1col">
                    <div class="xg_1col first-child">
                        <?php xg_sidebar($this) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php xg_footer() ?>
