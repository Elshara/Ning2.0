<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_FeatureHelper.php');

class Index_FeatureHelperTest extends UnitTestCase {

    public function setUp() {
        XN_Event::listen('test/feature/add/after', array($this, 'featureAdded'));
        XN_Event::listen('test/feature/remove/after', array($this, 'featureRemoved'));
        $this->featuresAdded = array();
        $this->featuresRemoved = array();
    }

    public function testFireEvents1() {
        Index_FeatureHelper::fireEvents(array('a', 'b', 'c', 'd'), array('z', 'b', 'c', 'e'), 'test/feature/add/after', 'test/feature/remove/after');
        $this->assertEqual(array('z', 'e'), $this->featuresAdded);
        $this->assertEqual(array('a', 'd'), $this->featuresRemoved);
    }

    public function testFireEvents2() {
        Index_FeatureHelper::fireEvents(array(), array(), 'test/feature/add/after', 'test/feature/remove/after');
        $this->assertEqual(array(), $this->featuresAdded);
        $this->assertEqual(array(), $this->featuresRemoved);
    }

    public function testFireEvents3() {
        Index_FeatureHelper::fireEvents(array('x'), array('x'), 'test/feature/add/after', 'test/feature/remove/after');
        $this->assertEqual(array(), $this->featuresAdded);
        $this->assertEqual(array(), $this->featuresRemoved);
    }

    var $featuresAdded = array();

    var $featuresRemoved = array();

    public function featureAdded($widgetName) {
        $this->featuresAdded[] = $widgetName;
    }

    public function featureRemoved($widgetName) {
        $this->featuresRemoved[] = $widgetName;
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
