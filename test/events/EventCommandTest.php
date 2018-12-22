<?php	# $Id: $
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_LockHelper.php');
$GLOBALS['xntrace'] = 0;
class XN_Cache_Wrapper {
    //
    public function __construct() { # void
    }
    //
	public function __call($method, $args) { # void
		$res = call_user_func_array(array('XN_Cache',$method),$args);
		if($GLOBALS['xntrace'])
			echo "XN_Cache::$method(".join(",",$args).") = `$res';<br>\n";
		return $res;
    }
}

class TestLockHelper extends XG_LockHelper {
	public static function initLocksDebug() { # void
        self::$locks = array();
		self::$waitInterval = 0.01;
		self::$waitTimeout = 0.03;
		self::$expiredTimeout= 0.5;
		self::$cache = new XN_Cache_Wrapper;
    }
	public static function expiredTimeout() {
		return self::$expiredTimeout;
	}
	public static function expiredLockName() {
		return self::$expiredLockName;
	}
}

class MyException extends Exception {
}

class EventCommandTest extends UnitTestCase {
    public function setUp() {
        XG_TestHelper::setCurrentWidget('events');
        XG_TestHelper::deleteTestObjects();
        EventWidget::init();
        TestLockHelper::initLocksDebug();
        Events_EventCommand::_reset();
    }

    //
    public function _before1 ($cmd,$a,$b) { # void
		if($a == 1 && $b == 2)
			$this->list[] = 'before1';
    }
    //
    public function _before2 ($cmd,$a,$b) { # void
		if($a == 1 && $b == 2) {
			$this->list[] = 'before2';
			$cmd->aaa = 2;
		}
    }
    //
    public function _before3 ($cmd,$a,$b) { # void
		if($a == 1 && $b == 2)
			$this->list[] = 'before3';
    }
    //
    public function _on1 ($cmd,$a,$b) { # void
		if($a == 1 && $b == 2)
			$this->list[] = 'on1';
    }
    //
    public function _on2 ($cmd,$a,$b) { # void
		if($a == 1 && $b == 2) {
			$this->list[] = 'on2';
			$this->aaa = $cmd->aaa;
		}
	}
    //
    public function _on3 ($cmd,$a,$b) { # void
    	if($a == 1 && $b == 2)
			$this->list[] = 'on3';
    }
    //
    public function _after1 ($cmd,$a,$b) { # void
    	if($a == 1 && $b == 2)
			$this->list[] = 'after1';
    }
    //
    public function _after2 ($cmd,$a,$b) { # void
		if($a == 1 && $b == 2)
			$this->list[] = 'after2';
	}
    //
    public function _after3 ($cmd,$a,$b) { # void
    	if($a == 1 && $b == 2)
			$this->list[] = 'after3';
    }

    //
	public function _beforeL1($cmd,$a,$b) { # void
    	if($a == 1 && $b == 2)
			$this->list[] = 'beforeL1';
		$cmd->addLock('lock1');
    }

    //
	public function _beforeL2($cmd,$a,$b) { # void
    	if($a == 1 && $b == 2)
			$this->list[] = 'beforeL2';
		$cmd->addLock('lock2');
    }

    //
    public function _onExc ($cmd, $a, $b) { # void
    	if($a == 1 && $b == 2) {
    		$this->a1 = XN_Cache::get('lock1');
			$this->a2 = XN_Cache::get('lock2');
			$this->list[] = 'onExc';
			throw new MyException;
		}
    }


    //
    public function testRegister () { # void
		Events_EventCommand::register('cmd1',array($this,'_before1'),array($this,'_on1'),array($this,'_after1'));
		Events_EventCommand::register('cmd1',array($this,'_before2'),array($this,'_on2'),array($this,'_after2'));
		Events_EventCommand::register('cmd3',array($this,'_before3'),array($this,'_on3'),array($this,'_after3'));
		$this->list = array();
		Events_EventCommand::execute('cmd1',1,2);
		$this->assertEqual($this->list,qw('before1 before2 on1 on2 after1 after2'));
		$this->assertEqual($this->aaa, 2);

		$this->list = array();
		Events_EventCommand::execute('cmd3',1,2);
		$this->assertEqual($this->list,qw('before3 on3 after3'));
    }

