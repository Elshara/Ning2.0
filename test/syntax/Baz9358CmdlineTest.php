<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Baz9358CmdlineTest extends CmdlineTestCase {

    public function testUseXDollarInsteadOfDollar() {
        $frinkJQueryStillExists = file_exists(NF_APP_BASE . '/xn_resources/widgets/profiles/js/profile/jquery-1.2.5.min.js');
        foreach(array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.js'), XG_TestHelper::globr(NF_APP_BASE, '*.php')) as $file) {
            // TODO: remove this once addressed (per Andrey)
            if ($_SERVER['DOCUMENT_ROOT'] . '/xn_resources/widgets/index/js/tablayout/edit.js' == $file) { continue; }

			if (mb_strpos($file, '_iphone') !== false) { continue; } // iphone uses $()
            if (mb_strpos($file, '/test') !== false) { continue; }
            if (mb_strpos($file, 'widgets/lib/js') !== false) { continue; }
            if (mb_strpos($file, 'PluginDetect') !== false) { continue; }
            if (mb_strpos($file, 'iui.js') !== false) { continue; }
            if (mb_strpos($file, 'ui.sortable') !== false) { continue; }
            if (strpos($file, 'xn_resources/widgets/lib/core.min.js') !== false) { continue; }
            if ($frinkJQueryStillExists && mb_strpos($file, 'xn_resources/widgets/profiles/js/profile/editLayout.js') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (mb_strpos($contents, '$(') === FALSE && mb_strpos($contents, '$.') === FALSE) { continue; }
            $contents = str_replace(array('xg.$$', 'xg.$', 'x$', 'Use $$'), '', $contents);
            if (mb_strpos($contents, '$(') === FALSE && mb_strpos($contents, '$.') === FALSE) { continue; }
            // reset lineNumber
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                $this->assertTrue(mb_strpos($line, '$(') === FALSE, $line . ' ' . $file . ' ' . $lineNumber . ' ***');
                $this->assertTrue(mb_strpos($line, '$.') === FALSE, $line . ' ' . $file . ' ' . $lineNumber . ' ***');
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
