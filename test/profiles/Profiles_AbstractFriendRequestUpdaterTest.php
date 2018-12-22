<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_AbstractFriendRequestUpdater.php');

class Profiles_AbstractFriendRequestUpdaterTest extends UnitTestCase {

    public function setUp() {
        $this->updater = new TestAbstractFriendRequestUpdater();
        $this->user = TestContent::create('TestUser');
        $this->user->my = new XN_AttributeContainer($this->user);
        TestAbstractFriendRequestUpdater::setUser($this->user);
    }

    public function testSetStart() {
        $this->updater->setStart(12345);
        $this->assertEqual(12345, $this->user->my->sentFriendRequestUpdateStart);
    }

}

class TestAbstractFriendRequestUpdater extends Profiles_AbstractFriendRequestUpdater {

    public function __construct() {
    }

    public function setStart($start) {
        parent::setStart($start);
    }

    public static function setUser($user) {
        self::$user = $user;
    }

    protected function getStartAttributeName() {
        return 'sentFriendRequestUpdateStart';
    }

    protected function getRelationship() {
        return XN_Profile::FRIEND_PENDING;
    }

    protected function update($contacts) {
    }

    protected function finished() {
    }

}

class TestContent extends XN_Content {
    public function save() {}
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