    //
    public function testLockFail () { # void
        XN_Cache::remove('lock1');
        $now = microtime(TRUE);
        XN_Cache::put('lock1',$now);

		Events_EventCommand::register('cmd1',array($this,'_beforeL1'),array($this,'_on1'));
		$this->list = array();
		$ok = 0; try{ Events_EventCommand::execute('cmd1',1,2); } catch(Events_LockException $e){$ok = 1;};
		$this->assertEqual($this->list,array('beforeL1'));
		$this->assertTrue($ok);
		$this->assertEqual(XN_Cache::get('lock1'),$now);

		usleep( TestLockHelper::expiredTimeout() * 1000000 * 1.5 ); // more than expiredTimeout

		$this->list = array();
		$ok = 0; try{ Events_EventCommand::execute('cmd1',1,2); } catch(Events_LockException $e){$ok = 1;};
		$this->assertEqual($this->list,array('beforeL1','on1'));
		$this->assertFalse($ok);
		$this->assertNotEqual(XN_Cache::get('lock1'),$now);
	}

    //
	public function testLockFailRepair () { # void
        XN_Cache::remove('lock1');
		XN_Cache::remove(TestLockHelper::expiredLockName());
        $now = microtime(TRUE);
        XN_Cache::put('lock1',$now);

		Events_EventCommand::register('cmd1',array($this,'_beforeL1'),array($this,'_on1'));
		$this->list = array();
		$ok = 0; try{ Events_EventCommand::execute('cmd1',1,2); } catch(Events_LockException $e){$ok = 1;};
		$this->assertEqual($this->list,array('beforeL1'));
		$this->assertTrue($ok);
		$this->assertEqual(XN_Cache::get('lock1'),$now);

		usleep( TestLockHelper::expiredTimeout() * 1000000 * 1.5 ); // more than expiredTimeout

        $now2 = microtime(TRUE);
        XN_Cache::put(TestLockHelper::expiredLockName(),$now2);

		$this->list = array();
		$ok = 0; try{ Events_EventCommand::execute('cmd1',1,2); } catch(Events_LockException $e){$ok = 1;};
		$this->assertEqual($this->list,array('beforeL1'));
		$this->assertTrue($ok);
		$this->assertEqual(XN_Cache::get('lock1'),$now);

		usleep( TestLockHelper::expiredTimeout() * 1000000 * 1.5 ); // more than expiredTimeout

		$this->list = array();
		$ok = 0; try{ Events_EventCommand::execute('cmd1',1,2); } catch(Events_LockException $e){$ok = 1;};
		$this->assertEqual($this->list,array('beforeL1','on1'));
		$this->assertFalse($ok);
		$this->assertNotEqual(XN_Cache::get('lock1'),$now);
	}


    //
	public function testDeadLock() { # void
		#$GLOBALS['xntrace'] = 1;
		XN_Cache::remove('lock1');
        XN_Cache::remove('lock2');
        $now = microtime(TRUE);
        XN_Cache::put('lock1',$now-100);
        XN_Cache::put('lock2',$now);

		Events_EventCommand::register('cmd1',array($this,'_beforeL1'),array($this,'_on1'));
		Events_EventCommand::register('cmd1',array($this,'_beforeL2'),array($this,'_on1'));

		$this->assertEqual(XN_Cache::get('lock1'),$now-100);
		$this->assertEqual(XN_Cache::get('lock2'),$now);

		$this->list = array();
		$ok = 0; try{ Events_EventCommand::execute('cmd1',1,2); } catch(Events_LockException $e){$ok = 1;};

		$this->assertEqual($this->list,array('beforeL1','beforeL2'));
		$this->assertTrue($ok);
		$this->assertEqual(XN_Cache::get('lock1'),NULL);
		$this->assertEqual(XN_Cache::get('lock2'),$now);

		usleep( TestLockHelper::expiredTimeout() * 1000000 * 1.5 ); // more than expiredTimeout

		$this->list = array();
		$ok = 0; try{ Events_EventCommand::execute('cmd1',1,2); } catch(Events_LockException $e){$ok = 1;};

		$this->assertEqual($this->list,array('beforeL1','beforeL2','on1','on1'));
		$this->assertFalse($ok);
		$this->assertEqual(XN_Cache::get('lock2'),NULL);
		$this->assertEqual(XN_Cache::get('lock1'),NULL);

    }

    //
	public function testException () { # void
		Events_EventCommand::register('cmd1',array($this,'_beforeL1'),array($this,'_on1'));
		Events_EventCommand::register('cmd1',array($this,'_beforeL2'),array($this,'_onExc'));

		$now = microtime(TRUE);

		$this->assertFalse($this->a1);
		$this->assertFalse($this->a2);

		$this->list = array();
		$ok = 0; try{ Events_EventCommand::execute('cmd1',1,2); } catch(MyException $e){$ok = 1;};
		$this->assertEqual($this->list,array('beforeL1','beforeL2','on1','onExc'));

		$this->assertTrue($this->a1>$now);
		$this->assertTrue($this->a2>$now);

		$this->assertTrue($ok);

		$this->assertEqual(XN_Cache::get('lock2'),NULL);
		$this->assertEqual(XN_Cache::get('lock1'),NULL);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
