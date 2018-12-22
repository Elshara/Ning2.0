<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
EventWidget::init();
Mock::generate('stdClass', 'MockXN_Content');
Mock::generate('XN_ProfileSet');
Mock::generate('XN_Query');
Mock::generate('XG_PagingList');
Mock::generate('Events_BroadcastHelper');
Mock::generate('Events_InvitationHelper');
Mock::generate('Events_EventHelper');
Mock::generate('XG_JobHelper');


class Events_BroadcastHelperTest extends UnitTestCase {

    private function createEvent() {
        $event = new MockXN_Content();
        $event->id = '123:Event:456';
        return $event;
    }

    public function setUp() {
        $this->helper = new MockEvents_BroadcastHelper();
        $this->invitationHelper = new MockEvents_InvitationHelper();
        $this->eventHelper = new MockEvents_EventHelper();
        $this->jobHelper = new MockXG_JobHelper();
        TestBroadcastHelper::init($this->helper, $this->invitationHelper, $this->eventHelper, $this->jobHelper);
    }

    public function testStatusChanged1() {
        $attendingProfileSet = new MockXN_ProfileSet();
        $attendingProfileSet->expectOnce('removeMember', array('jane'));
        $this->helper->expectOnce('_loadOrCreateProfileSet', array('xg_event_broadcast_123_Event_456_attending', array('xg_event_123_Event_456', 'xg_event_broadcast')));
        $this->helper->setReturnValue('_loadOrCreateProfileSet', $attendingProfileSet);
        $this->helper->expectOnce('acceptingBroadcasts', array('jane'));
        $this->helper->setReturnValue('acceptingBroadcasts', true);
        Events_BroadcastHelper::statusChanged('jane', '123:Event:456', EventAttendee::ATTENDING, EventAttendee::NOT_INVITED, $this->helper);
    }

    public function testStatusChanged2() {
        $mightAttendProfileSet = new MockXN_ProfileSet();
        $mightAttendProfileSet->expectOnce('addMembers', array('jane'));
        $this->helper->expectOnce('_loadOrCreateProfileSet', array('xg_event_broadcast_123_Event_456_might_attend', array('xg_event_123_Event_456', 'xg_event_broadcast')));
        $this->helper->setReturnValue('_loadOrCreateProfileSet', $mightAttendProfileSet);
        $this->helper->expectOnce('acceptingBroadcasts', array('jane'));
        $this->helper->setReturnValue('acceptingBroadcasts', true);
        Events_BroadcastHelper::statusChanged('jane', '123:Event:456', EventAttendee::NOT_RSVP, EventAttendee::MIGHT_ATTEND, $this->helper);
    }

    public function testStatusChanged3() {
        $mightAttendProfileSet = new MockXN_ProfileSet();
        $mightAttendProfileSet->expectOnce('removeMember', array('jane'));
        $notAttendingProfileSet = new MockXN_ProfileSet();
        $notAttendingProfileSet->expectOnce('addMembers', array('jane'));
        $this->helper->expectAt(0, '_loadOrCreateProfileSet', array('xg_event_broadcast_123_Event_456_might_attend', array('xg_event_123_Event_456', 'xg_event_broadcast')));
        $this->helper->setReturnValueAt(0, '_loadOrCreateProfileSet', $mightAttendProfileSet);
        $this->helper->expectAt(1, '_loadOrCreateProfileSet', array('xg_event_broadcast_123_Event_456_not_attending', array('xg_event_123_Event_456', 'xg_event_broadcast')));
        $this->helper->setReturnValueAt(1, '_loadOrCreateProfileSet', $notAttendingProfileSet);
        $this->helper->expectOnce('acceptingBroadcasts', array('jane'));
        $this->helper->setReturnValue('acceptingBroadcasts', true);
        Events_BroadcastHelper::statusChanged('jane', '123:Event:456', EventAttendee::MIGHT_ATTEND, EventAttendee::NOT_ATTENDING, $this->helper);
    }

