<?php
/*  $Id: $
 *
 */
$music = W_Cache::getWidget('music');
?>
<div class="xg_floating_module" style="display:none">
	<div class="xg_floating_container xg_floating_container_wide xg_module">
		<div class="xg_module_head">
			<h2><span class="png-fix"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/quickpost/music.png')%>" alt="" /></span><%=xg_html('ADD_MUSIC_TRACKS')%></h2>
		</div>
		<div class="xg_module_body">
			<form id="xg_quickadd_music" action="<%
					// .txt to prevent IE6 from showing download dialog for IFrameTransport [Jon Aquino 2007-01-30]. See also bazel-1385.
					echo qh($music->buildUrl('track', 'createMultipleQuick','/.txt?xn_out=json'))
				%>" method="post" enctype="multipart/form-data">
				<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
				<input type="hidden" name="linkMode" value="0"/>
                <input type="hidden" name="uploadMarker" value="present"/>
				<div class="msg" id="xg_quickadd_music_notify" style="display:none"></div>
				<fieldset class="nolegend">
					<p>
						<ul class="options">
							<li><input type="file" class="file" name="track_01"/></li>
						</ul>
					</p>
				</fieldset>
				<fieldset class="nolegend">
					<p>
						<label><%=xg_html('OR_PASTE_THE_URL')%></label><br/>
						<input type="text" name="track_02" class="textfield wide" /><br/>
					</p>
				</fieldset>
				<?php if (XG_SecurityHelper::userIsAdmin()) { ?>
					<p><label class="subdue"><input type="checkbox" class="checkbox" name="featureOnMain" value="1"/><%=xg_html('FEATURE_ON_HOME')%></label></p>
				<?php }?>
				<p class="small"><%= xg_html('I_HAVE_RIGHT_TO_UPLOAD_SONG', 'href="' . qh(W_Cache::getWidget('main')->buildUrl('authorization', 'termsOfService', array('noBack' => 1))) . '" target="_blank"') %></p>
				<p class="buttongroup">
					<a href="<%=qh($music->buildUrl('track', 'new'))%>" lnkEmbed="<%=qh($music->buildUrl('track','newLink'))%>" class="left more_options"><%=xg_html('MORE_OPTIONS')%></a>
					<input class="button button-primary" name="add" type="submit" value="<%=xg_html('ADD_MUSIC_TRACKS')%>">
					<input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
				</p>
			</form>
		</div>
	</div>
</div>
