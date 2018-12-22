<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_AbstractFriendRequestUpdater.php');

/**
 * Tests Profiles_AbstractFriendRequestUpdater::run().
 */
class Profiles_AbstractFriendRequestUpdaterRunTest extends UnitTestCase {

    public function setUp() {
        $this->updater = new FriendRequestUpdaterPartialMock();
    }

    public function testRun1() {
        $this->updater->setReturnValue('isRunning', TRUE);
        try {
            $this->updater->run(array());
            $this->fail();
        } catch (Exception $e) {
            $this->assertEqual('Updater already running (1863285374)', $e->getMessage());
        }
    }

    public function testRun2() {
        $this->updater = new FriendRequestUpdaterPartialMock();
        $this->updater->setReturnValue('isRunning', FALSE);
        $this->updater->setReturnValue('getAllFriendRequests', array());
        $this->updater->expectNever('createJob');
        $this->updater->run(array());
    }

    public function testRun3() {
        $this->updater = new FriendRequestUpdaterPartialMock();
        $this->updater->setReturnValue('isRunning', FALSE);
        $this->updater->setReturnValue('getAllFriendRequests', array('a1', 'a2', 'a3', 'a4', 'a5'));
        $this->updater->setReturnValue('getStart', 123);
        $this->updater->expectOnce('runTask', array(array('foo' => 'bar', 'friendRequestBatches' => array(array('a1', 'a2', 'a3', 'a4', 'a5')), 'batchNumber' => 0, 'start' => 123, 'class' => 'FriendRequestUpdaterPartialMock')));
        $this->updater->expectOnce('finished');
        $this->updater->run(array('foo' => 'bar'));
    }

    public function testRun4() {
        $this->updater = new FriendRequestUpdaterPartialMock();
        $this->updater->setReturnValue('isRunning', FALSE);
        $this->updater->setReturnValue('getAllFriendRequests', array('a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24', 'a25', 'a26', 'a27', 'a28', 'a29', 'a30', 'a31', 'a32', 'a33', 'a34', 'a35', 'a36', 'a37', 'a38', 'a39', 'a40', 'a41', 'a42', 'a43', 'a44', 'a45', 'a46', 'a47', 'a48', 'a49', 'a50', 'a51', 'a52', 'a53', 'a54', 'a55', 'a56', 'a57', 'a58', 'a59', 'a60'));
        $this->updater->setReturnValue('getStart', 123);
        $this->updater->expectOnce('createJob', array(array(array(array('Profiles_AbstractFriendRequestUpdater', 'task_run'), array('foo' => 'bar', 'friendRequestBatches' => array(array('a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24', 'a25', 'a26', 'a27', 'a28', 'a29', 'a30', 'a31', 'a32', 'a33', 'a34', 'a35', 'a36', 'a37', 'a38', 'a39', 'a40', 'a41', 'a42', 'a43', 'a44', 'a45', 'a46', 'a47', 'a48', 'a49', 'a50'), array('a51', 'a52', 'a53', 'a54', 'a55', 'a56', 'a57', 'a58', 'a59', 'a60')), 'batchNumber' => 0, 'start' => 123, 'class' => 'FriendRequestUpdaterPartialMock')))));
        $this->updater->run(array('foo' => 'bar'));
    }

}

abstract class Profiles_AbstractFriendRequestUpdater2 extends Profiles_AbstractFriendRequestUpdater {
    public function run($extraArgs) {
        parent::run($extraArgs);
    }
}

Mock::generatePartial('Profiles_AbstractFriendRequestUpdater2', 'FriendRequestUpdaterPartialMock', array('isRunning', 'setStart', 'getAllFriendRequests', 'createJob', 'getStartAttributeName', 'getRelationship', 'update', 'finished', 'getStart', 'runTask'));

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
