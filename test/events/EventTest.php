<?php	# $Id: $
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class EventTest extends UnitTestCase {
    public function setUp() {
        XG_TestHelper::setCurrentWidget('events');
        XG_TestHelper::deleteTestObjects();
        EventWidget::init();
        $this->eh = new Events_EventHelper;
        $this->now = $this->eh->dateToTs();
    }

    //
    public function testCreate () { # void
        $event = Event::create(qw('title>title2 description>description2 eventType>My_Event startDate>2008-02-02 location>PaloAlto'));
        $this->assertEqual($event->title,'title2');
        $this->assertEqual($event->description,'description2');
        $this->assertEqual($this->eh->typeToList($event->my->eventType),array('My_Event'));
        $this->assertEqual($event->my->startDate,'2008-02-02 00:00');
        $this->assertEqual($event->my->location,'PaloAlto');
        $this->assertTrue(XG_TestHelper::exists($event->id));
    }

    public function testUpdate () { # void
        $event = Event::create(qw('title>title2 description>description2 eventType>My_Event startDate>2008-02-02 location>PaloAlto privacy>' . Event::INVITED));
        $this->assertEqual($event->title,'title2');
        $this->assertEqual($event->description,'description2');
        $this->assertEqual($this->eh->typeToList($event->my->eventType),array('My_Event'));
        $this->assertEqual($event->my->startDate,'2008-02-02 00:00');
        $this->assertEqual($event->my->location,'PaloAlto');
        $this->assertEqual($event->my->privacy,Event::INVITED);
        $this->assertTrue($event->isPrivate);
        $this->assertEqual($event->my->excludeFromPublicSearch,'Y');
        $this->assertTrue(XG_TestHelper::exists($event->id));

        Event::update($event, qw('location>MountainView title>title3 eventType>BBB privacy>' . Event::ANYONE));
        $this->assertEqual($event->title,'title3');
        $this->assertEqual($event->description,'description2');
        $this->assertEqual($this->eh->typeToList($event->my->eventType),array('BBB'));
        $this->assertEqual($event->my->startDate,'2008-02-02 00:00');
        $this->assertEqual($event->my->location,'MountainView');
        $this->assertEqual($event->my->privacy,Event::ANYONE);
        $this->assertFalse($event->isPrivate);
        $this->assertEqual($event->my->excludeFromPublicSearch,'N');

        $event2 = W_Model::findById('Event',$event->id);
        $this->assertEqual($event2->title,'title3');
        $this->assertEqual($event2->description,'description2');
        $this->assertEqual($this->eh->typeToList($event2->my->eventType),array('BBB'));
        $this->assertEqual($event2->my->startDate,'2008-02-02 00:00');
        $this->assertEqual($event2->my->location,'MountainView');
        $this->assertEqual($event->my->privacy,Event::ANYONE);
        $this->assertFalse($event->isPrivate);
        $this->assertEqual($event->my->excludeFromPublicSearch,'N');
    }

    //
    public function testUpcomingEvents() { # void
        $e1 = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now+100000),'+'));
        $e2 = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now-100000),'+'));
        $e3 = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now+200000),'+'));

        $list = Event::getUpcomingEvents(2);
        $this->assertEqual(count($list),2);
        $this->assertEqual($list[0]->id,$e1->id);
        $this->assertEqual($list[0]->my->startDate,$e1->my->startDate);
        $this->assertEqual($list[1]->id,$e3->id);
        $this->assertEqual($list[1]->my->startDate,$e3->my->startDate);

        $list = Event::getUpcomingEvents(1);
        $this->assertEqual(count($list),1);
        $this->assertEqual($list->totalCount,2);
        $this->assertEqual($list[0]->id,$e1->id);
        $this->assertEqual($list[0]->my->startDate,$e1->my->startDate);

    }

    //
    public function testFeaturedEvents() { # void
        $e1 = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now+100000),'+'));
        $e2 = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now-100000),'+'));
        $e3 = Event::create(qw('title>T1+startDate>'.$this->eh->dateToStr($this->now+200000),'+'));
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_PromotionHelper::promote($e1);
        Event::update($e1,array());

        $list= Event::getFeaturedEvents(2);
        $this->assertEqual(count($list),1);
        $this->assertEqual($list->totalCount, 1);
        $this->assertEqual($list[0]->id,$e1->id);
        $this->assertEqual($list[0]->my->startDate,$e1->my->startDate);
    }

    //
    public function testEventsByDate() { # void
        $e1 = Event::create(qw('title>T1+startDate>2008-02-02 00:00','+'));
        $e2 = Event::create(qw('title>T1+startDate>2008-02-02 13:59','+'));
        $e3 = Event::create(qw('title>T1+startDate>2008-02-02 23:59','+'));
        $e4 = Event::create(qw('title>T1+startDate>2008-02-03 00:00','+'));

        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_PromotionHelper::promote($e2);
        Event::update($e2,array());

        list($featured,$list) = Event::getEventsByDate('2008-02-02');
        $this->assertEqual(count($list),2);
        $this->assertEqual($featured->id, $e2->id);
        $this->assertEqual($list[0]->id,$e1->id);
        $this->assertEqual($list[1]->id,$e3->id);
    }

    public function testEventsByType() { # void
        $e1 = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now+200000),'+'));
        $e2 = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now+250000),'+'));
        $e3 = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now+300000),'+'));
        $e4 = Event::create(qw('title>T1+eventType>AA1+startDate>'.$this->eh->dateToStr($this->now+400000),'+'));

        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_PromotionHelper::promote($e2);
        Event::update($e2,array());

        list($featured,$list) = Event::getEventsByType('AA1',2);
        $this->assertEqual(count($list),2);
        $this->assertEqual($list->totalCount,3);
        $this->assertEqual($featured->id,$e2->id);
        $this->assertEqual($list[0]->id,$e1->id);
        $this->assertEqual($list[1]->id,$e3->id);
    }

    //
    public function testPrevNext () { # void
        Event::create(qw('title>T1+startDate>2007-12-15 00:00','+'));
        Event::create(qw('title>T1+startDate>2008-01-30 00:00+endDate>2008-02-04 01:00','+'));
        Event::create(qw('title>T1+startDate>2008-02-02 13:59','+'));
        Event::create(qw('title>T1+startDate>2008-02-25 23:59+endDate>2008-03-01 00:00','+'));
        Event::create(qw('title>T1+startDate>2008-02-27 05:00','+'));

        // Query simple+overlap test
            $cnt = Events_EventHelper::$count;
            // next
            $this->assertEqual(TestEvent::getPrevDate(array(),'2008-02-15'),'2008-02-04');
            $this->assertEqual(Events_EventHelper::$count,$cnt+1);
            $this->assertEqual(TestEvent::getPrevDate(array(),'2008-02-02'),'2008-02-01');
            $this->assertEqual(Events_EventHelper::$count,$cnt+2);

            // prev
            $this->assertEqual(TestEvent::getNextDate(array(),'2008-02-15'),'2008-02-25');
            $this->assertEqual(Events_EventHelper::$count,$cnt+3);
            $this->assertEqual(TestEvent::getNextDate(array(),'2008-02-25'),'2008-02-26');
            $this->assertEqual(Events_EventHelper::$count,$cnt+4);

        // Calendar test
            $calendar = EventCalendar::getCalendar('2008-01','2008-03');
            $cnt = Events_EventHelper::$count;
            // next
            $this->assertEqual(TestEvent::getPrevDate($calendar,'2008-02-15'),'2008-02-04');
            $this->assertEqual(Events_EventHelper::$count,$cnt);
            $this->assertEqual(TestEvent::getPrevDate($calendar,'2008-02-02'),'2008-02-01');
            $this->assertEqual(Events_EventHelper::$count,$cnt);

            // prev
            $this->assertEqual(TestEvent::getNextDate($calendar,'2008-02-15'),'2008-02-25');
            $this->assertEqual(Events_EventHelper::$count,$cnt);
            $this->assertEqual(TestEvent::getNextDate($calendar,'2008-02-25'),'2008-02-26');
            $this->assertEqual(Events_EventHelper::$count,$cnt);
    }

    //
    public function testDelete() { # void
        $GLOBALS['UNIT_TEST_SKIP_PROFILE_CHECK_IN_USER_LOAD'] = 1;
        $u1 = Events_EventHelper::create('User');
        Events_EventHelper::update(NULL, $u1,array('title'=>'U1','defaultVisibility'=>'all','addCommentPermission'=>'all'));

        $e		= Event::create(qw('title>title2 description>description2 eventType>AA1 startDate>2008-02-02'));
                  Event::create(qw('title>title2 description>description2 eventType>AA2 startDate>2008-02-03'));
        $eid	= $e->id;

        EventAttendee::setStatus('U1', $e, EventAttendee::ATTENDING);

        // widget
        $this->assertEqual(EventWidget::getEventTypes(), array('AA1'=>1,'AA2'=>1));
        // calendar
        $calendar = EventCalendar::getCalendar('2008-02','2008-02');
        $this->assertEqual(array_filter($calendar['2008-02']), array('2'=>1,'3'=>1));
        // attendees
        $this->assertEqual(XG_TestHelper::titles(EventAttendee::getAttendees($e, EventAttendee::ATTENDING,2)), array('U1'));
        $this->assertEqual(EventAttendee::getEventTypes('U1'), array('AA1'=>1));

        Event::delete($e);

        $this->assertFalse(XG_TestHelper::exists($eid));
        // widget
        $this->assertEqual(EventWidget::getEventTypes(),array('AA2'=>1));
        // calendar
        $calendar = EventCalendar::getCalendar('2008-02','2008-02');
        $this->assertEqual(array_filter($calendar['2008-02']),array('3'=>1));
        // attendees
        $this->assertEqual(XG_TestHelper::titles(EventAttendee::getAttendees($e, EventAttendee::ATTENDING,2)), array());
        $this->assertEqual(EventAttendee::getEventTypes('U1'), array());
    }


    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

class TestEvent extends Event {
    public static function getPrevDate($calendar, $date) {
        return parent::_getPrevDate($calendar, $date);
    }
    public static function getNextDate($calendar, $date) {
        return parent::_getNextDate($calendar, $date);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