    public function testAllowBroadcasts1() {
        $eventAttendees = new MockXG_PagingList();
        $eventAttendees->pageCount = 0;
        $this->helper->setReturnValue('_createPagingList', $eventAttendees);
        $this->helper->expectNever('_allowBroadcastsProper');
        $this->jobHelper->expectNever('create');
        Events_BroadcastHelper::allowBroadcasts('jane', $this->helper);
    }

    public function testAllowBroadcasts2() {
        $eventAttendees = new MockXG_PagingList();
        $eventAttendees->pageCount = 1;
        $this->helper->setReturnValue('_createPagingList', $eventAttendees);
        $this->helper->expectOnce('_allowBroadcastsProper', array('jane', $eventAttendees));
        $this->jobHelper->expectNever('create');
        Events_BroadcastHelper::allowBroadcasts('jane', $this->helper);
    }

    public function testAllowBroadcasts3() {
        $eventAttendees = new MockXG_PagingList();
        $eventAttendees->pageCount = 2;
        $eventAttendees->totalCount = 20;
        $this->helper->setReturnValue('_time', 610860355);
        $this->helper->setReturnValue('_createPagingList', $eventAttendees);
        $this->helper->expectNever('_allowBroadcastsProper');
        $this->jobHelper->expectOnce('create', array(array(
            array(array('Events_BroadcastHelper', 'task_allowBroadcasts'), 0, 'jane', 610860356)
        )));
        Events_BroadcastHelper::allowBroadcasts('jane', $this->helper);
    }

    public function testAllowBroadcasts4() {
        $eventAttendees = new MockXG_PagingList();
        $eventAttendees->pageCount = 2;
        $eventAttendees->totalCount = 40;
        $this->helper->setReturnValue('_time', 610860355);
        $this->helper->setReturnValue('_createPagingList', $eventAttendees);
        $this->helper->expectNever('_allowBroadcastsProper');
        $this->jobHelper->expectOnce('create', array(array(
            array(array('Events_BroadcastHelper', 'task_allowBroadcasts'), 0, 'jane', 610860356),
            array(array('Events_BroadcastHelper', 'task_allowBroadcasts'), 1, 'jane', 610860356)
        )));
        Events_BroadcastHelper::allowBroadcasts('jane', $this->helper);
    }

    public function testTaskAllowBroadcasts1() {
        $this->doTestTaskAllowBroadcasts(0, 0, 30);
    }

    public function testTaskAllowBroadcasts2() {
        $this->doTestTaskAllowBroadcasts(3, 90, 120);
    }


    private function doTestTaskAllowBroadcasts($page, $expectedBegin, $expectedEnd) {
        $query = new MockXN_Query();
        $query->expectCallCount('filter', 2);
        $query->expectAt(0, 'filter', array('my->screenName', '=', 'jane'));
        $query->expectAt(1, 'filter', array('createdDate', '<=', '2008-04-02T06:42:39+00:00', 'date'));
        $query->setReturnValue('filter', $query);
        $query->expectOnce('order', array('createdDate', 'asc', 'date'));
        $query->setReturnValue('order', $query);
        $query->expectOnce('begin', array($expectedBegin));
        $query->setReturnValue('begin', $query);
        $query->expectOnce('end', array($expectedEnd));
        $query->setReturnValue('end', $query);
        $query->expectOnce('execute', array());
        $query->setReturnValue('execute', '[query-results]');
        $this->eventHelper->expectOnce('query', array('EventAttendee'));
        $this->eventHelper->setReturnValue('query', $query);
        $this->helper->expectOnce('_allowBroadcastsProper', array('jane', '[query-results]'));
        Events_BroadcastHelper::task_allowBroadcasts($page, 'jane', 1207118559, $this->helper);
    }

