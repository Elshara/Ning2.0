<?php
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageCatalogReader.php');

/**
 * Tests I18N strings for a particular translation.
 */
abstract class AbstractLanguageTest extends UnitTestCase {

    public function testInvalidCharacters() {
        self::showLanguageEditorNote($this->language);
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*' . $this->language . '.*') as $filename) {
            $lineNumber = 0;
            foreach (explode("\n", file_get_contents($filename)) as $line) {
                $lineNumber++;
                if (preg_match('@[^\r' . $this->allowedCharacters . ']@ui', $line, $matches)) {
                    echo 'Invalid character: ' . $matches[0] . ' – ' . $line;
                    $this->assertFalse(true, $filename . ' ' . $lineNumber . ' ***');
                }
            }
        }
        echo '<br /><br />';
    }

    public function testJavascriptKeysMissing() {
        self::showLanguageEditorNote($this->language);
        $reader = new Index_MessageCatalogReader();
        $reader->read(file_get_contents(XG_LanguageHelper::javaScriptCatalogPath('en_US')));
        $englishKeys = $reader->getData();
        $reader = new Index_MessageCatalogReader();
        $reader->read(file_get_contents(XG_LanguageHelper::javaScriptCatalogPath($this->language)));
        $otherKeys = $reader->getData();
        foreach ($englishKeys as $name => $message) {
            $this->assertTrue($otherKeys[$name], $name . ' => ' . $message);
        }
    }

    public function testJavascriptKeysNotUsed() {
        self::showLanguageEditorNote($this->language);
        $reader = new Index_MessageCatalogReader();
        $reader->read(file_get_contents(XG_LanguageHelper::javaScriptCatalogPath('en_US')));
        $englishKeys = $reader->getData();
        $reader = new Index_MessageCatalogReader();
        $reader->read(file_get_contents(XG_LanguageHelper::javaScriptCatalogPath($this->language)));
        $otherKeys = $reader->getData();
        foreach ($otherKeys as $name => $message) {
            $this->assertTrue($englishKeys[$name], $name . ' => ' . $message);
        }
    }

    public function testPhpKeysMissing() {
        self::showLanguageEditorNote($this->language);
        foreach(XG_TestHelper::globr(NF_APP_BASE, 'XG_MessageCatalog_en_US.php') as $file) {
            $otherFile = str_replace('en_US', $this->language, $file);
            $otherFileDisplay = str_replace($_SERVER['DOCUMENT_ROOT'], '', $otherFile);
            $englishKeys = XG_TestHelper::phpI18NKeys($file);
            $otherKeys = XG_TestHelper::phpI18NKeys($otherFile);
            foreach ($englishKeys as $key => $line) {
                $this->assertTrue($otherKeys[$key], $line . ' missing in ' . $otherFileDisplay);
            }
        }
    }

    public function testPhpKeysNotUsed() {
        self::showLanguageEditorNote($this->language);
        foreach(XG_TestHelper::globr(NF_APP_BASE, 'XG_MessageCatalog_en_US.php') as $file) {
            $otherFile = str_replace('en_US', $this->language, $file);
            $otherFileDisplay = str_replace($_SERVER['DOCUMENT_ROOT'], '', $otherFile);
            $englishKeys = XG_TestHelper::phpI18NKeys($file);
            $otherKeys = XG_TestHelper::phpI18NKeys($otherFile);
            foreach ($otherKeys as $key => $line) {
                $this->assertTrue($englishKeys[$key], $line . ' not used in ' . $otherFileDisplay);
            }
        }
    }

    public function testMultilineMessages() {
        self::showLanguageEditorNote($this->language);
        foreach(XG_TestHelper::globr(NF_APP_BASE, 'XG_MessageCatalog_en_US.php') as $file) {
            $otherFile = str_replace('en_US', $this->language, $file);
            $englishKeys = XG_TestHelper::phpI18NKeys($file);
            $otherKeys = XG_TestHelper::phpI18NKeys($otherFile);
            foreach ($otherKeys as $key => $line) {
                $englishMessageHasNewlines = strpos($englishKeys[$key], "\n") !== false;
                $otherMessageHasNewlines = strpos($otherKeys[$key], "\n") !== false;
                $this->assertTrue(! $englishMessageHasNewlines || $otherMessageHasNewlines, 'Missing newlines: ' . $key . ' ' . $englishKeys[$key] . ' ' . $key . ' ' . $otherKeys[$key]);
            }
        }
    }

    private static $languageEditorNoteShown = false;

    private static function showLanguageEditorNote($locale) {
        if (self::$languageEditorNoteShown) { return; }
        self::$languageEditorNoteShown = true; ?>
        <style>
            .fail { color: red; } pre { background-color: lightgray; }
            .tip {
                background: #ffffc7;
                border: 1px solid #ffcc00;
                padding: 1em;
                float: left;
                font-family: Verdana, Arial, Helvetica, sans-serif;
                font-size: 14px;
            }
            .tip em {
                color: red;
                font-style: normal;
            }
        </style>
        <p class="tip"><em>Tip:</em> The <a href="/main/language/edit?locale=<%= $locale %>">Language Editor</a> makes it easy to edit your translation and find out what texts are missing.</p>
        <br style="clear:left" />
        <?php
    }

}

