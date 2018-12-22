<?php
/**
 * 	Test for the dojo.addOnLoad calls, which are deprecated in favor of xg.addOnRequire
 * 	(or any other functions that we may want to add)
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax30Test extends UnitTestCase {
    public function testDojoAddOnLoad(){
        foreach (XG_TestHelper::globr(NF_APP_BASE,'*.php') as $name) {
            if (strpos($name, 'index/templates/embed/footer.php') !== FALSE) { continue; }
            $this->assertFileDoesNotContainDojoAddOnLoad($name);
        }
        foreach (XG_TestHelper::globr(NF_APP_BASE,'*.js') as $name) {
            if (strpos($name, 'dojo-full.js') !== FALSE) { continue; }
            if (strpos($name, 'adapter/core.js') !== FALSE) { continue; }
            if (strpos($name, 'core.min.js') !== FALSE) { continue; }
            $this->assertFileDoesNotContainDojoAddOnLoad($name);
        }
    }
    //
    public function assertFileDoesNotContainDojoAddOnLoad($name) { # void
        if ($this->_shouldSkipFile($name)) {
            return;
        }
        return $this->assertStringDoesNotContainDojoAddOnLoad(
            file_get_contents($name),
            "$name should not contain dojo.addOnLoad() call"
        );
    }

    public function assertStringDoesNotContainDojoAddOnLoad($contents, $message) {
        return $this->assertNoUnwantedPattern('/dojo\.addOnLoad/', $contents, $message);
    }

    private function _shouldSkipFile($name) {
        static $allowed_names = array(
            'index/templates/embed/footer.php',
            'dojo-full.js',
            'adapter/core.js',
            'xn_resources/widgets/lib/js/dojo-adapter/all.js',
            'xn_resources/widgets/lib/js/dojo-adapter/src/core.js',
        );
        foreach ($allowed_names as $allowed_name) {
            if (preg_match('/test|dojo\.js/', $name)
                || substr($name, -strlen($allowed_name)) == $allowed_name
            ) {
                return true;
            }
        }
        return false;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
