<?php xg_header(null, $title = xg_text('SIGN_IN_TO_APPNAME', XN_Application::load()->name), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <form action="<%= xnhtmlentities($this->_buildUrl('authorization', 'doSignIn', array('target' => $this->target, 'groupToJoin' => $this->groupToJoin))) %>" method="post" class="xg_module_body xg_lightborder">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <?php
                    if ($this->showInvitationExpiredMessage) { ?>
                        <h3><%= xg_html('YOUR_INVITATION_HAS_EXPIRED') %></h3>
                        <p>
                            <%= xg_html('SORRY_INVITATION_EXPIRED', xnhtmlentities(XN_Application::load()->name)) %>
                        </p>
                    <?php
                    } elseif (XG_App::appIsPrivate()) { ?>
                        <h2><%= xnhtmlentities(XN_Application::load()->name) %></h2>
                        <p class="small description"><%= xnhtmlentities(XG_MetatagHelper::appDescription()) %></p>
                    <?php
                    } elseif (! $this->showSignUpLink) { ?>
                        <h3><%= xg_html('MEMBERSHIP_TO_APPNAME_BY_INVITATION_ONLY', xnhtmlentities(XN_Application::load()->name)) %></h3>
                        <p><%= xg_html('SORRY_BUT_ADMINISTRATOR_REQUIRES_INVITATION', XN_Application::load()->name) %></p>
                    <?php
                    } ?>

                    <?php if (XG_App::appIsPrivate()) { ?>
                        <h3><%= xg_html('SIGN_IN') %></h3>
                        <?php if (XG_App::allowJoinByAll()) { ?>
                            <p class="small"><%= xg_html('IF_YOURE_NOT_A_MEMBER_SIGN_UP', 'href="' . xnhtmlentities(XG_AuthorizationHelper::signUpUrl($this->target, $this->groupToJoin)) . '"') %></p>
                        <?php } else { ?>
                            <p class="small"><%= xg_html('THIS_NETWORK_IS_BY_INVITATION_ONLY') %></p>
                        <?php } ?>
                    <?php } else { ?>
                        <h3 id="ningid"><%= xnhtmlentities($title) %></h3>
                    <?php } ?>
                    <div class="errordesc" <%= $this->errors ? '' : 'style="display: none"' %>>
                        <h4><%= xg_html('PROBLEM_SIGNING_IN') %></h4>
                        <p class="last-child"><%= reset($this->errors) %></p>
                    </div>
                    <div class="errordesc" style="display:none" id="cookie_check">
                        <p class="last-child"><%= xg_html('PLEASE_ENABLE_COOKIES', 'href="http://help.ning.com/cgi-bin/ning.cfg/php/enduser/std_adp.php?p_faqid=3217" target="_new"') %></p>
                    </div>
                    <fieldset class="nolegend account clear" id="signin">
                        <dl>
                            <dt<%= $this->errors['emailAddress'] ? ' class="error"' : '' %>><label for="signin_email"><big><strong><%= xg_html('EMAIL_ADDRESS'); %></strong></big></label></dt>
                            <dd<%= $this->errors['emailAddress'] ? ' class="error"' : '' %>><%= $this->form->text('emailAddress', 'id="signin_email" class="textfield large" size="20"') %></dd>
                        </dl>
                        <dl>
                            <dt<%= $this->errors['password'] ? ' class="error"' : '' %>><label for="signin_password"><big><strong><%= xg_html('PASSWORD') %></strong></big></label></dt>
                            <dd<%= $this->errors['password'] ? ' class="error"' : '' %>><%= $this->form->password('password', 'id="signin_password" class="password large" size="20"') %></dd>
                        </dl>
                        <dl>
                            <?php
                            if ($this->showSignUpLink) { ?>
                                <dd><%= xg_html('SIGN_IN_OR_SIGN_UP', 'type="submit" class="button"', 'href="' . xnhtmlentities(XG_AuthorizationHelper::signUpUrl($this->target, $this->groupToJoin)) . '"') %></dd>
                            <?php
                            } else { ?>
                                <dd><input type="submit" class="button" value="<%= xg_html('SIGN_IN') %>" /></dd>
                            <?php
                            } ?>
                            <dd id="helplinks" class="clear">
                                <input type="checkbox" name="tosAgree" id="tosAgree" value="true" checked="checked" class="checkbox" /> 
                                <div><%= xg_html('BY_SIGNING_IN_YOU_AGREE_AMENDED', 'href="' . xnhtmlentities($this->_buildUrl('authorization', 'termsOfService', array('noBack' => 1))) . '" target="_blank"', 'href="' . xnhtmlentities($this->_buildUrl('authorization', 'privacyPolicy', array('noBack' => 1))) . '" target="_blank"') %></div>
                                <a href="<%= xnhtmlentities($this->_buildUrl('authorization', 'requestPasswordReset', array('previousUrl' => XG_HttpHelper::currentUrl()))) %>"><%= xg_html('FORGOT_YOUR_PASSWORD') %></a><br />
                                <a href="<%= xnhtmlentities($this->_buildUrl('authorization', 'problemsSigningIn', array('previousUrl' => XG_HttpHelper::currentUrl()))) %>"><%= xg_html('PROBLEMS_SIGNING_IN') %></a>
                            </dd>
                        </dl>
                    </fieldset>
                    <?php if (XG_App::appIsPrivate()) { $this->_widget->dispatch('authorization', 'footerPrivateSignIn'); } ?>
                </form>
                <?php if (! XG_App::appIsPrivate()) { $this->_widget->dispatch('authorization', 'footer'); } ?>
    </div>
</div>
<script type="text/javascript">
    document.getElementById('signin_email').focus();
    document.getElementById('signin_email').select();
    if (document.cookie.indexOf('xg_cookie_check') < 0) {
        document.getElementById('cookie_check').style.display = 'block';
    }
</script>
<?php xg_footer(null, array('displayFooter' => false)) ?>
