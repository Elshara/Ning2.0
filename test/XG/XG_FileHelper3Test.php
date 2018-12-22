<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_FileHelper.php');

class XG_FileHelper3Test extends UnitTestCase {

    protected $baseDir;

    public function setUp() {
        $this->baseDir = $_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile/deltree-test';
        mkdir($this->baseDir, 0777, true);
    }

    public function tearDown() {
        XG_FileHelper::deltree($this->baseDir);
        $this->assertFalse(file_exists($this->baseDir));
    }

    public function testDeltree() {
        $this->assertTrue(file_exists($this->baseDir));
        $this->assertFalse(file_exists($this->baseDir . '/Z.txt'));
        $this->assertFalse(file_exists($this->baseDir . '/a'));
        $this->assertFalse(file_exists($this->baseDir . '/a/A.txt'));
        $this->assertFalse(file_exists($this->baseDir . '/a/b'));
        $this->assertFalse(file_exists($this->baseDir . '/a/b/B.txt'));
        mkdir($this->baseDir . '/a/b', 0777, true);
        touch($this->baseDir . '/Z.txt');
        touch($this->baseDir . '/a/A.txt');
        touch($this->baseDir . '/a/b/B.txt');
        $this->assertTrue(file_exists($this->baseDir));
        $this->assertTrue(file_exists($this->baseDir . '/Z.txt'));
        $this->assertTrue(file_exists($this->baseDir . '/a'));
        $this->assertTrue(file_exists($this->baseDir . '/a/A.txt'));
        $this->assertTrue(file_exists($this->baseDir . '/a/b'));
        $this->assertTrue(file_exists($this->baseDir . '/a/b/B.txt'));
        XG_FileHelper::deltree($this->baseDir . '/a');
        $this->assertTrue(file_exists($this->baseDir));
        $this->assertTrue(file_exists($this->baseDir . '/Z.txt'));
        $this->assertFalse(file_exists($this->baseDir . '/a'));
        $this->assertFalse(file_exists($this->baseDir . '/a/A.txt'));
        $this->assertFalse(file_exists($this->baseDir . '/a/b'));
        $this->assertFalse(file_exists($this->baseDir . '/a/b/B.txt'));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
