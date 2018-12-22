<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('ABOUT_X', xnhtmlentities($this->appName)));
W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php'); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <%= xg_headline(xg_html('ABOUT_X', $this->appName),
                        array('byline1Html' => xg_html('VIEW_ALL_APPLICATIONS', 'href="' .  qh($this->_buildUrl('application', 'list')) . '"' ) . '<a href="' . qh(OpenSocial_LinkHelper::getMyApplicationsLink()) . '">' . xg_html('MY_APPLICATIONS') . '</a>')) %>
        <?php if (! $this->appDetails || ! $this->appDetails['approved']) { ?>
            <p class="notification"><%= xg_html('THIS_APPLICATION_HAS_NOT_BEEN_APPROVED') %></p>
        <?php } ?>
        <div class="xg_column xg_span-11">
            <div class="xg_module module_application">
                <div class="xg_module_body">
                    <?php $this->renderPartial('fragment_errorMsg', 'application'); ?>
                    <?php if($this->gadgetPrefs['description']) { ?>
                        <p><%= xnhtmlentities($this->gadgetPrefs['description']) %></p>
                    <?php } else { ?>
                        <p><em><%= xg_html('THERE_IS_NO_ADDITIONAL_INFORMATION') %></em></p>
                    <?php }
                    if ($this->gadgetPrefs['screenshot']) { ?>
                        <p class="item_screenshot"><img src="<%= xnhtmlentities($this->gadgetPrefs['screenshot']) %>" /></p>
                    <?php } ?>
                </div>
                <div class="xg_module_foot">
                    <?php if ($this->gadgetPrefs['ningApplication'] == "1") { ?>
                        <p><%= xg_html('THIS_APPLICATION_WAS_DEVELOPED_NING', 'href="' . xnhtmlentities($this->reportUrl) .'"') %></p>
                    <?php } else { ?>
                        <p><%= xg_html('THIS_APPLICATION_WAS_DEVELOPED', 'href="' . xnhtmlentities($this->reportUrl) .'"') %></p>
                    <?php } ?>
                </div>
            </div>
            <?php if ($this->showStats) {
                $this->renderPartial('fragment_reviews', 'application', array('appUrl' => $this->appUrl));
            } ?>
        </div>
        <?php if ($this->showStats) { ?>
            <div class="xg_column xg_span-5 xg_last">
                <div class="xg_module">
                    <div class="xg_module_body" id="xg_opensocial_about_options">
                        <?php W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php');
                        if ($this->currentUserHasApp) { ?>
                            <p><strong><%= xg_html('YOUVE_ADDED_THIS_APPLICATION') %></strong></p>
                            <p><%= xg_html('VIEW_APPLICATION_LINK', 'class="desc view-application" href="' . qh($this->_buildUrl('application', 'show', array('appUrl' => $this->appUrl, 'owner' => $this->_user->screenName))) . '"') %></p>
                            <p><?php $this->renderPartial('fragment_removeAppLink', '_shared', array('appUrl' => $this->appUrl)); ?></p>
                        <?php } else if (OpenSocial_LinkHelper::showAboutPageAddLink($this->appUrl, XN_Profile::current()->screenName)) { ?>
                            <p class="item_add-button"><?php $this->renderPartial('fragment_addAppLink', '_shared', array('appUrl' => $this->appUrl, 'cssClass' => 'button')); ?></p>
                        <?php } ?>
                    </div>
          		    <div class="xg_module_body">
                        <ul class="nobullets last-child">
          		        <?php if ($this->numMembers) { ?>
                                <li>
                                    <strong><%= xg_html('STATS_COLON') %></strong>
                                    <p><%= xg_html('N_MEMBERS_ADDED_THIS_APPLICATION', $this->numMembers, 'href="' . xnhtmlentities($this->membersUrl) . '"') %></p>
                                <?php if ($this->numFriends && XN_Profile::current()->isLoggedIn()) { ?>
                                    <p><%= xg_html('N_FRIENDS_ADDED_THIS_APPLICATION', $this->numFriends, 'href="' . xnhtmlentities($this->friendsUrl) . '"') %></p>
                                <?php } ?>
                            </li>
                        <?php } ?>
                        <?php if ($this->author) { ?>
                            <li>
                                <strong><%= xg_html('DEVELOPER_COLON') %></strong>
                                <p><%= ($this->authorLink ? xg_html('DEVELOPER_LINK', 'href="' . xnhtmlentities($this->authorLink) . '"', xnhtmlentities($this->author)) : xnhtmlentities($this->author)) %></p>
                            </li>
                        <?php } ?>
                            <li>
                                <strong><%= xg_html('CATEGORY_COLON') %></strong>
                                <p><%= xg_html('CATEGORY_LINK', 'href="' . xnhtmlentities(W_Cache::getWidget('opensocial')->buildUrl('application', 'list', array('category' => $this->category))) . '"', xg_html(xnhtmlentities($this->category))) %></p>
                            </li>
                            <li class="last-child">
                                <strong><%= xg_html('RATING_COLON') %></strong>
                                <p class="last-child" id="overallStarRating"><%= xg_rating_image($this->avgRating) %></p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><!--/xg_column-->
        <?php } ?>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this); ?>
    </div>
</div>

<?php xg_footer(); ?>
