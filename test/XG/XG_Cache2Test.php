<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');
Mock::generate('XN_Cache');

class XG_Cache2Test extends UnitTestCase {
    public function testOutputCacheStart() {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile/cacheUnitTestFile';
        touch($file);
        $fileSystemTime = filemtime($file);
        $phpTime = time();
        if ($phpTime != $fileSystemTime) {
            $this->assertTrue(false, "PHP clock ($phpTime) does not match file-system clock ($fileSystemTime)" );
            return;
        }
        // This doesn't test concurrent long operations though.  [Jon Aquino 2007-09-04]
        $this->assertEqual('apple', $this->doTestOutputCacheStart('apple'));
        $this->assertEqual('apple', $this->doTestOutputCacheStart('pear'));
        sleep(3);
        $this->assertEqual('banana', $this->doTestOutputCacheStart('banana'));
    }

    private function doTestOutputCacheStart($outputForCacheRebuild) {
        ob_start();
        if (! XG_Cache::outputCacheStart('testOutputCacheStart', 2)) {
            echo $outputForCacheRebuild;
            XG_Cache::outputCacheEnd('testOutputCacheStart');
        }
        $output = trim(ob_get_contents());
        ob_end_clean();
        return $output;
    }

    public function testLoad() {
        // XN_Profile::load throws an exception if the profile cannot be found.
        // XG_Cache::profiles does not. [Jon Aquino 2007-09-21]
        $this->assertNotNull(XG_Cache::profiles('NingDev'));
        if (XN_Profile::current()->screenName == 'NingDev') {
            $this->assertNotNull(XG_Cache::profiles('NingDev')->email);
        }
        $this->assertNull(XG_Cache::profiles('blahblahblahfoo'));
    }

    public function testLockAndUnlock() {
        $this->assertTrue(XG_Cache::unlock('profile-address-' . md5('never-locked')));
        $this->doTestLockAndUnlock('profile-address-' . md5('any-old-thing'));
        //TODO put in a non-md5-ed id and test checking of format?
    }

    private function doTestLockAndUnlock($id) {
        $this->assertTrue(XG_Cache::lock($id, 1));
        $this->assertFalse(XG_Cache::lock($id, 1));
        sleep(2);
        $this->assertTrue(XG_Cache::lock($id, 10));
        $this->assertFalse(XG_Cache::lock($id, 1));
        $this->assertTrue(XG_Cache::unlock($id));
        $this->assertTrue(XG_Cache::lock($id, 10));
        $this->assertFalse(XG_Cache::lock($id, 1));
        $this->assertFalse(XG_Cache::lock($id, 1));
    }

	// 1: no object
    public function testConcurrentLock1() {
    	$id = rand();
		$cacher = new MockXN_Cache;

		$cacher->setReturnValue('insert',true);
		$cacher->expectOnce('insert');

		$cacher->expectNever('get');

		$cacher->expectNever('remove');

		$this->assertTrue(XG_Cache::lock($id, 10, $cacher));
	}
	// 2: object exists, but disappears
    public function testConcurrentLock2() {
		$id = rand();
		$cacher = new MockXN_Cache;

		$cacher->setReturnValueAt(0, 'insert', false);
		$cacher->setReturnValueAt(1, 'insert', true);
		$cacher->expectCallCount('insert', 2);

		$cacher->setReturnValue('get', 0);
		$cacher->expectOnce('get');

		$cacher->expectNever('remove');

		$this->assertTrue(XG_Cache::lock($id, 10, $cacher));
		$cacher->tally();
	}

	// 3: object exists and not expired
    public function testConcurrentLock3() {
    	$id = rand();
		$cacher = new MockXN_Cache;

		$cacher->setReturnValueAt(0, 'insert', false);
		$cacher->expectOnce('insert');

		$cacher->setReturnValue('get', time()+100);
		$cacher->expectOnce('get');

		$cacher->expectNever('remove');

		$this->assertFalse(XG_Cache::lock($id, 10, $cacher));
		$cacher->tally();
	}

	// 4: object exists and expired: repair key exists
    public function testConcurrentLock4() {
    	$id = rand();
		$repairKey = "$id-XG_Cache-repair-32409gu4095";
		XN_Cache::put($repairKey, 1);

		$cacher = new MockXN_Cache;

		$cacher->setReturnValueAt(0, 'insert', false);
		$cacher->expectOnce('insert');

		$cacher->setReturnValue('get', time()-100);
		$cacher->expectOnce('get');

		$cacher->expectNever('remove');

		$this->assertFalse(XG_Cache::lock($id, 10, $cacher));
		$cacher->tally();
		XN_Cache::remove($repairKey);
	}

	// 5: object exists and expired: object was replaced
    public function testConcurrentLock5() {
    	$id = rand();
		$repairKey = "$id-XG_Cache-repair-32409gu4095";
		XN_Cache::remove($repairKey);
		$cacher = new MockXN_Cache;

		$cacher->setReturnValueAt(0, 'insert', false);
		$cacher->expectOnce('insert');

		$cacher->setReturnValueAt(0, 'get', time()-100);
		$cacher->setReturnValueAt(1, 'get', -1);
		$cacher->expectCallCount('get', 2);

		$cacher->expectNever('remove');

		$this->assertFalse(XG_Cache::lock($id, 10, $cacher));
		$cacher->tally();
	}

	// 6: object exists and expired: object was removed, but then created again
    public function testConcurrentLock6() {
    	$id = rand();
		$repairKey = "$id-XG_Cache-repair-32409gu4095";
		XN_Cache::remove($repairKey);
		$cacher = new MockXN_Cache;

		$cacher->setReturnValueAt(0, 'insert', false);
		$cacher->setReturnValueAt(1, 'insert', false);
		$cacher->expectCallCount('insert', 2);

		$cacher->setReturnValue('get', time()-100);
		$cacher->expectCallCount('get', 2);

		$cacher->expectCallCount('remove',1);

		$this->assertFalse(XG_Cache::lock($id, 10, $cacher));
		$cacher->tally();
	}
}

XG_App::includeFileOnce('/test/test_footer.php');
