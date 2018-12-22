<?php xg_header('manage', xg_text('MEMBERS')) ?>
<?php
XG_App::ningLoaderRequire('xg.index.bulk');
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
                            $this->_widget->dispatch('membership', 'tabs', array(xg_text('PENDING'), $this->tabs)); ?>
                            <%= $this->renderPartial('fragment_search_members') %>
                            <form method="post" id="xg_member_form" action="<%= $this->_widget->buildUrl('membership', 'savePending') %>">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <input type="hidden" name="page" value="<%= $this->page %>" />
                                <input type="hidden" name="operation" value="" />
                            <p>
                                <small>
                                    <a dojoType="CheckForMembers" href="#" class="button"
                                        _type="acceptMember"
                                        _errorMsg="<%= xg_html('PLEASE_SELECT_A_MEMBER') %>"
                                    ><%= xg_html('ACCEPT_MEMBERSHIP') %></a>
                                    <a dojoType="CheckForMembers" href="#" class="button"
                                        _type="declineMember"
                                        _errorMsg="<%= xg_html('PLEASE_SELECT_A_MEMBER') %>"
                                    ><%= xg_html('DECLINE_MEMBERSHIP') %></a>
                                    <a id="ban_button" dojoType="BulkActionLink"
                                    title="<%= xg_html('BAN_FROM_NETWORK') %>"
                                    _url="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('bulk','removeByUser', array('user' => $this->profile->screenName, 'xn_out' => 'json'))) %>"
                                    _verb="<%= xg_html('BAN') %>"
                                    _confirmMessage="<%= xg_html('ARE_YOU_SURE_BAN_MEMBERS') %>"
                                    _progressTitle="<%= xg_html('REMOVING_MEMBERS') %>"
                                    _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_MEMBERS_DELETED') %>"
                                    _successUrl="<%= xnhtmlentities($this->_buildUrl('membership','listPending', array('saved' => 1))) %>" href="#" class="button">
                                    <%= xg_html('BAN_FROM_NETWORK') %></a>
                                </small>
                            </p>
                            <?php
                            $users = array();
                            XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
                            foreach ($this->pendingProfiles as $p) {
                                $users[] = array(
                                        'name' => xg_username($p),
                                        'ningId' => $p ? $p->screenName : null,
                                        'checkboxName' => 'user_' . $p->screenName,
                                        'email' => $p->email,
                                        'date' => $this->pendingUsers[$p->screenName]->createdDate,
                                        'viewProfileUrl' => W_Cache::getWidget('profiles')->buildUrl('profile','showPending',array('id' => $p->screenName)),
                                        'statusHtml' => '<td><div class="requested">' . xg_html('PENDING_APPROVAL') . '</div></td>',
                                        'status' => $status = XG_MembershipHelper::REQUESTED);
                            }
                            $this->_widget->dispatch('membership', 'table', array(xg_text('DATE_APPLIED'), $users, $this->extraColumns));
                            XG_PaginationHelper::outputPagination($this->pendingInfo['numUsers'], $this->pageSize); ?>
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
