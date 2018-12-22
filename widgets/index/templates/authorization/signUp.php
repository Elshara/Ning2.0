<?php xg_header(null, $title = xg_text('SIGN_UP_FOR_X', XN_Application::load()->name), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <form action="<%= xnhtmlentities($this->_buildUrl('authorization', 'doSignUp', array('target' => $this->target, 'groupToJoin' => $this->groupToJoin))) %>" method="post" class="xg_module_body pad xg_lightborder">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <?php $this->renderPartial('fragment_signUpHeading', 'authorization', array('title' => $title, 'target' => $this->target, 'groupToJoin' => $this->groupToJoin, 'invitation' => $this->invitation)); ?>
                    <div class="errordesc" <%= $this->errors ? '' : 'style="display: none"' %>>
                        <h4><%= xg_html('PROBLEM_SIGNING_UP') %></h4>
                        <ul class="last-child">
                            <?php
                            foreach ($this->errors as $error) { ?>
                                <li><%= $error %></li>
                            <?php
                            } ?>
                        </ul>
                    </div>
                    <div class="errordesc" style="display:none" id="cookie_check">
                        <p class="last-child"><%= xg_html('PLEASE_ENABLE_COOKIES', 'href="http://help.ning.com/cgi-bin/ning.cfg/php/enduser/std_adp.php?p_faqid=3217" target="_new"') %></p>
                    </div>
                    <fieldset class="nolegend account clear" id="signup">
                        <dl>
                            <dt<%= $this->errors['emailAddress'] ? ' class="error"' : '' %>><label for="signup_email"><big><strong><%= xg_html('EMAIL_ADDRESS'); %></strong></big></label></dt>
                            <dd<%= $this->errors['emailAddress'] ? ' class="error"' : '' %>><%= $this->form->text('emailAddress', 'id="signup_email" class="textfield large" size="20" maxlength="100"') %></dd>
                        </dl>
                        <?php
                        if ($this->shouldConfirmEmailAddress()) { ?>
                            <dl>
                                <dt<%= $this->errors['emailAddressConfirmation'] ? ' class="error"' : '' %>><label for="signup_email_confirm"><big><strong><%= xg_html('CONFIRM_EMAIL'); %></strong></big></label></dt>
                                <dd<%= $this->errors['emailAddressConfirmation'] ? ' class="error"' : '' %>><%= $this->form->text('emailAddressConfirmation', 'id="signup_email_confirm" class="textfield large" size="20" maxlength="100"') %></dd>
                            </dl>
                        <?php
                        } ?>
                        <dl>
                            <dt<%= $this->errors['password'] ? ' class="error"' : '' %>><label for="signup_password"><big><strong><%= xg_html('PASSWORD') %></strong></big></label></dt>
                            <dd<%= $this->errors['password'] ? ' class="error"' : '' %>><%= $this->form->password('password', 'id="signup_password" class="password large" size="20" maxlength="64"') %></dd>
                        </dl>
                        <?php
                        if ($this->shouldConfirmPassword()) { ?>
                            <dl>
                                <dt<%= $this->errors['passwordConfirmation'] ? ' class="error"' : '' %>><label for="signup_password_confirm"><big><strong><%= xg_html('RETYPE_PASSWORD') %></strong></big></label></dt>
                                <dd<%= $this->errors['passwordConfirmation'] ? ' class="error"' : '' %>><%= $this->form->password('passwordConfirmation', 'id="signup_password_confirm" class="password large" size="20" maxlength="64"') %></dd>
                            </dl>
                        <?php
                        } ?>
                        <dl>
                            <dt<%= $this->errors['birthdateMonth'] ? ' class="error"' : '' %>><label for="dob-month"><big><strong><%= xg_html('BIRTHDAY') %></strong></big></label></dt>
                            <dd<%= $this->errors['birthdateMonth'] ? ' class="error"' : '' %>>
                                <%= $this->form->select('birthdateMonth', $this->monthOptions, false, 'class="large" id="dob-month"') %>
                                <%= $this->form->select('birthdateDay', $this->dayOptions, false, 'class="large"') %>
                                <%= $this->form->select('birthdateYear', $this->yearOptions, false, 'class="large"') %><br />
                                <small class="xg_lightfont"><%= xg_html('WE_WONT_DISPLAY_YOUR_AGE') %></small>
                            </dd>
                        </dl>
                        <dl>
                            <dt<%= $this->errors['captchaValue'] ? ' class="error"' : '' %>><label for="signup_captcha"><big><strong><%= xg_html('TYPE_CODE_ON_RIGHT') %></strong></big></label></dt>
                            <dd<%= $this->errors['captchaValue'] ? ' class="error"' : '' %>>
                                <input type="text" name="captchaValue" id="signup_captcha" class="textfield verification" size="7" maxlength="5" autocomplete="off" />
                                <img class="verification" src="<%= preg_replace('#^\w+://[^/]+/#', '/', xnhtmlentities($this->captcha->url)) %>" alt="<%= xg_html('VERIFICATION_IMAGE') %>" width="100" height="50" />
                                <input type="hidden" name="captchaToken" value="<%= $this->captcha->token %>" />
                            </dd>
                        </dl>
                        <dl>
                            <dd>
                                <input type="submit" class="button" value="<%= xg_html('SIGN_UP') %>" />
                                <a id="problems_so" href="<%= xnhtmlentities($this->_buildUrl('authorization', 'problemsSigningIn', array('noBack' => 1))) %>" target="_blank"><%= xg_html('PROBLEMS_SIGNING_UP') %></a>
                            </dd>
                            <dd id="helplinks" class="clear">
                                <input type="checkbox" name="tosAgree" id="tosAgree" value="true" checked="checked" class="checkbox" />
                                <div><%= xg_html('BY_SIGNING_UP_YOU_AGREE', 'href="' . xnhtmlentities($this->_buildUrl('authorization', 'termsOfService', array('noBack' => 1))) . '" target="_blank"', 'href="' . xnhtmlentities($this->_buildUrl('authorization', 'privacyPolicy', array('noBack' => 1))) . '" target="_blank"') %></div>
                            </dd>
                        </dl>
                    </fieldset>
                </form>
                <%= Index_AuthorizationController::EOC_144_SCRIPT %>
                <?php $this->_widget->dispatch('authorization', 'footer'); ?>
    </div>
</div>
<script type="text/javascript">
    document.getElementById('signup_email').focus();
    document.getElementById('signup_email').select();
    if (document.cookie.indexOf('xg_cookie_check') < 0) {
        document.getElementById('cookie_check').style.display = 'block';
    }
</script>
<?php xg_footer(null, array('displayFooter' => false)) ?>