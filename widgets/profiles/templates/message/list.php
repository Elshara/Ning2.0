<?php xg_header('inbox', $this->title); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
        <div class="xg_module" id="xj_error_container"<%= $this->status ? '' : ' style="display: none;"'%>>
                <div class="xg_module_body success" id="xj_error_inner_container">
                    <p class="last-child" id="xj_error_span"><%= $this->status ? xg_html('STATUS_MESSAGE_' . strtoupper($this->status)) /** @non-mb */ : '' %></p>
                </div>
            </div>
        <%= xg_headline(xg_text('MESSAGES')) %>
        <div class="xg_module">
            <div class="xg_module_body pad">
                <?php $this->renderPartial('fragment_tabNavigation'); ?>
                <div id="xj_list_body">
                    <?php $this->renderPartial('fragment_listBody', 'message', array('messages' => $this->messages, 'errorMessage' => $this->errorMessage, 'profiles' => $this->profiles, 'otherParties' => $this->otherParties, 'selected' => array(), 'folder' => $this->folder, 'page' => $this->page, 'pageSize' => $this->pageSize, 'totalMessages' => $this->totalMessages)); ?>
                </div><!--/xj_list_body-->
            </div><!--/xg_module_body-->
        </div><!--/xg_module-->
    </div><!--/xg_span-16-->
    <div class="xg_column xg_span-4 last-child">
            <?php xg_sidebar($this); ?>
    </div><!--/xg_span-4-->
</div><!--/xg_body-->
<?php XG_App::ningLoaderRequire('xg.profiles.message.list'); ?>
<?php xg_footer(); ?>
