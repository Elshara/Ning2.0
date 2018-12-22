<?php if (XG_SecurityHelper::userIsAdmin() || $this->chatStatus !='disabled') { ?>
<?php XG_App::ningLoaderRequire('xg.chat.ChatEmbed', 'xg.shared.util');
if (XN_Profile::current()->isLoggedIn()) {
    XG_App::ningLoaderRequire('xg.chat.onlinestatus');
} ?>
<div 
<?php if (XG_SecurityHelper::userIsAdmin()) { ?>
    class="xg_module module_chat" dojoType="ChatEmbed" 
    _url="<%= $this->url %>"
    _chatEnabled="<%=$this->chatStatus !='disabled' ? 1 : 0%>"
<?php } ?>
>
    <div class="xg_module_head">
        <small class="right">
            <select id="xj_online_status" _cookieName="<%= Chat_ConnectionHelper::CHAT_STATUS_COOKIE_NAME %>" _startChatUrl="<%= $this->startChatUrl %>" class="xg_lightfont disabled" style="display:none;<%= XG_SecurityHelper::userIsAdmin() ? 'margin-right:15px !important;' : '' %>" disabled="disabled"><%= Chat_ConnectionHelper::getStatusOptions($this->userOnlineStatus) %></select>
            <a class="smalldelete" style="display:none" id="xj_chat_disable" href="#"><%= xg_html('CLOSE') %></a></small>
        <h2><%= xnhtmlentities($this->title) %></h2>
    </div>
    <div class="xg_module_body body_<%= $this->moduleLocation %>">
        <div id="xj_chat">
            <?php if($this->chatStatus != 'disabled') { $this->renderPartial('chat', 'embed'); } ?>
        </div> 
        <p>
            <a class="desc add" style="display: none" id="xj_chat_enable" href="#"><%=xg_text('ENABLE_CHAT')%></a>
        </p>
    </div>
</div>
<?php } ?>
