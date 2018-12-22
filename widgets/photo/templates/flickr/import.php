<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_PHOTOS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_2col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <form id="import_photos_form" action="<%= xnhtmlentities($this->_buildUrl('flickr', 'runImport')) %>" method="post">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <input type="hidden" name="nsid" value="<%= $this->nsid %>"/>
                                <input type="hidden" name="token" value="<%= $this->token %>"/>
                                <fieldset class="nolegend">
                                    <ul class="options">
                                        <li><input type="radio" name="importoption" id="import_recent" value="recentX" checked /> <label for="import_recent"> <%= xg_text('GET_MY') %></label> <%= $this->numRecent %> <label for="import_recent"><%= xg_text('MOST_RECENT_PHOTOS') %></label></li>
                                        <?php if ($this->setOptions) { ?>
                                            <li><input type="radio" name="importoption" id="import_set" value="chosenset" /> <label for="import_set"><%= xg_text('GET_PHOTOS_FROM_SET') %></label><%= $this->setOptions %></li>
                                        <?php } ?>
                                        <li><input type="radio" name="importoption" id="import_tagged" value="gettagged" /> <label for="import_tagged"><%= xg_text('GET_ALL_MY_TAGGED') %></label> <input type="text" id="flickrTagged" name="tagged" value="" style="width:80px;" /></li>
                                        <li><input type="radio" name="importoption" id="import_all" value="getall" /> <label for="import_all"><%= xg_text('GET_ALL_MY_FLICKR') %></label></li>
                                    </ul>
                                    <p><input type="checkbox" name="getdescriptions" id="getdescriptions" /> <label for="getdescriptions" style="font-weight:normal;"><%= xg_html('GET_MY_FLICKR_PHOTOS_DESCRIPTIONS') %></label></p>
                                    <p><input type="checkbox" name="getoriginals" id="getoriginals" /> <label for="getoriginals" style="font-weight:normal;"><%= xg_html('GET_MY_FLICKR_PHOTOS_ORIGINALS') %></label></p>
                                </fieldset>
                                <p><big><input type="submit" class="button" value="<%= xg_html('GET_PHOTOS') %>" /></big></p>
                            </form>
                        </div>
                        <div class="xg_module_body pad">
                            <img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" id="importSpinner" alt="<%= xg_html('SPINNER') %>" class="right" style="display:none; margin-right:5px" />
                            <div id="importMessage">
                                <h4><%= xg_html('HOW_IT_WORKS') %></h4>
                                <p><%= xg_html('GET_STARTED_BY_IMPORT') %></p>
                            </div>
                            <div><img src="<?php echo xg_cdn($this->_widget->buildResourceUrl('gfx/flickr/blank.gif')) ?>" height="50" id="importingProgress" style="display:none;padding:10px;" /></div>
                            <div id="importWarning" style="display:none;"><p><strong><%= xg_html('PLEASE_KEEP_THIS_PAGE_OPEN_WHILE_IMPORT') %></strong></p></div>
                        </div>
                    </div>
                </div>
                <div class="xg_1col">
                    <div class="xg_module">
                        <div class="xg_module_body">
                            <div class="xg_module_body">
                                <h3><%= xg_html('MORE_WAYS_TO_ADD_PHOTOS') %></h3>
                                <h4><%= xg_html('FROM_YOUR_COMPUTER') %></h4>
                                <img class="left" alt="" src="<%= xg_cdn('/xn_resources/widgets/photo/gfx/add.gif') %>" width="32" height="32" />
                                <p style="margin-left:40px"><%= xg_html('UPLOAD_PHOTOS_FROM_YOUR') %></p>
                                <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('photo', XG_MediaUploaderHelper::action())) %>"><%= xg_html('MORE_INFORMATION') %></a></strong></p>
                            </div>
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
<?php XG_App::ningLoaderRequire('xg.photo.flickr.import'); ?>
<?php xg_footer(); ?>