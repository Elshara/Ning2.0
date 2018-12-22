<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Announcement.php');

class XG_AnnouncementTest extends UnitTestCase {
    protected static $originalAcknowledgements;

    public function setUp() {
        //  Clear acknowledged announcements
        $user = User::load(XN_Profile::current());
        $this->originalAcknowledgements = $user->my->announcementsAcknowledged;
        $user->my->announcementsAcknowledged = serialize(array());
        $user->save();
    }

    public function testGetAnnouncement() {
        $user = User::load(XN_Profile::current());
        $message = XG_Announcement::getAnnouncement();
        $this->assertNotNull($message);
        XG_Announcement::acknowledge($message[0]);
        $message2 = XG_Announcement::getAnnouncement();
        $this->assertNull($message2);
    }

    public function testAcknowledge() {
        $user = User::load(XN_Profile::current());
        $this->assertFalse(XG_Announcement::userHasAcknowledged('blah'));
        XG_Announcement::acknowledge('blah');
        $this->assertTrue(XG_Announcement::userHasAcknowledged('blah'));
        XG_Announcement::acknowledge('blah');
        $this->assertTrue(XG_Announcement::userHasAcknowledged('blah'));
        $this->assertFalse(XG_Announcement::userHasAcknowledged('blahblah'));
        XG_Announcement::acknowledge('blahblah');
        $this->assertTrue(XG_Announcement::userHasAcknowledged('blahblah'));
        $this->assertTrue(XG_Announcement::userHasAcknowledged('blah'));
    }

    public function tearDown() {
        //  Restore previous acknowledgements
        $user = User::load(XN_Profile::current());
        $user->my->announcementsAcknowledged = $this->originalAcknowledgements;
        $user->save();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
