<?php

require_once (empty($_SERVER['DOCUMENT_ROOT']) ? '.' : $_SERVER['DOCUMENT_ROOT']) . '/test/TestGroupRunner.php';

class CmdlineTestGroupRunner extends TestGroupRunner {
    public function __construct($dir = ".", $files = null) {
    	$this->dir = $dir;
        if (is_null($files)) {
            $files = self::glob_files($dir.'/*Test.php', true);
            $files = array_merge($files, self::glob_files($dir.'/*/*Test.php', true));
        }
        foreach ($files as $file) {
            if (strpos(basename($file), 'Abstract') !== false) { continue; }
            $this->files[] = preg_replace('@^./@','',$file);
        }
    }

    public function run() {
    	$testname = (count($this->files) <= 3) ? ('Specific tests: '.join(',',$this->files).'...') : ('All Cmdline Tests in '.$this->dir.'...');
		$grouptest = &new GroupTest($testname);
    	foreach ($this->files as $file) {
//    		print $file."\n";
			$grouptest->addTestFile($file);
    	}
		$grouptest->run(new TextReporter());
    }
}

