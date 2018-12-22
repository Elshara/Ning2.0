<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/music/controllers/TrackController.php');
XG_App::includeFileOnce('/widgets/music/lib/helpers/Music_SecurityHelper.php');
XG_App::includeFileOnce('/widgets/music/lib/helpers/Music_UserHelper.php');
XG_App::includeFileOnce('/widgets/music/lib/helpers/Music_PlaylistHelper.php');
XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
Mock::generate('Music_SecurityHelper');
Mock::generate('Music_UserHelper');
Mock::generate('Music_PlaylistHelper');
Mock::generate('XG_ActivityHelper');
Mock::generate('Track');
Mock::generate('Playlist');
Mock::generate('AudioAttachment');
Mock::generate('stdClass', 'MockXN_Content', array('save', 'fileUrl'));
Mock::generate('stdClass', 'MockXN_AttributeContainer', array('set'));

class TrackControllerTest extends UnitTestCase {

    private $mocks;

    public function setUp() {
        XG_TestHelper::setCurrentWidget('music');
        $_POST = array();
        $_REQUEST = array();
        $this->helpers = array(
                'musicSecurityHelper' => new MockMusic_SecurityHelper(),
                'musicUserHelper' => new MockMusic_UserHelper(),
                'musicPlaylistHelper' => new MockMusic_PlaylistHelper(),
                'xgActivityHelper' => new MockXG_ActivityHelper(),
                'trackModel' => new MockTrack(),
                'playlistModel' => new MockPlaylist(),
                'audioAttachmentModel' => new MockAudioAttachment());
        $this->mocks = array_merge($this->helpers, array(
                'trackA' => new MockXN_Content(),
                'trackB' => new MockXN_Content(),
                'audioAttachmentA' => new MockXN_Content(),
                'audioAttachmentB' => new MockXN_Content(),
                'playlist' => new MockXN_Content(),
                'playlistMy' => new MockXN_AttributeContainer(),
                'user' => new MockXN_Content()));
        $this->mocks['playlist']->id = '555:Playlist:555';
        $this->mocks['playlist']->my = $this->mocks['playlistMy'];
        $this->mocks['trackA']->id = 'AAA:Track:AAA';
        $this->mocks['trackB']->id = 'BBB:Track:BBB';
        $this->mocks['user']->contributorName = 'Joe';
        $this->mocks['audioAttachmentA']->id = 'AAA:AudioAttachment:AAA';
        $this->mocks['audioAttachmentB']->id = 'BBB:AudioAttachment:BBB';
    }

