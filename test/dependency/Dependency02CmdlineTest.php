<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Tests JavaScript dependencies for half of the JavaScript files.
 * Split into two tests to avoid timeout.
 */
class Dependency02CmdlineTest extends CmdlineTestCase {

    public function setUp() {
        list($this->fileToRequires, $this->requireToFiles, $this->fileToProvides, $this->provideToFiles) = XG_TestHelper::buildDependencyGraph();
    }

    public function testUnnecessaryRequires() {
        $files = XG_TestHelper::globr(NF_APP_BASE, '*.js');
        $n = round(count($files)/2);
        foreach (array_slice($files, 0, $n) as $file) {
            if (strpos($file, '/xn_resources/instances/shared/js/messagecatalogs') !== false) { continue; }
            if (strpos($file, 'js/actionicons/') !== false) { continue; } // Workaround for BAZ-1997 [Jon Aquino 2007-02-27]
            // TODO: remove this special case once tablayout/edit.js is cleaned up
            if ($_SERVER['DOCUMENT_ROOT'] . '/xn_resources/widgets/index/js/tablayout/edit.js' == $file) { continue; }
            $contents = str_replace(".' + xg.global.locale", "'", self::getFileContent($file));
            if (mb_strpos($contents, '@explicit') !== FALSE) { $contents = preg_replace('#.*@explicit.*#u', '', $contents); }
            foreach (array_keys($this->provideToFiles) as $moduleName) {
                preg_match('/[^.]+$/', $moduleName, $matches);
                $moduleNameLastPart = $matches[0];
                if ($moduleName == 'xg.index.language.jslint') { continue; }
                if ($moduleName == 'xg.events.Scroller') { continue; }
                if ($moduleName == 'xg.photo.index._shared') { continue; }
                if ($moduleName == 'xg.page.instance.PageEditor') { continue; }
                if ($moduleName == 'xg.index.InviteCustomizer') { continue; }
                if ($moduleName == 'xg.music.playlist.edit' && strpos($file, 'RemoveTrackLink.js') !== false) { continue; }
                if ($moduleName == 'xg.shared.BazelImagePicker' && strpos($file, 'quickadd/event.js') !== false) { continue; }
                if ($moduleName == 'xg.shared.SimpleToolbar' && preg_match('/createWidget..SimpleToolbar/', $contents)) { continue; }
                $this->assertFalse(!preg_match('/(?<!require..|provide..)' . str_replace('.', '\.', $moduleName) . '/', $contents) && !preg_match('/getWidgetsByType..' . $moduleNameLastPart . '/', $contents) && preg_match('/require..' . $moduleName . "'/", $contents),
                        'Unnecessary require in ' . $file . ': ' . $moduleName);
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
