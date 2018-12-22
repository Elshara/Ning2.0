<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/groups/controllers/SequencedjobController.php';
Mock::generate('XN_Query');
Mock::generate('stdClass', 'MockXN_Content', array('save'));
Mock::generate('stdClass', 'MockXN_AttributeContainer', array('set'));

class Groups_SequencedjobControllerTest extends UnitTestCase {

    private $controller;

    public function setUp() {
        $this->controller = new TestSequencedjobController(W_Cache::getWidget('main'));
    }

    private function createQuery($returnValue) {
        $query = new MockXN_Query();
        $query->expectAt(0, 'filter', array('owner'));
        $query->expectAt(1, 'filter', array('type', '=', 'Group'));
        $query->expectAt(2, 'filter', array('my.activityScore', '=', null));
        $query->expectNever('begin');
        $query->expectOnce('end', array(20));
        $query->expectOnce('execute', array());
        $query->setReturnValue('execute', $returnValue);
        return $query;
    }

    public function testInitializeActivityAttributes1() {
        $query = $this->createQuery(array());
        $this->controller->initializeActivityAttributes($query);
        $this->assertIdentical(false, $this->controller->getContinueJob());
    }

    public function testinitializeActivityAttributes2() {
        $group = new MockXN_Content();
        $group->id = '1:Group:2';
        $group->updatedDate = '2008-03-06T02:57:21.182Z';
        $group->expectOnce('save');
        $group->my = new MockXN_AttributeContainer();
        $group->my->expectOnce('set', array('lastActivityOn', '2008-03-06T02:57:21.182Z', 'date'));
        $query = $this->createQuery(array($group));
        $this->controller->initializeActivityAttributes($query);
        $this->assertIdentical(true, $this->controller->getContinueJob());
        $this->assertIdentical(0, $group->my->activityScore);
    }

}

class TestSequencedjobController extends Groups_SequencedjobController {
    public function getContinueJob() { return $this->continueJob; }
    public function initializeActivityAttributes($query) {
        parent::initializeActivityAttributes($query);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
