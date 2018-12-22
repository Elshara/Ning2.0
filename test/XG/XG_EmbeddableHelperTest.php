<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');

class XG_EmbeddableHelperTest extends UnitTestCase {

    public function testSpecialRequire() {
        // This test ensures that the explicit require will not be removed by David's automated string replacement [Jon Aquino 2007-06-30]
        $contents = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/lib/XG_EmbeddableHelper.php');
        $this->assertTrue(strpos($contents, 'XG_App::includeFileOnce(\'/widgets/music/lib/helpers/Music_EmbeddableHelper.php\')') !== false);
    }

    public function testSwfHasVersionParameter() {
        foreach(array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.php'), XG_TestHelper::globr(NF_APP_BASE, '*.js')) as $file) {
            if (strpos($file, '/lib/ext/facebook') !== false) { continue; }
            if (strpos($file, '/test') !== false) { continue; }
            $contents = file_get_contents($file);
            if (strpos($contents, 'swf') !== false) {
                $lineNumber = 0;
                foreach (explode("\n", $contents) as $line) {
                    $lineNumber++;
                    if (strpos($line, '$player_url = $app_url . $this->_widget->buildResourceUrl(\'swf/xspf_player.swf\');') !== false) { continue; }
                    if (strpos($line, '$this->swfUrl = xg_absolute_url($this->_widget->buildResourceUrl(\'flvplayer/flvplayer.swf\'));') !== false) { continue; }
                    if (strpos($line, 'xg_cdn') !== false) { continue; }
                    if (strpos($line, 'xg_akamai_url') !== false) { continue; }
                    if (strpos($line, 'var_dump') !== false) { continue; }
                    if (strpos($line, 'googleplayer.swf') !== false) { continue; }
                    if (strpos($line, '_clipboard.swf') !== false) { continue; }
                    if (strpos($line, "'.swf'") !== false) { continue; }
                    if (strpos($line, 'buttonplayer.swf') !== false) { continue; }
                    if (strpos($line, 'flvplayer.swf') !== false && strpos($line, "dojo.byId('video-url')") !== false) { continue; }
                    if (strpos($line, '.swf') !== false && strpos($line, 'ersion') === false) {
                        $this->assertTrue(FALSE, $line . ' ' . $file . ' ' . $lineNumber . ' ***');
                    }
                }
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
