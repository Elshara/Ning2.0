<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Version.php');

Mock::generate('stdClass', 'MockW_Widget', array('saveConfig'));
Mock::generate('stdClass', 'MockXN_Version', array('currentCodeVersion', 'codeIsNewerThan', 'appWideCodeUpgrade'));
Mock::generate('stdClass', 'MockXN_Shape', array('setAttribute', 'save', 'load'));

/**
 * This class exposes protected bits of XG_Version for access by tests */
class XG_VersionStub extends XG_Version {
    public static function migratePrivacySettings() { parent::migratePrivacySettings(); }
    public static function setRevision($r) { parent::$revision = $r; }
    public static function setRelease($r)  { parent::$release  = $r; }
}
class XG_VersionTest extends BazelTestCase {

    public function testEmptyVersion() {
        XG_VersionStub::setRevision('');
        $this->assertTrue(preg_match('@0$@', XG_Version::currentCodeVersion()));
    }

    public function testBaseVersion() {
        XG_VersionStub::setRevision('$Revision$');
        $this->assertTrue(preg_match('@0$@', XG_Version::currentCodeVersion()));
    }

    public function testSomeVersion() {
        XG_VersionStub::setRevision('$Revision: 3 $');
        $this->assertTrue(preg_match('@3$@', XG_Version::currentCodeVersion()));
    }

    public function testCompareByParts() {
        $this->assertTrue(TestVersion::compareByParts('1', '1') == 0);
        $this->assertTrue(TestVersion::compareByParts('1', '2') < 0);
        $this->assertTrue(TestVersion::compareByParts('2', '1') > 0);
        $this->assertTrue(TestVersion::compareByParts('1.2', '1') > 0);
        $this->assertTrue(TestVersion::compareByParts('1.2', '1.1') > 0);
        $this->assertTrue(TestVersion::compareByParts('1.2.1', '1.2') > 0);
        $this->assertTrue(TestVersion::compareByParts('1.2.2', '1.2.1') > 0);
        $this->assertTrue(TestVersion::compareByParts('1.2.2', '1.2.10') < 0);
    }

    public function testCompareByReleaseAndRevision() {
        $this->assertTrue(TestVersion::compareByReleaseAndRevision('1', '1') == 0);
        $this->assertTrue(TestVersion::compareByReleaseAndRevision('1', '2') < 0);
        $this->assertTrue(TestVersion::compareByReleaseAndRevision('1:0', '1:0') == 0);
        $this->assertTrue(TestVersion::compareByReleaseAndRevision('1:0', '1:1') < 0);
        $this->assertTrue(TestVersion::compareByReleaseAndRevision('2:5', '3:3') < 0);
        $this->assertTrue(TestVersion::compareByReleaseAndRevision('2:5', '3') > 0);
        $this->assertTrue(TestVersion::compareByReleaseAndRevision('2:1', '3') < 0);
        $this->assertTrue(TestVersion::compareByReleaseAndRevision('3', '2:5') < 0);
        $this->assertTrue(TestVersion::compareByReleaseAndRevision('3', '2:1') > 0);
    }

    public function testCurrentCodeVersion() {
        XG_VersionStub::setRevision('$Revision: 47 $');
        XG_VersionStub::setRelease(
                'HeadURL: http://svn.collab.net/repos/trunk/lib/XG_Version.php $');
        $this->assertEqual('47', XG_Version::currentCodeVersion());
    }

    public function testCurrentCodeVersion2() {
        XG_VersionStub::setRevision('$Revision: 47 $');
        XG_VersionStub::setRelease(
                'HeadURL: http://svn.collab.net/repos/tags/1.4/lib/XG_Version.php $');
        $this->assertEqual('1.4:47', XG_Version::currentCodeVersion());
    }

    public function testCurrentCodeVersion3() {
        XG_VersionStub::setRevision('$Revision: 47 $');
        XG_VersionStub::setRelease(
                'HeadURL: http://svn.collab.net/repos/branches/1.4/lib/XG_Version.php $');
        $this->assertEqual('1.4:47', XG_Version::currentCodeVersion());
    }

    public function testCodeIsNewer() {
        XG_VersionStub::setRevision('$Revision: 47 $');
        XG_VersionStub::setRelease(
                'HeadURL: http://svn.collab.net/repos/trunk/lib/XG_Version.php $');
        $this->assertTrue(TestVersion::codeIsNewerThan('1'));
        $this->assertFalse(TestVersion::codeIsNewerThan('47'));
        $this->assertFalse(TestVersion::codeIsNewerThan('100'));
        $this->assertTrue(TestVersion::codeIsNewerThan('1.1:1'));
        $this->assertFalse(TestVersion::codeIsNewerThan('1.2.1:50'));
    }

