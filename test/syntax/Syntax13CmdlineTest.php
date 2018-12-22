<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax13CmdlineTest extends CmdlineTestCase {
    public function testRemoveErrorLogCalls() {
        $allowedLines = array (
          'error_log("Couldn\'t add {$profile->screenName} to USERS profile set: {$e->getMessage()}");' => 'x',
          'error_log(\'User \'.$this->_user->screenName.\' tried to delete content: \'.$content->id.\' (15409628259620867)\');' => 'x',
          'error_log(var_export($_SERVER, true));' => 'x',
          'error_log("Forum Topic search query ({$_GET[\'q\']}) failed with: " . $e->getCode());' => 'x',
          'error_log("Group search query ({$_GET[\'q\']}) failed with: " . $e->getCode());' => 'x',
          'error_log($e->getMessage());' => 'x',
          'error_log(\'Add content error: \' . $e->getMessage());' => 'x',
          'error_log(\'Save content error: \' . $e->getMessage());' => 'x',
          'error_log(\'not a post\');' => 'x',
          'error_log(\'bad ID \' . $_GET[\'id\']);' => 'x',
          'error_log("Waiting to read unavailable user CSS file " . $filename . "...");' => 'x',
          'error_log("Timed out attempting to read user CSS file " . $filename);' => 'x',
          'error_log("/xn/detail problem: " . $e->getMessage());' => 'x',
          'error_log("Couldn\'t generate actiovity log item after profile question change: " . $e->getMessage());' => 'x',
          'error_log("Couldn\'t update User searchability after profile question change: " . $e->getMessage());' => 'x',
          'error_log(\'Setting admin status for \' . $user->contributorName . \' to \' . ($operation == \'promote\' ? \'TRUE\' : \'FALSE\'));' => 'x',
          'error_log("Friend status is {$contact->relationship} between {$_POST[\'from\']} and {$_POST[\'to\']}");' => 'x',
          'error_log("App-wide search query ({$this->term}) failed with " . $e->getCode());' => 'x',
          'error_log(\'Could not find object to share (id \' . $id . \')!\');' => 'x',
          'error_log(\'Unrecognized type: \' . $item->type . \' at \' . __FILE__ . \':\' . __LINE__);' => 'x',
          'error_log("Couldn\'t remove comment: $idForError -- {$e->getMessage()}");' => 'x',
          'error_log($e->getTraceAsString());' => 'x',
          'error_log($e->getMessage() . "\n" . $e->getTraceAsString());' => 'x',
          'error_log("Couldn\'t approve comment: $idOrComment -- {$e->getMessage()}");' => 'x',
          'error_log("Can\'t send invitation: " . $e->getMessage());' => 'x',
          'error_log("Can\'t accept invitation with key {$invitation->my->key} by $screenName:" . $e->getMessage());' => 'x',
          'error_log("Can\'t load remembered invitation with key \'$invitationKey\': {$e->getMessage()}");' => 'x',
          'error_log($e->getErrorsAsString());' => 'x',
          'error_log("User::isMember(): can\'t load User object for $screenName: " . $e->getMessage());' => 'x',
          'error_log(\'(4648228798249686) action_updateFromPlayer, rating failed:\'.$e->getMessage());' => 'x',
          'error_log(\'error removing artwork (9069659071564521): \'.$e->getMessage());' => 'x',
          'error_log(\'file_get_contents failed: \' . $url);' => 'x',
          'error_log("$this->prefix addContent error: {$e->getMessage()}");' => 'x',
          'error_log($message);' => 'x',
          'error_log("Failed to send message {$msg->summary} to $screenName: {$ex->getMessage()}");' => 'x',
          'error_log("Can\'t edit: {$e->getMessage()}");' => 'x',
          'error_log("Can\'t save edit: " . $e->getMessage());' => 'x',
          'error_log("Can\'t retrieve post: " . $e->getMessage());' => 'x',
          'error_log("Can\'t show post: " . $e->getMessage());' => 'x',
          'error_log("Can\'t list posts: " . $e->getMessage());' => 'x',
          'error_log("Feed error: " . $e->getMessage());' => 'x',
          'error_log(\'File upload error: \' . $e->getMessage());' => 'x',
          '// error_log("remove($user,$limit,$changed,$remaining) 4: {$chattersOnUser[\'numComments\']}, " . count($chattersOnUser[\'comments\']));' => 'x',
          '// error_log("remove($user,$limit,$changed,$remaining) 5: {$blogPosts[\'numPosts\']}, " . count($blogPosts[\'posts\']));' => 'x',
          '// error_log("remove($user,$limit,$changed,$remaining) end");' => 'x',
          'error_log(\'Chatter notification: \' . $e->getMessage());' => 'x',
          'error_log("Can\'t render comment list: " . $e->getMessage());' => 'x',
          'error_log("No screenName parameter supplied for comment thread");' => 'x',
          'error_log("Can\'t display friend feed: " . $e->getMessage());' => 'x',
          'error_log("Can\'t display friend/user list: " . $e->getMessage());' => 'x',
          'error_log(\'in profiles/friend/block, \\\'blocked\\\' not specified!\');' => 'x',
          'error_log("Friend search query ({$q}) failed with: " . $e->getCode());' => 'x',
          'error_log("Can\'t save layout: " . $e->getMessage());' => 'x',
          'error_log("Content error: " . $e->getErrorsAsString());' => 'x',
          'error_log("Exception in PUT to $url: " . $e->getMessage);' => 'x',
          'error_log("Failed to send message {$msg->summary()} to $screenName: {$ex->getMessage()}");' => 'x',
          'error_log(\'no previewFrame: \'.$e->getMessage());' => 'x',
          'error_log(\'BAZ-2332\');' => 'x',
          'error_log(\'BAZ-4672\');' => 'x',
          'error_log(\'Current URL: \' . XG_HttpHelper::currentURL());' => 'x',
          'error_log(\'Referrer: \' . $_SERVER[\'HTTP_REFERER\']);' => 'x',
          'error_log(\'Unknown sort by field: \' . $field);' => 'x',
          'error_log(\'Log cleared\');' => 'x',
          'error_log(\'exitWith500 \' . XN_Profile::current()->screenName . \' \' . $json->encode($_POST));' => 'x',
          'error_log (\'api: Data unchanged from what\\\'s stored, skipping write (\' . $key . \' = \' . $value . \')\');' => 'x',
          'error_log("Failed to change fullName to $fullName on all GroupMembership objects belonging to $screenName because they belong to more than $max groups.");' => 'x',
          'if ($nodeList->length > 1) { error_log("More than one node matching $rawEmbed in profile page layout - not updating"); return; }' => 'x',
          'if (! $nodeList->item(0)) { error_log("$rawEmbed not found in existing profile page layout."); return; }' => 'x',
          'error_log("asyncJob:".var_export($_REQUEST,TRUE));' => 'x',
          'error_log("asyncJob: done in ".sprintf(\'%.4f\',microtime(true)-$start));' => 'x',
          'error_log(\'BAZ-6796 @ Current user: \' . XN_Profile::current()->screenName . \' @ Current URL: \' . XG_HttpHelper::currentURL() . \' @ Referrer: \' . $_SERVER[\'HTTP_REFERER\']);' => 'x',
          'error_log("AsyncJob failure:".var_export($_REQUEST,TRUE));' => 'x',
          'error_log(\'Doing pre-3.3 CSS migration for network\');' => 'x',
          'error_log("Removing user {$username} failed: {$e->getMessage()}");' => 'x',
          'error_log(\'unable to save user: \' . $e->getMessage());' => 'x',
          'error_log(\'Partial upload\');' => 'x',
          'error_log(\'signOut failed - CSRF token invalid: \' . $_REQUEST[\'xg_token\'] . \' @ Current user: \' . XN_Profile::current()->screenName . \' @ Current URL: \' . XG_HttpHelper::currentURL() . \' @ Referrer: \' . $_SERVER[\'HTTP_REFERER\'] . \' @ User Agent: \' . $_SERVER[\'HTTP_USER_AGENT\']);' => 'x',
          'error_log("Not installing app at $url because that app is already installed.");' => 'x',
          'error_log("Not installing app at $url because the user cannot add features on their page.");' => 'x',
          'if ($instanceId === FALSE) { error_log("Not installing app at $url because insertModule failed."); return FALSE; }' => 'x',
          'if (! $found) { error_log("Did not find $id in results"); return false; }' => 'x',
          'if (! $appData) { error_log("Tried to update settings for $appUrl for $screenName which is not installed."); return; }' => 'x',
          'if (! $appData) { error_log("Tried to update $key setting for $appUrl for $screenName which is not installed."); return; }' => 'x',
          'error_log("Tried to setValues of $appUrl for $screenName but no corresponding OpenSocialAppData object.");' => 'x',
          'error_log("Tried to render embed of $appUrl for $screenName but no corresponding OpenSocialAppData object.");' => 'x',
        );
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/widgets/') === false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'error_log') === false) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if ($allowedLines[trim($line)]) { continue; }
                if ($this->isAllowedLine($line)) { continue; }
                if (strpos($line, 'asyncJob failure') !== false) { continue; }
                if (strpos($line, 'Perceived page-load time') !== false) { continue; }
                if (strpos($line, 'asyncJob $callback') !== false) { continue; }
                if (strpos($line, 'denormalizeFullName') !== false) { continue; }
                if (strpos($line, 'creating async job') !== false) { continue; }
                if (strpos($line, 'Exception rendering') !== false) { continue; }
                if (strpos($line, 'Error retrieving video content for Facebook') !== false) { continue; }
                if (strpos($line, 'Not installing') !== false) { continue; }
                if (strpos($line, 'BAZ-7028') !== false) { continue; }
                if (strpos($line, 'BAZ-8252') !== false) { continue; }
                if (strpos($line, 'BAZ-9176') !== false) { continue; }
                if (strpos($line, 'BAZ-9810') !== false) { continue; }
                if (strpos($line, 'Facebook promotion - error setting up') !== false) { continue; }
                if (strpos($line, 'action_newFromProfile: accessed from') !== false) { continue; }
                if (strpos($line, 'Failed to create OpenSocialAppData') !== false) { continue; }
                if (strpos($line, 'Not adding app to My Page') !== false) { continue; }
                if (strpos($line, 'Doing pre-3.2 CSS migration') !== false) { continue; }
                if (preg_match('/\(\d{7,}\)/',$line)) { continue; } // Skip "..message.. (123456789)" style messages
                $this->assertTrue(strpos($line, 'error_log') === false, $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

    public function testCheckMembershipBeforeJoinGroupOnSaveOrDelete() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'XG_JoinPromptHelper::joinGroupOn') === false) { continue; }
            $previousLine = '';
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if ((preg_match('@XG_JoinPromptHelper::joinGroupOn(?:Save|Delete)\(\);@', $line, $matches)) &&
                    (! preg_match('@XG_SecurityHelper::redirectIfNotMember\((\$[^\)]+)?\);@', $previousLine))) {
                $this->fail("{$matches[0]} without preceding XG_SecurityHelper::redirectIfNotMember() in $file@$i");
                    }
                $previousLine = $line;
            }
        }
    }

    public function testEliminateSystemPageUrls() {
        // Waiting for David Warner and Tim to verify the matrix in
        // http://home.ninginc.com/display/PRODUCT/Basic+Flows+-+Bazel+-+Links+to+System+Pages  [Jon Aquino 2007-09-20]
        $searches = array(
            'feedback.html?currentUrl=http://',
            'ning.Bar.open(\'clone\');',
            'currentUrl=',
            'view=',
            'editAccount=',
            'http://www.ning.com/',
            'op=',
            'sop=',
        );
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'Contact_Vcard_Parse.php') !== false) { continue; }
            if (strpos($file, 'OpenSocial_ApplicationDirectoryHelper.php') !== false) { continue; }
            if (strpos($file, 'jquery') !== false) { continue; }
            if (strpos($file, 'xn_resources/widgets/lib/core.min.js') !== false) { continue; }
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, '/dojo') !== false) { continue; }
            if (strpos($file, 'php') === false && strpos($file, 'js') === false) { continue; }
            $contents = self::getFileContent($file);
            $contents = str_replace('<conversion xmlns="http://www.ning.com/atom/1.0">', '', $contents);
            $contents = str_replace('http://www.ning.com/help/feedback/other.html', '', $contents);
            $contents = str_replace('/home/apps/edit?appUrl=', '', $contents);
            $contents = str_replace('addAtTop=', '', $contents);
            $contents = str_replace('crop=', '', $contents);
            $contents = str_replace('/home/apps/premium?appUrl=', '', $contents);
            $contents = str_replace('op=set', '', $contents);
            $contents = str_replace('/home/apps/create?appUrl=', '', $contents);
            $contents = str_replace('/?page_id=139&appUrl=', '', $contents);
            $contents = str_replace('http://www.ning.com/help/faq-creating-network.html', '', $contents);
            // Don't supply currentUrl to feedback.html - it has no effect [Jon Aquino 2007-09-22]
            $contents = str_replace('http://www.ning.com/help/feedback.html"' /* " at the end */, '', $contents);
            $contents = str_replace('http://www.ning.com/help/', '', $contents);
            $contents = str_replace('"http://www.ning.com/"', '', $contents);
            $contents = str_replace('http://www.ning.com/about/businesses.html', '', $contents);
            $contents = str_replace('http://www.ning.com/about/dmca-notice.html', '', $contents);
            $contents = str_replace('http://www.ning.com/about/businesses.html', '', $contents);
            $contents = str_replace(array(
                '$feed = \'<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0"><title type="text" /><xn:size>\' . count($users) . \'</xn:size><updated>\' . xg_xmlentities(date(\'c\', $time)) . \'</updated>\';',
                'return $url; // short circuit for desktop=>desktop.',
            ), '', $contents);

            if (strpos($file, '/gadgets.js') !== false) { $contents = str_replace('view=\'', '', $contents); }
            $found = false;
            foreach ($searches as $search) {
                if (strpos($contents, $search) !== false) {
                    $found = true;
                    break;
                }
            }
            if (! $found) { continue; }
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, 'op=set&') !== false && strpos($file, 'gadget') !== false) { continue; }
                $found = false;
                foreach ($searches as $search) {
                    if (strpos($line, $search) !== false) {
                        $found = true;
                        break;
                    }
                }
                $this->assertFalse($found, $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }


    public function testRemoveNetworkName() {
        // Remove "NetworkName" and "Network Name" [Jon Aquino 2007-09-20]
        $searches = array('NetworkName', 'Network Name');
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'php') === false && strpos($file, 'js') === false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'NetworkName') === false && strpos($contents, 'Network Name') === false) { continue; }
            $contents = preg_replace('@//.*@', '', $contents);
            $contents = preg_replace('@\'NETWORK_NAME\'\s*=>.*@', '', $contents);
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, 'FACEBOOK_INSTR_SETUP_11') !== false) { continue; }
                $this->assertFalse(preg_match('@\bNetworkName\b|\bNetwork Name\b@', $line), $this->escape($line) . ' - ' . $file . ' line ' . $i);
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
