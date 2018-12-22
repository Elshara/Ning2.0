<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_CachedCountHelper.php');

class Profiles_CachedCountHelperTest extends UnitTestCase {

    public function setUp() {
        $this->helper = new TestCachedCountHelper();
        $this->current = XN_Profile::current()->screenName;
        $this->json = new NF_JSON();
    }

    public function testCacheId() {
        $this->assertEqual('foo-NingDev2', $this->helper->cacheId('foo', 'NingDev2'));
    }

    public function testPut() {
        XN_Cache::remove('foo-NingDev');
        $this->assertNull($this->helper->getProper('foo', 'NingDev'));
        $this->helper->put('foo', 'NingDev', '5');
        $this->assertEqual(5, $this->helper->getProper('foo', 'NingDev'));
    }

    private function doTestUpdateCountsProper($newRelationship, $oldRelationship, $expected, $maxOtherUsersToUpdate = 2) {
        $helper = new TestCachedCountHelperPartialMock();
        $helper->setReturnValue('getProper', 1, array('sentFriendRequestCount', $this->current));
        $helper->setReturnValue('getProper', 2, array('receivedFriendRequestCount', $this->current));
        $helper->setReturnValue('getProper', 3, array('numberOfFriendsOnNetwork', $this->current));
        $helper->setReturnValue('getProper', 4, array('numberOfFriendsAcrossNing', $this->current));
        $helper->setReturnValue('getProper', 5, array('sentFriendRequestCount', 'a2'));
        $helper->setReturnValue('getProper', 6, array('receivedFriendRequestCount', 'a2'));
        $helper->setReturnValue('getProper', 7, array('numberOfFriendsOnNetwork', 'a2'));
        $helper->setReturnValue('getProper', 8, array('numberOfFriendsAcrossNing', 'a2'));
        $expectedGetProperCallCount = 0;
        foreach ($expected as $putArgs) {
            if (mb_strpos($putArgs[0], '-a2')) { $expectedGetProperCallCount += 2; } // a1 and a2 [Jon Aquino 2008-09-13]
            else { $expectedGetProperCallCount += 1; } // current user [Jon Aquino 2008-09-13]
        }
        $helper->expectCallCount('getProper', $expectedGetProperCallCount);
        $actual = $helper->updateCountsProper(array('a1', 'a2'), $newRelationship, $oldRelationship, $maxOtherUsersToUpdate);
        $this->assertEqual($this->json->encode($expected), $this->json->encode($actual));
        if ($this->json->encode($expected) !== $this->json->encode($actual)) {
            var_dump($expected);
            var_dump($actual);
        }
    }

    public function testUpdateCountsProper1() {
        // Sending friend requests [Jon Aquino 2008-09-13]
        $this->doTestUpdateCountsProper(XN_Profile::FRIEND, XN_Profile::NOT_FRIEND, array(
            array('sentFriendRequestCount-' . $this->current, array('sentFriendRequestCount'), 1800, 3),
            array('receivedFriendRequestCount-a2', array('receivedFriendRequestCount'), 1800, 7),
        ));
    }

    public function testUpdateCountsProper2() {
        // Accepting received friend requests [Jon Aquino 2008-09-13]
        $this->doTestUpdateCountsProper(XN_Profile::FRIEND, XN_Profile::GROUPIE, array(
            array('receivedFriendRequestCount-' . $this->current, array('receivedFriendRequestCount'), 1800, 0),
            array('numberOfFriendsOnNetwork-' . $this->current, array('numberOfFriendsOnNetwork'), 1800, 5),
            array('numberOfFriendsAcrossNing-' . $this->current, array('numberOfFriendsAcrossNing'), 1800, 6),
            array('sentFriendRequestCount-a2', array('sentFriendRequestCount'), 1800, 4),
            array('numberOfFriendsOnNetwork-a2', array('numberOfFriendsOnNetwork'), 1800, 8),
            array('numberOfFriendsAcrossNing-a2', array('numberOfFriendsAcrossNing'), 1800, 9),
        ));
    }

    public function testUpdateCountsProper3() {
        // Defriending [Jon Aquino 2008-09-13]
        $this->doTestUpdateCountsProper(XN_Profile::NOT_FRIEND, XN_Profile::FRIEND, array(
            array('numberOfFriendsOnNetwork-' . $this->current, array('numberOfFriendsOnNetwork'), 1800, 1),
            array('numberOfFriendsAcrossNing-' . $this->current, array('numberOfFriendsAcrossNing'), 1800, 2),
            array('numberOfFriendsOnNetwork-a2', array('numberOfFriendsOnNetwork'), 1800, 6),
            array('numberOfFriendsAcrossNing-a2', array('numberOfFriendsAcrossNing'), 1800, 7),
        ));
    }

    public function testUpdateCountsProper4() {
        // Withdrawing sent friend requests [Jon Aquino 2008-09-13]
        $this->doTestUpdateCountsProper(XN_Profile::NOT_FRIEND, XN_Profile::FRIEND_PENDING, array(
            array('sentFriendRequestCount-' . $this->current, array('sentFriendRequestCount'), 1800, 0),
            array('receivedFriendRequestCount-a2', array('receivedFriendRequestCount'), 1800, 5),
        ));
    }

    public function testUpdateCountsProper5() {
        // Ignoring received friend requests [Jon Aquino 2008-09-13]
        $this->doTestUpdateCountsProper(XN_Profile::NOT_FRIEND, XN_Profile::GROUPIE, array(
            array('receivedFriendRequestCount-' . $this->current, array('receivedFriendRequestCount'), 1800, 0),
            array('sentFriendRequestCount-a2', array('sentFriendRequestCount'), 1800, 4),
        ));
    }

    public function testUpdateCountsProper6() {
        // Ignoring received friend requests; $maxOtherUsersToUpdate exceeded [Jon Aquino 2008-09-13]
        $this->doTestUpdateCountsProper(XN_Profile::NOT_FRIEND, XN_Profile::GROUPIE, array(
            array('receivedFriendRequestCount-' . $this->current, array('receivedFriendRequestCount'), 1800, 0),
        ), 1);
    }

    public function testGet1() {
        $this->assertIdentical(FALSE, $this->helper->buildingCache);
    }

    public function testGet2() {
        try {
            $this->helper->foo;
            $this->fail();
        } catch (Exception $e) {
            $this->pass();
        }
    }

    public function testSet() {
        try {
            $this->helper->buildingCache = FALSE;
            $this->fail();
        } catch (Exception $e) {
            $this->pass();
        }
    }

}

class TestCachedCountHelper extends Profiles_CachedCountHelper {
    public function cacheId($countName, $screenName) {
        return parent::cacheId($countName, $screenName);
    }
    public function updateCountsProper($screenNames, $newRelationship, $oldRelationship, $maxOtherUsersToUpdate) {
        return parent::updateCountsProper($screenNames, $newRelationship, $oldRelationship, $maxOtherUsersToUpdate);
    }
    public function getProper($countName, $screenName) {
        return parent::getProper($countName, $screenName);
    }
}

Mock::generatePartial('TestCachedCountHelper', 'TestCachedCountHelperPartialMock', array('getProper'));

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
