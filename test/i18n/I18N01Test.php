<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_MessageCatalog_en_US.php');

class I18N01Test extends UnitTestCase {

    public function __construct() {
        $this->allPhpFiles = XG_TestHelper::globr(NF_APP_BASE, '*.php');
        $this->phpMessageCatalogFiles = XG_TestHelper::globr(NF_APP_BASE, 'XG_MessageCatalog_??_??.php');
        $this->jsMessageCatalogFiles = XG_TestHelper::globr(NF_APP_BASE . '/xn_resources/widgets/shared/js/messagecatalogs', '??_??.js');
        $this->enUsLines = explode("\n", file_get_contents(NF_APP_BASE . '/lib/XG_MessageCatalog_en_US.php'));
        $this->referenceKeys = array();
        $this->loadReferenceKeys();
    }

    private function loadReferenceKeys() {
        $this->referenceKeys = array('php' => array(), 'js' => array());
        foreach (array_merge($this->phpMessageCatalogFiles, $this->jsMessageCatalogFiles) as $file) {
            if (mb_strstr($file, 'en_US') === false) { continue; }
            $contents = file_get_contents($file);
            $namespace = null;
            foreach (explode("\n", $contents) as $line) {
                $key = null;
                if (preg_match('/\$a\s*\[\'([^\']+)\'\]\s*\[\'([^\']+)\'\]/u', $line, $match)) {
                    $key = $match[1] . ':' . $match[2];
                } else if (preg_match('/\s*\'([^\']+)\'\s*=>\s*\'/u', $line, $match)) {
                    $key = $match[1];
                } else if (preg_match('/dojo\.lang\.mixin\s*\(([^,]+)/', $line, $match)) {
                    $namespace = trim($match[1]);
                } else if (! is_null($namespace) && preg_match('/\s*([^:\s]+):\s*(?:\'|function)/', $line, $match)) {
                    $key = $namespace . '.' . trim($match[1]);
                }
                if (! is_null($key)) {
                    $rootNamespace = is_null($namespace) ? 'php' : 'js';
                    $this->referenceKeys[$rootNamespace][$key] = 1;
                }
            }
        }
    }

    public function testFindDuplicateAndMissingKeys() {
        $this->findDuplicateAndMissingKeysPhp();
        $this->findDuplicateAndMissingKeysJs();
    }

    private function findDuplicateAndMissingKeysPhp() {
        foreach ($this->phpMessageCatalogFiles as $file) {
            $contents = file_get_contents($file);
            $keys = array();
            $lineNum = 1;
            foreach (explode("\n", $contents) as $line) {
                if (preg_match('/\$a\s*\[\'([^\']+)\'\]\s*\[\'([^\']+)\'\]/u', $line, $match)) {
                    $key = $match[1] . ':' . $match[2];
                } else if (preg_match('/\s*\'([^\']+)\'\s*=>\s*\'/u', $line, $match)) {
                    $key = $match[1];
                } else {
                    $key = null;
                }
                if (! is_null($key)) {
                    $this->assertFalse(array_key_exists($key, $keys), "Duplicate key [$key] (line [" . $keys[$key] . "]) on line [$lineNum] of [$file]");
                    $keys[$key] = $lineNum;
                }
                $lineNum++;
            }

            if ($_GET['testMissingKeys']) {
                // check for missing keys using en_US as reference
                foreach (array_keys($this->referenceKeys['php']) as $key) {
                    $this->assertTrue(array_key_exists($key, $keys), "Missing key [$key] in [$file]");
                }
            }
        }
    }

    private function findDuplicateAndMissingKeysJs() {
        foreach ($this->jsMessageCatalogFiles as $file) {
            $contents = file_get_contents($file);
            $keys = array();
            $lineNum = 1;
            $namespace = null;
            foreach (explode("\n", $contents) as $line) {
                if (preg_match('/dojo\.lang\.mixin\s*\(([^,]+)/', $line, $match)) {
                    $namespace = trim($match[1]);
                } else if (! is_null($namespace) && preg_match('/\s*([^:\s]+):\s*(?:\'|function)/', $line, $match)) {
                    $key = trim($match[1]);
                    $this->assertFalse(array_key_exists($namespace . '.' . $key, $keys), "Duplicate key [$key] (line [" . $keys[$namespace . '.' . $key] . "]) namespace [$namespace] on line [$lineNum] of [$file]");
                    $keys[$namespace . '.' . $key] = $lineNum;
                }
                $lineNum++;
            }

            if ($_GET['testMissingKeys']) {
                // check for missing keys using en_US as reference
                foreach (array_keys($this->referenceKeys['js']) as $key) {
                    $this->assertTrue(array_key_exists($key, $keys), "Missing key [$key] in [$file]");
                }
            }
        }
    }

