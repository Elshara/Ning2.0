<div class="xg_module">
    <div class="xg_module_head">
        <h2><%= xg_html('INFORMATION') %></h2>
    </div>
    <div class="xg_module_body nopad">
        <div class="xg_column xg_span-4">
            <div class="pad5">
                <img src="<%= xnhtmlentities(Group::iconUrl($this->group, 171)) %>" width="171" height="171" alt="<%= xnhtmlentities($this->group->title) %>" class="groupicon"/>
            </div>
        </div>
        <div class="xg_column xg_span-8 last-child">
            <div class="pad5">
            <?php if ($this->group->description) { ?>
                <p><%= xnhtmlentities($this->group->description) %></p>
            <?php } ?>
                <p>
                    <?php if ($this->group->my->externalWebsiteUrl) { ?>
                        <%= xg_html('WEBSITE_COLON') %>
                        <a href="<%= xnhtmlentities($this->group->my->externalWebsiteUrl) %>"><%= xnhtmlentities($this->group->my->externalWebsiteUrl) %></a><br/>
                    <?php } ?>
                    <?php if ($this->group->my->location) { ?>
                        <%= xg_html('LOCATION_COLON') %>
                        <a href="<%= xnhtmlentities($this->_buildUrl('group', 'listByLocation', array('location' => $this->group->my->location))) %>"><%= xnhtmlentities($this->group->my->location) %></a><br/>
                    <?php } ?>
                        <%= xg_html('MEMBERS_COLON') %>
                            <strong><%= xnhtmlentities($this->group->my->memberCount) %></strong><br/>
                    <?php if ($this->group->my->lastActivityOn) { ?>
                        <%= xg_html('LATEST_ACTIVITY_COLON_TIME', '<strong>' . xg_elapsed_time($this->group->my->lastActivityOn) . '</strong>') %>
                    <?php } ?>
                </p>
            </div>
        </div>
    </div>
</div>
