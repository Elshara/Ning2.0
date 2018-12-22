<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/BulkHelperTestCase.php');
XG_App::includeFileOnce('/widgets/music/lib/helpers/Music_BulkHelper.php');

class Music_BulkHelperTest extends BulkHelperTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('music');
    }

    public function testSetPrivacy() {
        list($friendlyTrackId, $friendlyAAId) = $this->createTrack(false, 'on');
        list($semifriendlyTrackId, $semifriendlyAAId) = $this->createTrack(true, 'on');
        list($unfriendlyTrackId, $unfriendlyAAId) = $this->createTrack(true, 'off');
        $openPlaylistId = $this->createPlaylist(false);
        $closedPlaylistId = $this->createPlaylist(true);

        Music_BulkHelper::setPrivacy(30, false);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $friendlyTrack = XN_Content::load($friendlyTrackId);
        $friendlyAA = XN_Content::load($friendlyAAId);
        $semifriendlyTrack = XN_Content::load($semifriendlyTrackId);
        $semifriendlyAA = XN_Content::load($semifriendlyAAId);
        $unfriendlyTrack = XN_Content::load($unfriendlyTrackId);
        $unfriendlyAA = XN_Content::load($unfriendlyAAId);
        $openPlaylist = XN_Content::load($openPlaylistId);
        $closedPlaylist = XN_Content::load($closedPlaylistId);

        $this->assertEqual(false, $friendlyTrack->isPrivate);
        $this->assertEqual(false, $friendlyAA->isPrivate);
        $this->assertEqual(false, $semifriendlyTrack->isPrivate);
        $this->assertEqual(false, $semifriendlyAA->isPrivate);
        $this->assertEqual(true, $unfriendlyTrack->isPrivate);
        $this->assertEqual(true, $unfriendlyAA->isPrivate);
        $this->assertEqual(false, $openPlaylistId->isPrivate);
        $this->assertEqual(false, $closedPlaylistId->isPrivate);

        Music_BulkHelper::setPrivacy(30, true);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $this->checkPrivacy(true, array($friendlyTrackId, $friendlyAAId, $semifriendlyTrackId, $semifriendlyAAId, $unfriendlyTrackId,
            $unfriendlyAAId, $openPlaylistId, $closedPlaylistId));
    }

    /**
     * Creates a dummy Track and associated AudioAttachment for testing.  Returns their IDs.
     *
     * @param   $isPrivate boolean          Specifies if the Track should be private (true) or public (false).
     * @param   $enableProfileUsage  string Set the enableProfileUsage property of the track to this value.  Should be 'on' or 'off'.
     * @return  array                       ID of the Track and ID of the AudioAttachment.
     */
    private function createTrack($isPrivate, $enableProfileUsage) {
        $track = Track::create();
        $track->isPrivate = $isPrivate;
        $track->my->enableProfileUsage = $enableProfileUsage;
        $track->save();
        $audioAttachment = $this->createAudioAttachment($track);
        $audioAttachment->save();
        $this->assertEqual($isPrivate, $track->isPrivate);
        $this->assertEqual($isPrivate, $audioAttachment->isPrivate);
        return array($track->id, $audioAttachment->id);
    }

    /**
     * Creates a dummy AudioAttachment for testing.  Returns it's ID.
     *
     * @param   $track   Track  Track to attach the dummy AudioAttachment to.
     * @return  string          ID of the AudioAttachment.
     */
    private function createAudioAttachment($track) {
        $audioAttachment = W_Content::create('AudioAttachment');
        $audioAttachment->my->mozzle = W_Cache::current('W_Widget')->dir;
        $audioAttachment->title = 'test_audio_attachment';
        $audioAttachment->my->audio = $track->id;
        $audioAttachment->isPrivate = $track->isPrivate;
        $audioAttachment->my->isSource = 'N';
        $audioAttachment->my->mimeType = 'audio/x-mp3';
        $audioAttachment->set('data', null, XN_Attribute::UPLOADEDFILE);
        return $audioAttachment;
    }

    /**
     * Creates a dummy playlist for testing.  Returns it's ID.
     *
     * @param   boolean isPrivate   Whether the playlist should be private (true) or public (false).
     * @return  string              ID of the playlist created.
     */
    private function createPlaylist($isPrivate) {
        $playlist = Playlist::create();
        $playlist->isPrivate = $isPrivate;
        $playlist->save();
        return $playlist->id;
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
