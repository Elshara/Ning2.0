<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Baz8222Test extends UnitTestCase {

    public function testBaz8222() {
        $contents = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/lib/index.php');
        $this->assertPattern('@include.*XG_Query.php@', $contents);
        $this->assertPattern('@include.*XG_Cache.php@', $contents);
        $this->assertPattern('@include.*XG_App.php@', $contents);
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

