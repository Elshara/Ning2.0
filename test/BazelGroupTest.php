<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class BazelGroupTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('groups');
        $this->key = 'unittestgroup_' . rand();
        $this->name = 'My Unit Testing Group ' . rand();
        $this->desc = 'Fictitious group for unit testing';
        unset($_GET['groupId']);
    }

    public function testCleanTitle() {
        $this->assertEqual('<b>Hello</b> http://google.com world<script>', Group::cleanTitle('<b>Hello</b> http://google.com world<script>'));
        $this->assertEqual('Untitled', Group::cleanTitle('   '));
        $this->assertEqual(Group::MAX_TITLE_LENGTH, strlen(Group::cleanTitle(str_repeat('a', 5 + Group::MAX_TITLE_LENGTH))));
    }

    public function testCleanUrl() {
        $this->assertEqual('<b>Hello</b> http://google.com world<script>', Group::cleanUrl('<b>Hello</b> http://google.com world<script>'));
        $this->assertEqual('', Group::cleanUrl('   '));
        $this->assertEqual(Group::MAX_URL_LENGTH, strlen(Group::cleanUrl(str_repeat('a', 5 + Group::MAX_URL_LENGTH))));
    }

    public function testCleanDescription() {
        $this->assertEqual('<b>Hello</b> http://google.com world<script>', Group::cleanDescription('<b>Hello</b> http://google.com world<script>'));
        $this->assertNull(Group::cleanDescription('   '));
        $this->assertEqual(Group::MAX_DESCRIPTION_LENGTH, strlen(Group::cleanDescription(str_repeat('a', 5 + Group::MAX_DESCRIPTION_LENGTH))));
    }

    public function testCleanExternalWebsiteName() {
        $this->assertEqual('<b>Hello</b> http://google.com world<script>', Group::cleanExternalWebsiteName('<b>Hello</b> http://google.com world<script>'));
        $this->assertNull(Group::cleanExternalWebsiteName('   '));
        $this->assertEqual(Group::MAX_EXTERNAL_WEBSITE_NAME_LENGTH, strlen(Group::cleanExternalWebsiteName(str_repeat('a', 5 + Group::MAX_EXTERNAL_WEBSITE_NAME_LENGTH))));
    }

    public function testCleanExternalWebsiteUrl() {
        $this->assertEqual('<b>Hello</b> http://google.com world<script>', Group::cleanExternalWebsiteUrl('<b>Hello</b> http://google.com world<script>'));
        $this->assertNull(Group::cleanExternalWebsiteUrl('   '));
        $this->assertNull(Group::cleanExternalWebsiteUrl('http://'));
        $this->assertNull(Group::cleanExternalWebsiteUrl('   http://   '));
        $this->assertEqual(Group::MAX_EXTERNAL_WEBSITE_URL_LENGTH, strlen(Group::cleanExternalWebsiteUrl(str_repeat('a', 5 + Group::MAX_EXTERNAL_WEBSITE_URL_LENGTH))));
        $this->assertEqual('http://www.foo.com', Group::cleanExternalWebsiteUrl('www.foo.com'));
        $this->assertEqual('http://www.foo.com', Group::cleanExternalWebsiteUrl('http://www.foo.com'));
        $this->assertEqual('https://www.foo.com', Group::cleanExternalWebsiteUrl('https://www.foo.com'));
        $this->assertEqual('ftp://www.foo.com', Group::cleanExternalWebsiteUrl('ftp://www.foo.com'));
    }

    public function testNameTaken() {
        $this->assertFalse(Group::nameTaken('a'));
        Group::create('a', 'b')->save();
        $this->assertFalse(Group::nameTaken('b'));
    }

    public function testIconUrl() {
        $this->assertEqual(xg_cdn('/xn_resources/widgets/groups/gfx/avatar-group.png'), Group::iconUrl(Group::create(), 100));
    }

    public function testSetStatus() {
        $group = Group::create('a');
        $group->save();
        $groupMembership = GroupMembership::setStatus(GroupMembership::loadOrCreate($group, XN_Profile::current()->screenName), 'member');
        $this->assertEqual('N', $groupMembership->my->welcomed);
        Group::setStatus($group, XN_Profile::current()->screenName, 'nonmember');
        $this->assertEqual('nonmember', Group::status($group, XN_Profile::current()->screenName));
    }

    public function testGroups() {
        $flora = Group::create('flora');
        $flora->my->deleted = 'Y'; // Deleted
        $flora->save();
        $fauna = Group::create('fauna');
        $fauna->save();
        $this->assertEqual(serialize(array()), serialize(array_keys(Group::groupsForObjects(array()))));
        $tree = XN_Content::create('Tree');
        $tree->my->groupId = $flora->id;
        $cat = XN_Content::create('Cat');
        $cat->my->groupId = $fauna->id;
        $this->assertEqual(serialize(array()), serialize(array_keys(Group::groupsForObjects(array($tree)))));
        $this->assertEqual(serialize(array($fauna->id)), serialize(array_keys(Group::groupsForObjects(array($cat)))));
    }

    public function testAdmins() {
        $users = XG_TestHelper::getTwoUsers();
        $current = $users['current'];
        $admin = $users['other'];
        $this->assertNotNull($admin, 'Not enough members on this network to have an admin and a member');
        $groupName = 'Pizza Fans';
        $group = Group::create($groupName);
        $group->save();
        Group::setStatus($group, $admin, 'admin');
        Group::setStatus($group, $current, 'member');
        $admins = Group::adminProfiles($group);
        $this->assertEqual(1, count($admins));
        $this->assertEqual($admin, reset($admins)->screenName);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';