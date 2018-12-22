<?php
/**
 * A form for submitting discussion comments.
 *
 * @param $heading string  Heading text for the form
 * @param $topic W_Content|XN_Content  The Topic.
 * @param $parentComment W_Content|XN_Content  The parent Comment, if any; otherwise, NULL.
 * @param $open boolean  Whether the form starts open
 * @param $firstPage boolean  Whether this form is on the first page
 * @param $lastPage boolean  Whether this form is on the last page
 * @param $buttonText string  Text for the submit button
 * @param $autoClose boolean  Whether to close the form after submitting a comment.
 */ ?>
<dd>
     <?php
    if (XG_JoinPromptHelper::promptToJoinOnSave($goesToAnotherPage) && $goesToAnotherPage) { ?>
        <p class="toggle">
            <a <%= XG_JoinPromptHelper::promptToJoin(XG_HttpHelper::currentUrl()) %>><span><!--[if IE]>&#9658;<![endif]--><![if !IE]>&#9654;<![endif]></span> <%= xnhtmlentities($heading) %></a>
        </p>
    <?php
    } else {
        $open = XG_JoinPromptHelper::promptToJoinOnSave() ? false : $open;
        $forceNormalFormSubmission = 'refreshPage' == Forum_CommentHelper::positionOfNewComment($parentComment, $firstPage, $lastPage, Forum_CommentHelper::newestPostsFirst());
        // The form's ID is used in NewCommentForm.js  [Jon Aquino 2007-01-24]
        $n = mt_rand();
        XG_App::ningLoaderRequire('xg.forum.topic.NewCommentForm'); ?>
        <form <%= $open ? 'dojoType="NewCommentForm"' : '' %> id="comment_form_<%= $n %>" method="post" enctype="multipart/form-data"
                action="<%= xnhtmlentities($this->_buildUrl('comment', 'create', array('topicId' => $topic->id, 'parentCommentId' => $parentComment->id))) %>"
                _maxlength="<%= Comment::MAX_COMMENT_LENGTH %>"
                _emptyDescriptionErrorMessage="<%= xg_html('PLEASE_WRITE_SOMETHING_FOR_REPLY') %>"
                _forceNormalFormSubmission="<%= $forceNormalFormSubmission ? 'true' : 'false' %>"
                _firstPage="<%= $firstPage ? 'true' : 'false' %>"
                _lastPage="<%= $lastPage ? 'true' : 'false' %>"
                _open="<%= $open ? 'true' : 'false' %>"
                _autoClose="<%= $autoClose ? 'true' : 'false' %>"
                _joinPromptText="<%= xnhtmlentities(XG_JoinPromptHelper::promptToJoinOnSave()) %>">
            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
            <p class="toggle">
                <a href="#" class="comment_form_toggle toggle"><span><%= $open ? '&#9660;' : '<!--[if IE]>&#9658;<![endif]--><![if !IE]>&#9654;<![endif]>' %></span> <%= xnhtmlentities($heading) %></a>
            </p>
            <div class="form_body" <%= $open ? 'style="display: block"' : 'style="display: none"' %>>
                <div class="texteditor clear">
                    <textarea id="textarea_<%= $n %>" name="description" rows="8" cols="60"></textarea>
                </div>
                <p style="visibility: hidden"><a href="#" class="upload_link"><%= xg_html('WOULD_YOU_LIKE_TO_UPLOAD_FILES') %></a></p>
                <div style="display: none">
                    <p><%= xg_html('UPLOAD_FILES') %></p>
                    <ul class="options nobullets">
                        <li><input type="file" class="file" name="file1" /></li>
                        <li><input type="file" class="file" name="file2" /></li>
                        <li><input type="file" class="file" name="file3" /></li>
                    </ul>
                </div>
                <p class="buttongroup">
                    <input type="submit" class="button" value="<%= xnhtmlentities($buttonText) %>">
                </p>
            </div>
        </form>
    <?php
    } ?>
</dd>