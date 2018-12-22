<?php
/*  $Id: $
 *
 *  Renders quick add bar
 *
 */
$mods = XG_ModuleHelper::getEnabledModules();
$quick = array();
$quick[] = '<option value="post" js="xg.index.quickadd.post" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','post')).'">'.xg_html('ADD_BLOG_POST2').'</option>';
if ($mods['forum']) {
	$forum = W_Cache::getWidget('forum');
	W_Cache::push($forum); // Category::findAll refers to the W_Cache::current('W_Widget');
	$forum->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
    if (Forum_SecurityHelper::currentUserCanSeeAddTopicLinks()) {
		$quick[] = '<option value="discussion" js="xg.index.quickadd.discussion" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','discussion')).'">'.xg_html('ADD_DISCUSSION2').'</option>';
	}
	W_Cache::pop($forum);
}
// opts
if ($mods['events']) {
	XG_App::includeFileOnce('/widgets/events/lib/helpers/Events_SecurityHelper.php');
	if (Events_SecurityHelper::currentUserCanCreateEvent()) {
		$quick[] = '<option value="event" js="xg.index.quickadd.event" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','event')).'">'.xg_html('ADD_EVENT2').'</option>';
	}
}
if ($mods['music']) $quick[] = '<option value="music" js="xg.index.quickadd.music" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','music')).'">'.xg_html('ADD_MUSIC2').'</option>';
if ($mods['photo']) $quick[] = '<option value="photo" js="xg.index.quickadd.photo" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','photo')).'">'.xg_html('ADD_PHOTOS2').'</option>';
if ($mods['video']) $quick[] = '<option value="video" js="xg.index.quickadd.video" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','video')).'">'.xg_html('ADD_VIDEO2').'</option>';
if ($mods['notes']) {
	if (XG_SecurityHelper::userIsAdmin()) {
		$quick[] = '<option value="note" js="xg.index.quickadd.note" url="'.qh(W_Cache::getWidget('main')->buildUrl('quickadd','note')).'">'.xg_html('ADD_NOTE2').'</option>';
	}
}
XG_App::ningLoaderRequire('xg.index.quickadd.bar');

// Simple heuristic to check whether we should display the "Share" dialog
$route = XG_App::getRequestedRoute();
$allowShare = preg_match('#^(show|list|index|search|detail)#',$route['actionName']);
?>
<form action="#">
	<fieldset class="last-child">
		<select dojoType="quickAddBar" disabled="disabled" class="quickpost">
			<option value=""><%=xg_html('QUICK_ADD')%></option>
			<?php if ($quick) { echo '<optgroup label="---------------">' . join('', $quick) . '</optgroup>'; } ?>
			<option value="invite" js="xg.index.quickadd.invite" url="<%=qh(W_Cache::getWidget('main')->buildUrl('quickadd','invite'))%>"><%=xg_html('INVITE_FRIENDS')%></option>
			<?php if ($allowShare) {?>
				<option value="share" js="xg.index.quickadd.share" url="<%=qh(W_Cache::getWidget('main')->buildUrl('quickadd','share'))%>"><%=xg_html('SHARE_THIS_PAGE')%></option>
			<?php }?>
		</select>
	</fieldset>
</form>
<div class="xg_floating_module" id="xg_quickadd_spinner" style="display:none">
	<div class="xg_floating_container xg_module" style="background-image:none;">
		<div class="xg_module_head">
			<h2></h2>
		</div>
		<div class="xg_module_body" _spinner="<%= /*otherwise img is loaded on every page */ qh('<img src="'.xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif').
			'" width="20" height="20" alt="'.qh(xg_html('UPLOADING')).'" class="left" style="margin-right:5px" />')%>">
			<p class="spinner_msg" style="margin-left:25px"></p>
			<p class="buttongroup">
				<a href="#" class="button"><%=xg_html('CANCEL')%></a>
			</p>
		</div>
	</div>
</div>
