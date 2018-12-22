<?php
$myPage = $this->embed->isOwnedByCurrentUser();
if ($myPage && $this->hideLinks) {
    return;
}
XG_App::ningLoaderRequire('xg.shared.PostLink');
W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
?>
<div class="xg_module module_user_summary">
    <?php
    $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
    $size = Profiles_UserHelper::SMALL_BADGE_AVATAR_SIZE; ?>
    <img class="photo" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($this->profile,$size,$size))%>" width="<%= $size %>" height="<%= $size %>" alt="" />
    <div class="xg_module_body profile vcard">
        <dl class="last-child">
            <dt class="fn">
                <span class="fn"><%= xnhtmlentities(xg_username($this->profile)) %></span>
                <?php
                    $additionalInfo = xg_age_and_location($this->profile);
                    if (mb_strlen($additionalInfo)) {
                        echo '<span>' . $additionalInfo . '</span>';
                    }
                    XG_App::includeFileOnce('/lib/XG_PresenceHelper.php');
                    if (XG_PresenceHelper::canShowPresenceIndicator($this->profile) && $this->profile->screenName != XN_Profile::current()->screenName) {
                        echo '<span class="desc online">'.xg_html('ONLINE_NOW').'</span>';
                    }

                    if ($myPage) {
                        $key = (XG_App::membersCanCustomizeTheme() ? 'CHANGE_MY_PHOTO_OR_THEME' : 'CHANGE_MY_PHOTO'); ?>
                        <span><%= xg_html($key, 'href="' . xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('settings', 'editProfileInfo')) . '"',
                                'href="' . xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('appearance', 'edit')) . '"') %></span>
                    <?php } ?>
            </dt>
            <?php
            $currUser = User::load(XN_Profile::current());
            if (!$myPage) {
                $link = xg_add_as_friend_link($this->profile->screenName, $this->friendStatus, 'desc addfriend', 'friend-pending desc', '');
                if ($link) {
                    echo '<dd id="relationship">' . $link . '</dd>';
                }
                if (($this->friendStatus === XN_Profile::FRIEND) || XG_SecurityHelper::userIsAdmin()) {
                    echo '<dd>' . xg_send_message_link($this->profile->screenName, $this->friendStatus) . '</dd>';
                }
            }
            if (Profiles_PrivacyHelper::canCurrentUserSeeShareLinks()) {
                $shareUrl = W_Cache::getWidget('main')->buildUrl('sharing', 'share', array(
                    'id' => urlencode(User::load($this->profile->screenName)->id),
                ));
                echo '<dd><a class="desc share" href="'. qh($shareUrl) .'">'.xg_html('SHARE').'</a>';
            }
            if ($this->friendStatus == XN_Profile::FRIEND) {
                XG_App::ningLoaderRequire('xg.profiles.embed.unfriend'); ?>
                <dd><a class="desc removefriend" href="#" _isProfilePage="true" _username="<%= xnhtmlentities(xg_username($this->profile)) %>"
                    _url="<%= $this->_buildUrl('profile','unfriend',array('xn_out' => 'json', 'user' => $this->profile->screenName)) %>"
                    dojoType="UnfriendLink"><%= xg_html('REMOVE_AS_FRIEND') %></a></dd>
                <?php
            }
            if (!$myPage && XN_Profile::current()->isLoggedIn()) {
                $showBlockLink = (($this->friendStatus !== XN_Profile::BLOCKED) && !$this->isBlocked);
                $username      = xnhtmlentities(xg_username($this->profile));
                $blockMsgUrl   = $this->_buildUrl('profile',"blockMessage",array('json' => 'yes', 'user' => $this->profile->screenName));
                $unblockMsgUrl = $this->_buildUrl('profile',"unblockMessage",array('json' => 'yes', 'user' => $this->profile->screenName));
                XG_App::ningLoaderRequire('xg.profiles.embed.blocking');?>
                <dd dojoType="BlockingLink">
                    <a id="xj_block_messages" <%=($showBlockLink) ? "" : "style=\"display:none\"" %>
                        class="desc msgblock" href="#" _isProfilePage="true"
                        _url="<%= $blockMsgUrl %>" _confirmTitle="<%=xg_html('BLOCK_MESSAGES_TITLE', $username)%>"
                        _confirmMessage="<%=xg_html('BLOCK_MESSAGES_CONFIRM')%>"><%= xg_html('BLOCK_MESSAGES') %></a>
                    <a id="xj_unblock_messages" <%=($showBlockLink) ? "style=\"display:none\"" : "" %>
                        class="desc msgunblock" href="#" _isProfilePage="true"
                        _url="<%= $unblockMsgUrl %>" _confirmTitle="<%=xg_html('UNBLOCK_MESSAGES_TITLE', $username)%>"
                        _confirmMessage="<%=xg_html('UNBLOCK_MESSAGES_CONFIRM')%>"><%= xg_html('UNBLOCK_MESSAGES') %></a>
                </dd>
            <?php } ?>
            <?php
                if ($myPage && XG_App::openSocialEnabled()) { ?>
                        <dd><span><a href="<%= xnhtmlentities(W_Cache::getWidget('opensocial')->buildUrl('application', 'list')) %>" class="desc add"><%= xg_html('ADD_APPLICATIONS') %></a></span></dd>
            <?php } ?>
        </dl>
    </div>
    <div class="xg_module_body">
        <ul class="nobullets last-child">
            <?php foreach ($this->profileLinks as $v) {
                if ($v['count']) {
                    echo '<li><a href="'.qh($v['viewUrl']).'">'.qh($v['name']).'</a> ('.$v['count'].')</li>';
                } elseif ($myPage) {
                    echo '<li><a href="'.qh($v['addUrl']).'">'.qh($v['name']).'</a></li>';
                } else {
                    echo '<li class="disabled">'.qh($v['name']).'</li>';
                }
            } ?>
        </ul>
    </div>
</div>
<?php
if (XG_SecurityHelper::userIsAdmin()) { ?>
    <div class="xg_module">
        <div class="xg_module_head">
            <h2><%= xg_html('ADMIN_OPTIONS') %></h2>
        </div>
        <div class="xg_module_body">
            <ul class="nobullets last-child">
                <li>
                    <?php
                    XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
                    W_Cache::getWidget('main')->dispatch('promotion','link',array($this->user)); ?>
                </li>
                <?php if ($this->canBan) { ?>
                    <li>
                        <?php XG_App::ningLoaderRequire('xg.index.bulk'); ?>
                        <a
                            dojoType="BulkActionLink"
                            title="<%= xg_html('BAN_USERNAME', xnhtmlentities(xg_username($this->profile))) %>"
                            _url="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('bulk','removeByUser', array('user' => $this->profile->screenName, 'xn_out' => 'json'))) %>"
                            _verb="<%= xg_html('OK') %>"
                            _confirmMessage="<%= xg_html('ARE_YOU_SURE_BAN_X_AND_CONTENT', xnhtmlentities(xg_username($this->profile))) %>"
                            _progressTitle="<%= xg_html('REMOVING_X', xnhtmlentities(xg_username($this->profile))) %>"
                            _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_CONTENT_DELETED_2', xnhtmlentities(xg_username($this->profile))) %>"
                            _successUrl="http://<%= $_SERVER['HTTP_HOST'] %>/"
                            href="#" class="desc ban-member"><%= xg_html('BAN_MEMBER_FROM_NETWORK') %></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
<?php } ?>
