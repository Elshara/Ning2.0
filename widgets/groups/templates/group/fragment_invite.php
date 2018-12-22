<?php
XG_App::ningLoaderRequire('xg.shared.PostLink');
if ($private) {
    $membersOnly = xg_html('MEMBERSHIP_BY_INVITATION_ONLY_FS');
}
?>
<div class="xg_module">
    <div class="xg_module_body notification topmsg">
        <p style="line-height: 1.8em;" class="last-child">
            <%= xg_html('USER_HAS_INVITED_YOU_TO_JOIN_GROUP', '<strong>' . xg_avatar(XG_Cache::profiles($invitation['inviter']), 48, 'left photo', 'style="margin-right:10px"') . xg_userlink(XG_Cache::profiles($invitation['inviter']), null, true), xnhtmlentities($group->title)) . '</strong>' %> <%= $membersOnly %><br/>
                <?php /* TODO: Combine the following line into one message, to make it more translatable [Jon Aquino 2008-02-05] */ ?>
                <a style="display:none" class="button" href="#" dojoType="PostLink"
                        _url="<%= xnhtmlentities($this->_buildUrl('group','join', array('id' => $group->id, 'joinGroupTarget' => $_GET['joinGroupTarget']))) %>"
                        <%= XG_JoinPromptHelper::promptAttributesForPending() %>
                        ><%= xg_html('JOIN_NOW') %></a> <%= xg_html('OR') %>
                <a style="display:none" href="#" dojoType="PostLink" _url="<%= xnhtmlentities($this->_buildUrl('invitation','delete', array('groupId' => $group->id))) %>"><%= xg_html('DECLINE') %></a>
        </p>
    </div>
</div>