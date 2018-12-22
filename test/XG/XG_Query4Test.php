<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Query.php');

XN_Debug::allowDebug();

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(true);
    XG_Cache::clearStatistics();
}

/** This stub class exposes protected XG_Query info so it can be tested */
class XG_QueryTestStub extends XG_Query {
    public static function estimateSerializedSize($arg) { return parent::estimateSerializedSize($arg); }
    public static function estimateSerializedContentSize($arg) { return parent::estimateSerializedContentSize($arg); }
    public static function setMaxResultSerializedSize($size = null) { parent::setMaxResultSerializedSize($size); }
    public static function getMaxResultSerializedSize() { return parent::getMaxResultSerializedSize(); }
    public static function getSerializedSizeMemoryRatio() { return parent::getSerializedSizeMemoryRatio(); }
}

class XG_Query4Test extends UnitTestCase {

    public function testEstimateSerializedSize() {
        $delta = 0.15;
        $delta_bytes = 1000;
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        foreach (array('1','5','10','15','20','30','40','50','70','90','100') as $size) {
            $query = XG_Query::create('Content')->filter('owner')->end($size);
            $results = $query->execute();
            $estimated = XG_QueryTestStub::estimateSerializedSize($results);
            $actual = strlen(serialize($results));
            $diff = abs($estimated - $actual);
            $this->assertFalse(  ((($diff / $actual) > $delta) && ($diff > $delta_bytes)),
               "Estimated serialized size ($estimated) is too far off of actual ($actual) for $size element result set");
        }
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

XG_App::includeFileOnce('/test/test_footer.php');

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(false);
}
