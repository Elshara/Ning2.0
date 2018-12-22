<?php xg_header('manage', xg_text('MEMBERS')) ?>
<?php XG_App::ningLoaderRequire('xg.index.membership.CheckForMembers'); ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<%= xg_headline(xg_text('MEMBERS'))%>
				<?php $this->renderPartial('fragment_success', 'admin'); ?>
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <?php
                            $this->_widget->dispatch('membership', 'tabs', array(xg_text('BANNED'), $this->tabs)); ?>
                            <%= $this->renderPartial('fragment_search_members') %>
                            <form method="post" id="xg_member_form" action="<%= $this->_widget->buildUrl('membership', 'saveBanned') %>">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <input type="hidden" name="page" value="<%= $this->page %>" />
                            <p>
                                <small>
                                    <a dojoType="CheckForMembers"  href="#" class="button"
                                        _type="ban"
                                        _errorMsg="<%= xg_html('PLEASE_SELECT_A_MEMBER') %>"
                                    ><%= xg_html('REMOVE_BAN') %></a>
                                </small>
                            </p>
                            <?php
                            $users = array();
                            XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
                            foreach ($this->bannedInfo['users'] as $user) {
                                $p = $this->bannedProfiles[$user->contributorName];
                                $users[] = array(
                                        'name' => xg_username($p),
                                        'ningId' => $p->screenName,
                                        'email' => $p->email,
                                        'checkboxName' => 'user_' . $user->id,
                                        'date' => $user->createdDate,
                                        'statusHtml' => '<td><div class="blocked">' . xg_html('BANNED') . '</div></td>',
                                        'status' => XG_MembershipHelper::BANNED);
                            }
                            $this->_widget->dispatch('membership', 'table', array(xg_text('DATE_JOINED'), $users));
                            XG_PaginationHelper::outputPagination($this->bannedInfo['numUsers'], $this->pageSize); ?>
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
