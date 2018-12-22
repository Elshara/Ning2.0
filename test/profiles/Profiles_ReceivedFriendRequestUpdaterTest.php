<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_ReceivedFriendRequestUpdater.php');

class Profiles_ReceivedFriendRequestUpdaterTest extends UnitTestCase {

    public function setUp() {
        TestFriendHelper::setInstance(NULL);
    }

    public function testUpdate() {
        Mock::generate('Profiles_FriendHelper');
        $friendHelper = new MockProfiles_FriendHelper();
        $friendHelper->expectOnce('acceptFriendRequests', array(array('a', 'b', 'c'), TRUE, 5));
        TestFriendHelper::setInstance($friendHelper);
        $updater = new TestReceivedFriendRequestUpdater();
        $updater->update(array('a', 'b', 'c'), array('action' => 'acceptAll', 'batchNumber' => '0', 'friendRequestBatches' => array(array('a', 'b', 'c'), array('d', 'e'))));
    }

}

class TestFriendHelper extends Profiles_FriendHelper {
    public static function setInstance($instance) {
        parent::$instance = $instance;
    }
}

class TestReceivedFriendRequestUpdater extends Profiles_ReceivedFriendRequestUpdater {
    public function __construct() {
    }
    public function update($screenNames, $args) {
        parent::update($screenNames, $args);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
