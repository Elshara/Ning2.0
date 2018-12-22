<?php XG_IPhoneHelper::header(null, xg_text('SIGN_IN_TO_APPNAME', XN_Application::load()->name), NULL, array('contentClass' => 'simple signIn','largeIcon' => true, 'hideNavigation' => true)); ?>
<form title="<%= XN_Application::load()->name %>" class="panel" action="<%= xnhtmlentities($this->_buildUrl('authorization', 'doSignIn', array('target' => $this->target, 'groupToJoin' => $this->groupToJoin))) %>" method="POST" selected="true">
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <?php 
    if ($this->showInvitationExpiredMessage) {
    	if (is_array($this->errors)) {
    		$this->errors[] = xg_html('SORRY_INVITATION_EXPIRED', xnhtmlentities(XN_Application::load()->name));
    	}
    	else {
    		$this->errors = xg_html('SORRY_INVITATION_EXPIRED', xnhtmlentities(XN_Application::load()->name));
    	}
    }
    XG_IPhoneHelper::outputErrors($this->errors);
    ?>
    <div class="panel"><h2><%= xnhtmlentities($title) %></h2></div>
    <?php
    if (XG_App::appIsPrivate() && !XG_App::allowJoinByAll()) { ?>
    <p><%= xg_html('MEMBERSHIP_TO_APPNAME_BY_INVITATION_ONLY', xnhtmlentities(XN_Application::load()->name)) %></p>
    <?php
    } ?>
    <fieldset>
        <div class="row">
            <label for="signin_email"><%= xg_html('EMAIL'); %></label>
            <%= $this->form->text('emailAddress', 'id="signin_email" size="20"') %>
        </div>
        <div class="row">
            <label for="signin_password"><%= xg_html('PASSWORD') %></label>
            <%= $this->form->password('password', 'id="signin_password" size="20"') %>
        </div>
    </fieldset>
    <p class="management"><input type="checkbox" name="tosAgree" id="tosAgree" value="true" checked="checked" /> <%= xg_html('BY_SIGNING_IN_YOU_AGREE_AMENDED', 'href="' . xnhtmlentities($this->_buildUrl('authorization', 'termsOfService', array('noBack' => 1))) . '" target="_blank"', 'href="' . xnhtmlentities($this->_buildUrl('authorization', 'privacyPolicy', array('noBack' => 1))) . '" target="_blank"') %></p>
    <a class="whiteButton" type="submit" href="#" onclick="this.parentNode.submit(); return false"><%= xg_html('SIGN_IN') %></a>
<?php
if ($this->showSignUpLink) { ?>
<p class="promo"><a href="<%= xnhtmlentities($this->_buildUrl('authorization', 'signUp', array('target' => $this->target, 'groupToJoin' => $this->groupToJoin))) %>"><%= xg_html('SIGN_UP') %></a></p>
<?php
} ?>
<p class="management"><a target="_blank" href="<%= xnhtmlentities(XG_Browser::browserUrl('desktop', $this->_buildUrl('authorization', 'requestPasswordReset', array('previousUrl' => XG_HttpHelper::currentUrl())))) %>"><%= xg_html('FORGOT_YOUR_PASSWORD') %></a> | <a target="_blank" href="<%= xnhtmlentities(XG_Browser::browserUrl('desktop',$this->_buildUrl('authorization', 'problemsSigningIn', array('previousUrl' => XG_HttpHelper::currentUrl())))) %>"><%= xg_html('PROBLEMS_SIGNING_IN') %></a></p>
</form>
<?php xg_footer(NULL,array('contentClass' => 'simple','displayFooter' => false)); ?>