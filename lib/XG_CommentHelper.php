<?php

/**
 * Useful functions for displaying comments.
 *
 * To add comments to a page, start by calling XG_CommentHelper::outputStandardComments(array('attachedTo' => $object))
 * where you want the comments to appear. You will see some error messages.
 * Once you have fixed all of the error messages, the installation of the comments component
 * will be complete.
 */
class XG_CommentHelper {

    /**
     * Displays comments, assuming that server-side handling is done by an XG_CommentController.
     * New comment implementations should use this function instead of outputComments().
     *
     * Remember to call XG_CommentHelper::stopFollowingIfRequested($attachedTo) in the action function,
     * and XG_CommentHelper::outputStoppedFollowingNotification() at the top of the page
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @param $canDeleteCallback callback  (optional)  a function that, given a comment, returns whether the user can delete it
     * @param $currentUserCanSeeAddCommentSection boolean  (optional) whether to display the Add Comments section,
     *         containing a form or message
     * @param $currentUserCanAddComment  (optional) whether the current user is allowed to add a comment,
     *         or null to check that the user is a member of the network and of the current group (if any)
     * @param $htmlIfCannotAddComment  (optional) HTML for the message to display in the Add Comment section
     *         if the current user is not allowed to add a comment; defaults to a link to the sign-up page
     * @param $commentController string  (optional)  the name of the comment controller; defaults to 'comment'
     * @param $pageSize integer  (optional) the number of Comments per page; defaults to 10
     * @param $commentsClosedText string  (optional) message to display if commenting is closed
     * @param $newestCommentsFirst boolean  (optional) whether newer comments appear before older comments; defaults to false
     * @param $pageParamName string  (optional) the name of the url parameter for the page number; defaults to 'page'
     * @param $showFeedLink boolean  (optional) whether to display the RSS link; defaults to true
	 * @param $addCommentsHeader  boolean (optional) Whether to show comments header (.xg_module_head h2)
     */
    public static function outputStandardComments($args) {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        foreach ($args as $key => $value) { ${$key} = $value; }
        if (! $attachedTo) { throw new Exception('attachedTo not specified (47474803)'); }
        if (! $pageParamName) { $pageParamName = 'page'; }
        if (! $commentController) { $commentController = 'comment'; }
        if (! $pageSize) { $pageSize = self::DEFAULT_PAGE_SIZE; }
        if (! $canDeleteCallback) { $canDeleteCallback = array('XG_CommentHelper', 'currentUserCanDeleteComment'); }
        if (is_null($currentUserCanSeeAddCommentSection)) { $currentUserCanSeeAddCommentSection = self::currentUserCanSeeAddCommentSection($attachedTo); }
        if (is_null($showFeedLink)) { $showFeedLink = true; }
        $start = XG_PaginationHelper::computeStart($_GET[$pageParamName], $pageSize);
        $comments = Comment::getCommentsFor($attachedTo->id, $start, $start + $pageSize, 'Y', 'createdDate', $newestCommentsFirst ? 'desc' : 'asc');
        XG_Cache::profiles($comments['comments']);
        $commentData = array();
        foreach ($comments['comments'] as $comment) {
            $commentData[] = array(
                'comment' => $comment,
                'canDelete' => $canDeleteCallback ? call_user_func($canDeleteCallback, $comment) : false,
                'deleteEndpoint' => W_Cache::current('W_Widget')->buildUrl($commentController, 'delete', array('xn_out' => 'json')),
                'canApprove' => false,
                'approveEndpoint' => null);
        }
        self::outputComments(array(
                'commentData' => $commentData,
                'numComments' => $comments['numComments'],
                'pageSize' => $pageSize,
                'attachedTo' => $attachedTo,
                'currentUserCanSeeAddCommentSection' => $currentUserCanSeeAddCommentSection,
                'currentUserCanAddComment' => $currentUserCanAddComment,
                'htmlIfCannotAddComment' => $htmlIfCannotAddComment,
                'commentsClosedText' => $commentsClosedText,
                'createCommentEndpoint' => W_Cache::current('W_Widget')->buildUrl($commentController, 'create', array('attachedTo' => $attachedTo->id, 'pageSize' => $pageSize, 'pageParamName' => $pageParamName)),
                'showFollowLink' => true,
                'feedUrl' => ($showFeedLink && self::feedAvailable($attachedTo)) ? W_Cache::current('W_Widget')->buildUrl($commentController, 'feed', array('attachedTo' => $attachedTo->id, 'xn_auth' => 'no')) : null,
                'feedTitle' => xg_text('COMMENTS_TITLE', $attachedTo->title),
                'feedFormat' => 'atom',
                'newestCommentsFirst' => $newestCommentsFirst,
                'pageParamName' => $pageParamName,
				'addCommentsHeader' => $addCommentsHeader,
		));
    }

