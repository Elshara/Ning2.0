                    <?php
                        $isInbox = $this->folder === Profiles_MessageHelper::FOLDER_NAME_INBOX;
                        $isAlerts = $this->folder === Profiles_MessageHelper::FOLDER_NAME_ALERTS;
                        $isSent = $this->folder === Profiles_MessageHelper::FOLDER_NAME_SENT;
                        $isArchive = $this->folder === Profiles_MessageHelper::FOLDER_NAME_ARCHIVE;
                        $page = max(1, intval($this->page));
                    ?>
                    <ul class="page_tabs xg_lightborder">
                        <li id="xg_inbox_tab" class="first-child<%= $isInbox ? ' this' : '' %>">
                            <?php if (! $isInbox || ! $this->listView) { ?><a<%= ! $this->listView && $isInbox ? ' class="xg_lightborder"' : '' %> href="<%= xnhtmlentities($this->_buildUrl('message', 'listInbox', array('page' => $isInbox ? $page : 1))) %>"><?php } else { ?><span class="xg_tabs xg_lightborder"><?php } ?>
                                <span class="xj_count_unreadmessages xj_count_unreadmessages_0"<%= intval($this->numUnreadMsgs) > 0 ? ' style="display:none;"' : '' %>><%= xg_html('INBOX_TAB_TEXT') %></span>
                                <span class="xj_count_unreadmessages xj_count_unreadmessages_n"<%= intval($this->numUnreadMsgs) > 0 ? '' : ' style="display:none;"' %>><%= xg_html('INBOX_N_TAB_TEXT', 'class="xj_count"', $this->numUnreadMsgs) %></span>
                            <?php if (! $isInbox || ! $this->listView) { ?></a><?php } else { ?></span><?php } ?>
                        </li>
                        <?php if (XG_App::openSocialEnabled()) { ?>
                            <li id="xg_alerts_tab"<%= $isAlerts ? ' class="this"' : '' %>>
                                <?php if (! $isAlerts || ! $this->listView) { ?><a href="<%= xnhtmlentities($this->_buildUrl('message', 'listAlerts', array('page' => $isAlerts ? $page : 1))) %>"><?php } else { ?><span class="xg_tabs xg_lightborder"><?php } ?>
                                    <span class="xj_count_unreadalerts xj_count_unreadalerts_0"<%= intval($this->numUnreadAlertsMsgs) > 0 ? ' style="display:none;"' : '' %>><%= xg_html('ALERTS_TAB_TEXT') %></span>
                                    <span class="xj_count_unreadalerts xj_count_unreadalerts_n"<%= intval($this->numUnreadAlertsMsgs) > 0 ? '' : ' style="display:none;"' %>><%= xg_html('ALERTS_N_TAB_TEXT', 'class="xj_count"', $this->numUnreadAlertsMsgs) %></span>
                                <?php if (! $isAlerts || ! $this->listView) { ?></a><?php } else { ?></span><?php } ?>
                            </li>
                        <?php } ?>
                        <li id="xg_sent_tab"<%= $isSent ? ' class="this"' : '' %>>
                            <?php if (! $isSent || ! $this->listView) { ?><a<%= ! $this->listView && $isSent ? ' class="xg_lightborder"' : '' %> href="<%= xnhtmlentities($this->_buildUrl('message', 'listSent', array('page' => $isSent ? $page : 1))) %>"><?php } else { ?><span class="xg_tabs xg_lightborder"><?php } ?>
                                <%= xg_html('SENT') %>
                            <?php if (! $isSent || ! $this->listView) { ?></a><?php } else { ?></span><?php } ?>
                        </li>
                        <li id="xg_archive_tab"<%= $isArchive ? ' class="this"' : '' %>>
                            <?php if (! $isArchive || ! $this->listView) { ?><a<%= ! $this->listView && $isArchive ? ' class="xg_lightborder"' : '' %> href="<%= xnhtmlentities($this->_buildUrl('message', 'listArchive', array('page' => $isArchive ? $page : 1))) %>"><?php } else { ?><span class="xg_tabs xg_lightborder"><?php } ?>
                                <%= xg_html('ARCHIVE') %>
                            <?php if (! $isArchive || ! $this->listView) { ?></a><?php } else { ?></span><?php } ?>
                        </li>
                        <li id="xg_compose_tab"><a href="<%= xnhtmlentities($this->_buildUrl('message', 'new', array('folder' => $this->folder, 'page' => $this->page))) %>" class="desc add"><%= xg_html('COMPOSE') %></a></li>
                    </ul>
