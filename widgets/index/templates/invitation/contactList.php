<div id="contact_list_module" class="xg_module"
        _createWithContactListUrl="<%= xnhtmlentities($this->createWithContactListUrl) %>"
        _spamMessageParts="<%=xnhtmlentities(json_encode($this->messageParts))%>"
        _spamUrl="<%=$this->_buildUrl('invitation','checkMessageForSpam')%>"
        _cancelUrl="<%= xnhtmlentities($this->cancelUrl) %>"
        _inviteOrShare="<%= xnhtmlentities($this->inviteOrShare) %>">
    <div id="loading_message" class="xg_module_body pad">
        <img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/spinner.gif')) %>" alt="" class="left" style="margin-right:7px" width="20" height="20"/>
        <p style="margin-left:27px">
            <%= xg_html('WE_ARE_LOADING_ADDRESSES') %>
        </p>
    </div>
    <form id="search_form" style="display:none" class="xg_module_body pad" action="#" onsubmit="return false;">
        <p id="search_controls" class="last-child">
            <small>
                <label for="search_friends"><%= xnhtmlentities($this->searchLabelText) %></label>
                <input type="text" class="textfield" name="q" />
                <input type="submit" class="button" value="<%= xg_html('SEARCH') %>" />
            </small>
        </p>
        <p id="search_description" class="last-child" style="display:none">
        </p>
    </form>
    <div id="contact_section" style="display:none" class="xg_module_body pad">
        <p class="right last-child tablescrollpadding"><small id="friend_count"></small></p>
        <p class="last-child tablescroll"><label class="toggle"><input id="toggle_all_checkbox" type="checkbox" checked="checked" /><%= xg_html('SELECT_ALL_NONE') %></label></p>
        <div id="table_container" class="tablescroll">
            <table class="members">
            </table>
        </div>
        <p class="clear" style="margin:1.5em 0 0">
            <a href="#" id="invite_button" class="button"><strong><%= xnhtmlentities($this->submitButtonText) %></strong></a>
            <a href="#" id="cancel_button" class="button"><%= xg_html('CANCEL') %></a>
        </p>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.index.invitation.contactList','xg.shared.SpamWarning'); ?>
<input type="hidden" id="contact_list" value="<%= xnhtmlentities($this->contactListJson) %>" />
