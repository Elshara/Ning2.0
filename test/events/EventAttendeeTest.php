<?php	# $Id: $
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_EventHelper.php');
Mock::generate('XN_Query');
Mock::generate('Events_EventHelper');

class EventAttendeeTest extends UnitTestCase {
    public function setUp() {
        XG_TestHelper::setCurrentWidget('events');
        EventWidget::init();
        $this->eh = new Events_EventHelper;
        $this->now = $this->eh->dateToTs();

        $this->u1 = Events_EventHelper::create('User');
        Events_EventHelper::update(NULL, $this->u1,array('title'=>'U1','defaultVisibility'=>'all','addCommentPermission'=>'all'));

        $this->u2 = Events_EventHelper::create('User');
        Events_EventHelper::update(NULL, $this->u2,array('title'=>'U2','defaultVisibility'=>'all','addCommentPermission'=>'all'));

        $this->u3 = Events_EventHelper::create('User');
        Events_EventHelper::update(NULL, $this->u3,array('title'=>'U3','defaultVisibility'=>'all','addCommentPermission'=>'all'));

        $GLOBALS['UNIT_TEST_SKIP_PROFILE_CHECK_IN_USER_LOAD'] = 1;
    }

    public function testSetGetStatus () { # void
        $e = array();
        $e[0] = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now),'+'));
        $e[1] = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now),'+'));
        $e[2] = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now),'+'));
        $e[3] = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now),'+'));

        EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U1', $e[1], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U1', $e[3], EventAttendee::MIGHT_ATTEND);

        EventAttendee::setStatus('U2', $e[2], EventAttendee::NOT_ATTENDING);
        EventAttendee::setStatus('U2', $e[3], EventAttendee::NOT_ATTENDING);

        $this->assertEqual(EventAttendee::getStatuses('U1',$e), array(
            $e[0]->id => EventAttendee::ATTENDING,
            $e[1]->id => EventAttendee::ATTENDING,
            $e[2]->id => EventAttendee::NOT_INVITED,
            $e[3]->id => EventAttendee::MIGHT_ATTEND,
            ) );

        $this->assertEqual(EventAttendee::getStatuses('U1',$e[0]), EventAttendee::ATTENDING);

        $this->assertEqual(EventAttendee::getStatuses('U2',$e), array(
            $e[0]->id => EventAttendee::NOT_INVITED,
            $e[1]->id => EventAttendee::NOT_INVITED,
            $e[2]->id => EventAttendee::NOT_ATTENDING,
            $e[3]->id => EventAttendee::NOT_ATTENDING,
            ) );
    }

    //
    public function testAttendees () { # void
        $e[0] = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now),'+'));
        $e[1] = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now),'+'));
        $e[2] = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now),'+'));

        EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U1', $e[1], EventAttendee::ATTENDING);

        EventAttendee::setStatus('U2', $e[2], EventAttendee::NOT_ATTENDING);

        EventAttendee::setStatus('U3', $e[1], EventAttendee::MIGHT_ATTEND);

        usleep(250*1000);
        EventAttendee::setStatus('U2', $e[1], EventAttendee::ATTENDING);

        $this->assertEqual( XG_TestHelper::titles(EventAttendee::getAttendees($e[0], EventAttendee::ATTENDING, 1)), array('U1') );
        $this->assertEqual( XG_TestHelper::titles(EventAttendee::getAttendees($e[1], EventAttendee::ATTENDING, 2)), array('U2','U1',) );
        $this->assertEqual( XG_TestHelper::titles(EventAttendee::getAttendees($e[2], EventAttendee::NOT_ATTENDING, 1)), array('U2') );
    }

    //
    public function testEventTypes () { # void
        $e[0] = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now+100000),'+'));
        $e[1] = Event::create(qw('title>T1+eventType>AA2+startDate>'.$this->eh->dateToStr($this->now+150000),'+'));
        $e[2] = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now+200000),'+'));

        EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U1', $e[1], EventAttendee::MIGHT_ATTEND);
        EventAttendee::setStatus('U1', $e[2], EventAttendee::MIGHT_ATTEND);

        EventAttendee::setStatus('U2', $e[1], EventAttendee::NOT_ATTENDING);
        EventAttendee::setStatus('U2', $e[2], EventAttendee::ATTENDING);

        $this->assertEqual(EventAttendee::getEventTypes('U1'), array('AA1'=>2,'AA2'=>1));
        $this->assertEqual(EventAttendee::getEventTypes('U2'), array('AA1'=>1));

        $this->assertEqual( XG_TestHelper::ids(EventAttendee::getEventsByType('U1','AA1',2)), array($e[0]->id,$e[2]->id));
        $this->assertEqual( XG_TestHelper::ids(EventAttendee::getEventsByType('U1','AA2',2)), array($e[1]->id));

        $this->assertEqual( XG_TestHelper::ids(EventAttendee::getEventsByType('U2','AA1',2)), array($e[2]->id));
        $this->assertEqual( XG_TestHelper::ids(EventAttendee::getEventsByType('U2','AA2',2)), array());
    }

    //
    public function testUpcomingEvents () { # void
        $e[0] = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now+200000),'+'));
        $e[1] = Event::create(qw('title>T1+eventType>AA2+startDate>'.$this->eh->dateToStr($this->now+100000),'+'));
        $e[2] = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now-100000),'+'));

        EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U1', $e[1], EventAttendee::MIGHT_ATTEND);
        EventAttendee::setStatus('U1', $e[2], EventAttendee::NOT_ATTENDING);

        EventAttendee::setStatus('U2', $e[0], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U2', $e[1], EventAttendee::NOT_ATTENDING);

        $this->assertEqual(XG_TestHelper::ids( EventAttendee::getUpcomingEvents('U1', 2) ), array($e[1]->id, $e[0]->id,));
        $this->assertEqual(XG_TestHelper::ids( EventAttendee::getUpcomingEvents('U2', 2) ), array($e[0]->id,));
    }

    //
    public function testNotAttendingEvents () { # void
        $e[0] = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now+200000),'+'));
        $e[1] = Event::create(qw('title>T1+eventType>AA2+startDate>'.$this->eh->dateToStr($this->now+100000),'+'));
        $e[2] = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now-100000),'+'));

        EventAttendee::setStatus('U1', $e[0], EventAttendee::NOT_ATTENDING);
        EventAttendee::setStatus('U1', $e[1], EventAttendee::NOT_ATTENDING);
        EventAttendee::setStatus('U1', $e[2], EventAttendee::NOT_ATTENDING);

        EventAttendee::setStatus('U2', $e[0], EventAttendee::NOT_ATTENDING);
        EventAttendee::setStatus('U2', $e[1], EventAttendee::ATTENDING);

        $this->assertEqual(XG_TestHelper::ids( EventAttendee::getNotAttendingEvents('U1', 2) ), array($e[1]->id, $e[0]->id,));
        $this->assertEqual(XG_TestHelper::ids( EventAttendee::getNotAttendingEvents('U2', 2) ), array($e[0]->id,));
    }


    //
    public function testAttendingEvents () { # void
        $e[0] = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now+200000),'+'));
        $e[1] = Event::create(qw('title>T1+eventType>AA2+startDate>'.$this->eh->dateToStr($this->now+100000),'+'));
        $e[2] = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now-100000),'+'));

        EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U1', $e[1], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U1', $e[2], EventAttendee::ATTENDING);

        EventAttendee::setStatus('U2', $e[0], EventAttendee::NOT_ATTENDING);
        EventAttendee::setStatus('U2', $e[1], EventAttendee::ATTENDING);

        $this->assertEqual(XG_TestHelper::ids( EventAttendee::getAttendingEvents('U1', 2) ), array($e[1]->id, $e[0]->id,));
        $this->assertEqual(XG_TestHelper::ids( EventAttendee::getAttendingEvents('U2', 2) ), array($e[1]->id,));
    }

    //
    public function testEventsByDate() { # void
        $e[0] = Event::create(qw('title>T1+startDate>2008-02-02 00:00','+'));
        $e[1] = Event::create(qw('title>T1+startDate>2008-02-02 13:59','+'));
        $e[2] = Event::create(qw('title>T1+startDate>2008-02-02 23:59','+'));
        $e[3] = Event::create(qw('title>T1+startDate>2008-02-04 00:00','+'));

        EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U1', $e[1], EventAttendee::NOT_ATTENDING);
        EventAttendee::setStatus('U1', $e[2], EventAttendee::ATTENDING);
        EventAttendee::setStatus('U1', $e[3], EventAttendee::ATTENDING);

        $calendar = EventAttendee::getCalendar('U1', '2008-01','2008-01');
        $list = EventAttendee::getEventsByDate('U1','2008-02-02',$calendar);
        $this->assertEqual(count($list),2);
        $this->assertEqual($list[0]->id,$e[0]->id);
        $this->assertEqual($list[1]->id,$e[2]->id);
        $this->assertTrue($list->isFirstPage());
        $this->assertFalse($list->isLastPage());
        $this->assertPattern('/2008-02-04/',$list->nextPageUrl());
    }

    public function testAttendeesToScreenNames() {
        $this->assertEqual(array(), EventAttendee::screenNames(array()));
        $this->assertEqual(array('jane' => 'jane'), EventAttendee::screenNames(array($this->createEventAttendee('jane'))));
        $this->assertEqual(array('jane' => 'jane', 'bob' => 'bob'), EventAttendee::screenNames(array($this->createEventAttendee('jane'), $this->createEventAttendee('bob'))));
    }

    private function createEventAttendee($screenName, $eventId = '123:Event:456') {
        $eventAttendee = new stdClass();
        $eventAttendee->my->screenName = $screenName;
        $eventAttendee->my->eventId = $eventId;
        return $eventAttendee;
    }

    private function createEvent($id) {
        $event = new stdClass();
        $event->my->screenName = $screenName;
        $event->id = $id;
        return $event;
    }

    public function testAttendeesToEventsProper1() {
        $list = TestEventAttendee::_attendeesToEventsProper(new XG_PagingList());
        $this->assertEqual(0, count($list));
        $this->assertEqual(0, count($list->getList()));
        $this->assertEqual(0, $list->totalCount);
    }

    public function testAttendeesToEventsProper2() {
        $eventAttendees = array($this->createEventAttendee('a', 'A'), $this->createEventAttendee('b', 'B'), $this->createEventAttendee('c', 'C'));
        $events = array($this->createEvent('A'), $this->createEvent('B'), $this->createEvent('C'));
        $query = new MockXN_Query();
        $query->expectOnce('filter', array('id', 'in', array('A', 'B', 'C')));
        $query->setReturnValue('filter', $query);
        $query->expectNever('end');
        $query->expectNever('order');
        $query->expectOnce('execute', array());
        $query->setReturnValue('execute', $events);
        $eventHelper = new MockEvents_EventHelper();
        $eventHelper->expectOnce('query', array('Event'));
        $eventHelper->setReturnValue('query', $query);
        $list = new XG_PagingList();
        $list->setResult($eventAttendees, 7);
        TestEventAttendee::setEventHelper($eventHelper);
        $list = TestEventAttendee::_attendeesToEventsProper($list);
        $this->assertEqual($events, $list->getList());
        $this->assertEqual(7, $list->totalCount);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

class TestEventAttendee extends EventAttendee {
    public static function _attendeesToEventsProper($list) {
        return parent::_attendeesToEventsProper($list);
    }
    public static function setEventHelper($eventHelper) {
        parent::$eventHelper = $eventHelper;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
