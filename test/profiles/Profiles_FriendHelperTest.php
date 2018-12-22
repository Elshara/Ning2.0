<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_FriendHelper.php');
Mock::generate('TestRest');

class Profiles_FriendHelperTest extends UnitTestCase {

    public function setUp() {
        TestRest::setInstance(null);
        $this->friendRequestCountsId = 'friend-request-counts-' . XN_Profile::current()->screenName;
        $this->friendHelper = new TestFriendHelper();
        XN_Cache::remove($this->friendRequestCountsId);
    }

    public function testSetContactStatus1() {
        Mock::generatePartial('TestFriendHelper', 'FriendHelperPartialMock1', array('setContactStatusProper'));
        $friendHelper = new FriendHelperPartialMock1();
        $friendHelper->expectOnce('setContactStatusProper', array(array('aaaaa1'), 'groupie'));
        $friendHelper->setContactStatus('aaaaa1', 'groupie');
    }

    public function testSetContactStatus2() {
        Mock::generatePartial('TestFriendHelper', 'FriendHelperPartialMock2', array('setContactStatusProper'));
        $friendHelper = new FriendHelperPartialMock2();
        $friendHelper->expectOnce('setContactStatusProper', array(array('aaaaa1', 'aaaaa2'), 'groupie'));
        $friendHelper->setContactStatus(array('aaaaa1', 'aaaaa2', 'aaaaa1'), 'groupie');
    }

    public function testSetContactStatus3() {
        Mock::generatePartial('TestFriendHelper', 'FriendHelperPartialMock3', array('setContactStatusProper'));
        $friendHelper = new FriendHelperPartialMock3();
        $friendHelper->setContactStatus(array('a', 'b', 'c'), 'groupie');
    }

    public function testSetContactStatus4() {
        Mock::generatePartial('TestFriendHelper', 'FriendHelperPartialMock4', array('setContactStatusProper'));
        $friendHelper = new FriendHelperPartialMock4();
        $friendHelper->setContactStatus(array('a', 'b', 'c', 'd'), 'groupie');
    }

}

class TestFriendHelper extends Profiles_FriendHelper {
    public function setContactStatusProper($screenName, $relationship) {
        return parent::setContactStatusProper($screenName, $relationship);
    }
    public function setContactStatus($screenName, $relationship) {
        return parent::setContactStatus($screenName, $relationship, $relationship);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
