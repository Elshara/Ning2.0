<?php
$myFriends = $this->_user->screenName == $this->screenName;
$title = $myFriends ? xg_text('MY_FRIENDS') : xg_text('XS_FRIENDS', Photo_FullNameHelper::fullName($this->screenName)); ?>

<?php xg_header(W_Cache::current('W_Widget')->dir, $title); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline($title, array('count' => $this->totalUsers))%>
            <?php
            if (count($this->users) == 0 && !isset($this->searchFor) && $myFriends) { ?>
                <div class="xg_module">
                    <div class="xg_module_head notitle"></div>
                    <div class="xg_module_body">
                        <?php
                        if (Photo_PrivacyHelper::canCurrentUserSeeInviteLinks()) { ?>
                            <p><%= xg_html('INVITE_YOUR_FRIENDS_TO', xnhtmlentities(XN_Application::load()->name)) %></p>
                            <p class="buttongroup">
                                <a href="/invite" class="button"><%= xg_html('INVITE') %></a>
                            </p>
                        <?php
                        } else { ?>
                            <p><%= xg_html('YOU_HAVE_NOT_ADDED_FRIENDS', xnhtmlentities(XN_Application::load()->name)) %></p>
                        <?php
                        } ?>
                    </div>
                </div>
            <?php
            } else {
                $this->renderPartial('fragment_list', 'user', array(
                        'searchLabel' => xg_text('SEARCH_FRIENDS'),
                        'screenName' => $this->screenName,
                        'showViewAllPeopleLink' => true,
                        'paginationUrl' => Photo_HtmlHelper::addParamToUrl($this->pageUrl, 'sort', $this->sort['code']),
                        'sort' => $this->sort,
                        'page' => $this->page,
                        'numPages' => $this->numPages,
                        'searchFor' => $this->searchFor,
                        'users' => $this->users));
            } ?>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.photo.index._shared'); ?>
<?php xg_footer(); ?>
