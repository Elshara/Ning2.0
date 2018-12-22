<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_PhotoHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_SecurityHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_UserHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_MessagingHelper.php');
XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
XG_App::includeFileOnce('/lib/XG_TagHelper.php');
Mock::generate('Photo_PhotoHelper');
Mock::generate('Photo_SecurityHelper');
Mock::generate('Photo_UserHelper');
Mock::generate('XG_TagHelper');
Mock::generate('Photo_MessagingHelper');
Mock::generate('XG_ActivityHelper');
Mock::generate('stdClass', 'MockXN_Content', array('save', 'set', 'setApproved', 'setVisibility', 'setTitle', 'setDescription'));

class Photo_PhotoHelperTest extends UnitTestCase {

    public function testImageMimeType() {
        $this->assertEqual('image/tiff', TestPhotoHelper::imageMimeType('image/tiff', 'foo'));
        $this->assertEqual('image/tiff', TestPhotoHelper::imageMimeType('image/tiff;', 'foo'));
        $this->assertEqual(null, TestPhotoHelper::imageMimeType('video/mpeg', 'foo'));
        $this->assertEqual(null, TestPhotoHelper::imageMimeType('foo', 'foo'));
        $this->assertEqual('image/jpeg', TestPhotoHelper::imageMimeType('foo', 'foo.jpeg'));
        $this->assertEqual('image/jpeg', TestPhotoHelper::imageMimeType('foo', 'foo.jpg'));
        $this->assertEqual('image/png', TestPhotoHelper::imageMimeType(null, 'foo.png'));
        $this->assertEqual('image/gif', TestPhotoHelper::imageMimeType('foo', 'foo.gif'));
        $this->assertEqual('image/bmp', TestPhotoHelper::imageMimeType('foo', 'foo.bmp'));
        $this->assertEqual(null, TestPhotoHelper::imageMimeType('foo', null));
        $this->assertEqual(null, TestPhotoHelper::imageMimeType(null, 'foo'));
        $this->assertEqual(null, TestPhotoHelper::imageMimeType(null, null));
    }

    public function testHandleUploadByEmail1() {
        // TODO: Extract common code from testHandleUploadByEmail1, testHandleUploadByEmail2 [Jon Aquino 2008-09-24]
        $photoPhotoHelper = new MockPhoto_PhotoHelper();
        $photoSecurityHelper = new MockPhoto_SecurityHelper();
        $photoUserHelper = new MockPhoto_UserHelper();
        $photoMessagingHelper = new MockPhoto_MessagingHelper();
        $xgActivityHelper = new MockXG_ActivityHelper();
        $tagHelper = new MockXG_TagHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->contributorName = 'Joe';
        $user = new MockXN_Content();
        $user->my = new stdClass();
        $logItem = new MockXN_Content();
        $logItem->id = '123:ActivityLogItem:456';
        $photoSecurityHelper->expectOnce('checkCurrentUserIsAdmin', array(XN_Profile::current()));
        $photoSecurityHelper->setReturnValue('checkCurrentUserIsAdmin', null);
        $photoPhotoHelper->expectOnce('create', array());
        $photoPhotoHelper->setReturnValue('create', $photo);
        $photo->expectOnce('set', array('data', 'foo.jpg', XN_Attribute::UPLOADEDFILE));
        $photo->expectOnce('setApproved', array('Y'));
        $photo->my->approved = 'Y';
        $photoUserHelper->expectOnce('load', array(XN_Profile::current()));
        $photoUserHelper->setReturnValue('load', $user);
        $photoUserHelper->expectNever('get');
        $photo->expectOnce('setTitle', array(xg_text('PHOTO_UPLOADED_ON_X', xg_date(xg_text('F_J_Y')))));
        $photo->expectOnce('setVisibility', array('friends'));
        $photo->expectAt(1, 'save', array());
        $photo->expectAt(2, 'save', array());
        $xgActivityHelper->expectOnce('logActivityIfEnabled', array(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_PHOTO, 'Joe', array($photo)));
        $xgActivityHelper->setReturnValue('logActivityIfEnabled', $logItem);
        $photoMessagingHelper->expectNever('photosAwaitingApproval');
        $photoUserHelper->expectOnce('addPhotos', array($user, 1));
        $user->expectOnce('save', array());
        $tagHelper->expectOnce('updateTagsAndSave', array($photo, NULL));
        $_POST['content'] = 'foo.jpg';
        $this->assertNull(TestPhotoHelper::uploadProper(array(
                'postVariableName' => 'content', 'title' => null, 'description' => null, 'visibility' => 'friends',
                'photoPhotoHelper' => $photoPhotoHelper, 'photoSecurityHelper' => $photoSecurityHelper, 'photoUserHelper' => $photoUserHelper,
                'photoMessagingHelper' => $photoMessagingHelper, 'xgActivityHelper' => $xgActivityHelper, 'tagHelper' => $tagHelper)));
        $this->assertNull($photo->body);
        $this->assertEqual('image/jpeg', $photo->my->mimeType);
        $this->assertEqual('123:ActivityLogItem:456', $photo->my->newContentLogItem);
    }

