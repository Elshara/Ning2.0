<?php
/**
 * @param $screenName
 * @param $friends
 * @param $showViewFriendsLink Whether to show a link to the person's Friends page
 */
$my = $this->_user->screenName == $screenName; ?>
<div class="xg_module">
    <div class="xg_module_body">
        <h3><%= $my ? xg_html('MY_FRIENDS') : xg_html('XS_FRIENDS', xnhtmlentities(Video_FullNameHelper::fullName($screenName))) %></h3>
        <?php
        foreach ($friends as $friend) { ?>
            <dl class="vcard">
                <dt><?php echo Video_HtmlHelper::avatar($friend->title, 54); ?>  <?php echo Video_HtmlHelper::linkedScreenName($friend->title); ?></dt>
                <dd>
                    <?php
                    if (Video_UserHelper::get($friend, 'videoCount')) { ?>
                        <a href="<%= xnhtmlentities($this->_buildUrl('user', 'show', array('screenName' => $friend->title))) %>"><%= xg_html('VIEW_VIDEOS') %></a>
                    <?php
                    } ?>
                </dd>
            </dl>
        <?php
        } ?>
    </div>
    <?php
    if ($showViewFriendsLink) { ?>
        <div class="xg_module_foot">
            <p class="right"><a href="<%= xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('friend','list',array('user' => $screenName))) %>"><%= $my ? xg_html('VIEW_ALL_MY_FRIENDS') : xg_html('VIEW_ALL_XS_FRIENDS', xnhtmlentities(Video_FullNameHelper::fullName($screenName))) %></a></p>
        </div>
    <?php
    } ?>
</div>