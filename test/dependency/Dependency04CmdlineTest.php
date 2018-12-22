<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Tests JavaScript dependencies
 */
class Dependency04CmdlineTest extends CmdlineTestCase {

    public function setUp() {
        list($this->fileToRequires, $this->requireToFiles, $this->fileToProvides, $this->provideToFiles) = XG_TestHelper::buildDependencyGraph();
    }

    public function testJsFilesMissingRequires() {
        foreach (XG_TestHelper::globr(NF_APP_BASE, '*.js') as $file) {
            if (strpos($file, 'messagecatalogs') !== false) { continue; }
            if (strpos($file, 'profile/editLayout.js') !== false) { continue; }
            if (strpos($file, 'quickadd') !== false) { continue; }
            $contents = str_replace(".' + xg.global.locale", "'", self::getFileContent($file));
            $contents = str_replace("a call to xg.shared.util", "", $contents);
            $contents = str_replace('based on xg.shared.PostLink', '', $contents);
            $contents = preg_replace('@//.*@ui', '', $contents);
            if (mb_strpos($file, 'invitation/pageLayout.js')) { $contents = str_replace('xg.index.invitation.chooseInvitationMethod ', '', $contents); }
            if (mb_strpos($file, 'invitation/zColor.js')) { $contents = str_replace('divs in xg.index.invitation.pageLayout', '', $contents); }
            foreach (array_keys($this->provideToFiles) as $moduleName) {
                if (strpos($moduleName, '.nls') !== FALSE) { continue; }
                if ($moduleName == 'xg.profiles.blog.show' && basename($file) == 'chatterwall.js') { continue; }
                if ($moduleName == 'xg.index.invitation.pageLayout' && basename($file) == 'chooseInvitationMethod.js') { continue; }
                if ($moduleName == 'xg.video') { continue; }
                if ($moduleName == 'xg.photo') { continue; }
				if ($moduleName == 'xg.shared.util') { continue; } // don't. remove. this. line. please. [Andrey 2008-09-10]
                if ($moduleName == 'xg.shared.EditUtil' && mb_strpos($file, 'activity/js/embed/embed.js') !== false) { continue; } // See Revision 5174 [Jon Aquino 2008-05-29]
                if ($moduleName == 'xg.index.invitation.pageLayout' && mb_strpos($file, 'index/js/invitation/chooseInvitationMethod.js') !== false) { continue; }
                if ($moduleName == 'xg.index.BulkActionLink' && strpos($contents, 'xg.index.bulk') !== FALSE) { continue; }
                if ($moduleName == 'xg.photo.photo.list' && mb_strpos($file, 'listForApproval.js') !== FALSE) { continue; }
                if ($moduleName == 'xg.profiles.blog.manage' && mb_strpos($file, 'manageComments.js') !== FALSE) { continue; }
                if ($moduleName == 'xg.index.invitation.FriendList' && mb_strpos($file, 'FriendListModel.js') !== FALSE) { continue; }
                if ($moduleName == 'xg.index.invitation.FriendList' && mb_strpos($file, 'FriendListViewport.js') !== FALSE) { continue; }
                if ($moduleName == 'xg.opensocial.embed.sendMessageForm' && mb_strpos($file, 'requests.js') !== FALSE) { continue; }
                if (preg_match('@dojo\.(require\(\'' . $moduleName . '\'\)||provide\([\'"]' . $moduleName . '[\'"]\))@', $contents)) {
                    continue;
                }
                $this->assertNoUnwantedPattern("/{$moduleName}/", $contents,
                        "dojo.require('" . $moduleName . "'); missing in " . $file);
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