    public function testUnusedJavascriptMessages() {
        foreach (XG_TestHelper::javascriptI18NKeys(XG_LanguageHelper::javaScriptCatalogPath('en_US')) as $namespace => $messages) {
            if ($namespace == 'xg.uploader.nls') { continue; }
            foreach(XG_TestHelper::globr(NF_APP_BASE . '/xn_resources/widgets' . str_replace('xg', '', str_replace('nls', '', str_replace('.' , '/', $namespace))) . 'js', '*.js') as $file) {
                $contents = file_get_contents($file);
                foreach (explode("\n", $contents) as $line) {
                    if (strpos($line, 'nls') === FALSE) { continue; }
                    foreach ($messages as $name => $message) {
                        if (strpos($line, $name) !== FALSE) { unset($messages[$name]); }
                    }
                }
            }
            if ($namespace == 'xg.profiles.nls') {
                unset($messages['bulkConfirm_blockSender']);
                unset($messages['bulkConfirm_delete']);
            }
            if ($namespace == 'xg.shared.nls') {
                unset($messages['addTags']);
                unset($messages['editLocation']);
                unset($messages['editYourTags']);
                unset($messages['editTypes']);
            }
            if ($namespace == 'xg.index.nls') {
                unset($messages['yes']);
            }
            if ($namespace == 'xg.notes.nls') {
                unset($messages['pleaseEnterNoteEntry']);
                unset($messages['noteExists']);
            }
            $this->assertEqual(0, count($messages), 'Unused JavaScript messages in ' . $namespace . ': ' . implode(', ', array_keys($messages)));
        }
    }

    public function testMissingJavascriptMessages() {
        foreach (XG_TestHelper::javascriptI18NKeys(XG_LanguageHelper::javaScriptCatalogPath('en_US')) as $namespace => $messages) {
            foreach(XG_TestHelper::globr(NF_APP_BASE . '/xn_resources/widgets' . str_replace('xg', '', str_replace('nls', '', str_replace('.' , '/', $namespace))) . 'js', '*.js') as $file) {
                if (mb_strpos($file, 'quickadd') !== FALSE) { continue; }
                $contents = file_get_contents($file);
                foreach (explode("\n", $contents) as $line) {
                    if (strpos($line, 'nls') === FALSE) { continue; }
                    preg_match_all('/(?:nls.text|nls.html)\(.(\w+)./', $line, $matches);
                    foreach ($matches[1] as $name) {
                        if (mb_strpos($line, 'selectNoneShowing') !== false) { continue; }
                        if (mb_strpos($line, 'bulkConfirm_') !== false) { continue; }
                        if (mb_strpos($line, 'this._addKey') !== false) { continue; }
                        if (mb_strpos($line, 'this._editKey') !== false) { continue; }
                        $this->assertTrue($messages[$name], 'Missing JavaScript message "' . $name . '" in ' . $line . ', ' . $file);
                    }
                }
            }
        }
    }

    public function testDuplicatePhpMessages() {
        $messages = array();
        foreach ($this->enUsLines as $line) {
            if (preg_match("/'(.+)' *=> *'(.+)'/", $line, $matches)) {
                if ($messages[$matches[1]]) {
                    $this->assertEqual($matches[1] . ' => ' . $messages[$matches[1]], $matches[1] . ' => ' . $matches[2]);
                }
                $messages[$matches[1]] = $matches[2];
            }
        }
    }