    public function testHandleUploadByEmail2() {
        // Not admin, approval required, subject, body  [Jon Aquino 2008-01-04]
        $photoPhotoHelper = new MockPhoto_PhotoHelper();
        $photoSecurityHelper = new MockPhoto_SecurityHelper();
        $photoUserHelper = new MockPhoto_UserHelper();
        $photoMessagingHelper = new MockPhoto_MessagingHelper();
        $xgActivityHelper = new MockXG_ActivityHelper();
        $tagHelper = new MockXG_TagHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->contributorName = 'Joe';
        $user = new MockXN_Content();
        $user->title = 'joesmith';
        $user->my = new stdClass();
        $logItem = new MockXN_Content();
        $logItem->id = '123:ActivityLogItem:456';
        $photoSecurityHelper->expectOnce('checkCurrentUserIsAdmin', array(XN_Profile::current()));
        $photoSecurityHelper->setReturnValue('checkCurrentUserIsAdmin', 'Not an admin');
        $photoSecurityHelper->expectOnce('isApprovalRequired', array());
        $photoSecurityHelper->setReturnValue('isApprovalRequired', true);
        $photoPhotoHelper->expectOnce('create', array());
        $photoPhotoHelper->setReturnValue('create', $photo);
        $photo->expectOnce('set', array('data', 'foo.jpg', XN_Attribute::UPLOADEDFILE));
        $photo->expectOnce('setApproved', array('N'));
        $photo->my->approved = 'N';
        $photoUserHelper->expectOnce('load', array(XN_Profile::current()));
        $photoUserHelper->setReturnValue('load', $user);
        $photoUserHelper->expectNever('get');
        $photo->expectOnce('setTitle', array('Joe\'s birthday'));
        $photo->expectOnce('setDescription', array('Here I am celebrating my 45th'));
        $photo->expectOnce('setVisibility', array('all'));
        $photo->expectAt(1, 'save', array());
        $photo->expectAt(2, 'save', array());
        $xgActivityHelper->expectNever('logActivityIfEnabled');
        $xgActivityHelper->setReturnValue('logActivityIfEnabled', $logItem);
        $photoMessagingHelper->expectOnce('photosAwaitingApproval', array(array($photo), 'joesmith'));
        $photoUserHelper->expectNever('addPhotos');
        $user->expectOnce('save', array());
        $_POST['content'] = 'foo.jpg';
        $tagHelper->expectOnce('updateTagsAndSave', array($photo, 'a, b, c, d'));
        $this->assertNull(TestPhotoHelper::uploadProper(array(
                'postVariableName' => 'content',
                'title' => 'Joe\'s birthday',
                'description' => 'Here I am celebrating my 45th',
                'visibility' => 'all',
                'tags' => 'a, b, c, d',
                'photoPhotoHelper' => $photoPhotoHelper, 'photoSecurityHelper' => $photoSecurityHelper, 'photoUserHelper' => $photoUserHelper,
                'photoMessagingHelper' => $photoMessagingHelper, 'xgActivityHelper' => $xgActivityHelper, 'tagHelper' => $tagHelper)));
        $this->assertEqual('image/jpeg', $photo->my->mimeType);
        $this->assertEqual(null, $photo->my->newContentLogItem);
    }

    public function testLogPhotoCreation1() {
        $xgActivityHelper = new MockXG_ActivityHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->my->approved = 'N';
        $photo->my->newContentLogItem = null;
        $photo->contributorName = null;
        $photo->expectNever('save');
        TestPhotoHelper::logPhotoCreationProper($photo, $xgActivityHelper);
    }

    public function testLogPhotoCreation2() {
        $xgActivityHelper = new MockXG_ActivityHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->my->approved = 'N';
        $photo->my->newContentLogItem = null;
        $photo->contributorName = 'Joe';
        $photo->expectNever('save');
        TestPhotoHelper::logPhotoCreationProper($photo, $xgActivityHelper);
    }

    public function testLogPhotoCreation3() {
        $xgActivityHelper = new MockXG_ActivityHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->my->approved = 'N';
        $photo->my->newContentLogItem = 'X';
        $photo->contributorName = null;
        $photo->expectNever('save');
        TestPhotoHelper::logPhotoCreationProper($photo, $xgActivityHelper);
    }

    public function testLogPhotoCreation4() {
        $xgActivityHelper = new MockXG_ActivityHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->my->approved = 'N';
        $photo->my->newContentLogItem = 'X';
        $photo->contributorName = 'Joe';
        $photo->expectNever('save');
        TestPhotoHelper::logPhotoCreationProper($photo, $xgActivityHelper);
    }

    public function testLogPhotoCreation5() {
        $xgActivityHelper = new MockXG_ActivityHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->my->approved = 'Y';
        $photo->my->newContentLogItem = null;
        $photo->contributorName = null;
        $photo->expectNever('save');
        TestPhotoHelper::logPhotoCreationProper($photo, $xgActivityHelper);
    }

