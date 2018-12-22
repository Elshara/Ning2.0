<?php xg_header(null, $title = xg_text('SIGN_UP_FOR_X', XN_Application::load()->name), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <form action="<%= xnhtmlentities($this->_buildUrl('authorization', 'doSignUpNingUser', array('target' => $this->target, 'groupToJoin' => $this->groupToJoin))) %>" method="post" class="xg_module_body xg_lightborder">
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
                            <dt><label for="signup_email"><big><strong><%= xg_html('EMAIL_ADDRESS'); %></strong></big></label></dt>
                            <dd><%= $this->form->text('emailAddress', 'id="signup_email" class="textfield large" size="20"') %></dd>
                            <dd>
                                <%= xg_html('YOU_ARE_SIGNING_UP_WITH') %><br />
                                <%= xg_html('NOW_ENTER_PASSWORD') %><br />
                                <small><a href="<%= xnhtmlentities($this->_buildUrl('authorization', 'requestPasswordReset', array('previousUrl' => XG_HttpHelper::currentUrl()))) %>"><%= xg_html('FORGOT_YOUR_PASSWORD') %></a></small>
                            </dd>
                        </dl>
                        <dl>
                            <dt><label for="signup_password"><big><strong><%= xg_html('PASSWORD') %></strong></big></label></dt>
                            <dd><%= $this->form->password('password', 'id="signup_password" class="password large" size="20"') %></dd>
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
    if (document.cookie.indexOf('xg_cookie_check') < 0) {
        document.getElementById('cookie_check').style.display = 'block';
    }
</script>
<?php xg_footer(null, array('displayFooter' => false)) ?>
