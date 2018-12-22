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
                            <h3><%= xg_html('IMPORT_PHOTOS_FLICKR') %></h3>
                        <?php if ($this->flickrEnabled == 'Y') { ?>
                            <p><%= xg_html('TO_GET_YOUR_PHOTOS_FROM_FLICKR', $this->appName)%></p>
                            <p><big><a href="<%= xnhtmlentities($this->_buildUrl('flickr', 'index')) %>"><%= xg_html('CLICK_HERE_TO_CONTINUE_TO_FLICKR') %></a></big></p>
                        </div>
                        <div class="xg_module_body pad">
                            <h4><%= xg_html('HOW_IT_WORKS') %></h4>
                            <p><%= xg_html('IF_YOURE_NOT_SIGNED_IN') %></p>
                        <?php } elseif (XG_SecurityHelper::userIsAdmin()) { ?>
                            <p><%= xg_html('YOU_NEED_TO_SET_UP_FLICKR', "href='" . W_Cache::getWidget('main')->buildUrl('flickr','keys') . "'") %></p>
                            <p><input type="checkbox" value="N" id="promptOwnerForFlickr" onchange="toggleFlickrNotification(this)"><label for="promptOwnerForFlickr"> <%= xg_html('DONT_SHOW_THIS_AGAIN_FLICKR')%></label></p>
                        <?php } ?>
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
                        <div class="xg_module_body">
                            <h4><%= xg_html('BY_PHONE_OR_EMAIL') %></h4>
                            <img class="left" alt="" src="<%= xg_cdn('/xn_resources/widgets/photo/gfx/phoneadd_large.gif') %>" width="32" height="32" />
                            <p style="margin-left:40px"><%= xg_html('ADD_PHOTOS_OR_VIDEOS_TO_X', $this->appName) %></p>
                            <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('photo', 'addByPhone')) %>"><%= xg_html('MORE_INFORMATION') %></a></strong></p>
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
<?php XG_App::ningLoaderRequire('xg.photo.photo.flickr'); ?>
<?php xg_footer(); ?>