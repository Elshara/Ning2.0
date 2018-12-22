<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_UserHelper.php');

class Video_UserHelperTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('video');
    }


    public function testGetWithXnContent() {
        $user = XN_Content::create('User');
        $user->save();
        $user = XN_Content::load($user->id);
        Video_UserHelper::set($user, 'food', 'Big Mac');
        $this->assertEqual('Big Mac', $user->my->xg_video_food);
        $this->assertEqual('Big Mac', Video_UserHelper::get($user, 'food'));
        Video_UserHelper::set($user, 'happiness', 5, XN_Attribute::NUMBER);
        $this->assertIdentical(5, $user->my->xg_video_happiness);
        $this->assertIdentical(5, Video_UserHelper::get($user, 'happiness'));
        $this->assertNull(Video_UserHelper::get($user, 'drink'));
    }
    public function testGetWithWContent() {
        $user = W_Content::create(XN_Content::create('User'));
        $user->title = $user->my->searchText = $user->my->mozzle = $user->my->defaultVisibility = $user->my->addCommentPermission = 'all';
        $user->save();
        $user = W_Content::load($user->id);
        Video_UserHelper::set($user, 'food', 'Big Mac');
        $this->assertEqual('Big Mac', $user->my->xg_video_food);
        $this->assertEqual('Big Mac', Video_UserHelper::get($user, 'food'));
        Video_UserHelper::set($user, 'happiness', 5, XN_Attribute::NUMBER);
        $this->assertIdentical(5, $user->my->xg_video_happiness);
        $this->assertIdentical(5, Video_UserHelper::get($user, 'happiness'));
        $this->assertNull(Video_UserHelper::get($user, 'drink'));
    }

    public function testCreateAnonymousProfile() {
        $profile = Video_UserHelper::createAnonymousProfile();
        $this->assertNull($profile->screenName);
        $this->assertFalse($profile->isLoggedIn());
        $this->assertTrue($profile instanceof XN_Profile);
        $this->assertFalse(XG_SecurityHelper::userIsAdmin($profile));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
