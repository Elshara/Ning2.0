<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('EDIT_TRACK'));?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div id="edit_tracks_module" class="xg_3col first-child">
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <form id="editMultipleTracksForm" action="<%= xnhtmlentities($this->_buildUrl('track', 'updateMultiple',array('playlistId'=>$this->playlistId, 'trackCountChange' => $this->trackCountChange))) %>" method="post" enctype="multipart/form-data">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <?php
                                $skipUrl = $this->_buildUrl('playlist', 'edit', array('id'=>$this->playlistId, 'trackCountChange' => $this->trackCountChange));
                                $names = NF_App::routeRequest();
                                if (preg_match('/editMultiple/u', $names['actionName'])) { ?>
                                    <h3><%= xg_html('ADD_TITLE_DESCRIPTION_AND_OTHER_TO_TRACK_1') %></h3>
                                    <p><%= xg_html('EVERYTHING_IS_OPTIONAL') %></p>
                                    <p class="right"><a href="<%= xnhtmlentities($skipUrl) %>"><strong><%= xg_html('SKIP_THIS_STEP') %>&nbsp;&#187;</strong></a></p>
                                <?php
                                } else { ?>
                                    <h3><%= xg_html('ADD_TITLE_DESCRIPTION_AND_OTHER_TO_TRACK_2') %></h3>
                                    <p><strong><a href="<%= xnhtmlentities($skipUrl) %>"><%= xg_html('CANCEL_AND_RETURN_TO_PLAYLIST') %></a></strong></p>
                                <?php
                                }
                                // If we're on this page, then at least one file uploaded successfully
                                $this->renderPartial('fragment_uploadErrors', 'track', array(
                                        'id' => 'editMultipleTracksForm_notify', 'failedFiles' => $this->failedFiles, 'allHadErrors' => false));
                                foreach ($this->tracks as $i => $track) {
                                    $otherLicense = ($track->my->licenseUrl && !array_key_exists($track->my->licenseUrl, $this->availableLicenses) );
                                    $index = str_pad($i, 2, "0", STR_PAD_LEFT); ?>
                                    <input name="track<%= $index %>-id" id="track-<%= $index %>-id" type="hidden" value="<%= $track->id %>"/>
                                    <div class="edit_item clear">
                                        <h4>
                                            <a href="<%= xnhtmlentities($track->my->audioUrl) %>" class="play-button"><img src="<%= xg_cdn('/xn_resources/widgets/music/gfx/miniplayer.gif') %>" width="21" height="16" alt="" /></a>&nbsp;
                                            <%= xnhtmlentities($track->my->filename); %>
                                        </h4>
                                        <fieldset class="nolegend">
                                            <dl class="left" style="width:385px">
                                                <dt><label for="name"><%= xg_html('TRACK_TITLE') %></label></dt>
                                                <dd><input name="track<%= $index %>-title" id="track-<%= $index %>-title" value="<%= xnhtmlentities($track->my->trackTitle); %>" type="text" class="textfield" size="25" maxlength="200" /></dd>
                                                <dt><label for="artist"><%= xg_html('TRACK_ARTIST') %></label></dt>
                                                <dd><input name="track<%= $index %>-artist" id="track-<%= $index %>-artist" value="<%= xnhtmlentities($track->my->artist); %>" type="text" class="textfield" size="25" maxlength="200" /></dd>
                                                <dt><label for="album"><%= xg_html('TRACK_ALBUM') %></label></dt>
                                                <dd><input name="track<%= $index %>-album" id="track-<%= $index %>-album" value="<%= xnhtmlentities($track->my->album); %>" type="text" class="textfield" size="25" maxlength="200" /></dd>
                                                <dd>
                                                    <ul class="details"><?php
                                                    if(W_Cache::getWidget('main')->config['disableMusicDownload']!='yes'){ ?>
                                                        <li><label><input name="track<%= $index %>-enableDownloadLink" type="checkbox" class="checkbox" <%= ($track->my->enableDownloadLink == 'on') ? 'checked="checked"' : ''; %> /><%= xg_html('ENABLE_DOWNLOAD_LINK') %></label></li><?php
                                                        } ?>
                                                        <li><label><input name="track<%= $index %>-enableProfileUsage" type="checkbox" class="checkbox" <%= ($track->my->enableProfileUsage == 'on') ? 'checked="checked"' : ''; %> /><%= xg_html('ALLOW_PEOPLE_TO_ADD_TRACK_TO_PROFILE') %></label></li>
                                                    </ul>
                                                </dd>
                                            </dl>
                                            <div class="artwork">
                                                <p class="left"><label for="artwork" style="font-weight:normal"><%= xg_html('TRACK_ARTWORK') %></label></p>
                                                <div dojoType="BazelImagePicker" trimUploadsOnSubmit="1" fieldName="track<%= $index %>-artwork" allowTile="0" onChange=""
                                                <?php
                                                    echo 'swatchWidth="100px" swatchHeight="100px" currentImagePath ="' . $track->my->artworkUrl . '"';
                                                ?>></div>
                                            </div>
                                        </fieldset>
                                        <fieldset class="nolegend">
                                            <legend class="toggle">
                                                <?php XG_App::ningLoaderRequire('xg.music.track.MetadataToggleLink'); ?>
                                                <a href="#" dojoType="MetadataToggleLink" style="visibility: hidden;"><span><!--[if IE]>&#9658;<![endif]--><![if !IE]>&#9654;<![endif]></span><%= xg_html('MORE_ELLIPSIS') %></a>
                                            </legend>
                                            <div id="advanced<%= $index %>" style="display:<%= ($_GET['advanced']=='yes')?'block':'none' %>;">
                                                <div class="left block">
                                                    <dl>
                                                        <dt><label for="track<%= $index %>-genre"><%= xg_html('GENRE') %></label></dt>
                                                        <dd><input id="track<%= $index %>-genre" name="track<%= $index %>-genre" type="text" class="textfield" size="25" value="<%= xnhtmlentities($track->my->genre); %>" /></dd>
                                                        <dt><label for="track<%= $index %>-year"><%= xg_html('YEAR') %></label></dt>
                                                        <dd><input id="track<%= $index %>-year" name="track<%= $index %>-year" type="text" class="textfield" size="25" value="<%= xnhtmlentities($track->my->year); %>" /></dd>
                                                        <dt><label for="track<%= $index %>-label"><%= xg_html('LABEL') %></label></dt>
                                                        <dd><input id="track<%= $index %>-label" name="track<%= $index %>-label" type="text" class="textfield" size="25" value="<%= xnhtmlentities($track->my->label); %>" /></dd>
                                                        <dd><label><input type="checkbox" class="checkbox" name="track<%= $index %>-explicit" <%= ($track->my->explicit == 'yes') ? 'checked="checked"' : ''; %>/><%= xg_html('EXPLICIT_LYRICS') %></label></dd>
                                                    </dl>
                                                </div>
                                                <div class="right block">
                                                    <dl>
                                                        <dt><label for="track<%= $index %>-artistUrl"><%= xg_html('ARTIST_SITE') %></label></dt>
                                                        <dd><input id="track<%= $index %>-artistUrl" name="track<%= $index %>-artistUrl" type="text" class="textfield" size="25" value="<%= xnhtmlentities($track->my->artistUrl); %>" /></dd>
                                                        <dt><label for="track<%= $index %>-trackHostUrl"><%= xg_html('HOSTING_SITE') %></label></dt>
                                                        <dd><input id="track<%= $index %>-trackHostUrl" name="track<%= $index %>-trackHostUrl" type="text" class="textfield" size="25" value="<%= xnhtmlentities($track->my->trackHostUrl); %>" /></dd>
                                                        <dt><label for="track<%= $index %>-labelUrl"><%= xg_html('LABEL_SITE') %></label></dt>
                                                        <dd><input id="track<%= $index %>-labelUrl" name="track<%= $index %>-labelUrl" type="text" class="textfield" size="25" value="<%= xnhtmlentities($track->my->labelUrl); %>" /></dd>
                                                        <dt><label for="track<%= $index %>-licenseUrl"><%= xg_html('LICENSE') %></label></dt>
                                                        <dd>
                                                            <select class="license" style="width:180px" id="track<%= $index %>-licenseUrl" name="track<%= $index %>-licenseUrl" onChange="licenseChanged(this.value, 'track<%= $index %>-otherLicense')">
                                                                <option><%= xg_html('SELECT_LICENSE') %></option><?php
                                                            foreach($this->availableLicenses as $licenseUrl=>$licenseName) {?>
                                                                <option value="<%= xnhtmlentities($licenseUrl); %>" <%= ($track->my->licenseUrl == $licenseUrl)?'selected="selected"':'' %>><%= xnhtmlentities($licenseName); %></option><?php
                                                            }?>
                                                                <option value="other"><%= xg_html('OTHER') %></option>
                                                            </select>
                                                        </dd>
                                                    </dl>
                                                    <dl style="display:<%= (!$otherLicense)?'none':'block' %>" id="track<%= $index %>-otherLicense">
                                                        <dt><label for="track<%= $index %>-otherlicenseName"><%= xg_html('LICENSE_NAME') %></label></dt>
                                                        <dd><input id="track<%= $index %>-otherlicenseName" name="track<%= $index %>-otherlicenseName" type="text" class="textfield" size="25" value="<%= ($otherLicense)?xnhtmlentities($track->my->licenseName):''; %>" /></dd>
                                                        <dt><label for="track<%= $index %>-otherlicenseUrl"><%= xg_html('LICENSE_URL') %></label></dt>
                                                        <dd><input id="track<%= $index %>-otherlicenseUrl" name="track<%= $index %>-otherlicenseUrl" type="text" class="textfield" size="25" value="<%= ($otherLicense)?xnhtmlentities($track->my->licenseUrl):''; %>" /></dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                <?php
                                }
                                ?>
                                <p class="clear align-right"><strong><input type="submit" class="button" value="<%= xg_html('DONE') %>" /></strong></p>
                            </form>
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
<?php XG_App::ningLoaderRequire('xg.music.shared.buttonplayer','xg.shared.BazelImagePicker', 'xg.music.track.editMultiple'); ?>
<?php xg_footer(); ?>
