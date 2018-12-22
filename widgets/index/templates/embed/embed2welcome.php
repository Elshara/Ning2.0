<?php
/*
 *	Renders the Green Welcome Box on the main page
 */
XG_App::ningLoaderRequire('xg.index.embed.WelcomeBox');

$mods = XG_ModuleHelper::getEnabledModules();
$isAdmin = XG_SecurityHelper::userIsAdmin();

$max = 3;
$links = array();

if (count($links) < $max && $mods['photo']) {
	$links[] = '<li><a href="#" module="photo" js="xg.index.quickadd.photo" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','photo')).'">'.
		'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/photo.gif').'" width="32" height="32" alt="" />' .
		xg_html('ADD_PHOTOS3').'</a></li>';
}
if (count($links) < $max && $mods['video']) {
	$links[] = '<li><a href="#" module="video" js="xg.index.quickadd.video" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','video')).'">'.
		'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/video.gif').'" width="32" height="32" alt="" />' .
		xg_html('ADD_A_VIDEO2').'</a></li>';
}
if (count($links) < $max && $mods['forum']) {
	$forum = W_Cache::getWidget('forum');
	W_Cache::push($forum); // Category::findAll refers to the W_Cache::current('W_Widget');
	$forum->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
    if (Forum_SecurityHelper::currentUserCanSeeAddTopicLinks()) {
		$links[] = '<li><a href="#" module="discussion" js="xg.index.quickadd.discussion" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','discussion')).'">'.
			'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/forum.gif').'" width="32" height="32" alt="" />' .
			xg_html('START_A_DISCUSSION2').'</a></li>';
	}
	W_Cache::pop($forum);
}
if (count($links) < $max && $mods['events']) {
	XG_App::includeFileOnce('/widgets/events/lib/helpers/Events_SecurityHelper.php');
	if (Events_SecurityHelper::currentUserCanCreateEvent()) {
		$links[] = '<li><a href="#" module="event" js="xg.index.quickadd.event" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','event')).'">'.
			'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/event.gif').'" width="32" height="32" alt="" />' .
			xg_html('ADD_AN_EVENT2').'</a></li>';
	}
}
if (count($links) < $max && $mods['music']) {
	$links[] = '<li><a href="#" module="music" js="xg.index.quickadd.music" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','music')).'">'.
		'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/music.gif').'" width="32" height="32" alt="" />' .
		xg_html('ADD_MUSIC3').'</a></li>';
}
if (count($links) < $max) {
	$links[] = '<li><a href="#" module="post" js="xg.index.quickadd.post" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','post')).'">'.
		'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/blog.gif').'" width="32" height="32" alt="" />' .
		xg_html('ADD_A_BLOG_POST').'</a></li>';
}
if (count($links) < $max && $mods['notes'] && $isAdmin) {
	$links[] = '<li><a href="#" module="note" js="xg.index.quickadd.note" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','note')).'">'.
		'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/note.gif').'" width="32" height="32" alt="" />' .
		xg_html('ADD_A_NOTE').'</a></li>';
}

if (count($links) < 2 && $isAdmin) {
	$links[] = '<li><a href="'.$this->_buildUrl('feature','add').'">'.
		'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/features.gif').'" width="32" height="32" alt="" />' .
		xg_html('ADD_FEATURES2').'</a></li>';
}

array_unshift($links, '<li><a href="#" module="invite" js="xg.index.quickadd.invite" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','invite')).'">'.
		'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/invite.gif').'" width="32" height="32" alt="" />' .
	xg_html('INVITE_FRIENDS2').'</a></li>');

array_push($links, $isAdmin
	? '<li><a href="'.$this->_buildUrl('admin','manage').'">'.
		'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/customize.gif').'" width="32" height="32" alt="" />' .
		xg_html('MAKE_IT_UNIQUE').'</a></li>'
	: '<li><a href="'.W_Cache::getWidget('profiles')->buildUrl('settings','editProfileInfo').'">'.
		'<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/icon/welcomebox/profile.gif').'" width="32" height="32" alt="" />' .
		xg_html('EDIT_PROFILE').'</a></li>');
?>
<div class="xg_module create-success">
	<div class="xg_module_head">
        <small class="right"><a href="#" id="welcome_box" _url="<%= xnhtmlentities($this->_buildUrl('embed', 'welcomeSetValues', array('id' => $this->embedLocator, 'xn_out' => 'json'))) %>"><%= xg_html('CLOSE') %></a></small>
		<h2><%= $isAdmin
			? xg_html('WELCOME_TO_YOUR_NETWORK_USERNAME', qh(xg_username($this->_user)))
        	: xg_html('WELCOME_TO_NETWORK_USERNAME', qh(XN_Application::load()->name), qh(xg_username($this->_user))) %></h2>
	</div>
	<div class="xg_module_body">
		<p><%=xg_html('HERE_ARE_A_FEW')%></p>
		<ul class="easyclear" id="xj_welcomebox_link_container">
			<%=join('', $links)%>
		</ul>
	</div>
	<div class="xg_module_foot"></div>
</div>
