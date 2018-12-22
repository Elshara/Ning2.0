<?php
/**
 * A post or reply in a discussion.
 *
 * @param $topic XN_Content|W_Content  The Topic object (the discussion)
 * @param $comment XN_Content|W_Content  The Comment object (the post or reply)
 * @param $highlight boolean  Whether to apply a visual highlight
 * @param $firstPage boolean  Whether this comment is on the first page
 * @param $lastPage boolean  Whether this comment is on the last page
 * @param $hasChildComments boolean  Whether this comment has child comments;
 *         used only for comments that haven't been marked as deleted
 * @param $threaded boolean Whether comments are threaded or flat
 */
$contributor = XG_Cache::profiles($comment->contributorName);
$contributorLink = xg_userlink(XG_Cache::profiles($comment->contributorName), 'class="fn url"', true, $this->_buildUrl('topic', 'listForContributor', array('user' => $comment->contributorName)));
$nameAttribute = str_replace(':', '', $comment->id);
$ancestorCommentCount = $threaded ? Forum_CommentHelper::getAncestorCommentCount($comment) : 0; ?>
<dl class="discussion clear i<%= $ancestorCommentCount %> xg_lightborder">
    <dt class="byline">
        <a name="<%= xnhtmlentities($comment->id) %>"></a> <?php /* Deprecated permalink (with ":") */ ?>
        <?php
        echo xg_avatar($contributor, 48, 'photo left');
        // Remove ":" from fragment, for IE [Jon Aquino 2007-03-28]
        $linkAttributes = 'name="' . xnhtmlentities($nameAttribute) . '" href="' . Forum_CommentHelper::url($comment) . '" title="' . xg_html('PERMALINK_TO_REPLY') . '" class="icon link"';
        $time = xg_elapsed_time($comment->createdDate, $showingMonth);
        if ($showingMonth) {
            echo xg_html('REPLY_BY_USER_DATE_TIME', $linkAttributes, $contributorLink, 'class="timestamp"',
                    xnhtmlentities(xg_date(xg_text('F_J_Y'), $comment->createdDate)), xnhtmlentities(xg_date(xg_text('G_IA'), $comment->createdDate)));
        } else {
            echo xg_html('REPLY_BY_USER_WHEN', $linkAttributes, $contributorLink, 'class="timestamp"', xnhtmlentities($time));
        } ?>
    </dt>
    <?php
    $links = array();
    if ($this->_user->screenName != $comment->contributorName) {
        $links[0] = xg_send_message_link($comment->contributorName, null, xg_text('SEND_MESSAGE'));
    }
    if (Forum_SecurityHelper::currentUserCanDeleteComment($comment)) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        ob_start(); ?>
        <a class="icon delete" href="#" dojoType="DeleteCommentLink"
                _commentId="<%= xnhtmlentities($comment->id) %>"
                _deleteCommentUrl="<%= xnhtmlentities($this->_buildUrl('comment', 'delete', array('id' => $comment->id, 'xn_out' => 'json', 'firstPage' => $firstPage ? 1 : 0, 'lastPage' => $lastPage ? 1 : 0))) %>"
                _deleteCommentAndSubCommentsUrl="<%= xnhtmlentities($this->_buildUrl('bulk', 'removeCommentAndSubComments', array('limit' => 20, 'id' => $comment->id, 'xn_out' => 'json'))) %>"
                _currentUserCanDeleteCommentAndSubComments="<%= Forum_SecurityHelper::currentUserCanDeleteCommentAndSubComments($comment) ? 'true' : 'false' %>"
                _hasChildComments="<%= $hasChildComments ? 'true' : 'false' %>"
                _joinPromptText="<%= xnhtmlentities(XG_JoinPromptHelper::promptToJoinOnDelete()) %>">
                <%= xg_html('DELETE') %></a>
        <?php
        $links[1] = ob_get_contents();
        ob_end_clean(); ?>
    <?php
    }
    // BAZ-6215: broke up action links to remove white space from empty dd.actions if only the absolutely positioned delete link
    if ($links[0]) { ?>
        <dd class="actions">
            <?php echo $links[0]; ?>
        </dd>
    <?php }
    if ($links[1]) { ?>
        <dd class="item_delete">
            <?php echo $links[1]; ?>
        </dd>
    <?php } ?>
    <dd>
        <?php
        if (Forum_CommentHelper::isMarkedAsDeleted($comment)) { ?>
            <p><em><%= xg_html('REPLY_WAS_DELETED') %></em></p>
        <?php
        } else {
            // The nl2br and other function calls should be kept in sync with the same set of calls in CommentController#action_update [Jon Aquino 2007-02-27]
            if (Forum_SecurityHelper::currentUserCanEditComment($comment)) { ?>
                <div class="description" id="<%= 'desc_' . $nameAttribute %>" dojoType="InPlaceEditor"
                        _controlAttributes="<%= xnhtmlentities('rows="5" cols="60"') %>"
                        _html="true"
                        _toolbar="true"
                        _maxLength="4000"
                        _setValueUrl="<%= xnhtmlentities($this->_buildUrl('comment', 'update', array('id' => $comment->id))) %>"
                        _value="<%= xnhtmlentities($html = xg_resize_embeds(xg_shorten_linkText($comment->description), 475)) %>"
                        _joinPromptText="<%= xnhtmlentities(XG_JoinPromptHelper::promptToJoinOnSave()) %>">
                        <%= xg_nl2br($html) %></div>
            <?php
            } else { ?>
                <div class="description" id="<%= 'desc_' . $nameAttribute %>"><%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($comment->description), 475)) %></div>
            <?php
            }
            if (Forum_SecurityHelper::currentUserCanEditComment($comment)) { ?>
                <em class="desc edit"><%= xg_html('N_MINUTES', Forum_CommentHelper::getEditMinutes($comment)) %></em> &#160;
            <?php
            }
        } ?>
    </dd>
    <?php
    if (count(Forum_FileHelper::getFileAttachments($comment))) {
        $this->renderPartial('fragment_attachments', 'topic', array('attachedTo' => $comment));
    }
    if ($ancestorCommentCount + 1 < Forum_CommentHelper::MAX_COMMENT_LEVEL && Forum_SecurityHelper::currentUserCanSeeAddCommentLinks($topic) && $threaded) {
        $this->renderPartial('fragment_commentForm', 'topic', array('heading' => xg_text('REPLY_TO_THIS'), 'topic' => $topic, 'parentComment' => $comment, 'open' => FALSE, 'firstPage' => $firstPage, 'lastPage' => $lastPage, 'buttonText' => xg_text('ADD_YOUR_REPLY'), 'autoClose' => true));
    } elseif (Forum_SecurityHelper::currentUserCanSeeAddCommentLinks($topic) && ! $threaded) { ?>
        <dd>
            <?php
            if (XG_JoinPromptHelper::promptToJoinOnSave($goesToAnotherPage) && $goesToAnotherPage) { ?>
                <p class="toggle">
                    <a <%= XG_JoinPromptHelper::promptToJoin(XG_HttpHelper::currentUrl()) %>><span><!--[if IE]>&#9658;<![endif]--><![if !IE]>&#9654;<![endif]></span> <%= xnhtmlentities(xg_text('REPLY_TO_THIS')) %></a>
                </p>
            <?php
            } else {
                XG_App::ningLoaderRequire('xg.forum.topic.QuoteLink'); ?>
                <p class="toggle">
                    <a href="#" dojoType="QuoteLink" _citeUrl="<%= xnhtmlentities(XG_HttpHelper::currentUrl() . '#' . $nameAttribute) %>" _contributor="<%= xnhtmlentities(xg_username($comment->contributorName)) %>" _descId="<%= 'desc_' . $nameAttribute %>"><span><!--[if IE]>&#9658;<![endif]--><![if !IE]>&#9654;<![endif]></span> <%= xnhtmlentities(xg_text('REPLY_TO_THIS')) %></a>
                </p>
            <?php }
            ?>
        </dd>
    <?php
    } ?>
</dl>
