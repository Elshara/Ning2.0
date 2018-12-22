<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax25CmdlineTest extends CmdlineTestCase {

    public function testUseXgCdnForXnResourcesUrls() {
        // BAZ-7565  [Jon Aquino 2008-05-13]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, '.php') === false && strpos($file, '.js') === false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, '/xn_resources/') === false && strpos($contents, 'buildResourceUrl') === false) { continue; }
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (mb_strpos($line, '/xn_resources/') === false && mb_strpos($line, 'buildResourceUrl') === false) { continue; }
                if (mb_strpos($line, 'xg_cdn') !== FALSE) { continue; }
                if (mb_strpos($line, 'xg.shared.util.cdn') !== FALSE) { continue; }
                if (mb_strpos($file, '_footer.php') !== FALSE && mb_strpos($line, 'spacer.gif') !== FALSE) { continue; }
                if (mb_strpos($line, 'dojo.uri.dojoUri') !== FALSE) { continue; }
                if (mb_strpos($line, '.tpl') !== FALSE) { continue; }
                if (mb_strpos($line, 'com.ning.NoteEditor.nocache.js') !== FALSE) { continue; }
                if (mb_strpos($line, 'files generated by bazel') !== FALSE) { continue; }
                if (mb_strpos($line, 'rpc_relay.html') !== FALSE) { continue; }
                if (mb_strpos($line, 'setModulePrefix') !== FALSE) { continue; }
                if (mb_strpos($line, 'ning.loader.setPrefixPattern') !== FALSE) { continue; }
                if (mb_strpos($line, ".css'") !== false) { continue; }
                if (mb_strpos($line, 'typographyCssUrl') !== false) { continue; }
                if (mb_strpos($line, "passing to buildResourceUrl") !== false) { continue; }
                if (mb_strpos($line, "-config.xml") !== false) { continue; } // BAZ-3833 [Jon Aquino 2008-05-19]
                if (mb_strpos($line, 'var_dump(Video_VideoHelper') !== false) { continue; }
                if (mb_strpos($line, '%pageBgImage%') !== false) { continue; }
                if (mb_strpos($line, '%headBgImage%') !== false) { continue; }
                if (mb_strpos($line, '%siteBgImage%') !== false) { continue; }
                if (mb_strpos($line, '@param') !== false) { continue; }
                if (mb_strpos($line, 'no cdn!') !== false) { continue; }
                if (mb_strpos($line, 'FLICKR_SCREENSHOT_GETKEY') !== false) { continue; }
                if (mb_strpos($line, 'FLICKR_SCREENSHOT_KEYINFO') !== false) { continue; }
                if (mb_strpos($line, 'FLICKR_SCREENSHOT_SETUPKEY') !== false) { continue; }
                if (mb_strpos($line, 'FLICKR_SCREENSHOT_KEY') !== false) { continue; }
                if (mb_strpos($line, 'DOCUMENT_ROOT') !== false) { continue; }
                if (mb_strpos($line, 'W_INCLUDE_PREFIX') !== false) { continue; }
                if (mb_strpos($line, 'NF_APP_BASE') !== false) { continue; }
                if (mb_strpos($line, 'Index_AppearanceHelper::setThemeCss($user, str_replace(\'url(/xn_resources\', \'url(http://\' . $_SERVER[\'HTTP_HOST\'] . \'/xn_resources\', Index_AppearanceHelper::getThemeCss($user)));') !== false) { continue; }
                if (mb_strpos($line, '$cdnUrl = preg_replace(\'@.*/xn_resources(.*)@u\', \'http://\' . XN_AtomHelper::HOST_APP(\'static\') . \'/\' . XN_Application::load()->relativeUrl . \'$1\', $url);') !== false) { continue; }
                $this->fail($line . ' ' . $file . ' ' . $lineNumber);
            }
        }
    }

    public function testUseXgCdnInsteadOfXgAkamaiUrl() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (basename($file) == 'XG_TemplateHelpers.php') { $contents = str_replace('function xg_akamai_url', '', $contents); }
            $this->assertTrue(mb_strpos($contents, 'xg_akamai_url') === false, $file);
        }
    }

    public function testNoGpl() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'xg_masthead.gif') !== false) { continue; }
            if (strpos($file, 'termsOfService.php') !== false) { continue; }
            // jquery is available under dual license including MIT, which is okay
            if (strpos($file, 'jquery') !== false) { continue; }
            if (strpos($file, 'xn_resources/widgets/lib/core.min.js') !== false) { continue; }
            if (strpos($file, 'ui.sortable-uncompressed.js') !== false) { continue; }
            if (strpos($file, 'XG_LangHelper') !== false) { continue; } // Noted [Jon Aquino 2008-05-24]
            $contents = self::getFileContent($file);
            $this->assertTrue(mb_strpos($contents, 'GPL') === false, $file);
        }
    }

    public function testConfigCachingEnabled() {
        $this->assertTrue(TestConfigCachingApp::isCachingEnabled());
    }

    public function testEmailActivityPref() {
        // emailActivityPref and emailModeratedPref are not Y/N values (BAZ-8043) [Jon Aquino 2008-06-17]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (mb_strpos($contents, 'emailActivityPref') === FALSE && mb_strpos($contents, 'emailModeratedPref') === FALSE) { continue; }
            $this->assertNoPattern('@(emailActivityPref|emailModeratedPref).*\b(Y|N)\b@u', $contents, $file);
        }
    }

}

class TestConfigCachingApp extends XG_ConfigCachingApp {
    public function isCachingEnabled() { return parent::$cachingEnabled; }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';