<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax23Test extends UnitTestCase {

    public function testTagValueCountQueryShouldSpecifyContentType() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (mb_strpos($file, '/test') !== false) { continue; }
            if (strpos($file, '/lib/scripts/eoc167a.php') !== false) { continue; }
            if (mb_strpos($file, 'XG_TagHelper.php') !== false) { continue; }
            $contents = file_get_contents($file);
            $contents = str_replace('based on a Tag_ValueCount query', '', $contents);
            if (mb_stripos($contents, 'Tag_ValueCount') === false) { continue; }
            $contents = str_replace("\r", '', str_replace("\n", '', $contents));
            $this->assertPattern('@Tag_ValueCount.{0,100}(content->type|contentId)@ui', $contents, $file);
        }
    }

    public function testXgResizeEmbedsShouldUseKnownWidth() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (mb_strpos($file, '/test') !== false) { continue; }
            $contents = file_get_contents($file);
            preg_match_all('@xg_resize_embeds.*@ui', $contents, $matches);
            foreach ($matches[0] as $match) {
                if (strpos($match, 'Forum_CommentHelper::maxEmbedWidth') !== false) { continue; }
                if (strpos($match, 'xg_resize_embeds()') !== false) { continue; }
                if (strpos($match, '$html, $maxWidth = NULL, $columnCount = NULL') !== false) { continue; }
                if (strpos($match, 'maxEmbedWidth') !== false) { continue; }
                if (strpos($match, 'xg_excerpt_html(') !== false) { continue; }
                $this->assertPattern('@171|475|530|540|545|646|712|737@ui', $match, $match . ' - ' . $file);
            }
        }
    }

    public function testDoNotPutFilesDirectlyInLibDirectory() {
        // Files in a widget's lib directory are autoloaded. Put them in lib/helpers instead. [Jon Aquino 2008-03-01]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'XG_Layout_groups.php') !== false) { continue; }
            if (strpos($file, 'XG_Layout_profiles.php') !== false) { continue; }
            if (strpos($file, 'Notes_UrlHelper.php') !== false) { continue; }
            if (strpos($file, 'autoload.php') !== false) { continue; }
            $this->assertNoPattern('@widgets.*lib/[^/]*.php@ui', $file, $file);
        }
    }

    public function testHelperPrefixMatchesWidget() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'lib/helpers') === false) { continue; }
            if (strpos($file, 'Gadget_ContentHelper.php') !== false) { continue; }
            if (mb_strpos($file, 'XN_Message.php') !== false) { continue; }
            $this->assertPattern('@widgets/([^/]+)/lib/helpers/\1_@ui', $file, $file);
        }
    }

    public function testCssHasNoImportStatements() {
        $this->doTestCssHasNoImportStatements('http://' . $_SERVER['HTTP_HOST']);
        $this->doTestCssHasNoImportStatements('http://' . $_SERVER['HTTP_HOST'] . '/photo');
    }

    public function doTestCssHasNoImportStatements($url) {
        // Import statements may indicate error from CSS compressor.
        $homepageHtml = file_get_contents($url);
        $this->assertTrue(preg_match('@/xn/css[^"]*@u', $homepageHtml, $matches));
        $css = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . html_entity_decode($matches[0]));
        $this->assertPattern('@list-style@u', $css);
        $this->assertNoPattern('/@import/u', $css);
    }

    public function testNing7132Workaround() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (mb_strpos($file, '/test') !== false) { continue; }
            $contents = file_get_contents($file);
            if (mb_strpos($contents, 'Tag_ValueCount') === false) { continue; }
            $contents = str_replace("\n", ' ', $contents);
            preg_match_all("@'Tag_ValueCount'.*?execute.{0,200}@", $contents, $matches);
            foreach ($matches[0] as $match) {
                if (strpos($match, 'content->type') !== false || strpos($match, 'content.type') !== false) {
                    $this->assertPattern('@NING-7132@', $match, $match . ' ' . $file);
                }
            }
        }
    }

    public function testUseLightboxConfirmDialogInsteadOfBrowserConfirmDialog() {
        foreach(array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.php'), XG_TestHelper::globr(NF_APP_BASE, '*.js')) as $file) {
            if (mb_strpos($file, '/test') !== false) { continue; }
            if (mb_strpos($file, '_iphone') !== false) { continue; }
            $contents = file_get_contents($file);
            if (mb_strpos($file, 'quickadd/photo.js') !== false) { $contents = str_replace("confirm(xg.index.nls.text('looksLikeNotImage')", '', $contents); }
            if (mb_strpos($file, 'quickadd/music.js') !== false) { $contents = str_replace("confirm(xg.index.nls.text('looksLikeNotMusic')", '', $contents); }
            if (mb_strpos($file, 'quickadd/video.js') !== false) { $contents = str_replace("confirm(xg.index.nls.text('looksLikeNotVideo')", '', $contents); }
            if (mb_strpos($file, 'quickadd/core.js') !== false) { $contents = str_replace("confirm(xg.index.nls.text('cannotKeepFiles')", '', $contents); }
            if (strpos($contents, 'confirm(') === false) { continue;; }
            $this->assertFalse(preg_match('@[^.]confirm\(.*@u', $contents, $matches), $matches[0] . ' ' . $file);
        }
    }

    public function testSpecifyFalseForShowEllipsisAfterTruncation() {
        // If we always display an ellipsis (indicated by CONTINUE_ELLIPSIS),
        // make sure we set showEllipsisAfterTruncation to false in xg_excerpt (BAZ-6472) [Jon Aquino 2008-03-05]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (mb_strpos($file, '/test') !== false) { continue; }
            $contents = file_get_contents($file);
            if (strpos($contents, 'CONTINUE_ELLIPSIS') === false || strpos($contents, 'xg_excerpt') === false) { continue;; }
            if (preg_match('@xg_excerpt.*, false\)@u', $contents)) { continue; }
            preg_match_all('@xg_excerpt.*@u', $contents, $matches);
            foreach ($matches[0] as $match) {
                $this->assertTrue(strpos($match, ', false)') !== false, $match . ' - ' . $file);
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
