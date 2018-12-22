<?php xg_header(null, $title = xg_text('INVITE_FRIENDS'), null, array('hideNavigation' => true)) ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_2col first-child" style="margin-left:235px;">
			<%= xg_headline($title)%>
            <div class="xg_module" dojoType="ImportMonitor">
                <div class="xg_module_body pad">
                    <h3><%= xg_html('IMPORTING_ADDRESS_BOOK') %></h3>
                    <img class="left" style="margin-right:10px" src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/spinner.gif')) %>" alt="" width="43" height="43" />
                    <p class="last-child"><%= xg_html('LEAVE_WINDOW_WHILE_GET') %></p>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="jobId" value="<%= xnhtmlentities($this->jobId) %>" />
<input type="hidden" id="target" value="<%= xnhtmlentities($this->target) %>" />
<?php XG_App::ningLoaderRequire('xg.index.invitation.waitForImport'); ?>
<?php xg_footer() ?>
