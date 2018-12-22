<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_FaultTolerantTask.php');

class XG_FaultTolerantTaskTest extends UnitTestCase {

    private static $onErrorArgs;

    public function setUp() {
        self::$onErrorArgs = null;
    }

    private function extractMetadata($operations) {
        $metadata = array();
        foreach ($operations as $operation) {
            $metadata[] = $operation->metadata[0];
        }
        return implode(',', $metadata);
    }

    public function testNoTasks() {
        $task = new XG_FaultTolerantTask(array('XG_FaultTolerantTaskTest', 'onError'), null);
        $task->execute(30);
        $this->assertNull(self::$onErrorArgs);
    }

    public function testNoErrors() {
        $task = new XG_FaultTolerantTask(array('XG_FaultTolerantTaskTest', 'onError'), null);
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(1));
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(2));
        $task->execute(30);
        $this->assertNull(self::$onErrorArgs);
    }

    public function testNegativeTimeout() {
        $task = new XG_FaultTolerantTask(array('XG_FaultTolerantTaskTest', 'onError'));
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(1));
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(2));
        $task->execute(-5);
        $this->assertEqual('1,2', $this->extractMetadata(self::$onErrorArgs));
    }

    public function testZeroTimeout() {
        $task = new XG_FaultTolerantTask(array('XG_FaultTolerantTaskTest', 'onError'));
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(1));
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(2));
        $task->execute(0);
        $this->assertEqual('1,2', $this->extractMetadata(self::$onErrorArgs));
    }

    public function testException() {
        $task = new XG_FaultTolerantTask(array('XG_FaultTolerantTaskTest', 'onError'));
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(1));
        $task->add(array('XG_FaultTolerantTaskTest', 'throwException'), array(), array(2));
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(3));
        $task->add(array('XG_FaultTolerantTaskTest', 'throwException'), array(), array(4));
        $task->execute(30);
        $this->assertEqual('2,4', $this->extractMetadata(self::$onErrorArgs));
    }

    public function testUserError() {
        $task = new XG_FaultTolerantTask(array('XG_FaultTolerantTaskTest', 'onError'));
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(1));
        $task->add(array('XG_FaultTolerantTaskTest', 'triggerUserError'), array(), array(2));
        $task->add(array('XG_FaultTolerantTaskTest', 'doNothing'), array(), array(3));
        $task->add(array('XG_FaultTolerantTaskTest', 'triggerUserError'), array(), array(4));
        $task->execute(30);
        $this->assertEqual('2,4', $this->extractMetadata(self::$onErrorArgs));
    }

    public function testFatalError() {
        $this->assertEqual('2,4,5', file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/test/XG_FaultTolerantTaskTestScript1.php'));
    }

    public function testTimeout() {
        $this->assertEqual('1,5end', file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/test/XG_FaultTolerantTaskTestScript2.php'));
    }

    public function testExtractMetadata() {
        $this->assertEqual('1,2,3', implode(',', XG_FaultTolerantTask::extractMetadata(array(
                new XG_FaultTolerantOperation(array(), array(), '1'),
                new XG_FaultTolerantOperation(array(), array(), '2'),
                new XG_FaultTolerantOperation(array(), array(), '3')))));
    }

    public static function onError($args) {
        self::$onErrorArgs = array_values($args);
    }

    public static function doNothing() {
    }

    public static function throwException() {
        throw new Exception('Test exception');
    }

    public static function triggerUserError() {
        trigger_error('Test error', E_USER_ERROR);
    }

    public static function triggerWarning() {
        simplexml_load_string('foo');
    }

    public static function triggerParseError() {
        eval('foo');
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
