<?php if (Groups_SecurityHelper::currentUserCanSeeAdminControls($this->group)) { ?>
<div class="xg_module adminbox">
    <div class="xg_module_head">
        <h2><%= xg_html('ADMIN_OPTIONS') %></h2>
    </div>
    <div class="xg_module_body">
        <ul class="nobullets last-child">
            <?php if (XG_SecurityHelper::userIsAdmin() && !Group::userIsMember($this->group)) {
                 XG_App::ningLoaderRequire('xg.shared.PostLink'); ?>
            <li><a class="desc add" dojoType="PostLink" href="#" _url="<%= xnhtmlentities($this->_buildUrl('group','join', array('id' => $this->group->id))) %>"><%= xg_html('JOIN_GROUP', xnhtmlentities($this->group->title)) %></a></li>
            <?php } ?>
            <?php if (XG_PromotionHelper::currentUserCanPromote($this->group)) {
                XG_App::ningLoaderRequire('xg.index.actionicons');?>
                <li><%= W_Cache::getWidget('main')->dispatch('promotion', 'link', array($this->group)) %></li>
            <?php } ?>
            <?php if (Groups_SecurityHelper::currentUserCanEditGroup($this->group)) { ?>
                <li><a href="<%= xnhtmlentities($this->_buildUrl('group','edit', array('id' => $this->group->id))) %>" class="desc edit"><%= xg_html('EDIT_GROUP_INFO') %></a></li>
            <?php } ?>
            <?php if (Groups_SecurityHelper::currentUserCanEditMemberships($this->group)) { ?>
                <li><a href="<%= xnhtmlentities($this->_buildUrl('user','edit', array('groupId' => $this->group->id))) %>" class="desc friends"><%= xg_html('MANAGE_GROUP_MEMBERS') %></a></li>
            <?php } ?>
            <?php if (Groups_SecurityHelper::currentUserCanDeleteGroup($this->group)) {
                XG_App::ningLoaderRequire('xg.shared.PostLink'); ?>
                <li><a dojoType="PostLink" _confirmTitle="<%= xg_html('DELETE_GROUP') %>" _confirmOkButtonText="<%= xg_html('DELETE') %>" _confirmQuestion="<%= xg_html('DELETE_GROUP_Q') %>" _url="<%= xnhtmlentities($this->_buildUrl('group','delete', array('id' => $this->group->id))) %>" style="display:none" href="#" class="desc delete"><%= xg_html('DELETE_GROUP') %></a></li>
            <?php } ?>
        </ul>
    </div>
</div>
<?php } ?>
<?php if (Group::userIsMember($this->group) || XG_SecurityHelper::userIsAdmin()) {
	$messageParts = array();
	if (XG_SecurityHelper::userIsAdmin()) {
		$messageParts[xg_html('NETWORK_NAME')] = XN_Application::load()->name;
	}
	if ($this->group->contributorName == XN_Profile::current()->screenName) {
		$messageParts[xg_html('GROUP_TITLE')] = $this->group->title;
	}
?>
<div class="xg_module">
    <div class="xg_module_body">
        <ul class="nobullets last-child">
            <?php if (Groups_SecurityHelper::currentUserCanSendMessageToGroup($this->group)) {
                XG_App::ningLoaderRequire('xg.index.bulk','xg.shared.SpamWarning'); ?>
                <li><a dojoType="BroadcastMessageLink"
                    title="<%= xg_html('SEND_BROADCAST_MESSAGE') %>"
                    _url="<%= $this->_buildUrl('bulk','broadcast',array('groupId' => xnhtmlentities($this->group->id), 'xn_out' => 'json')) %>"
                    _spamUrl="<%=xnhtmlentities(W_Cache::getWidget('main')->buildUrl('invitation','checkMessageForSpam'))%>"
					_spamMessageParts="<%=xnhtmlentities(json_encode($messageParts))%>"
                    _successTitle="<%= xg_html('MESSAGE_SENT') %>"
                    _successMessage="<%= xg_html('YOUR_MESSAGE_HAS_BEEN_SENT_GROUP') %>"
                    _progressMessage="<%= xg_html('YOUR_MESSAGE_IS_BEING_SENT') %>"
                    href="javascript:void(0)" class="desc sendmessage">
                    <%= xg_html('SEND_MESSAGE_TO_GROUP') %></a></li>
            <?php } ?>
            <%= xg_follow_unfollow_links($this->group) %>
            <?php if (Groups_SecurityHelper::currentUserCanLeaveGroup($this->group)) {
                XG_App::ningLoaderRequire('xg.shared.PostLink'); ?>
                <li><a dojoType="PostLink" class="desc leave" _url="<%= xnhtmlentities($this->_buildUrl('group','leave', array('id' => $this->group->id))) %>" style="display:none" href="#"><%= xg_html('LEAVE_GROUP') %></a></li>
            <?php } ?>
        </ul>
    </div>
</div>
<?php } ?>