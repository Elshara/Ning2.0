<?php
require $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class EmptyClass {
}

Mock::generate('EmptyClass', 'MockXN_Cache',qw('get put'));
Mock::generate('EmptyClass', 'MockXG_Cache',qw('lock'));
Mock::generate('EmptyClass', 'MockWidget',qw('saveConfig'));

class BlockedContactListWrapper extends BlockedContactList {
    //
	public function __construct ($widget, $locker, $cacher) { # void
        $this->widget = $widget;
		$this->locker = $locker;
        $this->cacher = $cacher;
    }

	public function getKeys ($maxAge = 20, $keysToKeep = 3, $lockMaxAge = 5) { # void
		return self::_getCipherKeysProper($this->widget, 'dummy', $maxAge, $keysToKeep, $lockMaxAge, $this->locker, $this->cacher);
    }
}

class BlockedContactListTest extends UnitTestCase {

    //
    public function _createObjs () { # list
		$cacher	= new MockXN_Cache;
		$locker = new MockXG_Cache;
		$widget = new MockWidget;
		$widget->privateConfig = array('dummy' => serialize(array()));
		$list = new BlockedContactListWrapper($widget, $locker, $cacher);

		return array($cacher, $locker, $widget, $list);
    }
    //
	public function _checkKeys($keys, $widget, $extra = array()) { # void
		$data = unserialize($widget->privateConfig['dummy']);
		$this->assertTrue(is_array($data));
		if (!$data['keys']) {
			$data['keys'] = array();
		}
		foreach((array)$extra as $k)
			array_unshift($data['keys'], $k);
		$this->assertEqual($data['keys'], $keys);
    }

	// 0: no keys, lock ok
    public function testCipherKeys1 () { # void
		list($cacher,$locker,$widget,$list) = $this->_createObjs();

		$locker->expectOnce('lock');
		$locker->setReturnValue('lock', 1);

		$cacher->expectNever('get');

		$widget->expectOnce('saveConfig');
		$cacher->expectOnce('put');

		$keys = $list->getKeys();
		$this->assertEqual(count($keys),1);
		$this->_checkKeys($keys, $widget);
	}

	// 2: no keys, lock fail, data in cache
    public function testCipherKeys2 () { # void
		list($cacher,$locker,$widget,$list) = $this->_createObjs();

		$locker->expectOnce('lock');
		$locker->setReturnValue('lock', 0);

		$cacher->expectOnce('get');
		$cacher->setReturnValue('get', 'abcdef');

		$widget->expectNever('saveConfig');
		$cacher->expectNever('put');

		$keys = $list->getKeys();
		$this->assertEqual(count($keys),1);
		$this->_checkKeys($keys, $widget, 'abcdef');
	}

	// 3: no keys, lock fail, data not in cache
    public function testCipherKeys3 () { # void
		list($cacher,$locker,$widget,$list) = $this->_createObjs();

		$locker->expectOnce('lock');
		$locker->setReturnValue('lock', 0);

		$cacher->expectCallCount('get', 2);
		$cacher->setReturnValue('get', null);

		$widget->expectOnce('saveConfig');
		$cacher->expectOnce('put');

		$keys = $list->getKeys();
		$this->assertEqual(count($keys),1);
		$this->_checkKeys($keys, $widget);
	}

	// 3: no keys, lock fail, data not in cache, but then added to cache
    public function testCipherKeys3a () { # void
		list($cacher,$locker,$widget,$list) = $this->_createObjs();

		$locker->expectOnce('lock');
		$locker->setReturnValue('lock', 0);

		$cacher->expectCallCount('get', 2);
		$cacher->setReturnValueAt(0, 'get', null);
		$cacher->setReturnValueAt(1, 'get', 'abcdef');

		$widget->expectNever('saveConfig');
		$cacher->expectNever('put');

		$keys = $list->getKeys();
		$this->assertEqual(count($keys),1);
		$this->_checkKeys($keys, $widget, 'abcdef');
	}

	// 4: keys up-to-date
    public function testCipherKeys4 () { # void
		list($cacher,$locker,$widget,$list) = $this->_createObjs();
		$widget->privateConfig['dummy'] = serialize(array(
			'tm' => array(time(), time()),
			'keys' => array('abc','def'),
		));

		$locker->expectNever('lock');
		$cacher->expectNever('get');
		$widget->expectNever('saveConfig');
		$cacher->expectNever('put');

		$keys = $list->getKeys();
		$this->assertEqual(count($keys),2);
		$this->_checkKeys($keys, $widget);
	}

	// 5: key is expired, lock ok
    public function testCipherKeys5 () { # void
		list($cacher,$locker,$widget,$list) = $this->_createObjs();
		$widget->privateConfig['dummy'] = serialize(array(
			'tm' => array(time()-1000, time()-1000),
			'keys' => array('abc','def'),
		));

		$locker->expectOnce('lock');
		$locker->setReturnValue('lock', true);

		$widget->expectOnce('saveConfig');
		$cacher->expectOnce('put');

		$keys = $list->getKeys();
		$this->assertEqual(count($keys),3);
		$this->_checkKeys($keys, $widget);
	}

	// 6: key is expired, lock fail, get staled
    public function testCipherKeys6 () { # void
		list($cacher,$locker,$widget,$list) = $this->_createObjs();
		$widget->privateConfig['dummy'] = serialize(array(
			'tm' => array(time()-1000, time()-1000),
			'keys' => array('abc','def'),
		));

		$locker->expectOnce('lock');
		$locker->setReturnValue('lock', false);

		$widget->expectNever('saveConfig');
		$cacher->expectNever('put');

		$keys = $list->getKeys();
		$this->assertEqual(count($keys),2);
		$this->_checkKeys($keys, $widget);
	}

