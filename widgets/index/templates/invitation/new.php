<?php xg_header('invite', $title = xg_text('INVITE'));
$title = xg_text('INVITE_TO_APPNAME', XN_Application::load()->name); ?>
<?php XG_App::ningLoaderRequire('xg.index.invitation.pageLayout'); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_2col first-child" style="margin-left:235px;">
			<%= xg_headline($title)%>
            <?php
            if ($this->showNoAddressesFoundMessage) { ?>
                <div class="xg_module">
                    <div class="xg_module_body pad errordesc">
                        <h3><%= xg_html('NO_ADDRESSES_FOUND') %></h3>
                        <p class="last-child"><%= xg_html('WE_DID_NOT_FIND_ADDRESSES') %></p>
                    </div>
                </div>
            <?php
            } else if ($this->showInvitationsSentMessage) { ?>
                <div class="xg_module">
                    <div class="xg_module_body pad success">
                        <h3><%= xg_html('YOUR_INVITATIONS_HAVE_BEEN_SENT') %></h3>
                        <p class="last-child"><%= xg_html('WANT_TO_INVITE_MORE_FRIENDS') %></p>
                    </div>
                </div>
            <?php
            } ?>
            <div class="xg_module module_invite">
                <?php W_Cache::getWidget('main')->dispatch('invitation', 'chooseInvitationMethod', array($this->invitationArgs)); ?>
            </div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
