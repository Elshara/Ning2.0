<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');

class XG_ActivityHelperTest extends UnitTestCase {

    public function testDeleteActivityItemsWithContentDeprecated() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $filename) {
            if (strpos($filename, '/test') !== false) { continue; }
            $this->assertTrue(strpos(file_get_contents($filename), 'deleteActivityItemsWithContent') === false, 'Non-existent function deleteActivityItemsWithContent() called in ' . $filename);
        }
    }

    public function testDeleteContentFromActivityItemsDeprecated() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $filename) {
            if (strpos($filename, '/test') !== false) { continue; }
            $this->assertTrue(strpos(file_get_contents($filename), 'deleteContentFromActivityItems') === false, 'Non-existent function deleteContentFromActivityItems() called in ' . $filename);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
