<?php

class TestGroupRunner {
    protected $files = array();
    
    public function glob_files($pattern = "./*Test.php", $cmdlineTests = false) {
    	$files = glob($pattern);
    	foreach ($files as $index => $file) {
    		if ( ($cmdlineTests && !preg_match("/.*CmdlineTest.*/",$file)) || (!$cmdlineTests && preg_match("/.*CmdlineTest.*/",$file)) )
    			unset($files[$index]);
    	}
    	return $files;
    }

    public function __construct($files = null) {
        if (is_null($files)) {
            $files = self::glob_files('./*Test.php');
            $files = array_merge($files, self::glob_files('./*/*Test.php'));
        }
        foreach ($files as $file) {
            if (strpos(basename($file), 'Abstract') !== false) { continue; }
            $this->files[] = preg_replace('@^./@','',$file);
        }
    }
}

