<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/controllers/SearchController.php');
XG_App::includeFileOnce('/widgets/profiles/controllers/BulkController.php');

class Syntax12CmdlineTest extends CmdlineTestCase {

    public function testAllQueriesHaveOwnerFilter() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            if (strpos($file, 'XG_Query.php') !== FALSE) { continue; }
            $pattern = '/.{0,150}Query::create\(.Content(?!.{0,350}filter..owner).{0,350}/';
            $contents = str_replace("\n", ' ', self::getFileContent($file));
            $matchFound = preg_match($pattern, $contents, $matches);
            if ($matchFound && strpos($matches[0], '$query = $_GET[\'tag\'] || $_GET[\'q\']') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'finite number of cache files') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'function recentTopics(') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '$embed->get(\'itemCount\')') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'self::filter(') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'Forum_Filter') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'getSpecificPhotosProper') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'function getSpecificPhotos') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'Groups_Filter') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'Groups_GroupMembershipFilter') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'Page_Filter') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '_getList') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '$filter->execute') !== FALSE) { continue; }
            $this->assertFalse($matchFound, $matches[0] . ' in ' . $file . ' *****');
        }
    }

}

class TestSearchController12 extends Index_SearchController {
    public static function getTypesToExclude() { return self::$typesToExclude; }
}

class TestBulkController12 extends Profiles_BulkController {
    public static function getTypesToExcludeFromRemovalByUser() { return self::$typesToExcludeFromRemovalByUser; }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
