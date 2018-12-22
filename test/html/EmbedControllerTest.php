<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/html/controllers/EmbedController.php');
Mock::generate('W_Widget');

class EmbedControllerTest extends UnitTestCase {

    public function testGetMaxLength1() {
        $widget = new MockW_Widget();
        $widget->config = array();
        $widget->expectOnce('saveConfig', array());
        $this->assertEqual(500000, TestEmbedController::getMaxLength($widget));
        $this->assertEqual(500000, $widget->config['maxLength']);
    }

    public function testGetMaxLength2() {
        $widget = new MockW_Widget();
        $widget->config = array('maxLength' => 1000);
        $widget->expectNever('saveConfig', array());
        $this->assertEqual(1000, TestEmbedController::getMaxLength($widget));
        $this->assertEqual(1000, $widget->config['maxLength']);
    }

    public function testGetMaxLength3() {
        $widget = new MockW_Widget();
        $widget->config = array('maxLength' => 0);
        $widget->expectNever('saveConfig', array());
        $this->assertEqual(0, TestEmbedController::getMaxLength($widget));
        $this->assertEqual(0, $widget->config['maxLength']);
    }

    public function testLimitConsecutiveLineBreaks() {
        $this->assertEqual('a<br><br><br>b', TestEmbedController::limitConsecutiveLineBreaks('a<br><br><br>b', 3));
        $this->assertEqual('a<br><br><br>b', TestEmbedController::limitConsecutiveLineBreaks('a<br><br><br><br>b', 3));
        $this->assertEqual('a<BR><br/><br />b', TestEmbedController::limitConsecutiveLineBreaks('a<BR><br/><br />b', 3));
        $this->assertEqual('a<BR><br/><br />b', TestEmbedController::limitConsecutiveLineBreaks('a<BR><br/><br /><BR>b', 3));
        $this->assertEqual('A<br><br><br>B<br><br>C<br><br><br>', TestEmbedController::limitConsecutiveLineBreaks('A<br><br><br><br>B<br><br>C<br><br><br><br>', 3));
        $this->assertEqual('a<br>
<br>
<br>
b', TestEmbedController::limitConsecutiveLineBreaks('a<br>
<br>
<br>
b', 3));
        $this->assertEqual('a<br>
<br>
<br>
b', TestEmbedController::limitConsecutiveLineBreaks('a<br>
<br>
<br>
<br>
b', 3));
        $this->assertEqual('a<BR>
<br/>
<br />
b', TestEmbedController::limitConsecutiveLineBreaks('a<BR>
<br/>
<br />
b', 3));
        $this->assertEqual('a<BR>
<br/>
<br />
b', TestEmbedController::limitConsecutiveLineBreaks('a<BR>
<br/>
<br />
<BR>
b', 3));
        $this->assertEqual('A<br>
<br>
<br>
B<br>
<br>
C<br>
<br>
<br>
', TestEmbedController::limitConsecutiveLineBreaks('A<br>
<br>
<br>
<br>
B<br>
<br>
C<br>
<br>
<br>
<br>', 3));
    }

}

class TestEmbedController extends Html_EmbedController {
    public static function getMaxLength($widget) {
        return parent::getMaxLength($widget);
    }
    public static function limitConsecutiveLineBreaks($html, $n) {
        return parent::limitConsecutiveLineBreaks($html, $n);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
