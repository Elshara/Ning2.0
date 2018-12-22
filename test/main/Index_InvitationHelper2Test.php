<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');

class Index_InvitationHelper2Test extends UnitTestCase {

    public function testMetadataForInvitations() {
        $a = XN_Invitation::create(array('name' => 'Joe', 'recipient' => XN_Profile::current()->email));
        TestInvitation::mergeData($a, array('id' => 'A1', 'createdDate' => '1997-07-16T19:20:30+00:00', 'inviter' => 'valerie'));
        $b = XN_Invitation::create(array('name' => 'Sally', 'recipient' => 'sally123@example.org'));
        TestInvitation::mergeData($b, array('id' => 'B1', 'createdDate' => '1998-07-16T19:20:30+00:00', 'inviter' => 'anthony'));
        $this->assertEqual(array(
            array(
                'displayName' => 'Joe',
                'screenName' => XN_Profile::current()->screenName,
                'emailAddress' => XN_Profile::current()->email,
                'id' => XN_Profile::current()->email,
                'date' => '1997-07-16T19:20:30+00:00',
                'inviter' => 'valerie'),
            array(
                'displayName' => 'Sally',
                'screenName' => null,
                'emailAddress' => 'sally123@example.org',
                'id' => 'sally123@example.org',
                'date' => '1998-07-16T19:20:30+00:00',
                'inviter' => 'anthony'),
            ), TestInvitationHelper::metadataForInvitations(array($a, $b), 'recipient'));
    }
}

class TestInvitation extends XN_Invitation {
    public static function mergeData($invitation, $data) {
        $invitation->_data = array_merge($invitation->_data, $data);
    }
}

class TestInvitationHelper extends Index_InvitationHelper {
    public static function canDeleteEquivalentInvitations($label) {
        return parent::canDeleteEquivalentInvitations($label);
    }
    public static function addGroupInviting($user, $groupId, $inviter) {
        return parent::addGroupInviting($user, $groupId, $inviter);
    }
    public static function setAcceptedInvitation($screenName, $inviter, $label) {
        parent::setAcceptedInvitation($screenName, $inviter, $label);
    }
    public static function metadataForInvitations($invitations, $idAttribute) {
        return parent::metadataForInvitations($invitations, $idAttribute);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
