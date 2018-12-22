<?php
// This page is designed to function acceptably with Javascript turned off. [Jon Aquino 2007-01-24]
xg_header(W_Cache::current('W_Widget')->dir, $this->title); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Groups_HtmlHelper::subMenu($this->_widget, $this->editingExistingGroup ? 'group' : 'none')) ?>
		<%= xg_headline($this->title)%>
        <form id="add_group_form" action="<%= xnhtmlentities($this->formUrl) %>" method="post" enctype="multipart/form-data"
                _checkNameUrl="<%= xnhtmlentities($this->_buildUrl('group', 'nameOrUrlTaken', array('xn_out' => 'json', 'id' => $this->group->id))) %>">
            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
            <div class="xg_module">
                <div class="xg_module_body nopad">
                    <div class="pad5">
                        <dl class="errordesc msg" id="add_group_form_notify" <%= $this->errors ? '' : 'style="display: none"' %>>
                            <?php
                            if ($this->errors) { ?>
                                <dt><%= xg_html('THERE_HAS_BEEN_AN_ERROR') %></dt>
                                <dd>
                                    <ol>
                                        <?php
                                        foreach (array_unique($this->errors) as $error) { ?>
                                            <li><%= xnhtmlentities($error) %></li>
                                        <?php
                                        } ?>
                                    </ol>
                                </dd>
                            <?php
                            } ?>
                        </dl>
                        <?php
                        if (! $this->hideStepLinks) { ?>
                            <ol class="steps easyclear">
                                <li class="current"><%= xg_html('STEP_ONE_GROUP_INFORMATION') %></li>
                                <li><%= xg_html('STEP_TWO_INVITE_MEMBERS') %></li>
                            </ol>
                        <?php
                        } ?>
                    </div>
                    <div class="xg_column xg_span-9">
                        <div class="pad5">
                            <fieldset class="nolegend">
                                <div class="legend"><%= xg_html('GROUP_INFORMATION') %></div>
                                <dl>
                                    <dt <%= $this->errors['title'] ? 'class="error"' : '' %>><label for="groupname"><%= xg_html('NAME') %></label></dt>
                                    <dd><%= $this->form->text('title','id="groupname" class="required textfield" size="40" style="width:95%" maxlength="' . Group::MAX_TITLE_LENGTH . '"') %></dd>

                                    <?php XG_App::ningLoaderRequire('xg.shared.BazelImagePicker'); ?>
                                    <dt <%= $this->errors['icon'] ? 'class="error"' : '' %>><label for="groupimage"><%= xg_html('IMAGE') %></label></dt>
                                    <dd><div class="swatch_group nofloat required" dojoType="BazelImagePicker" fieldname="icon" allowTile="0" cssClass="swatch_group nofloat required" showUseNoImage="0" trimUploadsOnSubmit="0" currentImagePath="<%= xnhtmlentities(Group::iconUrlProper($this->group)) %>"></div></dd>
                                    <dt><label for="groupdescription"><%= xg_html('DESCRIPTION') %></label></dt>
                                    <dd><%= $this->form->textarea('description','id="groupdescription" rows="3" cols="40" style="width:95%" _maxlength="' . Group::MAX_DESCRIPTION_LENGTH . '"') %></dd>
                                    <?php
                                    if (!$this->editingExistingGroup) { ?>
                                    <dt class="<%= $this->errors['url'] ? 'error' : '' %>"><label for="url"><%= xg_html('GROUP_WEB_ADDRESS') %></label></dt>
                                        <?php
                                        $httpHost = str_replace('xna.ningops.net', 'ning.com', $_SERVER['HTTP_HOST']); // For Kyle's screencast [Jon Aquino 2007-05-14]
                                        ?>
                                        <dd><%= $this->form->text('url', 'id="groupurl" class="required textfield" size="40" style="width:95%" maxlength="' . Group::MAX_URL_LENGTH . '"') %><wbr /><small><%= xg_html('THIS_SETS_URL_OF_GROUP', $httpHost . '<wbr />/group') %></small></dd>
                                    <?php
                                    } ?>
                                    <dt><label for="groupexternalurl"><%= xg_html('WEBSITE') %></label></dt>
                                    <dd><%= $this->form->text('externalWebsiteUrl','id="groupexternalurl" class="textfield" size="50" type="text" style="width:95%" maxlength="' . Group::MAX_EXTERNAL_WEBSITE_URL_LENGTH . '"') %><br/>
                                        <small><%= xg_html('IF_GROUP_HAS_WEBSITE') %></small>
                                    </dd>
                                    <dt><label for="groupLocation"><%= xg_html('LOCATION') %></label></dt>
                                    <dd>
                                        <%= $this->form->text('groupLocation','id="groupLocation" class="textfield" size="50" type="text" style="width:95%" maxlength="' . Group::MAX_LOCATION_LENGTH . '"') %><br />
                                        <small><%= xg_html('IF_GROUP_HAS_LOCATION') %></small>
                                    </dd>
                                </dl>
                            </fieldset>
                        </div>
                    </div>
                    <div class="xg_column xg_span-7 last-child">
                        <div class="pad5">
                            <fieldset>
                                <div class="legend"><%= xg_html('FEATURES') %></div>
                                <p><%= xg_html('CHOOSE_THE_GROUP_FEATURES') %></p>
                                <ul class="options">
                                    <li><label for="groupsActive"><%= $this->form->checkbox('groupsActive', 'yes', 'class="checkbox"') %><%= xg_html('COMMENTS') %></label></li>
                                    <li><label for="forumActive"><%= $this->form->checkbox('forumActive', 'yes', 'class="checkbox"') %><%= xg_html('DISCUSSION_FORUM') %></label></li>
                                    <li><label for="htmlActive"><%= $this->form->checkbox('htmlActive', 'yes', 'class="checkbox"') %><%= xg_html('TEXT_BOX') %></label></li>
                                    <li><label for="feedActive"><%= $this->form->checkbox('feedActive', 'yes', 'class="checkbox"') %><%= xg_html('RSS_READER') %></label></li>
                                </ul>
                            </fieldset>
                            <fieldset>
                                <?php
                                if ($this->editingExistingGroup) { ?>
                                    <div class="legend"><%= $this->group->my->groupPrivacy == 'public' ? xg_html('ANYONE_CAN_JOIN_GROUP') : xg_html('ONLY_INVITED_PEOPLE_CAN_JOIN_GROUP') %></div>
                                <?php
                                } else { ?>
                                    <div class="legend"><%= xg_html('PRIVACY') %></div>
                                    <p><%= xg_html('WHO_CAN_JOIN_GROUP_PRIVACY') %></p>
                                    <ul class="options" id="privacy_options">
                                        <li><label><%= $this->form->radio('groupPrivacy', 'public', 'class="radio"') %><%= xg_html('ANYONE') %></label></li>
                                        <li><label><%= $this->form->radio('groupPrivacy', 'private', 'class="radio"') %><%= xg_html('ONLY_INVITED_PEOPLE') %></label></li>
                                    </ul>
                                <?php
                                } ?>
                                <div id="invitation_options" style="<%= Group::isPrivate($this->group) ? '' : 'display:none' %>">
                                    <ul class="options" style="margin-left:1.5em">
                                        <li><label><%= $this->form->checkbox('allowInvitations', 'yes', 'class="checkbox"') %> <%= xg_html('MEMBERS_CAN_INVITE') %></label></li>
                                        <li><label><%= $this->form->checkbox('allowInvitationRequests', 'yes', 'class="checkbox"') %> <%= xg_html('ALLOW_PEOPLE_TO_REQUEST') %></label></li>
                                    </ul>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="legend"><%= xg_html('MESSAGES') %></div>
                                <ul class="options"><li><label><%= $this->form->checkbox('allowMemberMessaging', 'yes', 'class="checkbox"') %> <%= xg_html('ALLOW_MEMBERS_TO_MESSAGE') %></li></ul>
                            </fieldset>
                        </div>
                    </div>
                    <p class="buttongroup">
                        <input type="submit" class="button button-primary" value="<%= xnhtmlentities($this->buttonText) %>">
                        <a href="<%= $this->cancelUrl %>" class="button"><%= xg_html('CANCEL') %></a>
                    </p>
                </div>
            </div>
        </form>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.groups.group.newOrEdit'); ?>
<?php xg_footer(); ?>