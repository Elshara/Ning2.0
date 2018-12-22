<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_UserHelper.php');

class XG_UserHelperTest extends UnitTestCase {

    public function testUpdateUserAndProfile() {
        $profile = XN_Profile::current();
        //TODO because setFullName permanently affects the my->fullName attribute of the user's GroupMemberships we must set it back afterwards.
        // Would be better to replace this test with something that does not alter non-test objects, even temporarily.
        $originalFullName = XG_UserHelper::getFullName($profile);
        $profile->fullName = 'Jonathan Aquino';
        $profile->gender = 'm';
        $profile->birthdate = '1977-02-15';
        $profile->location = 'Victoria';
        $profile->country = 'CA';
        $user = User::load($profile);
        $user->my->syncdWithProfile = 'Y';
        XG_UserHelper::setFullName($profile, 'testUpdateUserAndProfile', true);
        $this->assertEqual('testUpdateUserAndProfile', XG_UserHelper::getFullName($profile));
        $this->assertEqual('testUpdateUserAndProfile', $profile->fullName);
        $this->assertEqual('testUpdateUserAndProfile', $user->my->fullName);
        XG_UserHelper::setFullName($profile, $originalFullName, true);
        $this->assertEqual($originalFullName, XG_UserHelper::getFullName($profile));
        $this->assertEqual($originalFullName, $profile->fullName);
        $this->assertEqual($originalFullName, $user->my->fullName);
        XG_UserHelper::setGender($profile, 'f', true);
        $this->assertEqual('f', XG_UserHelper::getGender($profile));
        $this->assertEqual('f', $profile->gender);
        $this->assertEqual('f', $user->my->gender);
        XG_UserHelper::setBirthdate($profile, '1971-02-20', true);
        $this->assertEqual('1971-02-20', XG_UserHelper::getBirthdate($profile));
        $this->assertEqual('1971-02-20', $profile->birthdate);
        $this->assertEqual('1971-02-20', $user->my->birthdate);
        XG_UserHelper::setLocation($profile, 'Toronto', true);
        $this->assertEqual('Toronto', XG_UserHelper::getLocation($profile));
        $this->assertEqual('Toronto', $profile->location);
        $this->assertEqual('Toronto', $user->my->location);
        XG_UserHelper::setCountry($profile, 'AU', true);
        $this->assertEqual('AU', XG_UserHelper::getCountry($profile));
        $this->assertEqual('AU', $profile->country);
        $this->assertEqual('AU', $user->my->country);
    }

    public function testUpdateUserButNotProfile() {
        $profile = XN_Profile::current();
        //TODO because setFullName permanently affects the my->fullName attribute of the user's GroupMemberships we must set it back afterwards.
        // Would be better to replace this test with something that does not alter non-test objects, even temporarily.
        $originalFullName = XG_UserHelper::getFullName($profile);
        $profile->fullName = 'Jonathan Aquino';
        $profile->gender = 'm';
        $profile->birthdate = '1977-02-15';
        $profile->location = 'Victoria';
        $profile->country = 'CA';
        $user = User::load($profile);
        XG_UserHelper::setFullName($profile, 'testUpdateUserButNotProfile', false);
        $this->assertEqual('testUpdateUserButNotProfile', XG_UserHelper::getFullName($profile));
        $this->assertEqual('Jonathan Aquino', $profile->fullName);
        $this->assertEqual('testUpdateUserButNotProfile', $user->my->fullName);
        XG_UserHelper::setFullName($profile, $originalFullName, false);
        $this->assertEqual($originalFullName, XG_UserHelper::getFullName($profile));
        $this->assertEqual('Jonathan Aquino', $profile->fullName);
        $this->assertEqual($originalFullName, $user->my->fullName);
        XG_UserHelper::setGender($profile, 'f', false);
        $this->assertEqual('f', XG_UserHelper::getGender($profile));
        $this->assertEqual('m', $profile->gender);
        $this->assertEqual('f', $user->my->gender);
        XG_UserHelper::setBirthdate($profile, '1971-02-20', false);
        $this->assertEqual('1971-02-20', XG_UserHelper::getBirthdate($profile));
        $this->assertEqual('1977-02-15', $profile->birthdate);
        $this->assertEqual('1971-02-20', $user->my->birthdate);
        XG_UserHelper::setLocation($profile, 'Toronto', false);
        $this->assertEqual('Toronto', XG_UserHelper::getLocation($profile));
        $this->assertEqual('Victoria', $profile->location);
        $this->assertEqual('Toronto', $user->my->location);
        XG_UserHelper::setCountry($profile, 'AU', false);
        $this->assertEqual('AU', XG_UserHelper::getCountry($profile));
        $this->assertEqual('CA', $profile->country);
        $this->assertEqual('AU', $user->my->country);
    }

