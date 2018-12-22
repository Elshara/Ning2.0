<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_ValidationHelper.php');

class Index_ValidationHelperTest extends UnitTestCase {

    public function testIsValidEmailAddress() {
        $this->assertTrue(Index_ValidationHelper::is_valid_email_address('jonathan.aquino@gmail.com'));
        $this->assertTrue(Index_ValidationHelper::is_valid_email_address('jonathan.aquino+test200704261757@gmail.com'));
        $this->assertFalse(Index_ValidationHelper::is_valid_email_address('jonathan.aquino+test200704261757gmail.com'));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';



