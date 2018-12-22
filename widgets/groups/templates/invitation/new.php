<?php xg_header('invite', $title = xg_text('INVITE_TO_GROUP', XG_GroupHelper::currentGroup()->title)); ?>
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
                        <p class="last-child"><%= xg_html('WANT_TO_INVITE_MORE_FRIENDS') %> <a href="<%= xnhtmlentities($this->_buildUrl('group', 'show', array('id' => $this->group->id))) %>"><%= xg_html('RETURN_TO_GROUPNAME', xnhtmlentities($this->group->title)) %></a></p>
                    </div>
                </div>
            <?php
            } ?>
            <div class="xg_module module_invite">
                <div class="xg_module_body pad">
                    <div class="share_preview">
                        <div class="share_thumbnail">
                        <img src="<%= xnhtmlentities(Group::iconUrl($this->group, 110)) %>" width="110" height="110" alt="<%= xnhtmlentities($this->group->title) %>"/>
                        </div>
                        <div class="share_description">
                        <p class="last-child">
                        <%= xnhtmlentities($this->group->description) %>
                        <br/>
                        <?php if ($this->creatingGroup) { ?>
                            <a href="<%= xnhtmlentities($this->_buildUrl('group', 'show', array('id' => $this->group->id))) %>"><%= xg_html('SKIP') %></a>
                        <?php } ?>
                        </p>
                        </div>
                    </div>
                </div>
                <?php W_Cache::getWidget('main')->dispatch('invitation', 'chooseInvitationMethod', array($this->invitationArgs)); ?>
            </div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
