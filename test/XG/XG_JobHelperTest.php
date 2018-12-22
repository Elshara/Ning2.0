<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_JobHelper.php');

class XG_JobHelperTest extends UnitTestCase {

    public function setUp() {
        $this->jobHelper = new MockXG_JobHelper();
        $this->jobHelper->setReturnValue('generateSecretKey', 'A1B2C3');
        TestJobHelper::setInstance($this->jobHelper);
    }

    public function testGetSecretKeyProper1() {
        $mainWidget = new MockW_Widget();
        $mainWidget->privateConfig = array();
        $mainWidget->expectOnce('saveConfig');
        $this->jobHelper->expectOnce('generateSecretKey');
        $this->assertEqual('A1B2C3', TestJobHelper::getSecretKeyProper($mainWidget));
        $this->assertEqual('A1B2C3', $mainWidget->privateConfig['asyncJobKey']);
        $this->assertNull($mainWidget->privateConfig['asyncJobKeyGracePeriodStart']);
    }

    public function testGetSecretKeyProper2() {
        $mainWidget = new MockW_Widget();
        $mainWidget->privateConfig = array('asyncJobKey' => '101010', 'asyncJobKeyGracePeriodStart' => '1981-09-05T00:00:00+00:00');
        $mainWidget->expectNever('saveConfig');
        $this->jobHelper->expectNever('generateSecretKey');
        $this->assertEqual('101010', TestJobHelper::getSecretKeyProper($mainWidget));
        $this->assertEqual('101010', $mainWidget->privateConfig['asyncJobKey']);
        $this->assertEqual('1981-09-05T00:00:00+00:00', $mainWidget->privateConfig['asyncJobKeyGracePeriodStart']);
    }

    public function testCheckSecretKeyProper1() {
        $this->doTestCheckSecretKeyProper('ZZZZZZ', '2008-04-16T00:41:42+00:00', false, array());
    }

    public function testCheckSecretKeyProper2() {
        $this->doTestCheckSecretKeyProper('ZZZZZZ', '2008-04-17T00:41:43+00:00', false, array('asyncJobKey' => 'A1B2C3', 'asyncJobKeyGracePeriodStart' => '2008-04-16T00:41:42+00:00'));
    }

    public function testCheckSecretKeyProper3() {
        $this->doTestCheckSecretKeyProper('ZZZZZZ', '2008-04-17T00:41:41+00:00', true, array('asyncJobKey' => 'A1B2C3', 'asyncJobKeyGracePeriodStart' => '2008-04-16T00:41:42+00:00'));
    }

    public function testCheckSecretKeyProper4() {
        $this->doTestCheckSecretKeyProper('A1B2C3', '2008-04-17T00:41:43+00:00', true, array('asyncJobKey' => 'A1B2C3', 'asyncJobKeyGracePeriodStart' => '2008-04-16T00:41:42+00:00'));
    }

    public function testCheckSecretKeyProper5() {
        $this->doTestCheckSecretKeyProper('ZZZZZZ', '2008-04-17T00:41:43+00:00', false, array('asyncJobKey' => 'A1B2C3', 'asyncJobKeyGracePeriodStart' => NULL));
    }

    public function testCheckSecretKeyProper6() {
        $this->doTestCheckSecretKeyProper('ZZZZZZ', '2008-04-17T00:41:43+00:00', false, array('asyncJobKey' => 'A1B2C3', 'asyncJobKeyGracePeriodStart' => ''));
    }

    private function doTestCheckSecretKeyProper($submittedKey, $date, $checkSecretKeyProper, $privateConfig) {
        $mainWidget = new MockW_Widget();
        $mainWidget->privateConfig = $privateConfig;
        $mainWidget->expectCallCount('saveConfig', $privateConfig['asyncJobKey'] ? 0 : 1);
        $this->jobHelper->expectCallCount('generateSecretKey', $privateConfig['asyncJobKey'] ? 0 : 1);
        $this->assertEqual($checkSecretKeyProper, TestJobHelper::checkSecretKeyProper($mainWidget, $submittedKey, strtotime($date)));
        $this->assertEqual('A1B2C3', $mainWidget->privateConfig['asyncJobKey']);
        $this->assertEqual($privateConfig['asyncJobKeyGracePeriodStart'], $mainWidget->privateConfig['asyncJobKeyGracePeriodStart']);
    }

}

class TestJobHelper extends XG_JobHelper {

    public static function getSecretKeyProper($mainWidget) {
        return parent::getSecretKeyProper($mainWidget, mt_rand());
    }

    public static function checkSecretKeyProper($config, $submittedKey, $time) {
        return parent::checkSecretKeyProper($config, $submittedKey, $time);
    }

    public static function setInstance($instance) {
        parent::$instance = $instance;
    }

    public function generateSecretKey() {
        return parent::generateSecretKey();
    }

}

Mock::generate('TestJobHelper', 'MockXG_JobHelper');
Mock::generate('W_Widget');

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
