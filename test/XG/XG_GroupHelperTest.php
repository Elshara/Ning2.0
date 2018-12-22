<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class XG_GroupHelperTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        unset($_GET['groupId']);
    }

    public function testBuildUrl() {
        $group = Group::create('Food Lovers');
        $group->my->url = 'foodlovers';
        $group->save();

        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/forum/topic/show?x=123', str_replace('/test/XG/XG_GroupHelperTest.php', '', XG_GroupHelper::buildUrl('forum', 'topic', 'show', array('x' => 123))));
        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/groups/comment/show?x=123', str_replace('/test/XG/XG_GroupHelperTest.php', '', XG_GroupHelper::buildUrl('groups', 'comment', 'show', array('x' => 123))));
        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/groups/group/edit?x=123', str_replace('/test/XG/XG_GroupHelperTest.php', '', XG_GroupHelper::buildUrl('groups', 'group', 'edit', array('x' => 123))));
        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/groups/group/show?x=123', str_replace('/test/XG/XG_GroupHelperTest.php', '', XG_GroupHelper::buildUrl('groups', 'group', 'show', array('x' => 123))));

        $_GET['groupId'] = $group->id;
        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/group/foodlovers/forum/topic/show', XG_GroupHelper::buildUrl('forum', 'topic', 'show', array('groupId' => $group->id)));
        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/group/foodlovers/comment/show', XG_GroupHelper::buildUrl('groups', 'comment', 'show', array('groupId' => $group->id)));
        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/group/foodlovers/edit', XG_GroupHelper::buildUrl('groups', 'group', 'edit', array('id' => $group->id)));
        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/group/foodlovers', XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $group->id)));
    }

    public function testCheckCurrentUserCanAccess() {
        XG_GroupHelper::checkCurrentUserCanAccess(null);
        $group = Group::create('Food Lovers');
        $group->save();
        $food = XN_Content::create('Food', 'Lasagna');
        $food->save();
        XG_GroupHelper::checkCurrentUserCanAccess($food);
        $food->my->groupId = $group->id;
        XG_GroupHelper::checkCurrentUserCanAccess($food);
        $group->my->groupPrivacy = 'private';
        $group->save();
        XG_GroupHelper::checkCurrentUserCanAccess($food);
        Group::setStatus($group, XN_Profile::current()->screenName, 'member');
        XG_GroupHelper::checkCurrentUserCanAccess($food);
        XN_Content::delete($group);
        XG_GroupHelper::checkCurrentUserCanAccess($food);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
