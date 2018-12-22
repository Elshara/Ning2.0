<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_FileHelper.php');

class XG_FileHelper2Test extends UnitTestCase {

    protected $baseDir;

    public function setUp() {
        $this->baseDir = $_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile/file-cleanup-test/' . md5(uniqid());
        mkdir($this->baseDir, 0777, true);
    }

    public function tearDown() {
        foreach (glob("{$this->baseDir}/*") as $f) {
            unlink($f);
        }
        rmdir($this->baseDir);
        rmdir($_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile/file-cleanup-test');
    }
    
    protected function createFiles($count, $sleep = 1) {
        $paths = array();
        foreach (range(1,$count) as $i) { 
            $path = "{$this->baseDir}/file$i.txt";
            file_put_contents($path,"file $i");
            $paths[] = $path;
            if ($sleep) { sleep($sleep); }
        }
        return $paths;
    }

    public function testFileCleanup() {
        $files = $this->createFiles(3, 0);
        $deleted = XG_FileHelper::fileCleanup("{$this->baseDir}/file*.txt");
        $this->assertEqual($deleted, $files);
        $this->assertEqual(glob("{$this->baseDir}/file*.txt"), array());
    }

    public function testFileCleanupArgs() {
        $files = $this->createFiles(3, 0);
        $deleted = XG_FileHelper::fileCleanup(array('glob' => "{$this->baseDir}/file*.txt"));
        $this->assertEqual($deleted, $files);
        $this->assertEqual(glob("{$this->baseDir}/file*.txt"), array());
    }

    public function testFileCleanupArgsRegex() {
        $regex = '@file1@';
        $files = $this->createFiles(3, 0);
        $deleted = XG_FileHelper::fileCleanup(array('glob' => "{$this->baseDir}/file*.txt",
                                                    'regex' => $regex));
        $expectedDeleted = array_values(preg_grep($regex, $files));
        $expectedPreserved = array_values(preg_grep($regex, $files, PREG_GREP_INVERT));
        $this->assertEqual(count($expectedDeleted), 1);
        $this->assertEqual(count($expectedPreserved), 2);
        $this->assertEqual($deleted, $expectedDeleted);
        $this->assertEqual(glob("{$this->baseDir}/file*.txt"), $expectedPreserved);

    }

    public function testFileCleanupMinCount() {
        $files = $this->createFiles(5);
        $deleted = XG_FileHelper::fileCleanup("{$this->baseDir}/file*.txt",
                                              array('min-count' => 2));
        // The last two (newest) should be kept
        $expectedPreserved = array_slice($files, -2);
        $this->assertEqual(count($expectedPreserved), 2);
        $expectedDeleted = array_values(array_diff($files, $expectedPreserved));
        // Make sure they're in the same order and reset keys
        sort($deleted);
        sort($expectedDeleted);
        $this->assertEqual(count($expectedDeleted), 3);
        $this->assertEqual($deleted, $expectedDeleted);
        $this->assertEqual(glob("{$this->baseDir}/file*.txt"), $expectedPreserved);
    }

    public function testFileCleanupMaxCount() {
        $this->createFiles(5);
        $deleted = XG_FileHelper::fileCleanup("{$this->baseDir}/file*.txt",
                                              array('max-count' => 3));
        $this->assertEqual($deleted, array("{$this->baseDir}/file2.txt",
                                           "{$this->baseDir}/file1.txt"));
        $this->assertEqual(glob("{$this->baseDir}/file*.txt"),
                           array("{$this->baseDir}/file3.txt",
                                 "{$this->baseDir}/file4.txt",
                                 "{$this->baseDir}/file5.txt"));
    }

    public function testFileCleanupMaxAge() {
        $files = $this->createFiles(5);
        $deleted = XG_FileHelper::fileCleanup("{$this->baseDir}/file*.txt",
                                              array('max-age' => 3));
        $remaining = glob("{$this->baseDir}/file*.txt");
        // The "now" timestamp could change slightly between the call to fileCleanup()
        // and when it's calculated internally, so just make sure the oldest two files
        // are deleted
        $mustBeDeleted = array_slice($files, 0, 2);
        foreach ($mustBeDeleted as $file) {
            $this->assertTrue(in_array($file, $deleted), "$file should be deleted");
            $this->assertFalse(in_array($file, $remaining), "$file should not be remaining");
        }
    }

    public function testFileCleanupMaxAgeMinMaxCount() {
        $files = $this->createFiles(5);
        $deleted = XG_FileHelper::fileCleanup("{$this->baseDir}/file*.txt",
                                              array('max-age' => 2,
                                                    'max-count' => 3,
                                                    'min-count' => 2));
        $remaining = glob("{$this->baseDir}/file*.txt");
        $expectDeleted = array_values(array_slice($files,0,3));
        $expectRemaining = array_values(array_slice($files,-2));
        sort($deleted); sort($expectDeleted);
        sort($remaining); sort($expectRemaining);
        $this->assertEqual($deleted, $expectDeleted);
        $this->assertEqual($remaining, $expectRemaining);
    }


}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
