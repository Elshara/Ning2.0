<?php xg_header('inbox', $this->title); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
            <div class="xg_module"<%= $this->showStatus ? '' : ' style="display: none;"'%>>
            <div class="xg_module_body pad<%= $this->isError ? ' errordesc' : ' success' %>">
                    <p class="last-child"><%= $this->statusMessage %></p>
                </div>
            </div>
        <%= xg_headline(xg_text('MESSAGES')) %>
        <div class="xg_module">
            <div class="xg_module_body pad">
                <%= $this->renderPartial('fragment_tabNavigation'); %>
                <%= $this->renderPartial('fragment_titleDetail', 'message', array('message' => $this->message, 'profiles' => $this->profiles, 'sender' => $this->sender, 'recipientList' => $this->recipientList)) %>
                <?php
                // TODO: These if statements can be made clearer if we take a more declarative approach:
                // if ($folder === Profiles_MessageHelper::FOLDER_NAME_SENT) { $visibleElements = array('reply', 'forward', 'archive', 'delete'); }
                // and so on for all the folders. [Jon Aquino 2008-09-18]
                ?>
                <ul id="message-detail-actions" class="xg_lightborder easyclear">
                    <li><a href="<%= xnhtmlentities($this->_buildUrl('message', 'reply', array('id' => $this->message->id, 'folder' => $this->folder, 'page' => $this->page))) %>" class="desc message-reply"><%= xg_html('REPLY') %></a></li>
                    <li><a href="<%= xnhtmlentities($this->_buildUrl('message', 'reply', array('id' => $this->message->id, 'folder' => $this->folder, 'page' => $this->page, 'replyAll' => 1))) %>" class="desc reply-all"><%= xg_html('REPLY_ALL') %></a></li>
                    <li><a href="<%= xnhtmlentities($this->_buildUrl('message', 'forward', array('id' => $this->message->id, 'folder' => $this->folder, 'page' => $this->page))) %>" class="desc forward"><%= xg_html('FORWARD') %></a></li>
                    <?php if ($this->folder === "Inbox" || $this->folder === "Sent") { ?>
                        <li><a href="<%= xnhtmlentities($this->_buildUrl('message', 'archive', array('id' => $this->message->id, 'folder' => $this->folder, 'page' => $this->page))) %>" class="desc folder"><%= xg_html('ARCHIVE_MESSAGE') %></a></li>
                    <?php } else if ($this->folder === "Archive") { ?>
                        <li><a href="<%= xnhtmlentities($this->_buildUrl('message', 'moveToInbox', array('id' => $this->message->id, 'folder' => $this->folder, 'page' => $this->page))) %>" class="desc folder"><%= xg_html('MOVE_TO_INBOX2') %></a></li>
                    <?php } ?>
                    <li><a href="<%= xnhtmlentities($this->_buildUrl('message', 'delete', array('id' => $this->message->id, 'folder' => $this->folder, 'page' => $this->page))) %>" class="desc delete"><%= xg_html('DELETE_MESSAGE') %></a></li>
                    <?php if ($this->folder !== "Sent") {
                        $showBlockLink = (($this->friendStatus !== XN_Profile::BLOCKED) && !$this->isBlocked);
                        if ($showBlockLink) {
                            if (mb_strtolower($this->message->sender) !== mb_strtolower(XN_Profile::current()->screenName)) { ?>
                                <li><a href="<%= xnhtmlentities($this->_buildUrl('message', 'blockSender', array('id' => $this->message->id, 'folder' => $this->folder, 'screenName' => $this->message->sender, 'page' => $this->page))) %>" class="desc blocked"><%= xg_html('BLOCK_SENDER2') %></a></li>
                                <?php }
                            } else { ?>
                                <li><span class="desc blocked xg_lightfont"><%= xg_html('BLOCKED') %></span></li>
                            <?php }
                        } ?>
                    </ul>
                    <p class="message-body">
                        <%= Profiles_MessageHelper::formatMessageForDisplay($this->message->body, ($this->folder !== "Alerts")) %>
                        <?php if (Profiles_MessageHelper::getMaxQuotingDepth($this->message->body) > Profiles_MessageHelper::MAX_BODY_QUOTING_DEPTH) {
                            XG_App::ningLoaderRequire('xg.profiles.message.show'); ?>
                            <a id="xj_show_more" href="#" _showMoreUrl="<%= $this->_buildUrl('message', 'getRestOfMessageBody', array('id' => $this->message->id, 'xn_out' => 'json')) %>"><%= xg_html('MESSAGE_BODY_SHOW_MORE') %></a> <span id="xj_spinner" style="visibility: hidden;"><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/icon/spinner.gif') %>" /> <%= xg_html('LOADING') %></span>
                        <?php } ?>
                    </p>
                </div>
            </div><!--/xg_module-->
    </div><!--/xg_span-16-->
    <div class="xg_column xg_span-4 last-child">
            <?php xg_sidebar($this); ?>
    </div><!--/xg_span-4-->
</div><!--/xg_body-->
<?php xg_footer(); ?>
