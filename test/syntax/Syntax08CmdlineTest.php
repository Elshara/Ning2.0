<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax08CmdlineTest extends CmdlineTestCase {

    public function testRemoveRememberedInvitationCode() {
        // BAZ-4530 [Jon Aquino 2007-09-25]
        $searches = array('acceptIfPresent', 'remembered invitation', 'rememberCookie', 'emptyListValue', 'rememberInvitation', 'forgetInvitation', 'forgetAllInvitations', 'getRememberedInvitations', 'getRememberedInvitationKeys', 'setRememberCookie', 'getRememberCookie', 'keepInvites');
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'php') === false && strpos($file, 'js') === false) { continue; }
            $contents = self::getFileContent($file);
            $found = false;
            foreach ($searches as $search) {
                if (strpos($contents, $search) !== false) {
                    $found = true;
                    break;
                }
            }
            if (! $found) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                $found = false;
                foreach ($searches as $search) {
                    if (strpos($line, $search) !== false) {
                        $found = true;
                        break;
                    }
                }
                $this->assertFalse($found, $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

    public function testRemoveXG_CoverageTesterProbes() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertIdentical(false, strpos($contents, 'XG_CoverageTester::probe'), $file);
        }
    }

    public function testUseProfileAddressForFriendsUrl() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'friends/') === false) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if ($this->isLineComment($line)) { continue; }
                if (strpos($line, 'friends/') !== false && strpos($line, 'profileAddress') === false) {
                    if ($this->isLineComment($line)) { continue; }
                    if (strpos($line, 'Used in friends/list') !== false) { continue; }
                    $this->assertTrue(false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
                }
            }
        }
    }

    public function testDoNotUseLoadOrCreate() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'AuthorizationController.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'User::loadOrCreate') === false) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, 'User::loadOrCreate') !== false) {
                    if (strpos($line, '$ownerUserObject') !== false) { continue; }
                    if (strpos($line, 'inside User::loadOrCreate') !== false) { continue; }
                    $this->assertTrue(false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
                }
            }
        }
    }

    public function testWrongUrlForTermsOfServiceAndPrivacyPolicy() {
        // BAZ-4619  [Jon Aquino 2007-09-26]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'termsOfService') === false && strpos($contents, 'privacyPolicy') === false) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                $this->assertTrue(strpos($line, 'termsOfService') === false || strpos($line, 'index') === false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
                $this->assertTrue(strpos($line, 'privacyPolicy') === false || strpos($line, 'index') === false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

    public function testRemoveNingIdIcon() {
        // BAZ-4654  [Jon Aquino 2007-09-26]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'ningid') === false) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                $this->assertTrue(! preg_match('@span.*ningid@', $line), $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
