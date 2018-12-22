<?php
$shortopts  = "";
$shortopts .= "a:"; // app base (Optional - default: current-dir [.])
$shortopts .= "d:"; // test-directory to run the tests (Optional - default: current-dir [test])
$shortopts .= "t:"; // particular test to run (Optional - default: run all tests)
$shortopts .= "c:"; // path to core (Optional - default: [../../php/includes/XN/])
$shortopts .= "e:"; // path to devtestengine (Optional - default: [../devtestengine/])
$shortopts .= "h";  // print help message (no value)

$options = getopt($shortopts);
//var_dump($options);

$default["apphome"]       = ".";
$default["testdir"]       = "./test";
$default["coredir"]       = "../../php/includes/XN/";
$default["devtestengine"] = "../devtestengine/";
$default["testfile"]      = "*";

$apphome       = empty($options["a"]) ? $default["apphome"] : $options["a"];
$testdir       = empty($options["d"]) ? $default["testdir"] : $options["d"];
$coredir       = empty($options["c"]) ? $default["coredir"] : $options["c"];
$devtestengine = empty($options["e"]) ? $default["devtestengine"] : $options["e"];
$testfile      = empty($options["t"]) ? $default["testfile"] : $options["t"];

if (isset($options["h"])) {
	print<<<EOH
	\$ php AllCmdlineUnitTests [options]
"-a" : app base (Optional - default: current-dir [${default["apphome"]}])
"-d" : test-directory to run the tests (Optional - default: [${default["testdir"]}])
"-t" : particular test to run (Optional - default: run all tests [${default["testfile"]}])
"-c" : path to core (Optional - default: [${default["coredir"]}])
"-e" : path to devtestengine (Optional - default: [${default["devtestengine"]}])
"-h" : print this help message (no value)


EOH;
	exit;
}

define('NF_APP_BASE',          $apphome);
define('W_INCLUDE_PREFIX',     $coredir);
define('NF_ENGINE_BASE',       W_INCLUDE_PREFIX."WWF/");
define('DEVTESTENGINE_PREFIX', $devtestengine);

$_SERVER['DOCUMENT_ROOT'] = NF_APP_BASE;

(file_exists(NF_APP_BASE) && is_dir(NF_APP_BASE)) or exit("Apphome ".NF_APP_BASE." not found! Please execute command from app home directory");
(file_exists(W_INCLUDE_PREFIX) && is_dir(W_INCLUDE_PREFIX)) or exit("Coredir ".W_INCLUDE_PREFIX." not found!  Core-dir path is required.");
(file_exists(DEVTESTENGINE_PREFIX) && is_dir(DEVTESTENGINE_PREFIX)) or exit(DEVTESTENGINE_PREFIX." not found!  Cannot execute simpletest from commandline.");
(file_exists($testdir) && is_dir($testdir)) or exit("Testdir $testdir not found! Please provide a valid test directory");

define('CMDLINE_TESTING', true);

require_once NF_APP_BASE.'/test/CmdlineTestGroupRunner.php';
require_once DEVTESTENGINE_PREFIX.'/simpletest_1.0.1alpha3/unit_tester.php';
require_once DEVTESTENGINE_PREFIX.'/simpletest_1.0.1alpha3/reporter.php';

$testfilestorun = $testfile === "*" ? null : array($testfile);

$time1 = gettimeofday();

//print 'Running tests under '.$testdir.":\n".str_repeat('-',77)."\n";
print str_repeat('-',77)."\n";
$ctgr = new CmdlineTestGroupRunner($testdir, $testfilestorun);
$ctgr->run();

$time2 = gettimeofday();

$micro = 1000000;

$total = ($time2['sec']*$micro+$time2['usec']) - ($time1['sec']*$micro+$time1['usec']);

print str_repeat('-',77)."\nTotal time: ".(float)$total/$micro." seconds\n".str_repeat('-',77)."\n";
