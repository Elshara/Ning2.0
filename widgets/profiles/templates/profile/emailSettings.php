<?php xg_header('profile', $title = xg_text('MY_SETTINGS') . ' - ' . xg_text('EMAIL'), null); ?>
<?php XG_App::ningLoaderRequire('xg.profiles.profile.emailSettings'); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
            <form id="settings_form" action="<%= $this->_buildUrl('profile', 'emailSettings'); %>" method="post">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <div class="xg_module">
                    <div class="xg_module_body pad">
                        <?php $this->renderPartial('fragment_settingsNavigation', '_shared', array('selected' => 'email')); ?>
                        <div class="right page_ticker_content">
                            <h3><%= xg_html('EMAIL') %></h3>
                            <fieldset class="nolegend">
                                <?php
                                if ($this->successMessage) { ?>
                                    <p class="success"><%= $this->successMessage %></p>
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
                                <p><%= xg_html('WHAT_NETWORK_ACTIVITY_EMAIL_FOR') %></p>
                                <fieldset>
                                    <div class="legend"><%= xg_html('NETWORK_MESSAGES') %></div>
                                    <ul class="options">
                                        <li><label><%= $this->form->checkbox('emailNewMessagePref','Y','class="checkbox email-optin"') %><%= xg_html('MESSAGES_SENT_TO_ME') %></label></li>
                                        <li><label><%= $this->form->checkbox('emailGroupBroadcastPref','Y','class="checkbox email-optin"') %><%= xg_html('MESSAGES_SENT_TO_GROUPS') %></label></li>
                                        <?php
                                        if ($this->enabledModules['events']) { ?>
                                            <li><label><%= $this->form->checkbox('emailEventBroadcastPref','Y','class="checkbox email-optin"') %><%= xg_html('MESSAGES_SENT_TO_EVENTS') %></label></li>
                                        <?php
                                        } ?>
                                        <li><label><%= $this->form->checkbox('emailSiteBroadcastPref','Y','class="checkbox email-optin"') %><%= xg_html('MESSAGES_SENT_TO_NETWORK') %></label></li>
                                        <li><label><%= $this->form->checkbox('emailAllFriendsPref','Y','class="checkbox email-optin"') %><%= xg_html('MESSAGES_SENT_TO_ALL_FRIENDS') %></label></li>
                                        <?php
                                        if ($this->enabledModules['opensocial']) { ?>
                                            <li><label><%= $this->form->checkbox('emailViaApplicationsPref','Y','class="checkbox email-optin"') %><%= xg_html('ALERTS_SENT_VIA_APPLICATIONS') %></label></li>
                                        <?php
                                        } ?>
                                    </ul>
                                </fieldset>
                                <fieldset>
                                    <div class="legend"><%= xg_html('MEMBER_ACTIVITY') %></div>
                                    <ul class="options">
                                        <li><label><%= $this->form->checkbox('emailFriendRequestPref','Y','class="checkbox email-optin"') %><%= xg_html('FRIEND_REQUESTS') %></label></li>
                                        <li><label><%= $this->form->checkbox('emailInviteeJoinPref','Y','class="checkbox email-optin"') %><%= xg_html('PEOPLE_IVE_INVITED_JOIN') %></label></li>
                                        <?php
                                        if ($_GET['test_moderated_notify_checkbox'] || (XG_App::contentIsModerated() && ! XG_SecurityHelper::userIsAdmin())) { ?>
                                            <li><label><%= $this->form->checkbox('emailModeratedPref','each','class="checkbox email-optin"') %><%= xg_html('THINGS_IVE_ADDED_APPROVED') %></label></li>
                                        <?php
                                        } ?>
                                        <li><label><%= $this->form->checkbox('emailCommentApprovalPref','Y','class="checkbox email-optin"') %><%= xg_html('I_HAVE_NEW_COMMENTS') %></label></li>
                                        <?php
                                        if (XG_SecurityHelper::userIsAdmin()) { ?>
                                            <li><label><%= $this->form->checkbox('emailApprovalPref','Y','class="checkbox email-optin"') %><%= xg_html('I_HAVE_NEW_NETWORK_CONTENT') %></label></li>
                                            <li><label><%= $this->form->checkbox('emailAdminMessagesPref','Y','classß="checkbox email-optin"') %> <%= xg_html('FEEDBACK_AND_PROBLEM_REPORTS') %></label></li>
                                        <?php
                                        } ?>
                                    </ul>
                                </fieldset>
                                <fieldset>
                                    <div class="legend"><%= xg_html('FOLLOWING_DISCUSSIONS_AND_BLOG_POSTS') %></div>
                                    <ul class="options">
                                        <li><label><%= $this->form->checkbox('emailActivityPref','activity', 'class="checkbox email-optin"') %><%= xg_html('FOLLOW_DISCUSSIONS_AND_BLOG_POSTS_I_ADD') %></label><br/>
                                        <li><label><%= $this->form->checkbox('autoFollowOnReplyPref','Y', 'class="checkbox email-optin email-follow"') %><%= xg_html('FOLLOW_DISCUSSIONS_AND_BLOG_POSTS_I_REPLY_TO') %></label></li>
                            <?php XG_App::ningLoaderRequire('xg.index.bulk'); ?>
                                        <small class="clear_follow_list"><a dojoType="PostLink" class="button" style="font-size:0.95em;" href="#"
                                            _url="<%= $this->_buildUrl('profile', 'clearFollowList',  array('clearFollowList' => true)) %>"
                                            _confirmTitle="<%= xg_html('CLEAR_MY_FOLLOW_LIST') %>"
                                            _confirmQuestion="<%= xg_html('ARE_YOU_SURE_STOP_RECEIVING_EMAILS_DISCUSSIONS_BLOG_POSTS',  xg_html('OK')) %>"
                                            ><%= xg_html('CLEAR_MY_FOLLOW_LIST') %></a></small><br />
                                        <small><%= xg_html('IF_YOU_CLICK_X_YOU_WILL_STOP_FOLLOWING', xg_html('CLEAR_MY_FOLLOW_LIST'))%></small>
                                        </li>
                                    </ul>
                                </fieldset>
                                <ul class="options never_email">
                                    <li><label><%= $this->form->checkbox('emailNeverPref','Y','class="checkbox"') %><strong><%= xg_html('NONE_I_DO_NOT_WANT',xnhtmlentities(XN_Application::load()->name)) %></strong></label></li>
                                </ul>
                                <p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('SAVE') %>" /></p>
                            </fieldset>
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
