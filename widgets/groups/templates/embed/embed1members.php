<?php
if ($this->profiles) { ?>
    <div class="xg_module">
        <div class="xg_module_head">
            <h2><%= xg_html('MEMBERS_N', $this->groupMemberCount) %></h2>
        </div>
        <div class="xg_module_body vcard-48grid">
        <?php foreach ($this->profiles as $profile) { ?>
               <%= xg_avatar($profile, 48) %>
        <?php } ?>
        </div>
        <?php if(count($this->profiles) > 0 && ($this->inviteUrl || $this->viewAllUrl)) { ?>
        <div class="xg_module_foot">
            <ul>
                <?php
                if ($this->inviteUrl) { ?>
                    <li class="left"><a href="<%= xnhtmlentities($this->inviteUrl) %>" class="desc add"><%= xg_html('INVITE_MORE') %></a></li>
                <?php
                }
                if ($this->viewAllUrl) { ?>
                    <li class="right"><a href="<%= xnhtmlentities($this->viewAllUrl) %>"><%= xg_html('VIEW_ALL') %></a></li>
                <?php
                } ?>
            </ul>
        </div>
        <?php } ?>
    </div>


<?php
} ?>
