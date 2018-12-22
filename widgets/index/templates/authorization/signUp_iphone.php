<?php XG_IPhoneHelper::header('admin', xg_text('SIGN_UP_FOR_X', XN_Application::load()->name), NULL, array('contentClass' => 'simple signUp','largeIcon' => true, 'hideNavigation' => true)); ?>
<form title="<%= XN_Application::load()->name %>" action="<%= xnhtmlentities($this->_buildUrl('authorization', 'doSignUp', array('target' => $this->target, 'groupToJoin' => $this->groupToJoin))) %>" method="POST" selected="true">
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <?php XG_IPhoneHelper::outputErrors($this->errors); ?>
    <div class="panel">
    <fieldset>
        <div class="row">
            <label for="emailAddress"><%= xg_html('EMAIL'); %></label>
			<%=$this->form->text('emailAddress', 'autocorrect="off"')%>
        </div>
        <?php
		if ($this->shouldConfirmEmailAddress()) { ?>
        <div class="row">
            <label for="emailAddressConfirmation"><%= xg_html('CONFIRM_EMAIL'); %></label>
			<%=$this->form->text('emailAddressConfirmation', 'autocorrect="off"')%>
        </div>
        <?php
        } ?>
        <div class="row">
            <label for="password"><%= xg_html('PASSWORD'); %></label>
            <input type="password" name="password" value=""/>
        </div>
        <?php
		if ($this->shouldConfirmPassword()) { ?>
        <div class="row">
            <label for="passwordConfirmation"><%= xg_html('RETYPE_PASSWORD'); %></label>
            <input type="password" name="passwordConfirmation" value=""/>
        </div>
        <?php
        } ?>
        <div class="row">
            <label class="date"><%= xg_html('BIRTHDAY'); %></label>
            <%= $this->form->select('birthdateMonth', $this->monthOptions, false, 'class="date"') %>
            <%= $this->form->select('birthdateDay', $this->dayOptions, false, 'class="date"') %>
            <%= $this->form->select('birthdateYear', $this->yearOptions, false, 'class="date"') %>
        </div>
        <div class="row captcha">
			<label for="signup_captcha"><%= xg_html('TYPE_CODE_ON_RIGHT') %></label>
			<input type="text" name="captchaValue" id="signup_captcha" value="" class="textfield" autocorrect="off" maxlength="5" size="5" />
			<input type="hidden" name="captchaToken" value="<%= $this->captcha->token %>" />
			<img class="verification" src="<%= preg_replace('#^\w+://[^/]+/#', '/', xnhtmlentities($this->captcha->url)) %>" alt="<%= xg_html('VERIFICATION_IMAGE') %>" width="100" height="50" alt="<%= xg_html('CAPTCHA') %>"/>
		</div>
    </fieldset>
	</div>
<p class="management"><input type="checkbox" name="tosAgree" id="tosAgree" value="true" checked="checked" /> <%= xg_html('BY_SIGNING_UP_YOU_AGREE', 'target="_blank" href="'. xnhtmlentities(XG_Browser::browserUrl('desktop', $this->_buildUrl('authorization', 'termsOfService', array('previousUrl' => XG_HttpHelper::currentUrl())))) .'"', 'target="_blank" href="'. xnhtmlentities(XG_Browser::browserUrl('desktop', $this->_buildUrl('authorization', 'privacyPolicy', array('previousUrl' => XG_HttpHelper::currentUrl())))) .'"') %></p>
<a class="whiteButton" type="submit" href="#" onclick="this.parentNode.submit()"><%= xg_html('SIGN_UP') %></a>
<p class="promo"><a href="<%= xnhtmlentities($this->_buildUrl('authorization', 'signIn', array('target' => $this->target, 'groupToJoin' => $this->groupToJoin))) %>"><%= xg_html('SIGN_IN') %></a></p>
<p class="management"><a target="_blank" href="<%= xnhtmlentities(XG_Browser::browserUrl('desktop',$this->_buildUrl('authorization', 'problemsSigningIn', array('previousUrl' => XG_HttpHelper::currentUrl())))) %>"><%= xg_html('PROBLEMS_SIGNING_UP') %></a></p>
</form>
<?php xg_footer(NULL,array('displayFooter' => false)); ?>
