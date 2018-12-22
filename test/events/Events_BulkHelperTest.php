<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_EventHelper.php');
W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_BulkHelper.php');
Mock::generate('XN_Query');
Mock::generate('Events_BulkHelper');
Mock::generate('Events_EventHelper');


class Events_BulkHelperTest extends UnitTestCase {

    public function setUp() {
        $this->helper = new MockEvents_BulkHelper();
        $this->eventHelper = new MockEvents_EventHelper();
        TestBulkHelper::init($this->helper, $this->eventHelper);
    }

    public function testRemoveEventAttendees1() {
        $this->doTestRemoveEventAttendees(5, 0);
    }

    public function testRemoveEventAttendees2() {
        $this->doTestRemoveEventAttendees(3, 1);
    }

    public function doTestRemoveEventAttendees($limit, $expectedRemaining) {
        $query = new MockXN_Query();
        $query->expectOnce('filter', array('my->screenName', '=', 'jane'));
        $query->setReturnValue('filter', $query);
        $query->expectOnce('end', array($limit));
        $query->setReturnValue('end', $query);
        $query->expectOnce('order', array('createdDate', 'asc', 'date'));
        $query->setReturnValue('order', $query);
        $query->expectOnce('execute', array());
        $query->setReturnValue('execute', array('[EventAttendee1]', '[EventAttendee2]', '[EventAttendee3]'));
        $this->eventHelper->expectOnce('query', array('EventAttendee'));
        $this->eventHelper->setReturnValue('query', $query);
        $this->helper->expectCallCount('_deleteEventAttendee', 3);
        $this->helper->expectAt(0, '_deleteEventAttendee', array('[EventAttendee1]'));
        $this->helper->expectAt(1, '_deleteEventAttendee', array('[EventAttendee2]'));
        $this->helper->expectAt(2, '_deleteEventAttendee', array('[EventAttendee3]'));
        $this->assertEqual(array('changed' => 3, 'remaining' => $expectedRemaining), Events_BulkHelper::removeEventAttendees($limit, 'jane'));
    }

    public function testRemoveEvents1() {
        $this->doTestRemoveEvents(5, 0);
    }

    public function testRemoveEvents2() {
        $this->doTestRemoveEvents(3, 1);
    }

    public function doTestRemoveEvents($limit, $expectedRemaining) {
        $query = new MockXN_Query();
        $query->expectOnce('filter', array('contributorName', '=', 'jane'));
        $query->setReturnValue('filter', $query);
        $query->expectOnce('end', array($limit));
        $query->setReturnValue('end', $query);
        $query->expectOnce('order', array('createdDate', 'asc', 'date'));
        $query->setReturnValue('order', $query);
        $query->expectOnce('execute', array());
        $query->setReturnValue('execute', array('[Event1]', '[Event2]', '[Event3]'));
        $this->eventHelper->expectOnce('query', array('Event'));
        $this->eventHelper->setReturnValue('query', $query);
        $this->helper->expectCallCount('_deleteEvent', 3);
        $this->helper->expectAt(0, '_deleteEvent', array('[Event1]'));
        $this->helper->expectAt(1, '_deleteEvent', array('[Event2]'));
        $this->helper->expectAt(2, '_deleteEvent', array('[Event3]'));
        $this->assertEqual(array('changed' => 3, 'remaining' => $expectedRemaining), Events_BulkHelper::removeEvents($limit, 'jane'));
    }

}

class TestBulkHelper extends Events_BulkHelper {
    public static function init($instance, $eventHelper) {
        parent::$instance = $instance;
        parent::$eventHelper = $eventHelper;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
