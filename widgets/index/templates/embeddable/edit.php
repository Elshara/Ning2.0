<?php xg_header(null, $title = xg_text('BADGES_AND_WIDGETS')); ?>
<?php XG_App::ningLoaderRequire('xg.shared.BazelImagePicker', 'xg.shared.BazelColorPicker', 'xg.index.embeddable.edit'); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
			<?php $this->renderPartial('fragment_success', 'admin'); ?>
            <form id="xg_player_cust_form" method="post" enctype="multipart/form-data">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <input type="hidden" name="submitAction" value="save">
                <?php foreach ($this->imageUrls as $name => $url) { ?>
                    <input type="hidden" name="<%= $name %>_currentUrl" value="<%= $this->imageUrls[$name] %>">
                <?php } ?>
                <div class="xg_module">
                    <div class="xg_module_body pad">
                        <ul class="page_tabs">
                            <li><a href="<%= xnhtmlentities($this->_widget->buildUrl('embeddable', 'list')) %>"><%= xg_html('GALLERY') %></a></li>
                            <li class="this"><a href="<%= xnhtmlentities($this->_widget->buildUrl('embeddable', 'edit')) %>"><%= xg_html('CUSTOMIZATION') %></a></li>
                        </ul>
                        <p><%= xg_html('CUSTOMIZE_YOUR_BADGES_AND_WIDGETS') %></p>

                        <fieldset class="easyclear">
                            <div class="legend"><%= xg_html('BACKGROUND_IMAGE_AND_COLOR') %></div>
                            <div dojotype="BazelImagePicker" fieldname="bgImage" onchange="xg.index.embeddable.edit.updateBothPreviews()"
                                    currentimagepath="<%= $this->imageUrls['bgOriginalImage'] %>" allowtile="0"></div>
                            <div dojotype="BazelColorPicker" fieldname="bgColor" defaultvalue="<?php echo $this->defaults['bgColor'] ?>" onchange="xg.index.embeddable.edit.updateBothPreviews()">
                            <span class="swatch"></span><button class="icon"><img src="<%= xg_cdn($this->_widget->buildResourceUrl('gfx/button/palette.gif')) %>"/></button></div>
                        </fieldset>

                        <fieldset class="easyclear zfix2">
                            <div class="legend"><%= xg_html('NETWORK_BRANDING_BADGES') %></div>
                            <div class="block left">
                                <ul class="options">
                                    <li class="zfix2"><label><%= $this->form->radio('badgeBranding', 'name', 'class="radio"') %><%= xg_html('NETWORK_NAME') %></label>
                                        <div class="swatch_group" style="float:none; margin-left:18px">
                                            <div dojotype="BazelColorPicker" fieldname="badgeFgColor" defaultvalue="<?php echo $this->defaults['badgeFgColor'] ?>" onchange="xg.index.embeddable.edit.updateBadgePreview()">
                                            <span class="swatch"></span><button class="icon"><img src="<%= xg_cdn($this->_widget->buildResourceUrl('gfx/button/palette.gif')) %>"/></button></div>
                                        </div>
                                    </li>
                                    <li class="clear"><label><%= $this->form->radio('badgeBranding', 'logo', 'class="radio"') %><%= xg_html('LOGO_IMAGE') %></label>
                                        <div class="swatch_group" style="float:none; margin-left:18px">
                                            <div dojotype="BazelImagePicker" fieldname="badgeFgImage" onchange="xg.index.embeddable.edit.updateBadgePreview()"
                                                currentimagepath="<%= $this->imageUrls['badgeFgImage'] %>" allowtile="0"></div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="embedpreview block right" id="xg_badge_preview">
                                <h4>
                                    <%= xg_html('PREVIEW') %>:
                                    <select name="badgePreview" onchange="xg.index.embeddable.edit.updateBadgePreview()">
                                        <option value="profile" <%= ($this->defaults['badgePreview'] == 'profile' ? 'selected="true"' : '') %>
                                                _url="<%= W_Cache::getWidget('profiles')->buildUrl('profile', 'embeddable', array('username' => XN_Profile::current()->screenName)) %>">
                                                <%= xg_html('MEMBER_BADGE') %></option>
                                        <option value="network_large" <%= ($this->defaults['badgePreview'] == 'network_large' ? 'selected="true"' : '') %>
                                                _url="<%= $this->_widget->buildUrl('embeddable', 'embeddable', array('large' => '1')) %>">
                                                <%= xg_html('LARGE_NETWORK_BADGE') %></option>
                                        <option value="network_small" <%= ($this->defaults['badgePreview'] == 'network_small' ? 'selected="true"' : '') %>
                                                _url="<%= $this->_widget->buildUrl('embeddable', 'embeddable', array('large' => '0')) %>">
                                                <%= xg_html('SMALL_NETWORK_BADGE') %></option>
                                    </select>
                                </h4>
								<a id="xj_badge_preview_refresh" href="#"><%= xg_html('REFRESH') %></a>
                                <div class="badge-container">
                                    <?php //preview will be placed here dynamically ?>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="easyclear">
                            <div class="legend"><%= xg_html('NETWORK_BRANDING_WIDGETS') %></div>
                            <div class="block left">
                                <ul class="options" style="">
                                    <li><label><%= $this->form->radio('playerBranding', 'none', 'class="radio"') %>
                                            <%= xg_html('DONT_DISPLAY_ANY_BRANDING') %></label></li>
                                    <li><label><%= $this->form->radio('playerBranding', 'name', 'class="radio"') %>
                                            <%= xg_html('NETWORK_NAME') %></label></li>
                                    <li><label><%= $this->form->radio('playerBranding', 'logo', 'class="radio"') %>
                                            <%= xg_html('WATERMARK_OR_LOGO_IMAGE') %></label>
                                        <div class="swatch_group" style="float:none; margin-left:18px">
                                            <div dojotype="BazelImagePicker" fieldname="playerLogoImage" onchange="xg.index.embeddable.edit.updatePlayerPreview()"
                                                    currentimagepath="<%= $this->imageUrls['playerLogoImage'] %>" allowtile="0"></div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <?php
                                $videoPlayerArgs = array('width' => 300, 'height' => 253, 'noVideosMessage' => xg_text('NETWORK_DOES_NOT_HAVE_VIDEOS'),
                                        'video_id' => 'most_recent', 'autoplay' => false, 'showDummyVideoIfNoneFound' => true);
                                $photoPlayerArgs = array('width' => 300, 'height' => 253, 'noPhotosMessage' => xg_text('NETWORK_DOES_NOT_HAVE_PHOTOS'),
                                        'photoSet' => 'all', 'internal' => false,
                                        'autoplay' => false);
                                $musicPlayerArgs = array('internal' => false, 'width' => 300, 'noMusicMessage' => xg_text('NETWORK_DOES_NOT_HAVE_MUSIC'),
                                        'playlistUrl' => $this->defaultPlaylistUrl,
                                        'internal' => false, 'autoplay' => false);
                            ?>
                            <div class="embedpreview block right" id="xg_player_preview">
                                <h4>
                                    <%= xg_html('PREVIEW') %>:
                                    <select name="playerPreview" onchange="xg.index.embeddable.edit.updatePlayerPreview()">
                                        <?php if ($this->enabledModules['video']) { ?>
                                            <option value="video" <%= ($this->defaults['playerPreview'] == 'video' ? 'selected="true"' : '') %>
                                                    _url="<%= W_Cache::getWidget('video')->buildUrl('video', 'embeddable', $videoPlayerArgs) %>">
                                                    <%= xg_html('VIDEO_PLAYER') %></option>
                                        <?php } ?>
                                        <?php if ($this->enabledModules['photo']) { ?>
                                            <option value="photo" <%= ($this->defaults['playerPreview'] == 'photo' ? 'selected="true"' : '') %>
                                                    _url="<%= W_Cache::getWidget('photo')->buildUrl('photo', 'embeddable', $photoPlayerArgs) %>">
                                                    <%= xg_html('PHOTO_SLIDESHOW_PROPER') %></option>
                                        <?php } ?>
                                        <option value="music" <%= ($this->defaults['playerPreview'] == 'music' ? 'selected="true"' : '') %>
                                                _url="<%= W_Cache::getWidget('music')->buildUrl('playlist', 'embeddable', $musicPlayerArgs) %>">
                                                <%= xg_html('MUSIC_PLAYER') %></option>
                                    </select>
                                </h4>
								<a id="xj_player_preview_refresh" href="#"><%= xg_html('REFRESH') %></a>
                                <div>
                                    <?php //preview will be placed here dynamically ?>
                                </div>
                            </div>
                        </fieldset>
                        <p class="buttongroup">
                        	<input type="submit" class="button button-primary" value="<%= xg_html('SAVE') %>" />
                        	<a class="button" href="<%=qh($this->_buildUrl('admin','manage'))%>"><%= xg_html('CANCEL') %></a>
						</p>
                    </div>
                </div>
            </form>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
