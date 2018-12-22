<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_ConfigCachingApp.php');

class XG_ConfigCachingAppTest extends UnitTestCase {

    public function setUp() {
        TestConfigCachingApp::setData(null);
        TestConfigCachingApp::setCachingEnabled(TRUE);
        $this->adminConfigPath = $_SERVER['DOCUMENT_ROOT'] . '/instances/admin/widget-configuration.xml';
        $this->testConfigPath = $_SERVER['DOCUMENT_ROOT'] . '/xn_private/configcachingapptest-private-configuration.xml';
        // Ensure that private photo config file exists. [Jon Aquino 2008-05-16]
        W_Cache::getWidget('photo')->saveConfig();
        TestConfigCachingApp::setData(null);
        XN_Cache::remove(XG_ConfigCachingApp::CACHE_ID);
    }

    public function tearDown() {
        $this->setTestValueInAdminConfigFile(null);
        @unlink($this->testConfigPath);
        XN_Cache::remove('updating-app-configuration');
    }

    private function setTestValueInAdminConfigFile($x) {
        $contents = preg_replace('@<test>.*</test>@u', '', file_get_contents($this->adminConfigPath));
        if (mb_strlen($x)) { $contents = str_replace('</version>', '</version><test>' . xg_xmlentities($x) . '</test>', $contents); }
        file_put_contents($this->adminConfigPath, $contents);
    }

