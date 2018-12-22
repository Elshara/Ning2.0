<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_SentFriendRequestUpdater.php');
Mock::generate('TestRest');

class Profiles_SentFriendRequestUpdaterTest extends UnitTestCase {

    const MINUTE = 60;

    public function setUp() {
        $this->user = XN_Content::create('TestUser');
        TestSentFriendRequestUpdater::setUser($this->user);
    }

    public function testGet() {
        $this->assertTrue(Profiles_SentFriendRequestUpdater::instance() === Profiles_SentFriendRequestUpdater::instance());
    }

    public function testGetStart1() {
        $this->user->my->sentFriendRequestUpdateStart = NULL;
        $this->assertNull(TestSentFriendRequestUpdater::callGetStart(Profiles_SentFriendRequestUpdater::instance()));
    }

    public function testGetStart2() {
        $start = time() - 59 * self::MINUTE;
        $this->user->my->sentFriendRequestUpdateStart = $start;
        $this->assertEqual($start, TestSentFriendRequestUpdater::callGetStart(Profiles_SentFriendRequestUpdater::instance()));
    }

    public function testGetStart3() {
        $start = time() - 61 * self::MINUTE;
        $this->user->my->sentFriendRequestUpdateStart = $start;
        $this->assertNull(TestSentFriendRequestUpdater::callGetStart(Profiles_SentFriendRequestUpdater::instance()));
    }

}

class TestSentFriendRequestUpdater extends Profiles_SentFriendRequestUpdater {

    public static function setUser($user) {
        self::$user = $user;
    }

    public static function callGetStart($updater) {
        return $updater->getStart();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
