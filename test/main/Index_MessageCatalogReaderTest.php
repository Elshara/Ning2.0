<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageCatalogReader.php');
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageCatalogWriter.php');

class Index_MessageCatalogReaderTest extends BazelTestCase {

    public function testReadWrite() {
        $pathnames = glob(NF_APP_BASE . '/lib/XG_MessageCatalog_en_US.*');
        $this->assertTrue(count($pathnames) > 0);
        foreach ($pathnames as $pathname) {
            $this->assertTrue(preg_match('@XG_MessageCatalog_(.*).php@u', $pathname, $matches));
            self::doTestReadWrite($matches[1]);
        }
    }

    public function doTestReadWrite($locale) {
        // This test writes out the message catalog, reads it back in, and compares.
        // Usually problems happen when there are duplicate keys. [Jon Aquino 2008-08-30]
        $reader = new Index_MessageCatalogReader();
        $expectedPhp = file_get_contents(XG_LanguageHelper::phpCatalogPath($locale));
        $expectedPhp = preg_replace('@public static function countryCodes.*@s', '}', $expectedPhp);
        $expectedJavaScript = file_get_contents(XG_LanguageHelper::javaScriptCatalogPath($locale));
        $reader->read($expectedPhp);
        $reader->read($expectedJavaScript);
        $writer = new Index_MessageCatalogWriter();
        list($actualPhp, $actualJavaScript) = $writer->write($locale, $reader->getData());
        $this->assertEqual($this->normalizePhp($expectedPhp), $this->normalizePhp($actualPhp));
        $this->assertEqual($this->normalizeJavaScript($expectedJavaScript), $this->normalizeJavaScript($actualJavaScript));
    }

    private function normalizePhp($s) {
        $s = str_replace("XG_App::includeFileOnce('/lib/XG_MessageCatalog_en_US.php');", "", $s);
        $s = str_replace("XG_App::includeFileOnce('/lib/XG_AbstractMessageCatalog.php');", "", $s);
        $s = str_replace("XG_CustomMessageCatalog", "XG_MessageCatalog", $s);
        $s = preg_replace('@require_once NF_APP_BASE . \'/lib/XG_MessageCatalog_.._...php\';@u', '', $s);
        $s = preg_replace('@require_once\(NF_APP_BASE . \'/lib/XG_AbstractMessageCatalog.php\'\);@u', '', $s);
        $s = preg_replace('@extends XG_MessageCatalog_.._..@u', '', $s);
        $s = preg_replace('@extends XG_AbstractMessageCatalog@u', '', $s);
        $s = preg_replace('@//.*@u', '', $s);
        // Whitespace removed from here forward [Jon Aquino 2007-08-13]
        $s = str_replace("\r", "", $s);
        $s = str_replace("\t", "", $s);
        $s = str_replace("\n", "", $s);
        $s = str_replace(" ", "", $s);
        $s = str_replace(",)", ")", $s);
        $s = preg_replace('@/\*.*?\*/@u', '', $s);
        $s = preg_replace('@protected.*?(?=protected|public|private)@u', '', $s);
        $s = preg_replace('@publicstaticfunctionfor_testing.*?(?=protected|public|private)@u', '', $s);
        $s = str_replace('publicstaticfunctiongetMessagesForTesting(){returnself::$messages;}}', '}', $s);
        return $s;
    }

    private function normalizeJavaScript($s) {
        $s = str_replace("xg.custom.", "xg.", $s);
        $s = preg_replace('@//.*@u', '', $s);
        // Whitespace removed from here forward [Jon Aquino 2007-08-13]
        $s = str_replace("\r", "", $s);
        $s = str_replace("\t", "", $s);
        $s = str_replace("\n", "", $s);
        $s = str_replace(" ", "", $s);
        $s = preg_replace('@/\*.*?\*/@u', '', $s);
        return $s;
    }

    public function testReadPhp() {
        $reader = new Index_MessageCatalogReader();
        $reader->read(file_get_contents(NF_APP_BASE . '/lib/XG_MessageCatalog_en_US.php'));
        $data = $reader->getData();
        $this->assertEqual('Cancel', $data['CANCEL']);
        $this->assertTrue(strpos($data[Index_LanguageHelper::SPECIAL_RULES_KEY], 'N_SECONDS_AGO') !== false, $this->escape($data[Index_LanguageHelper::SPECIAL_RULES_KEY]));
        $this->assertTrue(strpos($data[Index_LanguageHelper::TAB_NAMES_KEY], 'Music') === false, $data[Index_LanguageHelper::TAB_NAMES_KEY]);
    }

    public function testReadEmpty() {
        $reader = new Index_MessageCatalogReader();
        $reader->read('foo');
        $data = $reader->getData();
        $this->assertEqual(array(), $data);
    }

