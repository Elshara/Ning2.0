<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageCatalogReader.php');
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageCatalogWriter.php');

class LookForMalFormedLanguageFileTest extends UnitTestCase {
    public function test() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, 'XG_MessageCatalog_*.php') as $filename) {
            $contents = file_get_contents($filename);
            $this->assertNotMissingPercent($contents, $filename);
        }
    }

    public function assertNotMissingPercent($contents, $filename) {
        $message ='no language entries should contain "#$" without having %% before them: ' . $filename;
        $expectation = new NoPatternExpectation('/[^%][1-9]\$/');
        $this->assert($expectation, $contents, $message);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

