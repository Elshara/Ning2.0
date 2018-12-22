<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Tests for enabling Forum to work with Groups
 */
class GroupForumTest extends BazelTestCase {

    public function testPhp() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/widgets/forum', '*.php') as $file) {
            $pattern = '/'
                    . 'appIsPrivate(?!.*groupIsPrivate)' // Check group privacy in addition to app privacy [Jon Aquino 2007-04-14]
                    . '|Controller.php.*(?<!checkCurrentUserCanAccess.)XN_Content::load' // Call checkCurrentUserCanAccess in actions [Jon Aquino 2007-04-14]
                    . '|Controller.php.*(?<!checkCurrentUserCanAccess.)W_Content::load' // Call checkCurrentUserCanAccess in actions [Jon Aquino 2007-04-14]
                    . '|(?<!getWidget..groups..)(?<!getWidget..main..)(?<!getWidget..profiles..)->buildUrl(?!.*video)'  // Use XG_GroupHelper::buildUrl [Jon Aquino 2007-04-16]
                    . '|isOwner(?!.*isGroupAdmin)' // Use XG_GroupHelper::isGroupAdmin [Jon Aquino 2007-04-16]
                    . '|>ownerName(?!.*isGroupAdmin)' // Use XG_GroupHelper::isGroupAdmin [Jon Aquino 2007-04-16]
                    . '|\'groups\'\)->buildUrl' // Use XG_GroupHelper::buildUrl [Jon Aquino 2007-05-02]
                    . '|\'forum\'\)->buildUrl' // Use XG_GroupHelper::buildUrl [Jon Aquino 2007-05-02]
                    . '/i';
            $contents = file_get_contents($file);
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (preg_match($pattern, basename($file) . ':' . $line, $matches)) {
                    if (strpos($line, '! XG_GroupHelper::groupIsPrivate() && ! XG_App::appIsPrivate()') !== false) { continue; }
                    if ($matches[0] == 'Controller.php:            $this->topic = W_Content::load') { continue; }
                    if ($matches[0] == 'IsOwner' && strpos($line, 'MY_DISCUSSIONS_ONLY')) { continue; } // BAZ-2663 [Jon Aquino 2007-04-25]
                    if ($matches[0] == 'IsOwner' && strpos($line, 'OWNER_DISCUSSIONS_ONLY')) { continue; } // BAZ-2663 [Jon Aquino 2007-04-25]
                    if ($matches[0] == '>ownerName' && strpos($line, 'ownerDiscussions')) { continue; } // BAZ-2663 [Jon Aquino 2007-04-25]
                    $this->assertTrue(FALSE, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    private function format($match, $file, $line, $lineNumber) {
        return $this->escape($match) . ' in ' . $this->escape($line) . ' ' . $file . ' ' . $lineNumber . ' ***';
    }

    public function testCreateTopicLinksCallPromptToJoin() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/widgets/forum', '*.php') as $file) {
            $pattern = '/(?<!function )newTopicUrl/i';
            $contents = file_get_contents($file);
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (preg_match($pattern, basename($file) . ':' . $line, $matches)) {
                    $this->assertTrue(strpos($line, 'promptToJoin') !== false, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    public function testAllQueriesHaveGroupFilter() {
        $files = XG_TestHelper::globr(NF_APP_BASE . '/widgets/forum', '*.php');
        $files[] = NF_APP_BASE . '/lib/XG_FileHelper.php';
        $files[] = NF_APP_BASE . '/widgets/index/models/Comment.php';
        foreach($files as $file) {
            if (strpos($file, 'SequencedjobController.php') !== false) { continue; }
            if (strpos($file, 'Forum_BulkHelper.php') !== false) { continue; }
            $pattern = '/.{0,150}Query::create\(.Content(?!.{0,500}addGroupFilter).{0,400}/i';
            $contents = str_replace("\n", ' ', file_get_contents($file));
            $matchFound = preg_match($pattern, $contents, $matches);
            if ($matchFound && strpos($matches[0], '$approved = NULL, $order=\'createdDate\', $dir = \'asc\', $filters = null)') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '$featuredTopicsQuery = XN_Query::create') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'function recentTopics') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'Forum_Filter') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'self::filter(') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], "filter('my->attachedToAuthor', '=',") !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], "filter('my->attachedTo', '=',") !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], "filter('my->attachedTo', '=', \$_GET['id'])") !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], '$embed->get(\'itemCount\')') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'nite number of cache files') !== FALSE) { continue; }
            if ($matchFound && strpos($matches[0], 'ill delete the UploadedFiles that were attached to them') !== FALSE) { continue; }
            $this->assertFalse($matchFound, $matches[0] . ' in ' . $file . ' *****');
        }
    }

    public function testAllModelFilesDefineGroupId() {
        $files = XG_TestHelper::globr(NF_APP_BASE . '/widgets/forum/models', '*.php');
        $files[] = NF_APP_BASE . '/widgets/index/models/Comment.php';
        foreach($files as $file) {
            $contents = file_get_contents($file);
            $this->assertTrue(strpos($contents, 'groupId') !== false, basename($file));
        }
    }

    public function testAllGetFormsHaveGroupIdInHiddenField() {
        $files = XG_TestHelper::globr(NF_APP_BASE . '/widgets/forum', '*.php');
        foreach($files as $file) {
            $contents = file_get_contents($file);
			$this->assertTrue(! preg_match('/<form(?!.*method=.post)/i', $contents), $file);
        }
    }

    public function testControllers() {
        $files = XG_TestHelper::globr(NF_APP_BASE . '/widgets/forum/controllers', '*.php');
        foreach($files as $file) {
            if (strpos($file, 'SequencedjobController.php') !== false) { continue; }
            $contents = file_get_contents($file);
            $this->assertTrue(strpos($contents, 'XG_GroupEnabledController') !== false, basename($file));
        }
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
