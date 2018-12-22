<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageHelper.php');
Mock::generate('TestRest');
Mock::generatePartial('Index_MessageHelper', 'MessageHelperPartialMock', array('friendsAcrossNing', 'friendsOnNetwork', 'removeAllFriendsOptOuts'));

class Index_MessageHelperTest extends UnitTestCase {

    private $mockRest;

    public function setUp() {
        $this->mockRest = new ExceptionMockDecorator(new MockTestRest());
        $this->messageHelper = new TestMessageHelper();
        $this->messageHelper->invalidateFriendsOnNetworkCache(XN_Profile::current()->screenName);
    }

    public function tearDown() {
        TestRest::setInstance(null);
    }

    public function testAcceptingMessagesSentToAllFriends() {
        $this->assertTrue($this->doTestAcceptingMessagesSentToAllFriends(null, null));
        $this->assertTrue($this->doTestAcceptingMessagesSentToAllFriends(null, 'Y'));
        $this->assertFalse($this->doTestAcceptingMessagesSentToAllFriends(null, 'N'));
        $this->assertTrue($this->doTestAcceptingMessagesSentToAllFriends('Y', null));
        $this->assertTrue($this->doTestAcceptingMessagesSentToAllFriends('Y', 'Y'));
        $this->assertTrue($this->doTestAcceptingMessagesSentToAllFriends('Y', 'N'));
        $this->assertFalse($this->doTestAcceptingMessagesSentToAllFriends('N', null));
        $this->assertFalse($this->doTestAcceptingMessagesSentToAllFriends('N', 'Y'));
        $this->assertFalse($this->doTestAcceptingMessagesSentToAllFriends('N', 'N'));
    }

    private function doTestAcceptingMessagesSentToAllFriends($emailAllFriendsPref, $emailNewMessagePref) {
        $user = XN_Content::create('User');
        $user->my->emailAllFriendsPref = $emailAllFriendsPref;
        $user->my->emailNewMessagePref = $emailNewMessagePref;
        return TestMessageHelper::acceptingMessagesSentToAllFriends($user);
    }

