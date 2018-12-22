<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax17CmdlineTest extends CmdlineTestCase {

    public function testRemoveProbableScreenName() {
        // Martin says probableScreenName() should no longer be used [Jon Aquino 2007-09-21]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'probableScreenName') === false) { continue; }
            $previousLine = '';
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, 'ning._.probableScreenName') !== false && strpos($file, 'XG_HtmlLayoutHelper.php') !== false) { continue; }
                $this->assertTrue(strpos($line, 'probableScreenName') === false , $this->escape($line) . ' - ' . $file . ' line ' . $i);
                $previousLine = $line;
            }
        }
    }

    public function testNoIncludeOrRequire() {
        // See "include/require'ing PHP files in Bazel", http://clearspace.ninginc.com/clearspace/docs/DOC-1417  [Jon Aquino 2008-01-22]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, '/error.php') !== false) { continue; }
            if (strpos($file, '/opensocial.php') !== false) { continue; }
            if (strpos($file, '/runner.php') !== false) { continue; }
            if (strpos($file, '/index.php') !== false) { continue; }
            if (strpos($file, '/lib/scripts/') !== false) { continue; }
            if (strpos($file, '/lib/XG_FacebookApp.php') !== false) { continue; } // Loaded outside of Bazel - see bazel-2263 in Crucible [Jon Aquino 2008-08-15]
            if (strpos($file, '/lib/bots/') !== false) { continue; }
            if (strpos($file, '/ext/facebook/facebook.php') !== false) { continue; }
            if (strpos($file, '/widgets/index/templates/authorization/privacyPolicy.php') !== false) { continue; } // BAZ-9067
            if (strpos($file, '/widgets/index/templates/authorization/termsOfService.php') !== false) { continue; } // BAZ-9067
            $contents = self::getFileContent($file);
            if (basename($file) == 'Admin_DomainRedirectionHelper.php') { $contents = str_replace('require_once NF_APP_BASE . \'/lib/index.php\';";', '', $contents); }
            if (basename($file) == 'XG_FacebookApp.php') { $contents = str_replace("require_once NF_APP_BASE . '/lib/ext/facebook/facebook.php';", '', $contents); }
            preg_match_all('@^\s*(require|include).*@m', $contents, $matches);
            foreach ($matches[0] as $match) {
                if (preg_match('/\/\*\* @allowed \*\//', $match)) { continue; }
                if (strpos($match, 'need to preserve scope') !== false) { continue; }
                if (strpos($match, '_APPFS') !== false) { continue; }
                $this->fail($file . ' - ' . $match);
            }
        }
    }

    public function testProfileSetLoadAccompaniedByExistenceCheck() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $filename) {
            if (strpos($filename, '/test') !== false) { continue; }
            if (strpos($filename, '/lib/scripts/eoc167a.php') !== false) { continue; }
            $contents = self::getFileContent($filename);
            if (strpos($contents, 'XN_ProfileSet::load(') === false) { continue; }
            $lineNumber = 0;
            $previousLine = '';
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                $surroundingLines = $previousLine . ' ' . $line;
                if (! preg_match('@^\*@u', trim($line))
                        && strpos($surroundingLines, '$set = XN_ProfileSet::load($setName);') === false
                        && strpos($previousLine, 'return XN_ProfileSet::load(self::profileSetId($group->id));') === false
                        && strpos($previousLine, 'echo "$setName') === false
                        && strpos($line, '// Build the message we\'re going to send') === false
                        && strpos($previousLine, 'XN_ProfileSet::load(') !== false) {
                    $this->assertTrue(preg_match('@\bif\b@u', $surroundingLines), 'Should check that XN_ProfileSet::load() exists: ' . $this->escape($surroundingLines) . ' *** Line ' . $lineNumber . ', ' . $filename);
                }
                $previousLine = $line;
            }
        }
    }

    public function testUseConstantForMembersAlias() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $filename) {
            if (strpos($filename, '/test') !== false) { continue; }
            if (strpos($filename, '/lib/scripts/eoc167a.php') !== false) { continue; }
            $contents = self::getFileContent($filename);
            if (strpos($contents, 'XN_ProfileSet::') === false) { continue; }
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if ((strpos($line, 'XN_ProfileSet::') !== false) &&
                    (strpos($line, 'members') !== false)) {
                       $this->fail("Use XN_ProfileSet::USERS: {$this->escape($line)} **** Line $lineNumber, $filename");
                    }
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
