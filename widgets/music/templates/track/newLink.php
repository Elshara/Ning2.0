<?php
xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_MUSIC_TRACKS'));
?>
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
                                        <form id="add_tracks_form" action="<%= xnhtmlentities($this->_buildUrl('track', 'createMultiple',array('isMainPlaylist'=>$this->addToMainPlaylist, 'playlistId' => $this->playlistId))) %>" method="post">
                                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                            <input type="hidden" id="numTracks" value="<%= $this->numTracks %>"/>
                                            <input type="hidden" id="linkMode" name="linkMode" value="1"/>
                                            <h3><%= xg_html('CHOOSE_MUSIC_TRACKS') %></h3>
                                            <p><%= xg_html('ENTER_THE_URL_MP3_TO_ADD') %></p>
                                            <?php
                                            $this->renderPartial('fragment_uploadErrors', 'track', array(
                                                    'id' => 'add_tracks_form_notify', 'failedFiles' => $this->failedFiles, 'allHadErrors' => true)); ?>
                                            <fieldset id="upload-track-step" class="nolegend">
                                                <big>
                                                    <ol class="options">
<?php
                                                    for ($i=1; $i <= $this->numTracks; $i++) {
                                                    	$name = "track_".sprintf('%02d',$i);
                                                    	?>
														<li><input type="text" name="<%=$name%>" id="<%=$name%>" value="<%=qh($_REQUEST[$name])%>" /></li>
<?php
                                                    } ?>
                                                    </ol>
                                                </big>
                                            </fieldset>
                                            <p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('ADD_TRACKS') %>" /></p>
                                        </form>
                                    </div>
                                    <div class="xg_module_body pad">
                                        <h4><%= xg_html('HOW_IT_WORKS') %></h4>
                                        <p><%= xg_html('MUSIC_PLAYER_WILL_LINK') %></p>
                                        <h4><%= xg_html('BE_NICE') %></h4>
                                        <p><%= xg_html('PLEASE_LINK_ONLY_TRACKS') %></p>
                                    </div>
                                </div>
                            </div>
                            <div class="xg_1col">
                                <div class="xg_module">
                                    <div class="xg_module_body">
                                        <h3><%= xg_html('MORE_WAY_TO_ADD_MUSIC') %></h3>
                                        <h4><%= xg_html('ELLIPSIS_FROM_YOUR_COMPUTER') %></h4>
                                        <p><%= xg_html('UPLOAD_MP3_FROM_YOUR_COMPUTER') %></p>
                                        <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('track', XG_MediaUploaderHelper::action(),array('isMainPlaylist'=>$this->addToMainPlaylist,'playlistId' => $this->playlistId))) %>" class="desc add"><%= xg_html('ADD_MUSIC_TRACKS') %></a></strong></p>
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