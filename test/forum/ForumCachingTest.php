<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Checks that all queries are cached in the Forum widget
 */
class ForumCachingTest extends UnitTestCase {

    public function testAllFiltersHaveCaching() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/widgets/forum', '*.php') as $file) {
            if (strpos($file, 'Forum_BulkHelper.php') !== false) { continue; }
            if (strpos($file, 'SequencedjobController.php') !== false) { continue; }
            $pattern = '/.{0,200}filter..type(?!.{0,200}addCaching).{0,200}/i';
            $contents = str_replace("\n", ' ', file_get_contents($file));
            preg_match_all($pattern, $contents, $matches);
            foreach ($matches[0] as $match) {
                if (strpos($match, 'function deleteLinkIfNecessary') !== FALSE) { continue; }
                if (strpos($match, 'function updateActivityCount') !== FALSE) { continue; }
                if (strpos($match, "GET['id']") !== FALSE) { continue; }
                if (strpos($match, '->id') !== FALSE) { continue; }
                if (strpos($match, 'topicIdsToQuery') !== FALSE) { continue; }
                if (strpos($match, '->title') !== FALSE) { continue; }
                if (strpos($match, 'AndSubComments') !== FALSE) { continue; }
                if (strpos($match, 'function topics') !== FALSE) { continue; }
                if (strpos($match, 'XN_Query') !== FALSE) { continue; }
                if (strpos($match, 'addCaching') !== FALSE) { continue; }
                if (strpos($match, 'setCaching') !== FALSE) { continue; }
                if (strpos($match, 'no caching on search queries') !== FALSE) { continue; }
                $this->assertTrue(FALSE, $match . ' in ' . $file);
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
