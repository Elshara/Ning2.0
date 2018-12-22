<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');

XN_Debug::allowDebug();

class XG_CacheMemory2Test extends UnitTestCase {

    /** A consistent key to use across tests */
    protected $key = 'chocolate covered orange';

    /** Since tests rely on checking the number of entries in
     * the cache, we should wipe out everything in the cache when
     * starting each test
     */
    public function setUp() {
        // Clear the cache so we can start from scratch
         XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
         $this->assertEqual(XG_Cache::getCacheSize(), 0);
    }

    public function testBaz2186CleanOnHit() {
        $entriesToCreate = 5;
        $results = self::populateCache($this->key, $entriesToCreate, 1);
        $this->assertEqual(XG_Cache::getCacheSize(), $entriesToCreate);

        // Force too-old cleanup on the next load() call,
        // which should be a hit
        $tooOldPercentage = XG_Cache::getTooOldCleanupPercentage();
        XG_Cache::setTooOldCleanupPercentage(100);
        $memory = memory_get_usage();
        $data = XG_Cache::load($this->key,2);
        // A cache hit should only use a reasonable amount of memory
        $this->assertMemoryUsageLessThan($memory, 1024 * 1024);
        // Make sure it really was a hit
        $this->assertResultSetsAreEqual($data, $results);

        // And since we just cleaned up, some entries should have been
        // removed from the cache (because of timing differences from
        // test run to test run, we can't be sure of the exact number
        // of removed entries, but it should be at least one.
        $this->assertTrue(XG_Cache::getCacheSize() < $entriesToCreate,"Cache size (" . XG_Cache::getCacheSize() . ") is not smaller than $entriesToCreate");
        // But we should have at least one entry left, since the last load()
        // was (should have been) a hit
        $this->assertTrue(XG_Cache::getCacheSize() >= 1, "Cache size (" . XG_Cache::getCacheSize() . ") is not at least 1");

        XG_Cache::setTooOldCleanupPercentage($tooOldPercentage);
    }

    public function testBaz2186XGQuery() {
        $tooOldPercentage = XG_Cache::getTooOldCleanupPercentage();
        // Never clean
        XG_Cache::setTooOldCleanupPercentage(0);

        $q1 = XG_Query::create('Content')->filter('owner')->maxAge(1)->end(20);
        $q2 = XG_Query::create('Content')->filter('owner')->maxAge(1)->end(20);
        $q3 = XG_Query::create('Content')->filter('owner')->maxAge(1)->end(20);
        $q4 = XG_Query::create('Content')->filter('owner')->maxAge(1)->end(20);
        $memory = memory_get_usage();
        $res1 = $q1->execute();
        // Only use up a reasonable amount of memory for the result set
        $this->assertMemoryUsageLessThan($memory, 1024 * 1024);
        sleep(2);
        $memory = memory_get_usage();
        $res2 = $q2->execute();
        // Only use up a reasonable amount of memory for the result set
        $this->assertMemoryUsageLessThan($memory, 1024 * 1024);
        sleep(2);
        $memory = memory_get_usage();
        $res3 = $q3->execute();
        // Only use up a reasonable amount of memory for the result set
        $this->assertMemoryUsageLessThan($memory, 1536 * 1024);
        sleep(2);
        $memory = memory_get_usage();
        // We'll need this time for later to set the maxAge for $q5
        $q4at = time();
        $res4 = $q4->execute();
        // Only use up a reasonable amount of memory for the result set
        $this->assertMemoryUsageLessThan($memory, 1536 * 1024);
        sleep(2);

        $this->assertPattern('/cached\? \[no\]/',$q1->debugHtml());
        $this->assertPattern('/cached\? \[no\]/',$q2->debugHtml());
        $this->assertPattern('/cached\? \[no\]/',$q3->debugHtml());
        $this->assertPattern('/cached\? \[no\]/',$q4->debugHtml());

        $this->assertResultSetsAreEqual($res1, $res2);
        $this->assertResultSetsAreEqual($res1, $res3);
        $this->assertResultSetsAreEqual($res1, $res4);

        if (XG_Query::getCacheStorage() != 'api') {
            $this->assertEqual(XG_Cache::getCacheSize(), 4, "Cache size is not 4, it's " . XG_Cache::getCacheSize());
        }

        // Now make sure things are cleaned up on the next request
        XG_Cache::setTooOldCleanupPercentage(100);

        // Set max age to at least cover $q4
        $age = 1 + time() - $q4at;
        $q5 = XG_Query::create('Content')->filter('owner')->maxAge($age)->end(20);
        $memory = memory_get_usage();
        $res5 = $q5->execute();
        // Only use up a reasonable amount of memory for the result set
        $this->assertMemoryUsageLessThan($memory, 1536 * 1024);
/* Query caching has been turned off [Jon Aquino 2007-10-22]
        $this->assertPattern('/cached\? \[yes\]/',$q5->debugHtml());
*/
        $this->assertResultSetsAreEqual($res1, $res5);

        if (XG_Query::getCacheStorage() != 'api') {
            $this->assertTrue(XG_Cache::getCacheSize() < 4,"Cache size (" . XG_Cache::getCacheSize() . ") is not smaller than 4");
            // But we should have at least one entry left, since the last load()
            // was (should have been) a hit
            $this->assertTrue(XG_Cache::getCacheSize() >= 1, "Cache size (" . XG_Cache::getCacheSize() . ") is not at least 1");
        }


        XG_Cache::setTooOldCleanupPercentage($tooOldPercentage);

    }

    protected function assertResultSetsAreEqual($a, $b) {
        $this->assertEqual(self::idStringForContent($a), self::idStringForContent($b));
    }

    // Ensure that current memory usage is less than $diff bytes bigger
    // than $start
    protected function assertMemoryUsageLessThan($start, $diff) {
        $max = $start + $diff;
        $now = memory_get_usage();
        $this->assertTrue($now < $max,
            "Memory usage is " . number_format($now) ." bytes, max allowed is " . number_format($max) ." bytes ( " .
            number_format($now - $max) . " too many)");
    }


    /**
    * Put $entriesToCreate identical entries in the cache, waiting
    * $delay seconds between each write
    */
    protected static function populateCache($key, $entriesToCreate, $delay = 0) {
        $results = XN_Query::create('Content')->filter('owner')->end(10)->execute(); 
        $oldCacheCleanupPercentage = XG_Cache::getCacheCleanupPercentage();
        // Make sure all the entries we're putting in stay there
        XG_Cache::setCacheCleanupPercentage(0);
        for ($i = 0; $i < $entriesToCreate; $i++) {
            XG_Cache::save($key, $results);
            if ($delay) { sleep($delay);}
        }
        XG_Cache::setCacheCleanupPercentage($oldCacheCleanupPercentage);
        return $results;
    }

    protected static function idStringForContent($contents) {
        $s = array();
        foreach ($contents as $content) { $s[] = $content->id; }
        return implode('|', $s);
    }
}

XG_App::includeFileOnce('/test/test_footer.php');

