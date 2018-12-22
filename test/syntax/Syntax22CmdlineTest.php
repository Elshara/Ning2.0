<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax22CmdlineTest extends CmdlineTestCase {

    public function testFilesInHelperDirectoryShouldHaveHelperSuffix() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/helpers') === false) { continue; }
            if (strpos($file, 'Notes_Scrubber.php') !== false) { continue; }
            if (strpos($file, 'XN_Message.php') !== false) { continue; }
            if (strpos($file, 'Filter.php') !== false) { continue; }
            if (strpos($file, 'InvitationMode.php') !== false) { continue; }
            if (strpos($file, 'ShareMode.php') !== false) { continue; }
            if (strpos($file, 'Reader.php') !== false) { continue; }
            if (strpos($file, 'Writer.php') !== false) { continue; }
            if (strpos($file, 'OpenSocial_InvalidKeyException.php') !== false) { continue; }
            if (strpos($file, 'Profiles_BlogListMode') !== false) { continue; }
            if (strpos($file, 'FriendRequestUpdater.php') !== false) { continue; }
            if (strpos($file, 'Photo_EmbedType.php') !== false) { continue; }
            if (strpos($file, 'Index_InvitationMode.php') !== false) { continue; }
            if (strpos($file, 'OpenSocial_InvalidKeyException.php') !== false) { continue; }
            if (strpos($file, 'Events_EventCommand.php') !== false) { continue; }
            if (strpos($file, 'Events_NegativePagingList.php') !== false) { continue; }
            if (strpos($file, 'Photo_Context.php') !== false) { continue; }
            if (strpos($file, 'Profiles_UserSort.php') !== false) { continue; }
            $this->assertPattern('@Helper@', $file);
        }
    }

    public function testFilesInHelperDirectoryShouldHaveHelperSuffix2() {
        $handle = opendir(NF_APP_BASE . '/lib');
        while (false !== ($file = readdir($handle))) {
            if (strpos($file, '.php') === false) { continue; }
            if (strpos($file, 'XG_Browser.php') !== false) { continue; }
            if (strpos($file, 'error.php') !== false) { continue; }
            if (strpos($file, 'W_WIDGETAPP_6_12_STUB.php') !== false) { continue; }
            if (strpos($file, 'XG_ConfigCachingApp.php') !== false) { continue; }
            if (strpos($file, 'index.php') !== false) { continue; }
            if (strpos($file, 'Controller.php') !== false) { continue; }
            if (strpos($file, 'MessageCatalog') !== false) { continue; }
            if (strpos($file, 'XG_Version.php') !== false) { continue; }
            if (strpos($file, 'XG_FacebookApp.php') !== false) { continue; }
            if (strpos($file, 'XG_TabLayout.php') !== false) { continue; }
            if (strpos($file, 'XG_Date.php') !== false) { continue; }
            if (strpos($file, 'XG_Layout.php') !== false) { continue; }
            if (strpos($file, 'XG_Embed.php') !== false) { continue; }
            if (strpos($file, 'XG_App.php') !== false) { continue; }
            if (strpos($file, 'XG_Form.php') !== false) { continue; }
            if (strpos($file, 'XG_Message.php') !== false) { continue; }
            if (strpos($file, 'XG_Messages.php') !== false) { continue; }
            if (strpos($file, 'XG_Query.php') !== false) { continue; }
            if (strpos($file, 'XG_PagingList.php') !== false) { continue; }
            if (strpos($file, 'XG_Cache.php') !== false) { continue; }
            if (strpos($file, 'XG_PerfLogger.php') !== false) { continue; }
            if (strpos($file, 'XG_Announcement.php') !== false) { continue; }
            if (strpos($file, 'XG_FaultTolerantTask.php') !== false) { continue; }
            if (strpos($file, 'readme.php') !== false) { continue; }
            $this->assertPattern('@Helper@', $file);
        }
        closedir($handle);
    }

    public function testNoMoreThanOneSpaceAfterVar() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/models') === false) { continue; }
            $this->assertNoPattern('/@var[ \t][ \t].*/', self::getFileContent($file), basename($file));
        }
    }

    public function testDoNotEscapeVideoDescription() {
        // Video description is already scrubbed, and may contain HTML [Jon Aquino 2008-02-25]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/video/templates') === false) { continue; }
            if (strpos($file, '/video/templates/video/edit.php') !== false) { continue; }
            $this->assertNoPattern('@htmlentities.*video->description@', self::getFileContent($file), $file);
        }
    }

    public function testDoNotInstantiateXnContent() {
        // The XN_Content constructor is now protected [Jon Aquino 2008-02-26]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            if (strpos($file, '/backendApi.php') !== false) { continue; } // Already noted in a Jira ticket [Jon Aquino 2008-03-08]
            $this->assertNoPattern('@new XN_Content@', self::getFileContent($file), $file);
        }
    }

    public function testXgMessageAndFriendsLinksWrappedInP() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            if (strpos($file, 'XG_TemplateHelpers.php') !== false) { continue; }
            if (strpos($file, 'blog/showProper.php') !== false) { continue; }
            if (strpos($file, 'topic/show.php') !== false) { continue; }
            if (strpos($file, 'photo/show.php') !== false) { continue; }
            if (strpos($file, 'album/show.php') !== false) { continue; }
            if (strpos($file, 'video/show.php') !== false) { continue; }
            if (strpos($file, 'event/show.php') !== false) { continue; }
            if (strpos($file, 'index/show.php') !== false) { continue; }
            if (strpos($file, 'embed/embed3pagetitle.php') !== false) { continue; }
            $this->assertNoPattern('@xg_message_and_friend_links(?!.*</p>)@', self::getFileContent($file), $file);
        }
    }

    public function testNoMyInFrontOfCreatedDate() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            $this->assertNoPattern('@my->createdDate@', self::getFileContent($file), $file);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
