<?php	# $Id: $
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class EventWidgetTest extends UnitTestCase {
    public function setUp() {
        XG_TestHelper::setCurrentWidget('events');
        XG_TestHelper::deleteTestObjects();
        EventWidget::init();
    }

    //
    public function testEventTypes () { # void
		$e1 = Event::create(qw('title>T1+eventType>AA1+startDate>'.Events_EventHelper::dateToStr(),'+'));
		$e2 = Event::create(qw('title>T1+eventType>AA2+startDate>'.Events_EventHelper::dateToStr(),'+'));
		$e3 = Event::create(qw('title>T1+startDate>'.Events_EventHelper::dateToStr(),'+'));
		$e4 = Event::create(qw('title>T1+startDate>'.Events_EventHelper::dateToStr(),'+'));
		$e5 = Event::create(qw('title>T1+eventType>AA3+startDate>'.Events_EventHelper::dateToStr(),'+'));

		Event::update($e4, qw('eventType>AA2'));
		$types = EventWidget::getEventTypes();
		$this->assertEqual(count($types),3);
		$this->assertEqual($types,array('AA1'=>1,'AA2'=>2,'AA3'=>1));

		Event::update($e5, qw('eventType>'));
		$types = EventWidget::getEventTypes();
		$this->assertEqual(count($types),2);
		$this->assertEqual($types,array('AA1'=>1,'AA2'=>2));
	}

    //
	public function testMinMaxDates () { # void
		$e3 = Event::create(qw('title>T1+startDate>2008-01-02','+'));
		$e4 = Event::create(qw('title>T1+startDate>2008-02-02','+'));
		$this->assertEqual(EventWidget::getMinMaxEventDates(),array('2008-01-02 00:00','2008-02-02 00:00'));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
