<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Tests for enabling the HTML widget to work with Groups
 */
class GroupHtmlTest extends UnitTestCase {

    public function testPhp() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/widgets/html', '*.php') as $file) {
            $pattern = '/'
                    . 'appIsPrivate(?!.*groupIsPrivate)' // Check group privacy in addition to app privacy [Jon Aquino 2007-04-14]
                    . '|Controller.php.*(?<!checkCurrentUserCanAccess.)XN_Content::load' // Call checkCurrentUserCanAccess in actions [Jon Aquino 2007-04-14]
                    . '|Controller.php.*(?<!checkCurrentUserCanAccess.)W_Content::load' // Call checkCurrentUserCanAccess in actions [Jon Aquino 2007-04-14]
                    . '|(?<!getWidget..groups..)(?<!getWidget..main..)(?<!getWidget..profiles..)->buildUrl(?!.*video)'  // Use XG_GroupHelper::buildUrl [Jon Aquino 2007-04-16]
                    . '|isOwner(?!.*isGroupAdmin)' // Use XG_GroupHelper::isGroupAdmin [Jon Aquino 2007-04-16]
                    . '|>ownerName(?!.*isGroupAdmin)' // Use XG_GroupHelper::isGroupAdmin [Jon Aquino 2007-04-16]
                    . '|\'groups\'\)->buildUrl' // Use XG_GroupHelper::buildUrl [Jon Aquino 2007-05-02]
                    . '|\'html\'\)->buildUrl' // Use XG_GroupHelper::buildUrl [Jon Aquino 2007-05-02]
                    . '/i';
            $contents = file_get_contents($file);
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (preg_match($pattern, basename($file) . ':' . $line, $matches)) {
                    if ($matches[0] == 'IsOwner' && strpos($line, 'OWNER_DISCUSSIONS_ONLY')) { continue; } // BAZ-2663 [Jon Aquino 2007-04-25]
                    if ($matches[0] == '>ownerName' && strpos($line, '$this->app->ownerName')) { continue; } // BAZ-2663 [Jon Aquino 2007-04-25]
                    $this->assertTrue(FALSE, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    private function format($match, $file, $line, $lineNumber) {
        return $match . ' in ' . $line . ' ' . $file . ' ' . $lineNumber . ' ***';
    }

    public function testAllQueriesHaveGroupFilter() {
        $files = XG_TestHelper::globr(NF_APP_BASE . '/widgets/html', '*.php');
        $files[] = NF_APP_BASE . '/lib/XG_FileHelper.php';
        $files[] = NF_APP_BASE . '/widgets/index/models/Comment.php';
        foreach($files as $file) {
            $pattern = '/.{0,150}Query::create\(.Content(?!.{0,350}addGroupFilter).{0,100}/i';
            $contents = str_replace("\n", ' ', file_get_contents($file));
            $matchFound = preg_match($pattern, $contents, $matches);
            if ($matchFound && strpos($matches[0], '$approved = NULL, $order=\'createdDate\', $dir = \'asc\', $filters = null)') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'self::filter(') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'nite number of cache files') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'ill delete the UploadedFiles that were attached to them') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'ignoreGroupFilter') !== FALSE) { continue; }
            $this->assertFalse($matchFound, $matches[0] . ' in ' . $file . ' *****');
        }
    }

    public function testAllModelFilesDefineGroupId() {
        $files = XG_TestHelper::globr(NF_APP_BASE . '/widgets/html/models', '*.php');
        $files[] = NF_APP_BASE . '/widgets/index/models/Comment.php';
        foreach($files as $file) {
            $contents = file_get_contents($file);
            $this->assertTrue(strpos($contents, 'groupId') !== false, basename($file));
        }
    }

    public function testAllGetFormsHaveGroupIdInHiddenField() {
        $files = XG_TestHelper::globr(NF_APP_BASE . '/widgets/html', '*.php');
        foreach($files as $file) {
            $contents = file_get_contents($file);
			$this->assertTrue(! preg_match('/<form(?!.*method=.post)/i', $contents), $file);
        }
    }

    public function testControllers() {
        $files = XG_TestHelper::globr(NF_APP_BASE . '/widgets/html/controllers', '*.php');
        foreach($files as $file) {
            $contents = file_get_contents($file);
            $this->assertTrue(strpos($contents, 'XG_GroupEnabledController') !== false, basename($file));
        }
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
