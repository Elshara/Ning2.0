<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');

class XG_MetatagHelperTest extends UnitTestCase {

    public function testForDescription() {
        $this->assertEqual('quick brown fox jumps over lazy dog', XG_MetatagHelper::forDescription('The quick brown fox jumps over the lazy dog'));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
