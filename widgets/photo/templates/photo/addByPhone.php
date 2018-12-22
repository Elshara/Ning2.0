<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_PHOTOS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget,'none')) ?>
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_2col first-child">
					<div class="xg_module">
						<div class="xg_module_body pad">
                            <h3><%= xg_html('ADD_PHOTOS_BY_PHONE_OR_EMAIL') %></h3>
                            <p><%= xg_html('ADD_PHOTOS_AND_VIDEOS_TO_X_BY_SENDING', $this->appName) %></p>
                            <p class="notification" style="text-align: center;">
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
                            <h3><%= xg_html('MORE_WAYS_TO_ADD_PHOTOS') %></h3>
                            <h4><%= xg_html('FROM_YOUR_COMPUTER') %></h4>
                            <img class="left" alt="" src="<%= xg_cdn('/xn_resources/widgets/photo/gfx/add.gif') %>" width="32" height="32" />
                            <p style="margin-left:40px"><%= xg_html('UPLOAD_PHOTOS_FROM_YOUR') %></p>
                            <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('photo', XG_MediaUploaderHelper::action())) %>"><%= xg_html('MORE_INFORMATION') %></a></strong></p>
                        </div>
                        <?php if (($this->flickrEnabled == 'Y') || (XG_SecurityHelper::userIsAdmin() && $this->showFlickrToOwner == 'Y')) { ?>
                        <div class="xg_module_body">
                            <h4><%= xg_html('FROM_FLICKR') %></h4>
                            <span id="flickrlogo"><img src="<%= xg_cdn('/xn_resources/widgets/photo/gfx/flickr/flickr.png') %>" width="110" height="30" alt="Flickr" /></span>
                            <p><%= xg_html('ADD_PHOTOS_FROM_YOUR_FLICKR') %></p>
                            <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('photo', 'flickr')) %>"><%= xg_html('ADD_PHOTOS') %></a></strong></p>
                        </div>
                        <?php } ?>
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
<?php XG_App::ningLoaderRequire('xg.photo.photo.addByPhone'); ?>
<?php xg_footer(); ?>