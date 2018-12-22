<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationMode.php');
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NetworkInvitationMode.php');
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NetworkShareMode.php');
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_GroupInvitationMode.php');
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_GroupShareMode.php');
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_EventInvitationMode.php');
W_Cache::getWidget('events')->includeFileOnce('/models/EventAttendee.php');
W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_InvitationHelper.php');
Mock::generate('TestRest');
Mock::generate('Index_InvitationHelper');
Mock::generate('Events_InvitationHelper');
Mock::generate('Groups_InvitationHelper');
Mock::generate('EventAttendee');
Mock::generate('stdClass', 'MockXN_Content', array('save'));

class Index_InvitationModeTest extends UnitTestCase {

    public function tearDown() {
        TestRest::setInstance(null);
    }

    public function testKeyByEmailAddress() {
        $mode = new TestNetworkInvitationMode();
        $this->assertEqual(array(), $mode->keyByEmailAddress(array()));
        $this->assertEqual(array('jon@example.org' => array('name' => 'Jon', 'emailAddress' => 'jon@example.org')), $mode->keyByEmailAddress(array(array('name' => 'Jon', 'emailAddress' => 'jon@example.org'))));
        $this->assertEqual(array(XN_Profile::current()->email), array_keys($mode->keyByEmailAddress(array(XN_Profile::current()))));
        $this->assertEqual(array(XN_Profile::current()->email), array_keys($mode->keyByEmailAddress(array(User::load(XN_Profile::current())))));
        $groupMembership = XN_Content::create('GroupMembership');
        $groupMembership->my->username = XN_Profile::current()->screenName;
        $this->assertEqual(array(XN_Profile::current()->email), array_keys($mode->keyByEmailAddress(array($groupMembership))));
    }

    // Ian is not on Ning
    // Harry is on another Ning network
    // Gareth is a member
    // Gregory is a pending member
    // Gilbert is banned
    // Fred is a friend and member
    // Fabricio is a friend and pending member
    // Fonz is a friend but banned
    // Ed is a group member
    // Elizabeth is a friend and group admin
    // Esther is a friend but banned from the group

