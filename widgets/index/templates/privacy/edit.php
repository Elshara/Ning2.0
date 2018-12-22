<?php xg_header('manage',xg_text('PRIVACY')); ?>
<?php XG_App::ningLoaderRequire('xg.index.privacy.edit', 'xg.index.bulk', 'xg.shared.PostLink'); ?>
<script type="text/javascript">
    <?php /* Someday/maybe get rid of addOnRequire inline JavaScript  [Jon Aquino 2007-10-29] */ ?>
    xg.addOnRequire(function() {
        xg_handleLaunchBarSubmit = xg.index.privacy.edit.handleLaunchBarSubmit;
        xg.index.privacy.edit._setPrivacyUrl = '<%= $this->setPrivacyUrl %>';
        xg.index.privacy.edit._setPrivacySuccessUrl = '<%= $this->setPrivacySuccessUrl %>';
    });
</script>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline(xg_text('PRIVACY_FEATURE_CONTROLS'))%>
			<?php $this->renderPartial('fragment_success', 'admin'); ?>
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <form id="xg_privacy_form" name="xg_privacy_form" action="<%= $this->_buildUrl('privacy', 'save') %>" method="post">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <fieldset class="nolegend">
                    <?php if ($this->privacyLevelPublicChecked && $this->nonregVisibilityMessageChecked) { ?>
                        <div class="notification" style="margin-bottom:1em">
                            <p class="last-child"><%= xg_html('GRANDFATHERED_PRIVACY_SETTING') %></p>
                        </div>
                    <?php } ?>
                    <div class="left block">
                        <h3><%= xg_html('NETWORK_PRIVACY') %></h3>
                        <ul class="options">
                            <li class="indent">
                                <label>
                                    <input name="privacyLevel" id="privacyLevelPublic" type="radio" value="public" <%= $this->privacyLevelPublicChecked ? 'checked="checked" ' : '' %>class="radio" /><strong><big><%= xg_html('PUBLIC') %></big></strong><br />
                                    <%= xg_html('THIS_NETWORK_VISIBLE_TO_EVERYONE') %><br/>
                                    <%= xg_html('ANYONE_CAN_SIGN_UP_VISITORS_CAN') %>
                                </label>
                                <ul class="options">
                                    <li><label for="nonregVisibility_everything"><input type="radio" <%= $this->nonregVisibilityEverythingChecked ? 'checked="checked" ' : '' %><%= $this->privacyLevelPublicChecked ? '' : ' disabled="disabled"' %>class="radio" id="nonregVisibility_everything" value="everything" name="nonregVisibility"/><%= xg_html('SEE_EVERYTHING') %></label></li>
                                    <li><label for="nonregVisibility_homepage"><input type="radio" <%= $this->nonregVisibilityHomepageChecked ? 'checked="checked" ' : '' %><%= $this->privacyLevelPublicChecked ? '' : ' disabled="disabled"' %>class="radio" id="nonregVisibility_homepage" value="homepage" name="nonregVisibility"/><%= xg_html('SEE_JUST_THE_MAIN_PAGE') %></label></li>
                                    <?php if ($this->nonregVisibilityMessageChecked) { ?><li><label for="nonregVisibility_message"><input type="radio" <%= $this->nonregVisibilityMessageChecked ? 'checked="checked" ' : '' %><%= $this->privacyLevelPublicChecked ? '' : ' disabled="disabled"' %>class="radio" id="nonregVisibility_message" value="message" name="nonregVisibility"/><%= xg_html('SEE_JUST_THE_SIGN_UP_PAGE') %></label></li><?php } ?>
                                </ul>
                            </li>
                            <li class="indent" style="margin-top:.8em">
                                <label>
                                    <input name="privacyLevel" id="privacyLevelPrivate" type="radio" value="private" <%= $this->privacyLevelPrivateChecked ? 'checked="checked" ' : '' %>class="radio" /><strong><big><%= xg_html('PRIVATE') %></big></strong><br />
                                    <%= xg_html('THIS_NETWORK_VISIBLE_MEMBERS_ONLY') %><br/>
                                    <%= xg_html('WHO_CAN_SIGN_UP') %>
                                </label>
                                <ul class="options">
                                    <li><label><input type="radio" id="allowJoin_all" <%= $this->allowJoinAllChecked ? 'checked="checked" ' : '' %><%= $this->privacyLevelPublicChecked ? 'disabled="disabled" ' : '' %>class="radio"   value="all" name="allowJoin"/><%= xg_html('ANYONE') %></label></li>
                                    <li><label><input type="radio" id="allowJoin_invited" <%= $this->allowJoinAllChecked ? '' : 'checked="checked" ' %><%= $this->privacyLevelPublicChecked ? 'disabled="disabled" ' : '' %>class="radio" value="invited" name="allowJoin"/><%= xg_html('ONLY_INVITED_PEOPLE') %></label></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                <div class="right block">
                    <fieldset class="nolegend">
                        <h3><%= xg_html('FEATURE_CONTROLS') %></h3>
                        <ul class="options nobullets">
                            <li><label><input id="xj_user_groups_checkbox" type="checkbox" class="checkbox" value="yes" <%= $this->groupCreationChecked ? 'checked="checked" ' : '' %>name="groupCreation" /><%= xg_html('ALLOW_MEMBERS_CREATE_GROUPS') %></label></li>
                            <ul class="indent">
                                <li><label><input id="xj_moderate_groups_checkbox" type="checkbox" class="checkbox" value="yes" <%= $this->approveGroupsChecked ? 'checked="checked" ' : '' %> name="approveGroups" <%= $this->groupCreationChecked ? '' : 'disabled="disabled"' %> /><%= xg_html('APPROVE_GROUPS_BEFORE') %></label></li>
                            </ul>
                            <li><label><input type="checkbox" class="checkbox" value="yes" <%= $this->eventCreationChecked ? 'checked="checked" ' : '' %>name="eventCreation" /><%= xg_html('ALLOW_MEMBERS_CREATE_EVENTS') %></label></li>
                            <li><label><input type="checkbox" class="checkbox"  value="yes" <%= $this->enableMusicDownloadChecked ? 'checked="checked" ' : '' %> name="enableMusicDownload" /><%= xg_html('ENABLE_MUSIC_DOWNLOAD_LINKS') %></label></li>
                            <li><label><input type="checkbox" class="checkbox" value="yes" <%= $this->approveMembersChecked ? 'checked="checked" ' : '' %>name="approveMembers" /><%= xg_html('APPROVE_MEMBERS_BEFORE_JOIN') %></label></li>
                            <li><label><input type="checkbox" class="checkbox" value="yes" <%= $this->approveMediaChecked ? 'checked="checked" ' : '' %> name="approveMedia" /><%= xg_html('APPROVE_PHOTOS_VIDEOS') %></label></li>
                            <?php XG_App::ningLoaderRequire('xg.shared.ContextHelpToggler'); ?>
                            <li><label><input type="checkbox" class="checkbox" value="yes" <%= $this->allowCustomizeThemeChecked ? 'checked="checked" ' : '' %> name="allowCustomizeTheme" /><%= xg_html('ALLOW_MEMBERS_TO_CUSTOMIZE_THEIR_MY_PAGE_THEME') %></label>
                                    <span class="context_help"><a dojoType="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
                                        <span class="context_help_popup" style="display:none">
                                            <span class="context_help_content">
                                                <%= xg_html('SELECT_IF_YOU_WANT_MEMBERS_CUSTOMIZE_THEME') %>
                                                <small><a dojoType="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
                                            </span>
                                        </span>
                                    </span>
                                </li>
                            <li><label><input type="checkbox" class="checkbox" value="yes" <%= $this->allowCustomizeLayoutChecked ? 'checked="checked" ' : '' %> name="allowCustomizeLayout" /><%= xg_html('ALLOW_MEMBERS_TO_ADJUST_THEIR_MY_PAGE_LAYOUT') %></label>
                                    <span class="context_help"><a dojoType="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
                                        <span class="context_help_popup" style="display:none">
                                            <span class="context_help_content">
                                                <%= xg_html('SELECT_IF_YOU_WANT_MEMBERS_CUSTOMIZE_LAYOUT') %>
                                                <small><a dojoType="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
                                            </span>
                                        </span>
                                    </span>
                            </li>
                            <li><label><input type="checkbox" class="checkbox" value="yes" <%= $this->allow3rdPartyApplicationsChecked ? 'checked="checked" ' : '' %> name="allow3rdPartyApplications" /><%= xg_html('ALLOW_MEMBERS_TO_ADD_3RD_PARTY_APPLICATIONS') %></label>
                                    <span class="context_help"><a dojoType="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
                                        <span class="context_help_popup" style="display:none">
                                            <span class="context_help_content">
                                                <%= xg_html('ALL_APPLICATIONS_WILL_BE_REMOVED') %>
                                                <small><a dojoType="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
                                            </span>
                                        </span>
                                    </span>
                            </li>
                        </ul>
                    </fieldset>
                    <br />
                    <h3><%= xg_html('INVITATIONS') %></h3>
                    <p>
                        <%= xg_html('SHARE_LINK_WITH_PEOPLE') %><br/>
                        <input id="bulk_invitation_url_field" type="text" class="textfield" value="<%= xnhtmlentities($this->bulkInvitationUrl) %>" style="width:300px" />
                    </p>
                    <p class="buttongroup">
                        <a dojoType="PostLink" class="button" href="#"
                            _url="<%= $this->_buildUrl('privacy', 'generateBulkInvitationUrl') %>"
                            _confirmTitle="<%= xg_html('DISABLE_INVITATION_LINK') %>"
                            _confirmQuestion="<%= xg_html('CREATING_LINK_WILL_DISABLE') %>"
                            ><%= xg_html('CREATE_INVITATION_LINK') %></a>
                    </p>
                </div>
              </fieldset>
              </form>
              <?php /* This button must be outside the form */ ?>
              <p class="buttongroup">
				  <input type="button" id="savePrivacySettings" class="button button-primary" value="<%= qh(xg_html('SAVE')) %>" />
				  <input type="button" class="button" value="<%= xg_html('CANCEL')%>" onclick="window.location='<%= qh($this->_widget->buildUrl('admin', 'manage')) %>'"/>
              </p>
             </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
