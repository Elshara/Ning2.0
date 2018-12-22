<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');

class Groups_InvitationHelperTest extends UnitTestCase {

    public function testGroupId() {
        $this->assertEqual(null, TestInvitationHelper::groupId(null));
        $this->assertEqual(null, TestInvitationHelper::groupId('network-bulk-invitation'));
        $this->assertEqual(null, TestInvitationHelper::groupId('network-invitation'));
        $this->assertEqual('111:Group:222', TestInvitationHelper::groupId('group-invitation-111:Group:222'));
    }

}

class TestInvitationHelper extends Groups_InvitationHelper {
    public static function groupId($label) {
        return parent::groupId($label);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
