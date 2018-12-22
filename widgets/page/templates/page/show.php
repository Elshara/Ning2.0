<?php
// This page is designed to function acceptably with Javascript turned off. [Jon Aquino 2007-01-24]
xg_header(W_Cache::current('W_Widget')->dir, $this->page->title, null, array('metaDescription' => $this->metaDescription, 'metaKeywords' => $this->metaKeywords)); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared') %>
			<%= xg_headline($this->page->title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_head notitle"></div>
                        <div class="xg_module_body pad">
                            <div class="wpage">
                                <div class="description"><%= xg_resize_embeds($this->page->description, 712) %></div>
                                <?php
                                if (count($this->tags)) { ?>
                                    <p><small><%= xg_html('TAGGED_X', Page_HtmlHelper::tagLinks($this->tags)) %></small></p>
                                <?php
                                }
                                $this->renderPartial('fragment_attachments', 'page', array('attachedTo' => $this->page));
                                if (Page_SecurityHelper::currentUserCanEditPage($this->page) || Page_SecurityHelper::currentUserCanDeletePage($this->page)) { ?>
                                    <p class="actionpadding">
                                    <?php
                                    if (Page_SecurityHelper::currentUserCanEditPage($this->page)) { ?>
                                        <a href="<%= xnhtmlentities($this->_buildUrl('page', 'edit', array('id' => $this->page->id))) %>" class="desc edit"><%= xg_html('EDIT') %></a> &nbsp;
                                    <?php
                                    }
                                    if (Page_SecurityHelper::currentUserCanDeletePage($this->page)) {
                                        XG_App::ningLoaderRequire('xg.index.bulk'); ?>
                                        <a style="display: none" class="desc delete" href="#"
                                            dojoType="BulkActionLink"
                                            title="<%= xg_html('DELETE_THIS_PAGE_Q') %>"
                                            _confirmMessage="<%= xg_html('ARE_YOU_SURE_DELETE_THIS_PAGE') %>"
                                            _url="<%= xnhtmlentities($this->_buildUrl('bulk', 'remove', array('limit' => 20, 'id' => $this->page->id, 'xn_out' => 'json'))) %>"
                                            _successUrl="<%= $this->_buildUrl('index', 'index') %>"
                                            _verb="<%= xg_html('DELETE') %>"
                                            _progressTitle="<%= xg_html('DELETING') %>"
                                            ><%= xg_html('DELETE_PAGE') %></a>
                                        </a>
                                    <?php
                                    } ?>
                                    </p>
                                <?php
                                } ?>
                            </div>
                            <%= XG_PaginationHelper::outputPagination($this->totalCount, $this->pageSize, false, $this->pageUrl); %>
                        </div>
                    </div>
                <?php if (Page_SecurityHelper::usersCanComment()) { ?>
                    <?php
                    $commentData = array();
                    foreach ($this->comments as $comment) {
                        $commentData[] = array(
                            'comment' => $comment,
                            'canDelete' => Page_SecurityHelper::currentUserCanDeleteComment($comment),
                            'deleteEndpoint' => $this->_buildUrl('comment','delete', array('xn_out' => 'json')),
                            'canApprove' => false,
                            'approveEndpoint' => null);
                    }
                    $newestCommentsFirst = W_Cache::current('W_Widget')->config['newestCommentsFirst'] == 1 ? true : false;
                    XG_CommentHelper::outputComments(array(
                            'commentData' => $commentData,
                            'numComments' => $this->totalCount,
                            'pageSize' => $this->pageSize,
                            'attachedTo' => $this->page,
                            'currentUserCanSeeAddCommentSection' => Page_SecurityHelper::currentUserCanCreateComment($this->page),
                            'commentsClosedText' => false,
                            'createCommentEndpoint' => $this->_buildUrl('comment','create', array('page' => $this->page->id, 'json'=>'yes')),
                            'showFollowLink' => false,
                            'feedUrl' => null,
                            'feedTitle' => null,
                            'feedFormat' => null,
                            'newestCommentsFirst' => $newestCommentsFirst));?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.page.page.show'); ?>
<?php xg_footer(); ?>
