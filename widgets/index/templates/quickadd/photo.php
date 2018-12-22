<?php
/*  $Id: $
 *
 */
$photo = W_Cache::getWidget('photo');
XG_App::includeFileOnce('/lib/XG_MediaUploaderHelper.php');
?>
<div class="xg_floating_module" style="display:none">
	<div class="xg_floating_container xg_floating_container_wide xg_module">
		<div class="xg_module_head">
			<h2><span class="png-fix"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/quickpost/photo.png')%>" alt="" /></span><%=xg_html('ADD_PHOTOS')%></h2>
		</div>
		<div class="xg_module_body">
			<form id="xg_quickadd_photo" action="<%
					// .txt to prevent IE6 from showing download dialog for IFrameTransport [Jon Aquino 2007-01-30]. See also bazel-1385.
					echo qh($photo->buildUrl('photo', 'createMultipleQuick','/.txt?xn_out=json'))
				%>" method="post" enctype="multipart/form-data">
				<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
				<div class="msg" id="xg_quickadd_photo_notify" style="display:none"></div>
				<fieldset class="nolegend">
					<ol class="options">
						<li><input type="file" name="photo01" /></li>
						<li><input type="file" name="photo02" /></li>
						<li><input type="file" name="photo03" /></li>
						<li><input type="file" name="photo04" /></li>
					</ol>
					<?php if (XG_SecurityHelper::userIsAdmin()) { ?>
						<p><label class="subdue"><input type="checkbox" class="checkbox" name="featureOnMain" value="1"/><%=xg_html('FEATURE_ON_HOME')%></label></p>
					<?php }?>
					<input type="hidden" name="uploadMarker" value="present"/>
				</fieldset>
				<p class="small"><%= xg_html('I_HAVE_RIGHT_TO_UPLOAD_PHOTOS', 'href="' . qh(W_Cache::getWidget('main')->buildUrl('authorization', 'termsOfService', array('noBack' => 1))) . '" target="_blank"') %></p>
				<p class="buttongroup">
					<a href="<%=qh($photo->buildUrl('photo', XG_MediaUploaderHelper::action()))%>" class="left more_options"><%=xg_html('MORE_OPTIONS')%></a>
					<input class="button button-primary" name="add" type="submit" value="<%=xg_html('ADD_PHOTOS')%>">
					<input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
				</p>
			</form>
		</div>
	</div>
</div>