    /** Default number of comments per page. */
    const DEFAULT_PAGE_SIZE = 10;

    /**
     * Stops following the comments on the current page, if the user has indicated
     * in the query string that she wishes to do so. Call this in the action method,
     * before any output.
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     */
    public static function stopFollowingIfRequested($attachedTo) {
        if ($_GET['unfollow']) {
            XG_SecurityHelper::redirectIfNotMember();
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
            Index_NotificationHelper::stopFollowing($attachedTo);
        }
    }

    /**
     * If appropriate, displays a message saying that you have stopped following the
     * comments on the current page. Call this near the top of the page.
     *
     * @param $message string  plain-text message to display
     */
    public static function outputStoppedFollowingNotification($message) {
        self::$outputStoppedFollowingNotificationCalled = true;
        if ($_GET['unfollow']) { ?>
            <div class="xg_module">
                <div class="xg_module_body success">
                    <p class="last-child"><%= xnhtmlentities($message) %></p>
                </div>
            </div>
        <?php
        }
    }

    /** Whether outputStoppedFollowingNotification() has been called. */
    private static $outputStoppedFollowingNotificationCalled = false;

    /**
     * Displays the comments.
     *
     * @param $commentData array  array of arrays, each with:
     *         - comment - the Comment object
     *         - canDelete - whether the current user is allowed to delete the comment
     *         - deleteEndpoint - URL of the action for deleting a comment. The comment ID will be passed
     *                 as a POST variable named "id". Should return a JSON object with success: true
     *         - canApprove - whether the current user is allowed to approve the comment
     *         - approveEndpoint - URL of the action for approving a comment. The comment ID will be passed
     *                 as a POST variable named "id". Should return a JSON object with success: true
     * @param $numComments integer  the total number of Comments
     * @param $pageSize integer  the number of Comments per page
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @param $commentsClosedText string  message to display if commenting is closed
     * @param $currentUserCanSeeAddCommentSection boolean  whether to display the Add Comments section,
     *         containing a form or message
     * @param $currentUserCanAddComment  whether the current user is allowed to add a comment,
     *         or null to check that the user is a member of the network and of the current group (if any)
     * @param $htmlIfCannotAddComment  HTML for the message to display in the Add Comment section
     *         if the current user is not allowed to add a comment; defaults to a link to the sign-up page
     * @param $showFollowLink boolean  whether to show the Follow / Stop Following link.
     *         If true, be sure to call stopFollowingIfRequested() and outputStoppedFollowingNotification(),
     * @param $feedUrl string  URL of the comment feed, or null if no such feed exists.
     *         Ignored for private networks and private groups.
     * @param $feedTitle string  title for the comment feed
     * @param $feedFormat string  "atom" or "rss"
     * @param $newestCommentsFirst boolean  whether newer comments appear before older comments
     * @param $pageParamName string  The name of the url parameter for the page number; defaults to 'page'
     * @param $createCommentEndpoint string  URL of the action to post new comments to.
     *         If JavaScript is enabled, xn_out=json will be appended to this URL, which should return
     *         JSON containing:
     *
     *             - html - HTML for the comment
     *             - approved - whether the comment is approved
     *             - userIsNowFollowing - whether submitting the comment caused the user to start following comments
     *
     *         The URL should also handle a regular, non-Ajax post (no xn_out specified),
     *         which happens if JavaScript is not enabled. It should redirect to the page with
     *         the new comment - this URL will be in a GET variable named "target".
	 * @param $addCommentsHeader  boolean (optional) Whether to show comments header (.xg_module_head h2)
     */
    public static function outputComments($args) {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        XG_App::ningLoaderRequire('xg.shared.comment', 'xg.shared.SimpleToolbar');
        foreach ($args as $key => $value) { ${$key} = $value; }
        $commentsBelowForm = $formAboveComments = $newestCommentsFirst;
        $commentsAboveForm = $formBelowComments = ! $newestCommentsFirst;
        if (mb_strpos($createCommentEndpoint, 'xn_out') !== false) { throw new Exception('Assertion failed (70923159)'); }
        if (! XG_SecurityHelper::currentUserCanFollowComments()) { $showFollowLink = false; }
        if ($showFollowLink && ! self::$outputStoppedFollowingNotificationCalled) { throw new Exception('If you set $showFollowLink to true, you must call stopFollowingIfRequested() and outputStoppedFollowingNotification()'); } ?>
        <div class="xg_module">
			<?php if ($addCommentsHeader) {?>
				<div class="xg_module_head"><h2 id="comments" numComments="<%= $numComments %>"><%=$numComments ? xg_html('COMMENT_WALL_N_COMMENTS', $numComments) : xg_html('COMMENT_WALL')%></h2></div>
			<?php }?>
            <?php
            if ($commentsClosedText) { ?>
                <div class="xg_module_body pad">
                    <p class="last-child"><big><strong><%= xnhtmlentities($commentsClosedText) %></strong></big></p>
                </div>
            <?php
            }
            if ($commentsAboveForm) { self::outputCommentsProper($args, ! $currentUserCanSeeAddCommentSection); }
            if ($currentUserCanSeeAddCommentSection) { ?>
                <div class="xg_module_body">
                    <p id="add-comment"><%= xg_html('ADD_A_COMMENT') %></p>
                    <?php
                    if (! isset($currentUserCanAddComment)) { $currentUserCanAddComment = XG_GroupHelper::inGroupContext() ? Group::userIsMember(XG_GroupHelper::currentGroup(), XN_Profile::current()->screenName) : User::isMember(XN_Profile::current()); }
                    if ($currentUserCanAddComment) {
                        $paginationAfterCommentAdded = XG_PaginationHelper::computePagination(1 + $numComments, $pageSize, null, $pageParamName);
                        if ($newestCommentsFirst) {
                            $commentUrl = XG_HttpHelper::addParameter($paginationAfterCommentAdded['targetUrl'], $paginationAfterCommentAdded['pageParamName'], 1) . '#first_comment';
                            $ajax = $paginationAfterCommentAdded['curPage'] == 1;
                        } else {
                            $commentUrl = XG_HttpHelper::addParameter($paginationAfterCommentAdded['targetUrl'], $paginationAfterCommentAdded['pageParamName'], $paginationAfterCommentAdded['numPages']) . '#last_comment';
                            $ajax =  $paginationAfterCommentAdded['curPage'] == $paginationAfterCommentAdded['numPages'];
                        }
                        XG_App::ningLoaderRequire('xg.shared.CommentForm'); ?>
                        <dl id="comment_form_notify" style="display: none"></dl>
                        <form _ajax="<%= $ajax ? 'true' : 'false' %>" _addAtTop="<%= $newestCommentsFirst ? 'true' : 'false' %>" id="comment_form" action="<%= xnhtmlentities(XG_HttpHelper::addParameter($createCommentEndpoint, 'target', $commentUrl)) %>" method="post">
                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                            <fieldset class="nolegend">
                                <dl class="vcard comment xg_lightborder">
                                    <dt><img class="photo" src="<%= XG_UserHelper::getThumbnailUrl(XN_Profile::current(), 48, 48) %>" height="48" width="48" alt=""/></dt>
                                    <dd class="easyclear">
                                        <div class="texteditor">
                                            <textarea name="comment" rows="4" cols="60" dojoType="SimpleToolbar"></textarea>
                                        </div>
                                        <p class="buttongroup">
                                            <input type="submit" class="button" value="<%= xg_html('ADD_COMMENT') %>" />
                                        </p>
                                    </dd>
                                </dl>
                            </fieldset>
                        </form>
                    <?php
                    } elseif (XN_Profile::current()->isLoggedIn() && User::isPending(XN_Profile::current())) { ?>
                        <div class="comment-join">
                            <h3><%= xg_html('YOU_NEED_TO_BE_MEMBER', xnhtmlentities(XN_Application::load()->name)) %></h3>
                            <p><%= xg_html('YOUR_MEMBERSHIP_TO_X_IS_PENDING_APPROVAL', xnhtmlentities(XN_Application::load()->name)) %></p>
                        </div>
                    <?php
                    } elseif (XG_GroupHelper::inGroupContext()) {
                        $group = XG_GroupHelper::currentGroup();
                        ?>
                        <div class="comment-join">
                            <h3><%= xg_html('YOU_NEED_TO_BE_MEMBER', xnhtmlentities($group->title)) %></h3>
                            <?php if (Groups_SecurityHelper::currentUserCanSeeJoinLinks($group)) {
                                XG_App::ningLoaderRequire('xg.shared.PostLink'); ?>
                                <p><a dojoType="PostLink" href="#" _url="<%= xnhtmlentities(XG_GroupHelper::buildUrl('groups','group','join', array('id' => $group->id))) %>"><%= xg_html('JOIN_GROUP', xnhtmlentities($group->title)) %></a></p>
                            <?php
                            } ?>
                        </div>
                    <?php
                    } elseif (! $htmlIfCannotAddComment) { ?>
                        <div class="comment-join">
                            <h3><%= xg_html('YOU_NEED_TO_BE_MEMBER', xnhtmlentities(XN_Application::load()->name)) %></h3>
                            <p><%= xg_html('SIGN_UP_OR_SIGN_IN', 'href="' . xnhtmlentities(XG_HttpHelper::signUpUrl()) . '"', 'href="' . xnhtmlentities(XG_HttpHelper::signInUrl()) . '"') %></p>
                        </div>
                    <?php
                    } else { ?>
                        <div class="comment-join">
                            <%= $htmlIfCannotAddComment %>
                        </div>
                    <?php
                    }
                    if ($commentsAboveForm) { XG_PaginationHelper::outputPagination($numComments, $pageSize, null, null, $pageParamName, false, '#comments'); } ?>
                </div>
            <?php
            }
            if ($commentsBelowForm) { self::outputCommentsProper($args, true); }
            if (XG_App::appIsPrivate() || XG_GroupHelper::groupIsPrivate()) { $feedUrl = null; }
            if ($showFollowLink || $feedUrl) { ?>
                <div class="xg_module_foot" <%= $numComments ? '' : 'style="display:none"' %>>
                    <?php
                    if ($feedUrl) {
                        xg_autodiscovery_link($feedUrl, $feedTitle, $feedFormat); ?>
                        <p class="left">
                            <a class="desc rss" href="<%= xnhtmlentities($feedUrl) %>"><%= xg_html('RSS') %></a>
                        </p>
                    <?php
                    }
                    if ($showFollowLink) {
                        echo xg_follow_unfollow_links($attachedTo);
                    } ?>
                </div>
            <?php
            } ?>
        </div>
    <?php
    }

