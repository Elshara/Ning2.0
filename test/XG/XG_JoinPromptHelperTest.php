<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class XG_JoinPromptHelperTest extends UnitTestCase {

    public function testJoinOnSavePrecededByRedirectIfNotMember() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = file_get_contents($file);
            if (strpos($contents, 'XG_JoinPromptHelper::joinOn') === false) { continue; }
            $previousLine = '';
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                $this->assertTrue(strpos($line, 'XG_JoinPromptHelper::joinOn') === false || strpos($previousLine, 'redirectIfNotMember') !== false, $line . ' - ' . $file . ' line ' . $i);
                $previousLine = $line;
            }
        }
    }

    public function testUseJoinGroupOnInsteadOfJoinOn() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = file_get_contents($file);
            if (strpos($contents, 'XG_JoinPromptHelper::joinOn') === false) { continue; }
            $previousLine = '';
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                $this->assertTrue(strpos($line, 'XG_JoinPromptHelper::joinOn') === false, $line . ' - ' . $file . ' line ' . $i);
                $previousLine = $line;
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
