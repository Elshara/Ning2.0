<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
Mock::generate('Index_InvitationHelper');
Mock::generate('XN_Invitation');
Mock::generate('XN_Query');

class Index_InvitationHelperTest extends UnitTestCase {

    public function testIsErrorArray() {
        $this->assertTrue(Index_InvitationHelper::isErrorArray(array('contact-import:1' => 'No such import (perhaps it expired)', 'xn:status' => 404)));
        $this->assertFalse(Index_InvitationHelper::isErrorArray(array('foo' => 'bar')));
    }

    public function testDeleteInvitations1() {
        $this->doTestDeleteInvitations(false, 1, array($this->createInvitation(false), $this->createInvitation(false), $this->createInvitation(false)));
    }

    public function testDeleteInvitations2() {
        $this->doTestDeleteInvitations(true, 0, array($this->createInvitation(true), $this->createInvitation(true), $this->createInvitation(true)));
    }

    public function doTestDeleteInvitations($consume, $expectedDeleteInvitationsProperCount, $invitations) {
        $query = new MockXN_Query();
        $query->expectCallCount('filter', 2);
        $query->expectAt(0, 'filter', array('label', '=', 'foo'));
        $query->expectAt(1, 'filter', array('recipient', 'in', array('jane')));
        $query->setReturnValue('filter', $query);
        $query->expectOnce('execute', array());
        $query->setReturnValue('execute', $invitations);
        $helper = new MockIndex_InvitationHelper();
        $helper->expectOnce('_createInvitationQuery', array());
        $helper->setReturnValue('_createInvitationQuery', $query);
        $helper->expectCallCount('_deleteInvitationsProper', $expectedDeleteInvitationsProperCount);
        TestInvitationHelper::setInstance($helper);
        Index_InvitationHelper::deleteInvitations('jane', 'foo', $consume);
    }

    private function createInvitation($consumeExpected) {
        $invitation = new MockXN_Invitation();
        $invitation->expectCallCount('consume', $consumeExpected ? 1 : 0);
        return $invitation;
    }

    public function testRecipients1() {
        $this->assertEqual(array(), Index_InvitationHelper::recipients(array()));
    }

    public function testRecipients2() {
        $this->assertEqual(array('joe@foo.com'), Index_InvitationHelper::recipients(array($this->createInvitationForRecipient('joe@foo.com'))));
    }

    public function testRecipients3() {
        $this->assertEqual(array('joe@foo.com', 'jane@foo.com'), Index_InvitationHelper::recipients(array($this->createInvitationForRecipient('joe@foo.com'), $this->createInvitationForRecipient('jane@foo.com'))));
    }

    private function createInvitationForRecipient($recipient) {
        return XN_Invitation::create(array('recipient' => $recipient));
    }

    private function createInvitationForInviter($inviter) {
        $invitation = new StdClass();
        $invitation->inviter = $inviter;
        return $invitation;
    }

    public function testInviters() {
        $this->assertEqual('a,b,c', implode(',', TestInvitationHelper::inviters(array(
                $this->createInvitationForInviter('a'),
                $this->createInvitationForInviter('a'),
                $this->createInvitationForInviter('b'),
                $this->createInvitationForInviter('c')))));
    }

}

class TestInvitationHelper extends Index_InvitationHelper {
    public static function classifyUnusedInvitations($ids) {
        return parent::classifyUnusedInvitations($ids);
    }
    public static function setInstance($instance) {
        parent::$instance = $instance;
    }
    public static function inviters($invitations) {
        return parent::inviters($invitations);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