    /**
     * Returns whether an XG_CommentController feed is available for the comments on the given object.
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @return boolean  whether the XG_CommentController publishes a feed
     */
    public static function feedAvailable($attachedTo) {
        return self::feedAvailableProper($attachedTo, XG_App::appIsPrivate(), XG_GroupHelper::groupIsPrivate());
    }

    /**
     * Returns whether an XG_CommentController feed is available for the comments on the given object.
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @param $appIsPrivate boolean  whether the network is private
     * @param $groupIsPrivate boolean  whether we are in a group context, and the group is private
     * @return boolean  whether the XG_CommentController publishes a feed
     */
    protected static function feedAvailableProper($attachedTo, $appIsPrivate, $groupIsPrivate) {
        if ($appIsPrivate || $groupIsPrivate) { return false; }
        return $attachedTo->my->visibility == null || $attachedTo->my->visibility == 'all';
    }

    /**
     * Displays the comments.
     *
     * @param $commentData array  array of arrays, each with:
     *         - comment - the Comment object
     *         - canDelete - whether the current user is allowed to delete the comment
     *         - deleteEndpoint - URL of the action for deleting a comment. The comment ID will be passed
     *                 as a POST variable named "id". Should return a JSON object with success: true
     *         - canApprove - whether the current user is allowed to approve the comment
     *         - approveEndpoint - URL of the action for approving a comment. The comment ID will be passed
     *                 as a POST variable named "id". Should return a JSON object with success: true
     * @param $numComments integer  the total number of Comments
     * @param $pageSize integer  the number of Comments per page
     * @param $pageParamName string  The name of the url parameter for the page number; defaults to 'page'
     * @param $showPagination boolean  whether to display the pagination before the closing module tag
     */
    private static function outputCommentsProper($args, $showPagination) {
        foreach ($args as $key => $value) { ${$key} = $value; } ?>
        <div class="xg_module_body" id="comments" <%= $numComments == 0 ? 'style="display: none"' : '' %> _numComments="<%= $numComments %>">
            <p><%=  xg_html('N_COMMENTS', xg_number($numComments)) %></p>
            <?php
            $n = count($commentData);
            for ($i = 0; $i < $n; $i++) {
                self::outputComment($commentData[$i], $i == $n - 1 ? 'last_comment' : ($i == 0 ? 'first_comment' : null));
            }
            if ($showPagination) { XG_PaginationHelper::outputPagination($numComments, $pageSize, null, null, $pageParamName, false, '#comments'); } ?>
        </div>
    <?php
    }

