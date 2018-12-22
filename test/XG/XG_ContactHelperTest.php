<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_UserHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_UserHelper.php');
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_UserHelper.php');
Mock::generate('TestRest');

class XG_ContactHelperTest extends UnitTestCase {

    private $mockRest;

    public function setUp() {
        $this->mockRest = new ExceptionMockDecorator(new MockTestRest());
        TestContactHelper::setInstance(null);
        TestContactHelper::setGetContactsResults(array());
    }

    public function tearDown() {
        TestRest::setInstance(null);
        XG_TestHelper::deleteTestObjects();
    }

    public function testGetFriendStatusFor() {
        $result = XG_ContactHelper::getFriendStatusFor(XN_Profile::current()->screenName, array(XN_Profile::current()->screenName));
        $this->assertTrue(is_array($result), 'should return an array when multiple screen names are passed in');
        if (!is_array($result)) {
            /**
             * SimpleTest doesn't follow standard xUnit design.  If the above
             * assertTrue() fails, the test case will continue to execute.
             * Obviously, the further failures mean nothing as getFriendStatusFor()
             * didn't return what was expected in the first place.  Returning here
             * keeps the test user from being flooded with extraneous failures.
             */
            return;
        }
        $this->assertEqual(0, count($result));
        $result = XG_ContactHelper::getFriendStatusFor(XN_Profile::current()->screenName, array(User::loadOrCreate(XN_Profile::current()->screenName)));
        $this->assertTrue(is_array($result));
        $this->assertEqual(0, count($result));
        $this->assertNull(XG_ContactHelper::getFriendStatusFor(XN_Profile::current()->screenName, XN_Profile::current()->screenName));

        $result = Profiles_UserHelper::getFriendStatusFor(XN_Profile::current()->screenName, array(XN_Profile::current()->screenName));
        $this->assertTrue(is_array($result));
        $this->assertEqual(0, count($result));
        $result = Profiles_UserHelper::getFriendStatusFor(XN_Profile::current()->screenName, array(User::loadOrCreate(XN_Profile::current()->screenName)));
        $this->assertTrue(is_array($result));
        $this->assertEqual(0, count($result));
        $this->assertNull(Profiles_UserHelper::getFriendStatusFor(XN_Profile::current()->screenName, XN_Profile::current()->screenName));

        $result = Photo_UserHelper::getFriendStatusFor(XN_Profile::current(), array(XN_Profile::current()->screenName));
        $this->assertTrue(is_array($result));
        $this->assertEqual(0, count($result));
        $result = Photo_UserHelper::getFriendStatusFor(XN_Profile::current(), array(User::loadOrCreate(XN_Profile::current()->screenName)));
        $this->assertTrue(is_array($result));
        $this->assertEqual(0, count($result));
        $this->assertNull(Photo_UserHelper::getFriendStatusFor(XN_Profile::current(), XN_Profile::current()->screenName));

        $result = Video_UserHelper::getFriendStatusFor(XN_Profile::current(), array(XN_Profile::current()->screenName));
        $this->assertTrue(is_array($result));
        $this->assertEqual(0, count($result));
        $result = Video_UserHelper::getFriendStatusFor(XN_Profile::current(), array(User::loadOrCreate(XN_Profile::current()->screenName)));
        $this->assertTrue(is_array($result));
        $this->assertEqual(0, count($result));
        $this->assertNull(Video_UserHelper::getFriendStatusFor(XN_Profile::current(), XN_Profile::current()->screenName));
    }

    public function testGetContacts() {
        TestContactHelper::setInstance(new TestContactHelper());
        TestContactHelper::getContacts('joe', array('bob', 'lucia'));
        $this->assertTrue(TestContactHelper::getContactsProperCalled());
        TestContactHelper::getContacts('joe', array('bob', 'lucia'));
        $this->assertFalse(TestContactHelper::getContactsProperCalled());
        TestContactHelper::getContacts('joe', array('bob', 'sam'));
        $this->assertTrue(TestContactHelper::getContactsProperCalled());
    }

    public function testGetContacts2() {
        TestContactHelper::setInstance(new TestContactHelper());
        TestContactHelper::setGetContactsResults(array(serialize(array('joe', array('bob', 'lucia'))) => null));
        TestContactHelper::getContacts('joe', array('bob', 'lucia'));
        $this->assertFalse(TestContactHelper::getContactsProperCalled());
    }

    public function testGetContacts3() {
        TestContactHelper::setInstance(new TestContactHelper());
        TestContactHelper::setGetContactsResults(array(serialize(array('joe', array('bob', 'lucia'))) => array()));
        TestContactHelper::getContacts('joe', array('bob', 'lucia'));
        $this->assertFalse(TestContactHelper::getContactsProperCalled());
    }

}

class TestContactHelper extends XG_ContactHelper {
    public static function setInstance($instance) {
        parent::$instance = $instance;
    }
    static $getContactsProperCalled = false;
    public static function getContacts($owner, $contacts) {
        return parent::getContacts($owner, $contacts);
    }
    protected function getContactsProper($owner, $contacts) {
        self::$getContactsProperCalled = true;
        return array('foo');
    }
    public static function getContactsProperCalled() {
        $getContactsProperCalled = self::$getContactsProperCalled;
        self::$getContactsProperCalled = false;
        return $getContactsProperCalled;
    }
    public static function setGetContactsResults($getContactsResults) {
        parent::$getContactsResults = $getContactsResults;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';



