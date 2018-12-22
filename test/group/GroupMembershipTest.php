<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class GroupMembershipTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('groups');
    }

    public function testIsMember() {
        $x = new GroupMembership();
        $this->assertEqual(4, count($x->status_choices));
        $groupMembership = W_Content::create('GroupMembership');
        $groupMembership->my->status = null;
        $this->assertFalse(GroupMembership::isMember($groupMembership));
        $groupMembership->my->status = 'nonmember';
        $this->assertFalse(GroupMembership::isMember($groupMembership));
        $groupMembership->my->status = 'member';
        $this->assertTrue(GroupMembership::isMember($groupMembership));
        $groupMembership->my->status = 'admin';
        $this->assertTrue(GroupMembership::isMember($groupMembership));
        $groupMembership->my->status = 'banned';
        $this->assertFalse(GroupMembership::isMember($groupMembership));
    }

    public function testLoad() {
        $group = Group::create('a');
        $group->save();
        Group::setStatus($group, 'Joe', 'banned');
        $this->assertNotNull(GroupMembership::loadOrCreate($group, 'Joe'));
    }

    public function testDateJoined() {
        $group = Group::create('a');
        $group->save();
        Group::setStatus($group, 'Joe', 'banned');
        $this->assertNull(GroupMembership::loadOrCreate($group, 'Joe')->my->dateJoined);
        Group::setStatus($group, 'Joe', 'member');
        $this->assertNotNull(GroupMembership::loadOrCreate($group, 'Joe')->my->dateJoined);
    }

    public function testAddGroupId() {
        $user = XN_Content::create('Test');
        $this->assertNull($user->my->xg_groups_groups);
        TestGroupMembership::addGroupId('a', $user);
        $this->assertEqual('a', $user->my->xg_groups_groups);
        TestGroupMembership::addGroupId('b', $user);
        $this->assertEqual('a b', $user->my->xg_groups_groups);
        TestGroupMembership::addGroupId('a', $user);
        $this->assertEqual('a b', $user->my->xg_groups_groups);
        TestGroupMembership::removeGroupId('a', $user);
        $this->assertEqual('b', $user->my->xg_groups_groups);
        TestGroupMembership::removeGroupId('b', $user);
        $this->assertEqual('', $user->my->xg_groups_groups);
    }

    public function testGroupIds() {
        $user = XN_Content::create('Test');
        $this->assertEqual(serialize(array()), serialize(Group::groupIds($user)));
        $user->my->xg_groups_groups = 'a b';
        $this->assertEqual(serialize(array('a', 'b')), serialize(Group::groupIds($user)));
    }

    public function testCreate() {
        $group = Group::create('TestGroup9e892839aa');
        $profile = XN_Profile::current();
        $membership = GroupMembership::create($group, $profile->screenName);
        $this->assertEqual('groups', $membership->my->mozzle);
        $this->assertTrue($membership->isPrivate);
        $this->assertEqual('N', $membership->my->welcomed);
        $this->assertEqual(XG_UserHelper::getFullName($profile), $membership->my->fullName);
    }

    public function testDenormalizeFullName() {
        $user = XG_TestHelper::createTestUser('TestUserjfdlsu93jflmmm');
        $this->assertNotNull($user);
        $username = $user->title;
        $group = Group::create('TestGroup');
        $group->save();
        for ($i = 0; $i < 60; $i++) {
            $x = GroupMembership::create($group, $username);
            $x->my->fullName = null;
            $x->save();
        }
        $this->assertEqual(10, GroupMembership::denormalizeFullName());
        $this->assertEqual(0, GroupMembership::denormalizeFullName());
    }

    public function testSetFullName() {
        $profile = XN_Profile::current();
        //TODO because setFullName permanently affects the my->fullName attribute of all the user's GroupMemberships we must set it back afterwards.
        // Would be better to replace this test with something that does not alter non-test objects, even temporarily.
        $originalFullName = XG_UserHelper::getFullName($profile);
        $username = $profile->screenName;
        $group = Group::create('TestGroup');
        $group->save();
        $name = 'Reinaldo Neyimiyah';
        $x = GroupMembership::create($group, $username);
        $x->save();
        GroupMembership::setFullName($username, $name);
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'GroupMembership');
        $query->filter('my->groupId', '=', $group->id);
        $query->filter('my->username', '=', $username);
        $results = $query->execute();
        foreach ($results as $membership) {
            $this->assertEqual($name, $membership->my->fullName);
        }
        GroupMembership::setFullName($username, $originalFullName);
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'GroupMembership');
        $query->filter('my->groupId', '=', $group->id);
        $query->filter('my->username', '=', $username);
        $results = $query->execute();
        foreach ($results as $membership) {
            $this->assertEqual($originalFullName, $membership->my->fullName);
        }
    }

    public function testCreateJobForSetFullName() {
        $job = GroupMembership::createJobForSetFullName('TestUser999', 'Wadsworth the Butler');
        $this->assertEqual(XN_Job::CREATED, $job->status);
        $this->assertEqual(1, count($job->tasks));
        $this->assertNull($job->completionCallback);
        $this->assertEqual(XG_SecurityHelper::addCsrfToken(W_Cache::getWidget('groups')->buildUrl('group', 'setFullName')), $job->tasks[0]->executionCallback);
        $this->assertEqual('application/x-www-form-urlencoded', $job->tasks[0]->type);
        $this->assertEqual(array('screenName' => 'TestUser999', 'fullName' => 'Wadsworth the Butler'), $job->tasks[0]->content);
    }

    public function testCreateJobForDenormalizeFullName() {
        $job = GroupMembership::createJobForDenormalizeFullName('TestUser999', 'Wadsworth the Butler');
        $this->assertEqual(XN_Job::CREATED, $job->status);
        $this->assertEqual(1, count($job->tasks));
        $this->assertNull($job->completionCallback);
        $this->assertEqual(XG_SecurityHelper::addCsrfToken(W_Cache::getWidget('groups')->buildUrl('group', 'denormalizeFullName')), $job->tasks[0]->executionCallback);
        $this->assertEqual('application/x-www-form-urlencoded', $job->tasks[0]->type);
        $this->assertEqual(array(), $job->tasks[0]->content);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

class TestGroupMembership extends GroupMembership {
    public static function addGroupId($groupId, $user) {
        return parent::addGroupId($groupId, $user, false);
    }
    public static function removeGroupId($groupId, $user) {
        return parent::removeGroupId($groupId, $user);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
