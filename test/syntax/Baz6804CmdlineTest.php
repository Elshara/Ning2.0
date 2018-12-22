<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Baz6804CmdlineTest extends CmdlineTestCase {

    public function testUseFrCaInsteadOfFrFr() {
        // We currently have a fr_CA locale but not a fr_FR one. [Jon Aquino 2008-07-11]
        foreach(array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.js'), XG_TestHelper::globr(NF_APP_BASE, '*.php')) as $file) {
            if (mb_stripos($file, '/test') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(mb_stripos($contents, 'fr_FR') === false, $file);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