    public function testAllowBroadcastsProper() {
        $profileSet = new MockXN_ProfileSet();
        $profileSet->expectCallCount('addMembers', 3);
        $profileSet->expectAt(0, 'addMembers', array('jane'));
        $profileSet->expectAt(1, 'addMembers', array('jane'));
        $profileSet->expectAt(2, 'addMembers', array('jane'));
        $this->helper->expectCallCount('_profileSet', 3);
        $this->helper->setReturnValue('_profileSet', $profileSet);
        $this->helper->expectAt(0, '_profileSet', array('123:Event:456', EventAttendee::ATTENDING));
        $this->helper->expectAt(1, '_profileSet', array('123:Event:456', EventAttendee::MIGHT_ATTEND));
        $this->helper->expectAt(2, '_profileSet', array('123:Event:456', EventAttendee::NOT_ATTENDING));
        Events_BroadcastHelper::_allowBroadcastsProper('jane', array(
            $this->createEventAttendee(EventAttendee::NOT_RSVP),
            $this->createEventAttendee(EventAttendee::ATTENDING),
            $this->createEventAttendee(EventAttendee::MIGHT_ATTEND),
            $this->createEventAttendee(EventAttendee::NOT_ATTENDING),
        ), $this->helper);
    }

    private function createEventAttendee($status) {
        $eventAttendee = new MockXN_Content();
        $eventAttendee->my->eventId = '123:Event:456';
        $eventAttendee->my->status = $status;
        return $eventAttendee;
    }

    public function testBroadcast1() {
        $event = $this->createEvent();
        $this->helper->expectCallCount('_send', 2);
        $this->helper->expectAt(0, '_send', array('xg_event_broadcast_123_Event_456_attending@lists', $event, 'Abc'));
        $this->helper->expectAt(1, '_send', array('xg_event_broadcast_123_Event_456_not_attending@lists', $event, 'Abc'));
        $this->helper->expectNever('_broadcastToInvitees');
        Events_BroadcastHelper::broadcast('Abc', $event, 1, 0, 1, 0, $this->helper);
    }

    public function testBroadcast2() {
        $event = $this->createEvent();
        $this->helper->expectOnce('_send', array('xg_event_broadcast_123_Event_456_might_attend@lists', $event, 'Abc'));
        $this->helper->expectOnce('_broadcastToInvitees', array('Abc', '123:Event:456'));
        Events_BroadcastHelper::broadcast('Abc', $event, 0, 1, 0, 1, $this->helper);
    }

    public function testBroadcastToInvitees1() {
        $invitations = new MockXG_PagingList();
        $invitations->pageCount = 0;
        $this->invitationHelper->expectOnce('getInvitations', array('123:Event:456', 10, true));
        $this->invitationHelper->setReturnValue('getInvitations', $invitations);
        $this->helper->expectNever('_broadcastToInviteesProper');
        $this->jobHelper->expectNever('create');
        Events_BroadcastHelper::_broadcastToInvitees('Abc', '123:Event:456', $this->helper);
    }

    public function testBroadcastToInvitees2() {
        $invitations = new MockXG_PagingList();
        $invitations->pageCount = 1;
        $this->invitationHelper->expectOnce('getInvitations', array('123:Event:456', 10, true));
        $this->invitationHelper->setReturnValue('getInvitations', $invitations);
        $this->helper->expectOnce('_broadcastToInviteesProper', array('Abc', '123:Event:456', $invitations));
        $this->jobHelper->expectNever('create');
        Events_BroadcastHelper::_broadcastToInvitees('Abc', '123:Event:456', $this->helper);
    }

    public function testBroadcastToInvitees3() {
        $invitations = new MockXG_PagingList();
        $invitations->pageCount = 2;
        $invitations->totalCount = 20;
        $this->helper->setReturnValue('_time', 610860355);
        $this->invitationHelper->expectOnce('getInvitations', array('123:Event:456', 10, true));
        $this->invitationHelper->setReturnValue('getInvitations', $invitations);
        $this->helper->expectNever('_broadcastToInviteesProper');
        $this->jobHelper->expectOnce('create', array(array(
            array(array('Events_BroadcastHelper', 'task_broadcastToInvitees'), 0, 'Abc', '123:Event:456', 610860356)
        )));
        Events_BroadcastHelper::_broadcastToInvitees('Abc', '123:Event:456', $this->helper);
    }

