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

class XG_Query5Test extends UnitTestCase {

    public function testQueriesTooBigToCache() {
        if (XG_Query::getCacheStorage() == 'file') {
            XG_Query::invalidateCache(XG_Cache::INVALIDATE_ALL);
            // Set size based on memory limit
            XG_QueryTestStub::setMaxResultSerializedSize();
            // Get old size to reset later
            $oldSize = XG_QueryTestStub::getMaxResultSerializedSize();
            // Force unnaturally small result set limit size
            XG_QueryTestStub::setMaxResultSerializedSize(1000);
            $query = XG_Query::create('Content')->filter('owner')->end(10);
            $results = $query->execute();
            if (count($results) < 10) { return; }
            $this->assertEqual(count($results), 10);
            $this->assertPattern('/cached\? \[no\]/',$query->debugHtml());
            
            // The same query should still not be cached
            $query2 = XG_Query::create('Content')->filter('owner')->end(10);
            $results2 = $query2->execute();
            $this->assertEqual(count($results2), 10);
            $this->assertPattern('/cached\? \[no\]/',$query2->debugHtml());
            
            // Reset max result serialized size
            XG_QueryTestStub::setMaxResultSerializedSize($oldSize);
        }
        elseif (XG_Query::getCacheStorage() == 'api') {
            XN_Cache::remove(XN_Cache::ALL);

            $fraction = 3;

            $s = str_repeat('x', XN_Cache::MAX_DATA_SIZE / $fraction);
            for ($i = 0; $i <= $fraction; $i++) {
                $c = XN_Content::create('TestBaz5747');
                $c->my->filler = $s;
                XG_TestHelper::markAsTestObject($c);
                $c->save();
            }
 
            $query = XG_Query::create('Content')->filter('owner')
                ->filter('type','eic','TestBaz5747');
            $results = $query->execute();
            $this->assertEqual(count($results), $fraction + 1);
            $this->assertPattern('/cached\? \[no\]/',$query->debugHtml());
            
            // The same query should still not be cached
            $query2 = XG_Query::create('Content')->filter('owner')
                ->filter('type','eic','TestBaz5747');
            $results2 = $query2->execute();
            $this->assertEqual(count($results2), $fraction + 1);
            $this->assertPattern('/cached\? \[no\]/',$query2->debugHtml());
        }

    }

    /** @see BAZ-3447 */
    public function testEstimateSerializedSizeEmpty() {
        XG_Query::invalidateCache(XG_Cache::INVALIDATE_ALL);
        // No warning should be issued
        $result = XG_Query::create('Content')->filter('owner')
                    ->filter('type','eic',md5(mt_rand()))
                    ->end(1)
                    ->uniqueResult();
        $this->assertIdentical($result, null);

    }
/* Query caching has been turned off [Jon Aquino 2007-10-22]
    public function testBaz4299() {
        XN_Content::create('Food')->save();
        $results = XG_Query::create('Content')
                ->setCaching('blah')
                ->filter('owner')
                ->filter('type', '=', 'Food')
                ->execute();
        $this->assertEqual(1, count($results));

        XN_Content::create('Food')->save();
        $results = XG_Query::create('Content')
                ->setCaching('blah')
                ->filter('owner')
                ->filter('type', '=', 'Food')
                ->execute();
        $this->assertEqual(1, count($results));
    }
*/
    public function testBaz4300() {
        $query1 = XN_Query::create('Content');
        $this->addFilters($query1);

        XG_Cache::invalidate(XG_Cache::key('type', 'ActivityLogItem'));
        XN_Cache::invalidate(XG_Cache::key('type', 'ActivityLogItem'));
        $query2 = XG_Query::create('Content')->setCaching(XG_Cache::key('type', 'ActivityLogItem'));
        $this->addFilters($query2);

        $this->assertEqual(count($query1->execute()), count($query2->execute()));
    }

    private function addFilters($query) {
        $query
             ->filter('owner')
             ->filter('type', '=', 'ActivityLogItem')
             ->order('createdDate', 'desc')
             ->begin(0)
             ->end(8);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

XG_App::includeFileOnce('/test/test_footer.php');

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(false);
}