    /**
     * Displays the comment.
     *
     * @param $comment XN_Content|W_Content  the Comment object
     * @param $canDelete boolean  whether the current user is allowed to delete the comment
     * @param $deleteEndpoint string  URL of the action for deleting a comment.
     *         The comment ID will be passed as a POST variable named "id". Should return a JSON object with success: true
     * @param $canApprove boolean  whether the current user is allowed to approve the comment
     * @param $approveEndpoint string  URL of the action for approving a comment.
     *         The comment ID will be passed as a POST variable named "id". Should return a JSON object with success: true
     * @param $anchorName string  name for an anchor tag, or null to skip
     */
    public static function outputComment($args, $anchorName = null) {
        foreach ($args as $key => $value) { ${$key} = $value; }
        if ($deleteEndpoint && mb_strpos($deleteEndpoint, 'xn_out') === false) { throw new Exception('$deleteEndpoint should contain xn_out=json'); }
        if ($approveEndpoint && mb_strpos($approveEndpoint, 'xn_out') === false) { throw new Exception('$approveEndpoint should contain xn_out=json'); }
        $deleteAttributes = 'href="#" _url="' . xnhtmlentities($deleteEndpoint) . '" class="delete_link"';
        $approveAttributes = 'href="#" _url="' . xnhtmlentities($approveEndpoint) . '" class="approve_link"';
        $contributor = XG_Cache::profiles($comment->contributorName);
        $commentAnchorId = XG_CommentHelper::commentAnchorId($comment);
        $contributorLink = xg_userlink(XG_Cache::profiles($comment->contributorName), 'class="fn url"', true); ?>
        <dl _id="<%= $comment->id %>" id="<%= $commentAnchorId %>" class="comment vcard xg_lightborder<%= $anchorName == 'last_comment' ? ' last-child' : '' %>">
            <dt>
                <%= $anchorName ? '<a name="' . $anchorName . '"></a>' : '' %>
                <%= xg_avatar($contributor, 48, 'photo') %>
                <?php
                $time = xg_elapsed_time($comment->createdDate, $showingMonth);
                if ($showingMonth) {
                    echo xg_html('COMMENT_BY_USER_DATE_TIME', $contributorLink,
                            xnhtmlentities(xg_date(xg_text('F_J_Y'), $comment->createdDate)), xnhtmlentities(xg_date(xg_text('G_IA'), $comment->createdDate)));
                } else {
                    echo xg_html('COMMENT_BY_USER_WHEN', $contributorLink, xnhtmlentities($time));
                } ?>
            </dt>
            <dd>
                <?php
                if ($canDelete && ! ($canApprove && $comment->my->approved == 'N')) { ?>
                    <a <%= str_replace('delete_link', 'icon delete delete_link', $deleteAttributes) %> title="<%= xg_html('DELETE_COMMENT') %>"><%= xg_html('DELETE_COMMENT') %></a>
                <?php
                } ?>
                <%= xg_nl2br(xg_resize_embeds($comment->description, 646)) %>
            </dd>
            <?php
            if ($canDelete && $canApprove && $comment->my->approved == 'N') { ?>
                <dd class="item_approval">
                    <div class="pad5 right notification">
                        <small><%= xg_html('AWAITING_APPROVAL_APPROVE_DELETE', $approveAttributes, $deleteAttributes) %></small>
                    </div>
                </dd>
            <?php
            } elseif ($canApprove && $comment->my->approved == 'N') { ?>
                <dd class="item_approval">
                    <div class="pad5 right notification">
                        <small><%= xg_html('AWAITING_APPROVAL_APPROVE', $approveAttributes) %></small>
                    </div>
                </dd>
            <?php
            } ?>
        </dl>
    <?php
    }

