<?php xg_header('manage',xg_text('LATEST_ACTIVITY')); ?>
<?php XG_App::ningLoaderRequire('xg.index.activity.edit'); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline(xg_text('LATEST_ACTIVITY'))%>
            <form id="activity_form" action="<%= $this->_buildUrl('activity', 'edit'); %>" method="post">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <div class="xg_module">
<?php
if (!$this->hasActivityFeature) { ?>
                    <div class="xg_module_body pad notification">
                        <h3><%= xg_html('SHOW_LATEST_ACTIVITY_ON_YOUR_NETWORK')%></h3>
                        <p><%= xg_html('PUT_A_CONSTANTLY_UPDATED_STREAM')%></p>
                        <p><a href="<%= $this->_widget->buildUrl('feature', 'add') %>"><%= xg_html('CLICK_HERE_TO_ADD_LATEST_ACTIVITY')%></a></p>
                        <p><small><%= xg_html('THESE_OPTIONS_ARE_DISABLED_ACTIVITY')%></small></p>
                    </div>
<?php
}

$this->renderPartial('fragment_success', 'admin');

if (count($this->errors)) { ?>
    <dl class="errordesc msg" id="settings_form_notify">
        <dt><%= xg_html('PLEASE_CORRECT_THE_FOLLOWING') %></dt>
        <dd><ol>
        <?php foreach ($this->errors as $error) { ?>
            <li><%= $error %></li>
        <?php } ?>
        </ol></dd>
    </dl>
<?php
} ?>
                    <div class="xg_module_body pad">
                        <fieldset class="nolegend" <?php if (!$this->hasActivityFeature) { echo 'style="opacity:.6;"'; } ?>>
                            <div class="left block" style="280px !important">
                                <h3><%= xg_html('DISPLAY_PREFERENCES')%></h3>
                                <fieldset>
                                    <div class="legend"><%= xg_html('WHAT_DISPLAYS_LATEST_ACTIVITY')%></div>
                                    <ul class="options">
                                        <li><label><input type="checkbox" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> <%= $this->logNewContentChecked ? 'checked="checked" ' : ''       %> class="checkbox" value="Y" id="logNewContent"     name="logNewContent"     /><%= xg_html('NEW_CONTENT')%></label></li>
                                        <li><label><input type="checkbox" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> <%= $this->logNewCommentsChecked ? 'checked="checked" ' : ''      %> class="checkbox" value="Y" id="logNewComments"    name="logNewComments"    /><%= xg_html('NEW_COMMENTS')%></label></li>
                                        <li><label><input type="checkbox" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> <%= $this->logFriendshipsChecked ? 'checked="checked" ' : ''       %> class="checkbox" value="Y" id="logFriendships"     name="logFriendships"     /><%= xg_html('NEW_FRIENDSHIPS')%></label></li>
                                        <li><label><input type="checkbox" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> <%= $this->logNewMembersChecked ? 'checked="checked" ' : ''       %> class="checkbox" value="Y" id="logNewMembers"     name="logNewMembers"     /><%= xg_html('NEW_MEMBERS')%></label></li>
                                        <?php if ($this->hasEvents) {?><li><label><input type="checkbox" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> <%= $this->logNewEventsChecked ? 'checked="checked" ' : ''       %> class="checkbox" value="Y" id="logNewEvents"     name="logNewEvents"     /><%= xg_html('NEW_EVENTS')%></label></li><?php }?>
                                        <li><label><input type="checkbox" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> <%= $this->logProfileUpdatesChecked ? 'checked="checked" ' : ''   %> class="checkbox" value="Y" id="logProfileUpdates" name="logProfileUpdates" /><%= xg_html('MEMBER_UPDATES')%></label></li>
                                        <?php if ($this->hasOpenSocial) {?><li><label><input type="checkbox" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> <%= $this->logOpenSocialChecked ? 'checked="checked" ' : ''       %> class="checkbox" value="Y" id="logOpenSocial"     name="logOpenSocial"     /><%= xg_html('THIRD_PARTY_FEATURE_ACTIVITY')%></label></li><?php }?>
                                    </ul>
                                </fieldset>
                            </div>
                            <div class="right block">
                                <h3><%= xg_html('ADD_MESSAGE_TO_LATEST_ACTIVITY') %></h3>
                                <input type="hidden" name="addMessage" id="add_message_input" value="false" />
                                <input type="hidden" id="activitymessage" name="message" />
                                <input type="hidden" id="custom_message_flag" name="custom_message" />
                                <p>
                                    <label for="activitymessage"><%= xg_html('ADD_FACTS_ABOUT_THE_NETWORK_ELLIPSIS')%></label><br />
                                    <select style="width:425px;" name="choice" id='fact_selector'  <%= (!$this->hasActivityFeature)?'disabled="true"':'' %>><?php
                                    foreach($this->optiongroups as $groupname => $options){
                                        if(count($options)>0){?>
                                        <optgroup label="<%= xnhtmlentities($groupname) %>"> <?php
                                            foreach($options as $option){ ?>
                                            <option value="<%= $option['type'].','.$option['content'] %>" _html="<%= xnhtmlentities($option['html']) %>"><%= xnhtmlentities($option['label']) %></option>
                                            <?php
                                            } ?>
                                        </optgroup><?php
                                        }
                                    } ?>
                                    </select>
                                    <p class="buttongroup">
                                        <input type="button" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> class="button" value="<%= xg_html('ADD_MESSAGE') %>" id="add_message_fact_button" />
                                    </p>
                                </p>
                                <p>
                                    <small class="right">(<span id="character_count" dojoType="charCountDown" _inputId="customMessageBox" _limit="<%= $this->characterLimit %>"><%= $this->characterLimit %></span>)</small>
                                    <label for="activitymessage"><%= xg_html('ELLIPSIS_OR_WRITE_YOUR_OWN_MESSAGE') %></label><br />
                                    <textarea <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> id="customMessageBox" name="custommessage" style="width:425px" rows="2" cols="40"></textarea><br />
                                    <p class="buttongroup">
                                        <input type="button" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %> class="button" value="<%= xg_html('ADD_MESSAGE') %>" id="add_custom_message_button" />
                                    </p>
                                </p>
                            </div>
                        </fieldset>
                        <p class="buttongroup" <?php if (!$this->hasActivityFeature) { echo 'style="opacity:.6;"'; } ?>>
                            <input type="submit" class="button button-primary" value="<%= xg_html('SAVE')%>" <%= (!$this->hasActivityFeature)?'disabled="true"':'' %>/>
                            <a class="button" href="<%= $this->_widget->buildUrl('admin', 'manage') %>"><%= xg_html('CANCEL')%></a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