    public function testGetAge() {
        $year = 60 * 60 * 24 * 365;
        $profile = XN_Profile::current();
        XG_UserHelper::setBirthdate($profile, gmdate('c', time() - 0.3*$year), false);
        $this->assertEqual(0, XG_UserHelper::getAge($profile));
        XG_UserHelper::setBirthdate($profile, gmdate('c', time() - 0.8*$year), false);
        $this->assertEqual(0, XG_UserHelper::getAge($profile));
        XG_UserHelper::setBirthdate($profile, gmdate('c', time() - 1.2*$year), false);
        $this->assertEqual(1, XG_UserHelper::getAge($profile));
        XG_UserHelper::setBirthdate($profile, gmdate('c', time() - 1.9*$year), false);
        $this->assertEqual(1, XG_UserHelper::getAge($profile));
        XG_UserHelper::setBirthdate($profile, gmdate('c', time() - 2.1*$year), false);
        $this->assertEqual(2, XG_UserHelper::getAge($profile));
        $this->assertTrue(is_integer(XG_UserHelper::getAge($profile)));
        $this->assertTrue(! is_integer('5'));
        $this->assertTrue(is_integer(5));
    }

    public function testIsThumbnailDataOk() {
        // BAZ-5029  [Jon Aquino 2007-10-19]
        $this->assertTrue(TestUserHelper::isThumbnailDataOkProper($data = 'abc'));
        $this->assertTrue(TestUserHelper::isThumbnailDataOkProper($data = ' '));
        $this->assertTrue(TestUserHelper::isThumbnailDataOkProper($data = '00'));
        $this->assertFalse(TestUserHelper::isThumbnailDataOkProper($data = '0'));
        $this->assertFalse(TestUserHelper::isThumbnailDataOkProper($data = ''));
        $this->assertFalse(TestUserHelper::isThumbnailDataOkProper($data = null));
        $this->assertFalse(TestUserHelper::isThumbnailDataOkProper($data = 'i:1192628815;'));
    }

    public function testImageMimeType() {
        $this->assertEqual(null, TestUserHelper::imageMimeType($data = 'foo'));
        $this->assertEqual('image/gif', TestUserHelper::imageMimeType($data = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/xn_resources/widgets/index/gfx/icon/help.gif')));
        $this->assertEqual('image/png', TestUserHelper::imageMimeType($data = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/xn_resources/widgets/index/gfx/icon/activity.png')));
    }

    public function testSetFullName() {
        $profile = XN_Profile::current();
        //TODO because setFullName permanently affects the my->fullName attribute of the user's GroupMemberships we must set it back afterwards.
        // Would be better to replace this test with something that does not alter non-test objects, even temporarily.
        $originalFullName = XG_UserHelper::getFullName($profile);
        $user = User::load($profile);
        $username = $user->title;
        XG_UserHelper::setFullName(XG_Cache::profiles($username), 'Magical Mister Mestopheles', false);
        $user = User::load($username);
        $this->assertEqual('Magical Mister Mestopheles', $user->my->fullName);
        $group = Group::create('z');
        $group->save();
        $membership = GroupMembership::create($group, $username);
        $this->assertEqual('Magical Mister Mestopheles', $membership->my->fullName);

        XG_UserHelper::setFullName(XG_Cache::profiles($username), 'Mcavity', false);
        $user = User::load($username);
        $this->assertEqual('Mcavity', $user->my->fullName);
        $membership = GroupMembership::loadOrCreate($group, $username);
        $this->assertEqual('Mcavity', $membership->my->fullName);

        XG_UserHelper::setFullName(XG_Cache::profiles($username), $originalFullName, false);
        $user = User::load($username);
        $this->assertEqual($originalFullName, $user->my->fullName);
        TestGroupMembership::setGroupMemberships(array());
        $membership = GroupMembership::loadOrCreate($group, $username);
        $this->assertEqual($originalFullName, $membership->my->fullName);
    }

    public function testCanDisplayAge() {
        $user = new XN_Content('TestUser');
        $profile = XG_TestHelper::createProfile('charlie');
        TestUser::setScreenNameToUserMap(array('charlie' => $user));
        $this->assertTrue(XG_UserHelper::canDisplayAge($profile));
        $user->my->displayAge = 'N';
        $this->assertFalse(XG_UserHelper::canDisplayAge($profile));
        $user->my->displayAge = 'Y';
        $this->assertTrue(XG_UserHelper::canDisplayAge($profile));
    }

    public function testCanDisplayGender() {
        $user = new XN_Content('TestUser');
        $profile = XG_TestHelper::createProfile('charlie');
        TestUser::setScreenNameToUserMap(array('charlie' => $user));
        $this->assertTrue(XG_UserHelper::canDisplayGender($profile));
        $user->my->displayGender = 'N';
        $this->assertFalse(XG_UserHelper::canDisplayGender($profile));
        $user->my->displayGender = 'Y';
        $this->assertTrue(XG_UserHelper::canDisplayGender($profile));
    }

}

class TestUser extends User {
    public static function setScreenNameToUserMap($screenNameToUserMap) {
        parent::$screenNameToUserMap = $screenNameToUserMap;
    }
}

class TestUserHelper extends XG_UserHelper {
    public static function isThumbnailDataOkProper(&$thumbnailData) { return parent::isThumbnailDataOkProper($thumbnailData); }
    public static function imageMimeType(&$data) { return parent::imageMimeType($data); }
}

class TestGroupMembership extends GroupMembership {
    public static function setGroupMemberships($groupMemberships) { parent::$groupMemberships = $groupMemberships; }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
