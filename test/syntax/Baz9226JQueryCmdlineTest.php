<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Baz9226JQueryCmdlineTest extends CmdlineTestCase {

    public function test() {
        // Instead of the jQuery Ajax functions, use xg.get, xg.post, or dojo.io.bind
        // which set the xg_token (needed for CSRF prevention) (BAZ-9226) [Jon Aquino 2008-08-27]
        foreach(array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.js'), XG_TestHelper::globr(NF_APP_BASE, '*.php')) as $file) {
            if (mb_stripos($file, '/test') !== false) { continue; }
            if (mb_stripos($file, '/dojo-adapter') !== false) { continue; }
            if (strpos($file, 'xn_resources/widgets/lib/core.min.js') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(mb_stripos($contents, '$.ajax') === false, $file);
            $this->assertTrue(mb_stripos($contents, '$.post') === false, $file);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
