<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_LangHelper.php');

class XG_LangHelperTest extends UnitTestCase {

    public function testAddToDelimitedString() {
        $this->assertEqual('foo', XG_LangHelper::addToDelimitedString('foo', null));
        $this->assertEqual('foo', XG_LangHelper::addToDelimitedString('foo', ''));
        $this->assertEqual('foo bar', XG_LangHelper::addToDelimitedString('bar', 'foo'));
        $this->assertEqual('foo', XG_LangHelper::removeFromDelimitedString('bar', 'foo bar'));
        $this->assertEqual('', XG_LangHelper::removeFromDelimitedString('foo', 'foo'));
        $this->assertNull(XG_LangHelper::removeFromDelimitedString('foo', null));
        $this->assertEqual('foo bar', XG_LangHelper::removeFromDelimitedString('hello', 'foo bar'));
        $this->assertEqual('foo foo', XG_LangHelper::addToDelimitedString('foo', 'foo'));
        $this->assertEqual('foo', XG_LangHelper::addToDelimitedString('foo', 'foo', true));
        $this->assertEqual('foo bar', XG_LangHelper::addToDelimitedString('bar', 'foo', true));
        $this->assertEqual('foo food', XG_LangHelper::addToDelimitedString('food', 'foo', true));
        $this->assertEqual('food foo', XG_LangHelper::addToDelimitedString('foo', 'food', true));
    }

