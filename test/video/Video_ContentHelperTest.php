<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_ContentHelper.php');

class Video_ContentHelperTest extends UnitTestCase {

    public function testAddId() {
        $user = XN_Content::create('User');
        Video_ContentHelper::add('x12345', $user, 'ratings');
        Video_ContentHelper::add('x54321', $user, 'ratings');
        Video_ContentHelper::add('x33333', $user, 'ratings');
        $this->assertTrue(preg_match('/x33333 t\d+, x54321 t\d+, x12345 t\d+/', $user->my->ratings), $user->my->ratings);
        $this->assertNull(Video_ContentHelper::value('x54321', $user, 'ratings'));
        $this->assertNull(Video_ContentHelper::value('x99999', $user, 'ratings'));
        $this->assertTrue(Video_ContentHelper::has('x54321', $user, 'ratings'));
        $this->assertFalse(Video_ContentHelper::has('x99999', $user, 'ratings'));
    }

    public function testAddId2() {
        $user = XN_Content::create('User');
        Video_ContentHelper::add('x12345', $user, 'ratings', 3);
        Video_ContentHelper::add('x54321', $user, 'ratings', 5);
        Video_ContentHelper::add('x33333', $user, 'ratings', 1);
        $this->assertTrue(preg_match('/x33333 t\d+ 1, x54321 t\d+ 5, x12345 t\d+ 3/', $user->my->ratings), $user->my->ratings);
        $this->assertEqual(5, Video_ContentHelper::value('x54321', $user, 'ratings'));
        $this->assertNull(Video_ContentHelper::value(99999, $user, 'ratings'));
    }

    public function testAddId3() {
        $user = XN_Content::create('User');
        Video_ContentHelper::add('x12345', $user, 'ratings', 3, 2);
        Video_ContentHelper::add('x54321', $user, 'ratings', 5, 2);
        Video_ContentHelper::add('x33333', $user, 'ratings', 1, 2);
        $this->assertTrue(preg_match('/x33333 t\d+ 1, x54321 t\d+ 5/', $user->my->ratings), $user->my->ratings);
    }

    public function testAddId4() {
        $user = XN_Content::create('User');
        Video_ContentHelper::add('x33333', $user, 'ratings', 3);
        Video_ContentHelper::add('x33333', $user, 'ratings', 5);
        Video_ContentHelper::add('x33333', $user, 'ratings', 1);
        $this->assertTrue(preg_match('/x33333 t\d+ 1/', $user->my->ratings), $user->my->ratings);
    }

    public function testAddId5() {
        $user = XN_Content::create('User');
        Video_ContentHelper::add('x33333', $user, 'ratings', 3, NULL, TRUE);
        Video_ContentHelper::add('x33333', $user, 'ratings', 5, NULL, TRUE);
        Video_ContentHelper::add('x33333', $user, 'ratings', 1, NULL, TRUE);
        $this->assertTrue(preg_match('/x33333 t\d+ 1, x33333 t\d+ 5, x33333 t\d+ 3/', $user->my->ratings), $user->my->ratings);
    }

    public function testIds() {
        $user = XN_Content::create('User');
        $this->assertTrue(is_array(Video_ContentHelper::ids($user, 'ratings')));
        $this->assertEqual(0, count(Video_ContentHelper::ids($user, 'ratings')));
        Video_ContentHelper::add('x12345', $user, 'ratings', 3);
        Video_ContentHelper::add('x54321', $user, 'ratings', 5);
        Video_ContentHelper::add('x33333', $user, 'ratings', 1);
        $this->assertEqual('x33333,x54321,x12345', implode(',', Video_ContentHelper::ids($user, 'ratings')));
    }

    public function testTimestamps() {
        $user = XN_Content::create('User');
        $user->my->ratings = '111 t222 3, 444 t555 6, 777 t888 9';
        $this->assertEqual('222,555,888', implode(',', Video_ContentHelper::timestamps($user, 'ratings')));
    }

    public function testCount() {
        $user = XN_Content::create('User');
        $this->assertEqual(0, Video_ContentHelper::count($user, 'ratings'));
        Video_ContentHelper::add('x12345', $user, 'ratings', 3);
        $this->assertEqual(1, Video_ContentHelper::count($user, 'ratings'));
        Video_ContentHelper::add('x54321', $user, 'ratings', 5);
        $this->assertEqual(2, Video_ContentHelper::count($user, 'ratings'));
        Video_ContentHelper::add('x33333', $user, 'ratings', 1);
        $this->assertEqual(3, Video_ContentHelper::count($user, 'ratings'));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
