<?php xg_header('profile', $title = xg_text('MY_SETTINGS') . ' - ' . xg_text('PRIVACY'), null); ?>
<?php
$enabledModules = XG_ModuleHelper::getEnabledModules();
function error_if($ob,$field) {
    return isset($ob->errors[$field]) ? 'class="error"' : '';
} ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
            <form id="settings_form" action="<%= $this->_buildUrl('profile', 'privacySettings') %>" method="post">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <div class="xg_module">
                    <div class="xg_module_body pad">
                        <?php $this->renderPartial('fragment_settingsNavigation', '_shared', array('selected' => 'privacy')); ?>
                        <div class="right page_ticker_content">
                            <h3><%= xg_html('PRIVACY') %></h3>    
                            <fieldset class="nolegend">
                                <?php
                                if ($this->displaySavedNotification) { ?>
                                    <p class="success"><%= xg_html('PROFILE_SAVED_GO', 'href="' . xnhtmlentities($this->_buildUrl('index','index')) . '"') %></p>
                                <?php
                                } ?>
                                <dl class="errordesc msg" <%= $this->errors ? '' : 'style="display: none"' %>>
                                    <dt><%= xg_html('PLEASE_CORRECT_THE_FOLLOWING') %></dt>
                                    <dd>
                                        <ol>
                                            <?php
                                            foreach ($this->errors as $error) { ?>
                                                <li><%= $error %></li>
                                            <?php
                                            } ?>
                                        </ol>
                                    </dd>
                                </dl>
                                <fieldset>
                                    <div class="normal"><%= strip_tags(str_replace('>', '> ', xg_html('WHO_CAN_VIEW_YOUR_PHOTOS_VIDEOS'))) %> </div>
                                    <ul class="options" <%= error_if($this,'defaultVisibility') %>>
                                        <li><label><%= $this->form->radio('defaultVisibility','all','class="radio"') %><%= xg_html('ANYONE') %></label></li>
                                        <li><label><%= $this->form->radio('defaultVisibility','friends','class="radio"') %><%= xg_html('JUST_MY_FRIENDS') %></label></li>
                                        <li><label><%= $this->form->radio('defaultVisibility','me','class="radio"') %><%= xg_html('JUST_ME') %></label></li>
                                    </ul>
                                    <p><small><%= xg_html('YOU_CAN_OVERRIDE_WHEN_YOU_ADD') %></small></p>
                                </fieldset>
                                <fieldset>
                                    <div class="normal"><%= strip_tags(str_replace('>', '> ', xg_html('WHO_CAN_COMMENT_ON_PHOTOS_VIDEOS'))) %></div>
                                    <ul class="options" <%= error_if($this,'addCommentPermissions') %>>
                                        <li><label><%= $this->form->radio('addCommentPermission','all','class="radio"') %><%= xg_html('ANYONE') %></label></li>
                                        <li><label><%= $this->form->radio('addCommentPermission','friends','class="radio"') %><%= xg_html('JUST_MY_FRIENDS') %></label></li>
                                        <li><label><%= $this->form->radio('addCommentPermission','me','class="radio"') %><%= xg_html('JUST_ME') %></label></li>
                                    </ul>
                                </fieldset>
                                <fieldset>
                                    <div class="normal"><%= strip_tags(str_replace('>', '> ', xg_html('MODERATE_COMMENTS_ON_BLOG'))) %></div>
                                    <ul class="options" <%= error_if($this,$this->moderationAttributeName) %>>
                                        <li><label><%= $this->form->radio($this->moderationAttributeName,'Y','class="radio"') %><%= xg_html('YES_LET_ME_APPROVE') %></label></li>
                                        <li><label><%= $this->form->radio($this->moderationAttributeName,'N','class="radio"') %><%= xg_html('NO_PUBLISH_COMMENTS') %></label></li>
                                    </ul>
                                </fieldset>
                                <fieldset>
                                    <div class="normal"><%= strip_tags(str_replace('>', '> ', xg_html('MODERATE_COMMENTS_ON_COMMENTWALL'))) %></div>
                                    <ul class="options" <%= error_if($this,$this->commentWallModerationAttributeName) %>>
                                        <li><label><%= $this->form->radio($this->commentWallModerationAttributeName,'Y','class="radio"') %><%= xg_html('YES_LET_ME_APPROVE') %></label></li>
                                        <li><label><%= $this->form->radio($this->commentWallModerationAttributeName,'N','class="radio"') %><%= xg_html('NO_PUBLISH_COMMENTS') %></label></li>
                                    </ul>
                                </fieldset>
                                <?php if ($enabledModules['events']) {?>
                                <fieldset>
                                    <div class="normal"><%= strip_tags(str_replace('>', '> ', xg_html('WHO_CAN_VIEW_EVENTS'))) %></div>
                                    <ul class="options" <%= error_if($this,'viewEventsPermission') %>>
                                        <li><label><%= $this->form->radio('viewEventsPermission','all','class="radio"') %><%= xg_html('ANYONE') %></label></li>
                                        <li><label><%= $this->form->radio('viewEventsPermission','friends','class="radio"') %><%= xg_html('JUST_MY_FRIENDS') %></label></li>
                                        <li><label><%= $this->form->radio('viewEventsPermission','me','class="radio"') %><%= xg_html('JUST_ME') %></label></li>
                                    </ul>
                                </fieldset>
                                <?php }?>
                                <?php if (XG_App::everythingIsVisible()) { ?>
                                <fieldset>
                                    <div class="normal"><%= strip_tags(str_replace('>', '> ', xg_html('NOTIFY_BLOG_PING_SERVICES'))) %></div>
                                    <ul class="options" id="blog_ping_options">
                                          <li><label><%= $this->form->checkbox('blogPingPermission','Y','class="checkbox"') %><%= xg_html('YES_NOTIFY_BLOG_SERVICES','href="http://pingomatic.com" target="_new"')%></label></li>
                                      </ul>
                                </fieldset>
                                <?php } ?>
                                <?php
                                XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
                                if($enabledModules['activity']) { ?>
                                    <fieldset>
                                        <div class="normal"><%= strip_tags(str_replace('>', '> ', xg_html('WHICH_OF_YOUR_ACTIONS_DISPLAY_ACTIVITY'))) %></div>
                                        <ul class="options" id="activity_options">
                                            <li><label><%= $this->form->checkbox('activityNewContent','Y','class="checkbox"') %><%= xg_html('NEW_CONTENT_I_ADD')%></label></li>
                                            <li><label><%= $this->form->checkbox('activityNewComment','Y','class="checkbox"') %><%= xg_html('NEW_COMMENTS_I_ADD')%></label></li>
                                            <li><label><%= $this->form->checkbox('activityFriendships','Y','class="checkbox"') %><%= xg_html('NEW_FRIENDS')%></label></li>
                                            <li><label><%= $this->form->checkbox('activityProfileUpdate','Y','class="checkbox"') %><%= xg_html('WHEN_I_UPDATE_MY_PROFILE')%></label></li>
                                            <?php if ($enabledModules['events']) {?>
                                                <li><label><%= $this->form->checkbox('activityEvents','Y','class="checkbox"') %><%= xg_html('MY_EVENTS_AND_RSVP')%></label></li>
                                            <?php }?>
                                        </ul>
                                        <p><small><%= xg_html('THE_LATEST_ACTIVITY_WILL_NEVER')%></small></p>
                                    </fieldset>
                                <?php
                                } ?>
                                <p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('SAVE') %>" /></p>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
