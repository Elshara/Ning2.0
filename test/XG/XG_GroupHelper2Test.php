<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class XG_GroupHelper2Test extends UnitTestCase {
    
    //TODO There are no assertions in these tests.  Can we add some?

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        unset($_GET['groupId']);
    }

    public function testCheckCurrentUserCanAccess2() {
        $group = Group::create('Food Lovers');
        $group->save();
        $food1 = XN_Content::create('Food', 'Lasagna');
        $food1->my->groupId = $group->id;
        $food1->save();
        $food2 = XN_Content::create('Food', 'Lasagna');
        $food2->my->groupId = $group->id;
        $food2->save();
        XG_Cache::content($food1->id);
        XG_Cache::content(array($food1->id, $food2->id));
    }

    public function testCheckCurrentUserCanAccess3() {
        $group = Group::create('Food Lovers');
        $group->my->groupPrivacy = 'private';
        $group->save(); 
        Group::setStatus($group, XN_Profile::current()->screenName, 'member');
        $_GET['groupId'] = $group->id;
        $topic1 = Topic::create('Lasagna', 'test');
        $topic1->save();
        $topic2 = Topic::create('Lasagna', 'test');
        $topic2->save();
        Group::setStatus($group, XN_Profile::current()->screenName, 'banned');
        XG_Cache::content($topic1->id);
        XG_Cache::content(array($topic1->id, $topic2->id));
        Group::setStatus($group, XN_Profile::current()->screenName, 'member');
    }

    public function testCheckCurrentUserCanAccessGroup() {
        XG_GroupHelper::checkCurrentUserCanAccessGroup();
        $_GET['groupId'] = '';
        XG_GroupHelper::checkCurrentUserCanAccessGroup();
        $group = Group::create('Food Lovers');
        $group->my->groupPrivacy = 'private';
        $group->save();
        $_GET['groupId'] = $group->id;
        XG_GroupHelper::checkCurrentUserCanAccessGroup();
        Group::setStatus($group, XN_Profile::current()->screenName, 'member');
        $_GET['groupId'] = $group->id;
        XG_GroupHelper::checkCurrentUserCanAccessGroup();
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
