<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/controllers/SearchController.php');
XG_App::includeFileOnce('/widgets/profiles/controllers/BulkController.php');

class Syntax03CmdlineTest extends CmdlineTestCase {

    private static function contentTypes() {
        $contentTypes = array();
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            $pattern = "/Content::create\('(.*?)'/i";
            $contents = self::getFileContent($file);
            if (preg_match($pattern, $contents)) {
                foreach (explode("\n", $contents) as $line) {
                    if (preg_match($pattern, $line, $matches)) {
                        $contentTypes[$matches[1]] = $matches[1];
                    }
                }
            }
        }
        return $contentTypes;
    }

    public function testAllContentTypesHaveSearchHandlers() {
        // Skip types that have already been noted in BAZ-492 [Jon Aquino 2007-02-01]
        foreach (array_diff(self::contentTypes(), TestSearchController03::getTypesToExclude()) as $contentType) {
            foreach(XG_TestHelper::globr(NF_APP_BASE . '/widgets', 'IndexController.php') as $indexFile) {
                $searchHandlerFound = FALSE;
                if (preg_match("/action_detail.*'{$contentType}'/s", self::getFileContent($indexFile))) {
                    $searchHandlerFound = TRUE;
                    break;
                }
            }
            // These content types have been noted in BAZ-3208  [Jon Aquino 2007-06-20]
            if (in_array($contentType, array('SiteTabLayout', 'BlockedContactList', 'AudioAttachment', 'ImageAttachment', 'Playlist', 'Track', 'OpenSocialAppReview'))) { continue; }
            $this->assertTrue($searchHandlerFound, 'No search handler found for content type ' . $contentType);
        }
    }

    public function testTypesToExcludeFromRemovalByUser() {
        $typesToInclude = array('OpenSocialAppData', 'OpenSocialAppReview', 'OpenSocialApp', 'BlockedContactList', 'FriendRequestMessage', 'Note', 'Event', 'EventCalendar', 'EventWidget', 'ContactList', 'ActivityLogItem', 'ImageAttachment', 'UploadedFile', 'Topic', 'TopicCommenterLink', 'GroupInvitationRequest', 'GroupMembership', 'Comment', 'InvitationRequest', 'AudioAttachment', 'Playlist', 'Track', 'Page', 'Album', 'Photo', 'BlogPost', 'Video', 'VideoAttachment', 'VideoPreviewFrame');
        foreach (array_diff(self::contentTypes(), TestBulkController03::getTypesToExcludeFromRemovalByUser(), $typesToInclude) as $contentType) {
            $this->assertTrue(false, 'New content type detected: ' . $contentType . '. Add it to $typesToExcludeFromRemovalByUser?');
        }
    }

    public function testTypesToCheckForActivityLogDeletionProcessing() {
        $knownTypes = array('ActivityLogItem','Event','EventAttendee','EventCalendar','EventWidget','Category','Topic','TopicCommenterLink','Group',
                   'GroupIcon','GroupInvitationRequest','GroupMembership','BlockedContactList','Comment','ContactList','InvitationRequest',
                   'User','AudioAttachment','ImageAttachment','Playlist','Track','Note','OpenSocialAppData','Page',
                   'Album','Photo','SlideshowPlayerImage','BlogArchive','BlogPost','FriendRequestMessage','Video',
                   'VideoAttachment','VideoPlayerImage','VideoPreviewFrame','WatermarkImage', 'OpenSocialApp', 'OpenSocialAppReview');
        $typesToExclude = array('UploadedFile','PageLayout','ProfileCustomizationImage');
       foreach (array_diff(self::contentTypes(), $knownTypes, $typesToExclude) as $contentType) {
           $this->assertTrue(false, 'New content type detected: ' . $contentType . '. Add it to ActivityLogItem::typesForDeletionProcessing()?');
       }
    }

    public function testInPlaceEditorValueSpecified() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, '"InPlaceEditor"') !== false && strpos($contents, '_html="true"') !== false) {
                $this->assertTrue(strpos($contents, '_getValueUrl=') !== false || strpos($contents, '_value=') !== false, $file);
            }
        }
    }

    public function testPagesWithFeedLinksHaveFeedAutodiscoveryLinks() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            $pattern = '@class=.[^"\']*\brss@';
            $contents = str_replace("\n", ' ', preg_replace('@//.*@', ' ', self::getFileContent($file)));
            $contents = preg_replace('@<!--.*?-->@', '', self::getFileContent($file));
            if (preg_match($pattern, $contents)) {
                $this->assertTrue(preg_match('@xg_autodiscovery_link|outputFeedAutoDiscoveryLink@u', $contents), $file . ' *****');
            }
        }
    }

    public function testUnusedPartials() {
        $names = array();
        foreach(XG_TestHelper::globr(NF_APP_BASE, 'fragment_*.php') as $file) {
            if (mb_strpos($file, '_iphone.php') !== FALSE) { continue; }
            if (strpos($file, 'facebook/fragment_step') !== FALSE) { continue; }
            $name = str_replace('.php', '', basename($file));
            $names[$name] = $name;
        }
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            $contents = self::getFileContent($file);
            foreach ($names as $name) {
                if (strpos($contents, $name) !== FALSE) { unset($names[$name]); }
            }
        }
        $this->assertEqual(0, count($names), 'Unused partials: ' . implode(', ', $names));
    }

    public function testPopupZIndexFixInstalled() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.js') as $file) {
            $this->assertTrue(strpos($contents, 'xg_module_options') === FALSE || strpos($contents, 'fixPopupZIndexAfterShow') !== FALSE, $file);
            $this->assertTrue(! preg_match('/class=.[^\'"]*dialog/', $contents) || strpos($contents, 'fixPopupZIndexAfterShow') !== FALSE || strpos($contents, 'xg.index.actionicons.ActionButton') !== FALSE, $file);
        }
    }

}

class TestSearchController03 extends Index_SearchController {
    public static function getTypesToExclude() { return self::$typesToExclude; }
}

class TestBulkController03 extends Profiles_BulkController {
    public static function getTypesToExcludeFromRemovalByUser() { return self::$typesToExcludeFromRemovalByUser; }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
