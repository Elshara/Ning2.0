<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/controllers/PhotoController.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_UserHelper.php');

class Photo_PhotoControllerTest extends UnitTestCase {

    public function testPrepareSlideshowFeed1() {
        $this->doTestPrepareSlideshowFeed(XN_Profile::current(), 'true', null, true);
    }
    public function testPrepareSlideshowFeed2() {
        $this->doTestPrepareSlideshowFeed(XN_Profile::current(), null, XN_Profile::current()->screenName, false);
    }
    public function testPrepareSlideshowFeed3() {
        $this->doTestPrepareSlideshowFeed(XN_Profile::current(), 'true', null, true);
    }
    public function testPrepareSlideshowFeed4() {
        $this->doTestPrepareSlideshowFeed(XN_Profile::current(), null, XN_Profile::current()->screenName, false);
    }

    public function testPrepareSlideshowFeed5() {
        $this->doTestPrepareSlideshowFeed(XN_Profile::create(null, null), 'true', '', true);
    }
    public function testPrepareSlideshowFeed6() {
        $this->doTestPrepareSlideshowFeed(XN_Profile::create(null, null), null, '', true);
    }
    public function testPrepareSlideshowFeed7() {
        $this->doTestPrepareSlideshowFeed(XN_Profile::create(null, null), 'true', '', true);
    }
    public function testPrepareSlideshowFeed8() {
        $this->doTestPrepareSlideshowFeed(XN_Profile::create(null, null), null, '', true);
    }

    private function doTestPrepareSlideshowFeed($profile, $internalView, $expectedScreenName, $expectedShouldCache) {
        $_GET['internalView'] = $internalView;
        $photoController = new TestPhotoController(W_Cache::getWidget('main'));
        $photoController->_user = $profile;
        list($profile, $shouldCache) = $photoController->prepareSlideshowFeed();
        $this->assertEqual($expectedScreenName, $profile->screenName);
        $this->assertEqual($expectedShouldCache, $shouldCache);
    }

    public function testSetRotation() {
        $photo = W_Content::create('Photo');
        TestPhotoController::setRotation($photo, 15);
        $this->assertNull($photo->my->rotation);
        TestPhotoController::setRotation($photo, 90);
        $this->assertEqual(90, $photo->my->rotation);
        TestPhotoController::setRotation($photo, '271');
        $this->assertEqual(90, $photo->my->rotation);
        TestPhotoController::setRotation($photo, '270');
        $this->assertEqual(270, $photo->my->rotation);
        TestPhotoController::setRotation($photo, '0');
        $this->assertEqual(0, $photo->my->rotation);
    }

}

class TestPhotoController extends Photo_PhotoController {
    public function prepareSlideshowFeed() {
        return parent::prepareSlideshowFeed();
    }
    public function setRotation($photo, $rotation) {
        return parent::setRotation($photo, $rotation);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


