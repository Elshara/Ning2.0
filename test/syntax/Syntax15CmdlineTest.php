<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Syntax tests continued.
 */
class Syntax15CmdlineTest extends CmdlineTestCase {

    public function testLentgh() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            if (self::fileIsAnErrorLog($file)) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(stripos($contents, 'lentgh') === false, $file);
        }
    }

    public function testAllModelFilesHaveXnIgnoreStart() {
        // http://jira.ninginc.com/browse/BAZ-2078  [Jon Aquino 2007-03-01]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/models/') === FALSE) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'function') === false) { continue; }
            $this->assertTrue(strpos($contents, 'xn-ignore-start') !== FALSE, 'xn-ignore-start not found in ' . $file . ' ***** ');
            $this->assertTrue(strpos($contents, 'xn-ignore-end') !== FALSE, 'xn-ignore-end not found in ' . $file . ' ***** ');
        }
    }

    public function testValidAttributeTypes() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            $contents = self::getFileContent($file);
            if (strpos($contents, 'XN_Attribute::') === false) { continue; }
            if (preg_match_all('#XN_Attribute::(?!STRING|NUMBER|URL|DATE|FILEIMAGE|CONTENT|UPLOADEDFILE|BINARY|BOOLEAN)[A-Z]+#u', $contents, $matches)) {
                foreach ($matches[0] as $match) {
                    $this->fail($match . ' in ' . $file);
                }
            }
        }
    }

    private static function fileIsAnErrorLog($file) {
        return strpos($file, 'xn_volatile/error.log') !== false;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