    public function testLoadData1() {
        $lowercaseRelativeUrl = strtolower(XN_Application::load()->relativeUrl);
        $this->assertNull(TestConfigCachingApp::getData());
        $x1 = mt_rand();
        $this->setTestValueInAdminConfigFile($x1);
        TestConfigCachingApp::loadData();
        $x2 = mt_rand();
        $this->setTestValueInAdminConfigFile($x2);
        TestConfigCachingApp::loadData();
        $data = TestConfigCachingApp::getData();
        $this->assertPattern('@<test>' . $x1 . '</test>@', $data['instances']['admin']['public-config']);
        XG_ConfigCachingApp::rebuildData();
        $data = TestConfigCachingApp::getData();
        $this->assertPattern('@<test>' . $x2 . '</test>@', $data['instances']['admin']['public-config']);
        $this->assertEqual(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/instances/photo/widget-configuration.xml'), $data['instances']['photo']['public-config']);
        $this->assertEqual(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/xn_private/photo-private-configuration.xml'), $data['instances']['photo']['private-config']);
        $this->assertEqual(array('Album.php', 'Photo.php', 'SlideshowPlayerImage.php'), $data['filenames']["/apps/$lowercaseRelativeUrl/widgets/photo/models"]);
        $this->assertIdentical(array(), $data['filenames']["/apps/$lowercaseRelativeUrl/widgets/photo/lib"]);
        $this->assertIdentical(array(), $data['filenames']["/apps/$lowercaseRelativeUrl/widgets/admin/models"]);
        $this->assertEqual(array('Notes_UrlHelper.php'), $data['filenames']["/apps/$lowercaseRelativeUrl/widgets/notes/lib"]);
    }

    public function testLoadData2() {
        $this->assertNull(TestConfigCachingApp::getData());
        TestConfigCachingApp::setCachingEnabled(FALSE);
        TestConfigCachingApp::loadData();
        $this->assertNull(TestConfigCachingApp::getData());
    }

    public function testLoadData3() {
        TestConfigCachingApp::loadData();
        $this->assertNotNull(XN_Cache::get('app-configuration'));
        TestConfigCachingApp::setCachingEnabled(FALSE);
        TestConfigCachingApp::loadData();
        $this->assertNull(XN_Cache::get('app-configuration'));
    }

    public function testRebuildData() {
        $this->assertNull(TestConfigCachingApp::getData());
        TestConfigCachingApp::setCachingEnabled(FALSE);
        XG_ConfigCachingApp::rebuildData();
        $this->assertNull(TestConfigCachingApp::getData());
    }

    private static $testData1 = array(
            'instances' => array(
                'photo' => array(
                    'public-config' => '<widget>1</widget>',
                    'private-config' => '<widget>2</widget>'),
                'video' => array(
                    'public-config' => '<widget>3</widget>',
                    'private-config' => '<widget>4</widget>')),
            'filenames' => array(
                '/apps/networkname/widgets/photo/models' => array('Photo.php'),
                '/apps/networkname/widgets/photo/lib' => array(),
                '/apps/networkname/widgets/video/models' => array('Video.php'),
                '/apps/networkname/widgets/video/lib' => array()));

    public function testGetInstances() {
        TestConfigCachingApp::setData(self::$testData1);
        $this->assertEqual(array('photo', 'video'), XG_ConfigCachingApp::getInstances());
        TestConfigCachingApp::setCachingEnabled(FALSE);
        $this->assertEqual(glob(NF_APP_BASE . '/instances/*'), XG_ConfigCachingApp::getInstances());
    }

    public function testGetInstanceIdentifier() {
        $this->assertEqual('foo', XG_ConfigCachingApp::getInstanceIdentifier('foo'));
        TestConfigCachingApp::setCachingEnabled(FALSE);
        $this->assertEqual($_SERVER['DOCUMENT_ROOT'] . '/instances/foo/', XG_ConfigCachingApp::getInstanceIdentifier('foo'));
    }

    public function testGetInstanceDirectory() {
        $this->assertEqual($_SERVER['DOCUMENT_ROOT'] . '/instances/foo/', XG_ConfigCachingApp::getInstanceDirectory('foo'));
        TestConfigCachingApp::setCachingEnabled(FALSE);
        $this->assertEqual('foo', XG_ConfigCachingApp::getInstanceDirectory('foo'));
    }

    public function testGetWidgetPublicConfig() {
        TestConfigCachingApp::setData(self::$testData1);
        $this->assertEqual('<?xml version="1.0"?>' . "\n" . "<widget>1</widget>\n", XG_ConfigCachingApp::getWidgetPublicConfig(XG_ConfigCachingApp::getInstanceIdentifier('photo'))->asXML());
        try {
            XG_ConfigCachingApp::getWidgetPublicConfig('aaaaa');
            $this->fail();
        } catch (NF_Exception $e) {
        }
        TestConfigCachingApp::setCachingEnabled(FALSE);
        $this->assertEqual(simplexml_load_string(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/instances/photo/widget-configuration.xml'))->asXML(), XG_ConfigCachingApp::getWidgetPublicConfig(XG_ConfigCachingApp::getInstanceIdentifier('photo'))->asXML());
        try {
            XG_ConfigCachingApp::getWidgetPublicConfig('aaaaa');
            $this->fail();
        } catch (NF_Exception $e) {
        }
    }

    public function testGetWidgetPrivateConfig() {
        $w = W_Cache::getWidget('photo');
        TestConfigCachingApp::setData(self::$testData1);
        $this->assertEqual('<?xml version="1.0"?>' . "\n" . "<widget>2</widget>\n", XG_ConfigCachingApp::getWidgetPrivateConfig($w)->asXML());
        TestConfigCachingApp::setCachingEnabled(FALSE);
        $this->assertEqual(simplexml_load_string(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/xn_private/photo-private-configuration.xml'))->asXML(), XG_ConfigCachingApp::getWidgetPrivateConfig($w)->asXML());
    }

    public function testPutWidgetPublicConfig1() {
        $x = mt_rand();
        $w = W_Cache::getWidget('admin');
        $contents = str_replace('</version>', '</version><test>' . xg_xmlentities($x) . '</test>', file_get_contents($this->adminConfigPath));
        XG_ConfigCachingApp::putWidgetPublicConfig($w, $contents);
        $this->assertPattern('@' . $x . '@', file_get_contents($this->adminConfigPath));
        $data = TestConfigCachingApp::getData();
        $this->assertPattern('@' . $x . '@', $data['instances']['admin']['public-config']);
        $data = unserialize(XN_Cache::get(XG_ConfigCachingApp::CACHE_ID));
        $this->assertPattern('@' . $x . '@', $data['instances']['admin']['public-config']);
    }

    public function testPutWidgetPublicConfig2() {
        TestConfigCachingApp::setCachingEnabled(FALSE);
        $x = mt_rand();
        $w = W_Cache::getWidget('admin');
        $contents = str_replace('</version>', '</version><test>' . xg_xmlentities($x) . '</test>', file_get_contents($this->adminConfigPath));
        XG_ConfigCachingApp::putWidgetPublicConfig($w, $contents);
        $this->assertPattern('@' . $x . '@', file_get_contents($this->adminConfigPath));
        $this->assertNull(TestConfigCachingApp::getData());
    }

    public function testPutWidgetPrivateConfig3() {
        $this->doTestPutWidgetPublicConfig(0);
        $this->assertNull(XN_Cache::get('updating-app-configuration'));
    }

    public function testPutWidgetPrivateConfig4() {
        XN_Cache::put('updating-app-configuration', time());
        $this->doTestPutWidgetPublicConfig(5); // 5-second XG_LockHelper wait timeout [Jon Aquino 2008-05-17]
    }

    private function doTestPutWidgetPublicConfig($expectedDelay) {
        XG_ConfigCachingApp::rebuildData();
        $data = TestConfigCachingApp::getData();
        $data['instances']['foo']['public-config'] = '<foo></foo>';
        TestConfigCachingApp::setData($data);
        XN_Cache::put('app-configuration', serialize($data));
        $data['instances']['foo']['public-config'] = '<bar></bar>';
        TestConfigCachingApp::setData($data);
        $x = mt_rand();
        $w = W_Cache::getWidget('admin');
        $contents = str_replace('</version>', '</version><test>' . xg_xmlentities($x) . '</test>', file_get_contents($this->adminConfigPath));
        $start = microtime(true);
        XG_ConfigCachingApp::putWidgetPublicConfig($w, $contents);
        $this->assertWithinMargin(microtime(true) - $start, (float)$expectedDelay, 0.5);
        $data = TestConfigCachingApp::getData();
        $this->assertPattern('@' . $x . '@', $data['instances']['admin']['public-config']);
        $this->assertEqual('<foo></foo>', $data['instances']['foo']['public-config']);
        $data = unserialize(XN_Cache::get(XG_ConfigCachingApp::CACHE_ID));
        $this->assertPattern('@' . $x . '@', $data['instances']['admin']['public-config']);
        $this->assertEqual('<foo></foo>', $data['instances']['foo']['public-config']);
    }

    public function testPutWidgetPrivateConfig1() {
        $x = mt_rand();
        $w = new TestBaseWidget('configcachingapptest', $this->testConfigPath);
        XG_ConfigCachingApp::putWidgetPrivateConfig($w, '<configcachingapptest>' . $x . '</configcachingapptest>');
        $this->assertPattern('@' . $x . '@', file_get_contents($this->testConfigPath));
        $data = TestConfigCachingApp::getData();
        $this->assertPattern('@' . $x . '@', $data['instances']['configcachingapptest']['private-config']);
        $data = unserialize(XN_Cache::get(XG_ConfigCachingApp::CACHE_ID));
        $this->assertPattern('@' . $x . '@', $data['instances']['configcachingapptest']['private-config']);
    }

    public function testPutWidgetPrivateConfig2() {
        TestConfigCachingApp::setCachingEnabled(FALSE);
        $x = mt_rand();
        $w = new TestBaseWidget('configcachingapptest', $this->testConfigPath);
        $contents = str_replace('</version>', '</version><test>' . xg_xmlentities($x) . '</test>', file_get_contents($this->adminConfigPath));
        XG_ConfigCachingApp::putWidgetPrivateConfig($w, $contents);
        $this->assertPattern('@' . $x . '@', file_get_contents($this->testConfigPath));
        $this->assertNull(TestConfigCachingApp::getData());
    }

    public function testFindPhpInDirectory1() {
        $lowercaseRelativeUrl = strtolower(XN_Application::load()->relativeUrl);
        TestConfigCachingApp::setData(self::$testData1);
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("aaaaaaa"));
        $this->assertIdentical(array('Photo.php'), XG_ConfigCachingApp::findPhpInDirectory("/apps/networkname/widgets/photo/models"));
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("/apps/networkname/widgets/photo/lib"));
        $this->assertIdentical(array('Photo.php'), XG_ConfigCachingApp::findPhpInDirectory("/apps/networkname/widgets/photo/models"));
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("/apps/networkname/widgets/notes/lib"));
        $this->assertIdentical(array('Notes_UrlHelper.php'), XG_ConfigCachingApp::findPhpInDirectory("/apps/$lowercaseRelativeUrl/widgets/notes/lib"));
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("/apps/$lowercaseRelativeUrl/widgets/photo/lib"));
    }

    public function testFindPhpInDirectory2() {
        TestConfigCachingApp::setCachingEnabled(FALSE);
        $lowercaseRelativeUrl = strtolower(XN_Application::load()->relativeUrl);
        TestConfigCachingApp::setData(self::$testData1);
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("aaaaaaa"));
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("/apps/networkname/widgets/photo/models"));
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("/apps/networkname/widgets/photo/lib"));
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("/apps/networkname/widgets/photo/models"));
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("/apps/networkname/widgets/notes/lib"));
        $this->assertIdentical(array('Notes_UrlHelper.php'), XG_ConfigCachingApp::findPhpInDirectory("/apps/$lowercaseRelativeUrl/widgets/notes/lib"));
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("/apps/$lowercaseRelativeUrl/widgets/photo/lib"));
    }

    public function testFindPhpInDirectory3() {
        $lowercaseRelativeUrl = strtolower(XN_Application::load()->relativeUrl);
        TestConfigCachingApp::setData(array());
        $this->assertIdentical(array('Notes_UrlHelper.php'), XG_ConfigCachingApp::findPhpInDirectory("/apps/$lowercaseRelativeUrl/widgets/notes/lib"));
        TestConfigCachingApp::setData(array('filenames' => array("/apps/$lowercaseRelativeUrl/widgets/notes/lib" => array())));
        $this->assertIdentical(array(), XG_ConfigCachingApp::findPhpInDirectory("/apps/$lowercaseRelativeUrl/widgets/notes/lib"));
        TestConfigCachingApp::setData(array('filenames' => array("/apps/$lowercaseRelativeUrl/widgets/notes/lib" => null)));
        $this->assertIdentical(array('Notes_UrlHelper.php'), XG_ConfigCachingApp::findPhpInDirectory("/apps/$lowercaseRelativeUrl/widgets/notes/lib"));
        TestConfigCachingApp::setCachingEnabled(FALSE);
        $this->assertIdentical(array('Notes_UrlHelper.php'), XG_ConfigCachingApp::findPhpInDirectory("/apps/$lowercaseRelativeUrl/widgets/notes/lib"));
    }

}

class TestConfigCachingApp extends XG_ConfigCachingApp {
    public function isCachingEnabled() { return parent::$cachingEnabled; }
    public function setCachingEnabled($cachingEnabled) { parent::$cachingEnabled = $cachingEnabled; }
    public function getData() { return parent::$data; }
    public function setData($data) { parent::$data = $data; }
    public static function loadData() { parent::loadData(); }
}

class TestBaseWidget extends W_BaseWidget {
    public $dir;
    private $privateConfigPath;
    public function __construct($dir, $privateConfigPath) {
        $this->dir = $dir;
        $this->privateConfigPath = $privateConfigPath;
    }
    public function getPrivateConfigPath() {
        return $this->privateConfigPath;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
