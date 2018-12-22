<?php if (count($messages) > 0) { ?>
    <form id="xj_mailbox" _actionurl="<%= xnhtmlentities($this->_buildUrl('message', 'bulkActionUpdate', array('xn_out' => 'json'))) %>" _page="<%= $page %>" _folder="<%= $folder %>" _isinbox="<%= $folder === Profiles_MessageHelper::FOLDER_NAME_INBOX ? '1' : '0' %>" _isarchive="<%= $folder === Profiles_MessageHelper::FOLDER_NAME_ARCHIVE ? '1' : '0' %>"
        _isalerts="<%= $folder === Profiles_MessageHelper::FOLDER_NAME_ALERTS ? '1' : '0' %>">
        <table border="0" cellspacing="0" cellpadding="0" class="members bigmembers mail">
            <colgroup>
                <col width="34" />
                <col width="30" />
                <col width="46" />
                <col width="180" />
                <col width="100%" />
            </colgroup>
            <thead>
                <tr>
                    <th colspan="5">
                        <%= xg_html('SELECT_NO_HELLIP') %>
                        <?php
                        // TODO: These if statements can be made clearer if we take a more declarative approach:
                        // if ($folder === Profiles_MessageHelper::FOLDER_NAME_SENT) { $visibleElements = array('selectAll', 'selectNone', 'archive', 'delete'); }
                        // and so on for all the folders. [Jon Aquino 2008-09-18]
                        ?>
                        <a id="selectAll" href="#"><%= xg_html('ALL') %></a>,
                        <a id="selectNone" href="#"><%= xg_html('NONE') %></a>
                        <?php if ($folder !== Profiles_MessageHelper::FOLDER_NAME_SENT) { ?>,
                            <a id="selectRead" href="#"><%= xg_html('READ') %></a>,
                            <a id="selectUnread" href="#"><%= xg_html('UNREAD') %></a>
                        <?php } ?>
                        <select id="actions">
                            <option value="" selected="selected"><%= xg_html('ACTIONS_HELLIP') %></option>
                            <?php if ($folder !== Profiles_MessageHelper::FOLDER_NAME_SENT) { ?>
                                <option value="markRead"><%= xg_html('MARK_AS_READ') %></option>
                                <option value="markUnread"><%= xg_html('MARK_AS_UNREAD') %></option>
                            <?php } ?>
                            <?php if ($folder === Profiles_MessageHelper::FOLDER_NAME_SENT) { ?>
                                <option value="archive"><%= xg_html('ARCHIVE') %></option>
                            <?php } ?>
                            <?php if ($folder !== Profiles_MessageHelper::FOLDER_NAME_SENT && $folder !== Profiles_MessageHelper::FOLDER_NAME_ARCHIVE) { ?>
                                <option value="blockSender" _confirm="true"><%= xg_html('BLOCK_SENDER') %></option>
                                <?php if ($folder !== Profiles_MessageHelper::FOLDER_NAME_ALERTS) { ?>
                                    <option value="archive"><%= xg_html('ARCHIVE') %></option>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($folder === Profiles_MessageHelper::FOLDER_NAME_ARCHIVE) { ?>
                                <option value="inbox"><%= xg_html('MOVE_TO_INBOX') %></option>
                            <?php } ?>
                            <option value="delete" _confirm="true"><%= xg_html('DELETE') %></option>
                        </select>
                        <span id="xg_spinner" style="visibility: hidden"><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/icon/spinner.gif') %>" /> <%= xg_html('WORKING_HELLIP') %></span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $numMessages = count($messages);
                    foreach ($messages as $i => $message) {
                        $showUrl = xnhtmlentities($this->_buildUrl('message', 'show', array('id' => $message->id, 'folder' => $folder, 'page' => $page)));
                        $otherPartyScreenNameOrEmail = $otherParties[$message->id];
                        $this->renderPartial('fragment_messageHeader', array('message' => $message,
                                                                         'showUrl' => $showUrl,
                                                                         'otherPartyProfile' => $profiles[$otherPartyScreenNameOrEmail],
                                                                         'otherPartyScreenNameOrEmail' => $otherPartyScreenNameOrEmail,
                                                                         'otherPartyHasProfile' => $profiles[$otherPartyScreenNameOrEmail],
                                                                         'numRecipients' => count($message->recipients),
                                                                         'folder' => $folder,
                                                                         'selected' => array_key_exists($message->id, $selected),
                                                                         'last' => $i === $numMessages - 1));
                } ?>
            </tbody>
        </table>
    </form><!--/xg_mailbox-->
    <?php XG_App::includeFileOnce('/lib/XG_PaginationHelper.php'); ?>
    <%= XG_PaginationHelper::outputPagination($totalMessages, $pageSize, 'smallpagination xg_mailbox_pagination', $this->_buildUrl('message', 'list' . $folder, array('page' => $page))); %>
<?php } else { ?>
    <p><%= ($folder === Profiles_MessageHelper::FOLDER_NAME_ALERTS) ? xg_html('ALERTS_FOLDER_EXPLANATION') : xg_html('NO_MESSAGES_IN_' . strtoupper($folder)); /** @non-mb */ %></p>
<?php } ?>
