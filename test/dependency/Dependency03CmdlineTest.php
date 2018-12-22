<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Tests JavaScript dependencies for the other half of the JavaScript files.
 * Split into two tests to avoid timeout.
 */
class Dependency03CmdlineTest extends CmdlineTestCase {

    public function setUp() {
        list($this->fileToRequires, $this->requireToFiles, $this->fileToProvides, $this->provideToFiles) = XG_TestHelper::buildDependencyGraph();
    }

    public function testUnnecessaryRequires() {
        $files = XG_TestHelper::globr(NF_APP_BASE, '*.js');
        $n = round(count($files)/2);
        foreach (array_slice($files, $n) as $file) {
            if (strpos($file, 'js/actionicons/')) { continue; } // Workaround for BAZ-1997 [Jon Aquino 2007-02-27]
            $contents = str_replace(".' + xg.global.locale", "'", self::getFileContent($file));
            foreach (array_keys($this->provideToFiles) as $moduleName) {
                preg_match('/[^.]+$/', $moduleName, $matches);
                $moduleNameLastPart = $matches[0];
                if ($this->skippableModuleAndFile($moduleName, $file)) {
                    continue;
                }
                if ($moduleName == 'xg.photo.index._shared') { continue; }
                if ($moduleName == 'xg.index.InviteCustomizer') { continue; }
                if ($moduleName == 'xg.music.playlist.edit' && strpos($file, 'RemoveTrackLink.js') !== false) { continue; }
                if ($moduleName == 'xg.page.instance.PageEditor') { continue; }
                $this->assertFalse(!preg_match('/(?<!require..|provide..)' . str_replace('.', '\.', $moduleName) . '/', $contents) && !preg_match('/getWidgetsByType..' . $moduleNameLastPart . '/', $contents) && preg_match('/require..' . $moduleName . "'/", $contents),
                        'Unnecessary require in ' . $file . ': ' . $moduleName);
            }
        }
    }

    /**
     * Returns true if provided $moduleName and $file are skippable
     *
     * @param string $moduleName
     * @param string $file
     * @retun bool
     */
    private function skippableModuleAndFile($moduleName, $file) {
        static $skippables = null;
        if (is_null($skippables)) {
            $skippables = array(
                $_SERVER['DOCUMENT_ROOT'] . '/xn_resources/widgets/opensocial/js/embed/moduleBodyAndFooter.js' => 'xg.opensocial.embed.requests',
                $_SERVER['DOCUMENT_ROOT'] . '/xn_resources/widgets/opensocial/js/embed/sendMessageForm.js' => 'xg.index.invitation.FriendList',
            );
        }
        return isset($skippables[$file]) && $skippables[$file] == $moduleName;
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