    public function testCodeIsNewer2() {
        XG_VersionStub::setRevision('$Revision: 47 $');
        XG_VersionStub::setRelease(
                'HeadURL: http://svn.collab.net/repos/tags/1.4/lib/XG_Version.php $');
        $this->assertTrue(TestVersion::codeIsNewerThan('1'));
        $this->assertFalse(TestVersion::codeIsNewerThan('47'));
        $this->assertFalse(TestVersion::codeIsNewerThan('100'));
        $this->assertTrue(TestVersion::codeIsNewerThan('1.1:1'));
        $this->assertTrue(TestVersion::codeIsNewerThan('1.2.1:50'));
        $this->assertTrue(TestVersion::codeIsNewerThan('1.4:20'));
        $this->assertFalse(TestVersion::codeIsNewerThan('1.4:50'));
        $this->assertFalse(TestVersion::codeIsNewerThan('1.4.1:1'));
    }

    public function testCodeIsNewer3() {
        XG_VersionStub::setRevision('$Revision: 47 $');
        XG_VersionStub::setRelease(
                'HeadURL: http://svn.collab.net/repos/branches/1.4/lib/XG_Version.php $');
        $this->assertTrue(TestVersion::codeIsNewerThan('1'));
        $this->assertFalse(TestVersion::codeIsNewerThan('47'));
        $this->assertFalse(TestVersion::codeIsNewerThan('100'));
        $this->assertTrue(TestVersion::codeIsNewerThan('1.1:1'));
        $this->assertTrue(TestVersion::codeIsNewerThan('1.2.1:50'));
        $this->assertTrue(TestVersion::codeIsNewerThan('1.4:20'));
        $this->assertFalse(TestVersion::codeIsNewerThan('1.4:50'));
        $this->assertFalse(TestVersion::codeIsNewerThan('1.4.1:1'));
    }

