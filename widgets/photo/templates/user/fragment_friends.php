<?php
/**
 * @param $screenName
 * @param $friends
 */
$my = $this->_user->screenName == $screenName; ?>
<div class="xg_module">
    <div class="xg_module_body">
        <h3><%= $my ? xg_html('MY_FRIENDS') : xg_html('XS_FRIENDS', xnhtmlentities(Photo_FullNameHelper::fullName($screenName))) %></h3>
        <?php
        foreach ($friends as $friend) { ?>
            <dl class="vcard">
                <dt><?php echo Photo_HtmlHelper::avatar($friend->title, 54); ?>  <?php echo Photo_HtmlHelper::linkedScreenName($friend->title); ?></dt>
                <?php if (Photo_UserHelper::get($friend, 'photoCount')>0) { ?>

                <dd><a href="<%= xnhtmlentities($this->_buildUrl('user', 'show', array('screenName' => $friend->title))) %>"><%= xg_html('VIEW_PHOTOS') %></a></dd>
                <?php } ?>

            </dl>
        <?php
        } ?>
    </div>
    <?php if ($this->showMoreFriendsLink || $_GET['test_view_all_friends_link']){ ?>
    <div class="xg_module_foot">
        <p class="right"><a href="<%= User::quickFriendsUrl($screenName) %>"><%= $my ? xg_html('VIEW_ALL_MY_FRIENDS') : xg_html('VIEW_ALL_XS_FRIENDS', xnhtmlentities(Photo_FullNameHelper::fullName($screenName))) %></a></p>
    </div>
    <?php } ?>
</div>