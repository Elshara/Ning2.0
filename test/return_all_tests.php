<?php
/**
 * This file outputs a JSON array of all of the test cases located in /test/
 *
 * It is meant to be called directly by a remote test runner (either CLI or
 * web based) that can take the results and make calls to the test files directly
 * and parse their results.
 *
 * @todo add filtering via _GET parameter
 *
 */
require_once dirname(__FILE__) . '/test_utils/BazelTestCaseLocator.php';

if (!isset($_GET['type'])) {
    $_GET['type'] = '';
}

switch ($_GET['type']) {
    case 'unittests':
        $locatorClass = 'BazelUnitTestCaseLocator';
        break;
    
    case 'codingstandards':
        $locatorClass = 'BazelCleanUpTestCaseLocator';
        break;
    
    default:
        $locatorClass = 'BazelTestCaseLocator';
        break;
}

$l = new $locatorClass();
$array = array();
foreach ($l->getArray() as $test) {
	$array[] = 'test/' . $test;
}
$j = json_encode($array);

echo $j;

