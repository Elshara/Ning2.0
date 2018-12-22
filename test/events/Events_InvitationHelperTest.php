<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');

class Events_InvitationHelperTest extends UnitTestCase {

    public function testEventId() {
        $this->assertEqual(null, TestInvitationHelper::eventId(null));
        $this->assertEqual(null, TestInvitationHelper::eventId('network-bulk-invitation'));
        $this->assertEqual(null, TestInvitationHelper::eventId('network-invitation'));
        $this->assertEqual('111:Event:222', TestInvitationHelper::eventId('event-invitation-111:Event:222'));
    }

}

class TestInvitationHelper extends Events_InvitationHelper {
    public static function eventId($label) {
        return parent::eventId($label);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
