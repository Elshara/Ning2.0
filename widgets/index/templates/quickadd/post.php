<?php
/*  $Id: $
 *
 */
$profiles = W_Cache::getWidget('profiles');
?>
<div class="xg_floating_module" style="display:none">
	<div class="xg_floating_container xg_floating_container_wide xg_module">
		<div class="xg_module_head">
			<h2><span class="png-fix"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/quickpost/blog.png')%>" alt="" /></span><%=xg_html('ADD_A_BLOG_POST2')%></h2>
		</div>
		<div class="xg_module_body">
			<form id="xg_quickadd_post" action="<%=qh($profiles->buildUrl('blog','createQuick','/.txt?xn_out=json'))%>" method="post">
				<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
				<div class="msg" id="xg_quickadd_post_notify" style="display:none"></div>
				<input type="hidden" name="post_when" value="now">
				<fieldset class="nolegend">
					<p><label><%=xg_html('POST_TITLE')%><br /><input type="text" name="post_title" class="textfield wide"/></label></p>
					<p><label><%=xg_html('ENTRY')%><br /><textarea cols="30" rows="8" name="post_body" class="wide"></textarea></label></p>
					<?php if (XG_SecurityHelper::userIsAdmin()) { ?>
						<p><label class="subdue"><input type="checkbox" class="checkbox" name="featureOnMain" value="1"/><%=xg_html('FEATURE_ON_HOME')%></label></p>
					<?php }?>
				</fieldset>
				<p class="buttongroup">
					<a href="<%=qh($profiles->buildUrl('blog','new'))%>" class="left more_options"><%=xg_html('MORE_OPTIONS')%></a>
					<input class="button button-primary" name="add" type="submit" value="<%=xg_html('PUBLISH_POST')%>">
					<input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
				</p>
			</form>
		</div>
	</div>
</div>