    public function testNetworkInvitationModeClassifyRecipients1() {
        $mode = new TestNetworkInvitationMode();
        list($invitationRecipients, $friendRequestRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'friendUsers' => self::keyByEmailAddress(self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked')),
                'currentUserCanSendInvites' => true));
        $invitationRecipients = array_keys($invitationRecipients);
        $friendRequestRecipients = array_keys($friendRequestRecipients);
        sort($invitationRecipients);
        sort($friendRequestRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses('ian', 'harry'), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ed', 'gareth', 'gregory'), $json->encode($friendRequestRecipients));
    }

    public function testNetworkInvitationModeClassifyRecipients2() {
        $mode = new TestNetworkInvitationMode();
        list($invitationRecipients, $friendRequestRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'friendUsers' => self::keyByEmailAddress(self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked')),
                'currentUserCanSendInvites' => false));
        $invitationRecipients = array_keys($invitationRecipients);
        $friendRequestRecipients = array_keys($friendRequestRecipients);
        sort($invitationRecipients);
        sort($friendRequestRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ed', 'gareth', 'gregory'), $json->encode($friendRequestRecipients));
    }

    public function testGroupInvitationModeClassifyRecipients1() {
        $mode = new TestGroupInvitationMode();
        $invitationRecipients = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'friendUsers' => self::keyByEmailAddress(self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => true));
        $invitationRecipients = array_keys($invitationRecipients);
        sort($invitationRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses('ed', 'elizabeth', 'ian', 'harry', 'gareth', 'gregory', 'fred', 'fabricio'), $json->encode($invitationRecipients));
    }

    public function testGroupInvitationModeClassifyRecipients2() {
        $mode = new TestGroupInvitationMode();
        $invitationRecipients = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'friendUsers' => self::keyByEmailAddress(self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => false));
        $invitationRecipients = array_keys($invitationRecipients);
        sort($invitationRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
    }

    public function testEventInvitationModeClassifyRecipients1() {
        $mode = new TestEventInvitationMode();
        $invitationRecipients = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'eventAttendees' => self::keyByEmailAddress(self::eventAttendee('ed', EventAttendee::NOT_RSVP), self::eventAttendee('elizabeth', EventAttendee::ATTENDING), self::eventAttendee('esther', EventAttendee::NOT_ATTENDING)),
                'currentUserCanSendEventInvites' => true));
        $invitationRecipients = array_keys($invitationRecipients);
        sort($invitationRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses('ian', 'harry', 'gareth', 'gregory', 'fred', 'fabricio', 'ed'), $json->encode($invitationRecipients));
    }

    public function testEventInvitationModeClassifyRecipients2() {
        $mode = new TestEventInvitationMode();
        $invitationRecipients = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'eventAttendees' => self::keyByEmailAddress(self::eventAttendee('ed', 'member'), self::eventAttendee('elizabeth', 'admin'), self::eventAttendee('esther', 'banned')),
                'currentUserCanSendEventInvites' => false));
        $invitationRecipients = array_keys($invitationRecipients);
        sort($invitationRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
    }

    public function testNetworkShareModeClassifyRecipients1() {
        $mode = new TestNetworkShareMode(array());
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'currentUserCanSendInvites' => true,
                'everythingIsVisible' => true));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ian', 'harry', 'gareth', 'gregory', 'gilbert', 'fred', 'fabricio', 'fonz', 'ed', 'elizabeth', 'esther'), $json->encode($linkRecipients));
    }

    public function testNetworkShareModeClassifyRecipients2() {
        $mode = new TestNetworkShareMode(array());
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'currentUserCanSendInvites' => true,
                'everythingIsVisible' => false));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses('ian', 'harry'), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('gareth', 'gregory', 'fabricio', 'fred', 'ed', 'elizabeth', 'esther'), $json->encode($linkRecipients));
    }

    public function testNetworkShareModeClassifyRecipients3() {
        $mode = new TestNetworkShareMode(array());
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'currentUserCanSendInvites' => false,
                'everythingIsVisible' => true));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ian', 'harry', 'gareth', 'gregory', 'gilbert', 'fonz', 'fred', 'fabricio', 'ed', 'elizabeth', 'esther'), $json->encode($linkRecipients));
    }

    public function testNetworkShareModeClassifyRecipients4() {
        $mode = new TestNetworkShareMode(array());
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'currentUserCanSendInvites' => false,
                'everythingIsVisible' => false));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('gareth', 'gregory', 'fabricio', 'fred', 'ed', 'elizabeth', 'esther'), $json->encode($linkRecipients));
    }

    public function testGroupShareModeClassifyRecipients1() {
        $mode = new TestGroupShareMode();
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => true,
                'groupIsPublic' => true,
                'everythingIsVisible' => true));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ian', 'harry', 'gareth', 'gregory', 'gilbert', 'fred', 'fabricio', 'fonz', 'ed', 'elizabeth', 'esther'), $json->encode($linkRecipients));
    }

    public function testGroupShareModeClassifyRecipients2() {
        $mode = new TestGroupShareMode();
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => true,
                'groupIsPublic' => true,
                'everythingIsVisible' => false));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses('ian', 'harry'), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('gareth', 'gregory', 'fred', 'fabricio', 'ed', 'elizabeth'), $json->encode($linkRecipients));
    }

    public function testGroupShareModeClassifyRecipients3() {
        $mode = new TestGroupShareMode();
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => false,
                'groupIsPublic' => true,
                'everythingIsVisible' => true));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ian', 'harry', 'gareth', 'gregory', 'gilbert', 'fred', 'fabricio', 'fonz', 'ed', 'elizabeth', 'esther'), $json->encode($linkRecipients));
    }

    public function testGroupShareModeClassifyRecipients4() {
        $mode = new TestGroupShareMode();
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => false,
                'groupIsPublic' => true,
                'everythingIsVisible' => false));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('gareth', 'gregory', 'fred', 'fabricio', 'ed', 'elizabeth'), $json->encode($linkRecipients));
    }

    public function testGroupShareModeClassifyRecipients5() {
        $mode = new TestGroupShareMode();
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => true,
                'groupIsPublic' => false,
                'everythingIsVisible' => true));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses('ian', 'harry', 'gareth', 'gregory', 'fred', 'fabricio'), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ed', 'elizabeth'), $json->encode($linkRecipients));
    }

    public function testGroupShareModeClassifyRecipients6() {
        $mode = new TestGroupShareMode();
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => true,
                'groupIsPublic' => false,
                'everythingIsVisible' => false));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses('ian', 'harry', 'gareth', 'gregory', 'fred', 'fabricio'), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ed', 'elizabeth'), $json->encode($linkRecipients));
    }

    public function testGroupShareModeClassifyRecipients7() {
        $mode = new TestGroupShareMode();
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => false,
                'groupIsPublic' => false,
                'everythingIsVisible' => true));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ed', 'elizabeth'), $json->encode($linkRecipients));
    }

    public function testGroupShareModeClassifyRecipients8() {
        $mode = new TestGroupShareMode();
        list($invitationRecipients, $linkRecipients) = $mode->classifyRecipients(array(
                'contactList' => self::keyByEmailAddress(self::contact('ed'), self::contact('elizabeth'), self::contact('esther'), self::contact('fred'), self::contact('fabricio'), self::contact('fonz'), self::contact('gareth'), self::contact('gregory'), self::contact('gilbert'), self::contact('harry'), self::contact('ian')),
                'users' => self::keyByEmailAddress(self::user('ed'), self::user('elizabeth'), self::user('esther'), self::user('fred'), self::user('fabricio', 'pending'), self::user('fonz', 'blocked'), self::user('gareth'), self::user('gregory', 'pending'), self::user('gilbert', 'blocked')),
                'groupMemberships' => self::keyByEmailAddress(self::groupMembership('ed', 'member'), self::groupMembership('elizabeth', 'admin'), self::groupMembership('esther', 'banned')),
                'currentUserCanSendGroupInvites' => false,
                'groupIsPublic' => false,
                'everythingIsVisible' => false));
        $invitationRecipients = array_keys($invitationRecipients);
        $linkRecipients = array_keys($linkRecipients);
        sort($invitationRecipients);
        sort($linkRecipients);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual(self::emailAddresses(), $json->encode($invitationRecipients));
        $this->assertEqual(self::emailAddresses('ed', 'elizabeth'), $json->encode($linkRecipients));
    }

    private static function emailAddresses() {
        $args = func_get_args();
        $emailAddresses = array();
        foreach ($args as $name) {
            $emailAddresses[] = $name . '@example.org';
        }
        sort($emailAddresses);
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        return $json->encode($emailAddresses);
    }

    private static function keyByEmailAddress() {
        $items = func_get_args();
        $itemsKeyedByEmailAddress = array();
        foreach ($items as $item) {
            if (is_array($item)) { $itemsKeyedByEmailAddress[$item['emailAddress']] = $item; }
            elseif ($item->my->screenName) { $itemsKeyedByEmailAddress[$item->my->screenName . '@example.org'] = $item; }
            elseif ($item->my->username) { $itemsKeyedByEmailAddress[$item->my->username . '@example.org'] = $item; }
            else { $itemsKeyedByEmailAddress[$item->title . '@example.org'] = $item; }
        }
        return $itemsKeyedByEmailAddress;
    }

    private function contact($name) {
        return array('name' => ucfirst($name), 'emailAddress' => $name . '@example.org');
    }

    private function user($name, $status = null) {
        $user = XN_Content::create('User');
        $user->title = $name;
        $user->my->xg_index_status = $status;
        return $user;
    }

    private function groupMembership($name, $status) {
        $groupMembership = XN_Content::create('GroupMembership');
        $groupMembership->my->username = $name;
        $groupMembership->my->status = $status;
        return $groupMembership;
    }

    private function eventAttendee($name, $status) {
        $eventAttendee = XN_Content::create('EventAttendee');
        $eventAttendee->my->screenName = $name;
        $eventAttendee->my->status = $status;
        return $eventAttendee;
    }

    public function testNormalizeEmailAddresses() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $invitationMode = new TestInvitationMode();
        $this->assertEqual($json->encode(array(
                array('name' => 'Rocky', 'emailAddress' => 'sdlfs2394@example.org'),
                array('name' => 'Barbara', 'emailAddress' => 'lkhslds32946@users'),
                array('name' => 'Foo', 'emailAddress' => XN_Profile::current()->email))),
            $json->encode($invitationMode->normalizeEmailAddresses(array(
                array('name' => 'Rocky', 'emailAddress' => 'sdlfs2394@example.org'),
                array('name' => 'Barbara', 'emailAddress' => 'lkhslds32946@users'),
                array('name' => 'Foo', 'emailAddress' => XN_Profile::current()->email)))));
        $this->assertEqual($json->encode(array(
                array('name' => 'Rocky', 'emailAddress' => 'sdlfs2394@example.org'),
                array('name' => 'Barbara', 'emailAddress' => 'lkhslds32946@users'),
                array('name' => 'Foo', 'emailAddress' => XN_Profile::current()->email))),
            $json->encode($invitationMode->normalizeEmailAddresses(array(
                array('name' => 'Rocky', 'emailAddress' => 'sdlfs2394@example.org'),
                array('name' => 'Barbara', 'emailAddress' => 'lkhslds32946@users'),
                array('name' => 'Foo', 'emailAddress' => XN_Profile::current()->screenName . '@users')))));
    }

    public function testCreateGroupInvitation() {
        $user = new MockXN_Content();
        $group = new stdClass();
        $group->id = '123:Group:456';
        $groupInvitationMode = new TestGroupInvitationMode();
        $groupInvitationMode->setGroup($group);
        $groupInvitationHelper = new MockGroups_InvitationHelper();
        $invitationHelper = new MockIndex_InvitationHelper();
        $groupInvitationHelper->expectNever('addGroupInviting');
        $invitationHelper->expectOnce('createInvitation', array('amy@foo.com', 'Amy', 'group-invitation-123:Group:456'));
        $invitationHelper->setReturnValue('createInvitation', '[Invitation]');
        $groupInvitationHelper->expectOnce('groupInvitationLabel', array('123:Group:456'));
        $groupInvitationHelper->setReturnValue('groupInvitationLabel', 'group-invitation-123:Group:456');
        $user->expectNever('save');
        $this->assertEqual('[Invitation]', $groupInvitationMode->createGroupInvitation('amy@foo.com', 'Amy', array('joe@bar.com' => $user), $invitationHelper, $groupInvitationHelper));
    }

    public function testCreateGroupInvitation2() {
        $user = new MockXN_Content();
        $group = new stdClass();
        $group->id = '123:Group:456';
        $groupInvitationMode = new TestGroupInvitationMode();
        $groupInvitationMode->setGroup($group);
        $groupInvitationHelper = new MockGroups_InvitationHelper();
        $invitationHelper = new MockIndex_InvitationHelper();
        $groupInvitationHelper->expectOnce('addGroupInviting', array($user, '123:Group:456', XN_Profile::current()->screenName));
        $invitationHelper->expectOnce('createInvitation', array('amy@foo.com', 'Amy', 'group-invitation-123:Group:456'));
        $invitationHelper->setReturnValue('createInvitation', '[Invitation]');
        $groupInvitationHelper->expectOnce('groupInvitationLabel', array('123:Group:456'));
        $groupInvitationHelper->setReturnValue('groupInvitationLabel', 'group-invitation-123:Group:456');
        $user->expectOnce('save');
        $this->assertEqual('[Invitation]', $groupInvitationMode->createGroupInvitation('amy@foo.com', 'Amy', array('joe@bar.com' => $user, 'amy@foo.com' => $user), $invitationHelper, $groupInvitationHelper));
    }

    public function testCreateEventInvitation() {
        $eventAttendee = new MockEventAttendee();
        $user = new MockXN_Content();
        $user->title = 'amy';
        $event = new stdClass();
        $event->id = '123:Event:456';
        $eventInvitationMode = new TestEventInvitationMode();
        $eventInvitationMode->setEvent($event);
        $eventInvitationHelper = new MockEvents_InvitationHelper();
        $invitationHelper = new MockIndex_InvitationHelper();
        $eventAttendee->expectNever('setStatus');
        $invitationHelper->expectOnce('createInvitation', array('amy@foo.com', 'Amy', 'event-invitation-123:Event:456'));
        $invitationHelper->setReturnValue('createInvitation', '[Invitation]');
        $eventInvitationHelper->expectOnce('eventInvitationLabel', array('123:Event:456'));
        $eventInvitationHelper->setReturnValue('eventInvitationLabel', 'event-invitation-123:Event:456');
        $this->assertEqual('[Invitation]', $eventInvitationMode->createEventInvitation('amy@foo.com', 'Amy', array('joe@bar.com' => $user), $invitationHelper, $eventInvitationHelper, $eventAttendee));
    }

    public function testCreateEventInvitation2() {
        $eventAttendee = new MockEventAttendee();
        $user = new MockXN_Content();
        $user->title = 'amy';
        $event = new stdClass();
        $event->id = '123:Event:456';
        $eventInvitationMode = new TestEventInvitationMode();
        $eventInvitationMode->setEvent($event);
        $eventInvitationHelper = new MockEvents_InvitationHelper();
        $invitationHelper = new MockIndex_InvitationHelper();
        $eventAttendee->expectOnce('setStatus', array('amy', $event, EventAttendee::NOT_RSVP, false, XN_Profile::current()->screenName));
        $invitationHelper->expectOnce('createInvitation', array('amy@foo.com', 'Amy', 'event-invitation-123:Event:456'));
        $invitationHelper->setReturnValue('createInvitation', '[Invitation]');
        $eventInvitationHelper->expectOnce('eventInvitationLabel', array('123:Event:456'));
        $eventInvitationHelper->setReturnValue('eventInvitationLabel', 'event-invitation-123:Event:456');
        $this->assertEqual('[Invitation]', $eventInvitationMode->createEventInvitation('amy@foo.com', 'Amy', array('joe@bar.com' => $user, 'amy@foo.com' => $user), $invitationHelper, $eventInvitationHelper, $eventAttendee));
    }

    public function testGet() {
        $group = XN_Content::create('Group');
        $group->save();
        $event = XN_Content::create('Event');
        $event->save();
        TestInvitationMode::setInvitationModes(array());
        $this->assertTrue(TestInvitationMode::get(array('inviteOrShare' => 'invite')) instanceof Index_NetworkInvitationMode);
        $this->assertEqual(array("invite, , "), array_keys(TestInvitationMode::getInvitationModes()));

        TestInvitationMode::setInvitationModes(array());
        $this->assertTrue(TestInvitationMode::get(array('inviteOrShare' => 'share')) instanceof Index_NetworkShareMode);
        $this->assertEqual(array("share, , "), array_keys(TestInvitationMode::getInvitationModes()));

        TestInvitationMode::setInvitationModes(array());
        $this->assertTrue(TestInvitationMode::get(array('inviteOrShare' => 'invite', 'groupId' => $group->id)) instanceof Index_GroupInvitationMode);
        $this->assertEqual(array("invite, {$group->id}, "), array_keys(TestInvitationMode::getInvitationModes()));
        $this->assertEqual($group->id, TestAbstractGroupInvitationMode::getGroup(TestInvitationMode::get(array('inviteOrShare' => 'invite', 'groupId' => $group->id)))->id);

        TestInvitationMode::setInvitationModes(array());
        $this->assertTrue(TestInvitationMode::get(array('inviteOrShare' => 'share', 'groupId' => $group->id)) instanceof Index_GroupShareMode);
        $this->assertEqual(array("share, {$group->id}, "), array_keys(TestInvitationMode::getInvitationModes()));
        $this->assertEqual($group->id, TestAbstractGroupInvitationMode::getGroup(TestInvitationMode::get(array('inviteOrShare' => 'share', 'groupId' => $group->id)))->id);

        TestInvitationMode::setInvitationModes(array());
        $this->assertTrue(TestInvitationMode::get(array('inviteOrShare' => 'invite', 'eventId' => $event->id)) instanceof Index_EventInvitationMode);
        $this->assertEqual(array("invite, , {$event->id}"), array_keys(TestInvitationMode::getInvitationModes()));
    }

}

