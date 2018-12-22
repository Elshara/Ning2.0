<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/controllers/SearchController.php');
XG_App::includeFileOnce('/widgets/profiles/controllers/BulkController.php');

class Syntax20CmdlineTest extends CmdlineTestCase {

    public function testEmptyFiles() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (preg_match('/custom.*\.css/', $file)) { continue; }
            if (preg_match('@(groups|html)/css/component.css@', $file)) { continue; }
            if (basename($file) == 'ningbar.css') { continue; }
            if (basename($file) == 'module.css') { continue; }
            if (basename($file) == 'blank.php') { continue; }
            if (basename($file) == 'embeddable.php') { continue; }
            if (basename($file) == 'custom.css') { continue; }
            if (basename($file) == 'favicon.ico') { continue; }
            $this->assertTrue(filesize($file) > 10, $file);
        }
    }

    public function testFixDosLineEndings() {
        $badFiles = array();
        foreach(array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.php'), XG_TestHelper::globr(NF_APP_BASE, '*.js')) as $file) {
            $contents = self::getFileContent($file);
            // Fix them [Jon Aquino 2007-01-31]
            if (strstr($contents, "\r\n")) {
                $badFiles[] = basename($file);
                file_put_contents($file, str_replace("\r\n", "\n", str_replace("\r\n", "\n", $contents)));
                echo 'Fixed ' . $file . '<br />';
            }
        }
        $this->assertTrue(count($badFiles) == 0, 'Fixed ' . count($badFiles). ' files');
    }

    public function testRemoveUtf8YByteOrderMark() {
        $badFiles = array();
        foreach(array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.php'), XG_TestHelper::globr(NF_APP_BASE, '*.js')) as $file) {
            $contents = self::getFileContent($file);
            // Fix them [Jon Aquino 2007-02-11]
            if (strstr($contents, "\xEF\xBB\xBF")) {
                $badFiles[] = basename($file);
                file_put_contents($file, str_replace("\xEF\xBB\xBF", "", $contents));
                echo 'Fixed ' . $file . '<br />';
            }
        }
        $this->assertTrue(count($badFiles) == 0, 'Fixed ' . count($badFiles). ' files');
    }

    public function testBadCharacters() {
        $patterns = array("\x85", "\xEF\xBF\xBD");
        foreach(array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.php'), XG_TestHelper::globr(NF_APP_BASE, '*.js')) as $file) {
            if (strstr($file, '/test')) { continue; }
            if (strstr($file, 'zh_')) { continue; }
            if (strstr($file, 'ko_KR')) { continue; }
            if (strstr($file, 'pl_PL')) { continue; }
            if (strstr($file, 'el_GR')) { continue; }
            if (strstr($file, 'no_NO')) { continue; }
            if (strstr($file, 'sv_SE')) { continue; }
            if (strstr($file, 'bg_BG')) { continue; }
            if (strstr($file, 'ja_JP')) { continue; }
            $contents = self::getFileContent($file);
            $patternFound = false;
            foreach ($patterns as $pattern) {
                if (strpos($contents, $pattern) !== false) { $patternFound = true; }
            }
            if (! $patternFound) { continue; }
            foreach ($patterns as $pattern) {
                $lineNumber = 0;
                foreach (explode("\n", $contents) as $line) {
                    $lineNumber++;
                    if (strpos($line, 'COUNTRY_AX') !== false) { continue; }
                    $this->assertTrue(strpos($line, $pattern) === false, $this->format($pattern, $file, $line, $lineNumber));
                }
            }
        }
    }

    private function format($match, $file, $line, $lineNumber) {
        return $this->escape($match) . ' in ' . $this->escape($line) . ' ' . $file . ' ' . $lineNumber . ' ***';
    }

}

class TestSearchController20 extends Index_SearchController {
    public static function getTypesToExclude() { return self::$typesToExclude; }
}

class TestBulkController20 extends Profiles_BulkController {
    public static function getTypesToExcludeFromRemovalByUser() { return self::$typesToExcludeFromRemovalByUser; }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
