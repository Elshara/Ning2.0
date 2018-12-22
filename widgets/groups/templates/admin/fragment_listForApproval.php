<?php
/**
 * Renders the list of pending photos. TODO:: this template is still in progress
 *
 * @param $groups  The pending groups
 * @param $changeUrl The url to use when the page has been changed. May contain parameters.
 * @param $curPage  The current page to show
 * @param $numPages  The total number of pages
 */
if (count($groups) == 0) { ?>
    <div class="xg_module">
        <div class="xg_module_head notitle"></div>
        <div class="xg_module_body">
            <h3><big><%= xg_html('YOU_HAVE_FINISHED_MODERATING') %></big></h3>
            <p><%= xg_html('NO_GROUPS_AWAITING_APPROVAL', 'href="' . xnhtmlentities($this->_buildUrl('group', 'list', array('sort' => 'mostRecent'))) . '"') %></p>
        </div>
    </div>
<?php
} else { ?>
    <div class="xg_module">
        <div class="xg_module_body approve">
            <?php if (XG_SecurityHelper::userIsOwner()) { ?>
                <?php
                if (XG_App::contentIsModerated()) { ?>
                    <p><%= xg_html('YOUR_SITE_REQUIRES_YOU_GROUPS', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('privacy', 'edit')) . '"') %></p>
                <?php
                } else { ?>
                    <p><%= xg_html('YOUR_SITE_ALLOWS_USERS_GROUPS', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('privacy', 'edit')) . '"') %></p>
                <?php
                } ?>
            <?php } ?>
            <p>
                <?php XG_App::ningLoaderRequire('xg.index.bulk'); ?>
                <?php $limit = 20 ?>
                <a class="button button-primary" dojoType="BulkActionLink"
                    title ="<%= xg_html('APPROVE_ALL_GROUPS') %>"
                    _verb="<%= xg_html('APPROVE') %>"
                    _confirmMessage="<%= xg_html('ARE_YOU_SURE_APPROVE_ALL_GROUPS') %>"
                    _url="<%= xnhtmlentities($this->_buildUrl('bulk', 'approveAll', array('limit' => $limit, 'xn_out' => 'json'))) %>"
                    _successUrl="<%= $this->_buildUrl('admin', 'listForApproval') %>"
                    _progressTitle="<%= xg_html('APPROVING') %>"
                    ><%= xg_html('APPROVE_ALL') %>
                </a>
                <a class="button" dojoType="BulkActionLink"
                    title ="<%= xg_html('DELETE_ALL_GROUPS') %>"
                    _verb="<%= xg_html('DELETE') %>"
                    _confirmMessage="<%= xg_html('ARE_YOU_SURE_DELETE_ALL_GROUPS') %>"
                    _url="<%= xnhtmlentities($this->_buildUrl('bulk', 'removeUnapprovedGroups', array('limit' => $limit, 'xn_out' => 'json'))) %>"
                    _successUrl="<%= $this->_buildUrl('admin', 'listForApproval') %>"
                    _progressTitle="<%= xg_html('DELETING') %>"
                    ><%= xg_html('DELETE_ALL') %>
                </a>
            </p>
            <?php
            if (count($groups)) {
                XG_App::ningLoaderRequire('xg.groups.admin.ApprovalLink');
            }
            foreach ($groups as $group) {
                $pageAfterAction = count($groups) == 1 ? ($curPage > 1 ? $curPage - 1 : 1) : $curPage;
                ?>
                <div class="approval_row easyclear">
                    <p class="approval_buttons">
                        <a class="button button-primary" href="#" dojoType="ApprovalLink"
                            _processGroupUrl="<%= xnhtmlentities($this->_buildUrl('admin', 'approve', array('id' => $group->id, 'approved' => 'Y', 'page' => $pageAfterAction, 'xn_out' => 'json'))) %>"
                            _processAllGroupsForUserUrl="<%= xnhtmlentities($this->_buildUrl('bulk', 'approveByUser', array('limit' => 20, 'user' => $group->contributorName, 'xn_out' => 'json'))) %>"
                            _verb="<%= xg_html('APPROVE') %>"
                            _progressTitle="<%= xg_html('APPROVING') %>"
                            _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_GROUPS_APPROVED') %>"
                            _approvalListUrl="<%= xnhtmlentities($this->_buildUrl('admin', 'listForApproval')) %>"
                            ><%= xg_html('APPROVE') %></a>
                        <a class="button" href="#" dojoType="ApprovalLink"
                            _processGroupUrl="<%= xnhtmlentities($this->_buildUrl('admin', 'approve', array('id' => $group->id, 'approved' => 'N', 'page' => $pageAfterAction, 'xn_out' => 'json'))) %>"
                            _processAllGroupsForUserUrl="<%= xnhtmlentities($this->_buildUrl('bulk', 'removeUnapprovedGroups', array('limit' => 20, 'user' => $group->contributorName, 'xn_out' => 'json'))) %>"
                            _verb="<%= xg_html('DELETE') %>"
                            _progressTitle="<%= xg_html('DELETING') %>"
                            _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_GROUPS_DELETED') %>"
                            _approvalListUrl="<%= xnhtmlentities($this->_buildUrl('admin', 'listForApproval')) %>"
                            ><%= xg_html('DELETE') %></a>
                        <img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" style="display:none" class="approval_spinner" />
                        <?php
                        $numGroupsOnPageByContributor = 0;
                        foreach ($groups as $g) {
                            if ($g->contributorName == $group->contributorName) { $numGroupsOnPageByContributor++; }
                        } ?>
                        <label <%= $numPages > 1 || $numGroupsOnPageByContributor > 1 ? '' : 'style="display: none;"' %>><input type="checkbox" class="checkbox" /><%= xg_html('APPLY_TO_GROUPS_ADDED_BY', xnhtmlentities(xg_username($group->contributorName))) %></label>
                    </p>
                    <dl class="approval_item">
                        <dt>
                            <a href="<%= xnhtmlentities($this->_buildUrl('group', 'show', array('id' => $group->id))) %>">
                                <img class="left" alt="" src="<%= XG_HttpHelper::addParameters($group->my->iconUrl, array('crop' => '1:1', 'width' => '82', 'height' => '82')) %>" />
                                <%= xnhtmlentities($group->title) %>
                            </a>
                        </dt>
                        <?php $contributorLink = '<a href="' . xnhtmlentities(XG_HttpHelper::profileUrl($group->contributorName)) . '">' . xnhtmlentities(xg_username($group->contributorName)) . '</a>'; ?>
                        <dd><%= xg_html('ADDED_BY_STRONG_X', $contributorLink) %></dd>
                        <dd><%= $group->description ? xnhtmlentities($group->description) : '<em>' . xg_html('NO_DESCRIPTION') . '</em>' %></dd>
                        </dd>
                    </dl>
                </div>
            <?php
            } ?>
            <?php XG_PaginationHelper::outputPagination($this->totalCount, $this->pageSize); ?>
        </div>
    </div>
<?php
} ?>