    public function testBaz3130() {
        $this->assertTrue(strpos(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/lib/XG_Version.php'), '$extraConfigXml'));
    }

    public function testCreateNewInstance() {
        $contents = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/lib/XG_Version.php');
        foreach (glob(NF_APP_BASE . '/instances/*') as $instanceDirectory) {
            $instanceName = basename($instanceDirectory);
            if (in_array($instanceName, array('admin', 'feed', 'forum', 'html', 'main', 'page', 'photo', 'profiles', 'video'))) { continue; }
            $this->assertTrue(strpos($contents, 'self::createNewInstance(\'' . $instanceName . '\'') !== false, 'Missing from XG_Version.php: self::createNewInstance(\'' . $instanceName . '\');');
        }
    }

    /* Migrating Privacy Settings */
    public function testBaz4413() {
        $widget = W_Cache::getWidget('main');
        $saved = array();
        /* Save settings that may get altered during this test */
        foreach (array('appPrivacy','allowInvites','allowRequests','nonregVisibility','moderate','moderateMembers','allowJoin') as $c) {
            $saved[$c] = $widget->config[$c];
        }
        $newSettingsMatrix = array(
            /* Public networks => all can join, no member moderation */
            array('before' => array('appPrivacy' => 'public'),
                  'after'  => array('appPrivacy' => 'public',
                                    'allowInvites' => 'yes',
                                    'allowRequests' => 'no',
                                    'allowJoin' => 'all',
                                    'moderateMembers' => 'no')),
            /* Bazel set to Private, with no sub-options checked
             * =>
             * Bazel should stay Private, with "Invite Only" as the selected option and with Member Moderation turned off
             */
             array('before' => array('appPrivacy' => 'private',
                                     'allowInvites' => 'no',
                                     'allowRequests' => 'no'),
                   'after' => array('appPrivacy' => 'private',
                                    'allowInvites' => 'yes',
                                    'allowRequests' => 'no',
                                    'allowJoin' => 'invited',
                                    'moderateMembers' => 'no')),
            /* Bazel set to Private with the "members can invite other members" option checked
             * =>
             * Bazel should stay Private, with "Invite Only" as the selected option, and with Member Moderation turned off
             */
             array('before' => array('appPrivacy' => 'private',
                                     'allowInvites' => 'yes',
                                     'allowRequests' => 'no'),
                   'after' => array('appPrivacy' => 'private',
                                     'allowInvites' => 'yes',
                                     'allowRequests' => 'no',
                                     'allowJoin' => 'invited',
                                     'moderateMembers' => 'no')),
            /* Bazel set to Private with the "visitors can request an invite" option checked
             * =>
             * Bazel should stay Private, with "Anyone can join" as the selected option, and with Member Moderation turned on
             */
             array('before' => array('appPrivacy' => 'private',
                                     'allowInvites' => 'no',
                                     'allowRequests' => 'yes'),
                   'after' => array('appPrivacy' => 'private',
                                    'allowInvites' => 'yes',
                                    'allowRequests' => 'no',
                                    'allowJoin' => 'all',
                                    'moderateMembers' => 'yes')),
            /* Bazel set to Private with both the "members can invite other members" and the "visitors can request an invite" options checked
             * =>
             * Bazel should stay Private, with "Anyone can join" as the selected option, and with Member Moderation turned on
             */
             array('before' => array('appPrivacy' => 'private',
                                     'allowInvites' => 'yes',
                                     'allowRequests' => 'yes'),
                   'after' => array('appPrivacy' => 'private',
                                    'allowInvites' => 'yes',
                                    'allowRequests' => 'no',
                                    'allowJoin' => 'all',
                                    'moderateMembers' => 'yes')),
            );


        foreach ($newSettingsMatrix as $i => $newSettingsSet) {
            /* Clear the settings that are new for BAZ-4413 */
            $widget->config['allowJoin'] = '';
            $widget->config['moderateMembers'] = '';
            /* Set up the "before" values */
            foreach ($newSettingsSet['before'] as $k => $v) {
                $widget->config[$k] = $v;
            }
            /* Run the migrator */
            XG_VersionStub::migratePrivacySettings();
            /* Verify that the new settings are correct */
            foreach ($newSettingsSet['after'] as $k => $v) {
                $this->assertEqual($v, $widget->config[$k], "set $i: config[$k] is {$widget->config[$k]} instead of $v");
            }
        }
        /* Reset relevant config vars back to what they were before the test */
        foreach ($saved as $k => $v) {
            $widget->config[$k] = $v;
        }
        $widget->saveConfig();
    }

    public function testAddVersionParameterToCssUrls() {
        // BAZ-5687 [Jon Aquino 2008-01-16]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = file_get_contents($file);
            if (strpos($contents, 'rel="stylesheet') === false) { continue; }
            $lines = explode("\n", $contents);
            for ($i = 0; $i < count($lines)-1; $i++) {
                if (strpos($lines[$i], 'css/2.3.css') !== false) { continue; }
                if (strpos($lines[$i], 'ningbar-ie6-capable.css') !== false) { continue; }
                if (strpos($lines[$i], 'ningbar.css') !== false) { continue; }
                if (strpos($lines[$i], 'profileThemeCssUrl') !== false) { continue; }
                if (strpos($lines[$i], 'profileCustomCssUrl') !== false) { continue; }
                $this->assertTrue(strpos($lines[$i], 'rel="stylesheet') === false || strpos($lines[$i], 'addXnVersionParameter') !== false || strpos($lines[$i+1], 'addXnVersionParameter') !== false,
                    $this->prepareLineFileForMsg($lines[$i], $file) . ' ' . 1 . ' ***');
            }
        }
    }

    public function testAddXnVersionParameter() {
        XG_VersionStub::setRevision('Revision: 3040 $');
        XG_VersionStub::setRelease('HeadURL: svn://app.svn.ninginc.com/bazel/branches/1.2.3/lib/XG_Version.php $');
        $this->assertEqual('http://example.org/?xn_version=1.2.3_3040', XG_Version::addXnVersionParameter('http://example.org'));
    }

    public function testNing6797() {
        $n = substr_count(file_get_contents('http://' . $_SERVER['HTTP_HOST']), 'rel="stylesheet"');
        $this->assertTrue(3 >= $n, 'Expected 2 or 3 stylesheet links; found ' . $n);
    }

    public function testNoticeNewCodeVersion1() {
        $mainWidget = new MockW_Widget();
        $mainWidget->config = array('appCodeVersion' => '3.0:3093', 'appSubdomain' => XN_Application::load()->relativeUrl);
        $version = new MockXN_Version();
        $version->expectOnce('currentCodeVersion', array());
        $version->setReturnValue('currentCodeVersion', '2.0:2092');
        $version->expectOnce('codeIsNewerThan', array('3.0:3093'));
        $version->setReturnValue('codeIsNewerThan', false);
        $mainWidget->expectNever('saveConfig');
        $version->expectNever('appWideCodeUpgrade');
        TestVersion::noticeNewCodeVersionProper($mainWidget, $version);
        $this->assertEqual('3.0:3093', $mainWidget->config['appCodeVersion']);
    }

    public function testNoticeNewCodeVersion2() {
        $mainWidget = new MockW_Widget();
        $mainWidget->config = array('appCodeVersion' => '1.0:1091', 'appSubdomain' => XN_Application::load()->relativeUrl);
        $version = new MockXN_Version();
        $version->expectOnce('currentCodeVersion', array());
        $version->setReturnValue('currentCodeVersion', '2.0:2092');
        $version->expectOnce('codeIsNewerThan', array('1.0:1091'));
        $version->setReturnValue('codeIsNewerThan', true);
        $mainWidget->expectOnce('saveConfig', array());
        $version->expectOnce('appWideCodeUpgrade', array('2.0:2092', '1.0:1091'));
        TestVersion::noticeNewCodeVersionProper($mainWidget, $version);
        $this->assertEqual('2.0:2092', $mainWidget->config['appCodeVersion']);
    }

    public function testNoticeNewCodeVersion3() {
        $mainWidget = new MockW_Widget();
        $mainWidget->config = array('appCodeVersion' => '1.0:1091', 'appSubdomain' => 'sjlfshjjfsdf');
        $version = new MockXN_Version();
        $version->expectNever('currentCodeVersion');
        $version->expectNEver('codeIsNewerThan');
        $mainWidget->expectNever('saveConfig');
        $version->expectNever('appWideCodeUpgrade');
        TestVersion::noticeNewCodeVersionProper($mainWidget, $version);
        $this->assertEqual('1.0:1091', $mainWidget->config['appCodeVersion']);
    }

    public function testOlderThan() {
        $this->assertTrue(TestVersion::olderThan('2.3.1', '3.0'));
        $this->assertTrue(TestVersion::olderThan('1.10.2', '2.3'));
        $this->assertTrue(TestVersion::olderThan('1.10.2', '2.3.1'));
        $this->assertTrue(TestVersion::olderThan('1.10.2', '3.0'));
        $this->assertTrue(TestVersion::olderThan('2.0', '3.0'));
        $this->assertTrue(TestVersion::olderThan('2.0:2092', '3.0'));
        $this->assertTrue(TestVersion::olderThan('2.1:2092', '3.0'));
        $this->assertFalse(TestVersion::olderThan('3.0', '3.0'));
        $this->assertFalse(TestVersion::olderThan('3.0:2092', '3.0'));
        $this->assertFalse(TestVersion::olderThan('3.1:2092', '3.0'));
        $this->assertFalse(TestVersion::olderThan('4.0', '3.0'));
        $this->assertFalse(TestVersion::olderThan('4.0:2092', '3.0'));
        $this->assertFalse(TestVersion::olderThan('4.1:2092', '3.0'));
        $this->assertTrue(TestVersion::olderThan('2.0:2091', '3.0:2092'));
        $this->assertTrue(TestVersion::olderThan('2.0:2092', '3.0:2092'));
        $this->assertTrue(TestVersion::olderThan('2.0:2093', '3.0:2092'));
        $this->assertTrue(TestVersion::olderThan('3.0:2091', '3.0:2092'));
        $this->assertFalse(TestVersion::olderThan('3.0:2092', '3.0:2092'));
        $this->assertFalse(TestVersion::olderThan('3.0:2093', '3.0:2092'));
        $this->assertFalse(TestVersion::olderThan('4.0:2091', '3.0:2092'));
        $this->assertFalse(TestVersion::olderThan('4.0:2092', '3.0:2092'));
        $this->assertFalse(TestVersion::olderThan('4.0:2093', '3.0:2092'));
    }

    // Handy for testing but we don't actually want to run it on every run of the unit tests.
    public function testUpgradeToNewGrid() {
        // Run it twice so to check the locking to one run is working.
        //TestVersion::upgradeToNewGrid();
        //TestVersion::upgradeToNewGrid();
    }

    public function testSetShapeAttributes() {
        $mockGroupShape = new MockXN_Shape();
        $mockGroupShape->attributes = array(
                'my.a' => $this->createAttribute('text'),
                'my.b' => $this->createAttribute(null));
        $mockGroupShape->expectNever('save');
        $mockPhotoShape = new MockXN_Shape();
        $mockPhotoShape->attributes = array(
                'my.a' => $this->createAttribute('phrase'),
                'my.b' => $this->createAttribute('phrase'),
                'my.c' => $this->createAttribute('text'),
                'my.d' => $this->createAttribute(null));
        $mockPhotoShape->expectOnce('save');

        $mockShape = new MockXN_Shape();
        $mockShape->setReturnValue('load', $mockGroupShape, array('Group'));
        $mockShape->setReturnValue('load', $mockPhotoShape, array('Photo'));
        $mockShape->setReturnValue('load', null, array('Video'));
        TestVersion::setShapeAttributes(array(
            'Group' => array(
                    'my.a' => array('type' => XN_Attribute::STRING, 'indexing' => 'text'),
                    'my.b' => array('type' => XN_Attribute::STRING)),
            'Photo' => array(
                    'my.a' => array('type' => XN_Attribute::STRING, 'indexing' => 'text'),
                    'my.b' => array('type' => XN_Attribute::STRING),
                    'my.c' => array('type' => XN_Attribute::STRING, 'indexing' => 'text'),
                    'my.d' => array('type' => XN_Attribute::STRING),
                    'my.e' => array('type' => XN_Attribute::STRING, 'indexing' => 'text'),
                    'my.f' => array('type' => XN_Attribute::STRING)),
            'Video' => array(
                    'my.a' => array('type' => XN_Attribute::STRING, 'indexing' => 'text'),
                    'my.b' => array('type' => XN_Attribute::STRING),
                    'my.c' => array('type' => XN_Attribute::STRING, 'indexing' => 'text'),
                    'my.d' => array('type' => XN_Attribute::STRING)),
        ), $mockShape);
    }

    private function createAttribute($indexing) {
        $attribute = new stdClass();
        $attribute->indexing = $indexing;
        return $attribute;
    }

    public function testCreateNewInstance2() {
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/instances/oak';
        if (file_exists($dir)) { XG_FileHelper::deltree($dir); }
        XG_Version::createNewInstance('oak', 'tree', '<diameter>100</diameter>', 0, 1, 1);
        $this->assertEqual($this->normalizeConfigXml('<?xml version="1.0"?>
<widget  root="tree">
    <name>oak</name>
    <config>
        <version type="string" checkState="true">0.1</version>
        <title>Oak</title>
        <displayName>Oak</displayName>
        <description></description>
        <isMozzle type="number">1</isMozzle>
        <isFirstOrderFeature type="number">0</isFirstOrderFeature>
        <isPermanent type="number">1</isPermanent>
        <isEnabledDefault type="number">1</isEnabledDefault>
        <diameter>100</diameter>
    </config>
    <privateConfig>
        <isEnabled/>
    </privateConfig>
</widget>'), $this->normalizeConfigXml(file_get_contents($dir . '/widget-configuration.xml')));
        XG_FileHelper::deltree($dir);
    }

    public function testCreateNewInstance3() {
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/instances/oak';
        if (file_exists($dir)) { XG_FileHelper::deltree($dir); }
        XG_Version::createNewInstance('oak');
        $this->assertEqual($this->normalizeConfigXml('<?xml version="1.0"?>
<widget  root="oak">
    <name>oak</name>
    <config>
        <version type="string" checkState="true">0.1</version>
        <title>Oak</title>
        <displayName>Oak</displayName>
        <description></description>
        <isMozzle type="number">1</isMozzle>
        <isFirstOrderFeature type="number">1</isFirstOrderFeature>
        <isPermanent type="number">0</isPermanent>
        <isEnabledDefault type="number">0</isEnabledDefault>
    </config>
    <privateConfig>
        <isEnabled/>
    </privateConfig>
</widget>'), $this->normalizeConfigXml(file_get_contents($dir . '/widget-configuration.xml')));
        XG_FileHelper::deltree($dir);
    }

    private function normalizeConfigXml($xml) {
        return preg_replace('@\s@', '', preg_replace('@id="\d+"@', '', $xml));
    }

    public function testBaz5687() {
        $this->assertPattern('@/xn/css.*\bv=@', file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/'));
    }

}

class TestVersion extends XG_Version {
    public static function noticeNewCodeVersionProper($mainWidget, $versionClass) {
        parent::noticeNewCodeVersionProper($mainWidget, $versionClass);
    }
    public static function olderThan($a, $b) {
        return parent::olderThan($a, $b);
    }
    public static function compareByReleaseAndRevision($stringOne, $stringTwo) {
        return parent::compareByReleaseAndRevision($stringOne, $stringTwo);
    }
    public static function compareByParts($v1, $v2) {
        return parent::compareByParts($v1, $v2);
    }
    public static function codeIsNewerThan($haveString) {
        return parent::codeIsNewerThan($haveString);
    }
    public static function upgradeToNewGrid() {
        return parent::upgradeToNewGrid();
    }
    public static function setShapeAttributes($data, $shapeClass = 'XN_Shape') {
        return parent::setShapeAttributes($data, $shapeClass);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
