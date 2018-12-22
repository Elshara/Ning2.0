<?php
if ($this->displayFooter && $this->hideLinks) { ?>
        <div id="footer">
          <p>
            <%= xg_html('BROUGHT_TO_YOU_BY', qh($this->app->name), qh(xg_username($this->ownerProfile)), xg_date('Y')) %> |
            <a href="mailto:reportanissue@ning.com"><%= xg_html('REPORT_AN_ISSUE') %></a> |
            <a href="#"><%= xg_html('VIEW_REGULAR_VERSION_OF_THIS_PAGE') %></a>
          </p>
        </div><!--/footer-->
<?php
} elseif ($this->displayFooter) {
    /* BAZ-1374 -- don't link to the creator's profile if the current user can't see it */
    if (XG_App::everythingIsVisible() || ($this->_user->isLoggedIn() && User::isMember($this->_user))) {
        $creatorLink = xg_userlink($this->ownerProfile);
    } else {
        $creatorLink = xnhtmlentities(xg_username($this->ownerProfile));
    } ?>
        <div id="footer">
          <p>
            <%= xg_html('COPYRIGHT_CREATED_BY', xg_date('Y'), $creatorLink) %>
          </p>
          <p>
<?php if ($this->_user->isLoggedIn()) { ?>
            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
            <a href="<%= xnhtmlentities(XG_AuthorizationHelper::signOutUrl(XG_HttpHelper::currentUrl())) %>"><%= xg_html('SIGN_OUT_TITLE') %></a> |
<?php } else { ?>
            <a href="<%= xnhtmlentities(XG_HttpHelper::signInUrl()) %>"><%= xg_html('SIGN_IN') %></a> |
<?php }?>
            <?php if (!XG_SecurityHelper::userIsAdmin()) { ?>
            <a <%= XG_JoinPromptHelper::promptToJoin(XG_Browser::browserUrl('desktop', $this->_widget->buildUrl('index', 'report'))) %>><%= xg_html('REPORT_AN_ISSUE') %></a> |
            <?php } ?>
			<a href="<%= xnhtmlentities($this->_widget->buildUrl('authorization', 'privacyPolicy')) %>"><%= xg_html('PRIVACY') %></a> |
            <a href="<%= xnhtmlentities($this->_widget->buildUrl('authorization', 'termsOfService')) %>"><%= xg_html('TERMS_OF_SERVICE') %></a>
          </p>
		<?php if ($this->regularPageUrl) {?>
            <a href="<%= qh($this->regularPageUrl) %>"><%= xg_html('VIEW_REGULAR_VERSION_OF_THIS_PAGE') %></a>
		<?php }?>
        </div><!--/footer-->
<?php
} ?>
        <?php
        if ($this->contentClass) { ?>
            </div>
        <?php
        } ?>
    </div><!--/container-->
    <?php
    if ($this->notification) { ?>
    <div id="notification">
    <div class="msg success">
        <a href="#" class="close" onclick="this.parentNode.parentNode.style.visibility='hidden';">X</a>
        <h2><%= xg_html('SUCCESS') %></h2>
        <p><%= $this->notification %></p>
    </div>
    </div>
    <?php
    } ?>
    <!-- QUICK ADD -->
    <div id="quick_add_box" class="overlay" style="display:none;">
        <a class="overlay-close" href="#" onclick="javascript:void(0);">X</a>
        <ul>
            <li><a href="<%= xnhtmlentities(W_Cache::getWidget('forum')->buildUrl('topic','new')) %>"><%= xg_html('START_A_DISCUSSION') %></a></li>
            <li><a href="#" id="photo_upload_link" onclick="javascript:void(0);"><%= xg_html('UPLOAD_A_PHOTO') %></a></li>
            <li><a href="<%= xnhtmlentities($this->_widget->buildUrl('invitation', 'new', array('previousUrl' => XG_HttpHelper::currentUrl()))) %>"><%= xg_html('INVITE_FRIENDS') %></a></li>
        </ul>
    </div><!--/#overlay-->
    <div id="photo_upload_box" class="overlay" style="display:none;">
        <a class="overlay-close" href="#" onclick="javascript:void(0);">X</a>
        <?php
        $emailUrl = xnhtmlentities(xg_mailto_url($this->userProfile->uploadEmailAddress, xg_text('UPLOADING_PHOTOS_TO_X', $this->app->name), xg_text('ATTACH_PHOTOS_YOU_WANT_TO_UPLOAD')));
        ?>
        <div>
          <p><%= xg_html('ADD_PHOTOS_FROM_IPHONE_BY', $this->app->name) %></p>
          <p><a href="<%= $emailUrl %>"><%= xnhtmlentities($this->userProfile->uploadEmailAddress) %></a></p>
        </div>
    </div>
</body>
</html>
