<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Tests JavaScript dependencies
 */
class Dependency01CmdlineTest extends CmdlineTestCase {

    public function setUp() {
        list($this->fileToRequires, $this->requireToFiles, $this->fileToProvides, $this->provideToFiles) = XG_TestHelper::buildDependencyGraph();
    }

    public function testPhpFilesWithRequires() {
        foreach ($this->phpFileToAllRequires() as $phpFile => $allRequires) {
            // ning.loader.require automatically pulls in dependencies except those constructed
            // dynamically like dojo.require('xg.index.nls.' + xg.global.locale). So we only need to check
            // the nlsRequires. [Jon Aquino 2007-02-08]
            $actual = implode('', $this->nlsRequires($this->fileToRequires[$phpFile]));
            $expected = implode('', $this->nlsRequires($allRequires));
            $this->assertEqual($expected, $actual, 'Expected [' . $expected . '] but found [' . $actual . '] in ' . $phpFile);
        }
    }

    private function nlsRequires($requires) {
        $nlsRequires = array();
        foreach ($requires as $require) {
            if (strpos($require, 'nls') !== FALSE) {
                $nlsRequires[] = "'" . str_replace('nls', 'nls.', $require) . "' . XG_LOCALE, ";
            }
        }
        $nlsRequires = array_unique($nlsRequires);
        natsort($nlsRequires);
        return $nlsRequires;
    }

    private function phpFileToAllRequires() {
        $phpFileToAllRequires = array();
        foreach ($this->fileToRequires as $file => $requires) {
            if (strpos($file, 'php') === FALSE) { continue; }
            $phpFileToAllRequires[$file] = $this->allRequires($requires);
        }
        return $phpFileToAllRequires;
    }

    private function allRequires($requires) {
        $allRequires = array_merge(array(), $requires);
        foreach ($requires as $require) {
            // Prevent infinite loop caused by circular dependency [Jon Aquino 2008-03-05]
            if ($this->isKnownCircularDependency($require)) { continue; }
            // Workaround for BAZ-1997 [Jon Aquino 2007-02-27]
            if (count($this->provideToFiles[$require]) == 2 && strpos($this->provideToFiles[$require][0], 'bulk.js') !== FALSE && strpos($this->provideToFiles[$require][1], 'BulkActionLink.js') !== FALSE) { continue; }
            if (count($this->provideToFiles[$require]) == 2 && strpos($this->provideToFiles[$require][0], 'BulkActionLink.js') !== FALSE && strpos($this->provideToFiles[$require][1], 'bulk.js') !== FALSE) { continue; }
            if (count($this->provideToFiles[$require]) == 2 && strpos($this->provideToFiles[$require][0], 'actionicons.js') !== FALSE && strpos($this->provideToFiles[$require][1], 'ActionButton.js') !== FALSE) { continue; }
            $this->assertEqual(1, $count = count($this->provideToFiles[$require]), 'Expected ' . $require . ' to be provided in 1 file but it was provided in ' . $count);
            $requires = $this->fileToRequires[$this->provideToFiles[$require][0]];
            if (! $requires) { continue; }
            $allRequires = array_merge($allRequires, $this->allRequires($requires));
        }
        return $allRequires;
    }

    private function isKnownCircularDependency($require) {
        return $require == 'xg.shared.FriendLink'
            || $require == 'xg.page.instance.PageEditor'
            || $require == 'xg.shared.CountUpdater';
    }


}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