    public function testBroadcastToInvitees4() {
        $invitations = new MockXG_PagingList();
        $invitations->pageCount = 2;
        $invitations->totalCount = 40;
        $this->helper->setReturnValue('_time', 610860355);
        $this->invitationHelper->expectOnce('getInvitations', array('123:Event:456', 10, true));
        $this->invitationHelper->setReturnValue('getInvitations', $invitations);
        $this->helper->expectNever('_broadcastToInviteesProper');
        $this->jobHelper->expectOnce('create', array(array(
            array(array('Events_BroadcastHelper', 'task_broadcastToInvitees'), 0, 'Abc', '123:Event:456', 610860356),
            array(array('Events_BroadcastHelper', 'task_broadcastToInvitees'), 1, 'Abc', '123:Event:456', 610860356)
        )));
        Events_BroadcastHelper::_broadcastToInvitees('Abc', '123:Event:456', $this->helper);
    }

    public function testTaskBroadcastToInvitees1() {
        $this->doTestTaskBroadcastToInvitees(0, 0, 30);
    }

    public function testTaskBroadcastToInvitees2() {
        $this->doTestTaskBroadcastToInvitees(3, 90, 120);
    }

    private function doTestTaskBroadcastToInvitees($page, $expectedBegin, $expectedEnd) {
        $query = new MockXN_Query();
        $query->expectCallCount('filter', 2);
        $query->expectAt(0, 'filter', array('label', '=', 'event-invitation-123:Event:456'));
        $query->expectAt(1, 'filter', array('createdDate', '<=', '2008-04-02T06:42:39+00:00', 'date'));
        $query->setReturnValue('filter', $query);
        $query->expectOnce('order', array('createdDate', 'asc', 'date'));
        $query->setReturnValue('order', $query);
        $query->expectOnce('begin', array($expectedBegin));
        $query->setReturnValue('begin', $query);
        $query->expectOnce('end', array($expectedEnd));
        $query->setReturnValue('end', $query);
        $query->expectOnce('execute', array());
        $query->setReturnValue('execute', array());
        $this->helper->expectOnce('_createInvitationQuery', array());
        $this->helper->setReturnValue('_createInvitationQuery', $query);
        $this->helper->expectOnce('_broadcastToInviteesProper', array('Abc', '123:Event:456', array()));
        Events_BroadcastHelper::task_broadcastToInvitees($page, 'Abc', '123:Event:456', 1207118559, $this->helper);
    }

    public function testAcceptingBroadcasts1() { $this->assertTrue(Events_BroadcastHelper::acceptingBroadcasts($this->createUser('Y', 'Y'))); }
    public function testAcceptingBroadcasts2() { $this->assertTrue(Events_BroadcastHelper::acceptingBroadcasts($this->createUser('Y', 'N'))); }
    public function testAcceptingBroadcasts3() { $this->assertTrue(Events_BroadcastHelper::acceptingBroadcasts($this->createUser('Y', null))); }
    public function testAcceptingBroadcasts4() { $this->assertFalse(Events_BroadcastHelper::acceptingBroadcasts($this->createUser('N', 'Y'))); }
    public function testAcceptingBroadcasts5() { $this->assertFalse(Events_BroadcastHelper::acceptingBroadcasts($this->createUser('N', 'N'))); }
    public function testAcceptingBroadcasts6() { $this->assertFalse(Events_BroadcastHelper::acceptingBroadcasts($this->createUser('N', null))); }
    public function testAcceptingBroadcasts7() { $this->assertTrue(Events_BroadcastHelper::acceptingBroadcasts($this->createUser(null, 'Y'))); }
    public function testAcceptingBroadcasts8() { $this->assertFalse(Events_BroadcastHelper::acceptingBroadcasts($this->createUser(null, 'N'))); }
    public function testAcceptingBroadcasts9() { $this->assertTrue(Events_BroadcastHelper::acceptingBroadcasts($this->createUser(null, null))); }

    private function createUser($emailEventBroadcastPref, $emailNewMessagePref) {
        $user = new MockXN_Content();
        $user->my->emailEventBroadcastPref = $emailEventBroadcastPref;
        $user->my->emailNewMessagePref = $emailNewMessagePref;
        return $user;
    }

}

class TestBroadcastHelper extends Events_BroadcastHelper {
    public static function init($i, $invitationHelper, $eventHelper, $jobHelper) {
        parent::$i = $i;
        parent::$invitationHelper = $invitationHelper;
        parent::$eventHelper = $eventHelper;
        parent::$jobHelper = $jobHelper;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
