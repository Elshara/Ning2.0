<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/controllers/SearchController.php');
XG_App::includeFileOnce('/widgets/profiles/controllers/BulkController.php');

class Syntax16CmdlineTest extends CmdlineTestCase {

    /**
     * Without a type filter, a query will union all tables in the app.
     */
    public function testAllQueriesHaveTypeFilter() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            if (strpos($file, 'baz1140.php') !== FALSE) { continue; }
            $pattern = '/.{0,150}Query::create\(.Content(?!.{0,350}filter..type).{0,350}/i';
            $contents = str_replace("\n", ' ', self::getFileContent($file));
            $matchFound = preg_match($pattern, $contents, $matches);
            if ($matchFound && strpos($matches[0], '$query = $_GET[\'tag\'] || $_GET[\'q\']') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'finite number of cache files') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'function recentTopics(') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'function basicQuery(') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'self::filter(') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'Forum_Filter') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'Groups_Filter') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '$embed->get(\'itemCount\')') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'getSpecificPhotosProper') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'function getSpecificPhotos') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'Groups_GroupMembershipFilter') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'Page_Filter') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '_getList') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'delete the UploadedFiles that were attached to them') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'function getActiveUsers') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'privatizeWronglyPublicComments') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '$objects = array()') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '$from + $this->pageSize;') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '$filter->execute') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'XN_Query::create(\'Content_Count\')') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], "'id', 'in'") !== FALSE) { continue; }
            $this->assertFalse($matchFound, $matches[0] . ' in ' . $file . ' *****');
        }
    }

}

class TestSearchController16 extends Index_SearchController {
    public static function getTypesToExclude() { return self::$typesToExclude; }
}

class TestBulkController16 extends Profiles_BulkController {
    public static function getTypesToExcludeFromRemovalByUser() { return self::$typesToExcludeFromRemovalByUser; }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
