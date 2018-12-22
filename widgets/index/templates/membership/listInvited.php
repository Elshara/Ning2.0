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
                        if ($this->resendCount || $this->cancelCount) { ?>
                            <div class="success xg_module_body pad">
                                <?php if ($this->resendCount) { ?>
                                    <p class="last-child"><%= xg_html('N_INVITATIONS_RESENT', $this->resendCount) %></p>
                                <?php } elseif ($this->cancelCount) { ?>
                                    <p class="last-child"><%= xg_html('N_INVITATIONS_CANCELLED', $this->cancelCount) %></p>
                                <?php } ?>
                            </div>
                        <?php
                        }
                        $this->_widget->dispatch('membership', 'tabs', array(xg_text('INVITED'), $this->tabs)); ?>
                        <form method="post" id="xg_member_form" action="<%= $this->_widget->buildUrl('membership', 'saveInvited') %>">
                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                            <input type="hidden" name="page" value="<%= $this->page %>">
                            <input type="hidden" name="operation" value="default">
                        <p>
                            <small>
                                <a dojoType="CheckForMembers" href="#" class="button"
                                    _type="resendInvite"
                                    _errorMsg="<%= xg_html('PLEASE_SELECT_A_MEMBER') %>"
                                ><%= xg_html('RESEND_INVITATION') %></a>&#160;
                                <a dojoType="CheckForMembers" href="#" class="button"
                                    _type="cancelInvite"
                                    _errorMsg="<%= xg_html('PLEASE_SELECT_A_MEMBER') %>"
                                ><%= xg_html('CANCEL_INVITATION') %></a>
                            </small>
                        </p>
                        <?php
                        $this->_widget->dispatch('membership', 'table', array(xg_text('DATE_INVITED'), $this->users));
                        XG_PaginationHelper::outputPagination($this->totalNumInvites, $this->pageSize); ?>
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
