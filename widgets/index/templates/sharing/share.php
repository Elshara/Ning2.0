<?php xg_header(null, $this->pageTitle) ?>
<?php XG_App::ningLoaderRequire('xg.index.invitation.pageLayout'); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_2col first-child" style="margin-left:235px;">
			<%= xg_headline($this->pageTitle)%>
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
                        <h3><%= xg_html('YOUR_MESSAGE_HAS_BEEN') %></h3>
                        <p class="last-child"><%= xg_html('YOU_CAN_SHARE_THIS', 'href="' . xnhtmlentities(Index_SharingHelper::url($this->itemInfo)) . '"') %></p>
                    </div>
                </div>
            <?php
            } ?>
            <div class="xg_module module_invite">
                <div class="xg_module_body pad">
                    <div class="share_preview">
                        <?php
                        if ($this->itemInfo['display_thumb']) { ?>
                            <div class="share_thumbnail"><img width="110" src="<%= $this->itemInfo['display_thumb'] %>" /></div>
                        <?php
                        } ?>
                        <div class="share_description"><%= $this->itemInfo['description'] %></div>
                    </div>
                </div>
                <?php W_Cache::getWidget('main')->dispatch('invitation', 'chooseInvitationMethod', array($this->invitationArgs)); ?>
            </div>
        </div>
    </div>
</div>
<?php xg_footer() ?>
