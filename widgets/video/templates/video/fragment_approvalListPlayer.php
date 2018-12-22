<?php
/**
 * Display for a video on the Approval page.
 *
 * @param $video XN_Content|W_Content the Video to display
 * @param $currentUrl string URL for the current page
 */ ?>
<div class="approval_row easyclear" dojoType="ApprovalListPlayer"
        _contributorName="<%= xnhtmlentities($video->contributorName) %>"
        _contributorFullName="<%= xnhtmlentities(Video_FullNameHelper::fullName($video->contributorName)) %>"
        _approvalListUrl="<%= xnhtmlentities($this->_buildUrl('video', 'listForApproval')) %>"
        _approveVideoUrl="<%= xnhtmlentities($this->_buildUrl('video', 'approve', '?id=' . $video->id . '&approved=Y&json=yes&target=' . urlencode($currentUrl))) %>"
        _deleteVideoUrl="<%= xnhtmlentities($this->_buildUrl('video', 'approve', '?id=' . $video->id . '&approved=N&json=yes&target=' . urlencode($currentUrl))) %>"
        _approveAllVideosForUserUrl="<%= xnhtmlentities($this->_buildUrl('bulk', 'approveByUser', array('limit' => 20, 'user' => $video->contributorName, 'xn_out' => 'json'))) %>"
        _deleteAllVideosForUserUrl="<%= xnhtmlentities($this->_buildUrl('bulk', 'removeUnapprovedVideos', array('limit' => 20, 'user' => $video->contributorName, 'xn_out' => 'json'))) %>">
    <p class="approval_buttons">
		<input type="button" class="button xj_approve" value="<%= qh(xg_html('APPROVE')) %>" />
        <input type="button" class="button xj_delete" value="<%= qh(xg_html('DELETE')) %>" />
        <img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" style="display:none" class="approval_spinner xj_spinner" />
        <label style="display: none"><input type="checkbox" class="checkbox" /><%= xg_html('APPLY_TO_VIDEOS_ADDED_BY', xnhtmlentities(Video_FullNameHelper::fullName($video->contributorName))) %></label>
    </p>
    <dl class="approval_item">
        <dt>
            <%= $this->renderPartial('fragment_thumbnailProper', array('video' => $video, 'thumbWidth' => 150, 'imgClass' => 'left')); %>
            <a href="<%= xnhtmlentities($this->_buildUrl('video', 'show', array('id' => $video->id))) %>">
                <%= $video->title ? xnhtmlentities($video->title) : '<em>' . xg_html('NO_TITLE') . '</em>' %>
            </a>
        </dt>
        <dd><%= xg_html('ADDED_BY_STRONG_X', Video_HtmlHelper::linkedScreenName($video->contributorName)) %></dd>
        <dd><%= $video->description ? Video_HtmlHelper::excerpt($video->description, 150, $this->_buildUrl('video', 'show') . '?id=' . $video->id) : '<em>' . xg_html('NO_DESCRIPTION') . '</em>' %></dd>
    </dl>
</div>
