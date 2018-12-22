<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax18CmdlineTest extends CmdlineTestCase {

    public function testOldNingAddress() {
        // The new address is 735 Emerson Street [Jon Aquino 2007-12-13]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(strpos($contents, '167 Hamilton') === false, $file);
        }
    }

    public function testXnhtmlentitiesSpelling() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'ties') == false) { continue; }
            if (preg_match_all('@xn[a-z]+ties@ui', $contents, $matches)) {
                foreach ($matches[0] as $match) {
                    $this->assertEqual('xnhtmlentities', $match, $match . ' - ' . $file);
                }
            }
        }
    }

    public function testUseXgMailtoUrlInsteadOfMailTo() {
        // Use xg_mailto_url() instead of mailto:
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, '/lib/error.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'mailto:') === false) { continue; }
            foreach (explode("\n", $contents) as $line) {
                if (strpos($file, '/lib/XG_TemplateHelpers.php') !== false && strpos($line, "return 'mailto:") !== false) { continue; }
                if (strpos($line, "'PLEASE_TRY_REFRESHING'") !== false) { continue; }
                $this->assertTrue(strpos($line, 'mailto:') === false || strpos($line, 'subject') === false, $this->escape($line) . ' - ' . $file);
            }
        }
    }

    public function testDoNotCallXnhtmlentitiesInTextEmails() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*_text.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(strpos($contents, 'entities(') === false, $file);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
