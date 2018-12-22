<?php xg_header('manage',xg_text('MANAGE')); ?>
<?php XG_App::ningLoaderRequire('xg.index.admin.manage','xg.index.bulk','xg.shared.SpamWarning');
    // Many things should only be shown to the owner, not administrators
    $isOwner = XG_SecurityHelper::userIsOwner();
?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_4col first-child">
			<%= xg_headline(xg_text('MANAGE'))%>
	    <?php $this->renderPartial('fragment_success'); ?>
            <div id="xg_manage_promotion" class="xg_module">
                <div class="xg_module_body xg_lightborder">
                    <?php $this->renderPartial('fragment_manageSignout','_shared') ?>
                    <h3><%= xg_html('SPREAD_THE_WORD') %></h3>
                    <ul>
                        <?php
                        if (XG_App::canSeeInviteLinks($this->_user)) { ?>
                            <li class="invite-friends"><a href="/invite"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/invite-friends.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('INVITE_FRIENDS') %></a></li>
                        <?php
                        } ?>
                        <li class="broadcast-message"><a dojotype="BroadcastMessageLink"
                            title="<%= xg_html('SEND_BROADCAST_MESSAGE') %>"
                            _url="<%= xnhtmlentities($this->_buildUrl('bulk','broadcast',array('xn_out' => 'json'))) %>"
                            _spamUrl="<%=xnhtmlentities(W_Cache::getWidget('main')->buildUrl('invitation','checkMessageForSpam'))%>"
                            _spamMessageParts="<%=xnhtmlentities(json_encode(array(xg_text('NETWORK_NAME') => XN_Application::load()->name)))%>"
                            _successtitle="<%= xg_html('MESSAGE_SENT') %>"
                            _successmessage="<%= xg_html('YOUR_MESSAGE_HAS_BEEN_SENT') %>"
                            _progressmessage="<%= xg_html('YOUR_MESSAGE_IS_BEING_SENT') %>"
                           href="javascript:void(0)"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/broadcast-message.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('BROADCAST_MESSAGE') %></a></li>
                        <li class="latest-activity"><a href="<%= xnhtmlentities($this->_widget->buildUrl('activity', 'edit')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/latest-activity.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('LATEST_ACTIVITY') %></a></li>
                        <li class="badges-widgets"><a href="<%= xnhtmlentities($this->_widget->buildUrl('embeddable', 'edit')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/badges-widgets.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('BADGES_AND_WIDGETS') %></a></li>
                        <li class="facebook-promotion"><a href="<%= xnhtmlentities($this->_widget->buildUrl('facebook', 'setup')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/facebook-promotion.png'))) %>" height="32" width="32"/></span><br/><%= xg_html('FACEBOOK_EMBEDDING') %></a></li>
                    </ul>
                </div>
            </div>
            <div id="xg_manage_network" class="xg_module">
                <div class="xg_module_body xg_lightborder">
                    <h3><%= xg_html('YOUR_NETWORK') %></h3>
                    <ul>
                        <?php if ($isOwner) { ?>
                            <li class="network-information"><a href="<%= xnhtmlentities($this->_widget->buildUrl('admin', 'appProfile')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/network-information.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('NETWORK_INFORMATION') %></a></li>
                        <?php } ?>
                        <li class="features"><a href="<%= xnhtmlentities($this->_widget->buildUrl('feature', 'add')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/features.png'))) %>" height="32" width="32"/></span><br/><%= xg_html('FEATURES') %></a></li>
                        <li class="appearance"><a href="<%= xnhtmlentities($this->_widget->buildUrl('appearance', 'edit')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/appearance.png'))) %>" height="32" width="32"/></span><br/><%= xg_html('APPEARANCE') %></a></li>
                        <?php if ($this->forumManageUrl) { ?>
                            <li class="manage-forum"><a href="<%= xnhtmlentities($this->forumManageUrl) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/manage-forum.png'))) %>" height="32" width="32" /></span><br /><%= xg_html('DISCUSSION_FORUM') %></a></li>
                        <?php } ?>
                        <?php if ($this->tabManagerEnabled) { ?>
                            <li class="tab-manager"><a href="<%= xnhtmlentities($this->_widget->buildUrl('tablayout', 'edit')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/tab-manager.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('TAB_MANAGER') %></a></li>
                        <?php } ?>
                        <li class="language-editor"><a href="<%= xnhtmlentities($this->_widget->buildUrl('language', 'list')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/language-editor.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('LANGUAGE_EDITOR') %></a></li>
                        <?php if ($isOwner) { ?>
                            <li class="analytics"><a href="<%= xnhtmlentities($this->_widget->buildUrl('admin', 'tracking')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/analytics.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('ANALYTICS') %></a></li>
                            <li class="premium-services"><a href="<%= $this->premiumFeaturesUrl %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/premium-services.png')))  %>" height="32" width="32" /></span><br/><%= xg_html('PREMIUM_SERVICES') %></a></li>
                        <?php } ?>
                        <li class="flickr-importing"><a href="<%= xnhtmlentities($this->_widget->buildUrl('flickr', 'keys')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/flickr-importing.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('FLICKR_IMPORTING') %></a></li>
                    </ul>
                </div>
            </div>
            <div id="xg_manage_members" class="xg_module">
                <div class="xg_module_body xg_lightborder">
                    <h3><%= xg_html('YOUR_MEMBERS') %></h3>
                    <ul>
                        <li class="profile-questions"><a href="<%= xnhtmlentities($this->_widget->buildUrl('membership', 'questions')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/profile-questions.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('PROFILE_QUESTIONS') %></a></li>
                        <li class="members"><a href="<%= xnhtmlentities($this->_widget->buildUrl('membership', 'listMembers')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/members.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('MEMBERS') %></a></li>
                        <?php if ($isOwner) { ?>
                            <li class="privacy-content-control"><a href="<%= xnhtmlentities($this->_widget->buildUrl('privacy', 'edit')) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/privacy-content-control.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('PRIVACY_FEATURE_CONTROLS') %></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div id="xg_manage_resources" class="xg_module">
                <div class="xg_module_body xg_lightborder">
                    <h3><%= xg_html('RESOURCES') %></h3>
                    <ul>
                        <li class="create-new-network"><a href="<%= $this->gyoUrl %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/create-new-network.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('CREATE_A_NEW_NETWORK') %></a></li>
                        <li class="network-creators"><a href="http://networkcreators.ning.com/"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/network-creators.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('NETWORK_CREATORS') %></a></li>
                        <li class="help-center"><a href="http://help.ning.com/"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/help-center.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('HELP_CENTER') %></a></li>
                        <li class="developer-network"><a href="http://developer.ning.com/"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/developer-network.png'))) %>" height="32" width="32" /></span><br/><%= xg_html('DEVELOPER_NETWORK') %></a></li>
                    </ul>
                </div>
            </div>
            <?php if ($isOwner){ ?>
                <div id="xg_manage_status" class="xg_module last-child">
                    <div class="xg_module_body">
                        <ul>
                            <div id="xg_manage_online_status_container">
                                <li id="xg_manage_online_status" class="network-status network-"></li>
                            </div>
                            <li class="delete"><a href="<%= xnhtmlentities(XN_Request::deleteAppUrl(XN_Application::load())) %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/delete.png'))) %>" height="16" width="16"/></span><%= xg_html('DELETE_NETWORK') %></a></li>
                        <?php if (!$this->haveCode) { ?>
                            <li class="request-code"><a href="<%= $this->requestCodeUrl %>"><span class="png-fix"><img alt="" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/manage/request-code.png'))) %>" height="16" width="16" /></span><%= xg_html('REQUEST_SOURCE_CODE') %></a></li>
                        <?php } ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div><!--/.xg_colgroup-->
</div>
<?php xg_footer(); ?>
