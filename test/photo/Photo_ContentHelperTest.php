<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_ContentHelper.php');

class Photo_ContentHelperTest extends UnitTestCase {

    public function setUp() {
        TestContentHelper::setInstance(null);
        TestContentHelper::setFindByIDResults(array());
    }

    public function testAddId() {
        $user = XN_Content::create('User');
        Photo_ContentHelper::add('x12345', $user, 'ratings');
        Photo_ContentHelper::add('x54321', $user, 'ratings');
        Photo_ContentHelper::add('x33333', $user, 'ratings');
        $this->assertTrue(preg_match('/x33333 t\d+, x54321 t\d+, x12345 t\d+/', $user->my->ratings), $user->my->ratings);
        $this->assertNull(Photo_ContentHelper::value('x54321', $user, 'ratings'));
        $this->assertNull(Photo_ContentHelper::value('x99999', $user, 'ratings'));
        $this->assertTrue(Photo_ContentHelper::has('x54321', $user, 'ratings'));
        $this->assertFalse(Photo_ContentHelper::has('x99999', $user, 'ratings'));
    }

    public function testAddId2() {
        $user = XN_Content::create('User');
        Photo_ContentHelper::add('x12345', $user, 'ratings', 3);
        Photo_ContentHelper::add('x54321', $user, 'ratings', 5);
        Photo_ContentHelper::add('x33333', $user, 'ratings', 1);
        $this->assertTrue(preg_match('/x33333 t\d+ 1, x54321 t\d+ 5, x12345 t\d+ 3/', $user->my->ratings), $user->my->ratings);
        $this->assertEqual(5, Photo_ContentHelper::value('x54321', $user, 'ratings'));
        $this->assertNull(Photo_ContentHelper::value(99999, $user, 'ratings'));
    }

    public function testAddId3() {
        $user = XN_Content::create('User');
        Photo_ContentHelper::add('x12345', $user, 'ratings', 3, 2);
        Photo_ContentHelper::add('x54321', $user, 'ratings', 5, 2);
        Photo_ContentHelper::add('x33333', $user, 'ratings', 1, 2);
        $this->assertTrue(preg_match('/x33333 t\d+ 1, x54321 t\d+ 5/', $user->my->ratings), $user->my->ratings);
    }

    public function testAddId4() {
        $user = XN_Content::create('User');
        Photo_ContentHelper::add('x33333', $user, 'ratings', 3);
        Photo_ContentHelper::add('x33333', $user, 'ratings', 5);
        Photo_ContentHelper::add('x33333', $user, 'ratings', 1);
        $this->assertTrue(preg_match('/x33333 t\d+ 1/', $user->my->ratings), $user->my->ratings);
    }

    public function testAddId5() {
        $user = XN_Content::create('User');
        Photo_ContentHelper::add('x33333', $user, 'ratings', 3, NULL, TRUE);
        Photo_ContentHelper::add('x33333', $user, 'ratings', 5, NULL, TRUE);
        Photo_ContentHelper::add('x33333', $user, 'ratings', 1, NULL, TRUE);
        $this->assertTrue(preg_match('/x33333 t\d+ 1, x33333 t\d+ 5, x33333 t\d+ 3/', $user->my->ratings), $user->my->ratings);
    }

    public function testIds() {
        $user = XN_Content::create('User');
        $this->assertTrue(is_array(Photo_ContentHelper::ids($user, 'ratings')));
        $this->assertEqual(0, count(Photo_ContentHelper::ids($user, 'ratings')));
        Photo_ContentHelper::add('x12345', $user, 'ratings', 3);
        Photo_ContentHelper::add('x54321', $user, 'ratings', 5);
        Photo_ContentHelper::add('x33333', $user, 'ratings', 1);
        $this->assertEqual('x33333,x54321,x12345', implode(',', Photo_ContentHelper::ids($user, 'ratings')));
    }

    public function testTimestamps() {
        $user = XN_Content::create('User');
        $user->my->ratings = '111 t222 3, 444 t555 6, 777 t888 9';
        $this->assertEqual('222,555,888', implode(',', Photo_ContentHelper::timestamps($user, 'ratings')));
    }

    public function testCount() {
        $user = XN_Content::create('User');
        $this->assertEqual(0, Photo_ContentHelper::count($user, 'ratings'));
        Photo_ContentHelper::add('x12345', $user, 'ratings', 3);
        $this->assertEqual(1, Photo_ContentHelper::count($user, 'ratings'));
        Photo_ContentHelper::add('x54321', $user, 'ratings', 5);
        $this->assertEqual(2, Photo_ContentHelper::count($user, 'ratings'));
        Photo_ContentHelper::add('x33333', $user, 'ratings', 1);
        $this->assertEqual(3, Photo_ContentHelper::count($user, 'ratings'));
    }

    public function testSortByAttribute() {
        $a = XN_Content::create('Food', 'A');
        $b = XN_Content::create('Food', 'B');
        $c = XN_Content::create('Food', 'C');
        $foods = array($a, $b, $c);
        Photo_ContentHelper::sortByAttribute($foods, array('A', 'B', 'C'), 'title');
        $this->assertEqual(array('A', 'B', 'C'), XG_TestHelper::titles($foods));
        $foods = array($a, $b, $c);
        Photo_ContentHelper::sortByAttribute($foods, array('C', 'B', 'A'), 'title');
        $this->assertEqual(array('C', 'B', 'A'), XG_TestHelper::titles($foods));
    }

    public function testFindById() {
        TestContentHelper::setInstance(new TestContentHelper());
        $this->assertTrue(Photo_ContentHelper::findById('Photo', '12345', true, true) instanceof W_Content);
        $this->assertTrue(TestContentHelper::findByIDProperCalled());
        $this->assertTrue(Photo_ContentHelper::findById('Photo', '12345', true, false) instanceof W_Content);
        $this->assertFalse(TestContentHelper::findByIDProperCalled());
        $this->assertTrue(Photo_ContentHelper::findById('Photo', '12345', false, true) instanceof XN_Content);
        $this->assertTrue(TestContentHelper::findByIDProperCalled());
        $this->assertTrue(Photo_ContentHelper::findById('Photo', '12345', false, false) instanceof XN_Content);
        $this->assertFalse(TestContentHelper::findByIDProperCalled());
    }

    public function testFindById2() {
        TestContentHelper::setInstance(new TestContentHelper());
        TestContentHelper::setFindByIDResults(array('Photo-12345-1' => null));
        $this->assertFalse(Photo_ContentHelper::findById('Photo', '12345', true, true) instanceof W_Content);
    }

}

class TestContentHelper extends Photo_ContentHelper {
    public static function setInstance($instance) {
        parent::$instance = $instance;
    }
    static $findByIDProperCalled = false;
    protected function findByIDProper($type, $id, $useWContent, $useCache) {
        self::$findByIDProperCalled = true;
        return $useWContent ? W_Content::create($type) : XN_Content::create($type);
    }
    public static function findByIDProperCalled() {
        $findByIDProperCalled = self::$findByIDProperCalled;
        self::$findByIDProperCalled = false;
        return $findByIDProperCalled;
    }
    public static function setFindByIDResults($findByIDResults) {
        parent::$findByIDResults = $findByIDResults;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
