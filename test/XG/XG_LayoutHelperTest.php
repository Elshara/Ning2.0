<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
Mock::generate('XG_Layout');

class XG_LayoutHelperTest extends UnitTestCase {

    public function testAddPageEditingAttributes() {
        return; //TODO: these tests are out of sync with the code.  Need bringing up to date. [Thomas David Baker 2008-07-17]
        $html = "";
        $this->assertEqual($html, XG_LayoutHelper::addPageEditingAttributes($html, "foo", "bar", "baz"));
        $html = '<div class="xg_module"></div>';
        $expected = '<div class="xg_module"></div>';
        $this->assertEqual($expected, XG_LayoutHelper::addPageEditingAttributes($html, "foo", "bar", "baz"));
        $html = '<div class="foo xg_module bar"><baz/></div>';
        $expected = '<div class="xg_module"></div>';
        $this->assertEqual($expected, XG_LayoutHelper::addPageEditingAttributes($html, "foo", "bar", "baz"));
        $html = '<div class="foo xg_module_head bar"><baz/></div>';
        $this->assertEqual($html, XG_LayoutHelper::addPageEditingAttributes($html, "foo", "bar", "baz"));
    }

    public function testModifyModuleEmbedClasses() {
        $testCases = array(
                           array('<div>', '+foo', null, '<div>'),
                           array('a<div>b', '+foo', null, 'a<div>b'),
                           array('<div class="bar">', '+foo,-bar', null, '<div class="bar">'),
                           array('<div class="bar">', array('+foo', '-bar'), null, '<div class="bar">'),
                           array('<div class="bar">', array('+foo', '-bar'), 'a', '<div class="bar">'),
                           array('<div class="bar xg_module">', '-bar', null, '<div class="xg_module">'),
                           array('<div class="bar xg_module">', '-foo', null, '<div class="bar xg_module">'),
                           array('<div class="bar xg_module">', '-foo', 'a', '<div class="bar xg_module">a'),
                           array('<div class="bar xg_module">', '-bar', null, '<div class="xg_module">'),
                           array('<div class="bar multiline' . "\n" . ' xg_module">', '-multiline,+foo', null, '<div class="bar xg_module foo">'),
                           array('<div class="xg_module foo_module">', '+bar,-foo', 'a', '<div class="xg_module foo_module bar">a'),
                           array('<div class="xg_module foo_module">', '+bar,-foo_module', 'a', '<div class="xg_module bar">a')
                          );
        foreach ($testCases as $testCase) {
            $this->assertEqual($testCase[3], XG_LayoutHelper::modifyModuleEmbedClasses($testCase[1], $testCase[0], $testCase[2]));
        }
    }
    
    public function testWidgetNamesInLayout() {
        $domDocument = new DOMDocument();
        $domDocument->loadXML(TestLayout::removeWhitespaceBetweenTags(self::PROFILE_PAGE_XML));
        $widgetNames = XG_LayoutHelper::widgetNamesInLayout($domDocument);
        sort($widgetNames);
        $this->assertEqual(array('activity', 'feed', 'forum', 'groups', 'html', 'main', 'music', 'photo', 'profiles', 'video'), $widgetNames);
    }

    const PROFILE_PAGE_XML = '<layout nextEmbedInstanceId="100" version="5">
        <colgroup locked="1">
            <column width="3">
              <module widgetName="profiles" action="embed3pagetitle" embedInstanceId="10"><screenName>$safeName</screenName></module>
              <colgroup>
                <column width="1">
                  <module widgetName="profiles" action="embed1smallbadge" embedInstanceId="0"><screenName>$safeName</screenName></module>
                  <module widgetName="profiles" action="embed1friends" embedInstanceId="6"><screenName>$safeName</screenName></module>
                  <module widgetName="music" action="embed1" embedInstanceId="14" />
                  <module widgetName="groups" action="embed1" embedInstanceId="13" />
                  <module widgetName="forum" action="embed1" embedInstanceId="11" />
                  <module widgetName="feed" action="embed1" embedInstanceId="4"/>
                </column>
                <column width="2">
                  <module widgetName="profiles" action="embed3welcome" embedInstanceId="12"><visible>1</visible></module>
                  <module widgetName="activity" action="embed2" embedInstanceId="15"><screenName>$safeName</screenName></module>
                  <module widgetName="profiles" action="embed2profileqa" embedInstanceId="3"><screenName>$safeName</screenName></module>
                  <module widgetName="html" action="embed2" embedInstanceId="5" />
                  <module widgetName="photo" action="embed2" embedInstanceId="1" />
                  <module widgetName="video" action="embed2" embedInstanceId="2" />
                  <module widgetName="profiles" action="embed2blogposts" embedInstanceId="7"><screenName>$safeName</screenName></module>
                  <module widgetName="profiles" action="embed2chatterwall" embedInstanceId="8"><screenName>$safeName</screenName></module>
                </column>
              </colgroup>
            </column>
            <column width="1" locked="1">
              <module widgetName="main" action="sidebar"/>
            </column>
        </colgroup>
    </layout>';

}

abstract class TestLayout extends XG_Layout {
    public static function removeWhitespaceBetweenTags($xml) {
        return parent::removeWhitespaceBetweenTags($xml);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
