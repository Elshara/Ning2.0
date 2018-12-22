<?php xg_header('manage', $title = xg_text('GROUPNAME_MEMBERS', $this->group->title)) ?>
<?php XG_App::ningLoaderRequire('xg.index.bulk'); ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<%= xg_headline($title, array('count' => $this->totalCount, 'byline1Html' => XG_GroupHelper::groupLink(true)))%>
                    <div class="xg_module">
                        <div class="xg_module_body">
                            <?php
                            if ($this->successMessage) { ?>
                                <div class="success xg_module_body">
                                    <p class="last-child"><%= xnhtmlentities($this->successMessage) %></p>
                                </div>
                            <?php
                            }
                            W_Cache::getWidget('main')->dispatch('membership', 'tabs', array('currentTab' => $this->currentTab, 'tabs' => array(
                                    array('text' => xg_text('MEMBERS'), 'count' => $this->memberCount, 'url' => $this->_buildUrl('user', 'editMembers', array('groupId' => $this->group->id))),
                                    array('text' => xg_text('INVITED'), 'count' => $this->invitationCount, 'url' => $this->_buildUrl('user', 'editInvitations', array('groupId' => $this->group->id))),
                                    array('text' => xg_text('REQUESTED_INVITE'), 'count' => $this->invitationRequestCount, 'url' => $this->_buildUrl('user', 'editInvitationRequests', array('groupId' => $this->group->id))),
                                    array('text' => xg_text('BANNED'), 'count' => $this->bannedCount, 'url' => $this->_buildUrl('user', 'editBanned', array('groupId' => $this->group->id)))))); ?>
                            <?php if ($this->searchable) { ?>
                                <%= $this->renderPartial('fragment_search_members') %>
                            <?php } ?>
                            <?php /* TODO: Fix: The opening and closing <form> tags are at different levels of nesting [Jon Aquino 2008-05-27] */ ?>
                            <form method="post" id="xg_member_form" action="<%= $this->_widget->buildUrl('user', 'update', array('groupId' => $this->group->id, 'target' => XG_HttpHelper::currentUrl())) %>">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <input type="hidden" name="operation" value="default">
                            <p>
                                <small><?php $this->renderPartial($this->buttonTemplate, 'user', array('group' => $this->group)); ?></small>
                            </p>
                            <?php
                            if ($this->buttonTemplate == 'fragment_membersTabButtons' && (Groups_SecurityHelper::currentUserCanSeePromoteToAdministratorButton($this->group) || Groups_SecurityHelper::currentUserCanSeeDemoteFromAdministratorButton($this->group))) { ?>
                                <p style="margin:.5em 0 0">
                                    <small><%= xg_html('GROUP_ADMINISTRATORS_HAVE_A_SIMILAR_LEVEL') %></small>
                                </p>
                            <?php
                            }
                            W_Cache::getWidget('main')->dispatch('membership', 'table', array($this->dateTitle, $this->users));
                            XG_PaginationHelper::outputPagination($this->totalCount, Groups_UserController::EDIT_PAGE_SIZE); ?>
                            <p class="right"><a href="<%= xnhtmlentities($this->_buildUrl('invitation','new', array('groupId' => $this->group->id))) %>" class="desc add"><strong><%= xg_html('INVITE_MORE_PEOPLE') %></strong></a></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="xg_1col">
                <?php xg_sidebar($this) ?>
            </div>
        </div>
    </div>
<?php xg_footer() ?>