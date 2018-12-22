<?php xg_header('manage',xg_text('NETWORK_INFORMATION')); ?>
<?php XG_App::ningLoaderRequire('xg.index.admin.appProfile'); ?>
<script type="text/javascript">
    xg.addOnRequire(function() {
        xg_handleLaunchBarSubmit = xg.index.admin.appProfile.handleLaunchBarSubmit;
    });
</script>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
            <?php
            if ($this->displayPrelaunchButtons) { ?>
                <div id="xg_setup_next_header_top">
                    <?php W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_SetupHelper.php'); ?>
                    <%= Index_SetupHelper::nextButton($this->nextLink, true); %>
                </div>
                <h1><%= xg_html('DESCRIBE_NETWORK') %></h1>
                <p><big><%= xg_html('GIVE_EVERYONE_REASON') %></big></p>
            <?php
            } else { ?>
                    <h1><%= xg_html('NETWORK_INFORMATION') %></h1>
                    <?php $this->renderPartial('fragment_success'); ?>
            <?php
            } ?>
                <form id="profile_form" method="post" enctype="multipart/form-data" action="<%= $this->_widget->buildUrl('admin', 'appProfile') %>">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                        <?php
                        if (XG_App::appIsLaunched()) { ?>
                            <dl id="profile_form_notify" style="display:none"></dl>
                        <?php
                        } else { ?>
                            <div id="profile_form_notify" style="display:none"></div>
                        <?php
                        } ?>
                        <input type="hidden" name="successTarget"/>
                        <input type="hidden" name="oldAppName" value="<%= xnhtmlentities(XN_Application::load()->name) %>" />
                            <fieldset class="nolegend networkinfo">
                            <dl class="first">
                                <dt class="align-right"><label for="profile_app_name"><%= xg_html('NETWORK_NAME') %></label></dt>
                                <dd><%= $this->form->text('name','id="profile_app_name" class="textfield" size="40" maxlength="64"') %></dd>
                            </dl>
                            <?php
                            if (!XG_App::appIsLaunched()) { ?>
                                <dl>
                                    <dt class="align-right"><%= xg_html('PRIVACY') %></dt>
                                    <dd>
                                        <ul class="options">
                                            <li><label><%= $this->form->radio('appPrivacy', 'public', 'id="site_type_public" class="radio"') %>
                                            <%= xg_html('PUBLIC_ANYBODY_CAN_JOIN') %></label>
                                            <%= $this->form->hidden('nonregVisibility') %>
                                            </li>
                                            <li><label><%= $this->form->radio('appPrivacy', 'private', 'id="site_type_private" class="radio"') %>
                                            <%= xg_html('PRIVATE_ONLY_INVITED') %></label>
                                            <%= $this->form->hidden('allowInvites') %>
                                            <%= $this->form->hidden('allowJoin') %>
                                            <%= $this->form->hidden('allowRequests') %>
                                            </li>
                                        </ul>
                                    </dd>
                                </dl>
                            <%= $this->form->hidden('moderate') %>
                            <?php
                            } ?>
                                <dl>
                                    <dt class="align-right"><label for="profile_app_tagline"><%= xg_html('TAGLINE') %></label></dt>
                                    <dd>
                                    <%= $this->form->text('tagline','id="profile_app_tagline" class="textfield" size="40" maxlength="80"') %>
                                        <small><%= xg_html('APPEARS_IN_HEADER') %></small>
                                    </dd>
                            </dl>
                            <dl>
                                    <dt class="align-right"><label for="profile_description"><%= xg_html('DESCRIPTION') %></label></dt>
                                    <dd>
                                        <%= $this->form->textarea('description','id="profile_app_description" cols="38" rows="3"') %><br />
                                        <span><small><%= xg_html('UP_TO_N_CHARACTERS', 140) %></small></span>
                                    </dd>
                            </dl>
                            <dl>
                                    <dt class="align-right"><label for="profile_app_tags"><%= xg_html('KEYWORDS') %></label></dt>
                                    <dd>
                                        <%= $this->form->text('tags','id="profile_app_tags" class="textfield" size="40"') %><br />
                                        <small><%= xg_html('SEPARATE_EACH_KEYWORD') %></small>
                                    </dd>
                            </dl>
                            <?php
                            if (count($this->languages)) { ?>
                                <dl>
                                    <dt class="align-right"><label for="profile_app_language"><%= xg_html('LANGUAGE') %></label></dt>
                                    <dd>
                                        <select name="locale" id="profile_app_language">
                                            <?php
                                            foreach ($this->languages as $lc => $name) {
                                                echo '<option value="' . $lc . '"';
                                                if ($this->locale == $lc) {
                                                    echo ' selected="selected"';
                                                }
                                                echo ">$name</option>";
                                            } ?>
                                        </select>
                                    </dd>
                                </dl>
                            <?php
                            } ?>
                            <?php
                            if (XG_App::appIsLaunched()) { ?>
                                <dl>
                                <dt class="align-right"><%= xg_html('SET_NETWORK_ICON') %></dt>
                                <dd>
                                    <img src="<%= xnhtmlentities($this->appIconUrl) %>" alt="<%= xg_html('APP_ICON') %>" class="left" width="64" height="64"/>
                                        <%= $this->form->file('icon','id="profile_app_icon" class="file" size="18"') %>
                                </dd>
                                <dd class="small"><%= xg_html('UPLOAD_A_PHOTO_WHICH_WILL_BE_RESIZED') %></dd>
                                </dl>
                                <dl>
                                    <dt class="after-clear align-right"><%= xg_html('SET_DEFAULT_AVATAR') %></dt>
                                    <dd class="after-clear">
                                        <img src="<%= xnhtmlentities($this->defaultAvatar) %>" alt="<%= xg_html('PROFILE_ICON') %>" class="left" width="64" height="64"/>
                                        <%= $this->form->file('profile_icon','id="profile_icon" class="file" size="18"') %>
                                    </dd>
                                    <dd class="small"><%= xg_html('APPEARS_FOR_MEMBERS_PROFILE_PHOTO') %></dd>
                                </dl>
                            <?php
                            } ?>
                            </fieldset>
                            <?php
                            if ($this->displayPrelaunchButtons) {
                                    $this->renderPartial('_backnext', 'embed');
                            } else { ?>
                                    <p class="buttongroup"><input type="submit" class="button button-primary" onClick="xg.index.admin.appProfile.submitForm()" value="<%= xg_html('SAVE') %>" />
                                    <a class="button" href="<%= $this->_widget->buildUrl('admin', 'manage') %>"><%= xg_html('CANCEL') %></a></p>
                            <?php } ?>
                    </div>
                </div>
                </form>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>
<?php xg_footer(null, array('displayFooter' => XG_App::appIsLaunched())) ?>
