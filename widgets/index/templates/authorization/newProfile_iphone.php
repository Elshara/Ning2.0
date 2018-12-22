<?php XG_IPhoneHelper::header('admin', $title = xg_text('CREATE_YOUR_PROFILE', XN_Application::load()->name), null, array('contentClass' => 'compose','displayHeader' => false, 'hideNavigation' => true)); ?>
	<div id="header">
	<strong><%= xg_html('PROFILE_QUESTIONS') %></strong>
	<a class="title-button" id="add" href="javascript:void(0);"><%= xg_html('SUBMIT') %></a>
	<a class="title-button" id="cancel" href="javascript:void(0);"><%= xg_html('CANCEL') %></a>
	</div><!--/#header-->

	<div class="legend">
	<p><%= xg_html('TELL_THE_PEOPLE_ON_SHORT', XN_Application::load()->name) %></p>
	<p>
	<span class="icon required"><%= xg_html('REQUIRED') %></span><br/>
	<span class="icon private"><%= xg_html('PRIVATE_AND_ONLY_VISIBLE_ADMIN') %></span>
	</p>
	</div>
            <form id="compose" class="panel profile" id="profile_form" action="<%= xnhtmlentities($this->_buildUrl('authorization', 'createProfile', array('target' => $this->target, 'newNingUser' => $this->newNingUser, 'groupToJoin' => $this->groupToJoin))) %>" method="post" enctype="multipart/form-data">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %> <?php
                		XG_IPhoneHelper::outputErrors($this->errors); ?>
                		<fieldset class="nolegend profile">
                		<?php
                		$this->_widget->dispatch('authorization', 'profileInfoForm', array(array(
                                'errors' => $this->errors,
                                'showSimpleUploadField' => $this->newNingUser,
                                'aboutMeHtml' => $this->aboutMeHtml,
                                'showBirthdateFields' => false,
                                'showDisplayAgeCheckbox' => false,
                                'showGenderField' => $this->showGenderField,
                                'showLocationField' => $this->showLocationField,
                                'showCountryField' => $this->showCountryField,
                                'indicateRequiredFields' => true))); ?>
                		<?php
                        W_Cache::getWidget('profiles')->dispatch('profile', 'profileQuestionForm'); ?>
                        </fieldset>
            </form>
		    <script>initComposeForm()</script>
<script type="application/x-javascript" src="<%= xg_cdn($this->_widget->buildResourceUrl('js/authorization/newProfile_iphone.js')) %>"></script>
<?php xg_footer(null, array('contentClass' => 'compose', 'displayFooter' => false)) ?>