    public function testLogPhotoCreation6() {
        $xgActivityHelper = new MockXG_ActivityHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->my->approved = 'Y';
        $photo->my->newContentLogItem = null;
        $photo->contributorName = 'Joe';
        $photo->expectOnce('save');
        TestPhotoHelper::logPhotoCreationProper($photo, $xgActivityHelper);
    }

    public function testLogPhotoCreation7() {
        $xgActivityHelper = new MockXG_ActivityHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->my->approved = 'Y';
        $photo->my->newContentLogItem = 'X';
        $photo->contributorName = null;
        $photo->expectNever('save');
        TestPhotoHelper::logPhotoCreationProper($photo, $xgActivityHelper);
    }

    public function testLogPhotoCreation8() {
        $xgActivityHelper = new MockXG_ActivityHelper();
        $photo = new MockXN_Content();
        $photo->my = new stdClass();
        $photo->my->approved = 'Y';
        $photo->my->newContentLogItem = 'X';
        $photo->contributorName = 'Joe';
        $photo->expectNever('save');
        TestPhotoHelper::logPhotoCreationProper($photo, $xgActivityHelper);
    }

    public function testGetSpecificPhotos() {
        $a = XN_Content::create('Photo', 'A');
        $a->save();
        sleep(1);
        $b = XN_Content::create('Photo', 'B');
        $b->save();
        sleep(1);
        $c = XN_Content::create('Photo', 'C');
        $c->save();
        $photosData = Photo_PhotoHelper::getSpecificPhotos(null, array($a->id, $b->id, $c->id));
        $this->assertEqual(array($a->id, $b->id, $c->id), XG_TestHelper::ids($photosData['photos']));
        $photosData = Photo_PhotoHelper::getSpecificPhotos(null, array($c->id, $b->id, $a->id));
        $this->assertEqual(array($c->id, $b->id, $a->id), XG_TestHelper::ids($photosData['photos']));
        $this->assertEqual(array($b->id, $c->id), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '>', $a, null, 0, 5)));
        $this->assertEqual(array(), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '<', $a, null, 0, 5)));
        $this->assertEqual(array(), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '>', $c, null, 0, 5)));
        $this->assertEqual(array($b->id, $a->id), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '<', $c, null, 0, 5)));
        $this->assertEqual(array($c->id, $b->id), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '<', $a, null, 0, 5, 'album', array($a->id, $b->id, $c->id))));
        $this->assertEqual(array(), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '>', $a, null, 0, 5, 'album', array($a->id, $b->id, $c->id))));
        $this->assertEqual(array($b->id, $a->id), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '<', $c, null, 0, 5, 'album', array($c->id, $b->id, $a->id))));
        $this->assertEqual(array(), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '>', $c, null, 0, 5, 'album', array($c->id, $b->id, $a->id))));
        $this->assertEqual(array(), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '<', $c, null, 0, 5, 'album', array($a->id, $b->id, $c->id))));
        $this->assertEqual(array($b->id, $a->id), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '>', $c, null, 0, 5, 'album', array($a->id, $b->id, $c->id))));
        $this->assertEqual(array(), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '<', $a, null, 0, 5, 'album', array($c->id, $b->id, $a->id))));
        $this->assertEqual(array($c->id, $b->id), XG_TestHelper::ids(
                Photo_PhotoHelper::adjacentPhotos(XN_Profile::current(), '>', $a, null, 0, 5, 'album', array($c->id, $b->id, $a->id))));
    }

    public function assertEqual($a, $b) {
        if (is_array($a) && is_array($b)) {
            $json = new NF_JSON();
            $a = $json->encode($a);
            $b = $json->encode($b);
        }
        parent::assertEqual($a, $b);
    }

    public function testCreateQueryForSortedPhotos() {
        $this->assertTrue(TestPhotoHelper::createQueryForSortedPhotos(XN_Profile::current(), array(), null) instanceof XG_Query);
        $this->assertTrue(TestPhotoHelper::createQueryForSortedPhotos(Photo_UserHelper::createAnonymousProfile(), array(), null) instanceof XG_Query);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

class TestPhotoHelper extends Photo_PhotoHelper {
    public static function imageMimeType($mimeType, $filename) {
        return Photo_PhotoHelper::imageMimeType($mimeType, $filename);
    }
    public static function handleUploadByMailProper($photoPhotoHelper, $photoSecurityHelper, $photoUserHelper, $photoMessagingHelper, $xgActivityHelper) {
        return Photo_PhotoHelper::handleUploadByMailProper($photoPhotoHelper, $photoSecurityHelper, $photoUserHelper, $photoMessagingHelper, $xgActivityHelper);
    }
    public static function logPhotoCreationProper($photo, $xgActivityHelper) {
        return Photo_PhotoHelper::logPhotoCreationProper($photo, $xgActivityHelper);
    }
    public static function uploadProper($args) {
        return Photo_PhotoHelper::uploadProper($args);
    }
    public static function createQueryForSortedPhotos($profile, $filters, $sort, $begin = 0, $end = 100, $needApprovedFilter = null) {
        return parent::createQueryForSortedPhotos($profile, $filters, $sort, $begin, $end, $needApprovedFilter);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
