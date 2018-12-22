<?php
/**
 * The body and footer of the music module.
 *
 * @param $columnCount integer  the number of columns that the module spans
 */
XG_App::includeFileOnce('/lib/XG_FacebookHelper.php');
if (($this->trackCount>0)||($this->playlistSet=='podcast')){ ?>
    <div class="xg_module_body<%= ($this->userIsAdmin) ? ' admin':'' %> nopad">
<?php
    ob_start();
    // TODO: Pass booleans instead of "on"/"off", to make all parameters consistent [Jon Aquino 2008-01-24]
    // TODO: Use camel case instead of underscores, to make all parameters consistent [Jon Aquino 2008-01-24]
    $this->renderPartial('fragment_playerProper',   'playlist',
                                            array(  'autoplay'					=> $this->autoplay=='true',
                                                    'shuffle'					=> $this->shuffle=='true',
                                                    'width'						=> '100%',
                                                    'showplaylist'				=> $this->showPlaylist=='true',
                                                    'playlist_url'				=> $this->playlistUrl,
                                                    'display_add_links'		 	=> $this->displayAddToMineLinks,
                                                    'display_contributor'		=> $this->displayContributor,
                                                    'flexible_embed'            => 'on',
                                                    'detach_btn'                => 'on',
                                                    'ratings'                   => (($this->playlistSet=='podcast')||(!XN_Profile::current()->isLoggedIn())) ? 'off' : 'on',
                                                    'display_feature_btn'       =>
                                                    (($this->embed->getType() == 'profiles')?'on':'off'),
                                                    'containerWidth'            => $columnCount==1 ? 220 : 512,
                                                    'embed' => false));
    $playerHtml = trim(ob_get_contents());
    ob_end_clean();
    ?>
    <input type="hidden" name="musicPlayerHtml" class="musicPlayerHtml" value="<?php echo xnhtmlentities(preg_replace('/\s+/u', ' ', $playerHtml)) ?>" />
    <div class="musicplayer_visible">
        <%=$playerHtml;%>
    </div>
</div>

<?php if (XG_FacebookHelper::isAppEnabled('music')) {?>
    <div class="xg_module_body xg_module_facebook">
        <p><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/icon/facebook.gif') %>"><a href="<%= XG_FacebookHelper::getFacebookEmbedAppUrl('music') %>"><%= xg_html('FACEBOOK_ADD_TO_FACEBOOK') %></a></p>
    </div>
<?php } ?>

<?php
if ($this->userCanAddTracks || $this->userCanEditPlaylist) { ?>
<div class="xg_module_foot">
    <ul class="options"><?php
    if($this->userCanAddTracks) {?>
        <li class="left"><a class="desc add" href="<%=
         xnhtmlentities($this->_buildUrl('track', XG_MediaUploaderHelper::action(),array('playlistId'=>$this->playlistId, 'isMainPlaylist'=>$this->addToMainPlaylist))) %>"><%= xg_html('ADD_MUSIC_TRACKS') %></a></li><?php
    }
    if($this->userCanEditPlaylist) {?>
        <li class="right"><a class="desc edit" href="<%=
         xnhtmlentities($this->_buildUrl('playlist', 'edit', '?id='.$this->playlistId)) %>"><%= xg_html('EDIT_PLAYLIST') %></a></li><?php
    } ?>
    </ul>
</div>
<?php
} ?>
<?php
} else { ?>
    <div class="xg_module_body<%= ($this->userIsAdmin)?' admin':'' %> sparse">
        <?php
        if ($this->playlistSet=='homeplaylist') {
        } else if ($this->playlistSet=='userplaylist') {
        } else if ($this->playlistSet=='featured') {
            $this->userCanAddTracks = false; ?>
            <h3><strong><%= xg_html('NO_FEATURED_TRACKS') %></strong></h3>
            <p><%= xg_html('START_FEATURING_X_CLICK_Y','href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"') %></p>
        <?php
        } else { ?>
            <h3><strong><%= xg_html('NO_TRACKS_TO_PLAY') %></strong></h3>
            <p><%= xg_html('BOX_WILL_NOT_SHOW_TRACKS') %></p>
        <?php
        }
        if($this->userCanAddTracks) {?>
            <p><a class="desc add" href="<%= xnhtmlentities($this->_buildUrl('track', XG_MediaUploaderHelper::action(),array('playlistId'=>$this->playlistId, 'isMainPlaylist'=>$this->addToMainPlaylist))) %>"><%= xg_html('ADD_MUSIC_TRACKS') %></a></p>
        <?php
        } ?>
    </div>
<?php
} ?>
