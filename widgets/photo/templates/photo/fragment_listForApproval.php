<?php
/**
 * Renders the list of pending photos.
 *
 * @param $photos  The pending photos
 * @param $changeUrl The url to use when the page has been changed. May contain parameters.
 * @param $curPage  The current page to show
 * @param $numPages  The total number of pages
 */
if (count($photos) == 0) { ?>
    <div class="xg_module">
        <div class="xg_module_head notitle"></div>
        <div class="xg_module_body">
            <h3><big><%= xg_html('YOU_HAVE_FINISHED_MODERATING') %></big></h3>
            <p><%= xg_html('NO_PHOTOS_AWAITING_APPROVAL', 'href="' . xnhtmlentities($this->_buildUrl('photo', 'list')) . '?sort=mostRecent"') %></p>
        </div>
    </div>
<?php
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
                <a class="button button-primary" href="#" dojoType="BulkActionLink"
                    title ="<%= xg_html('APPROVE_ALL_PHOTOS') %>"
                    _verb="<%= xg_html('APPROVE') %>"
                    _confirmMessage="<%= xg_html('ARE_YOU_SURE_APPROVE_ALL_PHOTOS') %>"
                    _url="<%= xnhtmlentities($this->_buildUrl('bulk', 'approveAll', array('limit' => $limit, 'xn_out' => 'json'))) %>"
                    _successUrl="<%= $this->_buildUrl('photo', 'listForApproval') %>"
                    _progressTitle="<%= xg_html('APPROVING') %>"
                    ><%= xg_html('APPROVE_ALL') %>
                </a>
                <a class="button" href="#" dojoType="BulkActionLink"
                    title ="<%= xg_html('DELETE_ALL_PHOTOS') %>"
                    _verb="<%= xg_html('DELETE') %>"
                    _confirmMessage="<%= xg_html('ARE_YOU_SURE_DELETE_ALL_PHOTOS') %>"
                    _url="<%= xnhtmlentities($this->_buildUrl('bulk', 'removeUnapprovedPhotos', array('limit' => $limit, 'xn_out' => 'json'))) %>"
                    _successUrl="<%= $this->_buildUrl('photo', 'listForApproval') %>"
                    _progressTitle="<%= xg_html('DELETING') %>"
                    ><%= xg_html('DELETE_ALL') %>
                </a>
            </p>
            <?php
            if (count($photos)) {
				XG_App::ningLoaderRequire('xg.photo.photo.listForApproval');
			}
            foreach ($photos as $photo) {
                Photo_HtmlHelper::fitImageIntoThumb($photo, $imgColumnWidth=150, 400, $imgUrl, $imgWidth, $imgHeight);
                $pageAfterAction = count($photos) == 1 ? ($curPage > 1 ? $curPage - 1 : 1) : $curPage;
                ?>
                <div class="approval_row easyclear">
                    <p class="approval_buttons">
                        <a class="button button-primary" href="#" dojoType="ApprovalLink"
                            _processPhotoUrl="<%= xnhtmlentities($this->_buildUrl('photo', 'approve', '?id=' . $photo->id . '&approved=Y&page=' . $pageAfterAction)); %>"
                            _processAllPhotosForUserUrl="<%= xnhtmlentities($this->_buildUrl('bulk', 'approveByUser', array('limit' => 20, 'user' => $photo->contributorName, 'xn_out' => 'json'))) %>"
                            _verb="<%= xg_html('APPROVE') %>"
                            _progressTitle="<%= xg_html('APPROVING') %>"
                            _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_PHOTOS_APPROVED') %>"
                            _approvalListUrl="<%= xnhtmlentities($this->_buildUrl('photo', 'listForApproval')) %>"
                            ><%= xg_html('APPROVE') %></a>
                        <a class="button" href="#" dojoType="ApprovalLink"
                            _processPhotoUrl="<%= xnhtmlentities($this->_buildUrl('photo', 'approve', '?id=' . $photo->id . '&approved=N&page=' . $pageAfterAction)); %>"
                            _processAllPhotosForUserUrl="<%= xnhtmlentities($this->_buildUrl('bulk', 'removeUnapprovedPhotos', array('limit' => 20, 'user' => $photo->contributorName, 'xn_out' => 'json'))) %>"
                            _verb="<%= xg_html('DELETE') %>"
                            _progressTitle="<%= xg_html('DELETING') %>"
                            _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_PHOTOS_DELETED') %>"
                            _approvalListUrl="<%= xnhtmlentities($this->_buildUrl('photo', 'listForApproval')) %>"
                            ><%= xg_html('DELETE') %></a>
                        <img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" style="display:none" class="approval_spinner" />
                        <?php
                        $numPhotosOnPageByContributor = 0;
                        foreach ($photos as $p) {
                            if ($p->contributorName == $photo->contributorName) { $numPhotosOnPageByContributor++; }
                        } ?>
                        <label <%= $numPages > 1 || $numPhotosOnPageByContributor > 1 ? '' : 'style="display: none;"' %>><input type="checkbox" class="checkbox" /><%= xg_html('APPLY_TO_PHOTOS_ADDED_BY', xnhtmlentities(Photo_FullNameHelper::fullName($photo->contributorName))) %></label>
                    </p>
                    <dl class="approval_item">
                        <dt>
                            <a href="<%= xnhtmlentities($this->_buildUrl('photo', 'show', array('id' => $photo->id))) %>">
                                <img class="left" alt="" src="<%= $imgUrl %>" />
                                <%= $photo->title ? xnhtmlentities($photo->title) : '<em>' . xg_html('NO_TITLE') . '</em>' %>
                            </a>
                        </dt>
                        <dd><%= xg_html('ADDED_BY_STRONG_X', Photo_HtmlHelper::linkedScreenName($photo->contributorName)) %></dd>
                        <dd><%= $photo->description ? xnhtmlentities($photo->description) : '<em>' . xg_html('NO_DESCRIPTION') . '</em>' %></dd>
                    </dl>
                </div>
            <?php
            } ?>
            <?php
            $this->renderPartial('fragment_pagination', 'photo', array(
                    'targetUrl' => $changeUrl, 'pageParamName' => 'page', 'curPage' => $curPage, 'numPages' => $numPages)); ?>
        </div>
    </div>
<?php
} ?>
