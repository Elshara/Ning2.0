<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class XG_LanguageHelperTest extends UnitTestCase {

    public function testPhpCatalogPath() {
        $this->assertEqual(NF_APP_BASE . '/lib/XG_MessageCatalog_jp_HK.php', XG_LanguageHelper::phpCatalogPath('jp_HK'));
    }

    public function testCustomPhpCatalogPath() {
        $this->assertEqual(NF_APP_BASE . '/instances/main/messagecatalogs/XG_CustomMessageCatalog_jp_HK.php', XG_LanguageHelper::customPhpCatalogPath('jp_HK'));
    }

    public function testJavaScriptCatalogPath() {
        $this->assertEqual(NF_APP_BASE . '/xn_resources/widgets/shared/js/messagecatalogs/jp_HK.js', XG_LanguageHelper::javaScriptCatalogPath('jp_HK'));
    }

    public function testCustomJavaScriptCatalogPath() {
        $this->assertEqual(NF_APP_BASE . '/xn_resources/instances/shared/js/messagecatalogs/jp_HK.js', XG_LanguageHelper::customJavaScriptCatalogPath('jp_HK'));
    }

    public function testIsCustomLocale() {
        $this->assertTrue(XG_LanguageHelper::isCustomLocale('custom_12345'));
        $this->assertFalse(XG_LanguageHelper::isCustomLocale('en_US'));
    }

    public function testAddCustomLocaleMetadata() {
        W_Cache::getWidget('main')->config['customLocales'] = null;
        XG_LanguageHelper::addCustomLocaleMetadata('custom_12345', 'Swahili', 'fr_CA');
        $localesAndNames = XG_LanguageHelper::localesAndNames();
        $this->assertEqual('Swahili', $localesAndNames['custom_12345']);
        XG_LanguageHelper::removeCustomLocaleMetadata('custom_12345');
        $localesAndNames = XG_LanguageHelper::localesAndNames();
        $this->assertNull($localesAndNames['custom_12345']);
    }

    public function testCustomLocaleMetadataBackwardsCompatibility() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        W_Cache::getWidget('main')->config['customLocales'] = null;
        $this->assertEqual($json->encode(array()), $json->encode(TestLanguageHelper::customLocaleMetadata()));
        W_Cache::getWidget('main')->config['customLocales'] = serialize(array('custom_1' => 'Foo'));
        $this->assertEqual($json->encode(array()), $json->encode(TestLanguageHelper::customLocaleMetadata()));
        W_Cache::getWidget('main')->config['customLocales'] = $json->encode(array('custom_1' => 'Foo'));
        $this->assertEqual($json->encode(array('custom_1' => array('name' => 'Foo', 'baseLocale' => 'en_US'))), $json->encode(TestLanguageHelper::customLocaleMetadata()));
    }

    public function testBaseLocale() {
        W_Cache::getWidget('main')->config['customLocales'] = null;
        XG_LanguageHelper::addCustomLocaleMetadata('custom_12345', 'Swahili', 'fr_CA');
        $this->assertEqual('fr_CA', XG_LanguageHelper::baseLocale('custom_12345'));
        $this->assertEqual('en_GB', XG_LanguageHelper::baseLocale('en_GB'));
    }

    public function testName() {
        $this->assertEqual('English (U.S.)', XG_LanguageHelper::name('en_US'));
    }

    public function testWidgetTitleNames() {
        $widgetTitleNames = TestLanguageHelper::widgetTitleNames();
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/instances', 'widget-configuration.xml') as $filename) {
            if (strpos($filename, '/admin/') !== false) { continue; }
            if (strpos($filename, '/main/') !== false) { continue; }
            $this->assertTrue(preg_match('@<title>(.*?)</title>@u', file_get_contents($filename), $matches), $filename);
            $title = $matches[1];
            if (strpos($title, 'Latest Activity') !== false) { continue; }
            if (strpos($title, 'Pages') !== false) { continue; }
            $this->assertTrue($widgetTitleNames[$title], 'Missing from XG_LanguageHelper::$widgetTitleNames: ' . $title);
        }
    }

}

class TestLanguageHelper extends XG_LanguageHelper {
    public static function customLocaleMetadata() {
        return parent::customLocaleMetadata();
    }
    public static function widgetTitleNames() {
        return parent::$widgetTitleNames;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
