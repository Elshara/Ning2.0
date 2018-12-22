<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax09CmdlineTest extends CmdlineTestCase {

    public function testXgHtmlNotPassedToXnhtmlentities() {
        // Verifies that xg_html() is not found among the arguments passed to xnhtmlentities().
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'xnhtmlentities') === false) { continue; }
            if (strpos($contents, 'xg_html') === false) { continue; }
            if (preg_match_all('@xnhtmlentities\(' . XG_TestHelper::NESTED_PARENTHESES_PATTERN . '\)@ui', $contents, $matches)) {
                foreach ($matches[0] as $fullMatch) {
                    $this->assertTrue(strpos($fullMatch, 'xg_html') === false, $fullMatch . ' ' . $file);
                }
            }
        }
    }

    public function testXgHtmlNotPassedToXgAutodiscoveryLink() {
        // Verifies that xg_html() is not found among the arguments passed to xg_autodiscovery_link().
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'xg_autodiscovery_link') === false) { continue; }
            if (strpos($contents, 'xg_html') === false) { continue; }
            if (preg_match_all('@xg_autodiscovery_link\(' . XG_TestHelper::NESTED_PARENTHESES_PATTERN . '\)@ui', $contents, $matches)) {
                foreach ($matches[0] as $fullMatch) {
                    $this->assertTrue(strpos($fullMatch, 'xg_html') === false, $fullMatch . ' ' . $file);
                }
            }
        }
    }

    public function testFullNameEntityEncoded() {
        // Verifies that fullName(), getFullName(), and xg_username() output is passed
        // through xnhtmlentities(), to prevent XSS attacks [Jon Aquino 2007-12-13]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, '_text.php') !== false) { continue; }
            if (strpos($file, 'FullNameHelper') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'ullName(') === false && strpos($contents, 'xg_username(') === false) { continue; }
            $contents = preg_replace('@(?:entities|qh|xg_text|XG_Message::from|setFullName)\(' . XG_TestHelper::NESTED_PARENTHESES_PATTERN . '\)@u', '', $contents);
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, 'function') !== false) { continue; }
                if (strpos($line, 'scheduleDenormalizeFullName') !== false) { continue; }
                if (strpos($line, ' = ') !== false) { continue; }
                if (strpos($line, "'name' =>") !== false) { continue; }
                if (strpos($line, "'displayName' =>") !== false) { continue; }
                if (strpos($line, 'return xg_username($p);') !== false) { continue; }
                if (strpos($line, "'fullName' =>") !== false) { continue; }
                if (strpos($line, "'organizedBy'") !== false) { continue; }
                if (strpos($line, "'linkText'") !== false) { continue; }
                if (strpos($line, "'ORGANIZED'") !== false) { continue; }
                if (strpos($line, "imap_rfc822_write_address") !== false) { continue; }
                if (strpos($line, "denormalizeFullName") !== false) { continue; }
                if (strpos($line, "xg_headline(xg_username(") !== false) { continue; }
                if (basename($file) == 'XG_MessageHelper.php') {
                    if (strpos($line, 'return xg_username($screenName);') !== false) { continue; }
                    if (strpos($line, 'return xg_excerpt(xg_username($screenName),36);') !== false) { continue; }
                }
                // TODO: Figure out a better way to do these exemptions [Travis S. 2008-10-02]
                if (strpos($file, 'profiles/templates/profile/showPending.php') !== false && strpos($line, 'xg_headline(') !== false) { continue; }
                $this->assertTrue(strpos($line, 'ullName(') === false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
                $this->assertTrue(strpos($line, 'xg_username(') === false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
