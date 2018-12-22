<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('admin')->includeFileOnce('/lib/helpers/Admin_DomainRedirectionHelper.php');

class Admin_DomainRedirectionHelperTest extends UnitTestCase {

    public function testDefaultIndexFileContents() {
        $this->assertEqual(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/index.php')), Admin_DomainRedirectionHelper::DEFAULT_INDEX_FILE_CONTENTS);
    }

    public function testDisableRedirectionInIndexFile1() {
        $this->assertEqual('<?php
define(\'NF_APP_BASE\',dirname(__FILE__));
require_once NF_APP_BASE . \'/lib/index.php\';',
                TestDomainRedirectionHelper::disableRedirectionInIndexFileProper('<?php
define(\'NF_APP_BASE\',dirname(__FILE__));
require_once NF_APP_BASE . \'/lib/index.php\';'));
    }

    public function testDisableRedirectionInIndexFile2() {
        $this->assertEqual('<?php
if (FALSE /* Manual domain redirection is deprecated. */ && $_SERVER[\'SERVER_NAME\'] != "www.mydomain.com") {
header("HTTP/1.1 301 Moved Permanently");
header("Location: http://www.mydomain.com" . $_SERVER[\'HTTP_X_NING_REQUEST_URI\']);
}
define(\'NF_APP_BASE\',dirname(__FILE__));
require_once NF_APP_BASE . \'/lib/index.php\';',
                TestDomainRedirectionHelper::disableRedirectionInIndexFileProper('<?php
if ($_SERVER[\'SERVER_NAME\'] != "www.mydomain.com") {
header("HTTP/1.1 301 Moved Permanently");
header("Location: http://www.mydomain.com" . $_SERVER[\'HTTP_X_NING_REQUEST_URI\']);
}
define(\'NF_APP_BASE\',dirname(__FILE__));
require_once NF_APP_BASE . \'/lib/index.php\';'));
    }

    public function testDisableRedirectionInIndexFile3() {
        $this->assertEqual('<?php
if (FALSE /* Manual domain redirection is deprecated. */ && $_SERVER[\'SERVER_NAME\'] != \'www.mydomain.com\') {
header("HTTP/1.1 301 Moved Permanently");
header("Location: http://www.mydomain.com" . $_SERVER[\'HTTP_X_NING_REQUEST_URI\']);
exit;
}
define(\'NF_APP_BASE\',dirname(__FILE__));
require_once NF_APP_BASE . \'/lib/index.php\';',
                TestDomainRedirectionHelper::disableRedirectionInIndexFileProper('<?php
if ($_SERVER["SERVER_NAME"] != \'www.mydomain.com\') {
header("HTTP/1.1 301 Moved Permanently");
header("Location: http://www.mydomain.com" . $_SERVER[\'HTTP_X_NING_REQUEST_URI\']);
exit;
}
define(\'NF_APP_BASE\',dirname(__FILE__));
require_once NF_APP_BASE . \'/lib/index.php\';'));
    }

    public function testDisableRedirectionInIndexFile4() {
        $this->assertEqual('<?php
if (FALSE /* Manual domain redirection is deprecated. */ && $_SERVER[\'SERVER_NAME\'] != "www.mydomain.com") {
header("HTTP/1.1 301 Moved Permanently");
header("Location: http://www.mydomain.com" . $_SERVER[\'HTTP_X_NING_REQUEST_URI\']);
}
define(\'NF_APP_BASE\',dirname(__FILE__));
require_once NF_APP_BASE . \'/lib/index.php\';',
                TestDomainRedirectionHelper::disableRedirectionInIndexFileProper('<?php
if (FALSE /* Manual domain redirection is deprecated. */ && $_SERVER[\'SERVER_NAME\'] != "www.mydomain.com") {
header("HTTP/1.1 301 Moved Permanently");
header("Location: http://www.mydomain.com" . $_SERVER[\'HTTP_X_NING_REQUEST_URI\']);
}
define(\'NF_APP_BASE\',dirname(__FILE__));
require_once NF_APP_BASE . \'/lib/index.php\';'));
    }

    public function testDomainRedirectionHeaders1() {
        $this->assertEqual(array('HTTP/1.1 301 Moved Permanently', 'Location: http://foo.com/forum?a=1'), Admin_DomainRedirectionHelper::domainRedirectionHeaders('foo.com', '/forum?a=1', 'GET'));
    }

    public function testDomainRedirectionHeaders2() {
        $this->assertEqual(null, Admin_DomainRedirectionHelper::domainRedirectionHeaders('foo.com', '/forum?a=1', 'POST'));
    }

    public function testDomainRedirectionHeaders3() {
        $this->assertEqual(null, Admin_DomainRedirectionHelper::domainRedirectionHeaders(null, '/forum?a=1', 'GET'));
    }

    public function testDomainRedirectionHeaders4() {
        $this->assertEqual(null, Admin_DomainRedirectionHelper::domainRedirectionHeaders('', '/forum?a=1', 'GET'));
    }

    public function testDomainRedirectionHeaders5() {
        $this->assertEqual(null, Admin_DomainRedirectionHelper::domainRedirectionHeaders('foo.com', '/admin/index/editDomainRedirection', 'GET'));
    }

    public function testDomainRedirectionHeaders6() {
        $this->assertEqual(null, Admin_DomainRedirectionHelper::domainRedirectionHeaders(XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX, '/forum?a=1', 'GET'));
    }

    public function testDomainNames() {
        $unmappedDomain = XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX;
        $domainNames = Admin_DomainRedirectionHelper::domainNames();
        $this->assertEqual($unmappedDomain, $domainNames[$unmappedDomain]);
    }

    public function testWwwAndNonWwwVariants1() {
        $this->assertEqual(array('thisis50.com', 'www.thisis50.com'), TestDomainRedirectionHelper::wwwAndNonWwwVariants('thisis50.com'));
    }

    public function testWwwAndNonWwwVariants2() {
        $this->assertEqual(array('thisis50.com', 'www.thisis50.com'), TestDomainRedirectionHelper::wwwAndNonWwwVariants('www.thisis50.com'));
    }

    public function testWwwAndNonWwwVariants3() {
        $this->assertEqual(array('www.thisis50.com', 'www.www.thisis50.com'), TestDomainRedirectionHelper::wwwAndNonWwwVariants('www.www.thisis50.com'));
    }

    public function testWwwAndNonWwwVariants4() {
        $this->assertEqual(array('wwwalruses.com', 'www.wwwalruses.com'), TestDomainRedirectionHelper::wwwAndNonWwwVariants('wwwalruses.com'));
    }

    public function testWwwAndNonWwwVariants5() {
        $this->assertEqual(array('THISIS50.COM', 'WWW.THISIS50.COM'), TestDomainRedirectionHelper::wwwAndNonWwwVariants('WWW.THISIS50.COM'));
    }

    public function testWwwAndNonWwwVariants6() {
        $this->assertEqual(array('THISIS50.COM', 'www.THISIS50.COM'), TestDomainRedirectionHelper::wwwAndNonWwwVariants('THISIS50.COM'));
    }

    public function testApplicationIdProper1() {
        $this->assertEqual('thisis50', TestDomainRedirectionHelper::applicationIdProper('<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
  <title>Application feed for Thisis50</title>
  <id>http://thisis50.com/xn/atom/1.0/application</id>
  <updated>2008-04-18T18:57:03.665Z</updated>
  <xn:size>1</xn:size>
  <entry>
    <id>thisis50</id>
    <title type="text">Thisis50</title>
    <published>2007-06-03T20:04:44.739Z</published>
    <updated>2008-03-27T05:34:09.895Z</updated>
    <author>
      <name>broadwayallday</name>
    </author>
    <xn:domain>thisis50.ning.com</xn:domain>
    <xn:domain>thisis50.com</xn:domain>
    <xn:active>true</xn:active>
    <link rel="icon" href="http://api.ning.com/icons/appatar/784568?default=784568" />
    <xn:premium-service type="private-source" />
    <xn:premium-service type="run-own-ads" />
    <xn:premium-service type="domain-mapping" />
    <xn:tag>CURTIS</xn:tag>
    <xn:tag>G-UNIT</xn:tag>
    <xn:tag>GET</xn:tag>
    <xn:tag>RICH</xn:tag>
    <xn:tag>50</xn:tag>
    <xn:tag>MASSACRE</xn:tag>
    <xn:tag>TRYIN</xn:tag>
    <xn:tag>AMUSEMENT</xn:tag>
    <xn:tag>PARK</xn:tag>
    <xn:tag>IN</xn:tag>
    <xn:tag>DA</xn:tag>
    <xn:tag>DIE</xn:tag>
    <xn:tag>CLUB</xn:tag>
    <xn:tag>CENT</xn:tag>
    <xn:tag>50Cent</xn:tag>
    <xn:tag>Thisis50</xn:tag>
  </entry>
</feed>'));
    }

    public function testApplicationIdProper2() {
            $this->assertNull(TestDomainRedirectionHelper::applicationIdProper(''));
    }

    public function testApplicationIdProper3() {
            $this->assertNull(TestDomainRedirectionHelper::applicationIdProper(null));
    }

    public function testApplicationIdProper4() {
            $this->assertNull(TestDomainRedirectionHelper::applicationIdProper('lksdjfsl'));
    }

    public function testApplicationIdProper5() {
        $this->assertNull(TestDomainRedirectionHelper::applicationIdProper('<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
  <entry>
  </entry>
</feed>'));
    }

    public function testApplicationIdProper6() {
        $this->assertNull(TestDomainRedirectionHelper::applicationIdProper('<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0"></feed>'));
    }

    public function testApplicationIdProper7() {
        $this->assertNull(TestDomainRedirectionHelper::applicationIdProper('<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<foo></foo>'));
    }

    public function testApplicationIdProper8() {
        $this->assertNull(TestDomainRedirectionHelper::applicationIdProper('<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<foo>'));
    }

    public function testApplicationId() {
        $this->assertEqual('thisis50', Admin_DomainRedirectionHelper::applicationId('thisis50.com'));
        $this->assertEqual('thisis50', Admin_DomainRedirectionHelper::applicationId('www.thisis50.com'));
        $this->assertEqual('thisis50', Admin_DomainRedirectionHelper::applicationId('thisis50.ning.com'));
        $this->assertEqual(XN_Application::load()->relativeUrl, Admin_DomainRedirectionHelper::applicationId(XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX));
        $this->assertNull(Admin_DomainRedirectionHelper::applicationId('yubnub.org'));
        $this->assertNull(Admin_DomainRedirectionHelper::applicationId('sd697ds.234ljh3'));
        $this->assertNull(Admin_DomainRedirectionHelper::applicationId('964932239462.com'));
    }

}

class TestDomainRedirectionHelper extends Admin_DomainRedirectionHelper {
    public static function disableRedirectionInIndexFileProper($indexFileContents) {
        return parent::disableRedirectionInIndexFileProper($indexFileContents);
    }
    public static function wwwAndNonWwwVariants($domainName) {
        return parent::wwwAndNonWwwVariants($domainName);
    }
    public static function applicationIdProper($applicationEndpointResponse) {
        return parent::applicationIdProper($applicationEndpointResponse);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
