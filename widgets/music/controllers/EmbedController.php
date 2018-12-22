<?php
/**

 * Dispatches requests pertaining to "embeds", which are reusable
 * page components.
 */
class Music_EmbedController extends W_Controller {
    protected function _before() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_PlaylistHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_SecurityHelper.php');
    }

    public function action_embed1($args) { $this->renderEmbed($args['embed'], 1); }
    public function action_embed2($args) { $this->renderEmbed($args['embed'], 2); }
    private function renderEmbed($embed, $columnCount) {
        $this->userIsAdmin = XG_SecurityHelper::userIsAdmin($this->_user);
        $this->setValuesUrl = $this->_buildUrl('embed', 'setValues', array('id' => $embed->getLocator(), 'xn_out' => 'json', 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        if ($embed->getType() == 'profiles') {
            $this->playlist_options = array(array('label' => xg_text('PLAYLIST'), 'value' => 'userplaylist'),
                                            array('label' => xg_text('PODCAST'), 'value' => 'podcast'));
            $defaultPlaylistSet = 'userplaylist';
            $this->addToMainPlaylist = null;
            $this->embedLogoLink = xg_absolute_url('/');
        } else {
            $this->playlist_options = array(array('label' => xg_text('PLAYLIST'), 'value' => 'homeplaylist'),
                                            array('label' => xg_text('MOST_RECENT_TRACKS'), 'value' => 'recent'),
                                            array('label' => xg_text('FEATURED_TRACKS'), 'value' => 'featured'),
                                            array('label' => xg_text('HIGHEST_RATED_TRACKS'), 'value' => 'highestrated'),
                                            array('label' => xg_text('PODCAST'), 'value' => 'podcast'));
            $defaultPlaylistSet = 'homeplaylist';
            $this->addToMainPlaylist = 'yes';
            $this->embedLogoLink = xg_absolute_url('/');
        }
        $embed->set('autoplay', $embed->get('autoplay') ? $embed->get('autoplay') : '');
        $embed->set('shuffle', $embed->get('shuffle') ? $embed->get('shuffle') : '');
        $embed->set('playlistSet', $embed->get('playlistSet') ? $embed->get('playlistSet') : $defaultPlaylistSet);
        list($this->playlistUrl, $this->trackCount) = self::getSelectedPlaylist($embed);
        if (!$embed->get('playlistUrl')) { // fix for BAZ-6476
            $embed->set('playlistUrl', $this->playlistUrl);
        }
        $embed->set('showPlaylist', $embed->get('showPlaylist') ? $embed->get('showPlaylist') : 'true');
        // @todo Possibly remove all these instance variables by accessing them through $this->embed [Jon Aquino 2007-05-31]
        $this->autoplay                 = $embed->get('autoplay');
        $this->shuffle                  = $embed->get('shuffle');
        $this->showPlaylist             = $embed->get('showPlaylist');
        $this->playlistId               = $embed->get('playlistId');
        $this->playlistSet              = $embed->get('playlistSet');
        $this->columnCount              = $columnCount;
        $this->displayContributor       = (($embed->get('playlistSet') !='homeplaylist')&&($embed->get('playlistSet') !='userplaylist')&&($embed->get('playlistSet') !='podcast'));
        $this->displayAddToMineLinks    = ( ($embed->get('playlistSet')=='podcast')|| (!$this->_user->isLoggedIn()) || ($embed->isOwnedByCurrentUser()&&($embed->getType() == 'profiles')))?'off':null;
        if ($this->playlistId) { $this->playlist = Playlist::load($this->playlistId); }
        $this->userCanAddTracks = $this->currentUserCanAddTracks($embed);
        $this->userCanEditPlaylist = $this->currentUserCanEditPlaylist($embed, $this->playlist);
        $this->embed = $embed;
        $this->displayEmbedLink         = true;
        $this->title = xg_text('MUSIC');
        if ($embed->getType() == 'profiles' && $this->embed->isOwnedByCurrentUser()) { $this->title = xg_text('MY_MUSIC'); }
        $this->render('embed');
    }

    public function action_setValues() {
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owner.'); }
        $embed->set('autoplay'      , $_POST['autoplay']        );
        $embed->set('shuffle'       , $_POST['shuffle']        );
        $embed->set('showPlaylist'  , $_POST['showPlaylist']    );
        $embed->set('playlistSet'   , $_POST['playlistSet']     );
        if($embed->get('playlistSet')=='podcast'){
            if (Music_PlaylistHelper::hostHasCrossdomainFile($_POST['playlistUrl'])) {
                $embed->set('playlistUrl', $_POST['playlistUrl'] );
            } else {
                $embed->set('playlistUrl', $this->_buildUrl('playlist', 'showExternal', '?url='.urlencode($_POST['playlistUrl'])) );
            }
        }
        list($this->playlistUrl, $this->trackCount) = self::getSelectedPlaylist($embed);
        $embed->set('playlistUrl', $this->playlistUrl);
        $this->addToMainPlaylist = ($embed->getType() == 'profiles')?null:'yes';
        // @todo Possibly remove all these instance variables by accessing them through $this->embed [Jon Aquino 2007-05-31]
        $this->playlistUrl  = $embed->get('playlistUrl');
        $this->playlistId   = $embed->get('playlistId');
        $this->showPlaylist = $embed->get('showPlaylist');
        $this->autoplay     = $embed->get('autoplay');
        $this->shuffle      = $embed->get('shuffle');
        $this->playlistSet  = $embed->get('playlistSet');
        $this->embed = $embed;
        $this->displayContributor       = (($embed->get('playlistSet') !='homeplaylist')&&($embed->get('playlistSet') !='userplaylist')&&($embed->get('playlistSet') !='podcast'));
        if ($this->playlistId) { $this->playlist = Playlist::load($this->playlistId); }
        $this->userCanAddTracks = $this->currentUserCanAddTracks($embed);
        $this->userCanEditPlaylist = $this->currentUserCanEditPlaylist($embed, $this->playlist);
        $columnCount =  $_POST['columnCount'];
        ob_start();
        $this->renderPartial('fragment_moduleBodyAndFooter', array('columnCount' => $columnCount));
        $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    /**
     * Returns whether the current user can see the Add Music link.
     *
     * @param $embed XG_Embed  stores the module data.
     * @return boolean  whether permission is granted
     */
    private function currentUserCanAddTracks($embed) {
        return $embed->isOwnedByCurrentUser() && in_array($embed->get('playlistSet'), array('recent', 'userplaylist', 'homeplaylist'));
    }

    /**
     * Returns whether the current user can see the Add Music link.
     *
     * @param $embed XG_Embed  stores the module data.
     * @param $playlist XN_Content|W_Content  the current Playlist, or null if there isn't one yet
     * @return boolean  whether permission is granted
     */
    private function currentUserCanEditPlaylist($embed, $playlist) {
        return in_array($embed->get('playlistSet'), array('userplaylist', 'homeplaylist')) && $playlist && XG_SecurityHelper::passed(XG_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $playlist));
    }

    /**
     * Returns a playlist and track count
     *
     * @param $embed XG_Embed  stores the module data.
     * @return array  an array containing the playlist url (string) and the number of tracks in the playlist (int)
     */
    private function getSelectedPlaylist($embed) {
        switch ($embed->get('playlistSet')) {
            case 'homeplaylist':
                $playlistId = $this->_widget->privateConfig['mainPlaylist'];
                if(mb_strlen($playlistId)) {
                    $homeTracks = W_Cache::getWidget('music')->dispatch('playlist', 'show', array($playlistId,'internal'));
                    $embed->set('playlistId', $playlistId);
                    return array($this->_buildUrl('playlist', 'show', array('fmt' => 'xspf', 'id' => $playlistId)), count($homeTracks));
                } else {
                    $embed->set('playlistId', null);
                    return array(null, 0);
                }
            case 'userplaylist':
                $user = User::load($embed->getOwnerName());
                $playlistId = Music_PlaylistHelper::get($user, 'defaultPlaylist');
                if($playlistId) {
                    $userTracks = W_Cache::getWidget('music')->dispatch('playlist', 'show', array($playlistId,'internal'));
                    $embed->set('playlistId', $playlistId);
                    return array($this->_buildUrl('playlist', 'show', array('fmt' => 'xspf', 'id' => $playlistId)), count($userTracks));
                } else {
                    $embed->set('playlistId', null);
                    return array(null, 0);
                }
            case 'recent':
                $recentTracks = W_Cache::getWidget('music')->dispatch('track', 'list');
                $embed->set('playlistId', null);
                return array($this->_buildUrl('track', 'list', array('fmt' => 'xspf')), count($recentTracks));
            case 'featured':
                $featuredTracks = W_Cache::getWidget('music')->dispatch('track', 'listPromoted');
                $embed->set('playlistId', null);
                return array($this->_buildUrl('track', 'listPromoted', array('fmt' => 'xspf')), count($featuredTracks));
            case 'highestrated':
                $highestRatedTracks = W_Cache::getWidget('music')->dispatch('track', 'listHighestRated');
                $embed->set('playlistId', null);
                return array($this->_buildUrl('track', 'listHighestRated', array('fmt' => 'xspf')), count($highestRatedTracks));
            case 'podcast':
                return array($embed->get('playlistUrl'), 1);
            default:
                return array(null, 0);
        }
    }


}
