<?php
if (! $this->_user->isLoggedIn()) { ?>
    <div class="xg_module" id="xg_module_account">
		<div class="xg_module_body xg_signup xg_lightborder">
			<p><%=xg_html('WELCOME_TO_BR_X', qh(XN_Application::load()->name))%>
			<p class="last-child">
				<big><strong><a href="<%= xnhtmlentities(XG_AuthorizationHelper::signUpUrl()) %>"><%= xg_html('SIGN_UP') %></a></strong></big><br>
            	<%= xg_html('OR_SIGN_IN', 'href="' . xnhtmlentities(XG_AuthorizationHelper::signInUrl()) . '" style="white-space:nowrap"') %>
			</p>
        </div>
    </div>
<?php
} elseif (!User::isMember($this->_user)) { ?>
    <div class="xg_module">
        <div class="xg_module_body">
            <?php
            XG_App::ningLoaderRequire('xg.shared.PostLink');
            if (User::isPending($this->_user)) { ?>
                <p class="xg_signout"><a href="#" dojoType="PostLink" _url="<%= xnhtmlentities(XG_AuthorizationHelper::signOutUrl()) %>"><%= xg_html('SIGN_OUT_TITLE') %></a></p>
                <h3><%= xg_html('HELLO') %> <%= xnhtmlentities(xg_username($this->_user)) %></h3>
                <p><%= xg_html('YOUR_MEMBERSHIP_TO_X_IS_PENDING_APPROVAL', ucfirst(XN_Application::load()->name)) %></p>
            <?php
            } else { ?>
                <p style="margin-bottom:.5em">
                    <span style="font-size:1.1em"><%= xg_html('HELLO') %> <%= xnhtmlentities(xg_username($this->_user)) %></span>
                    <small class="nobr">(<a href="#" dojoType="PostLink" _url="<%= xnhtmlentities(XG_AuthorizationHelper::signOutUrl()) %>" class="flogout"><%= xg_html('SIGN_OUT') %></a>)</small>
                </p>
                <?php
                if ($this->invitation) {
                    // Won't get here anymore when BAZ-4530 is fixed [Jon Aquino 2007-09-24]
                    $this->renderPartial('_sidebarInvite');
                } ?>
                <p class="clear" style="font-size:1.7em; font-weight:bold; margin: 0.2em 0;"><a id="xg_module_account_join3" href="<%= xnhtmlentities(XG_AuthorizationHelper::signUpUrl()) %>" style=""><%= xg_html('JOIN_X_NOW', ucfirst(XN_Application::load()->name)) %></a></p>
            <?php
            } /* pending or not? */ ?>
        </div>
    </div>
<?php
} else {
?>
    <div class="xg_module" id="xg_module_account">
        <div class="xg_module_body account-links">
            <?php XG_App::ningLoaderRequire('xg.shared.PostLink') ?>
            <p>
              <strong><%= xnhtmlentities(xg_username($this->_user)) %></strong>
              <br/>
              <small><a href="#" dojoType="PostLink" _url="<%= xnhtmlentities(XG_AuthorizationHelper::signOutUrl()) %>"><%= xg_html('SIGN_OUT_TITLE') %></a></small>
            </p>
            <ul>
                <li>
                    <a href="<%= W_Cache::getWidget('profiles')->buildUrl('message', 'listInbox') %>" class="desc inbox"><%= xg_html('INBOX') %></a>
		    <span class="xj_count_unreadmessages xj_count_unreadmessages_0"<%= intval($this->numUnreadMsgs) > 0 ? ' style="display:none;"' : '' %>></span>
		    <span class="xj_count_unreadmessages xj_count_unreadmessages_n"<%= intval($this->numUnreadMsgs) > 0 ? '' : ' style="display:none;"' %>><small><a href="<%= W_Cache::getWidget('profiles')->buildUrl('message', 'listInbox') %>">(<%= xg_html('N_NEW_MESSAGES', 'class="xj_count"', $this->numUnreadMsgs) %>)</a></small></span>
                </li>
                <?php if (XG_App::openSocialEnabled()) { ?>
                    <li>
                        <a href="<%= W_Cache::getWidget('profiles')->buildUrl('message', 'listAlerts') %>" class="desc alerts"><%= xg_html('ALERTS') %></a>
                        <span class="xj_count_unreadalerts xj_count_unreadalerts_0"<%= intval($this->numUnreadAlertsMsgs) > 0 ? ' style="display:none;"' : '' %>></span>
                        <span class="xj_count_unreadalerts xj_count_unreadalerts_n"<%= intval($this->numUnreadAlertsMsgs) > 0 ? '' : ' style="display:none;"' %>><small><a href="<%= W_Cache::getWidget('profiles')->buildUrl('message', 'listAlerts') %>">(<%= xg_html('N_NEW_MESSAGES', 'class="xj_count"', $this->numUnreadAlertsMsgs) %>)</a></small></span>
                    </li>
                <?php } ?>
                <li>
                    <a href="<%= xnhtmlentities(User::quickFriendsUrl($this->_user->screenName)) %>" class="desc friends"><%= xg_html('MY_FRIENDS') %></a>
                    <span class="xj_count_friendrequestsreceived xj_count_friendrequestsreceived_0"<%= intval($this->numFriendRequests) > 0 ? ' style="display:none;"' : '' %>>
                        <?php if (XG_App::canSeeInviteLinks(XN_Profile::current())) { ?>
                            <small>&#8211; <a href="/invite"><%= xg_html('INVITE') %></a></small>
                        <?php } ?>
                    </span>
                    <span class="xj_count_friendrequestsreceived xj_count_friendrequestsreceived_1"<%= intval($this->numFriendRequests) == 1 ? '' : ' style="display:none;"' %>><small><a href="<%= W_Cache::getWidget('profiles')->buildUrl('friendrequest', 'listReceived') %>">(<%= xg_html('N_FRIEND_REQUESTS', 1) %>)</a></small></span>
                    <span class="xj_count_friendrequestsreceived xj_count_friendrequestsreceived_n"<%= intval($this->numFriendRequests) > 1 ? '' : ' style="display:none;"' %>><small><a href="<%= W_Cache::getWidget('profiles')->buildUrl('friendrequest', 'listReceived') %>">(<%= xg_html('N_FRIEND_REQUESTS', $this->numFriendRequests, 'class="xj_count"') %>)</a></small></span>
                </li>
                <li><a href="<%= W_Cache::getWidget('profiles')->buildUrl('settings', 'editProfileInfo') %>" class="desc settings"><%= xg_html('MY_SETTINGS') %></a></li>
            </ul>
	</div>
        <?php W_Cache::getWidget('main')->dispatch('quickadd','bar') ?>
	<?php if (count($this->approvalLinks)) { ?>
		<div class="xg_module_body notification">
			<h3><%= xg_html('CONTENT_AWAITING_APPROVAL') %></h3>
            <ul>
				<?php foreach ($this->approvalLinks as $approvalLink) { echo $approvalLink; } ?>
			</ul>
		</div>
	<?php } ?>
    </div>
<?php
}
