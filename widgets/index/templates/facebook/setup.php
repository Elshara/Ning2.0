<?php xg_header('manage',xg_text('FACEBOOK_SETUP')); ?>
<?php XG_App::ningLoaderRequire('xg.shared.util'); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline(xg_text('FACEBOOK_SETUP'))%>
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <p><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/facebook/platformlogo.png') %>" alt="<%= xg_html('FACEBOOK_PLATFORM_LOGO') %>" class="right" style="margin-left:10px" />
                    <%= xg_html('FACEBOOK_QUICK_INTRO_1')%></p>
                    <p><%= xg_html('FACEBOOK_QUICK_INTRO_2')%></p>
                </div>
            </div>

            <div class="xg_module facebook">
                <div class="xg_module_head">
                    <h2><%= xg_html('FACEBOOK_YOUR_APPLICATIONS') %></h2>
                </div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('FACEBOOK_MUSIC_PLAYER_TITLE') %></h3>
                    <fieldset class="nolegend">
						<?php  if (!$this->fbMusicEnabled) { ?>
	                        <div class="block left">
								<p><%= xg_html('FACEBOOK_MUSIC_PLAYER_DESC') %></p>
								<p><a class="button" href="<%= xnhtmlentities($this->_buildUrl('facebook','instructions','?appType=music&step=1')) %>"><%= xg_html('FACEBOOK_MUSIC_BUTTON') %></a></p>
							</div>
                        <?php } else {
                                $disableArgs = 'href="#" dojoType="PostLink" _url="'.
                                                                qh($this->_buildUrl('facebook','disableEmbed',"?appType=music")).'" _reload="true" _confirmQuestion="'.
                                                                xg_html('FACEBOOK_CONFIRM_DISABLE').'"';

                        	?>
	                        <div class="block left">
								<p><%= xg_html('FACEBOOK_MUSIC_PLAYER_DESC') %></p>
	                        <?php if ($this->fbAppCreated == 'music') { ?>
								<div class="success">
									<p><%=xg_html('YOUR_FACEBOOK_APPLICATION_HAS')%></p>
									<ol class="last-child">
										<li><%=xg_html('A_X_VIEW', 'href="'.qh($this->fbMusicUrl).'"')%></li>
										<li><%=xg_html('CLICK_ALLOW_TO_ADD')%></li>
									</ol>
								</div>
	                        <?php }?>
							<?php if ($this->fbMusicNeedsUpgrade) {?>
								<div class="notification">
									<p class="last-child"><%=xg_html('YOUR_APP_NEEDS_TO_BE_UPDATED', 'href="' . xnhtmlentities($this->_buildUrl('facebook', 'instructions', array('appType' => 'music', 'step' => '3', 'upgrade' => '1'))) . '"')%></p>
								</div>
							<?php } else {?>
								<form id="updateMusic" method="POST" action="<%= xnhtmlentities($this->_buildUrl('facebook','updateEmbedOptions')) %>">
									<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
									<p>
										<label for="musicsrc"><%= xg_html('FACEBOOK_MUSIC_SHOULD_PLAY_FROM') %></label><br/>
										<input type="hidden" name="appType" value="music"/>
										<select name="displayType" id="musicsrc">
											<option name="displayType" <?php if ($this->fbMusicType=='default') {echo 'selected';} ?> value="default"><%= xg_html('FACEBOOK_RECENT') %></option>
											<option name="displayType" <?php if ($this->fbMusicType=='rated') {echo 'selected';} ?> value="rated"><%= xg_html('FACEBOOK_RATED') %></option>
											<option name="displayType" <?php if ($this->fbMusicType=='promoted') {echo 'selected';} ?> value="promoted"><%= xg_html('FACEBOOK_PROMOTED') %></option>
										</select>
										<input class="button" onclick="document.forms.updateMusic.submit()" value="Update" type="submit">
									</p>
								</form>
							<?php }?>
							</div>
							<ul class="block right nobullets">
								<li><a class="desc view" href="<%= $this->fbMusicUrl; %>" target="_blank"><%= xg_html('FACEBOOK_VIEW_APP') %></a></li>
								<?php if (!$this->fbMusicNeedsUpgrade) {?>
									<li><a class="desc facebook" href="<%= xnhtmlentities($this->_buildUrl('facebook','postInstructions')) %>"><%= xg_html('FACEBOOK_PROMOTING_ON') %></a></li>
									<li><a class="desc settings" href="/main/facebook/instructions?appType=music&step=2"><%= xg_html('FACEBOOK_SETUP_INSTR') %></a></li>
									<li><a class="desc edit" href="/main/facebook/instructions?appType=music&step=3"><%= xg_html('TAB_AND_API_INFO') %></a></li>
								<?php }?>
								<li><a class="desc delete" <%=$disableArgs%>><%= xg_html('FACEBOOK_DISABLE') %></a></li>
							</ul>
                        <?php } ?>
                    </fieldset>
                </div>

                <div class="xg_module_body pad">
                <h3><%= xg_html('FACEBOOK_VIDEO_PLAYER_TITLE') %></h3>
                    <fieldset class="nolegend">
						<?php  if (!$this->fbVideoEnabled) { ?>
	                        <div class="block left">
    	                        <p><%= xg_html('FACEBOOK_VIDEO_PLAYER_DESC') %></p>
								<p><a class="button" href="<%= xnhtmlentities($this->_buildUrl('facebook','instructions','?appType=video&step=1')) %>"><%= xg_html('FACEBOOK_VIDEO_BUTTON') %></a></p>
							</div>
                        <?php } else {
                                $disableArgs = 'href="#" dojoType="PostLink" _url="'.
                                                                qh($this->_buildUrl('facebook','disableEmbed',"?appType=video")).'" _reload="true" _confirmQuestion="'.
                                                                xg_html('FACEBOOK_CONFIRM_DISABLE').'"';

                        	?>
	                        <div class="block left">
    	                        <p><%= xg_html('FACEBOOK_VIDEO_PLAYER_DESC') %></p>
	                        <?php if ($this->fbAppCreated == 'video') { ?>
								<div class="success">
									<p><%=xg_html('YOUR_FACEBOOK_APPLICATION_HAS')%></p>
									<ol class="last-child">
										<li><%=xg_html('A_X_VIEW', 'href="'.qh($this->fbVideoUrl).'"')%></li>
										<li><%=xg_html('CLICK_ALLOW_TO_ADD')%></li>
									</ol>
								</div>
	                        <?php }?>
							<?php if ($this->fbVideoNeedsUpgrade) {?>
								<div class="notification">
									<p class="last-child"><%=xg_html('YOUR_APP_NEEDS_TO_BE_UPDATED', 'href="' . xnhtmlentities($this->_buildUrl('facebook', 'instructions', array('appType' => 'video', 'step' => '3', 'upgrade' => '1'))) . '"')%></p>
								</div>
							<?php } else {?>
								<form id="updateVideo" method="POST" action="<%= xnhtmlentities($this->_buildUrl('facebook','updateEmbedOptions')) %>">
									<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
									<p>
										<label for="videosrc"><%= xg_html('FACEBOOK_VIDEO_SHOULD_PLAY_FROM') %></label><br/>
										<input type="hidden" name="appType" value="video"/>
											<select name="displayType" id="videosrc">
											<option <?php if ($this->fbVideoType=='default') {echo 'selected';} ?> value="default"><%= xg_html('FACEBOOK_RECENTLY_ADDED') %></option>
											<option <?php if ($this->fbVideoType=='promoted') {echo 'selected';} ?> value="promoted"><%= xg_html('FACEBOOK_RECENTLY_FEATURED') %></option>
											<option <?php if ($this->fbVideoType=='rated') {echo 'selected';} ?> value="rated"><%= xg_html('FACEBOOK_RATED') %></option>
										</select>
										<input class="button" type="submit" value="Update">
									</p>
								</form>
							<?php }?>
	                        </div>
							<ul class="block right nobullets">
								<li><a class="desc view" href="<%= $this->fbVideoUrl; %>" target="_blank"><%= xg_html('FACEBOOK_VIEW_APP') %></a></li>
								<?php if (!$this->fbVideoNeedsUpgrade) {?>
									<li><a class="desc facebook" href="<%= xnhtmlentities($this->_buildUrl('facebook','postInstructions')) %>"><%= xg_html('FACEBOOK_PROMOTING_ON') %></a></li>
									<li><a class="desc settings" href="/main/facebook/instructions?appType=video&step=2"><%= xg_html('FACEBOOK_SETUP_INSTR') %></a></li>
									<li><a class="desc edit" href="/main/facebook/instructions?appType=video&step=3"><%= xg_html('TAB_AND_API_INFO') %></a></li>
								<?php }?>
								<li><a class="desc delete" <%=$disableArgs%>><%= xg_html('FACEBOOK_DISABLE') %></a></li>
							</ul>
                    	<?php } ?>
                    </fieldset>
                </div>

                <div class="xg_module_body pad">
                    <h3><%= xg_html('FACEBOOK_SLIDESHOW_PLAYER_TITLE') %></h3>
                    <fieldset class="nolegend">
                        <?php if (!$this->fbPhotoEnabled) { ?>
	                        <div class="block left">
    	                        <p><%= xg_html('FACEBOOK_SLIDESHOW_PLAYER_DESC') %></p>
								<p><a class="button" href="<%= qh($this->_buildUrl('facebook','instructions','?appType=photo&step=1')) %>"><%= xg_html('FACEBOOK_SLIDESHOW_BUTTON') %></a></p>
							</div>
                        <?php } else {
                        	$disableArgs = 'href="#" dojoType="PostLink" _url="'.
								qh($this->_buildUrl('facebook','disableEmbed',"?appType=photo")).'" _reload="true" _confirmQuestion="'.
								xg_html('FACEBOOK_CONFIRM_DISABLE').'"';
                        	?>
                        	<div class="block left">
                            	<p><%= xg_html('FACEBOOK_SLIDESHOW_PLAYER_DESC') %></p>
	                        <?php if ($this->fbAppCreated == 'photo') { ?>
								<div class="success">
									<p><%=xg_html('YOUR_FACEBOOK_APPLICATION_HAS')%></p>
									<ol class="last-child">
										<li><%=xg_html('A_X_VIEW', 'href="'.qh($this->fbPhotoUrl).'"')%></li>
										<li><%=xg_html('CLICK_ALLOW_TO_ADD')%></li>
									</ol>
								</div>
	                        <?php }?>
							<?php if ($this->fbPhotoNeedsUpgrade) {?>
								<div class="notification">
									<p class="last-child"><%=xg_html('YOUR_APP_NEEDS_TO_BE_UPDATED', 'href="' . xnhtmlentities($this->_buildUrl('facebook', 'instructions', array('appType' => 'photo', 'step' => '3', 'upgrade' => '1'))) . '"')%></p>
								</div>
							<?php } else {?>
								<form id="updatePhoto" method="POST" action="<%= xnhtmlentities($this->_buildUrl('facebook','updateEmbedOptions')) %>">
									<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
									<p>
										<label for="photosrc"><%= xg_html('FACEBOOK_PHOTO_SHOULD_PLAY_FROM') %></label><br/>
										<input type="hidden" name="appType" value="photo"/>
										<select name="displayType" id="photosrc">
											<option <?php if ($this->fbPhotoType=='default') {echo 'selected';} ?> value="default"><%= xg_html('FACEBOOK_RECENT') %></option>
											<option <?php if ($this->fbPhotoType=='promoted') {echo 'selected';} ?> value="promoted"><%= xg_html('FACEBOOK_PROMOTED') %></option>
											<option <?php if ($this->fbPhotoType=='popular') {echo 'selected';} ?> value="popular"><%= xg_html('FACEBOOK_RATED') %></option>
										</select>
										<input class="button" type="submit" value="Update">
									</p>
								</form>
							<?php }?>
							</div>
							<ul class="block right nobullets">
								<li><a class="desc view" href="<%= $this->fbPhotoUrl; %>" target="_blank"><%= xg_html('FACEBOOK_VIEW_APP') %></a></li>
								<?php if (!$this->fbPhotoNeedsUpgrade) {?>
									<li><a class="desc facebook" href="<%= xnhtmlentities($this->_buildUrl('facebook','postInstructions')) %>"><%= xg_html('FACEBOOK_PROMOTING_ON') %></a></li>
									<li><a class="desc settings" href="/main/facebook/instructions?appType=photo&step=2"><%= xg_html('FACEBOOK_SETUP_INSTR') %></a></li>
									<li><a class="desc edit" href="/main/facebook/instructions?appType=photo&step=3"><%= xg_html('TAB_AND_API_INFO') %></a></li>
								<?php }?>
								<li><a class="desc delete" <%=$disableArgs%>><%= xg_html('FACEBOOK_DISABLE') %></a></li>
							</ul>
                        <?php }?>
					</fieldset>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