    public function testReadJavaScript() {
        $reader = new Index_MessageCatalogReader();
        $reader->read(file_get_contents(NF_APP_BASE . '/xn_resources/widgets/shared/js/messagecatalogs/en_US.js'));
        $data = $reader->getData();
        $this->assertEqual('Please enter your email address', $data['xg.groups.nls.pleaseEnterEmailAddress']);
        $this->assertEqual("function(x) { return x + ' is not a valid email address'; }", $data['xg.groups.nls.xIsNotValidEmailAddress']);
    }

    public function testReadJavaScript2() {
        $reader = new Index_MessageCatalogReader();
        $s = <<<JAVASCRIPT
dojo.provide('xg.shared.nls.en_US');

dojo.require('xg.index.i18n');

/**
 * Texts for the English (United States) locale.
 */
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    // Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]
    uploadAPhotoEllipsis: 'Upload a Photo…', /* A comment */ /* Another comment */
    /* A comment */
    useExistingImage: 'Use existing image:', // A comment
    nStarsOutOfM: function(n,m) { return n + ' stars out of ' + m; }, // A comment
    yourRatedThis: function(n) {
        return 'You rated this item with ' + n + ' stars.';  // A comment
    }, // A comment
    badFunction: function(n) {
    youHaventRated: 'You haven\'t rated this item yet.' // A comment
});
JAVASCRIPT;
        $reader->read($s);
        $data = $reader->getData();
        $this->assertEqual('Upload a Photo…', $data['xg.shared.nls.uploadAPhotoEllipsis']);
        $this->assertEqual('Use existing image:', $data['xg.shared.nls.useExistingImage']);
        $this->assertEqual("function(n,m) { return n + ' stars out of ' + m; }", $data['xg.shared.nls.nStarsOutOfM']);
        $this->assertEqual("function(n) {
        return 'You rated this item with ' + n + ' stars.';  // A comment
    }", $data['xg.shared.nls.yourRatedThis']);
        $this->assertEqual('You haven\'t rated this item yet.', $data['xg.shared.nls.youHaventRated']);
    }

    public function testExtract() {
        $this->assertEqual(' quick ', TestReader::extract('The', 'brown', 'The quick brown fox jumps over the lazy dog'));
        $this->assertEqual(null, TestReader::extract('The', 'red', 'The quick brown fox jumps over the lazy dog'));
        $this->assertEqual(null, TestReader::extract('foo', 'brown', 'The quick brown fox jumps over the lazy dog'));
        $this->assertEqual(null, TestReader::extract('brown', 'The', 'The quick brown fox jumps over the lazy dog'));
        $this->assertEqual('', TestReader::extract('A', 'B', 'AB'));
        $this->assertEqual('x', TestReader::extract('A', 'B', 'AxB'));
    }

    public function testExtractWithTokens() {
        $this->assertEqual('The quick brown', TestReader::extract('The', 'brown', 'The quick brown fox jumps over the lazy dog', true));
        $this->assertEqual(null, TestReader::extract('The', 'red', 'The quick brown fox jumps over the lazy dog', true));
        $this->assertEqual(null, TestReader::extract('foo', 'brown', 'The quick brown fox jumps over the lazy dog', true));
        $this->assertEqual(null, TestReader::extract('brown', 'The', 'The quick brown fox jumps over the lazy dog', true));
        $this->assertEqual('AB', TestReader::extract('A', 'B', 'AB', true));
        $this->assertEqual('AxB', TestReader::extract('A', 'B', 'AxB', true));
    }

    public function testExtractFunctions() {
        $php = '
<?php

XG_App::includeFileOnce(\'/lib/XG_AbstractMessageCatalog.php\');

/**
 * Texts for the custom_12345 locale. This file was generated by the Manage > Language page.
 */
class XG_CustomMessageCatalog_custom_12345 extends XG_AbstractCustomMessageCatalog {

    /**
     * Returns a localized version of a string. The first argument is the message name, e.g., \'ADD_A_PHOTO\'.
     * Subsequent arguments are substitution values (if the message contains sprintf format elements).
     *
     * @param $args array the message name, plus optional substitution values
     * @return string the localized string
     * @see xg_html()
     */
    public static function text($args) {
        $s = $args[0];
        // Music widget
        if ($s == \'N_TRACKS_REMOVED_FROM_PLAYLIST\') {
            if ($args[1] == 1) {
                return $args[1].\' song from this playlist can\\\'t be displayed because it has been deleted or is no longer shared.\';
            } elseif ($args[1] > 1) {
                return $args[1].\' songs from this playlist can\\\'t be displayed because they have been deleted or are no longer shared.\';
            }
        }
        $text = self::$messages[$s];
        if ($text) { $args[0] = $text; }
        return call_user_func_array(\'sprintf\', $args);
    }

    /**
     * Message names and texts. Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;
     */
    private static $messages = array(
        \'I_AM_MEMBER\' => \' Hey, I\\\'m a member of: \',
        \'X_SAID\' => \'%s said…\',
        \'ABC\' => \'a
  b
  c\',
    );

    /**
     * If the given widget title is the default English title for the widget,
     * translate it into the current language.
     *
     * @param $widgetTitle string  The title of the widget
     * @return string  A translated title if the title is the English default; otherwise, the title unchanged
     */
    public static function translateDefaultWidgetTitle($widgetTitle) {
        if ($widgetTitle == \'Blog\') { return \'Blog\'; }
        if ($widgetTitle == \'RSS\') { return \'RSS\'; }
        if ($widgetTitle == \'Forum\') { return \'Forum\'; }
        if ($widgetTitle == \'Videos\') { return \'Videos\'; }
        if ($widgetTitle == \'Photos\') { return \'Photos\'; }
        if ($widgetTitle == \'Text Box\') { return \'Text Box\'; }
        if ($widgetTitle == \'Groups\') { return \'Groups\'; }
        if ($widgetTitle == \'Music\') { return \'Music\'; }
        return $widgetTitle;
    }

}';
        $this->assertNull(Index_MessageCatalogReader::extractPhpArray(''));
        $this->assertNull(Index_MessageCatalogReader::extractPhpSpecialRules(''));
        $this->assertNull(Index_MessageCatalogReader::extractPhpTabNamingRules(''));
        $this->assertEqual(trim('= array(
        \'I_AM_MEMBER\' => \' Hey, I\\\'m a member of: \',
        \'X_SAID\' => \'%s said…\',
        \'ABC\' => \'a
  b
  c\','), trim(Index_MessageCatalogReader::extractPhpArray($php)));
        $this->assertEqual(trim('// Music widget
        if ($s == \'N_TRACKS_REMOVED_FROM_PLAYLIST\') {
            if ($args[1] == 1) {
                return $args[1].\' song from this playlist can\\\'t be displayed because it has been deleted or is no longer shared.\';
            } elseif ($args[1] > 1) {
                return $args[1].\' songs from this playlist can\\\'t be displayed because they have been deleted or are no longer shared.\';
            }
        }'), trim(Index_MessageCatalogReader::extractPhpSpecialRules($php)));
        $this->assertEqual(trim('if ($widgetTitle == \'Blog\') { return \'Blog\'; }
        if ($widgetTitle == \'RSS\') { return \'RSS\'; }
        if ($widgetTitle == \'Forum\') { return \'Forum\'; }
        if ($widgetTitle == \'Videos\') { return \'Videos\'; }
        if ($widgetTitle == \'Photos\') { return \'Photos\'; }
        if ($widgetTitle == \'Text Box\') { return \'Text Box\'; }
        if ($widgetTitle == \'Groups\') { return \'Groups\'; }
        if ($widgetTitle == \'Music\') { return \'Music\'; }'), trim(Index_MessageCatalogReader::extractPhpTabNamingRules($php)));

        // $includeTokens = true [Jon Aquino 2007-09-06]
        $this->assertEqual(trim('static $messages = array(
        \'I_AM_MEMBER\' => \' Hey, I\\\'m a member of: \',
        \'X_SAID\' => \'%s said…\',
        \'ABC\' => \'a
  b
  c\',
    );'), trim(Index_MessageCatalogReader::extractPhpArray($php, true)));
        $this->assertEqual(trim('$s = $args[0];
        // Music widget
        if ($s == \'N_TRACKS_REMOVED_FROM_PLAYLIST\') {
            if ($args[1] == 1) {
                return $args[1].\' song from this playlist can\\\'t be displayed because it has been deleted or is no longer shared.\';
            } elseif ($args[1] > 1) {
                return $args[1].\' songs from this playlist can\\\'t be displayed because they have been deleted or are no longer shared.\';
            }
        }
        $text = self::$messages[$s];'), trim(Index_MessageCatalogReader::extractPhpSpecialRules($php, true)));
        $this->assertEqual(trim('translateDefaultWidgetTitle($widgetTitle) {
        if ($widgetTitle == \'Blog\') { return \'Blog\'; }
        if ($widgetTitle == \'RSS\') { return \'RSS\'; }
        if ($widgetTitle == \'Forum\') { return \'Forum\'; }
        if ($widgetTitle == \'Videos\') { return \'Videos\'; }
        if ($widgetTitle == \'Photos\') { return \'Photos\'; }
        if ($widgetTitle == \'Text Box\') { return \'Text Box\'; }
        if ($widgetTitle == \'Groups\') { return \'Groups\'; }
        if ($widgetTitle == \'Music\') { return \'Music\'; }
        return $widgetTitle;'), trim(Index_MessageCatalogReader::extractPhpTabNamingRules($php, true)));
    }

}

class TestReader extends Index_MessageCatalogReader {
    public static function extract($startSubstring, $endSubstring, $s, $includeTokens = false) {
        return parent::extract($startSubstring, $endSubstring, $s, $includeTokens);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
