<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_UserHelper.php');

class Photo_UserHelperTest extends UnitTestCase {

    public function testCreateAnonymousProfile() {
        $profile = Photo_UserHelper::createAnonymousProfile();
        $this->assertNull($profile->screenName);
        $this->assertFalse($profile->isLoggedIn());
        $this->assertTrue($profile instanceof XN_Profile);
        $this->assertFalse(XG_SecurityHelper::userIsAdmin($profile));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