    /**
     * Returns an anchor ID element for the provided $comment
     *
     * @param XN_Content|W_Content $comment
     * @return string
     */
    public static function commentAnchorId($comment) {
        return 'comment-' . str_replace(':', '_', $comment->id);
    }

    /**
     * Returns whether the current user is allowed to see the Add A Comment form/message.
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSeeAddCommentSection($attachedTo) {
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        return self::currentUserCanSeeAddCommentSectionProper(array(
                'attachedTo' => $attachedTo,
                'addCommentPermission' => User::load($attachedTo->contributorName)->my->addCommentPermission,
                'currentUserScreenName' => XN_Profile::current()->screenName,
                'currentUserIsFriend' => XG_ContactHelper::getFriendStatusFor(XN_Profile::current()->screenName, $attachedTo->contributorName) == 'friend'));
    }

    /**
     * Returns whether the current user is allowed to see the Add A Comment form/message.
     *
     * @param $attachedTo XN_Content|W_Content  the content object that the comments are attached to
     * @param $addCommentPermission string  who is allowed to comment on the content object: all, friends, me
     * @param $currentUserScreenName string  username of the current user
     * @param $currentUserIsFriend boolean  whether the current user is a friend of the creator of the content object
     * @return boolean  Whether permission is granted
     */
    protected static function currentUserCanSeeAddCommentSectionProper($args) {
        foreach ($args as $key => $value) { ${$key} = $value; }
        $currentUserIsContentCreator = $attachedTo->contributorName == $currentUserScreenName;
        switch($addCommentPermission) {
            case 'all': return true;
            case 'friends': return $currentUserIsContentCreator || $currentUserIsFriend;
            case 'me': return $currentUserIsContentCreator;
            default: throw new Exception('Assertion failed (813970344)');
        }
    }

