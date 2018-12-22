<?php	# $Id: $
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class EventAttendeeSyncTest extends UnitTestCase {
	public function setUp() {
		XG_TestHelper::setCurrentWidget('events');
		XG_TestHelper::deleteTestObjects();
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

	public function testCreateForegroundSync() { # void
		EventAttendee::$fgCount = 10; // force updates in foreground
    	// create event
		$e[0] = Event::create(qw('title>T1+eventType>T1,T2+startDate>2008-02-01 01:00+endDate>2008-02-06 05:00','+'));
		$e[1] = Event::create(qw('title>T1+eventType>T2+startDate>2008-01-31 23:00','+'));
		$e[2] = Event::create(qw('title>T1+eventType>T1+startDate>2008-02-05 11:00','+'));
		$e0Id = $e[0]->id;

		// add attendees
		EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
		EventAttendee::setStatus('U1', $e[1], EventAttendee::NOT_ATTENDING);
		EventAttendee::setStatus('U1', $e[2], EventAttendee::MIGHT_ATTEND);
		EventAttendee::setStatus('U2', $e[2], EventAttendee::ATTENDING);

		// check users
		$this->assertEqual(EventAttendee::getEventTypes('U1'), array('T1'=>2,'T2'=>1));
		$info = EventAttendee::getCalendar('U1', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),qw(''));
		$this->assertEqual(array_filter($info['2008-02']),qw('1>1 2>1 3>1 4>1 5>2 6>1'));

		$this->assertEqual(EventAttendee::getEventTypes('U2'), array('T1'=>1));
		$info = EventAttendee::getCalendar('U2', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),array());
		$this->assertEqual(array_filter($info['2008-02']),qw('5>1'));

		// check EventAttendee
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T1'))
			->execute()), 2);
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T2'))
			->execute()), 2);
	}

	public function testUpdateForegroundSync() { # void
		EventAttendee::$fgCount = 10; // force updates in foreground
    	// create event
		$e[0] = Event::create(qw('title>T1+eventType>T1+startDate>2008-02-01 01:00+endDate>2008-02-06 05:00','+'));
		$e[1] = Event::create(qw('title>T1+eventType>T2+startDate>2008-01-31 23:00','+'));
		$e[2] = Event::create(qw('title>T1+eventType>T1,T2+startDate>2008-02-05 11:00','+'));
		$e0Id = $e[0]->id;

		// add attendees
		EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
		EventAttendee::setStatus('U1', $e[1], EventAttendee::NOT_ATTENDING);
		EventAttendee::setStatus('U1', $e[2], EventAttendee::MIGHT_ATTEND);
		EventAttendee::setStatus('U2', $e[2], EventAttendee::ATTENDING);

		// update event
		Event::update($e[0], qw('eventType>T2+startDate>2008-01-04 01:00+endDate>2008-01-06 01:00','+'));

		// check user
		$this->assertEqual(EventAttendee::getEventTypes('U1'), array('T1'=>1,'T2'=>2));
		$info = EventAttendee::getCalendar('U1', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),qw('4>1 5>1 6>1'));
		$this->assertEqual(array_filter($info['2008-02']),qw('5>1'));

		//check EventAttendee
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T1'))
			->execute()), 1);
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T2'))
			->execute()), 3);
	}

    public function testDeleteForegroundSync() { # void
		EventAttendee::$fgCount = 10; // force updates in foreground
    	// create event
		$e[0] = Event::create(qw('title>T1+eventType>T1,T2+startDate>2008-02-01 01:00+endDate>2008-02-06 05:00','+'));
		$e[1] = Event::create(qw('title>T1+eventType>T2+startDate>2008-01-31 23:00','+'));
		$e[2] = Event::create(qw('title>T1+eventType>T1+startDate>2008-02-05 11:00','+'));
		$e0Id = $e[0]->id;

		// add attendees
		EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
		EventAttendee::setStatus('U1', $e[1], EventAttendee::NOT_ATTENDING);
		EventAttendee::setStatus('U1', $e[2], EventAttendee::MIGHT_ATTEND);
		EventAttendee::setStatus('U2', $e[2], EventAttendee::ATTENDING);

		// delete event
		Event::delete($e[0]);

		// check user
		$this->assertEqual(EventAttendee::getEventTypes('U1'), array('T1'=>1));
		$info = EventAttendee::getCalendar('U1', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),qw(''));
		$this->assertEqual(array_filter($info['2008-02']),qw('5>1'));

		//check EventAttendee
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T1'))
			->execute()), 1);
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T2'))
			->execute()), 1);
    }

	public function testUpdateBackgroundAsync() { # void
		EventAttendee::$fgCount = 1; // force updates in background
    	// create event
		$e[0] = Event::create(qw('title>T1+eventType>T1+startDate>2008-02-01 01:00+endDate>2008-02-06 05:00','+'));
		$e[1] = Event::create(qw('title>T1+eventType>T2+startDate>2008-01-31 23:00','+'));
		$e[2] = Event::create(qw('title>T1+eventType>T1,T2+startDate>2008-02-05 11:00','+'));
		$e0Id = $e[0]->id;

		// add attendees
		EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
		EventAttendee::setStatus('U1', $e[1], EventAttendee::NOT_ATTENDING);
		EventAttendee::setStatus('U1', $e[2], EventAttendee::MIGHT_ATTEND);
		EventAttendee::setStatus('U2', $e[0], EventAttendee::ATTENDING);

		// update event
		Event::update($e[0], qw('eventType>T2+startDate>2008-01-04 01:00+endDate>2008-01-06 01:00','+'));
		EventAttendee::setStatus('U2', $e[0], EventAttendee::NOT_ATTENDING);

		$this->assertEqual(EventAttendee::getEventTypes('U2'), array());
		$info = EventAttendee::getCalendar('U2', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),qw(''));
		$this->assertEqual(array_filter($info['2008-02']),qw(''));

		// check users
		$this->assertEqual(EventAttendee::getEventTypes('U1'), array('T1'=>2,'T2'=>1));
		$info = EventAttendee::getCalendar('U1', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),qw(''));
		$this->assertEqual(array_filter($info['2008-02']),qw('1>1 2>1 3>1 4>1 5>2 6>1'));

		// check EventAttendee
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')->filter('my->eventId','=',$e0Id)->execute()), 2);
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T1'))
			->execute()), 2);
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T2'))
			->execute()), 2);

		XG_JobHelper::_dispatchAll();

		// check user 2 (skipNotAttending test)
		$this->assertEqual(EventAttendee::getEventTypes('U2'), array());
		$info = EventAttendee::getCalendar('U2', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),qw(''));
		$this->assertEqual(array_filter($info['2008-02']),qw(''));

		// check user
		$this->assertEqual(EventAttendee::getEventTypes('U1'), array('T1'=>1,'T2'=>2));
		$info = EventAttendee::getCalendar('U1', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),qw('4>1 5>1 6>1'));
		$this->assertEqual(array_filter($info['2008-02']),qw('5>1'));

		//check EventAttendee
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T1'))
			->execute()), 1);
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T2'))
			->execute()), 3);
	}

	public function testDeleteBackgroundAsync() { # void
		EventAttendee::$fgCount = 1; // force updates in background
    	// create event
		$e[0] = Event::create(qw('title>T1+eventType>T1,T2+startDate>2008-02-01 01:00+endDate>2008-02-06 05:00','+'));
		$e[1] = Event::create(qw('title>T1+eventType>T2+startDate>2008-01-31 23:00','+'));
		$e[2] = Event::create(qw('title>T1+eventType>T1+startDate>2008-02-05 11:00','+'));
		$e0Id = $e[0]->id;

		// add attendees
		EventAttendee::setStatus('U1', $e[0], EventAttendee::ATTENDING);
		EventAttendee::setStatus('U1', $e[1], EventAttendee::NOT_ATTENDING);
		EventAttendee::setStatus('U1', $e[2], EventAttendee::MIGHT_ATTEND);
		EventAttendee::setStatus('U2', $e[0], EventAttendee::ATTENDING);

		// delete event
		Event::delete($e[0]);

		// check users
		$this->assertEqual(EventAttendee::getEventTypes('U1'), array('T1'=>2,'T2'=>1));
		$info = EventAttendee::getCalendar('U1', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),qw(''));
		$this->assertEqual(array_filter($info['2008-02']),qw('1>1 2>1 3>1 4>1 5>2 6>1'));

		// check EventAttendee
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')->filter('my->eventId','=',$e0Id)->execute()), 2);
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T1'))
			->execute()), 2);
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T2'))
			->execute()), 2);

		XG_JobHelper::_dispatchAll();

		// check user
		$this->assertEqual(EventAttendee::getEventTypes('U1'), array('T1'=>1));
		$info = EventAttendee::getCalendar('U1', '2008-01','2008-02');
		$this->assertEqual(count($info),2);
		$this->assertEqual(count($info['2008-01']),31);
		$this->assertEqual(count($info['2008-02']),29);
		$this->assertEqual(array_filter($info['2008-01']),qw(''));
		$this->assertEqual(array_filter($info['2008-02']),qw('5>1'));

		//check EventAttendee
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T1'))
			->execute()), 1);
		$this->assertEqual(count(Events_EventHelper::query('EventAttendee')
			->filter('my->screenName','=','U1')
			->filter('my->eventType','like', Events_EventHelper::typeFilter('T2'))
			->execute()), 1);
    }

	public function tearDown() {
		XG_TestHelper::deleteTestObjects();
	}
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
