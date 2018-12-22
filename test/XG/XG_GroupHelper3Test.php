<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class XG_GroupHelper3Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        unset($_GET['groupId']);
    }

    public function testAddGroupFilter() {
        $pastaGroup = Group::create('pasta');
        $pastaGroup->save();
        $seafoodGroup = Group::create('seafood');
        $seafoodGroup->save();
        $vegetableGroup = Group::create('vegetables');
        $vegetableGroup->save();
        $_GET['groupId'] = $pastaGroup->id;
        $topic = Topic::create('Lasagna', 'test');
        $topic->save();
        unset($_GET['groupId']);
        $topic = Topic::create('Bacon Double Cheeseburger', 'test');
        $topic->save();
        $_GET['groupId'] = $seafoodGroup->id;
        $topic = Topic::create('Prawns', 'test');
        $topic->save();
        unset($_GET['groupId']);
        $this->assertEqual('Bacon Double Cheeseburger', XG_GroupHelper::addGroupFilter(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Topic')->filter('my.test', '=', 'Y'))->uniqueResult()->title);
        $_GET['groupId'] = $pastaGroup->id;
        $this->assertEqual('Lasagna', XG_GroupHelper::addGroupFilter(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Topic')->filter('my.test', '=', 'Y'))->uniqueResult()->title);
        $_GET['groupId'] = $seafoodGroup->id;
        $this->assertEqual('Prawns', XG_GroupHelper::addGroupFilter(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Topic')->filter('my.test', '=', 'Y'))->uniqueResult()->title);
        $_GET['groupId'] = $vegetableGroup->id;
        $this->assertEqual(0, count(XG_GroupHelper::addGroupFilter(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Topic')->filter('my.test', '=', 'Y'))->execute()));
    }

    public function testGroupIsPrivate() {
        $this->assertFalse(XG_GroupHelper::groupIsPrivate());
        $group = Group::create('Food Lovers');
        $group->save();
        $_GET['groupId'] = $group->id;
        $this->assertFalse(XG_GroupHelper::groupIsPrivate());
        $group->my->groupPrivacy = 'private';
        $group->save();
        $this->assertTrue(XG_GroupHelper::groupIsPrivate());
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
