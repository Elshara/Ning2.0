<?php xg_header($this->tab, $title = xg_text('MY_FRIENDS')); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
		<?php $this->renderPartial('fragment_membersNavigation', '_shared') ?>
		<%= Profiles_HtmlHelper::friendsHeadline($title, $this->_user, $this->memberCount) %>
		<%= Profiles_HtmlHelper::friendsSearchForm() %>
        <div class="xg_module">
            <div class="xg_module_body body_members_main">
                <?php $this->_widget->includeFileOnce('/lib/helpers/Profiles_HtmlHelper.php'); ?>
				<%= Profiles_HtmlHelper::tabsHtml('sent', $this->memberCount) %>
                <?php
                if ($this->sentFriendRequestLimitExceeded) { ?>
                    <div class="errordesc clear xj_error_msg">
                        <p class="last-child">
                            <%= xg_html('REACHED_LIMIT_N_FRIEND_REQUESTS', XG_App::constant('Profiles_FriendHelper::SENT_FRIEND_REQUEST_LIMIT')) %>
                        </p>
                    </div>
                <?php
                }
                foreach ($this->friendRequests as $friendRequest) {
                    $profile = XG_Cache::profiles($friendRequest['screenName']); ?>
                    <div class="request xg_lightborder easyclear">
                        <p class="xg_lightfont">
                            <%= xg_avatar($profile, 64, 'xg_lightborder') %>
                            <%= xg_html('YOU_SENT_FRIEND_REQUEST', '<strong>' . xg_userlink($profile) . '</strong>') %>
                            <br/>
                            <small><%= xg_elapsed_time($friendRequest['date'], $showingMonth, xg_text('F_J'), xg_text('F_J_Y_AT_G_IA')) %></small>
                        </p>
                        <?php
                        if ($message = $this->friendRequestMessages[$profile->screenName]) { ?>
                            <blockquote><p><%= xg_html('QUOTED_TEXT', xnhtmlentities($message)) %></p></blockquote>
                            <?php
                        } ?>
                        <?php /* This form works with or without JavaScript [Jon Aquino 2008-06-10] */ ?>
                        <?php XG_App::ningLoaderRequire('xg.profiles.friendrequest.SentFriendRequest') ?>
                        <form dojoType="SentFriendRequest" _maxSentRequests="<%= $this->maxSentRequests %>" action="<%= xnhtmlentities($this->_buildUrl('friendrequest', 'process', array('screenName' => $profile->screenName, 'page' => $this->page))) %>" method="post">
                            <p class="last-child">
                                <input name="withdraw" type="submit" class="button" value="<%= xg_html('WITHDRAW') %>" />
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                            </p>
                        </form>
                    </div>
                <?php
                }
                if ($this->count > 1) { ?>
                    <p class="buttons">
                        <?php XG_App::ningLoaderRequire('xg.shared.PostLink'); ?>
                        <a dojoType="PostLink" href="#" _url="<%= xnhtmlentities($this->_buildUrl('friendrequest', 'process', array('withdrawAll' => 1))) %>"><%= xg_html('WITHDRAW_ALL_N', $this->count) %></a>
                    </p>
                <?php
                } ?>
                <%= XG_PaginationHelper::outputPagination($this->count, $this->pageSize); %>
            </div>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
