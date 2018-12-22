<?php xg_header('profile', $title = xg_text('MY_SETTINGS') . ' - ' . xg_text('PROFILE'), null); ?>
<?php XG_App::ningLoaderRequire('xg.profiles.settings.editProfileInfo'); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
            <form id="settings_form" action="<%= $this->_buildUrl('settings', 'updateProfileInfo') %>" method="post" enctype="multipart/form-data">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <div class="xg_module">
                    <div class="xg_module_body pad">
                        <?php $this->renderPartial('fragment_settingsNavigation', '_shared', array('selected' => 'profile')); ?>
                        <div class="right page_ticker_content">
                            <fieldset class="nolegend profile">
                                <h3><%= xg_text('PROFILE') %></h3>
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
                                <dl>
                                    <dt><label><%= xg_html('EMAIL_ADDRESS') %></label></dt>
                                    <dd><%= xnhtmlentities($this->_user->email) %> &nbsp;<small><a href="<%= xnhtmlentities($this->_buildUrl('settings', 'editEmailAddress')) %>"><%= xg_html('CHANGE') %></a></small></dd>
                                </dl>
                                <dl>
                                    <dt><label><%= xg_html('PASSWORD') %></label></dt>
                                    <dd>&bull; &bull; &bull; &bull; &bull; &bull; &bull; &bull; &nbsp;<small><a href="<%= xnhtmlentities($this->_buildUrl('settings', 'editPassword')) %>"><%= xg_html('CHANGE') %></a></small></dd>
                                </dl>
                                <?php W_Cache::getWidget('main')->dispatch('authorization', 'profileInfoForm', array(array(
                                        'errors' => $this->errors,
                                        'showSimpleUploadField' => false,
                                        'aboutMeHtml' => null,
                                        'showBirthdateFields' => true,
                                        'showDisplayAgeCheckbox' => true,
                                        'showGenderField' => true,
                                        'showLocationField' => true,
                                        'showCountryField' => true,
                                        'saveParentFormOnChange' => true,
                                        'indicateRequiredFields' => false))); ?>
				<p class="small last-child ningid xg_lightfont">
			            <%= xg_html('WE_USE_NING_ID', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'ningId', '?noBack=1')) . '" class="xg_lightfont" target="_blank"') %>
    				</p>
                       		<?php if ($this->numQuestions > 0) { ?>
				    <h3><br /><%=xg_html('PROFILE_QUESTIONS')%></h3>
				    <p><%= xg_html('QUESTIONS_MARKED', 'class="private"') %></p>
                               	    <?php $this->_widget->dispatch('profile', 'profileQuestionForm'); ?>
                        	<?php } //if ?>
                                <h3><br /><%= xg_html('USEFUL_ADDRESSES') %></h3>
                                <dl>
                                    <dt><%= xg_html('PAGE_ADDRESS') %></dt>
                                    <dd>
                                        <strong><a href="<%= xnhtmlentities(xg_absolute_url('/profile/' . User::profileAddress($this->_user->screenName))) %>"><%= xnhtmlentities(xg_absolute_url('/profile')) %>&shy;<wbr />/<%= xnhtmlentities(User::profileAddress($this->_user->screenName)) %></a> </strong>
                                        <br/><small><a href="<%= $this->_buildUrl('settings', 'editProfileAddress') %>"><%= xg_html('CHANGE') %></a></small>
                                    </dd>
                                </dl>
                                <?php if (mb_strlen($this->uploadEmailAddress)) { ?>
                                <dl>
                                    <dt><%= xg_html('ADD_BY_PHONE') %></dt>
                                    <dd>
                                        <%= xg_html('ADD_PHOTOS_AND_VIDEOS_TO_APPNAME_FROM_PHONE', xnhtmlentities(XN_Application::load()->name)) %><br />
                                        <strong><a id="xg_profiles_settings_email_show" href="mailto:<%= xnhtmlentities($this->uploadEmailAddress) %>"><%= xnhtmlentities($this->uploadEmailAddress) %></a></strong><br/>
                                        <small>
                                            <a href="#" id="xg_profiles_settings_email_generate"><%= xg_html('GENERATE_NEW_EMAIL_ADDRESS') %></a>
                                        </small>
                                    </dd>
                                </dl>
                                <?php } ?>
                            </fieldset>
                            <p class="buttongroup">
                                    <?php
                                    if (! XG_SecurityHelper::userIsOwner()) { ?>
                                        <?php XG_App::ningLoaderRequire('xg.index.bulk'); ?>
                                        <a href="javascript:void(0)" class="left"
                                           dojoType="BulkActionLink"
                                           title="<%= xg_html('LEAVE_X_Q', xnhtmlentities(XN_Application::load()->name)) %>"
                                           _confirmMessage="<%= xg_html('ARE_YOU_SURE_LEAVE_X', xnhtmlentities(XN_Application::load()->name)) %>"
                                           _progressTitle="<%= xg_html('DELETING_CONTENT') %>"
                                           _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_CONTENT_DELETED') %>"
                                           _successUrl="<%= xnhtmlentities(XG_AuthorizationHelper::signOutUrl()) %>"
                                           _verb="<%= xg_html('DELETE') %>"
                                           _url="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('bulk','removeByUser', array('user' => $this->_user->screenName, 'xn_out' => 'json'))) %>"
                                           ><%= xg_html('LEAVE_X',xnhtmlentities(XN_Application::load()->name)) %></a>
                                <?php
                                } ?>
                                <input type="submit" class="button" value="<%= xg_html('SAVE') %>" />
                        </p>
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
