<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_HttpHelper.php');

class Video_HttpHelperTest extends UnitTestCase {

    public function testTrimGetAndPostValues() {
        $_GET['name'] = '  Jon  ';
        $_POST['id'] = '  12345  ';
        $_POST['foods'] = array('burger', 'chips');
        Video_HttpHelper::trimGetAndPostValues();
        $this->assertEqual('Jon', $_GET['name']);
        $this->assertEqual('12345', $_POST['id']);
        $this->assertEqual(2, count($_POST['foods']));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
