<?php xg_header('inbox', $this->title);
XG_App::ningLoaderRequire('xg.profiles.message.compose'); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
        <div id="xj_status" class="xg_module"<%= $this->showStatus ? '' : ' style="display: none;"'%>>
            <div id="xj_status_body" class="xg_module_body<%= $this->isError ? ' errordesc' : ' success' %>">
                <p id="xj_status_message" class="last-child"><%= $this->statusMessage %></p>
            </div>
        </div>
        <?php if ($this->allowRecipientChange) { ?>
            <%= $this->allFriends ? xg_headline(xg_text('SEND_MESSAGE_TO_FRIENDS')) : xg_headline(xg_text('MESSAGES')) %>
        <?php } ?>
        <div class="xg_module">
            <div class="xg_module_body pad">
                <?php if ($this->allowRecipientChange && ! $this->allFriends) { ?>
                    <%= $this->renderPartial('fragment_tabNavigation') %>
                <?php } ?>
                <%= $this->renderPartial('fragment_composeDetail') %>
                <?php /* TODO: Allow the form to be submitted without requiring JavaScript [Jon Aquino 2008-09-19] */ ?>
                <form id="xg_mail_compose" method="post" _spamMessageParts="<%=xnhtmlentities(json_encode($this->messageParts))%>" _spamUrl="<%=W_Cache::getWidget('main')->buildUrl('invitation', 'checkMessageForSpam')%>" onsubmit="return false;" action="<%= xnhtmlentities($this->_buildUrl('message', 'send', array_merge($_GET, array('xn_out' => 'json', 'action' => $this->action)))) %>" _maxRecipients="<%= $this->maxRecipients %>" _maxMessageLength="<%= Profiles_MessageHelper::MAX_MESSAGE_LENGTH %>">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <fieldset class="nolegend">
                        <%= $this->renderPartial('fragment_composeRecipients', 'message') %>
                        <?php if ($this->action === Profiles_MessageHelper::COMPOSE_NEW) { ?>
                            <dl>
                                <dt class="align-right"><label for="subject" id="xj_label_subject"><%= xg_html('SUBJECT') %></label></dt>
                                <dd><input type="text" class="textfield" id="subject" name="subject" maxlength="<%= Profiles_MessageHelper::MAX_SUBJECT_LENGTH %>" /></dd>
                            </dl>
                        <?php } ?>
                        <dl>
                                <dt class="align-right"><label for="message" id="xj_label_message"><%= xg_html('MESSAGE') %></label></dt>
                                <dd><textarea rows="15" cols="auto" id="message" name="message"></textarea></dd>
                        </dl>
                        <p class="buttongroup"><input type="button" id="xj_compose_submit" class="button" value="Send" /></p>
                    </fieldset>
                </form>
                <!-- message quoting, if replying or forwarding -->
                <?php if ($this->message) { ?>
                    <p>
                        <%= Profiles_MessageHelper::formatMessageForDisplay($this->message->body, TRUE, $this->action === Profiles_MessageHelper::COMPOSE_REPLY ? 1 : 0) %>
                    </p>
                <?php } ?>
                <!-- end message quoting -->
            </div>
        </div><!--/xg_module-->
    </div><!--/xg_span-16-->
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div><!--/xg_span-4-->
</div><!--/xg_body-->
<?php if (Profiles_MessageHelper::MAX_RECIPIENTS_TO_DISPLAY < count($this->recipients)) {
    XG_App::ningLoaderRequire('xg.profiles.message.show');
}
xg_footer(); ?>