    /**
     * Returns whether the current user is allowed to delete the comment.
     *
     * @param $comment  XN_Content|W_Content  The Comment object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeleteComment($comment) {
        if (XG_SecurityHelper::userIsAdmin()) { return true; }
        if (XN_Profile::current()->screenName == $comment->contributorName) { return true; }
        if (XN_Profile::current()->screenName == $comment->my->attachedToAuthor) { return true; }
        return false;
    }

    /**
     * Returns the URL of the page that the comment is on.
     * Assumes that $newestCommentsFirst is false.
     *
     * @param $comment XN_Content|W_Content  the Comment object
     * @param $pageParamName string  the name of the url parameter for the page number
     * @param $pageSize integer  the number of Comments per page
     * @return string  the URL
     */
    public static function url($comment, $pageParamName = 'page', $pageSize = self::DEFAULT_PAGE_SIZE) {
        $url = XG_GroupHelper::buildUrl($comment->my->mozzle, mb_strtolower($comment->my->attachedToType), 'show', array('id' => $comment->my->attachedTo, 'groupId' => $comment->my->groupId));
        if ($pageSize) { $url = XG_HttpHelper::addParameter($url, $pageParamName, self::page($comment, $pageSize)); }
        return $url;
    }

    /**
     * Returns the page number that the given comment is on.
     *
     * @param XN_Content|W_Content $comment  The Comment object
     * @param integer $pageSize  The number of comments per page
     * @return integer  1 for the first page, 2 for the second, etc.
     */
    public static function page($comment, $pageSize, $createQueryClassName = 'XG_CommentHelper') {
        $query = call_user_func(array($createQueryClassName, 'createQuery'), $comment->my->attachedTo, 0, 1);
        $query->filter('createdDate', '<=', $comment->createdDate, XN_Attribute::DATE);
        $results = $query->execute();
        $totalCount = $query->getTotalCount();
        // Workaround for NING-6842 [Jon Aquino 2008-01-10]
        if (! $results[0] || $results[0]->id != $comment->id) { $totalCount++; }
        return ceil($totalCount / $pageSize);
    }

    /**
     * Create a query for Comment objects. alwaysReturnTotalCount will be set to TRUE.
     *
     * @param string $attachedTo  The ID of the object that the comments are attached to
     * @param integer $begin  The zero-based position of the first Comment for the query to retrieve
     * @param integer $end  The zero-based position + 1 of the last Comment for the query to retrieve
     * @return XN_Query  The new query object
     */
    protected static function createQuery($attachedTo, $begin, $end) {
        $query = XN_Query::create('Content');
        $query->filter('type', '=', 'Comment');
        $query->filter('owner');
        $query->filter('my->attachedTo', '=', (string) $attachedTo);
        $query->order('createdDate', 'desc', XN_Attribute::DATE);
        $query->begin($begin);
        $query->end($end);
        $query->alwaysReturnTotalCount(true);
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'Comment'));
        }
        return $query;
    }

}
