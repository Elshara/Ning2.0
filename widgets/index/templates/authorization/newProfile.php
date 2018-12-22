<?php xg_header(null, $title = xg_text('CREATE_YOUR_PROFILE', XN_Application::load()->name), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true)); ?>
<?php XG_App::ningLoaderRequire('xg.index.authorization.newProfile'); ?>
<div id="xg_body">
            <?php
            ob_start();
            W_Cache::getWidget('profiles')->dispatch('profile', 'profileQuestionForm');
            $profileQuestionForm = trim(ob_get_contents());
            ob_end_clean(); ?>
            <form id="profile_form" action="<%= xnhtmlentities($this->_buildUrl('authorization', 'createProfile', array('target' => $this->target, 'newNingUser' => $this->newNingUser, 'groupToJoin' => $this->groupToJoin))) %>" method="post" enctype="multipart/form-data">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <div class="xg_module xg_lightborder">
                    <div class="xg_module_body pad">
                        <?php if ($this->unfinishedProfile) {?>
                            <div class="errordesc"><p><%=xg_html('YOUR_SIGN_UP_PROCESS')%></p></div>
                        <?php }?>
                        <h3><%= xnhtmlentities($title) %></h3>
                        <p><%= xg_html('ONE_MORE_STEP', xnhtmlentities(XN_Application::load()->name), 'class="private"') %></p>
                        <p><small><%=xg_html('INDICATES_REQUIRED_QUESTION') %></small></p>
                        <dl id="profile_form_notify" class="errordesc msg" <%= $this->errors ? '' : 'style="display: none"' %>>
                            <?php
                            if ($this->errors) { ?>
                                <dt><%= xg_html('A_PROBLEM_OCCURRED') %></dt>
                                <dd>
                                    <ol>
                                        <?php
                                        foreach ($this->errors as $error) { ?>
                                            <li><%= $error %></li>
                                        <?php
                                        } ?>
                                    </ol>
                                </dd>
                            <?php
                            } ?>
                        </dl>
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
                                'indicateRequiredFields' => true)));
                        if ($profileQuestionForm) { ?>
                            <fieldset class="nolegend profile">
                                <%= $profileQuestionForm %>
                            </fieldset>
                        <?php
                        } ?>
                        <p class="buttongroup">
                            <?php if ($this->unfinishedProfile) {?>
                                <div class="right">
                                    <input class="button" type="button" value="<%=xg_html('SIGN_OUT_TITLE')%>" onclick="window.location='<%=qh(XG_HttpHelper::signOutUrl())%>'"/>
                                </div>
                            <?php }?>
                            <input type="submit" class="button left" value="<%= $this->unfinishedProfile ? xg_html('CONTINUE') : xg_html('JOIN') %>" />
                        </p>
                    </div>
                </div>
            </form>
</div>
<script type="text/javascript">document.getElementById('fullname').focus();</script>
<?php xg_footer(null, array('displayFooter' => false)) ?>
