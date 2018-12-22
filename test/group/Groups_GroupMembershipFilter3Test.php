<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/groups/lib/helpers/Groups_GroupMembershipFilter.php');

class Groups_GroupMembershipFilter3Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('groups');
    }

    public function testMostRecent2() {
        $group = Group::create('Pizza Fans');
        $group->save();
        XG_TestHelper::setCurrentWidget('forum');
        $_GET['groupId'] = $group->id;
        Group::setStatus($group, 'JonathanAquino', 'nonmember');
        Group::setStatus($group, XN_Profile::current()->screenName, 'nonmember');
        Group::setStatus($group, XN_Profile::current()->screenName, 'member');
        sleep(1);
        Group::setStatus($group, 'JonathanAquino', 'member');
        $this->assertEqual('JonathanAquino, ' . XN_Profile::current()->screenName, implode(', ', $this->usernames('mostRecent', $group)));
    }

    public function testAdmin() {
        $group = Group::create('Pizza Fans');
        $group->save();
        Group::setStatus($group, 'JonathanAquino', 'admin');
        Group::setStatus($group, XN_Profile::current()->screenName, 'member');
        $admins = Groups_GroupMembershipFilter::get('admin')->profiles(XG_Query::create('Content'), $group->id);
        $this->assertEqual(1, count($admins));
        $this->assertEqual('JonathanAquino', reset($admins)->screenName);
    }

    private function usernames($filterName, $group) {
        $usernames = array();
        foreach (Groups_GroupMembershipFilter::get($filterName)->execute(XG_Query::create('Content')->end(5), $group->id) as $groupMembership) {
            $usernames[] = $groupMembership->my->username;
        }
        return $usernames;
    }

    public function testNeedsInvalidationKeys() {
        $this->assertFalse(TestFilter::needsInvalidationKeys(XN_Query::create('Content')));
        $this->assertTrue(TestFilter::needsInvalidationKeys(XG_Query::create('Content')));
        $this->assertFalse(TestFilter::needsInvalidationKeys(XG_Query::create('Content')->addCaching(XG_Cache::key('type', 'Group'))));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

abstract class TestFilter extends Groups_GroupMembershipFilter {
    public static function needsInvalidationKeys($query) {
        return parent::needsInvalidationKeys($query);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
