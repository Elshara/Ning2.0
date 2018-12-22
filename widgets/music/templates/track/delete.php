<?php
    xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('DELETE_TRACK'));
    $cancelUrl = $this->_buildUrl('playlist', 'edit');?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<%= xg_headline($title)%>
                <form method="POST">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                        <h3><%= xg_html('ARE_YOU_SURE_DELETE_THIS_TRACK') %></h3>
                            <ol class="playlist" id="playlist">
                                <li>
                                    <a href="<%= xnhtmlentities($this->track->my->audioUrl) %>" class="left play-button"><img alt="<%= xg_html('PLAY') %>" src="<%= xg_cdn('/xn_resources/widgets/music/gfx/miniplayer.gif') %>" width="21" height="16" /></a>
                                    <span class="time"><%= $this->track->my->duration %></span>
                                    <span class="song"><%= xnhtmlentities($this->track->title) %><%= ($this->track->my->artist && $this->track->title)?' &mdash;':'' %>
                                    <%= xnhtmlentities($this->track->my->artist) %></span>
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div class="xg_module_footer">
                    <p>
                        <input type="submit" class="button" value="<%= xg_html('DELETE') %>" />
                        <a class="button" href="<%= xnhtmlentities($cancelUrl) %>"><%= xg_html('CANCEL') %></a>
                    </p>
                    </div>
                </form>
            </div>
            <div class="xg_1col last-child">
                <?php xg_sidebar($this); ?>
            </div>
        </div>
    </div>
<?php XG_App::ningLoaderRequire('xg.music.shared.buttonplayer'); ?>
<?php xg_footer(); ?>
