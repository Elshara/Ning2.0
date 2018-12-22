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

class XG_Query3Test extends BazelTestCase {

    public function testGatherStatistics() {
        if (! $_GET['stats']) { return; }
        $stats = XG_Cache::getStatistics();
        print '<table border="1">';
        foreach ($stats as $line) {
            print '<tr><td>' . implode('</td><td>', explode("\t",$line)) . '</td></tr>';
        }
        print '</table>';
        $this->assertNotEqual(XG_Cache::getCacheSize(), 0);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $this->assertEqual(XG_Cache::getCacheSize(), 0);
    }

    public function testInvalidateOnDelete() {
        XG_Cache::invalidate(XG_Cache::key('type', 'Car'));
        $this->assertEqual(0, count(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Car')->execute()));
        $this->assertEqual(0, count(XG_Query::create('Content')->filter('owner')->filter('type', '=', 'Car')->addCaching(XG_Cache::key('type', 'Car'))->execute()));
        $car = XN_Content::create('Car')->save();
        $this->assertEqual(1, count(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Car')->execute()));
        $this->assertEqual(1, count(XG_Query::create('Content')->filter('owner')->filter('type', '=', 'Car')->addCaching(XG_Cache::key('type', 'Car'))->execute()));
        XN_Content::delete($car);
        $this->assertEqual(0, count(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Car')->execute()));
        $this->assertEqual(0, count(XG_Query::create('Content')->filter('owner')->filter('type', '=', 'Car')->addCaching(XG_Cache::key('type', 'Car'))->execute()));
    }

    public function testSetMaxResultSerializedSize() {
        $original_size = ini_get('memory_limit');
        ini_set('memory_limit', 32 * 1024 * 1024);
        $limit = trim(ini_get('memory_limit'));
        $size = null;
        if ($limit) {
            if (preg_match('/^[0-9]+$/', $limit)) {
                $size = intval($limit * XG_QueryTestStub::getSerializedSizeMemoryRatio());
            }
            else if (preg_match('/^(\d+)M$/ui', $limit, $matches)) {
                $size = intval($matches[1] * 1048576 *  XG_QueryTestStub::getSerializedSizeMemoryRatio());
            }

            XG_QueryTestStub::setMaxResultSerializedSize();
            if (is_null($size)) {
                $this->fail("Don't know how to test max result serialized size with memory_limit of $limit");
            } else {
                $this->assertEqual($size, XG_QueryTestStub::getMaxResultSerializedSize());
            }
        }
        ini_set('memory_limit', $original_size);
    }

    public function testEstimateSerializedContentSize() {
        $delta = 0.1;
        $delta_bytes = 500;
        $query = XN_Query::create('Content')->filter('owner')->end(50);
        $results = $query->execute();
        foreach ($results as $result) {
            $estimated = XG_QueryTestStub::estimateSerializedContentSize($result);
            $actual = strlen(serialize($result));
            $diff = abs($estimated - $actual);
            $this->assertFalse(  ((($diff / $actual) > $delta) && ($diff > $delta_bytes)),
               "Estimated serialized size ($estimated) is too far off of actual ($actual) for " . $this->escape($result->debugString()));
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
