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
                        $this->_widget->dispatch('membership', 'tabs', array(xg_text('ADMINISTRATORS'), $this->tabs)); ?>
                        <%= $this->renderPartial('fragment_search_members') %>
                        <form method="post" id="xg_member_form" action="<%= $this->_widget->buildUrl('membership', 'saveAdministrators') %>">
                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                            <input type="hidden" name="page" value="<%= $this->page %>">
                            <input type="hidden" name="operation" value="default">
                        <p>
                            <small>
                                <?php
                                if (XG_SecurityHelper::userIsOwner()) {
                                    $this->renderPartial('fragment_demoteButton');
                                } ?>
                            </small>
                        </p>
                        <?php
                        $users = array();
                        XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
                        foreach ($this->administratorInfo['users'] as $user) {
                            $p = $this->administratorProfiles[$user->contributorName];
                            $showCheckbox = !XG_SecurityHelper::userIsOwner($p);
                            if (XG_SecurityHelper::userIsOwner($p)) {
                                $statusHtml = '<td><div class="creator">' . xg_html('NETWORK_CREATOR') . '</div></td>';
                                $status = XG_MembershipHelper::OWNER;
                            } else if (XG_SecurityHelper::userIsAdmin($p)) {
                                $statusHtml = '<td><div class="admin">' . xg_html('NETWORK_ADMINISTRATOR') . '</div></td>';
                                $status = XG_MembershipHelper::ADMINISTRATOR;
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
                        XG_PaginationHelper::outputPagination($this->administratorInfo['numUsers'], $this->pageSize); ?>
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
