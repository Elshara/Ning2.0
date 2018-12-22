<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');

class XG_PaginationHelperTest extends UnitTestCase {

    public function tearDown() {
        $_GET['page'] = null;
    }

    public function testComputeStart() {
        $this->assertEqual(0, XG_PaginationHelper::computeStart(NULL, 20));
        $this->assertEqual(0, XG_PaginationHelper::computeStart(0, 20));
        $this->assertEqual(0, XG_PaginationHelper::computeStart(1, 20));
        $this->assertEqual(20, XG_PaginationHelper::computeStart(2, 20));
    }

    public function testComputePagination() {
        $_GET['page'] = 2;
        $this->assertEqual(array('targetUrl' => '#', 'pageParamName' => 'page', 'curPage' => 2, 'numPages' => 2),
            XG_PaginationHelper::computePagination(10, 5, '#'));
    }

    public function testComputePagination2() {
        $_GET['commentPage'] = 2;
        $this->assertEqual(array('targetUrl' => '#', 'pageParamName' => 'commentPage', 'curPage' => 2, 'numPages' => 2),
            XG_PaginationHelper::computePagination(10, 5, '#', 'commentPage'));
    }

    public function testOutputPagination() {
        ob_start();
        XG_PaginationHelper::outputPagination(40, 25, false, 'http://example.org');
        $actual = trim(ob_get_contents());
        ob_end_clean();
        $actual = preg_replace('@\s@', '', $actual);
        $expected = preg_replace('@\s@', '', '<ul class="pagination easyclear"><li class="this"><span>1</span></li><li><a href="http://example.org/?page=2">2</a></li></ul>');
        $this->assertEqual($expected, $actual);
    }

    public function testOutputPagination2() {
        ob_start();
        XG_PaginationHelper::outputPagination(40, 25, false, '#');
        $actual = trim(ob_get_contents());
        ob_end_clean();
        $actual = preg_replace('@\s@', '', $actual);
        $expected = preg_replace('@\s@', '', '<ul class="pagination easyclear"><li class="this"><span>1</span></li><li><a href="#">2</a></li></ul>');
        $this->assertEqual($expected, $actual);
    }

    public function testOutputPagination3() {
        $_GET['commentPage'] = 2;
        ob_start();
        XG_PaginationHelper::outputPagination(40, 25, false, '#', 'commentPage');
        $actual = trim(ob_get_contents());
        ob_end_clean();
        $actual = preg_replace('@\s@', '', $actual);
        $expected = preg_replace('@\s@', '', '<ul class="pagination easyclear"><li><a href="#">1</a></li><li class="this"><span>2</span></li></ul>');
        $this->assertEqual($expected, $actual);
    }

    public function testOutputPagination4() {
        ob_start();
        XG_PaginationHelper::outputPagination(40, 25, false, 'http://example.org', null, false);
        $actual = trim(ob_get_contents());
        ob_end_clean();
        $actual = preg_replace('@\s@', '', $actual);
        $expected = preg_replace('@\s@', '', '<ul class="pagination easyclear"><li class="this"><span>1</span></li><li><a href="http://example.org/?page=2">2</a></li></ul>');
        $this->assertEqual($expected, $actual);
    }

    public function testOutputPagination5() {
        ob_start();
        XG_PaginationHelper::outputPagination(40, 25, false, 'http://example.org', null, true);
        $actual = trim(ob_get_contents());
        ob_end_clean();
        $actual = preg_replace('@\s@', '', $actual);
        $expected = preg_replace('@\s@', '', '<li class="this"><span>1</span></li><li><a href="http://example.org/?page=2">2</a></li>');
        $this->assertEqual($expected, $actual);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
