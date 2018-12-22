<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');

XN_Debug::allowDebug();

class XG_CacheMemoryTest extends UnitTestCase {

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

    // Test that we successfully get a hit after storing multiple matching
    // entries in the cache
    public function testBaz2186Hit() {
        $entriesToCreate = 50;
        // A lot of entries, but no delay between them
        $results = self::populateCache($this->key, $entriesToCreate, 0);
        $this->assertEqual(XG_Cache::getCacheSize(), $entriesToCreate);
        $memory = memory_get_usage();
        $data = XG_Cache::load($this->key);
        $this->assertMemoryUsageLessThan($memory, 1024 * 1024);
        $this->assertResultSetsAreEqual($data, $results);
    }

    // Test that we successfully get a hit after storing multiple matching
    // entries in the cache
    public function testBaz2186HitDelay() {
        $entriesToCreate = 5;
        $results = self::populateCache($this->key, $entriesToCreate, 1);
        $this->assertEqual(XG_Cache::getCacheSize(), $entriesToCreate);
        $memory = memory_get_usage();
        $data = XG_Cache::load($this->key);
        $this->assertMemoryUsageLessThan($memory, 1024 * 1024);
        $this->assertResultSetsAreEqual($data, $results);
    }

    public function testBaz2186TooOld() {
        // Make sure we never clean up too-old entries for this test
        $tooOldPercentage = XG_Cache::getTooOldCleanupPercentage();
        XG_Cache::setTooOldCleanupPercentage(0);

        $entriesToCreate = 5;
        $results = self::populateCache($this->key, $entriesToCreate, 1);
        $this->assertEqual(XG_Cache::getCacheSize(), $entriesToCreate);

        // Make it so everything is too old
        sleep(5);

        // Now everything should be too old
        $memory = memory_get_usage();
        $data = XG_Cache::load($this->key,1);
        // We shouldn't chew up much memory on a cache miss
        $this->assertMemoryUsageLessThan($memory, 500);
        // Was it really a miss?
        $this->assertIsa($data, XG_Cache_Miss);

        // But this should find something
        $memory = memory_get_usage();
        $data = XG_Cache::load($this->key, 200);
        // Only use up a reasonable amount of memory for the result set
        $this->assertMemoryUsageLessThan($memory, 1024 * 1024);
        // Did we get back the right stuff?
        $this->assertResultSetsAreEqual($data, $results);

        XG_Cache::setTooOldCleanupPercentage($tooOldPercentage);
    }

    public function testBaz2186ForceCleanup() {
        // Make sure we never clean up too-old entries
        $tooOldPercentage = XG_Cache::getTooOldCleanupPercentage();
        XG_Cache::setTooOldCleanupPercentage(0);

        $entriesToCreate = 5;
        $results = self::populateCache($this->key, $entriesToCreate, 1);
        $this->assertEqual(XG_Cache::getCacheSize(), $entriesToCreate);

        // Now everything should be too old
        sleep(5);

        // Force too-old cleanup on the next load() call
        XG_Cache::setTooOldCleanupPercentage(100);
        $memory = memory_get_usage();
        $data = XG_Cache::load($this->key,1);
        // We shouldn't chew up much memory on a cache miss
        $this->assertMemoryUsageLessThan($memory, 500);

        // Make sure it was a miss
        $this->assertIsa($data, XG_Cache_Miss);

        // But this should not find anything now, since the last miss
        // Should have cleaned everything up
        $memory = memory_get_usage();
        $data = XG_Cache::load($key, 10);
        // We shouldn't chew up much memory on a cache miss
        $this->assertMemoryUsageLessThan($memory, 500);
        $this->assertIsa($data, XG_Cache_Miss);

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

