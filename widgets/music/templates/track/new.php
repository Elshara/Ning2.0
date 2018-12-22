<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_MUSIC_TRACKS')); ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
                <div class="xg_colgroup">
                    <div class="xg_3col first-child">
						<%= xg_headline($title)%>
                        <div class="xg_colgroup" id="add_tracks_module">
                            <div class="xg_2col first-child">
                                <div class="xg_module">
                                    <div class="xg_module_body pad">
                                        <form id="add_tracks_form" action="<%= xnhtmlentities($this->_buildUrl('track', 'createMultiple',array('isMainPlaylist'=>$this->addToMainPlaylist, 'playlistId' => $this->playlistId))) %>" method="post" enctype="multipart/form-data">
                                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                            <input type="hidden" name="uploadMarker" value="present"/>
                                            <input type="hidden" id="numTracks" value="<%= $this->numTracks %>"/>
                                            <h3><%= xg_html('UPLOAD_MUSIC_FROM_YOUR_COMPUTER') %></h3>
                                            <?php
                                            $this->renderPartial('fragment_uploadErrors', 'track', array(
                                                    'id' => 'add_tracks_form_notify', 'sizeLimitError' => $this->sizeLimitError, 'failedFiles' => $this->failedFiles, 'allHadErrors' => true)); ?>
                                            <?php if(!$this->hideBulkUploaderReferences) { ?>
                                            <p style="margin:1em 0"><img class="left" style="margin-right:5px" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/add-multiple.png'))) %>" alt="" /><%= xg_html('HAVE_LOT_OF_MUSIC') %><br />
                                            <a href="<%= xnhtmlentities($this->_widget->buildUrl('track', 'newWithUploader', array('isMainPlaylist'=>$this->addToMainPlaylist, 'playlistId' => $this->playlistId))) %>"><strong><%= xg_html('TRY_BULK_MUSIC_UPLOADER') %></strong></a></p>
                                            <?php } ?>
                                            <fieldset id="upload-track-step" class="nolegend">
                                                <big>
                                                    <ol class="options">
<?php
                                                    for ($i=1; $i <= $this->numTracks; $i++) { ?>
                                                        <li><input class="inputFile" type="file" name="track_<%= str_pad($i, 2, "0", STR_PAD_LEFT); %>" id="track_<%= str_pad($i, 2, "0", STR_PAD_LEFT); %>" value="" /></li>
<?php
                                                    } ?>
                                                    </ol>
                                                </big>
                                            </fieldset>
                                            <p><small><%= xg_html('I_HAVE_RIGHT_TO_UPLOAD_SONGS', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'termsOfService', array('previousUrl' => XG_HttpHelper::currentUrl()))) . '"') %></small></p>
                                            <p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('UPLOAD_TRACKS') %>" /></p>
                                        </form>
                                    </div>
                                    <div class="xg_module_body pad">
                                        <h4><%= xg_html('HOW_IT_WORKS') %></h4>
                                        <p><%= xg_html('EACH_MP3_FILE_MAY_BE_UP_TO') %></p>
                                        <h4><%= xg_html('BE_NICE') %></h4>
                                        <p><%= xg_html('PLEASE_UPLOAD_ONLY_TRACKS') %></p>
                                    </div>
                                </div>
                            </div>
                            <div class="xg_1col">
                                <div class="xg_module">
                                    <div class="xg_module_body">
                                        <h3><%= xg_html('MORE_WAY_TO_ADD_MUSIC') %></h3>
                                        <h4><%= xg_html('ELLIP_FROM_ANOTHER_WEBSITE') %></h4>
                                        <p><%= xg_html('ADD_MUSIC_FROM_ANY_WEBSITE') %></p>
                                        <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('track', 'newLink',array('isMainPlaylist'=>$this->addToMainPlaylist,'playlistId' => $this->playlistId ))) %>"><%= xg_html('ADD_MUSIC_TRACKS') %></a></strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="adding_music_module" class="xg_3col first-child" style="display:none">
                            <div class="xg_module">
                                <div class="xg_module_body pad">
                                    <h3><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" alt="<%= xg_html('SPINNER') %>" class="left" style="margin:0 15px 60px 0" /><strong><%= $this->approvalRequired ? xg_html('PERSON_IN_CHARGE', xnhtmlentities(XN_Application::load()->name)) : xg_html('LEAVE_WINDOW_OPEN') %></strong></h3>
                                    <p><%= $this->approvalRequired ? xg_html('KEEP_PAGE_OPEN_MUSIC') : xg_html('MEANWHILE_FEEL_FREE_MUSIC', 'href="/" target="_blank"', xnhtmlentities(XN_Application::load()->name)) %></p>
                                </div>
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
<?php XG_App::ningLoaderRequire('xg.music.track.new'); ?>
<?php xg_footer(); ?>