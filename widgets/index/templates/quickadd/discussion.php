<?php
/*  $Id: $
 *
 */
$forum = W_Cache::getWidget('forum');
W_Cache::push($forum); // Category::findAll refers to the W_Cache::current('W_Widget');
$categories = Category::findAll(XG_SecurityHelper::userIsAdmin() || XG_GroupHelper::isGroupAdmin());
W_Cache::pop($forum);
?>
<div class="xg_floating_module" style="display:none">
	<div class="xg_floating_container xg_floating_container_wide xg_module">
		<div class="xg_module_head">
			<h2><span class="png-fix"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/quickpost/forum.png')%>" alt="" /></span><%=xg_html('START_A_DISCUSSION')%></h2>
		</div>
		<div class="xg_module_body">
			<form id="xg_quickadd_discussion" action="<%=qh($forum->buildUrl('topic','createQuick','/.txt?xn_out=json'))%>" method="post">
				<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
				<div class="msg" id="xg_quickadd_discussion_notify" style="display:none"></div>
				<fieldset class="nolegend">
					<p><label><%=xg_html('DISCUSSION_TITLE2')%><br /><input type="text" name="title" class="textfield wide"/></label></p>
					<p><label><%=xg_html('POST2')%><br /><textarea cols="30" rows="8" name="description" _maxLength="<%=Topic::MAX_DESCRIPTION_LENGTH%>" class="wide"></textarea></label></p>
					<?php if($categories) {?>
					<p><label><%=xg_html('CATEGORY')%><br/>
							<select name="categoryId">
								<?php foreach($categories as $c) echo '<option value="'.qh($c->id).'">'.qh($c->title).'</option>'?>
							</select>
					</label></p>
					<?php } ?>
					<?php if (XG_SecurityHelper::userIsAdmin()) { ?>
						<p><label class="subdue"><input type="checkbox" class="checkbox" name="featureOnMain" value="1"/><%=xg_html('FEATURE_ON_HOME')%></label></p>
					<?php }?>
				</fieldset>
				<p class="buttongroup">
					<a href="<%=$forum->buildUrl('topic','new')%>" class="left more_options"><%=xg_html('MORE_OPTIONS')%></a>
					<input class="button button-primary" name="add" type="submit" value="<%=xg_html('START_DISCUSSION')%>">
					<input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
				</p>
			</form>
		</div>
	</div>
</div>
