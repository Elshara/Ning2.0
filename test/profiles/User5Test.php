<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_UserHelper.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_PrivacyHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_UserHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_PrivacyHelper.php');
Mock::generate('stdClass', 'MockXN_Content', array('save'));
Mock::generate('stdClass', 'MockUser', array('hasUserCreatedProper'));

class User5Test extends BazelTestCase {

    public function tearDown() {
        $_GET['xn_debug'] = null;
    }

    public function testUseProfileAddressFunction() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = file_get_contents($file);
            if (strpos($contents, 'my->profileAddress') === false) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (preg_match('@my->profileAddress += +@', $line)) { continue; }
                if (strpos($line, 'if ($user->my->profileAddress === $_POST[\'profileAddress\']) {') !== false) { continue; }
                if (strpos($line, 'return ($user && mb_strlen($user->my->profileAddress)) ? $user->my->profileAddress : $screenName;') !== false) { continue; }
                if (strpos($line, '$user->my->searchText = self::searchText($user->my->fullName, $user->my->profileAddress);') !== false) { continue; }
                if (strpos($line, 'if (is_null($ownerUserObject->my->profileAddress)) {') !== false) { continue; }
                $this->assertTrue(strpos($line, '>my->profileAddress') === false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

    public function testScreenNames() {
        $this->assertEqual(3, count(User::screenNames(array('jon', 'david', 'David', 'david', '', null))));
    }

    public function testNoSlashAfterQuickProfileUrl() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = file_get_contents($file);
            if (strpos($contents, '::quick') === false) { continue; }
            $contents = str_replace('/>', '', $contents);
            $contents = str_replace('</', '', $contents);
            $contents = str_replace('http://', '', $contents);
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, '::quick') === false) { continue; }
                $this->assertFalse(preg_match('@::quick.*/@', $line), $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

    public function testNoParametersAfterQuickProfileUrl() {
        // Quick profile URL redirects, so parameters are discarded.
        // Use profileAddress instead [Jon Aquino 2007-10-10]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = file_get_contents($file);
            if (strpos($contents, '::quick') === false) { continue; }
            $contents = str_replace('?>', '', $contents);
            $contents = str_replace('<?', '', $contents);
            $contents = str_replace(' ? ', '', $contents);
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, '::quick') === false) { continue; }
                $this->assertFalse(preg_match('@::quick.*\?@', $line), $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

    public function testLoadMultiple() {
        XN_Debug::allowDebug();
        $_GET['xn_debug'] = 'api-comm';
        ob_start();
        $users = User::loadMultiple(array('sdjkdsh'));
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertNotEqual(0, strlen($output));
        $this->assertEqual(0, count($users));

        ob_start();
        $users = User::loadMultiple(array('sdjkdsh'));
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertEqual(0, strlen($output));
        $this->assertEqual(0, count($users));
    }

    public function testIsMember() {
        $this->assertTrue(User::isMember(XN_Profile::current()));
        $this->assertFalse(User::isMember(XN_Profile::create(null, null)));
    }

    public function testIsPending() {
        $this->assertFalse(User::isPending(XN_Profile::current()));
        $this->assertFalse(User::isPending(XN_Profile::create(null, null)));
    }

}

class TestUser extends User {
    public static function setScreenNameToUserMap($screenNameToUserMap) {
        User::$screenNameToUserMap = $screenNameToUserMap;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
