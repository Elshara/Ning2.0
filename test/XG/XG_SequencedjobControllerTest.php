<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_SequencedjobController.php');
XG_App::includeFileOnce('/lib/XG_JobHelper.php');

class XG_SequencedjobControllerTest extends UnitTestCase {

    public function setUp() {
        $_POST[XG_JobHelper::KEY] = TestJobHelper::getSecretKey();
    }

    public function test1() {
        $controller = new TestSequencedjobController(W_Cache::getWidget('main'));
        try {
            $controller->action('test1');
            $this->fail();
        } catch (Exception $e) {
            $this->assertPattern('@setContinueJob@', $e->getMessage());
        }
        $this->assertEqual('HTTP/1.0 500 Internal Error', $controller->header);
        $this->assertNull($controller->startArgs);
    }

    public function test2() {
        $controller = new TestSequencedjobController(W_Cache::getWidget('main'));
        $controller->post = array('foo' => 'bar');
        $controller->action('test2');
        $this->assertEqual('HTTP/1.0 200 OK', $controller->header);
        $this->assertEqual('bar', $controller->output['foo']);
        $this->assertNull($controller->startArgs);
    }

    public function test3() {
        $controller = new TestSequencedjobController(W_Cache::getWidget('main'));
        $controller->allowChaining = false;
        try {
            $controller->action('test3');
            $this->fail();
        } catch (Exception $e) {
            $this->assertPattern('@Chaining not allowed@', $e->getMessage());
        }
        $this->assertEqual('HTTP/1.0 500 Internal Error', $controller->header);
        $this->assertNull($controller->startArgs);
    }

    public function test4() {
        $controller = new TestSequencedjobController(W_Cache::getWidget('main'));
        $controller->post = array('foo' => 'bar');
        $controller->action('test4');
        $this->assertEqual('HTTP/1.0 200 OK', $controller->header);
        $this->assertEqual('bar', $controller->output['foo']);
        $this->assertEqual(array('spacecraft', 'initialize', array('foo' => 'bar')), $controller->startArgs);
    }

    public function test5() {
        $controller = new TestSequencedjobController(W_Cache::getWidget('main'));
        try {
            $controller->action('test5');
            $this->fail();
        } catch (Exception $e) {
            $this->assertPattern('@Blah@', $e->getMessage());
        }
        $this->assertEqual('HTTP/1.0 500 Internal Error', $controller->header);
        $this->assertNull($controller->startArgs);
    }

    public function test6() {
        $controller = new TestSequencedjobController(W_Cache::getWidget('main'));
        $controller->post = array('foo' => 'bar');
        $controller->action('test6');
        $this->assertEqual('HTTP/1.0 200 OK', $controller->header);
        $this->assertEqual(array('spacecraft', 'initialize', array('foo' => 'baz')), $controller->startArgs);
    }

    public function test7() {
        $controller = new TestSequencedjobController(W_Cache::getWidget('main'));
        $controller->post = array('_widget' => 'evil');
        try {
            $controller->action('test7');
            $this->fail();
        } catch (Exception $e) {
            $this->assertPattern('@_widget already exists@', $e->getMessage());
        }
        $this->assertEqual('HTTP/1.0 500 Internal Error', $controller->header);
        $this->assertNull($controller->startArgs);
    }

}

class TestJobHelper extends XG_JobHelper {
    public static function getSecretKey() {
        return parent::getSecretKey();
    }
}

class TestSequencedjobController extends XG_SequencedjobController {

    public $post = array();

    public $output = array();

    public $allowChaining = true;

    public $startArgs = null;

    public function action_test1() {
    }

    public function action_test2() {
        $this->output['foo'] = $this->foo;
        $this->setContinueJob(false);
    }

    public function action_test3() {
    }

    public function action_test4() {
        $this->output['foo'] = $this->foo;
        $this->setContinueJob(true);
    }

    public function action_test5() {
        throw new Exception('Blah');
    }

    public function action_test6() {
        $this->foo = 'baz';
        $this->setContinueJob(true);
    }

    public function action_test7() {
        $this->setContinueJob(true);
    }

    protected function header($string) {
        $this->header = $string;
    }

    protected function getPostVariables() {
        return $this->post;
    }

    protected function allowChaining() {
        return $this->allowChaining;
    }

    protected static function getRequestedRoute() {
        return array('widgetName' => 'spacecraft', 'controllerName' => 'foo', 'actionName' => 'initialize');
    }

    protected function start($widgetName, $actionName, $args) {
        $this->startArgs = array($widgetName, $actionName, $args);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
