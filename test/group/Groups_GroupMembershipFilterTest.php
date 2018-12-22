<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/groups/lib/helpers/Groups_GroupMembershipFilter.php');

class Groups_GroupMembershipFilterTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('groups');
    }

    public function testGet() {
        $this->assertEqual(0, count(Groups_GroupMembershipFilter::$nameToFilterMap));
        Groups_GroupMembershipFilter::get('mostActive');
        $this->assertEqual(1, count(Groups_GroupMembershipFilter::$nameToFilterMap));
    }

    public function testMostActive() {
        if (! User::load('JonathanAquino')) { return; }
        $group = Group::create('Pizza Fans');
        $group->save();

        // Test cache invalidation [Jon Aquino 2007-04-20]
        $this->assertEqual('', implode(', ', $this->usernames('mostActive', $group)));

        XG_TestHelper::setCurrentWidget('forum');
        $_GET['groupId'] = $group->id;
        Group::setStatus($group, 'JonathanAquino', 'member');
        Group::setStatus($group, XN_Profile::current()->screenName, 'member');
        $pepperoni = Topic::create('Pepperoni Pizza', 'yum');
        $pepperoni->save();
        $this->assertEqual(XN_Profile::current()->screenName . ', JonathanAquino', implode(', ', $this->usernames('mostActive', $group)));

        GroupMembership::loadOrCreate($group, 'JonathanAquino')->my->activityCount = 2;
        GroupMembership::loadOrCreate($group, 'JonathanAquino')->save();
        $this->assertEqual('JonathanAquino, ' . XN_Profile::current()->screenName, implode(', ', $this->usernames('mostActive', $group)));

        $hawaiian = Topic::create('Hawaiian Pizza', 'yum');
        $hawaiian->save();
        $vegetarian = Topic::create('Vegetarian Pizza', 'yum');
        $vegetarian->save();
        $this->assertEqual(XN_Profile::current()->screenName . ', JonathanAquino', implode(', ', $this->usernames('mostActive', $group)));

        XN_Content::delete($hawaiian);
        XN_Content::delete($vegetarian);
        $this->assertEqual('JonathanAquino, ' . XN_Profile::current()->screenName, implode(', ', $this->usernames('mostActive', $group)));

        Group::setStatus($group, 'JonathanAquino', 'banned');
        $this->assertEqual(XN_Profile::current()->screenName, implode(', ', $this->usernames('mostActive', $group)));
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
