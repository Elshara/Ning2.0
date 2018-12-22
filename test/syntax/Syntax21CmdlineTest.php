<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Syntax tests continued.
 */
class Syntax21CmdlineTest extends CmdlineTestCase {

    public function testGroupAndThisGroup() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            if (strpos($file, 'Controller') !== false) { continue; }
            $contents = self::getFileContent($file);
            $contents = str_replace('protected $group;', '', $contents);
            $this->assertFalse(preg_match('@\$group\b@', $contents) && preg_match('@\$this->group\b@', $contents), 'File has both $group and $this->group: ' . $file);
        }
    }

    public function testDojoRequires() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.js') as $file) {
            if (strpos($file, '/lib/js/dojo-adapter') !== false) { continue; }
            if (strpos($file, 'profile/editLayout.js') !== false) { continue; }
            if (strpos($file, 'quickadd/bar.js') !== false) { continue; }
            if (strpos($file, 'quickadd/core.js') !== false) { continue; }
            if (strpos($file, 'quickadd/loader.js') !== false) { continue; }
            if (strpos($file, 'xn_resources/widgets/lib/core.min.js') !== false) { continue; }
            self::doTestDojoRequires($file);
        }
    }

    private function doTestDojoRequires($file) {
        $contents = self::getFileContent($file);
        $contents = str_replace("a call to xg.shared.util", "", $contents);
        $this->assertTrue(strpos($contents, 'dojo.animation.') === FALSE || strpos($contents, "dojo.require('dojo.animation"), $file);
        $this->assertTrue(strpos($contents, 'dojo.dnd.') === FALSE || strpos($contents, "dojo.require('dojo.dnd"), $file);
        $this->assertTrue(strpos($contents, 'dojo.fx.') === FALSE || preg_match("/dojo.require\(.dojo.fx/", $contents), $file);
        $this->assertTrue(strpos($contents, 'dojo.lfx.') === FALSE || preg_match("/dojo.require\(.dojo.lfx/", $contents), $file);
        $this->assertTrue(strpos($contents, 'xg.index.dom.') === FALSE || preg_match("/dojo.require\(.xg.index.dom/", $contents), $file);
    }

    public function testPhpAndJs() {
        foreach(array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.php'), XG_TestHelper::globr(NF_APP_BASE, '*.js')) as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            self::doTestPhpAndJs($file);
        }
    }

    private function doTestPhpAndJs($file) {
        $pattern = '@xg\.i18n|xg\.dom|xg\.util|xg\.widget|xn\.widget|\'xg\.widget\'|xn/static(?!/ningbar)|xg.shared.widget.video|xg.shared.widget.photo|onRequire.push@i';
        $contents = self::getFileContent($file);
        if (! preg_match($pattern, $contents)) {
            $this->assertTrue(TRUE);
        } else {
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (preg_match($pattern, $line, $matches)) {
                    if ($matches[0] == 'dojo.xml.Parse' && basename($file) == 'widget.js') { continue; }
                    if ($matches[0] == 'xn/static' && basename($file) == 'XG_HtmlLayoutHelper.php') { continue; }
                    $this->assertTrue(FALSE, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    private function format($match, $file, $line, $lineNumber) {
        return $match . ' in ' . $line . ' ' . $file . ' ' . $lineNumber . ' ***';
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