    public function testUnusedPhpMessages() {
        $messages = array();
        foreach ($this->enUsLines as $line) {
            if (strpos($line, 'APPNAME_NOW_HAS_') !== false) { continue; }
            if (strpos($line, 'MAIL_INBOX') !== false) { continue; }
            if (strpos($line, 'MAIL_SENT') !== false) { continue; }
            if (strpos($line, 'MAIL_ARCHIVE') !== false) { continue; }
            if (strpos($line, 'REPLY_TO') !== false) { continue; }
            if (strpos($line, 'FORWARD_TO') !== false) { continue; }
            if (strpos($line, 'NO_SUBJECT_PAREN') !== false) { continue; }
            if (strpos($line, 'NO_MESSAGES_IN_INBOX') !== false) { continue; }
            if (strpos($line, 'NO_MESSAGES_IN_ALERTS') !== false) { continue; }
            if (strpos($line, 'NO_MESSAGES_IN_SENT') !== false) { continue; }
            if (strpos($line, 'NO_MESSAGES_IN_ARCHIVE') !== false) { continue; }
            if (strpos($line, 'STATUS_MESSAGE_') !== false) { continue; }
            if (strpos($line, 'POSTED_BY_ME_LINK_ON_X_AT_X_NO_DASH') !== false) { continue; }
            if (strpos($line, 'POSTED_BY_X_ON_X_AT_X_NO_DASH') !== false) { continue; }
            if (strpos($line, 'YOU_ADDED_AN_APPLICATION') !== false) { continue; }
            if (strpos($line, 'YOU_ADDED_THE_X_APPLICATION') !== false) { continue; }
            if (strpos($line, 'X_ADDED_AN_APPLICATION') !== false) { continue; }
            if (strpos($line, 'X_ADDED_THE_Y_APPLICATION') !== false) { continue; }
            if (strpos($line, 'YOU_REVIEWED_AN_APPLICATION') !== false) { continue; }
            if (strpos($line, 'YOU_REVIEWED_THE_X_APPLICATION') !== false) { continue; }
            if (strpos($line, 'X_REVIEWED_AN_APPLICATION') !== false) { continue; }
            if (strpos($line, 'X_REVIEWED_THE_Y_APPLICATION') !== false) { continue; }
            if (strpos($line, 'MEMBERS_WITH_APPNAME') !== false) { continue; }
            if (strpos($line, 'FRIENDS_WITH_APPNAME') !== false) { continue; }
            if (strpos($line, 'MEMBERS_WITH_APPLICATION') !== false) { continue; }
            if (strpos($line, 'FRIENDS_WITH_APPLICATION') !== false) { continue; }
            if (strpos($line, 'UNABLE_TO_DISPLAY_MEMBERS_AT_THIS_TIME') !== false) { continue; }
            if (strpos($line, 'UNABLE_TO_DISPLAY_FRIENDS_AT_THIS_TIME') !== false) { continue; }
            if (strpos($line, 'APP_CATEGORY_') !== false) { continue; }
            if (preg_match("/'(?!COUNTRY|GENDER)(.+)' *=>/", $line, $matches)) {
                $messages[$matches[1]] = $matches[1];
            }
        }
        foreach($this->allPhpFiles as $file) {
            if (strpos($file, 'XG_MessageCatalog') !== FALSE) { continue; }
            $contents = file_get_contents($file);
            foreach ($messages as $message) {
                $pos = 0;
                while (FALSE !== ($pos = strpos($contents, $message,$pos))) {
                    $p	= $contents[$pos-1];
                    $n	= $contents[$pos+strlen($message)];
                    if( ($p=='"' || $p=="'") && ($n=='"' || $n=="'") ) { unset($messages[$message]); continue 2;}
                    $pos += strlen($message);
                }
            }
        }
        $this->assertEqual(0, count($messages), 'Unused PHP messages: ' . implode(', ', $messages));
    }

    public function testMissingPhpMessages() {
        $messages = array();
        $files = array();
        foreach($this->allPhpFiles as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            $contents = file_get_contents($file);
            preg_match_all('/(?:xg_xml|xg_html|xg_text)\(["\'](\w+)./', $contents, $matches);
            foreach ($matches[1] as $message) {
                if (strpos($message, 'APPNAME_NOW_HAS_') !== false) { continue; }
                if (strpos($message, 'NO_MESSAGES_IN_') !== false) { continue; }
                if (strpos($message, 'STATUS_MESSAGE_') !== false) { continue; }
                if ($message === 'MAIL_') { continue; }
                if ($message === 'YOU_') { continue; }
                if ($message === 'X_') { continue; }
               
                $messages[$message] = $message;
                $files[$message][] = mb_substr($file, mb_strlen($_SERVER['DOCUMENT_ROOT']));
            }
        }
        $contents = file_get_contents(NF_APP_BASE . '/lib/XG_MessageCatalog_en_US.php');
        foreach ($messages as $message) {
            if (mb_strpos($contents, "'$message'") !== false) {
                unset($messages[$message]);
            }
        }
        unset($messages['COUNTRY_']);
        foreach ($messages as $message) {
            $this->fail("Missing: $message (used in " . implode(', ', $files[$message]));
        }
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
