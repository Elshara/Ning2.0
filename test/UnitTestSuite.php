<?php

require_once dirname(__FILE__) . '/WebTestGroupRunner.php';
require_once dirname(__FILE__) . '/test_utils/BazelTestCaseLocator.php';
require_once dirname(__FILE__) . '/test_header.php';
require_once SIMPLETEST_PATH . '/collector.php';
require_once SIMPLETEST_PATH . '/reporter.php';

// @todo change this to something more generic
define('CMDLINE_TESTING', true);

/**
 * This should not be doing this...  This code needs to be refactored to use
 * the GroupTest/TestSuite code.
 */
class BazelUnitTestRunner extends WebTestGroupRunner
{
    protected $files = array();
    private $_type = '';
    
    public function __construct() {
        $this->_type = isset($_GET['type']) ? $_GET['type'] : 'unittests';
        $this->registerAllUnitTests();
    }
    
    public function registerAllUnitTests() {
        switch ($this->_type) {
            case 'unittests' :
                $locator = new BazelUnitTestCaseLocator();
                break;

            case 'codingstandards' :
                $locator = new BazelCleanUpTestCaseLocator();
                break;

            default :
                throw new Exception('Unknown test type: ' . htmlentities($this->_type));
                break;
        }
        $this->files = $locator->getArray();
    }
}

/**
 * A traditional SimpleTest group test.  This can not currently finish because
 * it takes too long to run.  Use {@link BazelUnitTestRunner} instead.
 *
 * @todo Refactor to TestSuite once SimpleTest is upgraded
 */
class BazelUnitTestSuite extends GroupTest
{
    protected $_pathsWithUnitTests = array(
        'activity',
        'admin',
        'events',
        'feed',
        'forum',
        'group',
        'html',
        'main',
        'music',
        'notes',
        'opensocial',
        'page',
        'photo',
        'profiles',
        'video',
    );
    
    public function __construct() {
        parent::GroupTest('Bazel Test Cases');
        $this->registerAllUnitTests();
    }
    
    private function registerAllUnitTests() {
        foreach ($this->_pathsWithUnitTests as $pathWithUnitTests) {
            $this->collect($pathWithUnitTests);
        }
    }
    
    public function collect($path) {
        parent::collect(
            dirname(__FILE__) . "/{$path}/",
            new SimplePatternCollector('/.*Test.php$/')
        );
    }
}

$test = new BazelUnitTestRunner();
$test->run(new HtmlReporter());
