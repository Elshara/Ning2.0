<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_AbstractFriendRequestUpdater.php');
Mock::generate('XN_Query');

/**
 * Tests Profiles_AbstractFriendRequestUpdater::runTask().
 */
class Profiles_AbstractFriendRequestUpdaterRunTaskTest extends UnitTestCase {

    public function setUp() {
        $this->updater = new FriendRequestUpdaterPartialMock();
    }

    public function testRunTask1() {
        $this->updater->expectNever('createContactQuery');
        $this->updater->runTask(array('start' => '456'));
    }

    public function testRunTask2() {
        $this->updater->setReturnValue('getStart', 123);
        $this->updater->expectOnce('update', array(array('d', 'e', 'f'), array('friendRequestBatches' => array(array('a', 'b', 'c'), array('d', 'e', 'f'), array('g', 'h')), 'batchNumber' => '1', 'start' => '123')));
        $this->updater->expectNever('finished');
        $this->updater->expectOnce('createJob', array(array(array(array('Profiles_AbstractFriendRequestUpdater', 'task_run'), array('friendRequestBatches' => array(array('a', 'b', 'c'), array('d', 'e', 'f'), array('g', 'h')), 'batchNumber' => 2, 'start' => '123')))));
        $this->updater->runTask(array('friendRequestBatches' => array(array('a', 'b', 'c'), array('d', 'e', 'f'), array('g', 'h')), 'batchNumber' => '1', 'start' => '123'));
    }

    public function testRunTask3() {
        $this->updater->setReturnValue('getStart', 123);
        $this->updater->expectOnce('update', array(array('g', 'h'), array('friendRequestBatches' => array(array('a', 'b', 'c'), array('d', 'e', 'f'), array('g', 'h')), 'batchNumber' => '2', 'start' => '123')));
        $this->updater->expectOnce('finished');
        $this->updater->expectNever('createJob');
        $this->updater->runTask(array('friendRequestBatches' => array(array('a', 'b', 'c'), array('d', 'e', 'f'), array('g', 'h')), 'batchNumber' => '2', 'start' => '123'));
    }

}

abstract class TestAbstractFriendRequestUpdater extends Profiles_AbstractFriendRequestUpdater {
    public function runTask($args) {
        parent::runTask($args);
    }
}

Mock::generatePartial('TestAbstractFriendRequestUpdater', 'FriendRequestUpdaterPartialMock', array('isRunning', 'setStart', 'getFriendRequestCount', 'initialFriendRequestIndex', 'createJob', 'getStartAttributeName', 'getRelationship', 'update', 'finished', 'createContactQuery', 'getStart'));

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
