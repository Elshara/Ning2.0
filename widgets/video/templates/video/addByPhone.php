<?php
xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_A_VIDEO'));
?>
<div id="xg_body">
	<div class="xg_colgroup">
		<div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget,'none')) ?>
			<%= xg_headline($title)%>
			<div class="xg_colgroup">
				<div class="xg_2col first-child">
					<div class="xg_module">
						<div class="xg_module_body pad">
							<h3><%= xg_html('ADD_VIDEOS_BY_PHONE_OR_EMAIL') %></h3>
							<p><%= xg_html('ADD_PHOTOS_AND_VIDEOS_TO_X_BY_SENDING', $this->appName) %></p>
							<p class="notification" style="text-align:center;">
								<a id="xg_profiles_settings_email_show" href="mailto:<%= $this->_user->uploadEmailAddress %>"><%= $this->_user->uploadEmailAddress %></a>
							</p>
						</div>
						<div class="xg_module_body pad">
							<h4><%= xg_html('HOW_IT_WORKS') %></h4>
							<p><%= xg_html('SEND_ONE_PHOTO_OR_VIDEO') %></p>
							<p><a href="#" onclick="generateNewEmailAddress();"><%= xg_html('CLICK_HERE_TO_GET_A_NEW_UNIQUE') %></a></p>
						</div>
					</div>
				</div>
				<div class="xg_1col">
					<div class="xg_module">
						<div class="xg_module_body">
							<h3><%= xg_html('MORE_WAYS_TO_ADD_VIDEOS') %></h3>
							<?php $this->renderPartial('fragment_addByComputer'); ?>
						</div>
						<div class="xg_module_body">
							<?php $this->renderPartial('fragment_addEmbed'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="xg_1col">
			<div class="xg_1col first-child">
				<?php xg_sidebar($this); ?>
			</div>
		</div>
	</div>
</div>
<?php XG_App::ningLoaderRequire('xg.video.index._shared', 'xg.video.video.addByPhone'); ?>
<?php xg_footer(); ?>