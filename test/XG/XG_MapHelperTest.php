<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_MapHelper.php');

class XG_MapHelperTest extends UnitTestCase {

    public function testGenerateGoogleMapsApiKeyIfNecessaryProper() {
        $_SERVER['HTTP_HOST'] = 'www.example.com';
        $minute = 60;

        $privateConfig = array('googleMapsApiKeys' => '123456789');
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, 'www.example.comTESTKEY'));
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('www.example.com' => 'www.example.comTESTKEY')))), serialize($privateConfig));

        $privateConfig = array('googleMapsApiKeys' => '');
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, 'www.example.comTESTKEY'));
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('www.example.com' => 'www.example.comTESTKEY')))), serialize($privateConfig));

        $privateConfig = array();
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, 'www.example.comTESTKEY'));
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('www.example.com' => 'www.example.comTESTKEY')))), serialize($privateConfig));

        $privateConfig = array('googleMapsApiKeys' => serialize(array('www.example.com' => '123456789')));
        $this->assertFalse(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, 'www.example.comTESTKEY'));
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('www.example.com' => '123456789')))), serialize($privateConfig));

        $privateConfig = array('googleMapsApiKeys' => serialize(array('www.example.com' => '-1')));
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, 'www.example.comTESTKEY'));
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('www.example.com' => 'www.example.comTESTKEY')))), serialize($privateConfig));

        $privateConfig = array('googleMapsApiKeys' => serialize(array('www.example.com' => '<html>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quam turpis, imperdiet a, egestas sed, euismod eget, lectus. Curabitur id libero id ipsum tempor lacinia. Aliquam nec turpis non elit varius consequat. Maecenas iaculis iaculis dolor. Nunc enim risus, semper vitae, ullamcorper nec, ornare at, augue. Nulla laoreet, massa congue tristique pellentesque, est lorem condimentum nulla, sed ultrices diam orci eu magna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam consequat. Aenean ultrices vehicula mauris. Quisque vitae diam.</html>')));
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, 'www.example.comTESTKEY'));
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('www.example.com' => 'www.example.comTESTKEY')))), serialize($privateConfig));

        $privateConfig = array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789')));
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, 'www.example.comTESTKEY'));
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789', 'www.example.com' => 'www.example.comTESTKEY')))), serialize($privateConfig));

        $privateConfig = array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789')));
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, '-1'));
        $time = time();
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789', 'www.example.com' => "tried $time")))), serialize($privateConfig));

        $privateConfig = array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789')));
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, "123456789\n123456789"));
        $time = time();
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789', 'www.example.com' => "tried $time")))), serialize($privateConfig));

        $privateConfig = array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789')));
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, "123456789<br>123456789"));
        $time = time();
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789', 'www.example.com' => "tried $time")))), serialize($privateConfig));

        $privateConfig = array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789', 'www.example.com' => 'tried ' . (time() - $minute*55))));
        $this->assertFalse(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, 'www.example.comTESTKEY'));

        $privateConfig = array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789', 'www.example.com' => 'tried ' . (time() - $minute*65))));
        $this->assertTrue(XG_MapHelper::generateGoogleMapsApiKeyIfNecessaryProper($privateConfig, 'www.example.comTESTKEY'));
        $this->assertEqual(serialize(array('googleMapsApiKeys' => serialize(array('foo.ning.com' => '123456789', 'www.example.com' => 'www.example.comTESTKEY')))), serialize($privateConfig));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
