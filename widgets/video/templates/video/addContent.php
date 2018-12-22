<?php XG_App::ningLoaderRequire('xg.video.video.TabTrigger'); ?>
<div id="add-<%= $this->prefix %>" class="add_section">
    <h3><%= xg_html('ADD_YOUR_FIRST_VIDEO') %></h3>
    <img class="feature_logo" alt="" src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/features/video.gif')) %>" />
    <fieldset class="nolegend">
        <ul class="page_tabs">
            <li class="this" id="<%= $this->prefix %>_upload_tab"><span class="xg_tabs"><%= xg_html('ADD_A_VIDEO') %></span></li>
            <li id="<%= $this->prefix %>_embed_tab"><a dojoType="TabTrigger" _tabId="<%= $this->prefix %>_embed_tab" _otherTabId="<%= $this->prefix %>_upload_tab" _hiddenInputId="<%= $this->prefix %>_selected_tab" href="#"><%= xg_html('USE_HTML_EMBED_CODE') %></a></li>
        </ul>
        <input type="hidden" id="<%= $this->prefix %>_selected_tab" name="<%= $this->prefix %>[selectedTab]" value="<%= $this->prefix %>_upload_tab" />
        <p class="upload" id="<%= $this->prefix %>_upload_section">
            <label for="<%= $this->prefix %>_file"><%= xg_html('UPLOAD_A_VIDEO_FROM') %></label><br />
            <input id="<%= $this->prefix %>_file" name="<%= $this->prefix %>_file" type="file" class="file" /><br />
            <small><%= xg_html('WE_SUPPORT_MOV') %></small>
        </p>
        <p class="upload" id="<%= $this->prefix %>_embed_section" style="display:none">
            <label for="<%= $this->prefix %>_embed"><%= xg_html('PASTE_IN_EMBED') %></label><br />
            <textarea id="<%= $this->prefix %>_embed" name="<%= $this->prefix %>[embedCode]" rows="3" cols="30"></textarea><br />
            <small><%= xg_html('NEED_HELP_HERE_ARE', 'href="http://youtube.com/t/help_cat11"', 'href="http://video.google.com/support/bin/answer.py?answer=35093&query=embed&topic=0&type=f"') %></small>
        </p>
        <p>
            <label for="<%= $this->prefix %>_title"><%= xg_html('ADD_TITLE_AND_DESCRIPTION') %></label><br />
            <input id="<%= $this->prefix %>_title" name="<%= $this->prefix %>[title]" type="text" class="textfield" value="<%= xg_html('DEFAULT_TITLE') %>" maxlength="200" /><br />
            <textarea id="<%= $this->prefix %>_description" name="<%= $this->prefix %>[description]" rows="3" cols="30"><%= xg_html('DEFAULT_DESCRIPTION') %></textarea>
        </p>
    </fieldset>
</div>