class TestNetworkInvitationMode extends Index_NetworkInvitationMode {
    public function keyByEmailAddress($items) { return parent::keyByEmailAddress($items); }
    public function classifyRecipients($args) { return parent::classifyRecipients($args); }
}

class TestGroupInvitationMode extends Index_GroupInvitationMode {
    public function __construct() {}
    public function classifyRecipients($args) { return parent::classifyRecipients($args); }
    public function setGroup($group) { $this->group = $group; }
    public function createGroupInvitation($emailAddress, $name, $usersKeyedByEmailAddress, $invitationHelperClass, $groupInvitationHelperClass) {
        return parent::createGroupInvitation($emailAddress, $name, $usersKeyedByEmailAddress, $invitationHelperClass, $groupInvitationHelperClass);
    }
}

class TestEventInvitationMode extends Index_EventInvitationMode {
    public function __construct() {}
    public function classifyRecipients($args) { return parent::classifyRecipients($args); }
    public function setEvent($event) { $this->event = $event; }
    public function createEventInvitation($emailAddress, $name, $usersKeyedByEmailAddress, $invitationHelperClass, $eventInvitationHelperClass, $eventAttendeeClass) {
        return parent::createEventInvitation($emailAddress, $name, $usersKeyedByEmailAddress, $invitationHelperClass, $eventInvitationHelperClass, $eventAttendeeClass);
    }
}

abstract class TestAbstractGroupInvitationMode extends Index_AbstractGroupInvitationMode {
    public static function getGroup($groupInvitationMode) {
        return $groupInvitationMode->group;
    }
}

class TestNetworkShareMode extends Index_NetworkShareMode {
    public function classifyRecipients($args) { return parent::classifyRecipients($args); }
}

class TestGroupShareMode extends Index_GroupShareMode {
    public function __construct() {}
    public function classifyRecipients($args) { return parent::classifyRecipients($args); }
}

class TestCache extends XG_Cache {
    public static function setCache($cache) { parent::$_cache = $cache; }
}

class TestInvitationMode extends Index_InvitationMode {
    public function normalizeEmailAddresses($contactList) {
        return parent::normalizeEmailAddresses($contactList);
    }
    public function sendProper($contactList, $message, $contentId) {
        throw new Exception('Shouldn\'t get here');
    }
    public function setInvitationModes($invitationModes) {
        parent::$invitationModes = $invitationModes;
    }

    public function getInvitationModes() {
        return parent::$invitationModes;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
