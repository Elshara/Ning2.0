<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_UserHelper.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_PrivacyHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_UserHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_PrivacyHelper.php');

class UserTest extends UnitTestCase {

    public function __construct() {
        // Initialize Widgets
        $appClass = W_Cache::getClass('app');
        call_user_func(array($appClass,'loadWidgets'));
    }

    public function testAttributeNames() {
        //  Attribute name and expected exception
        $tests = array(
            'fullName' => NULL,
            'blah' => 'Unknown property: blah',
            'xg_blah' => 'Unknown property: xg_blah',
            'xg_blah_' => 'Unknown property: xg_blah_',
            'xg__blah' => 'Unknown property: xg__blah',
            'xg_blah_blah' => NULL,
            '_xg_blah_blah' => 'Unknown property: _xg_blah_blah',
            'bz_blah_blah' => 'Unknown property: bz_blah_blah',
        );
        foreach ($tests as $name => $expectException) {
            $gotException = NULL;
            $profile = W_Content::create('User');
            try {
                $profile->my->$name = 'testing';
            } catch (Exception $e) {
                $gotException = $e->getMessage();
            }
            $this->assertEqual($gotException, $expectException);
        }
    }

    public function testMostActiveUsersForCurrentWidget() {
        XG_TestHelper::createTestUser('testJon', null);
        XG_TestHelper::createTestUser('testDavid', 0);
        XG_TestHelper::createTestUser('testPaul', 1);
        XG_TestHelper::createTestUser('testSara', 4);
        XG_TestHelper::createTestUser('testJim', 2);
        XG_TestHelper::createTestUser('testGina', 5);
        XG_TestHelper::createTestUser('testJermaine', 3);
        XG_TestHelper::createTestUser('testBill', 6, true);
        XG_TestHelper::setCurrentWidget('video');
        $this->assertEqual(implode(', ', array('testGina', 'testSara', 'testJermaine', 'testJim')), implode(', ', self::screenNames(Video_UserHelper::getMostActiveUsers(4, $numActiveUsers))));
        XG_TestHelper::setCurrentWidget('photo');
        $this->assertEqual(implode(', ', array('testGina', 'testSara', 'testJermaine', 'testJim')), implode(', ', self::screenNames(Photo_UserHelper::getMostActiveUsers(4))));
        XG_TestHelper::setCurrentWidget('forum');
        $this->assertEqual(implode(', ', array('testGina', 'testSara', 'testJermaine', 'testJim')), implode(', ', self::screenNames(User::getMostActiveUsersForCurrentWidget(4, $numActiveUsers))));
        $this->assertEqual(5, $numActiveUsers);
    }

    public function testSetAdminStatus() {
        XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
        $user = XG_TestHelper::createTestUser('testBakert92019', 0);
        $this->assertEqual(null, $user->my->memberStatus);
        $this->assertEqual('N', $user->my->isAdmin);
        User::setAdminStatus($user, true);
        $this->assertEqual('Y', $user->my->isAdmin);
        $this->assertEqual(XG_MembershipHelper::ADMINISTRATOR, $user->my->memberStatus);
        User::setAdminStatus($user, true);
        $this->assertEqual('Y', $user->my->isAdmin);
        $this->assertEqual(XG_MembershipHelper::ADMINISTRATOR, $user->my->memberStatus);
        User::setAdminStatus($user, false);
        $this->assertEqual('N', $user->my->isAdmin);
        $this->assertEqual(null, $user->my->memberStatus);

        $networkCreator = User::load(XN_Application::load()->ownerName);
        $this->assertEqual(XG_MembershipHelper::OWNER, $networkCreator->my->memberStatus);
        $this->assertEqual('N', $user->my->isAdmin);
        User::setAdminStatus($networkCreator, true);
        $this->assertEqual(XG_MembershipHelper::OWNER, $networkCreator->my->memberStatus);
        $this->assertEqual('N', $user->my->isAdmin);
        User::setAdminStatus($networkCreator, true);
        $this->assertEqual(XG_MembershipHelper::OWNER, $networkCreator->my->memberStatus);
        $this->assertEqual('N', $user->my->isAdmin);
        User::setAdminStatus($networkCreator, false);
        $this->assertEqual(XG_MembershipHelper::OWNER, $networkCreator->my->memberStatus);
        $this->assertEqual('N', $user->my->isAdmin);
    }

    private function screenNames($users) {
        $usernames = array();
        foreach ($users as $user) {
            $usernames[] = $user->title;
        }
        return $usernames;
    }

    public function testGetInternalFlag() {
        $user = XN_Content::create('TestUser');
        $this->assertFalse(User::isInternalFlagSet($user, 'a'));
        $this->assertNull($user->my->internalFlags);
        User::setInternalFlag($user, 'a');
        $this->assertTrue(User::isInternalFlagSet($user, 'a'));
        $this->assertIdentical(serialize(array('a' => TRUE)), $user->my->internalFlags);
        $this->assertFalse(User::isInternalFlagSet($user, 'b'));
        User::setInternalFlag($user, 'b');
        $this->assertTrue(User::isInternalFlagSet($user, 'a'));
        $this->assertTrue(User::isInternalFlagSet($user, 'b'));
        $this->assertIdentical(serialize(array('a' => TRUE, 'b' => TRUE)), $user->my->internalFlags);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';



