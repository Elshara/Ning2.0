<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_UserHelper.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_PrivacyHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_UserHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_PrivacyHelper.php');

class User4Test extends UnitTestCase {

    public function testLockProfileAddress() {
        XG_TestHelper::createTestUser('test');
        $user = W_Content::create('User');
        $this->assertFalse($user->lockProfileAddress('testProfileAddress'));
        $this->assertFalse($user->lockProfileAddress('testprofileaddress'));
        $this->assertFalse($user->lockProfileAddress('tesTprofilEaddresS'));
        $this->assertTrue($user->lockProfileAddress('KingOfTheHill'));
        $this->assertTrue($user->lockProfileAddress('KingOfTheHill2'));
        $this->assertFalse($user->lockProfileAddress('KingOfTheHill'));
        $this->assertFalse($user->lockProfileAddress('kingofthehill'));
        $this->assertFalse($user->lockProfileAddress('kingofthehill2'));
    }

    public function testLockProfileAddress2() {
        $user0 = XN_Content::create('User');
        $user = W_Content::create($user0);
        $user->my->mozzle = 'profiles';
        $user->my->defaultVisibility = 'me';
        $user->my->addCommentPermission = 'me';

        TestUser::setScreenNamesWithoutUserObjects(array());
        $this->assertTrue($user->lockProfileAddress('blahblahblah'));
        TestUser::setScreenNamesWithoutUserObjects(array());
        $this->assertFalse($user->lockProfileAddress('blahblahblah'));
        $user->title = 'blahblahblah';
        $user0->save(); // Save $user0 rather than $user to avoid validation checks [Jon Aquino 2007-10-04]
        TestUser::setScreenNamesWithoutUserObjects(array());
        $this->assertTrue($user->lockProfileAddress('blahblahblah'));

        TestUser::setScreenNamesWithoutUserObjects(array());
        $this->assertTrue($user->lockProfileAddress('hellohellohello'));
        TestUser::setScreenNamesWithoutUserObjects(array());
        $this->assertFalse($user->lockProfileAddress('hellohellohello'));
        $user->my->profileAddress = 'hellohellohello';
        $user0->save();
        TestUser::setScreenNamesWithoutUserObjects(array());
        $this->assertTrue($user->lockProfileAddress('hellohellohello'));
        XN_Content::delete($user->my->thumbnailId);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

class TestUser extends User {
    public static function setScreenNamesWithoutUserObjects($screenNamesWithoutUserObjects) {
        User::$screenNamesWithoutUserObjects = $screenNamesWithoutUserObjects;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';



