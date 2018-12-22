<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/controllers/SearchController.php');
XG_App::includeFileOnce('/widgets/profiles/controllers/BulkController.php');

class Syntax06CmdlineTest extends CmdlineTestCase {

    public function testPrivacySpecified() {
        foreach (glob(NF_APP_BASE . '/widgets/*') as $widgetDirectory) {
            $widgetName = basename($widgetDirectory);
            if (in_array($widgetName, array('chat', 'opensocial', 'activity', 'index', 'feed', 'html', 'admin', 'notifications'))) { continue; }
            $privacySpecified = FALSE;
            foreach(XG_TestHelper::globr($widgetDirectory, '*.php') as $file) {
                if (preg_match('/isPrivate\s+=\s+.*IsPrivate/', self::getFileContent($file), $matches)) {
                    $privacySpecified = TRUE;
                    break;
                }
            }
            $this->assertTrue($privacySpecified, $widgetName . ': Object privacy not set using XG_App::contentIsPrivate() or XG_App::appIsPrivate()');
        }
    }

    public function testClosingFormTags() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            if (strpos($file, '/dojo') !== FALSE) { continue; }
            $contents = str_replace("\n", ' ', self::getFileContent($file));
            $contents = str_replace("the <form> node", "", $contents);
            $contents = str_replace("contains a <form> ", "", $contents);
            $contents = str_replace("contains <form> tag", "", $contents);
            $contents = str_replace("Content of <form> node", "", $contents);
            $contents = str_replace("opening and closing <form> tags", "", $contents);
            preg_match_all('@<form@iu', $contents, $matches);
            $openingFormTagCount = count($matches[0]);
            preg_match_all('@</form@iu', $contents, $matches);
            $closingFormTagCount = count($matches[0]);
            $this->assertEqual($openingFormTagCount, $closingFormTagCount, $openingFormTagCount . ' <form>, ' . $closingFormTagCount . ' </form> in ' . $file . ' *****');
        }
    }

    public function testClosingDivTags() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            if (strpos($file, 'simplepie.inc') !== FALSE) { continue; }
            if (strpos($file, 'jquery') !== FALSE) { continue; }
            if (strpos($file, 'xn_resources/widgets/lib/core.min.js') !== false) { continue; }
            if (strpos($file, 'XG_LayoutHelper.php') !== FALSE) { continue; }
            if (strpos($file, '/index/templates/facebook/instructions.php') !== FALSE) { continue; }
            if (strpos($file, '/photo/templates/photo/fragment_grid_ncolumns.php') !== FALSE) { continue; }
            if (strpos($file, '/events/templates/_shared/fragment_list.php') !== FALSE) { continue; }
            if (strpos($file, '/events/templates/embed/embed.php') !== FALSE) { continue; }
            if (strpos($file, '/photo/templates/embed/embed.php') !== FALSE) { continue; }
            if (strpos($file, '/notes/templates/embed/embed.php') !== FALSE) { continue; }
            if (strpos($file, '/feed/templates/embed/embed.php') !== FALSE) { continue; }
            if (strpos($file, '/events/templates/embed/fragment_block.php') !== FALSE) { continue; }
            if (strpos($file, '/xn_resources/widgets/events/js/EventEmbedModule.js') !== FALSE) { continue; }
            if (strpos($file, '/xn_resources/widgets/notes/js/NoteEmbedModule.js') !== FALSE) { continue; }
            $contents = self::getFileContent($file);
            $contents = preg_replace('@^\s+/?\*.*|\s//.*$|<div.*\Skip testClosingDivTags@um', '', $contents);
            if (strpos($file, 'XG_TemplateHelpers.php') !== false) { $contents = str_replace('<div style="font-size: 20px; color: white; background: black;">', '', $contents); }
            if (strpos($file, '/feed/templates/embed/embed.php') !== false) { $contents = str_replace('<div class="xg_module module_feed xg_reset">', '', $contents); }
            if (strpos($file, '/opensocial/templates/embed/embed.php') !== false) { $contents = str_replace('<div class="xg_module module_opensocial">', '', $contents); }
            if (strpos($file, '/forum/templates/embed/embed.php') !== false) { $contents = str_replace('<div class="xg_module module_forum" dojoType="ForumModule"', '', $contents); }
            if (strpos($file, '/groups/templates/embed/embed.php') !== false) { $contents = str_replace('<div class="xg_module module_groups" dojoType="GroupModule"', '', $contents); }
            if (strpos($file, '/video/templates/embed/embed.php') !== false) { $contents = str_replace('<div class="xg_module module_video" dojotype="VideoModule"', '', $contents); }
            if (strpos($file, '/profiles/templates/embed/embed1activeMembers.php') !== false) { $contents = str_replace('<div class="xg_module module_members" dojoType="MembersModule"', '', $contents); }
            if (strpos($file, '/page/templates/embed/embed.php') !== false) { $contents = str_replace('<div class="xg_module" dojoType="PageModule"', '', $contents); }
            if (strpos($file, '/index/templates/embed/header.php') !== false) { $contents = str_replace('<div id="xg" ', '', $contents); }
            preg_match_all('@<div@iu', $contents, $matches);
            $openingDivTagCount = count($matches[0]);
            preg_match_all('@</div>@iu', $contents, $matches);
            $closingDivTagCount = count($matches[0]);
            if (strpos($file, '/index/templates/embed/footer.php') !== false) { $closingDivTagCount--; }
            if (strpos($contents, 'closing div handled in partial') !== false) { $closingDivTagCount++; }
            if (strpos($file, '/index/templates/embed/footer_iphone.php') !== false) { $closingDivTagCount -= 2; }
            if (strpos($file, '/index/templates/embed/header_iphone.php') !== false) { $closingDivTagCount += 2; }
            $this->assertEqual($openingDivTagCount, $closingDivTagCount, $openingDivTagCount . ' <div>, ' . $closingDivTagCount . ' </div> in ' . $file . ' *****');
        }
    }

}

class TestSearchController06 extends Index_SearchController {
    public static function getTypesToExclude() { return self::$typesToExclude; }
}

class TestBulkController06 extends Profiles_BulkController {
    public static function getTypesToExcludeFromRemovalByUser() { return self::$typesToExcludeFromRemovalByUser; }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
