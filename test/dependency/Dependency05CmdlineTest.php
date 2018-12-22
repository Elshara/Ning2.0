<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Syntax tests continued.
 */
class Dependency05CmdlineTest extends CmdlineTestCase {

    public function testForPatchedJQueryUISortable() {
        $file = NF_APP_BASE . "/xn_resources/widgets/profiles/js/profile/ui.sortable-uncompressed.js";
        // keyword/phrase => number of expected occurrences
        $presence = array('no_cross_container' => 5, 'moduleMargin' => 6, 'hideElement' => 5, 'BAZ-7706' => 3, 'inEmptyZone' => 2, 'getNearestItemAndDistanceByContainer' => 3, 'updatePlaceholderByContainerItem' => 3, 'nearest\.dist' => 2);
        $contents = self::getFileContent($file);
        foreach (array_keys($presence) as $keyword) {
            $count = preg_match_all('/\b' . $keyword . '\b/', $contents, $matches);
            $this->assertEqual($presence[$keyword], $count, "keyword '$keyword'; $presence[$keyword] expected occurrences with $count actual *****");
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
