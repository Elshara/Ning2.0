<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class XG_GroupHelper4Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        unset($_GET['groupId']);
    }

    // @TODO: get rid of the test - as it is repeated in BazelGroupTest
    public function testIsGroupAdmin() {
        $users = XG_TestHelper::getTwoUsers();
        $current = $users['current'];
        $other = $users['other'];
        $this->assertNotNull($other, 'Not enough members on this network to have an admin and a member');
        $this->assertFalse(XG_GroupHelper::isGroupAdmin());
        $this->assertFalse(XG_GroupHelper::isGroupAdmin($current));
        $this->assertFalse(XG_GroupHelper::isGroupAdmin($other));
        $group = Group::create('Food Lovers');
        $group->save();
        $_GET['groupId'] = $group->id;
        Group::setStatus($group, $current, 'admin');
        Group::setStatus($group, $other, 'member');
        $this->assertTrue(XG_GroupHelper::isGroupAdmin());
        $this->assertTrue(XG_GroupHelper::isGroupAdmin($current));
        $this->assertFalse(XG_GroupHelper::isGroupAdmin($other));
    }

    public function testCurrentGroup() {
        $this->assertNull(XG_GroupHelper::currentGroup());
        $group = Group::create('Food Lovers');
        $group->save();
        $_GET['groupId'] = $group->id;
        $this->assertEqual('Food Lovers', XG_GroupHelper::currentGroup()->title);
    }

    public function testAfterSave() {
        $group = Group::create('Food Lovers');
        $group->save();
        Group::setStatus($group, XN_Profile::current()->screenName, 'admin');
        $this->assertTrue(Group::userIsMember($group, XN_Profile::current()->screenName));
        $this->assertTrue(Group::userIsAdmin($group, XN_Profile::current()->screenName));
        Group::setStatus($group, XN_Profile::current()->screenName, 'banned');
        $this->assertFalse(Group::userIsMember($group, XN_Profile::current()->screenName));
        $this->assertFalse(Group::userIsAdmin($group, XN_Profile::current()->screenName));
        $_GET['groupId'] = $group->id;
        $topic = Topic::create('Lasagna', 'test');
        $topic->save();
        $this->assertFalse(Group::userIsMember($group, XN_Profile::current()->screenName));
        $this->assertFalse(Group::userIsAdmin($group, XN_Profile::current()->screenName));
        Group::setStatus($group, XN_Profile::current()->screenName, 'nonmember');
        $_GET['groupId'] = $group->id;
        $topic = Topic::create('Lasagna', 'test');
        $topic->save();
        $this->assertFalse(Group::userIsMember($group, XN_Profile::current()->screenName));
        $this->assertFalse(Group::userIsAdmin($group, XN_Profile::current()->screenName));
    }

    public function testGroupEnabledWidgetInstanceNames() {
        // Widgets containing XG_GroupEnabledControllers should be on the list of testGroupEnabledWidgetInstanceNames (BAZ-6856) [Jon Aquino 2008-05-18]
        foreach ($this->widgetsWithGroupEnabledControllers() as $dir) {
            if ($dir == 'groups') { continue; }
            $this->assertTrue(in_array($dir, XG_GroupHelper::groupEnabledWidgetInstanceNames()), $dir);
        }
    }

    public function testGroupEnabledControllers() {
        // Widgets used in XG_Layout_groups should have XG_GroupEnabledControllers (BAZ-6856) [Jon Aquino 2008-05-18]
        $contents = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/widgets/groups/lib/XG_Layout_groups.php');
        $contents = str_replace('widgetName="main" action="sidebar"', '', $contents);
        preg_match_all('@widgetName=.([^"\']+)@ui', $contents, $matches);
        foreach ($matches[1] as $dir) {
            $this->assertTrue(in_array($dir, $this->widgetsWithGroupEnabledControllers()), $dir);
        }
    }

    private function widgetsWithGroupEnabledControllers() {
        static $widgetsWithGroupEnabledControllers = array();
        if ($widgetsWithGroupEnabledControllers) { return $widgetsWithGroupEnabledControllers; }
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/widgets', '*.php') as $file) {
            if (! preg_match('@/widgets/([^/]+)/controllers@u', $file, $matches)) { continue; }
            $dir = $matches[1];
            $contents = file_get_contents($file);
            if (mb_strpos($contents, 'XG_GroupEnabledController') === false) { continue; }
            $widgetsWithGroupEnabledControllers[] = $dir;
        }
        return $widgetsWithGroupEnabledControllers;
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
