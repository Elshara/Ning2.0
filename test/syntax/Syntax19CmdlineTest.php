<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Syntax tests continued.
 */
class Syntax19CmdlineTest extends CmdlineTestCase {

    public function testWidgetIdsUnique() {
        $ids = array();
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/instances', 'widget-configuration.xml') as $file) {
            preg_match('@id="(\d+)"@', self::getFileContent($file), $matches);
            $id = $matches[1];
            $this->assertFalse(in_array($id, $ids), $id);
            $ids[$id] = $id;
        }
    }

    public function testPhp() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            if (strpos($file, 'jon.php') !== FALSE) { continue; }
            if (strpos($file, 'jontest.php') !== FALSE) { continue; }
            if (strpos($file, 'JonController.php') !== FALSE) { continue; }
            if (strpos($file, '/x.php') !== FALSE) { continue; }
            self::doTestPhp($file);
        }
    }

    private function doTestPhp($file) {
        $pattern =
                '/isset.*->my->' // isset() does not work with my-> variables (NING-5574) [Jon Aquino 2007-05-30]
                . '/i';
        $contents = self::getFileContent($file);
        if (! preg_match($pattern, $contents)) {
            $this->assertTrue(TRUE);
        } else {
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (preg_match($pattern, $line, $matches)) {
                    if (strpos($matches[0], 'isset($commentedOn[') === 0) { continue; }
                    if (strpos($matches[0], 'isset($allowedTypes[$photo->my->') === 0) { continue; }
                    if (strpos($matches[0], 'isset($this->contentTypeNameMap[') === 0) { continue; }
                    if (strpos($matches[0], 'isset($_POST[$f])') === 0) { continue; }
                    $this->assertTrue(FALSE, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    public function testNoThisInStaticFunctions() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = str_replace("\n", ' ', self::getFileContent($file));
            if (strpos($file, 'Profiles_MessageHelper.php') !== false) { $contents = str_replace('thisLineLevel', '', $contents); }
            if (preg_match_all('@static function[^{]*\{' . XG_TestHelper::NESTED_CURLY_BRACKETS_PATTERN . '\}@ui', $contents, $matches)) {
                foreach ($matches[0] as $fullMatch) {
                    preg_match_all('@ function @u', $fullMatch, $functionKeywordMatches);
                    if (count($functionKeywordMatches[0]) > 1) { continue; }
                    $this->assertTrue(strpos($fullMatch, '$this') === false, $this->escape($fullMatch) . ' ' . $file);
                }
            }
        }
    }

    public function testDoNotDisplayScreenName() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'xspf.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'contributorName)') === false) { continue; }
            $contents = preg_replace('@="[^"]+"@', '', $contents);
            $contents = preg_replace("@='[^']+'@", '', $contents);
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (preg_match('@xnhtmlentities\([^(]+contributorName\)@ui', $line, $matches)) {
                    $this->assertTrue(false, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    private function format($match, $file, $line, $lineNumber) {
        return $this->escape($match) . ' in ' . $this->escape($line) . ' ' . $file . ' ' . $lineNumber . ' ***';
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
