<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax04CmdlineTest extends CmdlineTestCase {

    public function testNoDoubleDollarSigns() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            $pattern = '/\$\$/i';
            $contents = self::getFileContent($file);
            if (preg_match($pattern, $contents)) {
                foreach (explode("\n", $contents) as $line) {
                    if (strpos($line, 'xg.$$') !== false) { continue; }
                    if (strpos($line, '$$v = xnhtmlentities($$v);') !== false) { continue; }
                    if (strpos($line, 'Contains $$$') !== false) { continue; }
                    if (strpos($line, 'isset($$v)') !== false) { continue; }
                    if (strpos($line, '$this->_data[$v] = $$v;') !== false) { continue; }
                    $this->assertFalse(preg_match($pattern, $line), $this->escape($line) . ' - ' . $file);
                }
            }
        }
        return $contentTypes;
    }

    public function testFollowLinkMustSpecifyAddDescriptionAndRemoveDescription() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(! preg_match('@dojoType.*FollowLink@ui', $contents) || strpos($contents, '_addDescription') !== false, $file);
        }
        return $contentTypes;
    }

    public function testPassControllerToXgSidebar() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            $contents = self::getFileContent($file);
            $this->assertFalse(preg_match('@xg_sidebar\(\)@u', $contents), 'xg_sidebar() needs to be passed the current controller ($this) - ' . $file);
        }
    }

    public function testBaz3985Php() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            $this->doTestBaz3985($file);
        }
    }

    public function testBaz3985Js() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.js') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            $this->doTestBaz3985($file);
        }
    }

    private function doTestBaz3985($filename) {
        $this->assertFalse(preg_match('@setAttribute.._@ui', self::getFileContent($filename)), 'Safari fails when you call setAttribute on an attribute beginning with an underscore: ' . $filename);
    }

    public function testMyDoesNotWorkInInstanceMethodsOfWModelSubclasses() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/models/') === false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'this->my->') === false) { continue; }
            $inStaticFunction = false;
            foreach (explode("\n", $contents) as $line) {
                if (strpos($line, 'function') !== false) {
                    $inStaticFunction = strpos($line, 'static') !== false;
                }
                if (! $inStaticFunction) {
                    $this->assertTrue(strpos($line, 'this->my->') === false, $this->escape($line) . ' - ' . basename($file));
                }
            }
        }
    }

    public function testDojoProvideShouldMatchFilename() {
        // dojo.provide should match the filename, otherwise Tim says the ning.loader can get confused [Jon Aquino 2007-09-05]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.js') as $filename) {
            if (strpos($filename, '/dojo') !== FALSE) { continue; }
            if (strpos($filename, '/adapter') !== FALSE) { continue; }
            if (strpos($filename, 'xn_resources/widgets/lib/core.min.js') !== false) { continue; }
            preg_match_all('@dojo.provide\(["\'].*\.([^."\']+)@u', self::getFileContent($filename), $matches);
            foreach ($matches[1] as $match) {
                // Skip existing usages as of 2007-09-05  [Jon Aquino 2007-09-05]
                if ($match == 'RemoveActivityLink' && strpos($filename, '/xn_resources/widgets/activity/js/embed/embed.js') !== false) { continue; }
                if ($match == 'ActivityModule' && strpos($filename, '/xn_resources/widgets/activity/js/embed/embed.js') !== false) { continue; }
                if ($match == 'OpenSocialModule' && strpos($filename, '/xn_resources/widgets/opensocial/js/embed/embed.js') !== false) { continue; }
                if ($match == 'FeedModule' && strpos($filename, '/xn_resources/widgets/feed/js/embed/embed.js') !== false) { continue; }
                if ($match == 'ForumLinkToggle' && strpos($filename, '/xn_resources/widgets/forum/js/topic/show.js') !== false) { continue; }
                if ($match == 'HtmlModule' && strpos($filename, '/xn_resources/widgets/html/js/embed/embed.js') !== false) { continue; }
                if ($match == 'bulk' && strpos($filename, '/xn_resources/widgets/index/js/BulkActionLink.js') !== false) { continue; }
                if ($match == 'ActionButton' && strpos($filename, '/xn_resources/widgets/index/js/actionicons.js') !== false) { continue; }
                if ($match == 'PromotionButton' && strpos($filename, '/xn_resources/widgets/index/js/actionicons.js') !== false) { continue; }
                if ($match == 'PromotionLink' && strpos($filename, '/xn_resources/widgets/index/js/actionicons.js') !== false) { continue; }
                if ($match == 'BulkActionLink' && strpos($filename, '/xn_resources/widgets/index/js/bulk.js') !== false) { continue; }
                if ($match == 'PromotionButton' && strpos($filename, '/xn_resources/widgets/index/js/actionicons/PromotionButtion.js') !== false) { continue; }
                if ($match == 'AddCommentForm' && strpos($filename, '/xn_resources/widgets/page/js/page/show.js') !== false) { continue; }
                if ($match == 'DeleteCommentLink' && strpos($filename, '/xn_resources/widgets/page/js/page/show.js') !== false) { continue; }
                if ($match == 'AlbumEditor' && strpos($filename, '/xn_resources/widgets/photo/js/album/edit.js') !== false) { continue; }
                if ($match == 'AvailablePhotosHandler' && strpos($filename, '/xn_resources/widgets/photo/js/album/edit.js') !== false) { continue; }
                if ($match == 'DragSource' && strpos($filename, '/xn_resources/widgets/photo/js/album/edit.js') !== false) { continue; }
                if ($match == 'DropTarget' && strpos($filename, '/xn_resources/widgets/photo/js/album/edit.js') !== false) { continue; }
                if ($match == 'PhotoModule' && strpos($filename, '/xn_resources/widgets/photo/js/embed/embed.js') !== false) { continue; }
                if ($match == 'photo' && strpos($filename, '/xn_resources/widgets/photo/js/index/_shared.js') !== false) { continue; }
                if ($match == 'TopicUpdatingText' && strpos($filename, '/xn_resources/widgets/photo/js/index/_shared.js') !== false) { continue; }
                if ($match == 'AddRemoveButton' && strpos($filename, '/xn_resources/widgets/photo/js/index/_shared.js') !== false) { continue; }
                if ($match == 'ApprovalLink' && strpos($filename, '/xn_resources/widgets/photo/js/photo/listForApproval.js') !== false) { continue; }
                if ($match == 'ThumbNav' && strpos($filename, '/xn_resources/widgets/photo/js/photo/show.js') !== false) { continue; }
                if ($match == 'ContextSelector' && strpos($filename, '/xn_resources/widgets/photo/js/photo/show.js') !== false) { continue; }
                if ($match == 'RotateLink' && strpos($filename, '/xn_resources/widgets/photo/js/photo/show.js') !== false) { continue; }
                if ($match == 'AddCommentForm' && strpos($filename, '/xn_resources/widgets/photo/js/photo/show.js') !== false) { continue; }
                if ($match == 'DeleteCommentLink' && strpos($filename, '/xn_resources/widgets/photo/js/photo/show.js') !== false) { continue; }
                if ($match == 'PhotoActionTabs' && strpos($filename, '/xn_resources/widgets/photo/js/photo/show.js') !== false) { continue; }
                if ($match == 'BlogModule' && strpos($filename, '/xn_resources/widgets/profiles/js/embed/blog.js') !== false) { continue; }
                if ($match == 'ChatterModule' && strpos($filename, '/xn_resources/widgets/profiles/js/embed/chatterwall.js') !== false) { continue; }
                if ($match == 'MembersModule' && strpos($filename, '/xn_resources/widgets/profiles/js/embed/embed.js') !== false) { continue; }
                if ($match == 'VideoModule' && strpos($filename, '/xn_resources/widgets/video/js/embed/embed.js') !== false) { continue; }
                if ($match == 'video' && strpos($filename, '/xn_resources/widgets/video/js/index/_shared.js') !== false) { continue; }
                if ($match == 'ApprovalListPlayer' && strpos($filename, '/xn_resources/widgets/video/js/video/listForApproval.js') !== false) { continue; }
                if ($match == 'ThumbNav' && strpos($filename, '/xn_resources/widgets/video/js/video/show.js') !== false) { continue; }
                if ($match == 'ContextSelector' && strpos($filename, '/xn_resources/widgets/video/js/video/show.js') !== false) { continue; }
                if ($match == 'StarRating' && strpos($filename, '/xn_resources/widgets/video/js/video/show.js') !== false) { continue; }
                if ($match == 'AddCommentForm' && strpos($filename, '/xn_resources/widgets/video/js/video/show.js') !== false) { continue; }
                if ($match == 'DeleteCommentLink' && strpos($filename, '/xn_resources/widgets/video/js/video/show.js') !== false) { continue; }
                if ($match == 'VideoEmbedField' && strpos($filename, '/xn_resources/widgets/video/js/video/show.js') !== false) { continue; }
                if ($match == 'VideoEmbedField' && strpos($filename, '/xn_resources/widgets/video/js/video/ShowEmbedToggle.js') !== false) { continue; }
                if ($match == 'VideoActionTabs' && strpos($filename, '/xn_resources/widgets/video/js/video/show.js') !== false) { continue; }

                $this->assertEqual(str_replace('.js', '', basename($filename)), $match, basename($filename) . ' provides "' . $match . '" - ' . $filename);
            }
        }
    }

    public function testUseEncodeUriComponentInsteadOfEscape() {
        // encodeURIComponent has a number of advantages [Jon Aquino 2007-09-05]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.js') as $file) {
            if (mb_strpos($file, '/iui.js') !== false) { continue; }
            if (mb_strpos($file, '/dojo') !== false) { continue; }
            if (mb_strpos($file, '/js/lib/Test') !== false) { continue; }
            $contents = str_replace('dojo.string.escape', '', self::getFileContent($file));
            if (strpos($contents, 'escape(')) {
                foreach (explode("\n", $contents) as $line) {
                    $this->assertFalse(strpos($line, 'escape('), $this->escape($line) . ' - ' . $file);
                }
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
