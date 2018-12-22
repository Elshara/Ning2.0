<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_PHOTOS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget, 'none')) ?>
			<%= xg_headline($title)%>
            <div class="xg_colgroup" id="add_photos_module">
                <div class="xg_2col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <form id="add_photos_form" action="<%= xnhtmlentities($this->_buildUrl('photo', 'createMultiple')) %>" method="post" enctype="multipart/form-data">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <input type="hidden" name="uploadMarker" value="present"/>
                                <?php
                                $this->renderPartial('fragment_uploadErrors', 'photo', array(
                                        'id' => 'add_photos_form_notify', 'sizeLimitError' => $this->sizeLimitError, 'failedFiles' => $this->failedFiles, 'allHadErrors' => true)); ?>
                                <h3><%= xg_html('UPLOAD_PHOTOS_FROM_YOUR_COMPUTER') %></h3>
                                <?php if(!$this->hideBulkUploaderReferences) { ?>
                                <p style="margin:1em 0"><img class="left" style="margin-right:5px" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/add-multiple.png'))) %>" alt="" /><%= xg_html('HAVE_LOT_OF_PHOTOS') %><br />
                                <a href="<%= xnhtmlentities($this->_widget->buildUrl('photo', 'newWithUploader')) %>"><strong><%= xg_html('TRY_BULK_PHOTO_UPLOADER') %></strong></a></p>
                                <?php } ?>
                                <fieldset id="upload-photo-step" class="nolegend">
                                    <big>
                                        <ol class="options">
                                            <li><input class="inputFile" type="file" name="photo01" id="photo01" /></li>
                                            <li><input class="inputFile" type="file" name="photo02" id="photo02" /></li>
                                            <li><input class="inputFile" type="file" name="photo03" id="photo03" /></li>
                                            <li><input class="inputFile" type="file" name="photo04" id="photo04" /></li>
                                            <li><input class="inputFile" type="file" name="photo05" id="photo05" /></li>
                                            <li><input class="inputFile" type="file" name="photo06" id="photo06" /></li>
                                            <li><input class="inputFile" type="file" name="photo07" id="photo07" /></li>
                                            <li><input class="inputFile" type="file" name="photo08" id="photo08" /></li>
                                        </ol>
                                    </big>
                                </fieldset>
                                <p><small><%= xg_html('I_HAVE_RIGHT_TO_UPLOAD_PHOTOS', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'termsOfService', array('previousUrl' => XG_HttpHelper::currentUrl()))) . '"') %></small></p>
                                <p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('ADD_PHOTOS') %>" /></p>
                            </form>
                        </div>
                        <div class="xg_module_body pad">
                            <h4><%= xg_html('HOW_IT_WORKS') %></h4>
                            <p><%= xg_html('PHOTO_MAY_BE_10MB') %></p>
                        </div>
                    </div>
                </div>
                <div class="xg_1col">
                    <div class="xg_module">
                        <div class="xg_module_body">
                            <h3><%= xg_html('MORE_WAYS_TO_ADD_PHOTOS') %></h3>
                            <h4><%= xg_html('BY_PHONE_OR_EMAIL') %></h4>
                            <img class="left" alt="" src="<%= xg_cdn('/xn_resources/widgets/photo/gfx/phoneadd_large.gif') %>" width="32" height="32" />
                            <p style="margin-left:40px"><%= xg_html('ADD_PHOTOS_OR_VIDEOS_TO_X', $this->appName) %></p>
                            <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('photo', 'addByPhone')) %>"><%= xg_html('MORE_INFORMATION') %></a></strong></p>
                        </div>
                        <?php if (($this->flickrEnabled == 'Y') || (XG_SecurityHelper::userIsAdmin() && $this->showFlickrToOwner == 'Y')) { ?>
                        <div class="xg_module_body">
                            <h4><%= xg_html('FROM_FLICKR') %></h4>
                            <span id="flickrlogo"><img src="<%= xg_cdn('/xn_resources/widgets/photo/gfx/flickr/flickr.png') %>" width="100" height="28" alt="Flickr" /></span>
                            <p><%= xg_html('ADD_PHOTOS_FROM_YOUR_FLICKR') %></p>
                            <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('photo', 'flickr')) %>"><%= xg_html('ADD_PHOTOS') %></a></strong></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="adding_photos_module" class="xg_3col first-child" style="display:none">
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <h3><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" alt="<%= xg_html('SPINNER') %>" class="left" style="margin:0 15px 60px 0" /><strong><%= $this->approvalRequired ? xg_html('PERSON_IN_CHARGE', xnhtmlentities(XN_Application::load()->name)) : xg_html('LEAVE_WINDOW_OPEN') %></strong></h3>
                    <p><%= $this->approvalRequired ? xg_html('KEEP_PAGE_OPEN_PHOTOS') : xg_html('MEANWHILE_FEEL_FREE_PHOTOS', 'href="/" target="_blank"', xnhtmlentities(XN_Application::load()->name)) %></p>
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
<?php XG_App::ningLoaderRequire('xg.photo.photo.new'); ?>
<?php xg_footer(); ?>