    //
	public function testLoad() { # void
		$this->assertNull(BlockedContactList::load('testningdev'));
		$this->assertTrue(is_object(BlockedContactList::load('testningdev',true)));
    }

    //
    public function testBlockSender() { # void
		BlockedContactList::blockSender('testningdev@test.com','ningdev');
		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev@test.com','ningdev'));
		$this->assertFalse(BlockedContactList::isSenderBlocked('testningdev@test.com','ningdev2'));

		BlockedContactList::blockSender('testningdev@test.com',array('email1@example.org','email2@example.org'));
		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev@test.com','email1@example.org'));
		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev@test.com','email2@example.org'));
		$this->assertFalse(BlockedContactList::isSenderBlocked('testningdev@test.com','email3@example.org'));
	}

    //
    public function testUnblockSender() { # void
        BlockedContactList::blockSender('testningdev@test.com','ningdev');
        $this->assertTrue(BlockedContactList::isSenderBlocked('testningdev@test.com','ningdev'));

        BlockedContactList::unblockSender('testningdev@test.com','ningdev');
        $this->assertFalse(BlockedContactList::isSenderBlocked('testningdev@test.com','ningdev'));

        // does running the same operation again still work...
        BlockedContactList::unblockSender('testningdev@test.com','ningdev');
        $this->assertFalse(BlockedContactList::isSenderBlocked('testningdev@test.com','ningdev'));

        BlockedContactList::blockSender('testningdev@test.com',array('email1@example.org','email2@example.org'));
        $this->assertTrue(BlockedContactList::isSenderBlocked('testningdev@test.com','email1@example.org'));
        $this->assertTrue(BlockedContactList::isSenderBlocked('testningdev@test.com','email2@example.org'));

        BlockedContactList::unblockSender('testningdev@test.com',array('email1@example.org','email2@example.org'));
        $this->assertFalse(BlockedContactList::isSenderBlocked('testningdev@test.com','email1@example.org'));
        $this->assertFalse(BlockedContactList::isSenderBlocked('testningdev@test.com','email2@example.org'));
    }
	
    //
    public function testBlockAll() { # void
		BlockedContactList::blockAllEmails('testningdev@test.com');
		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev@test.com','ningdev'));
		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev@test.com','ningdev2'));
    }

    // do nothing
	public function testMerge1() { # void
		BlockedContactList::blockSender('testningdev@test.com','email1@example.org');

		$this->assertFalse(BlockedContactList::isSenderBlocked('testningdev3@test.com','email1@example.org'));
		BlockedContactList::merge('testningdev2@test.com','testningdev3@test.com');
		$this->assertFalse(BlockedContactList::isSenderBlocked('testningdev3@test.com','email1@example.org'));

		$this->assertTrue(BlockedContactList::load('testningdev@test.com'));
		$this->assertFalse(BlockedContactList::load('testningdev2@test.com'));
		$this->assertFalse(BlockedContactList::load('testningdev3@test.com'));
    }

    // copy
	public function testMerge2() { # void
		BlockedContactList::blockSender('testningdev@test.com','email1@example.org');

		$this->assertFalse(BlockedContactList::isSenderBlocked('testningdev2@test.com','email1@example.org'));
		BlockedContactList::merge('testningdev@test.com','testningdev2@test.com');
		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev2@test.com','email1@example.org'));

		$this->assertFalse(BlockedContactList::load('testningdev@test.com'));
		$this->assertTrue(BlockedContactList::load('testningdev2@test.com'));
    }

    // merge
	public function testMerge3() { # void
		BlockedContactList::blockSender('testningdev@test.com','email1@example.org');
		BlockedContactList::blockSender('testningdev2@test.com','email2@example.org');

		$this->assertFalse(BlockedContactList::isSenderBlocked('testningdev2@test.com','email1@example.org'));
		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev2@test.com','email2@example.org'));

		BlockedContactList::merge('testningdev@test.com','testningdev2@test.com');

		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev2@test.com','email1@example.org'));
		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev2@test.com','email2@example.org'));

		$this->assertFalse(BlockedContactList::load('testningdev@test.com'));
		$this->assertTrue(BlockedContactList::load('testningdev2@test.com'));
    }

	// block everything flag
	public function testMerge4() { # void
		BlockedContactList::blockAllEmails('testningdev@test.com');

		$this->assertTrue(BlockedContactList::isSenderBlocked('testningdev@test.com','email1@example.org'));

		$be = 0;
		BlockedContactList::merge('testningdev2@test.com','testningdev@test.com', $be);
		$this->assertEqual($be, 0);

		BlockedContactList::blockAllEmails('testningdev3@test.com');

		$be = 0;
		BlockedContactList::merge('testningdev3@test.com','testningdev4@test.com', $be);
		$this->assertEqual($be, 1);

	}

    //
	public function testOptOut() { # void
		$code = BlockedContactList::createOptoutCode('abc','def');
		$this->assertEqual(BlockedContactList::parseOptoutCode($code), array('recipient'=>'abc','sender'=>'def'));
		$this->assertEqual(BlockedContactList::parseOptoutCode(strtolower($code),1), false);
		sleep(2);
		$this->assertEqual(BlockedContactList::parseOptoutCode($code,1), false);
    }

    public function tearDown() {
		XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
