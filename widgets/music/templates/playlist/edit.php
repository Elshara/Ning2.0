<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('EDIT_PLAYLIST'), null, array('forceDojo' => true));?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
					<%= xg_headline($title)%>
                    <div class="xg_colgroup">
                        <div class="xg_2col first-child">
                            <div class="xg_module">
                                <div class="xg_module_body"><?php
                                if ($this->uploaded) { ?>
                                    <div class="success" style="margin-bottom:1em">
                                        <p class="last-child"><%= xg_html('SONGS_SUCCESSFULLY_UPLOADED') %></p>
                                    </div>
                                <?php
                                } elseif ($this->trackCountChange < 0) { ?>
                                    <div class="notification">
                                        <p>
                                            <%= xg_html('N_TRACKS_REMOVED_FROM_PLAYLIST', abs($this->trackCountChange)) %>
                                        </p>
                                    </div><?php
                                } elseif ($this->trackCountChange > 0) { ?>
                                    <div class="success">
                                        <p>
                                            <%= xg_html('N_TRACKS_ADDED_TO_PLAYLIST', $this->trackCountChange) %>
                                        </p>
                                    </div><?php
                                } elseif ($this->limitReached) { ?>
                                    <div class="errordesc">
                                        <p>
                                            <%= xg_html('SORRY_YOU_CAN_ONLY_HAVE_N_TRACKS', $this->playlistLimit) %>
                                        </p>
                                    </div><?php
                                }
                                    if($this->canReorder) { ?>
                                    <p><%= xg_html('DRAG_TO_CHANGE_ORDER_PLAYLIST') %></p><?php
                                    } ?>
                                    <ol class="playlist<%= ($this->canReorder)?' can_reorder':'' %>" id="playlist"><?php
                                    $count = $this->begin;
                                    foreach($this->tracks as $track) {
                                        $myTrack = XG_SecurityHelper::passed(XG_SecurityHelper::checkCurrentUserContributed($this->_user, $track));
                                        $even = ++$count%2==0;?>
                                        <li class="<%= ($this->canReorder)?' draggable':'' %><%= ($even)?' alt':'' %><%= (!$track->my->enableProfileUsage && !$myTrack && ! XG_SecurityHelper::userIsAdmin())?' notshared':'' %>" _trackId="<%= $track->id; %>" >
                                            <span class="number"><%= $count %>.</span><?php
                                        if($myTrack || XG_SecurityHelper::userIsAdmin()){
                                            XG_App::ningLoaderRequire('xg.index.bulk'); ?>
                                            <a class="right desc delete"
                                                href="<%= xnhtmlentities($this->_buildUrl('track', 'delete', '?id='.$track->id.'&playlistId='.$this->playlist->id)) %>"
                                                onClick="return deleteLinkClicked(this);"
                                                title ="<%= xg_html('DELETE_THIS_TRACK_Q') %>"
                                                _confirmMessage ="<%= xg_html('ARE_YOU_SURE_DELETE_THIS_TRACK', xnhtmlentities(XG_FullNameHelper::fullName($this->user->title))) %>"
                                                _url = "<%= xnhtmlentities($this->_buildUrl('bulk', 'remove', array('limit' => 20, 'id' => $track->id, 'playlistId' => $this->playlist->id , 'xn_out' => 'json'))) %>"
                                                _displaySuccesDialog = "false"
                                                _successCallback = "removeTrackEntry"
                                                _progressTitle = "<%= xg_html('DELETING') %>"
                                                ><%= mb_strtolower(xg_html('DELETE')) %>
                                            </a><?php
                                            if($myTrack){ ?>
                                            <a href="<%= xnhtmlentities($this->_buildUrl('track', 'edit',  array('id' => $track->id, 'playlistId'=>$this->playlist->id))) %>" class="right desc edit"><%= mb_strtolower(xg_html('EDIT')) %></a><?php
                                            }
                                        } else {
                                            XG_App::ningLoaderRequire('xg.music.playlist.RemoveTrackLink'); ?>
                                            <a class="right desc delete" dojoType="RemoveTrackLink" style="visibility: hidden;" href="#"
                                                _url="<%= xnhtmlentities($this->_buildUrl('playlist', 'removeTrack', array('id' => $this->playlist->id, 'trackId' => $track->id, 'xn_out' => 'json'))) %>">
                                                <%= mb_strtolower(xg_html('DELETE')) %></a>
                                            </a>
                                        <?php
                                        }
                                        if($myTrack || XG_SecurityHelper::userIsAdmin() || ($track->my->enableProfileUsage) ){
                                            if($myTrack || XG_SecurityHelper::userIsAdmin() || ($track->my->enableDownloadLink) ){ ?>
                                            <a href="<%= xnhtmlentities($track->my->audioUrl) %>" class="left play-button"><?php
                                            } else { ?>
                                            <a href="#" _href="<%= xnhtmlentities($track->my->audioUrl) %>" class="left play-button"><?php
                                            }
                                        ?><img alt="<%= xg_html('PLAY') %>" src="<%= xg_cdn('/xn_resources/widgets/music/gfx/miniplayer.gif') %>" width="21" height="16"/></a><?php
                                        } ?>
                                            <span class="time"><%= $track->my->duration %></span>
                                            <span class="song"><%= xnhtmlentities($track->my->artist) %><%= ($track->my->artist && $track->my->trackTitle)?' &mdash;':'' %>
                                            <%= xnhtmlentities($track->my->trackTitle) %></span>
                                        </li><?php
                                    } ?>
                                    </ol>
                                    <?php if($this->canReorder) { ?>
                                        <form method="post" id="reorder-form" action="<%= xnhtmlentities($this->_buildUrl('playlist', 'reorder')) %>">
                                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                            <p class="buttongroup">
                                                <input type="hidden" name="id" value="<%= $this->playlist->id %>" style="width:500px" />
                                                <input type="hidden" name="begin" id="begin-field" value="<%= $this->begin %>" style="width:500px" />
                                                <input type="hidden" name="end" value="<%= $this->end %>" style="width:500px" />
                                                <input type="hidden" id="track-list" name="trackList" value="<%= $this->playlist->my->tracks %>" style="width:500px" />
                                                <input type="submit" id="done-button" class="button" value="<%= xg_html('DONE') %>" />
                                            </p>
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                        </div><?php
                        if($this->canAddTracks) { ?>
                        <div class="xg_1col">
                            <div class="xg_module">
                                <div class="xg_module_body">
                                    <h3><%= xg_html('ADD_MUSIC_ELLIPSIS') %></h3>
                                    <h4><%= xg_html('ELLIPSIS_FROM_YOUR_COMPUTER') %></h4>
                                    <p><%= xg_html('UPLOAD_MP3_FROM_YOUR_COMPUTER') %></p>
                                    <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('track', XG_MediaUploaderHelper::action(), array('playlistId'=>$this->playlist->id))) %>" class="desc add"><%= xg_html('ADD_MUSIC_TRACKS') %></a></strong></p>
                                </div>
                                <div class="xg_module_body">
                                    <h4><%= xg_html('ELLIP_FROM_ANOTHER_WEBSITE') %></h4>
                                    <p><%= xg_html('ADD_MUSIC_FROM_ANY_WEBSITE') %></p>
                                    <p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('track', 'newLink', array('playlistId'=>$this->playlist->id))) %>" class="desc add"><%= xg_html('ADD_MUSIC_TRACKS') %></a></strong></p>
                                </div>
                            </div>
                        </div><?php
                        } ?>
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
<?php XG_App::ningLoaderRequire('xg.music.playlist.edit', 'xg.music.shared.buttonplayer'); ?>
<!--[if gt IE 5.5]><script src="<%= xg_cdn('/xn_resources/widgets/shared/js/setInnerHtmlFromExternalScript.js') %>"></script><![endif]-->
<?php xg_footer(); ?>