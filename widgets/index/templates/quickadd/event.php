<?php
/*  $Id: $
 *
 */
$events = W_Cache::getWidget('events');

XG_App::includeFileOnce('/lib/XG_Form.php');
XG_App::includeFileOnce('/lib/XG_DateHelper.php');

$form = new XG_Form();
list(,,$h,$d,$m,$y) = localtime();
$start = date('Y-m-d H:i',mktime(18,0,0,$m+1,$d+7,$y));
$form->setDate('start', mb_substr($start,0,10));
$form->setTime('start', mb_substr($start,11,5));

?>
<div class="xg_floating_module" style="display:none">
	<div class="xg_floating_container xg_floating_container_wide xg_module">
		<div class="xg_module_head">
			<h2><span class="png-fix"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/quickpost/event.png')%>" alt="" /></span><%=xg_html('CREATE_AN_EVENT')%></h2>
		</div>
		<div class="xg_module_body">
			<form id="xg_quickadd_event" action="<%=qh($events->buildUrl('event','createQuick','/.txt?xn_out=json'))%>" method="post" enctype="multipart/form-data">
				<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
				<input type="hidden" name="setValues" value="1">
				<input type="hidden" name="organizedBy" value="<%=qh(xg_username($this->_user->screenName))%>">
				<input type="hidden" name="hideEnd" value="1">
				<input type="hidden" name="privacy" value="anyone">

				<div class="msg" id="xg_quickadd_event_notify" style="display:none"></div>
				<p><label><%=xg_html('NAME')%><br /><input type="text" name="title" class="textfield wide"/></label></p>
				<p id="xg_quickadd_event_img"><label><%=xg_html('EVENT_IMAGE')%></label><br /><%=$form->image('photo')%></p>
				<p><label><%=xg_html('DESCRIPTION')%><br /><textarea cols="30" rows="6" name="description" class="wide"></textarea></label></p>
				<p><label><%=xg_html('EVENT_TYPE')%><br /><input id="xg_qa_event_type" type="text" name="type" name="type" class="textfield wide"/></label></p>
				<p><label><%=xg_html('START_TIME')%><br /></label><%=$form->date('start','y:0:2md',0,'style="width:auto;"').$form->time('start','hi',0,'style="width:auto;"')%></p>
				<p><label><%=xg_html('LOCATION')%><br /><input type="text" name="location" class="textfield wide"/></label></p>
				<?php if (XG_SecurityHelper::userIsAdmin()) { ?>
					<p><label class="subdue"><input type="checkbox" class="checkbox" name="featureOnMain" value="1"/><%=xg_html('FEATURE_ON_HOME')%></label></p>
				<?php }?>
				<p class="buttongroup">
					<a href="<%=qh($events->buildUrl('event','new'))%>" class="left more_options"><%=xg_html('MORE_OPTIONS')%></a>
					<input class="button button-primary" name="add" type="submit" value="<%=xg_html('CREATE_EVENT')%>">
					<input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
				</p>
			</form>
		</div>
	</div>
</div>
