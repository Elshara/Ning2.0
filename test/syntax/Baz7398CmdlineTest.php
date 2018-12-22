<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Baz7398CmdlineTest extends CmdlineTestCase {

    public function testIframeUploadsSpecifyExplicitXgToken() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.js') as $file) {
            if (mb_stripos($file, '/test') !== false) { continue; }
            if (strpos($file, '/dojo') !== FALSE) { continue; }
            $contents = self::getFileContent($file);
            if (mb_stripos($contents, 'multipart/form-data') === false) { continue; }
            $this->assertTrue(mb_stripos($contents, 'createCsrfTokenHiddenInput') !== false, $file);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
