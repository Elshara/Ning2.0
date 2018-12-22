<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_SecurityHelper.php');

class Events_SecurityHelperTest extends UnitTestCase {

    public function testCurrentUserCanSendInvitesProper() {
        $this->assertFalse(TestSecurityHelper::currentUserCanSendInvitesProper(true, EventAttendee::ATTENDING, true, false));
        $this->assertTrue(TestSecurityHelper::currentUserCanSendInvitesProper(true, EventAttendee::MIGHT_ATTEND, false, false));
        $this->assertFalse(TestSecurityHelper::currentUserCanSendInvitesProper(false, EventAttendee::NOT_ATTENDING, true, false));
        $this->assertFalse(TestSecurityHelper::currentUserCanSendInvitesProper(false, EventAttendee::ATTENDING, false, false));
        $this->assertFalse(TestSecurityHelper::currentUserCanSendInvitesProper(true, EventAttendee::NOT_INVITED, true, false));
        $this->assertTrue(TestSecurityHelper::currentUserCanSendInvitesProper(true, EventAttendee::NOT_RSVP, false, false));
        $this->assertFalse(TestSecurityHelper::currentUserCanSendInvitesProper(false, EventAttendee::NOT_INVITED, true, false));
        $this->assertFalse(TestSecurityHelper::currentUserCanSendInvitesProper(false, EventAttendee::NOT_RSVP, false, false));
    }

}

class TestSecurityHelper extends Events_SecurityHelper {
    public static function currentUserCanSendInvitesProper($canSendNetworkInvites, $attendeeStatus, $eventIsClosed, $isEventCreator) {
        return parent::currentUserCanSendInvitesProper($canSendNetworkInvites, $attendeeStatus, $eventIsClosed, $isEventCreator);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
