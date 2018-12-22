<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_CacheHelper.php');

class XG_CacheHelperTest extends UnitTestCase {

    public function setUp() {
        XN_Cache::remove('foo');
        $this->helper = XG_CacheHelper::instance();
    }

    public function testGet() {
        $this->assertNull(XN_Cache::get('foo'));
        $this->assertEqual('abc!', XG_CacheHelper::instance()->get('foo', NULL, 5, array($this, 'buildCallback'), array('abc')));
        $data = unserialize(XN_Cache::get('foo'));
        $this->assertIdentical('abc!', $data['payload']);
        $this->assertWithinMargin(time() + 5, $data['expires'], 1);
        $this->assertEqual('abc!', XG_CacheHelper::instance()->get('foo', NULL, 5, array($this, 'buildCallback'), array('def')));
        $data = unserialize(XN_Cache::get('foo'));
        $this->assertIdentical('abc!', $data['payload']);
        $this->assertWithinMargin(time() + 5, $data['expires'], 1);
    }

    public function buildCallback($data) {
        return $data . '!';
    }

    public function testGetProper() {
        $this->assertNull($this->helper->getProper('foo'));
        XN_Cache::put('foo', serialize(array('expires' => time() - 5, 'payload' => 'bar')));
        $this->assertNull($this->helper->getProper('foo'));
        XN_Cache::put('foo', serialize(array('expires' => time() + 5, 'payload' => 'bar')));
        $this->assertEqual('bar', $this->helper->getProper('foo'));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

