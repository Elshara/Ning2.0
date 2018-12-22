<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_AppearanceHelper.php');

class Index_AppearanceHelperTest extends UnitTestCase {

    protected static $baseSubstitutions =  array('textFont' => '"Helvetica Neue", Arial, Helvetica, sans-serif', 'moduleBodyTextColor' => 'DDDDDD',
        'siteLinkColor' => 'FF4A12', 'siteBgColor' => '8BCFF2', 'siteBgImage' => null, 'siteBgImage_repeat' => 'repeat', 'pageHeaderTextColor' => 'FFFFFF',
        'headBgColor' => '3E4F4F', 'headBgImage' => null, 'headBgImage_repeat' => 'no-repeat', 'pageBgColor' => '222D2D', 'pageBgImage' => null,
        'pageBgImage_repeat' => 'repeat', 'ningbarColor' => 'FFFFFF', 'ningLogoDisplay' => 'none!important',  'customCss' => null);

    public function setUp() {
        $this->httpHost = $_SERVER['HTTP_HOST'];
    }

    public function tearDown() {
        $_SERVER['HTTP_HOST'] = $this->httpHost;
    }

    public function testCssify() {
        $this->assertEqual('#ffffff;', Index_AppearanceHelper::cssify('moduleBgColor', 'ffffff'));
        $this->assertEqual('#FFFFFF;', Index_AppearanceHelper::cssify('moduleBgColor', 'FFFFFF'));
        $this->assertEqual('#fa0;', Index_AppearanceHelper::cssify('moduleBgColor', 'fa0'));
        $this->assertEqual('url(http://ning.com/foo.png);', Index_AppearanceHelper::cssify('moduleBgImage', 'http://ning.com/foo.png'));
        $this->assertEqual('no-repeat;', Index_AppearanceHelper::cssify('moduleBgImage_repeat', null));
        $this->assertEqual('no-repeat;', Index_AppearanceHelper::cssify('moduleBgImage_repeat', 'no-repeat'));
        $this->assertEqual('repeat;', Index_AppearanceHelper::cssify('moduleBgImage_repeat', 'repeat'));
    }

    public function testMigrationRequired() {
        $this->assertTrue(Index_AppearanceHelper::migrationRequired(array('headTabColor' => 'CCCCCC')));
        $this->assertFalse(Index_AppearanceHelper::migrationRequired(array('pageBgColor' => 'CCCCCC')));
        $this->assertFalse(Index_AppearanceHelper::migrationRequired(array()));
        $this->assertFalse(Index_AppearanceHelper::migrationRequired(self::$baseSubstitutions));
        $this->assertTrue(Index_AppearanceHelper::migrationRequired(array_merge(self::$baseSubstitutions, array('headTabColor' => 'CCCCCC'))));
    }

    public function testApplyCdnUrls() {
        $applicationId = XN_Application::load()->relativeUrl;
        $domainSuffix = XN_AtomHelper::$DOMAIN_SUFFIX;
        $_SERVER['HTTP_HOST'] = 'abcde.com';
        $version = urlencode(XG_Version::currentCodeVersion());
        $this->assertEqual("background: url(http://static{$domainSuffix}/{$applicationId}/hello.png?v=$version)",
            Index_AppearanceHelper::applyCdnUrls("background: url(/xn_resources/hello.png)"));
        $this->assertEqual("background: url(http://static{$domainSuffix}/{$applicationId}/foo/bar/baz/hello.png?v=$version)",
            Index_AppearanceHelper::applyCdnUrls("background: url(/xn_resources/foo/bar/baz/hello.png)"));
        $this->assertEqual('background: url(hello.png)', Index_AppearanceHelper::applyCdnUrls("background: url(hello.png)"));
        $this->assertEqual("background: url(http://google.com)",
            Index_AppearanceHelper::applyCdnUrls("background: url(http://google.com)"));
        $this->assertEqual("background: url(http://google.com/xn_resources/hello.png)",
            Index_AppearanceHelper::applyCdnUrls("background: url(http://google.com/xn_resources/hello.png)"));
        $this->assertEqual("background: url(http://static{$domainSuffix}/{$applicationId}/hello.png?v=$version)",
            Index_AppearanceHelper::applyCdnUrls("background: url(http://{$applicationId}{$domainSuffix}/xn_resources/hello.png)"));
        $this->assertEqual("background: url(http://static{$domainSuffix}/{$applicationId}/hello.png?v=$version)",
            Index_AppearanceHelper::applyCdnUrls("background: url(http://abcde.com/xn_resources/hello.png)"));
    }

    public function testGetMigrationCss() {
        $substitutions = array_merge(self::$baseSubstitutions, array('pageTitleColor' => 'CCCCCC', 'headTabColor' => 'FF0000'));
        $expected = '/* Page Title */
#xg_body h1,
#xg_body ul.navigation a,
#xg_navigation ul li.this a,
#xg_navigation ul li a:hover {
    color:/* %pageTitleColor% */ #CCCCCC;
}
/* Tab Color */
#xg_navigation ul li a {
    background-color:/* %headTabColor% */ #FF0000;
}
/* Module Body: Background & Text */
.xg_module_body,
.xg_module_body legend,
.xg_module_body legend.toggle a,
.xg_module_foot,
ul.page_tabs li.this {
    background-color:/* %moduleBodyBgColor% */ #720000;
    background-image:/* %moduleBgImage% */ none;
    background-repeat:/* %moduleBgImage_repeat% */ no-repeat;
}
/* Module Body: Headings */
.xg_module_body h3,
.xg_module_body caption {
    color:/* %moduleHeadingColor% */ #EEE7AA;
}';
        $actual = Index_AppearanceHelper::getMigrationCss($substitutions);
        $this->assertEqual(preg_replace('@\s+@', ' ', $expected), preg_replace('@\s+@', ' ', $actual));
    }

    public function testOutputAdInitScriptProper() {
        ob_start();
        TestAppearanceHelper::outputAdInitScriptProper(array('border' => '111111', 'bg' => '222222', 'link' => '333333', 'text' => '444444', 'url' => '555555'));
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertEqual('<script type="text/javascript">
            google_ad_client = "pub-5349214509828986";
            google_ad_width = 160;
            google_ad_height = 600;
            google_ad_format = "160x600_as";
            google_ad_type = "text";
            google_ad_channel = "";
            google_color_border = "222222";
            google_color_bg = "222222";
            google_color_link = "333333";
            google_color_text = "444444";
            google_color_url = "555555";
        </script>', $output);
    }

    public function testAdColorsProper() {
        $this->assertEqual(array('bg' => '111111', 'link' => '333333', 'text' => '444444', 'url' => '333333'), TestAppearanceHelper::adColorsProper(array('moduleBodyBgColor' => '111111', 'pageBgColor' => '222222', 'siteLinkColor' => '333333', 'moduleBodyTextColor' => '444444')));
        $this->assertEqual(array('bg' => '222222', 'link' => '333333', 'text' => '444444', 'url' => '333333'), TestAppearanceHelper::adColorsProper(array('pageBgColor' => '222222', 'siteLinkColor' => '333333', 'moduleBodyTextColor' => '444444')));
    }
}

class TestAppearanceHelper extends Index_AppearanceHelper {
    public static function outputAdInitScriptProper($colors) {
        return parent::outputAdInitScriptProper($colors);
    }
    public static function adColorsProper($colors) {
        return parent::adColorsProper($colors);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
