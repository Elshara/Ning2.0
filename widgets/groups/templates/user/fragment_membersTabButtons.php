<?php
/**
 * Buttons for the Members tab.
 *
 * @param XN_Content|W_Content  the Group
 */
if (Groups_SecurityHelper::currentUserCanSeePromoteToAdministratorButton($group)) { ?>
    <a href="javascript:xg.index.membership.list.submitWithOp('promoteToAdministrator')" class="button"><%= xg_html('PROMOTE_TO_ADMINISTRATOR') %></a>
<?php
}
if (Groups_SecurityHelper::currentUserCanSeeDemoteFromAdministratorButton($group)) { ?>
    <a href="javascript:xg.index.membership.list.submitWithOp('demoteFromAdministrator')" class="button"><%= xg_html('DEMOTE_FROM_ADMINISTRATOR') %></a>
<?php
} ?>
<?php XG_App::ningLoaderRequire('xg.index.bulk'); ?>
<a id="ban_button" dojoType="BulkActionLinkWithCheckbox"
        title="<%= xg_html('BAN_FROM_GROUP') %>"
        _url="<%= xnhtmlentities($this->_buildUrl('user','ban', array('groupId' => $group->id, 'xn_out' => 'json'))) %>"
        _verb="<%= xg_html('BAN') %>"
        _confirmMessage="<%= xg_html('ARE_YOU_SURE_BAN_MEMBER_FROM_GROUP') %>"
        _checkboxMessage = "<%= xg_html('ALSO_DELETE_FORUM_POSTINGS') %>"
        _checkboxUrl="<%= xnhtmlentities($this->_buildUrl('bulk', 'banAndRemoveContent', array('groupId' => $group->id, 'limit' => 20, 'xn_out' => 'json'))) %>"
        _progressTitle="<%= xg_html('REMOVING_MEMBERS') %>"
        _ensureCheckboxClicked = 'true'
        _formId = 'xg_member_form'
        _checkboxSelectMessage = "<%= xg_html('PLEASE_SELECT_A_MEMBER') %>"
        _successUrl="<%= xnhtmlentities(XG_HttpHelper::currentUrl()) %>" href="#" class="button">
        <%= xg_html('BAN_FROM_GROUP') %></a>

