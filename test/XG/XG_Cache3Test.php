<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');

class XG_Cache3Test extends UnitTestCase {

    public function setUp() {
        TestCache::clear();
    }

    public function tearDown() {
        $_GET['xn_debug'] = null;
        XG_TestHelper::deleteTestObjects();
    }

    public function testProfiles() {
        $ningDev = XN_Profile::load('NingDev');
        $jon = XN_Profile::load('JonathanAquino');

        TestCache::clear();
        $results = XG_Cache::profiles(array('NingDev', 'JonathanAquino'));
        $this->assertEqual(2, count($results));
        $this->assertEqual('NingDev', $results['NingDev']->screenName);
        $this->assertEqual('JonathanAquino', $results['JonathanAquino']->screenName);

        if ($ningDev->email && $jon->email) {
            TestCache::clear();
            $results = XG_Cache::profiles(array($ningDev->email, $jon->email));
            $this->assertEqual(2, count($results));
            $this->assertEqual('NingDev', $results[$ningDev->email]->screenName);
            $this->assertEqual('JonathanAquino', $results[$jon->email]->screenName);
        }

        if ($jon->email) {
            TestCache::clear();
            $results = XG_Cache::profiles(array('NingDev', $jon->email));
            $this->assertEqual(2, count($results));
            $this->assertEqual('NingDev', $results['NingDev']->screenName);
            $this->assertEqual('JonathanAquino', $results[$jon->email]->screenName);
        }
    }

    public function testProfiles2() {
        XN_Debug::allowDebug();
        $_GET['xn_debug'] = 'api-comm';
        ob_start();
        $profiles = XG_Cache::profiles(array('foo@jsldkfh'));
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertNotEqual(0, strlen($output));
        $this->assertTrue(is_array($profiles));
        $this->assertEqual(0, count($profiles));

        ob_start();
        $profiles = XG_Cache::profiles(array('foo@jsldkfh'));
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertEqual(0, strlen($output));
        $this->assertTrue(is_array($profiles));
        $this->assertEqual(0, count($profiles));
    }

    public function testProfiles3() {
        XN_Debug::allowDebug();
        $_GET['xn_debug'] = 'api-comm';
        ob_start();
        $profile = XG_Cache::profiles('foo@jsldkfh');
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertNotEqual(0, strlen($output));
        $this->assertNull($profile);

        ob_start();
        $profile = XG_Cache::profiles('foo@jsldkfh');
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertEqual(0, strlen($output));
        $this->assertNull($profile);
    }

    public function testContent() {
        $a = XN_Content::create('Food');
        $a->save();
        $this->assertEqual($a->id, XG_Cache::content($a->id)->id);
    }
}

class TestCache extends XG_Cache {
    public static function clear() {
        parent::$_cache = array();
        parent::$_invalidIds = array();
    }
}

XG_App::includeFileOnce('/test/test_footer.php');