    public function testReplaceOnce() {
        $this->assertEqual('the quick brown fox
jumps over the lazy dog', XG_LangHelper::replaceOnce('foo', 'bar', 'the quick brown fox
jumps over the lazy dog'));
        $this->assertEqual('the bar brown fox
jumps over the lazy dog', XG_LangHelper::replaceOnce('quick', 'bar', 'the quick brown fox
jumps over the lazy dog'));
        $this->assertEqual('bar quick brown fox
jumps over the lazy dog', XG_LangHelper::replaceOnce('the', 'bar', 'the quick brown fox
jumps over the lazy dog'));
        $this->assertEqual('the quick brown bar over the lazy dog', XG_LangHelper::replaceOnce('fox
jumps', 'bar', 'the quick brown fox
jumps over the lazy dog'));
    }

    public function testArrayFlatten() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode(array()), $json->encode(XG_LangHelper::arrayFlatten(array())));
        $this->assertEqual($json->encode(array('A')), $json->encode(XG_LangHelper::arrayFlatten(array('A'))));
        $this->assertEqual($json->encode(array('A', 'B')), $json->encode(XG_LangHelper::arrayFlatten(array('A', array('B')))));
        $this->assertEqual($json->encode(array('A', 'B', 'C', 'D', 'E', 'F')), $json->encode(XG_LangHelper::arrayFlatten(array('A', 'B', array('C', 'D', array('E', 'F'))))));
    }

    public function testStartsWith() {
        $this->assertTrue(XG_LangHelper::startsWith("hello", "h"));
        $this->assertTrue(XG_LangHelper::startsWith("hello", "hell"));
        $this->assertTrue(XG_LangHelper::startsWith("hello", "hello"));
        $this->assertFalse(XG_LangHelper::startsWith("hello", "hello "));
    }

    public function testEndsWith() {
        $this->assertTrue(XG_LangHelper::endsWith('hello', 'llo'));
        $this->assertFalse(XG_LangHelper::endsWith('hello', 'o '));
        $this->assertTrue(XG_LangHelper::endsWith(0, ''));
        $this->assertFalse(XG_LangHelper::endsWith('', '0'));
        $this->assertTrue(XG_LangHelper::endsWith('0', ''));
        $this->assertFalse(XG_LangHelper::endsWith('1234567890', 0));
        $this->assertTrue(XG_LangHelper::endsWith('1234567890', '0'));
    }

    public function testUrlFriendlyStr() {
        $s = "网页    图片    资讯    地图    更多 »网页    图片    资讯    地图    更多";
        $this->assertEqual('wngytpinzxndetgngduwngytpinzxndetgngdu', XG_LangHelper::urlFriendlyStr($s));
        $s = "网页    图片    资讯    地图    更多 »网页    图片    资讯    地图    更多 hello";
        $this->assertEqual('wngytpinzxndetgngduwngytpinzxndetgngduhello', XG_LangHelper::urlFriendlyStr($s));
        $s = "Actualités";
        $this->assertEqual('Actualites', XG_LangHelper::urlFriendlyStr($s));
        $s = "Programmes de publicité";
        $this->assertEqual('Programmesdepublicite', XG_LangHelper::urlFriendlyStr($s));
        $s = "ビジネス ソリューション Google.com in English";
        $this->assertEqual('GooglecominEnglish', XG_LangHelper::urlFriendlyStr($s));
        $s = 'Παγκόσμιος ιστός';
        $this->assertEqual('Pagkhosmiosisthos', XG_LangHelper::urlFriendlyStr($s));
        $s = "¡Nuevo! páginas en español";
        $this->assertEqual('Nuevopaginasenespanol', XG_LangHelper::urlFriendlyStr($s));
        $s = 'What?  What are you talking about?!';
        $this->assertEqual('WhatWhatareyoutalkingabout', XG_LangHelper::urlFriendlyStr($s));
        $s = 'John T. Smith';
        $this->assertEqual('JohnTSmith', XG_LangHelper::urlFriendlyStr($s));
        $s = 'AbrahamLincoln';
        $this->assertEqual('AbrahamLincoln', XG_LangHelper::urlFriendlyStr($s));
        $s = "Eric and Parrish making $$$";
        $this->assertEqual('EricandParrishmaking', XG_LangHelper::urlFriendlyStr($s));
        $s = "In for a penny, in for a ₤£";
        $this->assertEqual('InforapennyinforaLL', XG_LangHelper::urlFriendlyStr($s));
        $s = "When I was an alien, cultures weren't opinions.";
        $this->assertEqual('When_I_was_an_alien_cultures_werent_opinions', XG_LangHelper::urlFriendlyStr($s, true));
        $s = 'для вашей организации';
        $this->assertEqual('dlya_vashej_organizacii', XG_LangHelper::urlFriendlyStr($s, true));
        $s = 'what-ever';
        $this->assertEqual('whatever', XG_LangHelper::urlFriendlyStr($s, true));
    }

    public function testAasort() {
        $data = array(
            array('A' => 'lemon',  'B' => 'chicken'),
            array('A' => 'orange', 'B' => 'juice'),
            array('A' => 'orange', 'B' => 'duck'),
            array('A' => 'lemon',  'B' => 'sherbert')
        );
        XG_LangHelper::aasort($data, 'A_a,B_a');
        //TODO: We can use assertIdentical here instead of assertTrue($x === $y) [Thomas David Baker 2008-08-02]
        $this->assertTrue($data[0]['A'] === 'lemon');
        $this->assertTrue($data[0]['B'] === 'chicken');
        $this->assertTrue($data[3]['A'] === 'orange');
        $this->assertTrue($data[3]['B'] === 'juice');
        XG_LangHelper::aasort($data, 'B_d,A_a');
        $this->assertTrue($data[1]['A'] === 'orange');
        $this->assertTrue($data[1]['B'] === 'juice');
        $this->assertTrue($data[2]['A'] === 'orange');
        $this->assertTrue($data[2]['B'] === 'duck');
    }

    public function testIndexes() {
        $this->assertEqual(array(0, 100), XG_LangHelper::indexes(0, 100, 150));
        $this->assertEqual(array(0, 100), XG_LangHelper::indexes(0, 100, 100));
        $this->assertEqual(array(0, 50, 100), XG_LangHelper::indexes(0, 100, 50));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
