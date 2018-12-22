<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_NetworkSpecificFriendRequestHelper.php');

class Profiles_NetworkSpecificFriendRequestHelperTest extends UnitTestCase {

    public function setUp() {
        $this->helper = new TestNetworkSpecificFriendRequestHelper();
        $this->friendRequestsCacheId = $this->helper->friendRequestsCacheId(XN_Profile::current()->screenName, XN_Profile::FRIEND_PENDING);
        XN_Cache::remove($this->friendRequestsCacheId);
    }

    public function tearDown() {
        W_Cache::getWidget('admin')->config['Profiles_NetworkSpecificFriendRequestHelper_FRIEND_REQUEST_LIMIT'] = NULL;
        W_Cache::getWidget('admin')->config['XG_QueryHelper_EXECUTE_AS_NEEDED_CHUNK_SIZE'] = NULL;
        W_Cache::getWidget('admin')->saveConfig();
    }

    public function testgetFriendRequestsProper() {
        W_Cache::getWidget('admin')->config['Profiles_NetworkSpecificFriendRequestHelper_FRIEND_REQUEST_LIMIT'] = 6;
        W_Cache::getWidget('admin')->config['XG_QueryHelper_EXECUTE_AS_NEEDED_CHUNK_SIZE'] = 3;
        W_Cache::getWidget('admin')->saveConfig();
        $query = new MockXN_Query();
        $query->expectCallCount('filter', 2);
        $query->expectAt(0, 'filter', array('owner', '=', XN_Profile::current()->screenName));
        $query->expectAt(1, 'filter', array('relationship', '=', 'pending'));
        $query->expectCallCount('begin', 2);
        $query->expectCallCount('end', 2);
        $query->expectAt(0, 'begin', array(0));
        $query->expectAt(0, 'end', array(3));
        $query->expectAt(1, 'begin', array(3));
        $query->expectAt(1, 'end', array(6));
        $query->expectCallCount('getResultTo', 3);
        $query->setReturnValueAt(0, 'getResultTo', 3);
        $query->setReturnValueAt(1, 'getResultTo', 3);
        $query->setReturnValueAt(2, 'getResultTo', 6);
        $query->expectOnce('getTotalCount');
        $query->setReturnValue('getTotalCount', 6);
        $query->expect('order', array('updatedDate', 'desc'));
        $query->expectCallCount('execute', 2);
        $query->setReturnValueAt(0, 'execute', array($this->contact('A'), $this->contact('B'), $this->contact('C')));
        $query->setReturnValueAt(1, 'execute', array($this->contact('D'), $this->contact('E'), $this->contact('F')));
        $helper = new NetworkSpecificFriendRequestHelperPartialMock();
        $helper->expectOnce('createContactQuery');
        $helper->setReturnValue('createContactQuery', $query);
        $helper->expectOnce('loadUsers', array(array('A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F')));
        $helper->setReturnValue('loadUsers', array('B' => $this->user(NULL), 'C' => $this->user('blocked'), 'D' => $this->user('pending'), 'F' => $this->user(NULL)));
        $friendRequests = $helper->getFriendRequestsProperProper(XN_Profile::current()->screenName, 'pending');
        $this->assertEqual(2, count($friendRequests));
        $this->assertEqual('B', $friendRequests[0]['screenName']);
        $this->assertEqual('F', $friendRequests[1]['screenName']);
    }

    private function contact($screenName) {
        return new TestContact($screenName);
    }

    private function user($xg_index_status) {
        $user = XN_Content::create('User');
        $user->my->xg_index_status = $xg_index_status;
        return $user;
    }


    public function testFriendRequestsCacheId() {
        $this->assertEqual('friend-requests-foo-groupie', $this->helper->friendRequestsCacheId('foo', XN_Profile::GROUPIE));
    }

    public function testFriendRequestsCacheLabel() {
        $this->assertEqual('friend-requests-foo', $this->helper->friendRequestsCacheLabel('foo'));
    }

    public function testGetFriendRequests1() {
        $this->doTestgetFriendRequestsProper(TRUE);
    }

    public function testGetFriendRequests2() {
        XN_Cache::put($this->friendRequestsCacheId, serialize(array('expires' => time() - 5, 'payload' => array(array('screenName' => 'abc', 'date' => '2008-09-03T04:16:25.002Z'), array('screenName' => 'def', 'date' => '2008-09-03T04:16:25.002Z')))));
        $this->doTestgetFriendRequestsProper(TRUE);
    }

    public function testGetFriendRequests3() {
        XN_Cache::put($this->friendRequestsCacheId, serialize(array('expires' => time() + 5, 'payload' => array(array('screenName' => 'abc', 'date' => '2008-09-03T04:16:25.002Z'), array('screenName' => 'def', 'date' => '2008-09-03T04:16:25.002Z')))));
        $this->doTestgetFriendRequestsProper(FALSE, array('abc', 'def'));
    }

    public function doTestGetFriendRequestsProper($expectedPut, $expectedScreenNames = NULL) {
        $oldData = XN_Cache::get($this->helper->friendRequestsCacheId(XN_Profile::current()->screenName, XN_Profile::FRIEND_PENDING));
        $friendRequests = $this->helper->getFriendRequestsProper(XN_Profile::current()->screenName, XN_Profile::FRIEND_PENDING);
        $newData = XN_Cache::get($this->helper->friendRequestsCacheId(XN_Profile::current()->screenName, XN_Profile::FRIEND_PENDING));
        $put = $oldData !== $newData;
        $this->assertTrue(is_array($friendRequests));
        $this->assertEqual($expectedPut, $put);
        if (! is_null($expectedScreenNames)) {
            $screenNames = array();
            foreach ($friendRequests as $friendRequest) {
                $screenNames[] = $friendRequest['screenName'];
            }
            $this->assertEqual($expectedScreenNames, $screenNames);
        }
    }

    public function testInvalidateFriendRequestsCache() {
        XN_Cache::put('abc', 'x', 'friend-requests-foo');
        $this->helper->invalidateFriendRequestsCache('bar');
        $this->assertEqual('x', XN_Cache::get('abc', 'x'));
        $this->helper->invalidateFriendRequestsCache('foo');
        $this->assertNull(XN_Cache::get('abc', 'x'));
    }

}

class TestContact extends XN_Contact {
    public function __construct($screenName) {
        $this->_data = array('screenName' => $screenName, 'updatedDate' => '2008-09-03T04:16:25.002Z');
    }
}

class TestNetworkSpecificFriendRequestHelper extends Profiles_NetworkSpecificFriendRequestHelper {
    public function friendRequestsCacheId($screenName, $relationship) {
        return parent::friendRequestsCacheId($screenName, $relationship);
    }
    public function friendRequestsCacheLabel($screenName) {
        return parent::friendRequestsCacheLabel($screenName);
    }
    public function getFriendRequestsProperProper($screenName, $relationship) {
        return parent::getFriendRequestsProperProper($screenName, $relationship);
    }
    public function invalidateFriendRequestsCache($screenName) {
        return parent::invalidateFriendRequestsCache($screenName);
    }
    public function getFriendRequestsProper($screenName, $relationship) {
        return parent::getFriendRequestsProper($screenName, $relationship);
    }
}

Mock::generatePartial('TestNetworkSpecificFriendRequestHelper', 'NetworkSpecificFriendRequestHelperPartialMock', array('createContactQuery', 'loadUsers'));
Mock::generate('XN_Query');

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
