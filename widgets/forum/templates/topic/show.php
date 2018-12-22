<?php
// This page is designed to function acceptably with Javascript turned off. [Jon Aquino 2007-01-24]
xg_header(W_Cache::current('W_Widget')->dir, $this->topic->title, null, array('metaDescription' => $this->metaDescription, 'metaKeywords' => $this->metaKeywords));
$pagination = XG_PaginationHelper::computePagination($this->totalCount, $this->pageSize);
$firstPage = $pagination['curPage'] == 1;
$lastPage = $pagination['curPage'] == $pagination['numPages'] || 0 == $pagination['numPages'];
$setCategoryUrl = $this->_buildUrl('topic', 'setCategory', array('id' => $this->topic->id)); ?>
<?php
// is xg.index.actionicons needed here? [ywh 2008-06-25]
XG_App::ningLoaderRequire('xg.shared.InPlaceEditor', 'xg.index.actionicons', 'xg.index.bulk', 'xg.shared.PostLink', 'xg.forum.topic.DeleteCommentLink', 'xg.shared.TagLink');
XG_App::ningLoaderRequire('xg.forum.topic.show');
?>
<div id="xg_body" class="xg_forum">
    <div class="xg_column xg_span-16">
        <%= $this->renderPartial('fragment_navigation', '_shared', array('categoryId' => $this->category ? $this->category->id : null)) %>
        <div id="discussion_closed_module" class="xg_module" <%= $this->showDiscussionClosedModule ? '' : 'style="display:none"'; %>>
            <div class="xg_module_body errordesc">
                <p class="last-child"><%= xg_html('SORRY_DISCUSSION_CLOSED') %></p>
            </div>
        </div>
        <?php
        if ($_GET['shareInvitesSent']) { ?>
            <div class="xg_module">
                <div class="xg_module_body success">
                    <p class="last-child"><%= xg_html($_GET['shareInvitesSent'] > 1 ? 'YOUR_MESSAGES_SENT' : 'YOUR_MESSAGE_SENT') %></p>
                </div>
            </div>
        <?php
        }
        if ($this->unFollow) { ?>
            <div class="xg_module">
                <div class="xg_module_body success">
                    <p class="last-child"><%= xg_html('YOU_ARE_NO_LONGER_FOLLOWING_THIS_X', mb_strtolower(xg_text('DISCUSSION'))) %></p>
                </div>
            </div>
        <?php
        } ?>
        <div class="xg_module">
			<?php
				$contributor = XG_Cache::profiles($this->topic->contributorName);
				$date = xg_date(xg_text('F_J_Y'), $this->topic->createdDate);
                $time = xg_date(xg_text('G_IA'), $this->topic->createdDate);
				if (XG_GroupHelper::inGroupContext()) {
					$line1 = xg_html('POSTED_BY_USER_ON_DATE_AT_TIME_IN_GROUP', xg_userlink($contributor), xnhtmlentities($date), xnhtmlentities($time), 'href="' . xnhtmlentities(XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => XG_GroupHelper::currentGroupId()))) . '"', xnhtmlentities(XG_GroupHelper::currentGroup()->title));
				} elseif (Forum_SecurityHelper::currentUserCanManageForum() && $this->categoryPickerOptionsJson) {
					XG_App::ningLoaderRequire('xg.forum.topic.CategoryPicker');
					$line1 = xg_html('POSTED_BY_USER_ON_DATE_AT_TIME_IN_CATEGORY_CHANGE', xg_userlink($contributor), xnhtmlentities($date), xnhtmlentities($time), 'href="' . xnhtmlentities($this->categoryUrl) . '"', xnhtmlentities($this->category->title), 'href="#" dojoType="CategoryPicker" _setValueUrl="' . xnhtmlentities($setCategoryUrl) . '" _options="' . xnhtmlentities($this->categoryPickerOptionsJson) . '" _currentCategoryId="' . xnhtmlentities($this->category->id) . '"');
				} elseif ($this->category) {
					$line1 = xg_html('POSTED_BY_USER_ON_DATE_AT_TIME_IN_CATEGORY', xg_userlink($contributor), xnhtmlentities($date), xnhtmlentities($time), 'href="' . xnhtmlentities($this->categoryUrl) . '"', xnhtmlentities($this->category->title));
				} else {
					$line1 = xg_html('POSTED_BY_USER_ON_DATE_AT_TIME', xg_userlink($contributor), xnhtmlentities($date), xnhtmlentities($time));
				}

				if (XG_GroupHelper::inGroupContext()) {
					$line2 = xg_html("BACK_TO_X_DISCUSSIONS", 'href="' . XG_GroupHelper::buildUrl('forum','index','index') . '" class="desc back"' , xnhtmlentities(XG_GroupHelper::currentGroup()->title));
				} else {
					$line2 = xg_message_and_friend_links($this->topic->contributorName, $this->_buildUrl('topic', 'listForContributor', array('user' => $contributor->screenName)), xg_text('VIEW_DISCUSSIONS'));
				}
				echo xg_headline($this->topic->title, array(
					'avatarUser' => $contributor,
					'byline1Html' => $line1,
					'byline2Html' => $line2,
				));
			?>
            <div class="xg_module_body">
                <div class="discussion">
                    <div class="description">
                        <%= $this->renderPartial('fragment_adminBox', 'topic') %>
                        <%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($this->topic->description), 712)) %>
                    </div>
                </div>
                <?php
                if ($this->tags) { ?>
                    <p class="small" id="tagsList">
                        <%= Forum_HtmlHelper::tagHtmlForDetailPage($this->tags); %>
                    </p>
                <?php
                }
                if (Forum_SecurityHelper::currentUserCanSeeShareLinks($this->topic)) {
                    $shareUrl = W_Cache::getWidget('main')->buildUrl('sharing', 'share', array('id' => urlencode($this->topic->id))); ?>
                    <p><a class="desc share" href="<%= $shareUrl %>"><%= xg_html('SHARE') %></a></p>
                <?php
                } ?>
                <dl class="discussion noindent">
                    <?php
                    if (count(Forum_FileHelper::getFileAttachments($this->topic))) {
                        $this->renderPartial('fragment_attachments', 'topic', array('attachedTo' => $this->topic));
                    }
                    if (Forum_SecurityHelper::currentUserCanSeeAddCommentLinks($this->topic)) {
                        $this->renderPartial('fragment_commentForm', 'topic', array('heading' => xg_text('REPLY_TO_THIS'), 'topic' => $this->topic, 'open' => TRUE, 'firstPage' => $firstPage, 'lastPage' => $lastPage, 'buttonText' => xg_text('ADD_YOUR_REPLY')));
                    } ?>
                </dl>
            </div>
            <?php
            if (! $this->comments && Forum_SecurityHelper::currentUserCanSeeAddCommentLinks($this->topic)) { ?>
                <div id="upper_follow_link_container" class="xg_module_foot">
                    <%= xg_follow_unfollow_links($this->topic) %>
                </div>
            <?php
            } ?>
        </div>
        <div class="xg_module">
            <div id="discussionReplies" <?php if (!count($this->comments)) { echo 'style="display:none"';} ?>>
                <?php
                if ($this->topic->my->commentsClosed == 'Y') { ?>
                    <div class="xg_module_body">
                        <p><big><strong><%= xg_html('REPLIES_CLOSED_FOR_DISCUSSION') %></strong></big></p>
                    </div>
                <?php
                } ?>
                <div class="xg_module_body">
                    <h3 id="comments"><%= xg_html('REPLIES_TO_THIS_DISCUSSION') %></h3>
                    <?php
                    if (count($this->comments)) { ?>
                        <?php
                        foreach ($this->comments as $comment) {
                            $this->renderPartial('fragment_comment', 'topic', array('topic' => $this->topic, 'comment' => $comment, 'highlight' => $comment->id == $this->currentCommentId, 'firstPage' => $firstPage, 'lastPage' => $lastPage, 'hasChildComments' => $this->threadingModel == 'flat' ? '' : $this->commentIdsWithChildComments[$comment->id], 'threaded' => $this->threadingModel == 'threaded'));
                        }
                        if ($this->threadingModel != 'threaded') {
							echo '<dl class="last-reply">';
                            $this->renderPartial('fragment_commentForm', 'topic', array('heading' => xg_text('REPLY_TO_THIS'), 'topic' => $this->topic, 'parentComment' => null, 'open' => FALSE, 'firstPage' => $firstPage, 'lastPage' => $lastPage, 'buttonText' => xg_text('ADD_YOUR_REPLY'), 'autoClose' => true));
							echo '</dl>';
                        }
                    } ?>
                    <%= XG_PaginationHelper::outputPagination($this->totalCount, $this->pageSize, '', null, null, false, '#comments'); %>
                </div>
                <div class="xg_module_foot">
                    <?php
                    if (! XG_App::appIsPrivate() && ! XG_GroupHelper::groupIsPrivate()) {
                        $title = XG_GroupHelper::inGroupContext() ? xnhtmlentities(XG_GroupHelper::currentGroup()->title) : xg_html('DISCUSSION_FORUM');
                        xg_autodiscovery_link($this->feedUrl, xnhtmlentities($this->topic->title), 'atom');
                        xg_autodiscovery_link($this->forumFeedUrl, $title, 'atom')?>
                        <p class="left">
                            <a class="desc rss" href="<%= xnhtmlentities($this->feedUrl) %>"><%= xg_html('RSS') %></a>
                        </p>
                    <?php
                    } ?>
                    <%= xg_follow_unfollow_links($this->topic) %>
                </div>
            </div>
        </div>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
