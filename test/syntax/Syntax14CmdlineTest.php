<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax14CmdlineTest extends CmdlineTestCase {

    public function testChangeAsteriskRequiredFieldsToIndicateRequiredFields() {
        // BAZ-4577  [Jon Aquino 2007-09-26]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'asteriskRequiredFields') === false) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                $this->assertTrue(strpos($line, 'asteriskRequiredFields') === false, $line . ' - ' . $file . ' line ' . $i);
            }
        }
    }

    public function testSpecifyPreviousUrl() {
        $pattern = "@'ningId'|'termsOfService'|'privacyPolicy'|'problemsSigningIn'|'requestPasswordReset'|'passwordResetSent'@";
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'quickadd/video.php') !== false) { continue; }
            if (strpos($file, '_iphone.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'buildUrl') === false) { continue; }
            if (! preg_match($pattern, $contents)) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, 'buildUrl') === false) { continue; }
                $this->assertTrue(! preg_match($pattern, $line) || strpos($line, 'noBack') || strpos($line, 'previousUrl'), $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

    public function testProfileUrl() {
        // BAZ-4704 [Jon Aquino 2007-10-02]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, "profiles/'") === false) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, 'if ($path == \'/profiles/\') { return true; }') !== false) { continue; }
                $this->assertTrue(strpos($line, "profiles/'") === false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

    public function testBaz5378() {
        // Check class_exists before XG_App::includeFileOnce('/lib/XG_MessageCatalog_....php')  [Jon Aquino 2007-11-27]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            $previousLine = '';
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, 'XG_MessageCatalog_') !== false && strpos($line, 'includeFileOnce') !== false) {
                    $this->assertTrue(strpos($previousLine, 'class_exists') !== false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
                }
                $previousLine = $line;
            }
        }
    }

    public function testAlwaysReturnTotalCountHasArgument() {
        // BAZ-5435  [Jon Aquino 2007-12-03]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $this->assertTrue(strpos(self::getFileContent($file), 'alwaysReturnTotalCount()') === false, $file);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
