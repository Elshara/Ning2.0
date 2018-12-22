<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class GroupInvitationRequestTest extends UnitTestCase {

    public function testLoadOrCreate() {
        $group = Group::create('a');
        $group->save();
        $a = GroupInvitationRequest::loadOrCreate($group, 'NingDev');
        $a->save();
        $b1 = GroupInvitationRequest::loadOrCreate($group, 'JonathanAquino');
        $b1->save();
        $b2 = GroupInvitationRequest::loadOrCreate($group, 'jonathanaquino');
        $b2->save();
        $this->assertNotEqual($a->id, $b1->id);
        $this->assertEqual($b1->id, $b2->id);
        //TODO: We can use assertIdentical here instead of assertTrue($x === $y) [Thomas David Baker 2008-08-02]
        $this->assertTrue($b1 === $b2);
        $this->assertEqual('JonathanAquino', $b1->my->requestor);
        $this->assertEqual($group->id, $b1->my->groupId);
    }

    public function testUserIds() {
        $group = Group::create('Pizza Lovers');
        $group->save();
        $groupInvitationRequest1 = GroupInvitationRequest::loadOrCreate($group, 'NingDev');
        $groupInvitationRequest2 = GroupInvitationRequest::loadOrCreate($group, 'JonathanAquino');
        $this->assertEqual(serialize(array('NingDev', 'JonathanAquino')), serialize(GroupInvitationRequest::userIds(array($groupInvitationRequest1, $groupInvitationRequest2))));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
