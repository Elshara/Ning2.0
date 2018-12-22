<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class AuthorizationControllerTest extends UnitTestCase {

    public function testOverridePrivacyCoversAllActions() {
        $authorizationControllerCode = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/widgets/index/controllers/AuthorizationController.php');
        $this->assertTrue(preg_match('@action_overridePrivacy.*?action_@s', $authorizationControllerCode, $matches));
        $overridePrivacyCode = $matches[0];
        $this->assertTrue(preg_match_all('@action_([^(]+)@i', $authorizationControllerCode, $matches));
        foreach ($matches[1] as $actionName) {
            if ($actionName == 'overridePrivacy') { continue; }
            if ($actionName == 'profileInfoForm') { continue; }
            if ($actionName == 'footer') { continue; }
            if ($actionName == 'footerPrivateSignIn') { continue; }
            if ($actionName == 'privateSignInFooter') { continue; }
            if ($actionName == 'applicationTos') { continue; }
            if ($actionName == 'developerTos') { continue; }
            if (preg_match('/_iphone$/', $actionName)) { continue; }
            $this->assertTrue(strpos($overridePrivacyCode, "'$actionName'") !== false, $actionName);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
