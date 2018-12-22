<?php	# $Id: $
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class EventCalendarTest extends UnitTestCase {
    public function setUp() {
        XG_TestHelper::setCurrentWidget('events');
        XG_TestHelper::deleteTestObjects();
        EventWidget::init();
    }

    //
    public function testCalendar() { # void
		$e1 = Event::create(qw('title>T1+eventType>+startDate>2008-03-01 00:00','+'));
		$e2 = Event::create(qw('title>T1+eventType>AA2+startDate>2008-01-01 00:00','+'));	//+
		$e3 = Event::create(qw('title>T1+eventType>AA1+startDate>2008-02-29 00:00','+'));	//+
		$e4 = Event::create(qw('title>T1+eventType>AA1+startDate>2008-02-15 15:00','+'));	//+
			  Event::create(qw('title>T1+eventType>+startDate>2008-02-15 16:00','+'));		//+
		$e5 = Event::create(qw('title>T1+eventType>AA3+startDate>2007-12-31 21:59','+'));

		$info = EventCalendar::getCalendar('2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);

		$this->assertEqual(array_filter($info['2008-01']),qw('1>1'));
		$this->assertEqual(array_filter($info['2008-02']),qw('15>2 29>1'));
/*
		$info = EventCalendar::getCalendar('2008-01','2008-02','AA1');
		$this->assertEqual(array_filter($info['2008-01']),array());
		$this->assertEqual(array_filter($info['2008-02']),qw('15>1 29>1'));
*/
		# Update dates
		Event::update($e4, qw('eventType>'));
		Event::update($e1, qw('startDate>2008-02-02 00:00+endDate>+eventType>AA1','+'));

		$info = EventCalendar::getCalendar('2008-01','2008-02');
		$this->assertEqual(array_filter($info['2008-01']),qw('1>1'));
		$this->assertEqual(array_filter($info['2008-02']),qw('2>1 15>2 29>1'));
/*
		$info = EventCalendar::getCalendar('2008-01','2008-02','AA1');
		$this->assertEqual(array_filter($info['2008-01']),array());
		$this->assertEqual(array_filter($info['2008-02']),qw('2>1 29>1'));
*/
    }

    //
	public function testMultiday() { # void
		$e1 = Event::create(qw('title>T1+eventType>AA1,AA2+startDate>2008-01-28 15:30+endDate>2008-02-02 04:00','+'));
		$e2 = Event::create(qw('title>T1+eventType>AA2+startDate>2008-01-01 00:00','+'));	//+

		# All types
		$info = EventCalendar::getCalendar('2008-01','2008-02');
		$this->assertEqual(array_filter($info['2008-01']),qw('1>1 28>1 29>1 30>1 31>1'));
		$this->assertEqual(array_filter($info['2008-02']),qw('1>1 2>1'));
/*
		# type AA1
		$info = EventCalendar::getCalendar('2008-01','2008-02','AA1');
		$this->assertEqual(array_filter($info['2008-01']),qw('28>1 29>1 30>1 31>1'));
		$this->assertEqual(array_filter($info['2008-02']),qw('1>1 2>1'));

		# type AA2
		$info = EventCalendar::getCalendar('2008-01','2008-02','AA2');
		$this->assertEqual(array_filter($info['2008-01']),qw('1>1 28>1 29>1 30>1 31>1'));
		$this->assertEqual(array_filter($info['2008-02']),qw('1>1 2>1'));
*/
		# UPDATE
		Event::update($e1,qw('title>T1+eventType>BB1,BB2+startDate>2007-01-28 15:30+endDate>2007-02-02 04:00','+'));
		Event::update($e2,qw('title>T1+eventType>BB2+startDate>2007-01-01 00:00+endDate>2007-01-01','+'));	//+

		# All types
		$info = EventCalendar::getCalendar('2008-01','2008-02');
		$this->assertEqual(array_filter($info['2008-01']),array());
		$this->assertEqual(array_filter($info['2008-02']),array());
/*
		# type AA1
		$info = EventCalendar::getCalendar('2008-01','2008-02','AA1');
		$this->assertEqual(array_filter($info['2008-01']),array());
		$this->assertEqual(array_filter($info['2008-02']),array());

		# type AA2
		$info = EventCalendar::getCalendar('2008-01','2008-02','AA2');
		$this->assertEqual(array_filter($info['2008-01']),array());
		$this->assertEqual(array_filter($info['2008-02']),array());
*/
		# All types (fixed dates)
		$info = EventCalendar::getCalendar('2007-01','2007-02');
		$this->assertEqual(array_filter($info['2007-01']),qw('1>1 28>1 29>1 30>1 31>1'));
		$this->assertEqual(array_filter($info['2007-02']),qw('1>1 2>1'));
/*
		# type BB1
		$info = EventCalendar::getCalendar('2007-01','2007-02','BB1');
		$this->assertEqual(array_filter($info['2007-01']),qw('28>1 29>1 30>1 31>1'));
		$this->assertEqual(array_filter($info['2007-02']),qw('1>1 2>1'));

		# type BB2
		$info = EventCalendar::getCalendar('2007-01','2007-02','BB2');
		$this->assertEqual(array_filter($info['2007-01']),qw('1>1 28>1 29>1 30>1 31>1'));
		$this->assertEqual(array_filter($info['2007-02']),qw('1>1 2>1'));
*/
	}

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
