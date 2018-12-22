<?php xg_header(W_Cache::current('W_Widget')->dir, $title = $this->title);?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
					<%= xg_headline($title)%>
                    <div class="xg_colgroup">
                        <div class="xg_2col first-child">
                            <div class="xg_module">
                                <div class="xg_module_body">
                                    <ol class="playlist" id="playlist"><?php
                                    $count = $this->begin;
                                    foreach($this->tracks as $track) {
                                        $myTrack = XG_SecurityHelper::passed(XG_SecurityHelper::checkCurrentUserContributed($this->_user, $track));
                                        $even = ++$count%2==0;?>
                                        <li <%= ($even)?'class="alt"':'' %> _trackId="<%= $track->id; %>" >
                                            <span class="number"><%= $count %>.</span><?php
                                            if($myTrack || XG_SecurityHelper::userIsAdmin() || ($track->my->enableDownloadLink) ){ ?>
                                            <a href="<%= xnhtmlentities($track->my->audioUrl) %>" class="left play-button"><?php
                                            } else { ?>
                                            <a href="#" _href="<%= xnhtmlentities($track->my->audioUrl) %>" class="left play-button"><?php
                                            }
                                            ?><img alt="<%= xg_html('PLAY') %>" src="<%= xg_cdn('/xn_resources/widgets/music/gfx/miniplayer.gif') %>" width="21" height="16"/></a>
                                            <span class="time"><%= $track->my->duration %></span>
                                            <span class="song"><%= xnhtmlentities($track->my->artist) %><%= ($track->my->artist && $track->my->trackTitle)?' &mdash;':'' %>
                                            <%= xnhtmlentities($track->my->trackTitle) %></span>
                                        </li><?php
                                    } ?>
                                    </ol>
                                </div>
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
<?php XG_App::ningLoaderRequire( 'xg.music.shared.buttonplayer'); ?>
<?php xg_footer(); ?>