<?php
$this->_widget->includeFileOnce('/lib/helpers/Groups_HtmlHelper.php');
$menu = Groups_HtmlHelper::subMenu($this->_widget,'none');
?>
<ul class="navigation">
	<?php foreach ($menu as $k=>$v) {
		echo '<li><a href="'.qh($v['url']).'">'.$v['name'].'</a></li>';
	} ?>
    <li class="right">
<?php if (Groups_SecurityHelper::currentUserCanSeeJoinLinks($this->group) && ! Group::userIsInvited($this->group)) {
    XG_App::ningLoaderRequire('xg.shared.PostLink'); ?>
        <a class="desc add" dojoType="PostLink" href="#" _url="<%= xnhtmlentities($this->_buildUrl('group','join', array('id' => $this->group->id))) %>"><%= xg_html('JOIN_GROUP', xnhtmlentities($this->group->title)) %></a>
<?php } elseif (Groups_SecurityHelper::currentUserCanSendInvites($this->group)) { ?>
	<a href="<%= xnhtmlentities($this->_buildUrl('invitation','new', array('groupId' => $this->group->id))) %>" class="desc add"><%= xg_html('INVITE_MORE_PEOPLE') %></a>
<?php }?>
    </li>
</ul>
<?php
    $contributor = XG_Cache::profiles($this->group->contributorName);
?>
<%= xg_headline($this->group->title, array(
				'avatarUser' => $contributor,
				'byline1Html' => xg_html('CREATED_BY_USER', xg_userlink($contributor)),
				'byline2Html' => xg_message_and_friend_links($this->group->contributorName, $this->_buildUrl('group', 'listForContributor', array('user' => $contributor->screenName)), xg_text('VIEW_GROUPS')))) %>
