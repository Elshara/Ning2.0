<?php xg_header('manage', xg_text('MEMBERS')) ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<%= xg_headline(xg_text('MEMBERS'))%>
				<?php $this->renderPartial('fragment_success', 'admin'); ?>
                <form method="post" id="xg_member_form" action="<%= $this->_widget->buildUrl('membership', 'saveRequested') %>">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <input type="hidden" name="page" value="<%= $this->page %>" />
                    <input type="hidden" name="operation" value="default" />
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <?php
                            $this->_widget->dispatch('membership', 'tabs', array(xg_text('REQUESTED_INVITE'), $this->tabs)); ?>
                            <p>
                                <a href="/invite" class="right desc add"><strong><%= xg_html('INVITE_MORE_PEOPLE') %></strong></a>
                                <small>
                                    <a class="button" href="javascript:xg.index.membership.list.submitWithOp('invite')">
                                    <%= xg_html('INVITE') %></a>
                                    <a class="button" href="javascript:xg.index.membership.list.submitWithOp('delete')">
                                    <%= xg_html('IGNORE') %></a>
                                </small>
                            </p>
                            <?php
                            $users = array();
                            XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
                            foreach ($this->requests as $req) {
                                $p = $this->profiles[$req->my->requestor];
                                $users[] = array(
                                        'name' => $p ? xg_username($p) : $req->my->requestor,
                                        'ningId' => $p ? $p->screenName : null,
                                        'checkboxName' => 'req_' . $req->id,
                                        'date' => $req->createdDate,
                                        'email' => $p->email,
                                        'statusHtml' => '<td><div class="invited">' . xg_html('INVITED_BY_X', xg_userlink($this->profiles[$inv->my->invitedBy])) . '</div></td>',
                                        'status' => XG_MembershipHelper::REQUESTED);
                            }
                            $this->_widget->dispatch('membership', 'table', array(xg_text('DATE_REQUESTED'), $users));
                            XG_PaginationHelper::outputPagination($this->totalNumRequests, $this->pageSize); ?>
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