    public function testCreateMultiple1() {
        foreach ($this->mocks as $key => $value) { ${$key} = $value; }
        $trackController = new TestTrackController();
        $musicSecurityHelper->expectOnce('checkCurrentUserCanAddMusic', array(XN_Profile::current()));
        $musicSecurityHelper->setReturnValue('checkCurrentUserCanAddMusic', 'Not allowed');
        $trackController->createMultiple(true, $this->helpers);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode(array(
            array('render', array('error', 'index')),
            )), $json->encode($trackController->calls));
    }

    public function testCreateMultiple2() {
        $_POST['uploadMarker'] = null;
        $_POST['linkMode'] = null;
        foreach ($this->mocks as $key => $value) { ${$key} = $value; }
        $trackController = new TestTrackController();
        $musicSecurityHelper->expectOnce('checkCurrentUserCanAddMusic', array(XN_Profile::current()));
        $musicSecurityHelper->setReturnValue('checkCurrentUserCanAddMusic', null);
        $trackController->createMultiple(true, $this->helpers);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode(array(
            array('redirectTo', array('new', 'track', array('sizeLimitError' => xg_text('UPLOAD_LIMIT_EXCEEDED')))),
            )), $json->encode($trackController->calls));
    }

    public function testCreateMultiple3() {
        $_POST['uploadMarker'] = null;
        $_POST['linkMode'] = 1;
        $_POST['track_3'] = 'http://example.org/foo_3.mp3';
        $_POST['track_1'] = 'c:\junk\foo_1.txt';
        $_REQUEST['playlistId'] = null;
        $_REQUEST['isMainPlaylist'] = 'yes';
        foreach ($this->mocks as $key => $value) { ${$key} = $value; }
        $trackController = new TestTrackController();
        $musicSecurityHelper->expectOnce('checkCurrentUserCanAddMusic', array(XN_Profile::current()));
        $musicSecurityHelper->setReturnValue('checkCurrentUserCanAddMusic', null);
        $musicUserHelper->expectOnce('load', array(XN_Profile::current()));
        $musicUserHelper->setReturnValue('load', $user);
        $trackModel->expectAt(0, 'create', array());
        $trackModel->setReturnValueAt(0, 'create', $trackA);
        $trackModel->expectAt(1, 'create', array());
        $trackModel->setReturnValueAt(1, 'create', $trackB);
        $trackA->expectOnce('save', array());
        $trackB->expectOnce('save', array());
        $musicPlaylistHelper->expectOnce('loadOrCreateDefaultNetworkPlaylist', array(W_Cache::getWidget('music')));
        $musicPlaylistHelper->setReturnValue('loadOrCreateDefaultNetworkPlaylist', array('playlist' => $playlist));
        $playlistMy->expectOnce('set', array('tracks', 'BBB:Track:BBB,AAA:Track:AAA,', XN_Attribute::STRING));
        $playlist->expectOnce('save', array());
        $musicUserHelper->expectOnce('addTracks', array($user, 2));
        $user->expectOnce('save', array());
        $xgActivityHelper->expectOnce('logActivityIfEnabled', array(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_HOME_TRACK, 'Joe', array(1 => $trackB, 3 => $trackA)));

        $trackController->createMultiple(false, $this->helpers);

        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual('http://example.org/foo_3.mp3', $trackA->my->audioUrl);
        $this->assertEqual('Y', $trackA->my->approved);
        $this->assertEqual('foo 3', $trackA->my->trackTitle);
        $this->assertEqual('foo_3.mp3', $trackA->my->filename);
        $this->assertEqual('http://example.org', $trackA->my->trackHostUrl);
        $this->assertEqual('c:\junk\foo_1.txt', $trackB->my->audioUrl);
        $this->assertEqual('Y', $trackB->my->approved);
        $this->assertEqual('c:\junk\foo 1.txt', $trackB->my->trackTitle);
        $this->assertEqual('c:\junk\foo_1.txt', $trackB->my->filename);
        $this->assertEqual('c:\junk\foo_1.txt', $trackB->my->trackHostUrl);
        $this->assertEqual(2, $playlist->my->trackCount);
        $this->assertEqual($json->encode(array(
                array('redirectTo', array('editMultiple', 'track', array('ids' => 'BBB:Track:BBB,AAA:Track:AAA', 'failedFiles' => '', 'playlistId' => '555:Playlist:555', 'trackCountChange' => 2))))),
                $json->encode($trackController->calls));
    }

    public function testCreateMultiple4() {
        $_POST['uploadMarker'] = null;
        $_POST['linkMode'] = 1;
        $_REQUEST['playlistId'] = null;
        $_REQUEST['isMainPlaylist'] = 'yes';
        foreach ($this->mocks as $key => $value) { ${$key} = $value; }
        $trackController = new TestTrackController();
        $musicSecurityHelper->expectOnce('checkCurrentUserCanAddMusic', array(XN_Profile::current()));
        $musicSecurityHelper->setReturnValue('checkCurrentUserCanAddMusic', null);
        $musicUserHelper->expectNever('load', array(XN_Profile::current()));

        $trackController->createMultiple(true, $this->helpers);

        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertNull($playlist->my->trackCount);
        $this->assertEqual($json->encode(array()),
                $json->encode($trackController->calls));
    }

    public function testCreateMultiple5() {
        $_POST['uploadMarker'] = null;
        $_POST['linkMode'] = 1;
        $_POST['track_3'] = 'http://example.org/foo_3.mp3';
        $_POST['track_1'] = 'c:\junk\foo_1.txt';
        $_REQUEST['playlistId'] = null;
        $_REQUEST['isMainPlaylist'] = 'no';
        foreach ($this->mocks as $key => $value) { ${$key} = $value; }
        $trackController = new TestTrackController();
        $musicSecurityHelper->expectOnce('checkCurrentUserCanAddMusic', array(XN_Profile::current()));
        $musicSecurityHelper->setReturnValue('checkCurrentUserCanAddMusic', null);
        $musicUserHelper->expectOnce('load', array(XN_Profile::current()));
        $musicUserHelper->setReturnValue('load', $user);
        $trackModel->expectAt(0, 'create', array());
        $trackModel->setReturnValueAt(0, 'create', $trackA);
        $trackModel->expectAt(1, 'create', array());
        $trackModel->setReturnValueAt(1, 'create', $trackB);
        $trackA->expectOnce('save', array());
        $trackB->expectOnce('save', array());
        $musicPlaylistHelper->expectOnce('loadOrCreateDefaultUserPlaylist', array(XN_Profile::current()));
        $musicPlaylistHelper->setReturnValue('loadOrCreateDefaultUserPlaylist', array('playlist' => $playlist));
        $playlist->my->trackCount = 5;
        $playlist->my->tracks = 't1,t2,t3,t4,t5,';
        $playlistMy->expectOnce('set', array('tracks', 'BBB:Track:BBB,AAA:Track:AAA,t1,t2,t3,t4,t5,', XN_Attribute::STRING));
        $playlist->expectOnce('save', array());
        $musicUserHelper->expectOnce('addTracks', array($user, 2));
        $user->expectOnce('save', array());
        $xgActivityHelper->expectOnce('logActivityIfEnabled', array(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_TRACK, 'Joe', array(1 => $trackB, 3 => $trackA)));

        $trackController->createMultiple(false, $this->helpers);

        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual('http://example.org/foo_3.mp3', $trackA->my->audioUrl);
        $this->assertEqual('Y', $trackA->my->approved);
        $this->assertEqual('foo 3', $trackA->my->trackTitle);
        $this->assertEqual('foo_3.mp3', $trackA->my->filename);
        $this->assertEqual('http://example.org', $trackA->my->trackHostUrl);
        $this->assertEqual('c:\junk\foo_1.txt', $trackB->my->audioUrl);
        $this->assertEqual('Y', $trackB->my->approved);
        $this->assertEqual('c:\junk\foo 1.txt', $trackB->my->trackTitle);
        $this->assertEqual('c:\junk\foo_1.txt', $trackB->my->filename);
        $this->assertEqual('c:\junk\foo_1.txt', $trackB->my->trackHostUrl);
        $this->assertEqual(7, $playlist->my->trackCount);
        $this->assertEqual($json->encode(array(
                array('redirectTo', array('editMultiple', 'track', array('ids' => 'BBB:Track:BBB,AAA:Track:AAA', 'failedFiles' => '', 'playlistId' => '555:Playlist:555', 'trackCountChange' => 2))))),
                $json->encode($trackController->calls));
    }

    public function testCreateMultiple6() {
        $_POST['uploadMarker'] = 1;
        $_POST['linkMode'] = null;
        $_POST['track_4'] = 'jon/foo_4.mp3';
        $_POST['track_4:status'] = '1';
        $_POST['track_4:type'] = 'audio/mp3';
        $_POST['track_4:size'] = '111';
        $_POST['track_3'] = 'jon/foo_3.mp3';
        $_POST['track_3:status'] = '0';
        $_POST['track_3:type'] = 'audio/mp3';
        $_POST['track_3:size'] = '888';
        $_POST['track_2'] = 'c:\junk\foo_2.aac';
        $_POST['track_2:status'] = '0';
        $_POST['track_2:type'] = 'audio/aac';
        $_POST['track_2:size'] = '111';
        $_POST['track_1'] = 'c:\junk\foo_1.gif';
        $_POST['track_1:status'] = '0';
        $_POST['track_1:type'] = 'image/gif';
        $_POST['track_1:size'] = '999';
        $_REQUEST['playlistId'] = '555:Playlist:555';
        $_REQUEST['isMainPlaylist'] = 'no';
        foreach ($this->mocks as $key => $value) { ${$key} = $value; }
        $trackController = new TestTrackController();
        $musicSecurityHelper->expectOnce('checkCurrentUserCanAddMusic', array(XN_Profile::current()));
        $musicSecurityHelper->setReturnValue('checkCurrentUserCanAddMusic', null);
        $musicUserHelper->expectOnce('load', array(XN_Profile::current()));
        $musicUserHelper->setReturnValue('load', $user);
        $trackModel->expectAt(0, 'create', array());
        $trackModel->setReturnValueAt(0, 'create', $trackA);
        $audioAttachmentModel->expectAt(0, 'create', array('jon/foo_3.mp3', $trackA, false, 'track_3', 'audio/mp3'));
        $audioAttachmentModel->setReturnValueAt(0, 'create', $audioAttachmentA);
        $audioAttachmentA->expectOnce('save', array());
        $audioAttachmentA->expectOnce('fileUrl', array('data'));
        $audioAttachmentA->setReturnValueAt(0, 'fileUrl', 'http://example.org/aaa');
        $trackA->expectOnce('save', array());
        $trackModel->expectAt(1, 'create', array());
        $trackModel->setReturnValueAt(1, 'create', $trackB);
        $audioAttachmentModel->expectAt(1, 'create', array('c:\junk\foo_1.gif', $trackB, false, 'track_1', 'image/gif'));
        $audioAttachmentModel->setReturnValueAt(1, 'create', $audioAttachmentB);
        $audioAttachmentB->expectOnce('save', array());
        $audioAttachmentB->expectOnce('fileUrl', array('data'));
        $audioAttachmentB->setReturnValueAt(0, 'fileUrl', 'http://example.org/bbb');
        $trackB->expectOnce('save', array());
        $playlistModel->expectOnce('load', array('555:Playlist:555'));
        $playlistModel->setReturnValue('load', $playlist);
        $playlist->my->trackCount = 5;
        $playlistMy->expectOnce('set', array('tracks', 'BBB:Track:BBB,AAA:Track:AAA,', XN_Attribute::STRING));
        $playlist->expectOnce('save', array());
        $musicUserHelper->expectOnce('addTracks', array($user, 2));
        $user->expectOnce('save', array());
        $xgActivityHelper->expectOnce('logActivityIfEnabled', array(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_TRACK, 'Joe', array(1 => $trackB, 3 => $trackA)));

        $trackController->createMultiple(false, $this->helpers);

        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual('AAA:AudioAttachment:AAA', $trackA->my->audioAttachment);
        $this->assertEqual('Y', $trackA->my->approved);
        $this->assertEqual('jon/foo_3', $trackA->my->trackTitle);
        $this->assertEqual('jon/foo_3.mp3', $trackA->my->filename);
        $this->assertEqual(null, $trackA->my->trackHostUrl);
        $this->assertEqual(888, $trackA->my->length);
        $this->assertEqual('http://example.org/aaa', $trackA->my->audioUrl);
        $this->assertEqual('BBB:AudioAttachment:BBB', $trackB->my->audioAttachment);
        $this->assertEqual('Y', $trackB->my->approved);
        $this->assertEqual('foo_1.gif', $trackB->my->trackTitle);
        $this->assertEqual('foo_1.gif', $trackB->my->filename);
        $this->assertEqual(null, $trackB->my->trackHostUrl);
        $this->assertEqual(999, $trackB->my->length);
        $this->assertEqual('http://example.org/bbb', $trackB->my->audioUrl);
        $this->assertEqual(7, $playlist->my->trackCount);
        $this->assertEqual($json->encode(array(
                array('redirectTo', array('editMultiple', 'track', array('ids' => 'BBB:Track:BBB,AAA:Track:AAA', 'failedFiles' => 'jon/foo_4.mp3,c:\junk\foo_2.aac', 'playlistId' => '555:Playlist:555', 'trackCountChange' => 2))))),
                $json->encode($trackController->calls));
    }

}

class TestTrackController extends Music_TrackController {

    public $calls = array();

    public function __construct() {
        parent::__construct(W_Cache::getWidget('music'));
        $this->_before();
    }

    public function render() {
        $args = func_get_args();
        $this->log('render', $args);
    }

    public function redirectTo() {
        $args = func_get_args();
        $this->log('redirectTo', $args);
    }

    private function log($method, $args) {
        $this->calls[] = array($method, $args);
    }

    public function createMultiple($dispatched, $helpers) {
        parent::createMultiple($dispatched, $helpers);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
