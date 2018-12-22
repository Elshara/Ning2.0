<?php
/*  $Id: $
 *
 */
$video = W_Cache::getWidget('video');
?>
<div class="xg_floating_module" style="display:none">
	<div class="xg_floating_container xg_floating_container_wide xg_module">
		<div class="xg_module_head">
			<h2><span class="png-fix"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/quickpost/video.png')%>" alt="" /></span><%=xg_html('ADD_A_VIDEO3')%></h2>
		</div>
		<div class="xg_module_body">
			<form id="xg_quickadd_video" action="<%=qh($video->buildUrl('video','createQuick','/.txt?xn_out=json'))%>" method="post" enctype="multipart/form-data">
				<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
				<div class="msg" id="xg_quickadd_video_notify" style="display:none"></div>
				<fieldset class="nolegend">
					<ul class="options">
						<li><input type="file" class="file" name="file"/></li>
					</ul>
				</fieldset>
				<fieldset class="nolegend">
				  <p>
  					<label><%=xg_html('PASTE_CODE_HERE')%></label><br />
  					<textarea class="wide" rows="3" name="embedCode"></textarea>
					</p>
				</fieldset>
				<?php if (XG_SecurityHelper::userIsAdmin()) { ?>
					<p><label class="subdue"><input type="checkbox" class="checkbox" name="featureOnMain" value="1"/><%=xg_html('FEATURE_ON_HOME')%></label></p>
				<?php }?>
				<p class="small"><%= xg_html('I_HAVE_RIGHT_TO_UPLOAD_VIDEO', 'target="_blank" href="' . qh(W_Cache::getWidget('main')->buildUrl('authorization', 'termsOfService')) . '"') %></p>
				<p class="buttongroup">
					<a href="<%=qh($video->buildUrl('video','new'))%>" lnkEmbed="<%=qh($video->buildUrl('video','addEmbed'))%>" class="left more_options"><%=xg_html('MORE_OPTIONS')%></a>
					<input class="button button-primary" name="add" type="submit" value="<%=xg_html('ADD_VIDEO')%>">
					<input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
				</p>
			</form>
		</div>
	</div>
</div>
