<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/groups/lib/helpers/Groups_BroadcastHelper.php');

class Groups_BroadcastHelperTest extends UnitTestCase {

    public function setUp() {
        FakeUser::$i = 0;
    }

    public function testBroadcast1() {
        $helper = new TestBroadcastHelper(false, array());
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(0, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
loadOrCreateProfileSet(123:Group:456)
findUsers(123:Group:456, 0, 3)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast1B() {
        $helper = new TestBroadcastHelper(true, array());
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(1, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast2() {
        $helper = new TestBroadcastHelper(false, array(new FakeUser()));
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(1, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
loadOrCreateProfileSet(123:Group:456)
findUsers(123:Group:456, 0, 3)
acceptingBroadcastsHook(User1)
addMembers(User1)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('User1', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast2B() {
        $helper = new TestBroadcastHelper(true, array(new FakeUser()));
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(1, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast3() {
        $helper = new TestBroadcastHelper(false, array(new FakeUser(true), new FakeUser(false)));
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(2, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
loadOrCreateProfileSet(123:Group:456)
findUsers(123:Group:456, 0, 3)
acceptingBroadcastsHook(User1)
addMembers(User1)
acceptingBroadcastsHook(User2)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('User1', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast4() {
        $helper = new TestBroadcastHelper(false, array(new FakeUser(false), new FakeUser(true)));
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(2, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
loadOrCreateProfileSet(123:Group:456)
findUsers(123:Group:456, 0, 3)
acceptingBroadcastsHook(User1)
acceptingBroadcastsHook(User2)
addMembers(User2)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('User2', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast5() {
        $helper = new TestBroadcastHelper(false, array(new FakeUser(false), new FakeUser(false)));
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(2, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
loadOrCreateProfileSet(123:Group:456)
findUsers(123:Group:456, 0, 3)
acceptingBroadcastsHook(User1)
acceptingBroadcastsHook(User2)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast6() {
        $helper = new TestBroadcastHelper(false, array(new FakeUser(true), new FakeUser(true)));
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(2, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
loadOrCreateProfileSet(123:Group:456)
findUsers(123:Group:456, 0, 3)
acceptingBroadcastsHook(User1)
addMembers(User1)
acceptingBroadcastsHook(User2)
addMembers(User2)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('User1,User2', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast7() {
        $helper = new TestBroadcastHelper(false, array(new FakeUser(true), new FakeUser(false), new FakeUser(true), new FakeUser(true), new FakeUser(false), new FakeUser(true)));
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(3, $changed);
        $this->assertEqual(3, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
loadOrCreateProfileSet(123:Group:456)
findUsers(123:Group:456, 0, 3)
acceptingBroadcastsHook(User1)
addMembers(User1)
acceptingBroadcastsHook(User2)
acceptingBroadcastsHook(User3)
addMembers(User3)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('User1,User3', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast7B() {
        $helper = new TestBroadcastHelper(true, array(new FakeUser(true), new FakeUser(false), new FakeUser(true), new FakeUser(true), new FakeUser(false), new FakeUser(true)));
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 0);
        $this->assertEqual(1, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testBroadcast8() {
        $helper = new TestBroadcastHelper(false, array(new FakeUser(true), new FakeUser(false), new FakeUser(true), new FakeUser(true), new FakeUser(false), new FakeUser(true)));
        list($changed, $contentRemaining) = $helper->broadcast(new FakeGroup(), 'test subject', 'test body', 1);
        $this->assertEqual(3, $changed);
        $this->assertEqual(0, $contentRemaining);
        $expectedLog = trim('
loadProfileSet(123:Group:456)
loadOrCreateProfileSet(123:Group:456)
findUsers(123:Group:456, 3, 6)
acceptingBroadcastsHook(User4)
addMembers(User4)
acceptingBroadcastsHook(User5)
acceptingBroadcastsHook(User6)
addMembers(User6)
broadcastProper(123:Group:456, test subject, test body)
');
        $this->assertLogsEqual($expectedLog, implode("\n", $helper->log));
        $this->assertEqual('User4,User6', implode(',', $helper->screenNamesAddedToProfileSet));
    }

    public function testAcceptingBroadcasts() {
        $user = XN_Content::create('User');
        $this->assertTrue(Groups_BroadcastHelper::acceptingBroadcasts($user));
        $user->my->emailNewMessagePref = 'Y';
        $this->assertTrue(Groups_BroadcastHelper::acceptingBroadcasts($user));
        $user->my->emailNewMessagePref = 'N';
        $this->assertFalse(Groups_BroadcastHelper::acceptingBroadcasts($user));
        $user->my->emailGroupBroadcastPref = 'Y';
        $this->assertTrue(Groups_BroadcastHelper::acceptingBroadcasts($user));
        $user->my->emailGroupBroadcastPref = 'N';
        $this->assertFalse(Groups_BroadcastHelper::acceptingBroadcasts($user));
    }

    public function testProfileSetId() {
        $this->assertEqual('xg_group_broadcast_123_Group_456', Groups_BroadcastHelper::profileSetId('123:Group:456'));
    }

    private function assertLogsEqual($expectedLog, $actualLog) {
        if ($expectedLog != $actualLog) {
            echo '<pre>' . $expectedLog . '</pre>';
            echo '<pre>' . $actualLog . '</pre>';
        }
        $this->assertEqual($expectedLog, $actualLog);
    }

}

class TestBroadcastHelper extends Groups_BroadcastHelper {
    private $profileSetExists;
    private $users;
    public $log = array();
    public $screenNamesAddedToProfileSet = array();
    public function TestBroadcastHelper($profileSetExists, $users) {
        $this->profileSetExists = $profileSetExists;
        $this->users = $users;
    }
    public function broadcastProper($group, $subject, $body) {
        $this->log[] = 'broadcastProper(' . $group->id . ", $subject, $body)";
    }
    public function acceptingBroadcastsHook($user) {
        $this->log[] = 'acceptingBroadcastsHook(' . $user->contributorName . ')';
        return $user->acceptingBroadcasts;
    }
    public function findUsers($group, $start, $end) {
        $this->log[] = "findUsers(" . $group->id . ", $start, $end)";
        return array('users' => array_slice($this->users, $start, $end - $start), 'numUsers' => count($this->users));
    }
    public function loadProfileSet($group) {
        $this->log[] = 'loadProfileSet(' . $group->id . ')';
        return $this->profileSetExists ? $this : false;
    }
    public function loadOrCreateProfileSet($group) {
        $this->log[] = 'loadOrCreateProfileSet(' . $group->id . ')';
        return $this;
    }
    public function userUpgradeLimit() {
        return 3;
    }
    public function addMembers($screenName) {
        $this->log[] = 'addMembers(' . $screenName . ')';
        $this->screenNamesAddedToProfileSet[] = $screenName;
    }
    public function outputLog() {
        echo '<pre>' . implode("\n", $this->log) . '</pre>';
    }
}

class FakeGroup {
    public $id = '123:Group:456';
}

class FakeUser {
    public static $i = 0;
    public $contributorName;
    public $acceptingBroadcasts;
    public function FakeUser($acceptingBroadcasts = true) {
        $this->contributorName = 'User' . ++self::$i;
        $this->acceptingBroadcasts = $acceptingBroadcasts;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
