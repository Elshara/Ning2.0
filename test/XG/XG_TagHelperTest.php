<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_TagHelper.php');

class XG_TagHelperTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testGetTagNamesForObject() {
        $food = XN_Content::create('Food');
        $food->save();
        XN_Tag::addTags($food, 'tasty');
        $this->assertEqual('tasty', implode(',', XG_TagHelper::getTagNamesForObject($food)));
        $this->assertEqual('tasty', implode(',', XG_TagHelper::getTagNamesForObject($food->id)));
    }

    public function testGetTagNamesForObject2() {
        $food = XN_Content::create('Food');
        $this->assertIdentical(array(), XG_TagHelper::getTagNamesForObject($food));
    }

    public function testParseTagString() {
        $x = XN_Tag::parseTagString(null);
        $this->assertTrue(is_array($x));
        $this->assertEqual(0, count($x));
    }

    public function testGetTagStringForObjectAndUser() {
        $food = XN_Content::create('Food');
        $food->save();
        XN_Tag::addTags($food, 'blue, red');
        $tagNames = XG_TagHelper::getTagStringForObjectAndUser($food->id, XN_Profile::current()->screenName);
        $this->assertTrue('blue, red' == $tagNames || 'red, blue' == $tagNames, 'Expected blue, red; found ' . $tagNames);
        $tagNames = XG_TagHelper::getTagStringForObjectAndUser($food, XN_Profile::current()->screenName);
        $this->assertTrue('blue, red' == $tagNames || 'red, blue' == $tagNames, 'Expected blue, red; found ' . $tagNames);
        $tagNames = XG_TagHelper::getTagStringForObjectAndUser($food->id, 'AAAAA');
        $this->assertTrue('' == $tagNames || '' == $tagNames, 'Expected empty string; found ' . $tagNames);
        $tagNames = XG_TagHelper::getTagStringForObjectAndUser($food, 'AAAAA');
        $this->assertTrue('' == $tagNames || '' == $tagNames, 'Expected empty string; found ' . $tagNames);
    }

    public function testGetTagStringForObjectAndUser2() {
        $food = XN_Content::create('Food');
        $this->assertIdentical('', XG_TagHelper::getTagStringForObjectAndUser($food, XN_Profile::current()->screenName));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
