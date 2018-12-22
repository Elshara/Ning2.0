<?php
/**
 * A link to start/stop following new topics
 */
$this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
$this->_widget->includeFileOnce('/lib/helpers/Forum_NotificationHelper.php');
XG_App::ningLoaderRequire('xg.shared.FollowLink'); ?>
<p class="right">
    <a href="#" dojoType="FollowLink"
        _isFollowed="<%= Forum_NotificationHelper::currentUserIsFollowingNewTopics() ? 1 : 0 %>"
        _addUrl="<%= xnhtmlentities($this->_buildUrl('notification', 'startFollowingNewTopics', array('xn_out' => 'json'))) %>"
        _removeUrl="<%= xnhtmlentities($this->_buildUrl('notification', 'stopFollowingNewTopics', array('xn_out' => 'json'))) %>"
        _addLinkText="<%= xg_html('NOTIFY') %>"
        _removeLinkText="<%= xg_html('STOP_NOTIFYING') %>"
        _addDescription="<%= xg_html('EMAIL_WHEN_NEW_DISCUSSIONS') %>"
        _removeDescription="<%= xg_html('DO_NOT_EMAIL_WHEN_NEW_DISCUSSIONS') %>"
        ></a>
</p>
