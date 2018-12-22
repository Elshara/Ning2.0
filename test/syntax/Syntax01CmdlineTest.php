<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/controllers/SearchController.php');
XG_App::includeFileOnce('/widgets/profiles/controllers/BulkController.php');

class Syntax01CmdlineTest extends CmdlineTestCase {

/*	Designers think it's fine.
    public function XtestCss() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.css') as $file) {
            $this->assertTestCss($file);
        }
    }

    private function assertTestCss($file) {
        $pattern = '/overflow-y'  // Safari doesn't support overflow-y (BAZ-1846) [Jon Aquino 2007-02-22]
                . '/i';
        $contents = self::getFileContent($file);
        if (! preg_match($pattern, $contents)) {
            $this->assertTrue(TRUE);
        } else {
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                // Huy says these overflow-y styles are required for ie6 [dkf 2008-09-15]
                if(strpos($file, 'index/css/common.css') !== FALSE && preg_match('/overflow-y:\s?visible/', $line) > 0) { continue; }
                if(strpos($file, 'groups/css/module.css') !== FALSE && preg_match('/overflow-y:\s?visible/', $line) > 0) { continue; }
                if (preg_match($pattern, $line, $matches)) {
                    $this->assertTrue(FALSE, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }
*/

    public function testPhp() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            /// TODO refactor these - this is an obscure test [Travis S. 2008-09-05]
            if ($_SERVER['DOCUMENT_ROOT'] . '/lib/XG_TabLayout.php' === $file) { continue; }
            if (strpos($file, 'test/') !== FALSE) { continue; }
            if (strpos($file, 'listShapes.php') !== FALSE) { continue; }
            if (strpos($file, 'jon.php') !== FALSE) { continue; }
            if (strpos($file, 'jontest.php') !== FALSE) { continue; }
            if (strpos($file, 'Contact_Vcard_Parse.php') !== FALSE) { continue; }
            if (strpos($file, 'JonController.php') !== FALSE) { continue; }
            if (strpos($file, '/x.php') !== FALSE) { continue; }
            $this->assertTestPhp($file);
        }
    }

    private function assertTestPhp($file) {
        $pattern = '/<<<<<<<|>>>>>>>|ning.loader.require\([^)]|XN_Content::delete.*comment(?!er)|(?<!function) xg_output_time|var_dump\(.*\)|xg_varDump\(.*\)|print_r|->debugHTML\(\)|split.*SHARE_THIS|dojo.animation|dojo.dnd|dojo.fx|dojo.html.\*|dojo.lfx|xg.index.dom'
                . '|Content::delete\(.*(?<!v|Gr)id(?!eshow)' // Pass objects to XN_Content::delete(), not IDs. See "Caching Approaches: Query Caching". [Jon Aquino 2007-02-09]
                . '|Content::delete\((?!XG_Cache::content).*->my' // Ditto [Jon Aquino 2007-02-09]
                . '|Content::delete\(XN_Content::load' // Instead, use Content::delete(XG_Cache::content [Jon Aquino 2007-02-09]
                . '|(?<!XN_Application::load..|app|item|attr|group->my|srcNode|attribute|attr.0.|\$v)->name\b'  // Use $widget->dir instead of $widget->name [Jon Aquino 2007-02-07]
                . '|config\[.title.\]' // Use $widget->title instead of $widget->config['title'], to get the internationalized defaults [Jon Aquino 2007-02-10]
                . '|(?<!_)nl2br\(' // Use xg_nl2br instead of nl2br [Jon Aquino 2007-03-31]
                . '|nl2br(?!.*xg_resize_embeds).*description' // Use xg_resize_embeds for freeform HTML (BAZ-970)  [Jon Aquino 2007-02-12]
                . '|multipart.form-data.*charset=utf-8' // Firefox doesn't seem to send multipart/form-data if charset=utf-8 is also specified (BAZ-1831) [Jon Aquino 2007-02-22]
                . '|onfig.*] *= *(htmlentities|htmlspecialchars)' // Don't escape values passed to config[] or privateConfig[] [Jon Aquino 2007-02-23]
                . '|W_Content::load\((?![^)]*(id|attachedTo|\$_GET..page))' // Use W_Content::create (which wraps) instead of W_Content::load (which re-queries) [Jon Aquino 2007-03-02]
                . '|HTML_Scrubber::scrub' // Use xg_scrub instead of HTML_Scrubber::scrub [Jon Aquino 2007-03-05]
                . '|(?<!mb_strlen\(.this->|mb_strlen..|return..)post->.{0,10}title' // Use BlogPost::getHtmlTitle($post) or BlogPost::getTextTitle($post) [Jon Aquino 2007-03-13]
                . '|xnhtmlentities.xg_html' // Use xnhtmlentities(xg_text [Jon Aquino 2007-03-21]
                . '|promote-add' // Use feature-add instead (BAZ-3935) [Jon Aquino 2007-08-01]
                . '|promote-remove' // Use feature-remove instead (BAZ-3935) [Jon Aquino 2007-08-01]
                . '|nls.*XG_LOCALE'
                . '|XG_ProfileSet' // Use XN_ProfileSet instead
                . '|my->contributorName' // Use contributorName instead
                . '/i';
        $contents = self::getFileContent($file);
        $contents = str_replace('if ($feature->name == \'indexing\')', "", $contents);
        $contents = str_replace('$a->name', "", $contents);
        $contents = str_replace('widget->name', "", $contents);
        $contents = str_replace('$invitation->name', "", $contents);
        $contents = str_replace('var_dump($errs, $object->export());', "", $contents);
        $contents = str_replace('#var_dump', "", $contents);
        $contents = str_replace('$importedContact->name', "", $contents);
        $contents = str_replace('promote-add.png', "", $contents);
        $contents = str_replace('promote-remove.png', "", $contents);
        $contents = str_replace('XN_Content::delete($user->my->previousThumbnailId);', "", $contents);
        $contents = str_replace('when XN_ProfileSet is in the real API', "", $contents);
        if (basename($file) == 'Page_InstanceHelper.php') { $contents = str_replace("config['title']", '', $contents); }
        if (basename($file) == 'Activity_LogHelperIPhone.php') { $contents = str_replace('$blogPost->title ?', '', $contents); }
        if (! preg_match($pattern, $contents)) {
            $this->assertTrue(TRUE);
        } else {
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (strpos($line, '[skip-SyntaxTest]') !== FALSE) { continue; }
                if (preg_match($pattern, $line, $matches)) {
                    if ($matches[0] == 'Content::delete(GroupInvitationRequest::loadOrCreate($group, $id') { continue; }
                    if ($matches[0] == 'post->title' && strpos($line, '$defaults[\'post_title\'] = $this->post->title;') !== FALSE) { continue; }
                    if ($matches[0] == 'post->title' && strpos($line, '$defaults[\'post_title\'] = $post->title;') !== FALSE) { continue; }
                    if ($matches[0] == 'Post->title' && strpos($line, 'Post->title?') !== FALSE) { continue; }
                    if ($matches[0] == "'like'," && strpos($line, '\'like\', $content->id') !== FALSE) { continue; }
                    if ($matches[0] == 'HTML_Scrubber::scrub' && basename($file) == 'XG_TemplateHelpers.php') { continue; }
                    if ($matches[0] == 'HTML_Scrubber::scrub' && strpos($line, '* Use HTML_Scrubber') !== FALSE) { continue; }
                    if ($matches[0] == 'HTML_Scrubber::scrub' && strpos($line, '@see HTML_Scrubber') !== FALSE) { continue; }
                    if ($matches[0] == 'ning.loader.require(\'' && basename($file) == 'footer.php') { continue; }
                    if ($matches[0] == 'ning.loader.require("' && basename($file) == 'XG_HtmlLayoutHelper.php') { continue; }
                    if ($matches[0] == 'ning.loader.require(\'' && basename($file) == 'header.php') { continue; }
                    if ($matches[0] == 'config[\'title\']' && basename($file) == 'FeatureController.php') { continue; }
                    if ($matches[0] == 'config[\'title\']' && basename($file) == 'XG_LayoutEditHelper.php') { continue; }
                    if ($matches[0] == 'config[\'title\']' && basename($file) == 'XG_ModuleHelper.php') { continue; }
                    if ($matches[0] == 'print_r' && basename($file) == 'facebookapi_php5_restlib.php') { continue; }
                    if ($matches[0] == 'Content::delete($group->my->iconId') { continue; }
                    if ($matches[0] == 'var_dump($args)') { continue; }
                    if ($matches[0] == 'var_dump(array_merge($args, array(\'feedUrl\' => $this->feedUrl)))') { continue; }
                    if ($matches[0] == 'var_dump(array_merge($args, array(\'username\' => $this->username)))') { continue; }
                    if ($matches[0] == "'like'," && strpos($line, '$filters[\'photoId\']') !== false) { continue; }
                    if ($matches[0] == 'XG_MessageCatalog_en_US' && preg_match('/XG_MessageCatalog/', $file)) { continue; }
                    if ($matches[0] == 'XG_MessageCatalog_en_US' && preg_match('/XG_TemplateHelpers/', $file)) { continue; }
                    if ($matches[0] == 'XN_Content::delete(W_Content::unwrap($comment' && basename($file) == 'Forum_BulkHelper.php') { continue; }
                    if ($matches[0] == 'XN_Content::delete(W_Content::unwrap($comment' && basename($file) == 'Page_BulkHelper.php') { continue; }
                    if ($matches[0] == 'XN_Content::delete($comment' && basename($file) == 'Comment.php') { continue; }
                    if ($matches[0] == 'XN_Content::delete($comment' && basename($file) == 'BlogPost.php') { continue; }
                    if ($matches[0] == 'var_dump($this->_layout)' && basename($file) == 'XG_Layout.php') { continue; }
                    if ($matches[0] == 'var_dump($var)' && basename($file) == 'XG_TemplateHelpers.php') { continue; }
                    if ($matches[0] == 'var_dump($xnresult)' && basename($file) == 'FlickrController.php') { continue; }
                    if (strpos($matches[0], 'xg_varDump') === 0 && basename($file) == 'XG_TemplateHelpers.php') { continue; }
                    if ($matches[0] == 'nls' && basename($file) == 'footer.php') { continue; }
                    if ($matches[0] == 'nl2br(' && strpos($file, 'widgets') === FALSE) { continue; }
                    if ($matches[0] == 'nl2br(' && basename($file) == 'log.php') { continue; }
                    if (strpos($line, 'print $this->debugHtml()') !== FALSE && basename($file) == 'XG_Query.php') { continue; }
                    if (strpos($line, 'echo $photo->debugHTML()') !== FALSE && basename($file) == 'PhotoController.php') { continue; }
                    if (strpos($line, 'echo $video->debugHTML()') !== FALSE && basename($file) == 'VideoController.php') { continue; }
                    if (strpos($matches[0], 'var_dump(Video_VideoHelper::embedPreviewFrameUrlAndMimeType(') !== FALSE && basename($file) == 'VideoController.php') { continue; }
                    if (strpos($line, 'Content::delete(ContactList::load') !== FALSE) { continue; }
                    if (strpos($line, 'error_log("Passed by this way with: "') !== FALSE) { continue; }
                    if (strpos($line, '("Unknown embed type: " . print_r($embed, true));') !== FALSE) { continue; }
                    if (strpos($line, '$this->name = \'Paul Lloyd\'') !== FALSE) { continue; }
                    if (strpos($line, 'Failed to insert " . print_r($embed, true)') !== FALSE) { continue; }
                    if (strpos($line, '$_GET[\'test_') !== FALSE) { continue; }
                    if (strpos($line, 'if ($user->my->thumbnailId) { XN_Content::delete($user->my->thumbnailId); }') !== FALSE) { continue; }
                    if (strpos($matches[0], 'nl2br') === 0 && basename($file) == 'PhotoController.php') { continue; }
                    if (strpos($matches[0], 'nl2br') === 0 && basename($file) == 'XG_FeedHelper.php') { continue; }
                    if (strpos($matches[0], 'nl2br') === 0 && basename($file) == 'VideoController.php') { continue; }
                    if (strpos($matches[0], 'nl2br') === 0 && basename($file) == 'manageComments.php') { continue; }
                    if (strpos($matches[0], 'nl2br') === 0 && basename($file) == 'rss.php') { continue; }
                    if (strpos($matches[0], 'nl2br($data[\'description') === 0 && basename($file) == 'BlogController.php') { continue; }
                    if (strpos($matches[0], 'nl2br') === 0 && strpos($file, 'XG_Message/') !== FALSE) { continue; }
                    if (strpos($matches[0], 'var_dump') === 0 && strpos($line, 'debug_show_random_number_in_member_box') !== FALSE) { continue; }
                    if ($matches[0] == 'XN_Content::delete($comment' && basename($file) == 'Event.php') { continue; }
                    if ($matches[0] == 'Post->id))) %>" title') { continue; }

                    // Skip existing uses of W_Content::load, to avoid changes we'd need to retest [Jon Aquino 2007-03-02]
                    if (strpos($matches[0], 'W_Content::load') === 0 && strpos($line, 'W_Content::load($photo)->setApproved(\'Y\');') !== FALSE && in_array(basename($file), array('BulkController.php', 'PhotoController.php'))) { continue; }
                    if (strpos($matches[0], 'W_Content::load') === 0 && strpos($line, 'W_Content::load($photo)->setApproved(\'N\');') !== FALSE && basename($file) == 'PhotoController.php') { continue; }
                    if (strpos($matches[0], 'W_Content::load') === 0 && strpos($line, '$photo = W_Content::load($photoObject);') !== FALSE && in_array(basename($file), array('FlickrController.php', 'PhotoController.php'))) { continue; }

                    $this->assertTrue(FALSE, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    public function testJs() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.js') as $file) {
            if (strpos($file, 'cache.js') !== false) { continue; }
            if (strpos($file, 'jquery') !== false) { continue; }
            if (strpos($file, '/dojo') !== FALSE) { continue; }
            if (strpos($file, '/js/lib/Test') !== FALSE) { continue; }
            if (strpos($file, '/adapter') !== FALSE) { continue; }
            if (strpos($file, 'xn_resources/widgets/lib/core.min.js') !== false) { continue; }
            $this->assertTestJs($file);
        }
    }

    private function assertTestJs($file) {
        $contents = self::getFileContent($file);
        $this->assertFalse(preg_match('/.{0,100},\s*\}/s', $contents, $matches), ',} in ' . $file . ': ' . $matches[0]);
        $this->assertTrue(basename($file) != 'en_US.js' || preg_match('/dojo.require\(.xg.index.i18n.\)/', $contents), $file);
        $pattern = '/dojo.xml.Parse|trim\(.*[^)]\.length\)+|console\.|(?<!show)(?<!util.)alert\([^)]+\)|ning.loader.require\([^)]|XG_LOCALE'
                . '|window.x' // Test code [Jon Aquino 2007-02-13]
                . '|setAttribute.._' // setAttribute does not work for new attributes beginning with an underscore - see Alan Williamson, "Safari Dom Exception 5 - setAttribute() gotcha", http://alan.blog-city.com/safari_setattribute.htm  [Jon Aquino 2007-05-11]
                . '|sync:.*true' // Avoid synchronous requests - they freeze Firefox [Jon Aquino 2007-07-02]
                . '|xg.global.locale' // No longer used (BAZ-3955) [Jon Aquino 2007-08-01]
                . '/i';
        if (! preg_match($pattern, $contents)) {
            $this->assertTrue(TRUE);
        } else {
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (preg_match($pattern, $line, $matches)) {
                    if ($matches[0] == 'dojo.xml.Parse' && basename($file) == 'util.js') { continue; }
                    if ($matches[0] == 'console.' && basename($file) == 'iui.js') { continue; }
                    if ($matches[0] == 'console.' && basename($file) == 'gadgets.js') { continue; }
                    if ($matches[0] == 'ning.loader.require' && basename($file) == 'i18n.js') { continue; }
                    if ($matches[0] == "alert('test_add_friend')") { continue; }
                    if ($matches[0] == "alert('test_add_friend')") { continue; }
                    if (strpos($matches[0], 'alert("Assertion failed') !== false) { continue; }
                    if (strpos($matches[0], 'alert(\'Assertion failed') !== false) { continue; }
                    if (strpos($matches[0], "alert(this._checkboxSelectMessage)") !== false) { continue; }
                    if (strpos($matches[0], "alert(xg.notes.nls.html('pleaseEnterNoteTitle") !== false) { continue; }
                    if (strpos($matches[0], 'sync:') !== false && strpos($line, 'VID-395') !== false) { continue; }
                    if (strpos($matches[0], 'sync:') !== false && basename($file) == 'import.js') { continue; }
                    if (strpos($matches[0], 'sync:') !== false && strpos($file, 'index/js/embeddable/list.js') !== false) {
                        // Will fix in BAZ-3802 [Jon Aquino 2007-07-22]
                        continue;
                    }
                    if ($matches[0] == "alert(errors.errors[0].error)") { continue; }
                    if (strpos($line, "heading.setAttribute('_numComments',") !== false) { continue; }
                    if (strpos($line, 'alert("Cannot save the note. Internal error."') !== false) { continue; }
                    if (strpos($line, 'alert("Internal error. Cannot find an image URL in the dialog response.")') !== false) { continue; }
                    if (strpos($line, 'alert(xg.notes.nls.text(\'pleaseEnterNoteTitle\'));') !== false) { continue; }
                    if (strpos($line, 'alert(xg.notes.nls.text(\'noteTitleTooLong\'));') !== false) { continue; }
                    if (strpos($line, "alert(notesStrings['YOU_ENTERED_INVALID_CHAR'])") !== false) { continue; }
                    if (strpos($line, "alert(notesStrings['NOTE_TITLE_TOO_LONG'])") !== false) { continue; }
                    if (strpos($line, "alert(\"Cannot send request. Previous request wasn't cleaned up properly\")") !== false) { continue; }
                    if (strpos($line, '// !') !== FALSE) { continue; }
                    $this->assertTrue(FALSE, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    private function format($match, $file, $line, $lineNumber) {
        return '[' . $match . '] in ' . $line . ' ' . $file . ' ' . $lineNumber . ' ***';
    }

}

class TestSearchController01 extends Index_SearchController {
    public static function getTypesToExclude() { return self::$typesToExclude; }
}

class TestBulkController01 extends Profiles_BulkController {
    public static function getTypesToExcludeFromRemovalByUser() { return self::$typesToExcludeFromRemovalByUser; }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
