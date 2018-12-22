<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_AuthorizationHelper.php');

class Index_AuthorizationHelperTest extends UnitTestCase {

    public function testNextActionOnError() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);

        // signIn, bad password
        $this->assertEqual($json->encode(array('forward', 'signIn', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => false,
                'isPending' => false,
                'isMember' => false))));
        $this->assertEqual($json->encode(array('redirect', 'signUpNingUser', array('target' => 'http://example.org', 'groupToJoin' => null, 'emailAddress' => 'abe@foo.com'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false))));
        $this->assertEqual($json->encode(array('forward', 'signIn', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => true))));
        $this->assertEqual($json->encode(array('forward', 'signIn', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => true,
                'isMember' => false)))); // pending

        // signUp, bad password
        $this->assertEqual($json->encode(array('forward', 'signUp', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUp',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => false,
                'isPending' => false,
                'isMember' => false))));
        $this->assertEqual($json->encode(array('redirect', 'signUpNingUser', array('target' => 'http://example.org', 'groupToJoin' => null, 'emailAddress' => 'abe@foo.com'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUp',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false))));
        $this->assertEqual($json->encode(array('forward', 'signUp', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUp',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => true))));
        $this->assertEqual($json->encode(array('forward', 'signUp', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUp',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => true,
                'isMember' => false)))); // pending

        // signUpNingUser, bad password
        $this->assertEqual($json->encode(array('forward', 'signUpNingUser', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUpNingUser',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => false,
                'isPending' => false,
                'isMember' => false))));
        $this->assertEqual($json->encode(array('forward', 'signUpNingUser', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUpNingUser',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false))));
        $this->assertEqual($json->encode(array('forward', 'signUpNingUser', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUpNingUser',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => true))));
        $this->assertEqual($json->encode(array('forward', 'signUpNingUser', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUpNingUser',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => true,
                'isMember' => false)))); // pending

        // reset password, bad password
        $this->assertEqual($json->encode(array('forward', 'editPassword', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => false,
                'isPending' => false,
                'isMember' => false))));
        $this->assertEqual($json->encode(array('forward', 'editPassword', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false))));
        $this->assertEqual($json->encode(array('forward', 'editPassword', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => true))));
        $this->assertEqual($json->encode(array('forward', 'editPassword', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => true,
                'isMember' => false)))); // pending
    }

    public function testNextActionOnSuccess() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);

        // signIn
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org', 'groupToJoin' => null))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org?xgi=12345&xgkc=1', 'groupToJoin' => null, 'xgi' => '12345', 'xgkc' => '1'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org?xgi=12345&xgkc=1',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'http://example.org', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => true,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'http://example.org', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => true,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest')))); // pending

        // signUp
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org', 'groupToJoin' => null, 'newNingUser' => '1'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUp',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => false,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org', 'groupToJoin' => null, 'newNingUser' => '1'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUp',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'http://example.org', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUp',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => true,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'http://example.org', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUp',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => true,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest')))); // pending

        // signUpNingUser
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org', 'groupToJoin' => null))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUpNingUser',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'http://example.org', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUpNingUser',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => true,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'http://example.org', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signUpNingUser',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => true,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest')))); // pending

        // reset password
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://' . $_SERVER['HTTP_HOST'] . '/', 'groupToJoin' => null))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'http://' . $_SERVER['HTTP_HOST'] . '/', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => true,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        $this->assertEqual($json->encode(array('redirect', 'http://' . $_SERVER['HTTP_HOST'] . '/', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => true,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest')))); // pending
    }

    public function testNextActionForPrivateNetworks() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        // Invite only, has invitation, good password
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org?xgi=12345&xgkc=1', 'groupToJoin' => null, 'xgi' => '12345', 'xgkc' => '1'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org?xgi=12345&xgkc=1',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => false,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Invite only, no invitation, good password
        $this->assertEqual($json->encode(array('redirect', 'invitationOnly', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => false,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Invite only, has invitation, bad password
        $this->assertEqual($json->encode(array('forward', 'signIn', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org?xgi=12345&xgkc=1',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => false,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Invite only, no invitation, bad password
        $this->assertEqual($json->encode(array('forward', 'signIn', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => false,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Sign-up allowed, has invitation, good password
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org?xgi=12345&xgkc=1', 'groupToJoin' => null, 'xgi' => '12345', 'xgkc' => '1'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org?xgi=12345&xgkc=1',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Sign-up allowed, no invitation, good password
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org', 'groupToJoin' => null))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Sign-up allowed, has invitation, bad password
        $this->assertEqual($json->encode(array('redirect', 'signUpNingUser', array('target' => 'http://example.org?xgi=12345&xgkc=1', 'groupToJoin' => null, 'emailAddress' => 'abe@foo.com'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org?xgi=12345&xgkc=1',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Sign-up allowed, no invitation, bad password
        $this->assertEqual($json->encode(array('redirect', 'signUpNingUser', array('target' => 'http://example.org', 'groupToJoin' => null, 'emailAddress' => 'abe@foo.com'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => 'http://example.org',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
    }

    public static function getUnusedInvitation($target) {
        return strpos($target, '12345') !== false;
    }

    public function testNextActionForResetPasswordOnPrivateNetworks() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        // Invite only, has invitation, good password
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org?xgi=12345&xgkc=1', 'groupToJoin' => null, 'xgi' => '12345', 'xgkc' => '1'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => 'http://example.org?xgi=12345&xgkc=1',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => false,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Invite only, no invitation, good password
        $this->assertEqual($json->encode(array('redirect', 'invitationOnly', null)), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => false,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Invite only, has invitation, bad password
        $this->assertEqual($json->encode(array('forward', 'editPassword', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => 'http://example.org?xgi=12345&xgkc=1',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => false,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Invite only, no invitation, bad password
        $this->assertEqual($json->encode(array('forward', 'editPassword', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => false,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Sign-up allowed, has invitation, good password
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://example.org?xgi=12345&xgkc=1', 'groupToJoin' => null, 'xgi' => '12345', 'xgkc' => '1'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => 'http://example.org?xgi=12345&xgkc=1',
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Sign-up allowed, no invitation, good password
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://' . $_SERVER['HTTP_HOST'] . '/', 'groupToJoin' => null))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Sign-up allowed, has invitation, bad password
        $this->assertEqual($json->encode(array('forward', 'editPassword', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => 'http://example.org?xgi=12345&xgkc=1',
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
        // Sign-up allowed, no invitation, bad password
        $this->assertEqual($json->encode(array('forward', 'editPassword', array('password' => 'Bad Password'))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'editPassword',
                'emailAddress' => null,
                'target' => null,
                'groupToJoin' => null,
                'errors' => array('password' => 'Bad Password'),
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false,
                'invitationHelperClass' => 'Index_AuthorizationHelperTest'))));
    }

    public function testDefaultTarget() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode(array('redirect', 'newProfile', array('target' => 'http://' . $_SERVER['HTTP_HOST'] . '/', 'groupToJoin' => null))), $json->encode(Index_AuthorizationHelper::nextAction(array(
                'formAction' => 'signIn',
                'emailAddress' => 'abe@foo.com',
                'target' => null,
                'groupToJoin' => null,
                'errors' => null,
                'signUpAllowed' => true,
                'isNingUser' => true,
                'isPending' => false,
                'isMember' => false))));
    }

    public function testAdjustPolicyLinks1() {
        $html = '<a href="/privacy.php">Privacy Policy</a> <a href="/dmca-notice.php">Ning DMCA notice page</a> <a href="http://about.ning.com/tos.php">Ning User Agreement</a>';
        $expected = '<a href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'privacyPolicy', array('previousUrl' => xg_absolute_url('/foo')))) . '">Privacy Policy</a> <a href="http://about.ning.com/dmca-notice.php">Ning DMCA notice page</a> <a href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'termsOfService', array('previousUrl' => xg_absolute_url('/foo')))) . '">Ning User Agreement</a>';
        $this->assertEqual($expected, TestAuthorizationHelper::adjustPolicyLinks($html, xg_absolute_url('/foo')));
    }

    public function testAdjustPolicyLinks2() {
        $html = '<a href="/privacy.php">Privacy Policy</a> <a href="/dmca-notice.php">Ning DMCA notice page</a> <a href="http://about.ning.com/tos.php">Ning User Agreement</a>';
        $expected = '<a href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'privacyPolicy', array())) . '">Privacy Policy</a> <a href="http://about.ning.com/dmca-notice.php">Ning DMCA notice page</a> <a href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'termsOfService', array())) . '">Ning User Agreement</a>';
        $this->assertEqual($expected, TestAuthorizationHelper::adjustPolicyLinks($html, NULL));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

class TestAuthorizationHelper extends Index_AuthorizationHelper {
    public static function adjustPolicyLinks($html, $previousUrl) {
        return parent::adjustPolicyLinks($html, $previousUrl);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
