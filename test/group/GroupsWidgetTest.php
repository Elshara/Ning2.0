<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Tests the code in the Groups widget
 */
class GroupsWidgetTest extends UnitTestCase {

    public function testControllers() {
        $files = XG_TestHelper::globr(NF_APP_BASE . '/widgets/groups/controllers', '*.php');
        foreach($files as $file) {
            $contents = file_get_contents($file);
            $this->assertTrue(strpos($contents, 'XG_GroupEnabledController') !== false || strpos($contents, 'XG_SequencedjobController') !== false, basename($file));
        }
    }

    public function testPhp() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/widgets/groups', '*.php') as $file) {
            $pattern = '/'
                    . '\'groups\'\)->buildUrl' // Use XG_GroupHelper::buildUrl [Jon Aquino 2007-05-02]
                    . '|\'forum\'\)->buildUrl' // Use XG_GroupHelper::buildUrl [Jon Aquino 2007-05-02]
                    . '/i';
            $contents = file_get_contents($file);
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (preg_match($pattern, basename($file) . ':' . $line, $matches)) {
                    $this->assertTrue(FALSE, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    private function format($match, $file, $line, $lineNumber) {
        return $match . ' in ' . $line . ' ' . $file . ' ' . $lineNumber . ' ***';
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