    public function testCreateContactList1() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $helper = new MessageHelperPartialMock();
        $helper->expectNever('friendsAcrossNing');
        $helper->expectNever('removeAllFriendsOptOuts');
        $contactList = array(array('name' => 'Jon', 'emailAddress' => 'jon@example.com'));
        $this->assertEqual($contactList, $helper->createContactList(array(
                'contactList' => $json->encode($contactList))));
    }

    public function testCreateContactList2() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $helper = new MessageHelperPartialMock();
        $helper->expectOnce('friendsAcrossNing', array(XN_Profile::current()->screenName, 50, 100));
        $helper->setReturnValue('friendsAcrossNing', array(
                'profiles' => array('a1' => new TestProfile('a1'), 'a2' => new TestProfile('a2'), 'a3' => new TestProfile('a3')),
                'numFriends' => 5000));
        $helper->expectOnce('removeAllFriendsOptOuts', array(array('a1' => 'a1', 'a2' => 'a2')));
        $helper->setReturnValue('removeAllFriendsOptOuts', array('a1'));
        $contactList = array(array('name' => null, 'emailAddress' => 'a1@users'));
        $this->assertEqual($contactList, $helper->createContactList(array(
                'friendStart' => 50,
                'friendEnd' => 100,
                'screenNamesExcluded' => 'a3,a5,a7')));
    }

    public function testCreateContactList3() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $helper = new MessageHelperPartialMock();
        $helper->expectOnce('friendsAcrossNing', array(XN_Profile::current()->screenName, 50, 100));
        $helper->setReturnValue('friendsAcrossNing', array(
                'profiles' => array('a1' => new TestProfile('a1'), 'a2' => new TestProfile('a2'), 'a3' => new TestProfile('a3')),
                'numFriends' => 5000));
        $helper->expectOnce('removeAllFriendsOptOuts', array(array('a1' => 'a1', 'a2' => 'a2', 'a3' => 'a3')));
        $helper->setReturnValue('removeAllFriendsOptOuts', array('a1', 'a3'));
        $contactList = array(array('name' => null, 'emailAddress' => 'a1@users'), array('name' => null, 'emailAddress' => 'a3@users'));
        $this->assertEqual($contactList, $helper->createContactList(array(
                'friendStart' => 50,
                'friendEnd' => 100)));
    }

    public function testCreateContactList4() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $helper = new MessageHelperPartialMock();
        $helper->expectOnce('friendsOnNetwork', array(XN_Profile::current()->screenName, 50, 100));
        $helper->setReturnValue('friendsOnNetwork', array(
                'profiles' => array('a1' => new TestProfile('a1'), 'a2' => new TestProfile('a2'), 'a3' => new TestProfile('a3')),
                'numFriends' => 5000));
        $helper->expectOnce('removeAllFriendsOptOuts', array(array('a1' => 'a1', 'a2' => 'a2')));
        $helper->setReturnValue('removeAllFriendsOptOuts', array('a1'));
        $contactList = array(array('name' => null, 'emailAddress' => 'a1@users'));
        $this->assertEqual($contactList, $helper->createContactList(array(
                'friendStart' => 50,
                'friendEnd' => 100,
                'friendSet' => 'FRIENDS_ON_NETWORK',
                'screenNamesExcluded' => 'a3,a5,a7')));
    }

    public function testFriendsOnNetworkProper1() {
        $this->doTestFriendsOnNetworkProper(0, TRUE);
    }

    public function testFriendsOnNetworkProper2() {
        $this->doTestFriendsOnNetworkProper(1, FALSE);
    }

    public function testFriendsOnNetworkProper3() {
        XN_Cache::put('friends-on-network-' . XN_Profile::current()->screenName . '-0-5', serialize(array('expires' => time() + 5, 'payload' => array('screenNames' => array('abc', 'def'), 'numFriends' => 2))), $this->messageHelper->friendsOnNetworkCacheLabel(XN_Profile::current()->screenName));
        $this->doTestFriendsOnNetworkProper(0, FALSE);
    }

    public function testFriendsOnNetworkProper4() {
        XN_Cache::put('friends-on-network-' . XN_Profile::current()->screenName . '-0-5', serialize(array('expires' => time() + 5, 'payload' => array('screenNames' => array('abc', 'def'), 'numFriends' => 2))), $this->messageHelper->friendsOnNetworkCacheLabel(XN_Profile::current()->screenName));
        $this->doTestFriendsOnNetworkProper(0, FALSE, array('abc', 'def'), 2);
    }

    public function testFriendsOnNetworkProper5() {
        $this->doTestFriendsOnNetworkProper(0, TRUE);
        $data = unserialize(XN_Cache::get('friends-on-network-' . XN_Profile::current()->screenName . '-0-5'));
        $this->assertWithinMargin($data['expires'], time() + 1800, 1.0);
    }

    public function doTestFriendsOnNetworkProper($begin, $expectedPut, $expectedScreenNames = NULL, $expectedNumFriends = NULL) {
        $oldData = XN_Cache::get('friends-on-network-' . XN_Profile::current()->screenName . '-' . $begin . '-5');
        $result = $this->messageHelper->friendsOnNetworkProper(XN_Profile::current()->screenName, $begin, 5);
        $newData = XN_Cache::get('friends-on-network-' . XN_Profile::current()->screenName . '-' . $begin . '-5');
        $put = $oldData !== $newData;
        $this->assertTrue(is_array($result['screenNames']));
        $this->assertTrue(is_numeric($result['numFriends']));
        $this->assertEqual($expectedPut, $put);
        if (! is_null($expectedScreenNames)) { $this->assertEqual($expectedScreenNames, $result['screenNames']); }
        if (! is_null($expectedNumFriends)) { $this->assertEqual($expectedNumFriends, $result['numFriends']); }
    }

    public function testFriendsOnNetworkCacheLabel() {
        $this->assertEqual('friends-on-network-' . XN_Profile::current()->screenName, $this->messageHelper->friendsOnNetworkCacheLabel(XN_Profile::current()->screenName));
    }

    public function testInvalidateFriendsOnNetworkCache() {
        $this->messageHelper->friendsOnNetwork(XN_Profile::current()->screenName, 0, 5);
        $this->assertNotNull(XN_Cache::get('friends-on-network-' . XN_Profile::current()->screenName . '-0-5'));
        $this->messageHelper->invalidateFriendsOnNetworkCache(XN_Profile::current()->screenName);
        $this->assertNull(XN_Cache::get('friends-on-network-' . XN_Profile::current()->screenName . '-0-5'));
    }

}

class TestProfile extends XN_Profile {
    public $screenName;
    public function __construct($screenName) {
        $this->screenName = $screenName;
    }
}

class TestMessageHelper extends Index_MessageHelper {
    public static function acceptingMessagesSentToAllFriends($user) {
        return parent::acceptingMessagesSentToAllFriends($user);
    }
    public function friendsOnNetworkProper($screenName, $begin, $end) {
        return parent::friendsOnNetworkProper($screenName, $begin, $end);
    }
    public function friendsOnNetworkCacheLabel($screenName) {
        return parent::friendsOnNetworkCacheLabel($screenName);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
