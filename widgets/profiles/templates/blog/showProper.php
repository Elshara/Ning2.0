<?php xg_header($this->tab, $this->pageTitle, null, array('metaDescription' => $this->metaDescription, 'metaKeywords' => $this->metaKeywords)); ?>
<?php xg_autodiscovery_link(Profiles_FeedHelper::feedUrl($this->_buildUrl('blog','feed',array('user' => $this->postContributorName, 'xn_auth' => 'no'))), xg_text('XS_POSTS', ucfirst(xg_username($this->profile))), 'atom') ?>
<?php XG_App::ningLoaderRequire('xg.profiles.blog.show', 'xg.index.actionicons', 'xg.shared.TagLink'); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
		<?php XG_PageHelper::subMenu(Profiles_HtmlHelper::blogSubMenu($this->_widget)) ?>
        <div class="xg_module xg_blog xg_blog_detail xg_blog_mypage">
            <?php
            if ($this->isPreview || $this->post->my->publishStatus == 'draft') { ?>
                <form method="post" action="<%= xnhtmlentities($this->_buildUrl('blog','previewSubmit',$this->formActionSuffix)) %>">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <div class="notification easyclear">
                        <p><%= xg_html('THIS_IS_PREVIEW_OF_BLOG_POST') %></p>
                        <input type="hidden" name="post_action" id="post_action"/>
                        <p class="buttongroup">
                            <button id="post_edit" type="submit" name="post_edit" value="edit" >&#171; <%= xg_html('BACK') %></button>&nbsp;
                            <button id="post_publish" type="submit" name="post_publish" value="publish"><strong><%= xg_html('PUBLISH') %></strong></button>
                        </p>
                    </div>
                    <?php foreach ($this->hiddenVariables as $v) { echo $this->form->hidden($v); } ?>
                </form>
            <?php
            }
            if (isset($_GET['shareInvitesSent']) && $_GET['shareInvitesSent']) { ?>
                <div class="xg_module">
                    <div class="xg_module_body success">
                        <p class="last-child"><%= xg_html($_GET['shareInvitesSent'] > 1 ? 'YOUR_MESSAGES_SENT' : 'YOUR_MESSAGE_SENT') %></p>
                    </div>
                </div>
            <?php
            } ?>
			<?php
			XG_CommentHelper::outputStoppedFollowingNotification(xg_text('NO_LONGER_FOLLOWING_BLOG_POST'));
			$contributor = XG_Cache::profiles($this->postContributorName);
			$date = xg_date(xg_text('F_J_Y'), $this->post->my->publishTime);
			$time = xg_date(xg_text('G_IA'), $this->post->my->publishTime);
			echo xg_headline(BlogPost::getHtmlTitle($this->post), array(
				'avatarUser' => $contributor,
				'byline1Html' => '<a class="nolink">' . xg_html('POSTED_BY_USER_ON_DATE_AT_TIME', '</a>' . xg_userlink($contributor) . '<a class="nolink">', xnhtmlentities($date), xnhtmlentities($time)) . '</a>',
				'byline2Html' => xg_message_and_friend_links($this->postContributorName,
					$this->_buildUrl('blog', 'list', array('user' => $contributor->screenName)),
					$contributor->screenName == $this->_user->screenName ? xg_text('VIEW_BLOG_POSTS') : xg_text('VIEW_XS_BLOG', xg_username($contributor->screenName)))
			));
            ?>
            <div class="xg_module_body">
                <div class="postbody">
                <?php
                $adminOptionListItems = array();
                XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
                ob_start();
                W_Cache::getWidget('main')->dispatch('promotion','link',array($this->post, 'post'));
                $featureLink = trim(ob_get_contents());
                ob_end_clean();
                if ($featureLink) { $adminOptionListItems[] = '<li>' . $featureLink . '</li>'; }
                if (XG_SecurityHelper::userIsContributor($this->_user, $this->post)) {
                    ob_start(); ?>
                    <li><a href="<%= xnhtmlentities($this->_buildUrl('blog','edit',array('id' => $this->post->id)))%>" class="desc edit"><%= xg_html('EDIT_POST') %></a></li>
                    <?php
                    $adminOptionListItems[] = ob_get_contents();
                    ob_end_clean();
                }
                if (XG_SecurityHelper::userIsAdminOrContributor($this->_user, $this->post)) {
                    $addOrEdit = mb_strlen($this->currentUserTagString) ? 'edit' : 'add';
                    ob_start(); ?>
                    <li dojoType="TagLink"
                        _actionUrl="<%= xnhtmlentities($this->_buildUrl('blog', 'tag', array('id' => $this->post->id, 'xn_out' =>'json'))); %>"
                        _tags="<%= xnhtmlentities($this->currentUserTagString); %>">
                        <a class="desc <%= $addOrEdit %>" href="#"><%= $addOrEdit == 'edit' ? xg_text('EDIT_YOUR_TAGS') : xg_text('ADD_TAGS') %></a>
                    </li>
                    <?php
                    $adminOptionListItems[] = ob_get_contents();
                    ob_end_clean();
                }
                if (XG_SecurityHelper::userIsAdminOrContributor($this->_user, $this->post)) {
                    ob_start(); ?>
                    <li>
                        <a class="desc delete" id="deleteBlogPostLink" href="#"
                            _url="<%= xnhtmlentities($this->_buildUrl('blog', 'update', '?id='.$this->post->id)) %>"
                            title ="<%= xg_html('DELETE_THIS_BLOG_POST_Q') %>"
                            _confirmQuestion ="<%= xg_html('ARE_YOU_SURE_DELETE_THIS_BLOG_POST') %>"
                            ><%= xg_html('DELETE_BLOG_POST') %></a>
                    </li>
                    <?php
                    $adminOptionListItems[] = ob_get_contents();
                    ob_end_clean();
                }
                if (XG_SecurityHelper::userIsContributor($this->_user, $this->post)) {
                    ob_start(); ?>
                    <li><a class="desc settings" href="<%= xnhtmlentities($this->_buildUrl('blog','managePosts')) %>"><%= xg_html('MANAGE_BLOG') %></a></li>
                    <?php
                    $adminOptionListItems[] = ob_get_contents();
                    ob_end_clean();
                }
                if ($adminOptionListItems && !$this->isPreview && $this->post->my->publishStatus != 'draft') { ?>
                    <div class="adminbox xg_module xg_span-4 adminbox-right">
                        <div class="xg_module_head">
                            <h2><%= xg_html('ADMIN_OPTIONS') %></h2>
                        </div>
                        <div class="xg_module_body">
                            <ul class="nobullets last-child">
                                <?php foreach ($adminOptionListItems as $li) { echo $li; } ?>
                            </ul>
                        </div>
                    </div>
                <?php
                } ?>
                    <?php
                    $description = BlogPost::upgradeDescriptionFormat($this->post->description, $this->post->my->format);
                    echo xg_nl2br(xg_resize_embeds(xg_shorten_linkText($description), 712)) ?>
                </div>
                <?php
                if ($this->tags) { ?>
                    <p class="small" id="tagsList">
                        <%= Profiles_HtmlHelper::tagHtmlForDetailPage($this->tags); %>
                    </p>
                <?php
                }
                if (Profiles_PrivacyHelper::canCurrentUserSeeShareLinks($this->post) && ! $this->isPreview) {
                    $shareUrl = W_Cache::getWidget('main')->buildUrl('sharing', 'share', array('id' => urlencode($this->post->id))); ?>
                    <p><a class="desc share" href="<%= $shareUrl %>"><%= xg_html('SHARE') %></a></p>
                <?php
                } ?>
                <?php
                if ($this->previousPost || $this->nextPost) { ?>
                    <ul class="pagination smallpagination">
                    <?php
                    if ($this->previousPost) { ?>
                        <li class="left"><a href="<%= xnhtmlentities($this->_buildUrl('blog', 'show', array('id' => $this->previousPost->id))) %>" title="<%= xnhtmlentities(BlogPost::getTextTitle($this->previousPost)) %>"><%= xg_html('PREVIOUS_POST') %></a></li> <?php /* [skip-SyntaxTest] */ ?>
                    <?php
                    }
                    if ($this->nextPost) { ?>
                        <li class="right"><a title="<%= xnhtmlentities(BlogPost::getTextTitle($this->nextPost)) %>" href="<%= xnhtmlentities($this->_buildUrl('blog', 'show', array('id' => $this->nextPost->id))) %>"><%= xg_html('NEXT_POST') %></a></li>
                    <?php
                    } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
        <?php
        if ($this->post->my->publishStatus == 'publish') {
            $commentData = array();
            foreach ($this->commentInfo['comments'] as $comment) {
                $commentData[] = array(
                    'comment' => $comment,
                    'canDelete' => Profiles_CommentHelper::userCanDeleteComment($this->_user, $comment),
                    'deleteEndpoint' => $this->_buildUrl('comment','delete', array('xn_out' => 'json')),
                    'canApprove' => Profiles_CommentHelper::userCanApproveComment($this->_user, $comment),
                    'approveEndpoint' => $this->_buildUrl('comment','approve', array('xn_out' => 'json')));
            }
            XG_CommentHelper::outputComments(array(
                    'commentData' => $commentData,
                    'numComments' => $this->commentInfo['numComments'],
                    'pageSize' => $this->pageSize,
                    'attachedTo' => $this->post,
                    'currentUserCanSeeAddCommentSection' => $this->allowComments,
                    'commentsClosedText' => $this->allowComments ? null : xg_text('COMMENTS_ARE_CLOSED'),
                    'createCommentEndpoint' => $this->_buildUrl('comment','createForBlogPost', array('attachedTo' => $this->post->id)),
                    'showFollowLink' => true,
                    'feedUrl' => XG_CommentHelper::feedAvailable($this->post) ? $this->_buildUrl('comment','feed',array('attachedTo' => $this->post->id, 'xn_auth' => 'no')) : null,
                    'feedTitle' => xg_text('COMMENTS_TITLE', $this->pageTitle),
                    'feedFormat' => 'atom',
                    'newestCommentsFirst' => false)); ?>
            <input type="hidden" id="incrementViewCountEndpoint" value="<%= xnhtmlentities($this->_buildUrl('blog','incrementViewCount', array('id' => $this->post->id, 'xn_out' => 'json'))) %>" />
        <?php
        } ?>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
