<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_MapHelper.php');
Mock::generate('XG_Layout');

class XG_LayoutTest extends UnitTestCase {

    public function testLayoutXmlValid() {
        $this->assertFalse(TestLayout::layoutXmlValid('<?xml version="1.0"?>'));
        $this->assertTrue(TestLayout::layoutXmlValid('<?xml version="1.0"?><foo/>'));
        $this->assertFalse(TestLayout::layoutXmlValid('<?xml version="1.0"?>
'));
        $this->assertTrue(TestLayout::layoutXmlValid('<?xml version="1.0"?>
<foo/>'));
        $this->assertFalse(TestLayout::layoutXmlValid(FALSE));
    }

    public function testRemoveWhitespace() {
        $this->assertEqual('<foo></foo>', TestLayout::removeWhitespaceBetweenTags('<foo>   </foo>'));
    }

    public function testGetIteration() {
        $xml = '<?xml version="1.0"?><layout iteration="428349"/>';
        $doc = DOMDocument::loadXML($xml);
        $this->assertEqual("428349", XG_Layout::getIteration($doc));
    }

    public function testSetInteration() {
        $xml = '<?xml version="1.0"?><layout iteration="7"/>';
        $doc = DOMDocument::loadXML($xml);
        XG_Layout::setIteration($doc, 8);
        $this->assertEqual("<?xml version=\"1.0\"?>\n<layout iteration=\"8\"/>\n", $doc->saveXML());
    }
    
    public function testGetMaxEmbedWidth() {
        $xml =
'<layout nextEmbedInstanceId="100" version="5">
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
        $layout = new DOMDocument();
        $layout->loadXML(TestLayout::removeWhitespaceBetweenTags($xml));
        $xpath = new DOMXPath($layout);
        $columns = $xpath->query('//column');
        $xgLayout = new MockXG_Layout();
        $xgLayout->setReturnValue('getName', 'index');
        $xgLayout->setReturnValue('getLayout', $layout);
        $this->assertEqual(300, TestLayout::getMaxEmbedWidthProper($columns->item(0), $xgLayout, array(300, 100, 200, 90)));
        $this->assertEqual(100, TestLayout::getMaxEmbedWidthProper($columns->item(1), $xgLayout, array(300, 100, 200, 90)));
        $this->assertEqual(200, TestLayout::getMaxEmbedWidthProper($columns->item(2), $xgLayout, array(300, 100, 200, 90)));
        $this->assertEqual(90, TestLayout::getMaxEmbedWidthProper($columns->item(3), $xgLayout, array(300, 100, 200, 90)));
        $this->assertNull(TestLayout::getMaxEmbedWidthProper($layout->documentElement, $xgLayout, array(300, 100, 200, 100)));
    }

    public function testReplaceChildren() {
        $NUM_BAR_NODES = 4;
        $xgLayout = XG_Layout::load('foo');
        $col1 = '/layout/colgroup/column/colgroup/column[1]';
        $nodes = array();
        for ($i = 0; $i < $NUM_BAR_NODES; $i++) {
            $nodeDoc = new DOMDocument();
            $nodeDoc->loadXML('<bar/>');
            $nodes[] = $xgLayout->getLayout()->importNode($nodeDoc->documentElement, true);
        }
        $xgLayout->replaceChildren($col1, $nodes);
        $xpath = new DOMXPath($xgLayout->getLayout());
        $nodeList = $xpath->query($col1 . '/bar');
        $this->assertEqual($NUM_BAR_NODES, $nodeList->length);
        $nodeList = $xpath->query($col1 . '/module');
        $this->assertEqual(0, $nodeList->length);
    }

    public function testGetModulesByType() {
        $xgLayout = XG_Layout::load('baz');
        $rawEmbeds = array('activity-embed2', 'main-embed1createdBy');
        $nodeLists = $xgLayout->getModulesByType($rawEmbeds);
        $this->assertEqual(2, count($nodeLists));
        foreach ($nodeLists as $nodeList) {
            $this->assertTrue($nodeList->item(0));
            $this->assertEqual(1, $nodeList->length);
        }
    }

    public function testGetModulesByType2() {
        $oldProfilesXml = '<?xml version="1.0"?>
            <layout nextEmbedInstanceId="100" version="4">
                <colgroup locked="1">
                    <column width="3">
                        <module widgetName="profiles" action="embed3pagetitle" embedInstanceId="10">
                            <screenName>safeusername</screenName>
                        </module>
                        <colgroup>
                            <column width="1">
                                <module widgetName="profiles" action="embed1smallbadge" embedInstanceId="0">
                                    <screenName>safeusername</screenName>
                                </module>
                                <module widgetName="music" action="embed1" embedInstanceId="14"/>
                                <module widgetName="profiles" action="embed1profileqa" embedInstanceId="3">
                                    <screenName>safeusername</screenName>
                                </module>
                                <module widgetName="groups" action="embed1" embedInstanceId="13">
                                    <groupSet>recent</groupSet>
                                    <itemCount>5</itemCount>
                                </module>
                                <module widgetName="photo" action="embed1" embedInstanceId="1">
                                    <photoSet>for_contributor</photoSet>
                                    <photoNum>4</photoNum>
                                    <photoType>slideshow</photoType>
                                </module>
                                <module widgetName="video" action="embed1" embedInstanceId="2"/>
                                <module widgetName="feed" action="embed1" embedInstanceId="4"/>
                                <module widgetName="forum" action="embed1" embedInstanceId="11">
                                    <topicSet>recent</topicSet>
                                    <itemCount>3</itemCount>
                                </module>
                            </column>
                            <column width="2">
                                <module widgetName="profiles" action="embed3welcome" embedInstanceId="12">
                                    <visible>1</visible>
                                </module>
                                <module widgetName="activity" action="embed2" embedInstanceId="15">
                                    <screenName>safeusername</screenName>
                                    <activityNum>8</activityNum>
                                    <activityItemsCount>1</activityItemsCount>
                                </module>
                                <module widgetName="html" action="embed2" embedInstanceId="5"/>
                                <module widgetName="profiles" action="embed2friends" embedInstanceId="6">
                                    <screenName>safeusername</screenName>
                                </module>
                                <module widgetName="profiles" action="embed2blogposts" embedInstanceId="7">
                                    <screenName>safeusername</screenName>
                                    <displaySet>detail</displaySet>
                                    <postsSet>5</postsSet>
                                </module>
                                <module widgetName="profiles" action="embed2chatterwall" embedInstanceId="8">
                                    <screenName>safeusername</screenName>
                                </module>
                            </column>
                        </colgroup>
                    </column>
                    <column width="1" locked="1">
                        <module widgetName="main" action="sidebar"/>
                    </column>
                </colgroup>
            </layout>';
        $xgLayout = XG_Layout::load('safeusername', 'profiles');
        $layout = new DOMDocument();
        $layout->loadXML(TestLayout::removeWhitespaceBetweenTags($oldProfilesXml));
        $xgLayout->loadLayout($layout);
        $rawEmbeds = array('profiles-embed1smallbadge', 'activity-embed2', 'profiles-embed2chatterwall');
        $nodeLists = $xgLayout->getModulesByType($rawEmbeds);
        $this->assertEqual(3, count($nodeLists));
        foreach ($nodeLists as $nodeList) {
            $this->assertTrue($nodeList->item(0));
            $this->assertEqual(1, $nodeList->length);
        }
    }

    public function testRearrangeLayout() {
        // This function was used as a one-off update for 3.0 release.
        // If the test is failing due to profile page changes it might make more sense to remove it than update it.
        // [Thomas David Baker 2008-10-04]
        $oldProfilesXml = '<?xml version="1.0"?>
<layout nextEmbedInstanceId="100" version="4">
    <colgroup locked="1">
        <column width="3">
            <module widgetName="profiles" action="embed3pagetitle" embedInstanceId="10">
                <screenName>infidilibum</screenName>
            </module>
            <colgroup>
                <column width="1">
                    <module widgetName="profiles" action="embed1smallbadge" embedInstanceId="0">
                        <screenName>infidilibum</screenName>
                    </module>
                    <module widgetName="music" action="embed1" embedInstanceId="14"/>
                    <module widgetName="profiles" action="embed1profileqa" embedInstanceId="3">
                        <screenName>infidilibum</screenName>
                    </module>
                    <module widgetName="groups" action="embed1" embedInstanceId="13">
                        <groupSet>recent</groupSet>
                        <itemCount>5</itemCount>
                    </module>
                    <module widgetName="photo" action="embed1" embedInstanceId="1">
                        <photoSet>for_contributor</photoSet>
                        <photoNum>4</photoNum>
                        <photoType>slideshow</photoType>
                    </module>
                    <module widgetName="video" action="embed1" embedInstanceId="2"/>
                    <module widgetName="feed" action="embed1" embedInstanceId="4"/>
                    <module widgetName="forum" action="embed1" embedInstanceId="11">
                        <topicSet>recent</topicSet>
                        <itemCount>3</itemCount>
                    </module>
                </column>
                <column width="2">
                    <module widgetName="profiles" action="embed3welcome" embedInstanceId="12">
                        <visible>1</visible>
                    </module>
                    <module widgetName="activity" action="embed2" embedInstanceId="15">
                        <screenName>infidilibum</screenName>
                        <activityNum>8</activityNum>
                        <activityItemsCount>1</activityItemsCount>
                    </module>
                    <module widgetName="html" action="embed2" embedInstanceId="5"/>
                    <module widgetName="profiles" action="embed2friends" embedInstanceId="6">
                        <screenName>infidilibum</screenName>
                    </module>
                    <module widgetName="profiles" action="embed2blogposts" embedInstanceId="7">
                        <screenName>infidilibum</screenName>
                        <displaySet>detail</displaySet>
                        <postsSet>5</postsSet>
                    </module>
                    <module widgetName="profiles" action="embed2chatterwall" embedInstanceId="8">
                        <screenName>infidilibum</screenName>
                    </module>
                </column>
            </colgroup>
        </column>
        <column width="1" locked="1">
            <module widgetName="main" action="sidebar"/>
        </column>
    </colgroup>
</layout>';
        $layout = new DOMDocument();
        $layout->loadXML(TestLayout::removeWhitespaceBetweenTags($oldProfilesXml));
        $xgLayout = XG_Layout::load('infidilibum', 'profiles');
        $xgLayout->loadLayout($layout);
        $xpath = new DOMXPath($xgLayout->getLayout());
        $nodeList = $xpath->query("//module");
        $this->assertEqual(16, $nodeList->length);
        $nodeList = $xpath->query('//module[@action="embed1profileqa"]');
        $this->assertEqual(1, $nodeList->length);
        $nodeList = $xpath->query('//module[@action="embed1smallbadge"]');
        $this->assertEqual(1, $nodeList->length);
        $xgLayout->rearrangeLayout($xpath);
        $xpath = new DOMXPath($xgLayout->getLayout());
        $nodeList = $xpath->query('/layout/colgroup/column[1]/colgroup/column[1]/module');
        $this->assertEqual(6, $nodeList->length);
        $nodeList = $xpath->query('/layout/colgroup/column[1]/colgroup/column[2]/module');
        $this->assertEqual(8, $nodeList->length);
        $nodeList = $xpath->query("//module");
        $this->assertEqual(16, $nodeList->length);
        $nodeList = $xpath->query('//module[@action="embed1smallbadge"]');
        $this->assertEqual(1, $nodeList->length);
        $nodeList = $xpath->query('//module[@action="embed1profileqa"]');
        $this->assertEqual(0, $nodeList->length);
        $nodeList = $xpath->query('//module[@action="embed2profileqa"]');
        $this->assertEqual(1, $nodeList->length);
    }

    public function testRearrangeUnexpectedLayout() {
        $profileXml = '<totallyunexpectedlayout/>';
        $layout = new DOMDocument();
        $layout->loadXML(TestLayout::removeWhitespaceBetweenTags($profileXml));
        $xgLayout = XG_Layout::load('totallyunexpectedlayoutman', 'profiles');
        $xgLayout->loadLayout($layout);
        $xpath = new DOMXPath($xgLayout->getLayout());
        $nodeList = $xpath->query("//totallyunexpectedlayout");
        $this->assertEqual(1, $nodeList->length);
        $nodeList = $xpath->query('//module');
        $this->assertEqual(0, $nodeList->length);
        $xgLayout->rearrangeLayout($xpath);
        $xpath = new DOMXPath($xgLayout->getLayout());
        $nodeList = $xpath->query('//totallyunexpectedlayout');
        $this->assertEqual(1, $nodeList->length);
        $nodeList = $xpath->query("//module");
        $this->assertEqual(0, $nodeList->length);
    }

    public function testNormalizeWidth() {
        for ($width = 1; $width <= 2; $width++) {
            for ($embedSize = 1; $embedSize <= 3; $embedSize++) {
                $xml = '<module widgetName="boris" action="embed' . $embedSize . 'spider"/>';
                $doc = DOMDocument::loadXML($xml);
                $xpath = new DOMXPath($doc);
                $nodeList = $xpath->query("//module");
                $this->assertEqual(1, $nodeList->length);
                $xgLayout = XG_Layout::load('ignore', 'profiles');
                $elem = $xgLayout->normalizeWidth($nodeList->item(0), $width);
                $expected = "embed" . ($embedSize === 3 ? 3 : $width) . "spider";
                $this->assertEqual($expected, $elem->getAttribute('action'));
            }
        }
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

abstract class TestLayout extends XG_Layout {
    public static function removeWhitespaceBetweenTags($xml) {
        return parent::removeWhitespaceBetweenTags($xml);
    }
    public static function getMaxEmbedWidthProper(DOMElement $element, $layout, $maxEmbedWidthsForColumns) {
        return parent::getMaxEmbedWidthProper($element, $layout, $maxEmbedWidthsForColumns);
    }
    public function layoutXmlValid($xml) {
        return parent::layoutXmlValid($xml);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
