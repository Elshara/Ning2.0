<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/BulkHelperTestCase.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_SecurityHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_PhotoHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_BulkHelper.php');

class Photo_BulkHelperTest extends BulkHelperTestCase {

    public function setUp() {
         XG_TestHelper::setCurrentWidget('photo');
    }

    public function testSetPrivacy() {
        $ids = array();
        $photo = Photo_PhotoHelper::create();
        $photo->isPrivate = false;
        $photo->title = "Driving Along Route 66";
        $photo->setApproved('Y');
        $photo->my->mimeType = 'image/jpeg';
        $photo->my->visibility = 'all';
        $photo->save();
        $id = $photo->id;

        Photo_BulkHelper::setPrivacy(30, true);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);

        $photo = XN_Content::load($id);
        $this->assertEqual(true, $photo->isPrivate);

        Photo_BulkHelper::setPrivacy(30, false);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);

        $photo = XN_Content::load($id);
        $this->assertEqual(false, $photo->isPrivate);
    }


    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
