<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class XG_AppConstantTest extends CmdlineTestCase {

    /**
     * Checks that all constants passed to XN_App::constant() exist.
     */
    public function testConstant() {
        $lowercaseRelativeUrl = strtolower(XN_Application::load()->relativeUrl);
        $files = array();
        $constants = array();
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            $files[basename($file)] = preg_replace('@.*' . $lowercaseRelativeUrl . '@u', '', $file);
            if (mb_stripos($file, '/test') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (mb_stripos($contents, 'XG_App::constant') === FALSE) { continue; }
            preg_match_all('@XG_App::constant\(.([^)]+?).\)@u', $contents, $matches);
            $constants = array_merge($constants, $matches[1]);
        }
        foreach (array_unique($constants) as $constant) {
            list($class, ) = explode('::', $constant);
            $file = $files[$class . '.php'];
            $this->assertTrue(mb_strlen($file) > 0);
            XG_App::includeFileOnce($file);
            $this->assertTrue(constant($constant) !== NULL, $constant);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
