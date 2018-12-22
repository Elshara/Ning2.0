<?php xg_header('manage', xg_text('MEMBERS')) ?>
<?php XG_App::ningLoaderRequire('xg.index.bulk');
      XG_App::ningLoaderRequire('xg.index.membership.CheckForMembers'); 
?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<%= xg_headline(xg_text('MEMBERS'))%>
				<?php $this->renderPartial('fragment_success', 'admin'); ?>
                <div class="xg_module">
                        <div class="xg_module_body pad">
                            <?php
                            $this->_widget->dispatch('membership', 'tabs', array(xg_text('MEMBERS'), $this->tabs)); ?>
                            <%= $this->renderPartial('fragment_search_members') %>
                            <form method="post" id="xg_member_form" action="<%= $this->_widget->buildUrl('membership', 'saveMembers') %>">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <input type="hidden" name="page" value="<%= $this->page %>">
                                <?php if (isset($this->q)) { ?>
                                    <input type="hidden" name="q" value="<%= $this->q %>">
                                <?php } ?>
                                <input type="hidden" name="operation" value="default">
                            <p>
                                <small>
                                    <?php if (XG_SecurityHelper::userIsOwner()) { // Don't let admins create/remove admins ?>
                                        <a dojoType="CheckForMembers" href="#" class="button"
                                            _type="promote"
                                            _errorMsg="<%= xg_html('PLEASE_SELECT_A_MEMBER') %>"
                                        ><%= xg_html('PROMOTE_TO_ADMINISTRATOR') %></a>
                                        <?php $this->renderPartial('fragment_demoteButton'); ?>
                                    <?php } ?>
                                    <a id="ban_button" dojoType="BulkActionLink"
                                    title="<%= xg_html('BAN_FROM_NETWORK') %>"
                                    _url="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('bulk','removeByUser', array('xn_out' => 'json'))) %>"
                                    _verb="<%= xg_html('BAN') %>"
                                    _confirmMessage="<%= xg_html('ARE_YOU_SURE_BAN_MEMBERS_AND_CONTENT') %>"
                                    _progressTitle="<%= xg_html('REMOVING_MEMBERS') %>"
                                    _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_CONTENT_DELETED') %>"
                                    _successUrl="<%= $this->_widget->buildUrl('membership', 'listMembers', array_merge($_GET, array('saved' => 1))) %>" href="#"
                                    _ensureCheckboxClicked = 'true'
                                    _formId = 'xg_member_form'
                                    _checkboxSelectMessage = "<%= xg_html('PLEASE_SELECT_A_MEMBER') %>"
                                    class="button">
                                    <%= xg_html('BAN_FROM_NETWORK') %></a>
                                </small>
                            </p>
                            <?php if (XG_SecurityHelper::userIsOwner()) { ?>
                                <p style="margin:.5em 0 0">
                                    <small><%= xg_html('ADMINISTRATORS_HAVE_A_SIMILAR_LEVEL') %></small>
                                </p>
                            <?php } ?>
                            <?php
                            $users = array();
                            XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
                            foreach ($this->memberInfo['users'] as $user) {
                                $p = $this->memberProfiles[$user->title];
                                if (XG_SecurityHelper::userIsOwner()) {
                                    $showCheckbox = !XG_SecurityHelper::userIsOwner($p);
                                } else {
                                    $showCheckbox = !XG_SecurityHelper::userIsAdmin($p);
                                }
                                if (XG_SecurityHelper::userIsOwner($p)) {
                                    $statusHtml = '<td><div class="creator">' . xg_html('NETWORK_CREATOR') . '</div></td>';
                                    $status = XG_MembershipHelper::OWNER;
                                } else if (XG_SecurityHelper::userIsAdmin($p)) {
                                    $statusHtml = '<td><div class="admin">' . xg_html('NETWORK_ADMINISTRATOR') . '</div></td>';
                                    $status = XG_MembershipHelper::ADMINISTRATOR;
                                } else {
                                    $statusHtml = '<td><div class="member">' . xg_html('MEMBER') . '</div></td>';
                                    $status = XG_MembershipHelper::MEMBER;
                                }
                                $users[] = array(
                                        'name' => xg_username($p),
                                        'profileUrl' => User::quickProfileUrl($p->screenName),
                                        'ningId' => $p->screenName,
                                        'checkboxName' => $showCheckbox ? 'user_' . $p->screenName : null,
                                        'email' => $p->email,
                                        'date' => $user->createdDate,
                                        'statusHtml' => $statusHtml,
                                        'status' => $status);
                            }
                            $this->_widget->dispatch('membership', 'table', array(xg_text('DATE_JOINED'), $users));
                            XG_PaginationHelper::outputPagination($this->memberInfo['numUsers'], $this->pageSize); ?>
                            <p class="right clear"><a class="desc download" style="cursor:pointer;"
                                dojoType="BulkActionLink"
                                _verb="<%= xg_html('OK') %>"
                                title="<%= xg_text('DOWNLOAD_ALL_MEMBER_DATA')%>"
                                _url="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('bulk','exportMemberData', array('xn_out' => 'json'))) %>"
                                _successMessage="<%= xg_html('CLICK_TO_SEE_MEMBER_DATA', "href='{$this->_widget->buildUrl('membership','downloadMemberData')}' target='_new'") %>"
                                _confirmMessage="<%= xg_html('CLICK_GO_TO_START_EXPORT') %>"
                                _successUrl=""
                            ><%= xg_text('DOWNLOAD_ALL_MEMBER_DATA')%></a></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="xg_1col last-child">
                <?php xg_sidebar($this) ?>
            </div>
        </div>
    </div>
<?php xg_footer() ?>
