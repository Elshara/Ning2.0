<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class DoNotUseAndMessageTest extends UnitTestCase {

    public function testDoNotUseAndMessage() {
        // Do not use xg_html('AND') - it is English-specific, and
        // not translatable to all languages. Instead use
        // xg_html('LIST', \$n, \$item1, \$item2, ...) [Jon Aquino 2008-08-16]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (mb_stripos($file, '/test') !== false) { continue; }
            if (mb_stripos($file, '/XG_MessageCatalog_') !== false) { continue; }
            $contents = file_get_contents($file);
            // Ignore existing violations [Jon Aquino 2008-08-16]
            if (basename($file) == 'fragment_logItem.php') {
                $contents = str_replace("Do not use xg_html('AND')", '', $contents);
                if (mb_substr_count($contents, "'AND'") == 8) { continue; }
            }
            if (basename($file) == 'Activity_LogHelperIPhone.php') {
                $contents = str_replace("Do not use xg_html('AND')", '', $contents);
                if (mb_substr_count($contents, "'AND'") == 2) { continue; }
            }
            $this->assertTrue(mb_stripos($contents, "'AND'") === false, $file, "Use xg_html('LIST', \$n, \$item1, \$item2, ...) instead of xg_html('AND')");
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
