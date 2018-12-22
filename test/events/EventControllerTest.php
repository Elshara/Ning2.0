<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('events')->includeFileOnce('/controllers/EventController.php');

class EventControllerTest extends UnitTestCase {

    public function setUp() {
        $this->controller = new TestEventController();
        $_POST = array();
    }

    private function doTestCheckForm($name, $maxLength) {
        $_POST[$name] = str_repeat('a', $maxLength);
        $this->controller->_checkForm();
        $this->pass();
        try {
            $_POST[$name] = str_repeat('a', $maxLength + 1);
            $this->controller->_checkForm();
            $this->fail();
        } catch(Exception $e) {
            $this->pass();
        }
    }

    public function testCheckForm1() {
        $this->doTestCheckForm('title', 200);
    }

    public function testCheckForm2() {
        $this->doTestCheckForm('type', 200);
    }

    public function testCheckForm3() {
        $_POST['description'] = str_repeat('a', 4000);
        $errors = $this->controller->_checkForm();
        $this->assertFalse($errors['description']);
    }

    public function testCheckForm4() {
        $_POST['description'] = str_repeat('a', 4001);
        $errors = $this->controller->_checkForm();
        $this->assertTrue($errors['description']);
    }

    public function testCheckForm5() {
        $this->doTestCheckForm('location', 255);
    }

    public function testCheckForm6() {
        $this->doTestCheckForm('street', 255);
    }

    public function testCheckForm7() {
        $this->doTestCheckForm('city', 255);
    }

    public function testCheckForm8() {
        $this->doTestCheckForm('website', 500);
    }

    public function testCheckForm9() {
        $this->doTestCheckForm('contact', 255);
    }

    public function testCheckForm10() {
        $this->doTestCheckForm('organizedBy', 255);
    }

}

class TestEventController extends Events_EventController {

    public function __construct() {
        parent::__construct(W_Cache::getWidget('events'));
    }

    public function _checkForm() {
        return parent::_checkForm();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
