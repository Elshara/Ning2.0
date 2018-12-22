<?php
/*  $Id: $
 *
 */
?>
<div class="xg_floating_module" style="display:none">
	<div class="xg_floating_container xg_floating_container_wide xg_module">
		<div class="xg_module_head">
			<h2><span class="png-fix"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/quickpost/note.png')%>" alt="" /></span><%=xg_html('ADD_A_NOTE')%></h2>
		</div>
		<div class="xg_module_body">
			<form id="xg_quickadd_note" action="<%=qh(Notes_UrlHelper::url('createQuick/.txt','xn_out=json'))%>" method="post">
				<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
				<div class="msg" id="xg_quickadd_note_notify" style="display:none"></div>

				<fieldset class="nolegend">
					<p><label><%=xg_html('NOTE_TITLE')%><br /><input type="text" name="noteKey" class="textfield wide"/></label></p>
					<p><label><%=xg_html('ENTRY')%><br /><textarea cols="30" rows="8" name="content" class="wide"></textarea></label></p>
					<?php if (XG_SecurityHelper::userIsAdmin()) { ?>
						<p><label class="subdue"><input type="checkbox" class="checkbox" name="featureOnMain" value="1"/><%=xg_html('FEATURE_ON_HOME')%></label></p>
					<?php }?>
				</fieldset>

				<p class="buttongroup">
					<a href="<%=qh(Notes_UrlHelper::url('edit','fromQuickPost=1'))%>" class="left more_options"><%=xg_html('MORE_OPTIONS')%></a>
					<input class="button button-primary" name="add" type="submit" value="<%=xg_html('PUBLISH_NOTE')%>">
					<input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
				</p>
			</form>
		</div>
	</div>
</div>
