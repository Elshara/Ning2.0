<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/groups/lib/helpers/Groups_GroupMembershipFilter.php');

class Groups_GroupMembershipFilter2Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('groups');
    }

    public function testBanned() {
        $group = Group::create('Pizza Fans');
        $group->save();
        XG_TestHelper::setCurrentWidget('forum');
        $_GET['groupId'] = $group->id;
        Group::setStatus($group, 'JonathanAquino', 'nonmember');
        Group::setStatus($group, XN_Profile::current()->screenName, 'banned');
        $this->assertEqual(XN_Profile::current()->screenName, implode(', ', $this->usernames('banned', $group)));
    }

    public function testMostRecent1() {
        $group = Group::create('Pizza Fans');
        $group->save();
        XG_TestHelper::setCurrentWidget('forum');
        $_GET['groupId'] = $group->id;
        Group::setStatus($group, 'JonathanAquino', 'nonmember');
        Group::setStatus($group, XN_Profile::current()->screenName, 'nonmember');
        Group::setStatus($group, 'JonathanAquino', 'member');
        sleep(1);
        Group::setStatus($group, XN_Profile::current()->screenName, 'member');
        $this->assertEqual(XN_Profile::current()->screenName . ', JonathanAquino', implode(', ', $this->usernames('mostRecent', $group)));
    }

    private function usernames($filterName, $group) {
        $usernames = array();
        foreach (Groups_GroupMembershipFilter::get($filterName)->execute(XG_Query::create('Content')->end(5), $group->id) as $groupMembership) {
            $usernames[] = $groupMembership->my->username;
        }
        return $usernames;
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
