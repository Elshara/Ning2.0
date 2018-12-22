<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('CHAT'));
if (XN_Profile::current()->isLoggedIn()) {
    XG_App::ningLoaderRequire('xg.chat.onlinestatus');
} ?>
<div id="xg_body">
    <div class="xg_column xg_span-15 xg_append-1">
        <div class="xg_module module_chat">
            <div class="xg_module_head">
                <small class="right">
                    <select id="xj_online_status" style="display:none;" _cookieName="<%= Chat_ConnectionHelper::CHAT_STATUS_COOKIE_NAME %>" _startChatUrl="<%= $this->startChatUrl %>" class="xg_lightfont disabled" disabled="disabled"><%= Chat_ConnectionHelper::getStatusOptions($this->userOnlineStatus) %></select>
                </small>

                <h2><%= xg_html('CHAT') %></h2>
            </div>
            <div class="xg_module_body">
              <%= $this->renderPartial('chat', 'embed'); %>
            </div>
        </div>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this, true, false, true); ?>
    </div>
</div>
<?php xg_footer(); ?>
