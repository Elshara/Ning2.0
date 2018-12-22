<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_LayoutEditHelper.php');

class XG_LayoutEditHelperTest extends UnitTestCase {

    //TODO add a few opensocial embeds to some of the JSON in one or more of these tests.
    // They do not currently make an appearance because they are optional on profile pages
    // [Thomas David Baker 2008-07-30]

    public function testInsertEmbed() {
        $layoutArr = array('iteration' => 12, 'col1' => array(array('xg_embed_key' => "video"), array('xg_embed_key' => "groups")));
        $embed = array('xg_embed_key' => 'music', 'xg_embed_instance_id' => 10);
        $location = array('col1', 1);
        $newLayoutArr = TestLayoutEditHelper::insertEmbed($layoutArr, $embed, $location);
        $this->assertEqual("video", $newLayoutArr['col1'][0]['xg_embed_key']);
        $this->assertEqual('music', $newLayoutArr['col1'][1]['xg_embed_key']);
        $this->assertEqual(10, $newLayoutArr['col1'][1]['xg_embed_instance_id']);
        $this->assertEqual("groups", $newLayoutArr['col1'][2]['xg_embed_key']);
    }

    public function testInsertEmbed2() {
        $layoutArr = array('iteration' => 49,
            'col1' => array(
                array('xg_embed_instance_id' => 0, 'xg_embed_key' => '_badge'),
                array('xg_embed_instance_id' => 6, 'xg_embed_key' => '_friends'),
                array('xg_embed_instance_id' => 14, 'xg_embed_key' => 'music'),
                array('xg_embed_instance_id' => 11, 'xg_embed_key' => 'forum'),
                array('xg_embed_instance_id' => 20, 'xg_embed_key' => 'events'),
                array('xg_embed_instance_id' => 5, 'xg_embed_key' => 'html'),
                array('xg_embed_instance_id' => 4, 'xg_embed_key' => 'feed'),
            ),
            'col2' => array(
                array('xg_embed_instance_id' => 12, 'xg_embed_key' => '_welcome'),
                array('xg_embed_instance_id' => 15, 'xg_embed_key' => 'activity'),
                array('xg_embed_instance_id' => 3, 'xg_embed_key' => '_profileqa'),
                array('xg_embed_instance_id' => 1, 'xg_embed_key' => 'photo'),
                array('xg_embed_instance_id' => 8, 'xg_embed_key' => '_chatterwall')
            )
        );
        $embed = array('xg_embed_key' => 'video', 'xg_embed_instance_id' => 2);
        $location = array('col2', 6);
        $newLayoutArr = TestLayoutEditHelper::insertEmbed($layoutArr, $embed, $location);
        $this->assertEqual("video", $newLayoutArr['col2'][5]['xg_embed_key']);
        $this->assertEqual(2, $newLayoutArr['col2'][5]['xg_embed_instance_id']);
        $this->assertEqual(6, count($newLayoutArr['col2']));
    }

    public function testAddMissingEmbed() {
        $newLayoutJson = '{"iteration":"0",
            "col1":[
                {"xg_embed_instance_id":"0","xg_embed_key":"_badge"},
                {"xg_embed_instance_id":"3","xg_embed_key":"_profileqa"},
                {"xg_embed_instance_id":"14","xg_embed_key":"music"},
                {"xg_embed_instance_id":"13","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"1","xg_embed_key":"photo"},
                {"xg_embed_instance_id":"4","xg_embed_key":"feed"},
                {"xg_embed_instance_id":"5","xg_embed_key":"html"},
                {"xg_embed_instance_id":"15","xg_embed_key":"activity"}],
            "col2":[
                {"xg_embed_instance_id":"2","xg_embed_key":"video"},
                {"xg_embed_instance_id":"11","xg_embed_key":"forum"},
                {"xg_embed_instance_id":"7","xg_embed_key":"_blogposts"},
                {"xg_embed_instance_id":"8","xg_embed_key":"_chatterwall"},
                {"xg_embed_instance_id":"20","xg_embed_key":"events"}]
        }';
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $layoutArr = $json->decode($newLayoutJson);

        $details = array('location' => array('col2', 3), 'widgetName' => 'newwidget', 'col1Action' => 'embed1', 'col2Action' => 'embed2');
        $newLayoutArr = TestLayoutEditHelper::addMissingEmbed(self::PROFILE_PAGE_LAYOUT_XML2, $layoutArr, "_newembed", $details);
        $this->assertEqual('_newembed', $newLayoutArr['col2'][3]['xg_embed_key']);
        $this->assertEqual(6, count($newLayoutArr['col2']));

        $details = array('location' => array('col2', 9), 'widgetName' => 'newwidget2', 'col1Action' => 'embed1', 'col2Action' => 'embed2');
        $newLayoutArr = TestLayoutEditHelper::addMissingEmbed(self::PROFILE_PAGE_LAYOUT_XML2, $layoutArr, "_newembed2", $details);
        $this->assertEqual('_newembed2', $newLayoutArr['col2'][5]['xg_embed_key']);
        $this->assertEqual(6, count($newLayoutArr['col2']));

        $details = array('location' => array('col1', 1), 'widgetName' => 'profiles', 'col1Action' => 'embed1friends', 'embedInstanceId' => 6);
        $newLayoutArr = TestLayoutEditHelper::addMissingEmbed(self::PROFILE_PAGE_LAYOUT_XML2, $layoutArr, "_friends", $details);
        $this->assertEqual('_friends', $newLayoutArr['col1'][1]['xg_embed_key']);
        $this->assertEqual("6", $newLayoutArr['col1'][1]['xg_embed_instance_id']);
        $this->assertEqual(9, count($newLayoutArr['col1']));

        $details = array('location' => array('col2', 0), 'widgetName' => 'profiles', 'col2Action' => 'embed3welcome', 'embedInstanceId' => 12);
        $newLayoutArr = TestLayoutEditHelper::addMissingEmbed(self::PROFILE_PAGE_LAYOUT_XML2, $layoutArr, "_welcome", $details);
        $this->assertEqual('_welcome', $newLayoutArr['col2'][0]['xg_embed_key']);
        $this->assertEqual("12", $newLayoutArr['col2'][0]['xg_embed_instance_id']);
        $this->assertEqual(8, count($newLayoutArr['col1']));
        $this->assertEqual(6, count($newLayoutArr['col2']));

        $details = array('location' => array('banner', 0), 'widgetName' => 'profiles', 'col3Action' => 'embed3pagetitle', 'embedInstanceId' => 10);
        $newLayoutArr = TestLayoutEditHelper::addMissingEmbed(self::PROFILE_PAGE_LAYOUT_XML2, $layoutArr, "_pagetitle", $details);
        $this->assertEqual('_pagetitle', $newLayoutArr['banner'][0]['xg_embed_key']);
        $this->assertEqual("10", $newLayoutArr['banner'][0]['xg_embed_instance_id']);
        $this->assertEqual(1, count($newLayoutArr['banner']));
    }

    public function testFindEmbed() {
        $layoutArr = array('iteration' => 1, 'col1' => array(), 'col2' => array(array(), array('xg_embed_key' => '_badge', 'xg_embed_instance_id' => 0)));
        $embed = TestLayoutEditHelper::findEmbed($layoutArr, '_badge');
        $this->assertEqual('_badge', $embed['xg_embed_key']);
        $this->assertEqual(0, $embed['xg_embed_instance_id']);
    }

    //TODO: create a test of addMissingEmbeds [Thomas David Baker 2008-05-21]

    public function testDetermineNewLayoutMainPage() {
        $originalLayoutXml = self::MAIN_PAGE_LAYOUT_XML;
        // Add a feed at the bottom of column 1, add groups in the middle of column 2,
        // add html box to col3.
        // Move _you (112) to the bottom of col3 (from the top), move ads (113) into column 1,
        // move feed (102) from col1 to bottom of col2, move feed (106) from col2 to top of col3,
        // move activity (108) to top of col2, move blog posts (110) to sidebar from col2.
        // Remove badges (116), forum (111), video (103) and photos (109).
        $newLayoutJson = '{"iteration":"9",
            "col1":[
                {"xg_embed_instance_id":"100","xg_width_option":"w1","xg_embed_limit":"1","xg_embed_key":"_description"},
                {"xg_embed_instance_id":"101","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"_members"},
                {"xg_embed_instance_id":"113","xg_width_option":"w1","xg_embed_limit":"1","xg_embed_key":"_ads"},
                {"xg_width_option":"w12","xg_embed_limit":"10","xg_embed_key":"feed"}],
            "col2":[
                {"xg_embed_instance_id":"108","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"activity"},
                {"xg_embed_instance_id":"104","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"notes"},
                {"xg_embed_instance_id":"105","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"events"},
                {"xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"102","xg_width_option":"w12","xg_embed_limit":"10","xg_embed_key":"feed"}],
            "col3":[
                {"xg_embed_instance_id":"106","xg_width_option":"w12","xg_embed_limit":"10","xg_embed_key":"feed"}],
            "sidebar":[
                {"xg_width_option":"w12","xg_embed_limit":"10","xg_embed_key":"html"},
                {"xg_embed_instance_id":"114","xg_width_option":"w1","xg_embed_limit":"1","xg_embed_key":"_createdBy"},
                {"xg_embed_instance_id":"110","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"profiles"},
                {"xg_embed_instance_id":"112","xg_width_option":"w1","xg_embed_limit":"1","xg_embed_key":"_you"}]
        }';
        $layout = new DOMDocument();
        $layout->loadXML(TestLayout::removeWhitespaceBetweenTags($originalLayoutXml));
        $originalLayout = XG_Layout::load('index');
        $originalLayout->loadLayout($layout);
        $xpath = new DOMXPath($originalLayout->getLayout());
        $nodeList = $xpath->query("//module");
        //TODO: We could use class constants or just function-level variables instead of these magic numbers throughout this test class [Thomas David Baker 2008-09-17]
        $this->assertEqual(15, $nodeList->length);
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(14, $embedsAdded);
        $xpath = new DOMXPath($newLayout->getLayout());
        $nodeList = $xpath->query("//module");
        $this->assertEqual(14, $nodeList->length);
        $groupsList = $xpath->query("//module[@action='embed2' and @widgetName='groups']");
        $this->assertEqual(1, $groupsList->length);
        $activityList = $xpath->query("/layout/colgroup/column/colgroup/column[2]/module[1]");
        $this->assertEqual(1, $activityList->length);
        $activity = $activityList->item(0);
        $this->assertEqual('activity', $activity->getAttribute('widgetName'));
        $this->assertEqual('embed2', $activity->getAttribute('action'));
        $videoList = $xpath->query("//module[@widgetName='video']");
        $this->assertEqual(0, $video->length);
        $feedList = $xpath->query("//module[@widgetName='feed']");
        $this->assertEqual(3, $feedList->length);
        $feedList->item(0)->getAttribute('embedInstanceId');
        $this->assertTrue($feedList->item(0)->getAttribute('embedInstanceId') >= 117);
        $this->assertTrue($feedList->item(1)->getAttribute('embedInstanceId') == 102);
        $this->assertEqual("showDescriptions", $feedList->item(1)->lastChild->nodeName);
        $this->assertEqual("1", $feedList->item(1)->lastChild->nodeValue);
        $this->assertTrue($feedList->item(2)->getAttribute('embedInstanceId') == 106);
    }

    public function testDetermineNewLayoutMainPageEmpty() {
        $originalLayoutXml = self::MAIN_PAGE_LAYOUT_XML;
        $newLayoutJson = '{"iteration":"9", "col1":[], "col2":[], "col3":[], "sidebar":[]}';
        $layout = new DOMDocument();
        $layout->loadXML(TestLayout::removeWhitespaceBetweenTags($originalLayoutXml));
        $originalLayout = XG_Layout::load('index');
        $originalLayout->loadLayout($layout);
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(0, $embedsAdded);
        $this->assertEqual(trim(TestLayout::removeWhitespaceBetweenTags($originalLayoutXml)),
            trim(TestLayout::removeWhitespaceBetweenTags($newLayout->getLayout()->saveXml())));
    }

    public function testDetermineNewLayoutMainPageNoHelloUsername() {
        $originalLayoutXml = self::MAIN_PAGE_LAYOUT_XML;
        $newLayoutJson = '{"iteration":"9",
            "col1":[
                {"xg_embed_instance_id":"100","xg_width_option":"w1","xg_embed_limit":"1","xg_embed_key":"_description"},
                {"xg_embed_instance_id":"101","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"_members"},
                {"xg_embed_instance_id":"113","xg_width_option":"w1","xg_embed_limit":"1","xg_embed_key":"_ads"},
                {"xg_width_option":"w12","xg_embed_limit":"10","xg_embed_key":"feed"}],
            "col2":[
                {"xg_embed_instance_id":"108","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"activity"},
                {"xg_embed_instance_id":"104","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"notes"},
                {"xg_embed_instance_id":"105","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"events"},
                {"xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"102","xg_width_option":"w12","xg_embed_limit":"10","xg_embed_key":"feed"}],
            "col3":[
                {"xg_embed_instance_id":"106","xg_width_option":"w12","xg_embed_limit":"10","xg_embed_key":"feed"}],
            "sidebar":[
                {"xg_width_option":"w12","xg_embed_limit":"10","xg_embed_key":"html"},
                {"xg_embed_instance_id":"114","xg_width_option":"w1","xg_embed_limit":"1","xg_embed_key":"_createdBy"},
                {"xg_embed_instance_id":"110","xg_width_option":"w12","xg_embed_limit":"1","xg_embed_key":"profiles"}]
        }';
        $layout = new DOMDocument();
        $layout->loadXML(TestLayout::removeWhitespaceBetweenTags($originalLayoutXml));
        $originalLayout = XG_Layout::load('index');
        $originalLayout->loadLayout($layout);
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(0, $embedsAdded);
        $this->assertEqual(trim(TestLayout::removeWhitespaceBetweenTags($originalLayoutXml)),
            trim(TestLayout::removeWhitespaceBetweenTags($newLayout->getLayout()->saveXml())));
    }

    public function testDetermineNewLayoutProfiles() {
        $originalLayoutXml = self::PROFILE_PAGE_LAYOUT_XML;
        $newLayoutJson = '{"iteration":"0",
            "col1":[
                {"xg_embed_instance_id":"0","xg_embed_key":"_badge"},
                {"xg_embed_instance_id":"6","xg_embed_key":"_friends"},
                {"xg_embed_instance_id":"3","xg_embed_key":"_profileqa"},
                {"xg_embed_instance_id":"14","xg_embed_key":"music"},
                {"xg_embed_instance_id":"13","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"1","xg_embed_key":"photo"},
                {"xg_embed_instance_id":"4","xg_embed_key":"feed"},
                {"xg_embed_instance_id":"5","xg_embed_key":"html"},
                {"xg_embed_instance_id":"15","xg_embed_key":"activity"}],
            "col2":[
                {"xg_embed_instance_id":"16","xg_embed_key":"_userpagetitle"},
                {"xg_embed_instance_id":"2","xg_embed_key":"video"},
                {"xg_embed_instance_id":"11","xg_embed_key":"forum"},
                {"xg_embed_instance_id":"7","xg_embed_key":"_blogposts"},
                {"xg_embed_instance_id":"8","xg_embed_key":"_chatterwall"},
                {"xg_embed_instance_id":"20","xg_embed_key":"events"}]
        }';
        $originalLayout = $this->loadTestProfilesLayout($originalLayoutXml);
        $xpath = new DOMXPath($originalLayout->getLayout());
        $oldNodeList = $xpath->query("//module");
        $this->assertEqual(18, $oldNodeList->length);
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(17, $embedsAdded); // We don't add the sidebar
        $xpath = new DOMXPath($newLayout->getLayout());
        $newNodeList = $xpath->query("//module");
        $this->assertEqual(18, $newNodeList->length);
        $oldTotals = self::widgetTotals($oldNodeList);
        $newTotals = self::widgetTotals($newNodeList);
        foreach ($oldTotals as $widgetName => $count) {
            $this->assertEqual($widgetName . $count, $widgetName . $newTotals[$widgetName]);
        }
        $nodeList = $xpath->query('//module[@action="embed1profileqa"]');
        $this->assertEqual(1, $nodeList->length);
        $nodeList = $xpath->query('//module[@action="embed2profileqa"]');
        $this->assertEqual(0, $nodeList->length);
        $nodeList = $xpath->query("/layout/colgroup/column/colgroup/column[1]/module[6]");
        $this->assertEqual(1, $nodeList->length);
        $photo = $nodeList->item(0);
        $this->assertEqual("photo", $photo->getAttribute('widgetName'));
        $this->assertEqual("embed1", $photo->getAttribute('action'));
        $this->assertEqual("1", $photo->getAttribute('embedInstanceId'));
        $nodeList = $xpath->query("/layout/colgroup/column/colgroup/column[1]/module[5]");
        $this->assertEqual(1, $nodeList->length);
        $groups = $nodeList->item(0);
        $this->assertEqual("itemCount", $groups->lastChild->nodeName);
        $this->assertEqual("23", $groups->lastChild->nodeValue);
        $welcome = $xpath->query("//module[@action='embed3welcome']")->item(0);
        $this->assertEqual("visible", $welcome->firstChild->nodeName);
        $this->assertEqual("1", $welcome->firstChild->nodeValue);
    }

    private static function widgetTotals($nodeList) {
        $totals = array();
        foreach ($nodeList as $module) {
            $totals[$module->getAttribute('widgetName')] =
                isset($totals[$module->getAttribute('widgetName')]) ? $totals[$module->getAttribute('widgetName')] + 1 : 1;
        }
        return $totals;
    }

    //TODO: the code and checks for this and testDetermineNewLayoutProfile are so similar make them into a shared function
    // with some specific checking left in the calling functions.  [Thomas David Baker 2008-05-21]
    public function testDetermineNewLayoutProfiles2() {
        $originalLayoutXml = self::PROFILE_PAGE_LAYOUT_XML2;
        $newLayoutJson = '{"iteration":"2",
            "col1":[
                {"xg_embed_instance_id":"14","xg_embed_key":"music"},
                {"xg_embed_instance_id":"13","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"1","xg_embed_key":"photo"},
                {"xg_embed_instance_id":"4","xg_embed_key":"feed"},
                {"xg_embed_instance_id":"5","xg_embed_key":"html"},
                {"xg_embed_instance_id":"15","xg_embed_key":"activity"},
                {"xg_embed_instance_id":"6","xg_embed_key":"_friends"},
                {"xg_embed_instance_id":"20","xg_embed_key":"events"},
                {"xg_embed_instance_id":"3","xg_embed_key":"_profileqa"}],
            "col2":[
                {"xg_embed_instance_id":"2","xg_embed_key":"video"},
                {"xg_embed_instance_id":"11","xg_embed_key":"forum"},
                {"xg_embed_instance_id":"7","xg_embed_key":"_blogposts"},
                {"xg_embed_instance_id":"8","xg_embed_key":"_chatterwall"},
                {"xg_embed_instance_id":"0","xg_embed_key":"_badge"}]}';
        $originalLayout = $this->loadTestProfilesLayout($originalLayoutXml);
        $xpath = new DOMXPath($originalLayout->getLayout());
        $oldNodeList = $xpath->query("//module");
        $this->assertEqual(18, $oldNodeList->length);
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(17, $embedsAdded); // We don't add the sidebar - it is hardcoded
        $xpath = new DOMXPath($newLayout->getLayout());
        $newNodeList = $xpath->query("//module");
        $oldTotals = self::widgetTotals($oldNodeList);
        $newTotals = self::widgetTotals($newNodeList);
        foreach ($oldTotals as $widgetName => $count) {
            $this->assertEqual($widgetName . $count, $widgetName . $newTotals[$widgetName]);
        }
        $this->assertEqual(18, $newNodeList->length);
        $profileqaList = $xpath->query("/layout/colgroup/column/colgroup/column/module[@action='embed1profileqa']");
        $this->assertEqual(1, $profileqaList->length);
        $forumList = $xpath->query("/layout/colgroup/column/colgroup/column[2]/module[@widgetName='forum']");
        $this->assertEqual(1, $forumList->length);
        $forum = $forumList->item(0);
        $this->assertEqual("11", $forum->getAttribute('embedInstanceId'));
        $this->assertEqual('itemCount', $forum->lastChild->nodeName);
        $this->assertEqual('8', $forum->lastChild->nodeValue);
        $welcome = $xpath->query("//module[@action='embed3welcome']")->item(0);
        $this->assertEqual("visible", $welcome->firstChild->nodeName);
        $this->assertEqual("0", $welcome->firstChild->nodeValue);
        // Let's make sure the badge didn't move even though we told it to.
        //TODO: this fails because we don't have badge-position-preserving code.  We just insert it if it is missing.
        // [Thomas David Baker 2008-07-17]
        return;
        $badgeList = $xpath->query("/layout/colgroup/column/colgroup/column[1]/module[@action='embed1smallbadge']");
        $this->assertTrue($badgeList->length == 1);
        if ($badgeList->length == 1) {
            $badge = $badgeList->item(0);
            $this->assertEqual("0", $badge->getAttribute('embedInstanceId'));
        }
    }

    public function testDetermineNewLayoutProfilesLotsOfModulesInactive() {
        $originalLayoutXml = self::PROFILE_PAGE_LAYOUT_XML;
        $newLayoutJson = '{"iteration":"0",
            "col1":[
                {"xg_embed_instance_id":"0","xg_embed_key":"_badge"},
                {"xg_embed_instance_id":"6","xg_embed_key":"_friends"},
                {"xg_embed_instance_id":"3","xg_embed_key":"_profileqa"},
                {"xg_embed_instance_id":"14","xg_embed_key":"music"},
                {"xg_embed_instance_id":"15","xg_embed_key":"activity"}],
            "col2":[
                {"xg_embed_instance_id":"2","xg_embed_key":"video"},
                {"xg_embed_instance_id":"7","xg_embed_key":"_blogposts"},
                {"xg_embed_instance_id":"8","xg_embed_key":"_chatterwall"},
                {"xg_embed_instance_id":"20","xg_embed_key":"events"}]
        }';
        $originalLayout = $this->loadTestProfilesLayout($originalLayoutXml);
        $xpath = new DOMXPath($originalLayout->getLayout());
        $oldNodeList = $xpath->query("//module");
        $this->assertEqual(18, $oldNodeList->length);
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(17, $embedsAdded); // We don't add the sidebar - it is hardcoded.
        $xpath = new DOMXPath($newLayout->getLayout());
        $newNodeList = $xpath->query("//module");
        $this->assertEqual(18, $newNodeList->length);
        $oldTotals = self::widgetTotals($oldNodeList);
        $newTotals = self::widgetTotals($newNodeList);
        foreach ($oldTotals as $widgetName => $count) {
            $this->assertEqual($widgetName . $count, $widgetName . $newTotals[$widgetName]);
        }
        $nodeList = $xpath->query('//module[@action="embed1profileqa"]');
        $this->assertEqual(1, $nodeList->length);
        $nodeList = $xpath->query('//module[@action="embed2profileqa"]');
        $this->assertEqual(0, $nodeList->length);
        $nodeList = $xpath->query("/layout/colgroup/column/colgroup/column[1]/module[6]");
        $this->assertEqual(1, $nodeList->length);
        $music = $nodeList->item(0);
        $this->assertEqual("music", $music->getAttribute('widgetName'));
        $this->assertEqual("embed1", $music->getAttribute('action'));
        $this->assertEqual("14", $music->getAttribute('embedInstanceId'));
        $nodeList = $xpath->query("/layout/colgroup/column/colgroup/column[1]/module[4]");
        $this->assertEqual(1, $nodeList->length);
        $groups = $nodeList->item(0);
        $this->assertEqual("itemCount", $groups->lastChild->nodeName);
        $this->assertEqual("groups", $groups->getAttribute('widgetName'));
        $this->assertEqual("23", $groups->lastChild->nodeValue);
        $welcome = $xpath->query("//module[@action='embed3welcome']")->item(0);
        $this->assertEqual("visible", $welcome->firstChild->nodeName);
        $this->assertEqual("1", $welcome->firstChild->nodeValue);
    }

    public function testDetermineNewLayoutProfilesPreservingBadge() {
        $originalLayoutXml = self::PROFILE_PAGE_LAYOUT_XML2;
        $newLayoutJson = '{"iteration":"2",
            "col1":[{"xg_embed_instance_id":"6","xg_embed_key":"_friends"},
                {"xg_embed_instance_id":"14","xg_embed_key":"music"},
                {"xg_embed_instance_id":"4","xg_embed_key":"feed"}],
            "col2":[{"xg_embed_instance_id":"15","xg_embed_key":"activity"},
                {"xg_embed_instance_id":"5","xg_embed_key":"html"},
                {"xg_embed_instance_id":"3","xg_embed_key":"_profileqa"},
                {"xg_embed_instance_id":"8","xg_embed_key":"_chatterwall"}]}';
        $originalLayout = $this->loadTestProfilesLayout($originalLayoutXml);
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $xpath = new DOMXPath($newLayout->getLayout());
        $modules = $xpath->query("//module");
        $this->assertEqual(17, $embedsAdded); // We don't add the sidebar - it is hardcoded.
        $xpath = new DOMXPath($newLayout->getLayout());
        $newNodeList = $xpath->query("//module");
        $this->assertEqual(18, $newNodeList->length);
        $xpath = new DOMXPath($newLayout->getLayout());
        $badgeModule = $xpath->query("/layout/colgroup/column/colgroup/column[1]/module[1]")->item(0);
        $this->assertEqual("module", $badgeModule->nodeName);
        $this->assertEqual("embed1smallbadge", $badgeModule->getAttribute('action'));
        $this->assertEqual("profiles", $badgeModule->getAttribute('widgetName'));
    }

    public function testDetermineNewLayoutProfilesIncorrectEmbedInstanceIds() {
        $originalLayoutXml = self::PROFILE_PAGE_LAYOUT_XML2;
        $newLayoutJson = '{"iteration":"2",
            "col1":[
                {"xg_embed_instance_id":"999","xg_embed_key":"_badge"},
                {"xg_embed_instance_id":"999","xg_embed_key":"_friends"},
                {"xg_embed_instance_id":"999","xg_embed_key":"_profileqa"},
                {"xg_embed_instance_id":"999","xg_embed_key":"music"},
                {"xg_embed_instance_id":"999","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"999","xg_embed_key":"photo"},
                {"xg_embed_instance_id":"999","xg_embed_key":"feed"},
                {"xg_embed_instance_id":"999","xg_embed_key":"html"},
                {"xg_embed_instance_id":"999","xg_embed_key":"activity"}],
            "col2":[
                {"xg_embed_instance_id":"999","xg_embed_key":"video"},
                {"xg_embed_instance_id":"999","xg_embed_key":"forum"},
                {"xg_embed_instance_id":"999","xg_embed_key":"_blogposts"},
                {"xg_embed_instance_id":"999","xg_embed_key":"_chatterwall"},
                {"xg_embed_instance_id":"999","xg_embed_key":"events"}]
        }';
        $originalLayout = $this->loadTestProfilesLayout($originalLayoutXml);
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(0, $embedsAdded);
        $this->assertEqual(trim(TestLayout::removeWhitespaceBetweenTags($originalLayoutXml)),
            trim(TestLayout::removeWhitespaceBetweenTags($newLayout->getLayout()->saveXml())));
    }

    public function testDetermineNewLayoutProfilesIncorrectEmbedKey() {
        $originalLayoutXml = self::PROFILE_PAGE_LAYOUT_XML2;
        $newLayoutJson = '{"iteration":"2",
            "col1":[
                {"xg_embed_instance_id":"14","xg_embed_key":"illegal_embed_key"},
                {"xg_embed_instance_id":"13","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"1","xg_embed_key":"photo"},
                {"xg_embed_instance_id":"4","xg_embed_key":"feed"},
                {"xg_embed_instance_id":"5","xg_embed_key":"html"},
                {"xg_embed_instance_id":"15","xg_embed_key":"activity"},
                {"xg_embed_instance_id":"6","xg_embed_key":"_friends"},
                {"xg_embed_instance_id":"20","xg_embed_key":"events"},
                {"xg_embed_instance_id":"3","xg_embed_key":"_profileqa"}],
            "col2":[
                {"xg_embed_instance_id":"2","xg_embed_key":"video"},
                {"xg_embed_instance_id":"11","xg_embed_key":"forum"},
                {"xg_embed_instance_id":"7","xg_embed_key":"_blogposts"},
                {"xg_embed_instance_id":"8","xg_embed_key":"_chatterwall"},
                {"xg_embed_instance_id":"0","xg_embed_key":"_badge"}]}';
        $originalLayout = $this->loadTestProfilesLayout($originalLayoutXml);
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(0, $embedsAdded);
        $this->assertEqual(trim(TestLayout::removeWhitespaceBetweenTags($originalLayoutXml)),
            trim(TestLayout::removeWhitespaceBetweenTags($newLayout->getLayout()->saveXml())));
    }

    public function testDetermineNewLayoutProfilesGarbage() {
        $originalLayoutXml = XG_LayoutEditHelperTest::PROFILE_PAGE_LAYOUT_XML2;
        $originalLayout = $this->loadTestProfilesLayout($originalLayoutXml);
        $newLayoutJson = '{}';
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(17, $embedsAdded);
        //TODO: Because we accept a blank input and because SERVICES_JSON accepts anything as valid when initialized
        // with SERVICES_JSON_LOOSE_TYPE we do not detect this as an error.  When we stop accepting blank input as
        // (Frink v2) this problem will resolve itself.
        $newLayoutJson = 'This is not even valid JSON';
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(17, $embedsAdded);
    }

    public function testDetermineNewLayoutProfilesDuplicates() {
        $originalLayoutXml = self::PROFILE_PAGE_LAYOUT_XML2;
        $originalLayout = $this->loadTestProfilesLayout($originalLayoutXml);
        // Two music embeds (one new) - not valid.
        $newLayoutJson = '{"iteration":"2",
            "col1":[
                {"xg_embed_instance_id":"14","xg_embed_key":"music"},
                {"xg_embed_instance_id":"","xg_embed_key":"music"},
                {"xg_embed_instance_id":"13","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"1","xg_embed_key":"photo"},
                {"xg_embed_instance_id":"4","xg_embed_key":"feed"},
                {"xg_embed_instance_id":"5","xg_embed_key":"html"},
                {"xg_embed_instance_id":"15","xg_embed_key":"activity"},
                {"xg_embed_instance_id":"6","xg_embed_key":"_friends"},
                {"xg_embed_instance_id":"20","xg_embed_key":"events"},
                {"xg_embed_instance_id":"3","xg_embed_key":"_profileqa"}],
            "col2":[
                {"xg_embed_instance_id":"2","xg_embed_key":"video"},
                {"xg_embed_instance_id":"11","xg_embed_key":"forum"},
                {"xg_embed_instance_id":"7","xg_embed_key":"_blogposts"},
                {"xg_embed_instance_id":"8","xg_embed_key":"_chatterwall"},
                {"xg_embed_instance_id":"0","xg_embed_key":"_badge"}]}';
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(0, $embedsAdded);
        $this->assertEqual(trim(TestLayout::removeWhitespaceBetweenTags($originalLayoutXml)),
            trim(TestLayout::removeWhitespaceBetweenTags($newLayout->getLayout()->saveXml())));
        // Two music embeds (with same embed id) - not valid.
        $newLayoutJson = '{"iteration":"2",
            "col1":[
                {"xg_embed_instance_id":"14","xg_embed_key":"music"},
                {"xg_embed_instance_id":"14","xg_embed_key":"music"},
                {"xg_embed_instance_id":"13","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"1","xg_embed_key":"photo"},
                {"xg_embed_instance_id":"4","xg_embed_key":"feed"},
                {"xg_embed_instance_id":"5","xg_embed_key":"html"},
                {"xg_embed_instance_id":"15","xg_embed_key":"activity"},
                {"xg_embed_instance_id":"6","xg_embed_key":"_friends"},
                {"xg_embed_instance_id":"20","xg_embed_key":"events"},
                {"xg_embed_instance_id":"3","xg_embed_key":"_profileqa"}],
            "col2":[
                {"xg_embed_instance_id":"2","xg_embed_key":"video"},
                {"xg_embed_instance_id":"11","xg_embed_key":"forum"},
                {"xg_embed_instance_id":"7","xg_embed_key":"_blogposts"},
                {"xg_embed_instance_id":"8","xg_embed_key":"_chatterwall"},
                {"xg_embed_instance_id":"0","xg_embed_key":"_badge"}]}';
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertEqual(0, $embedsAdded);
        $this->assertEqual(trim(TestLayout::removeWhitespaceBetweenTags($originalLayoutXml)),
            trim(TestLayout::removeWhitespaceBetweenTags($newLayout->getLayout()->saveXml())));
    }

    public function testPreserveVersion() {
        $originalLayoutXml = self::PROFILE_PAGE_LAYOUT_XML2;
        $originalLayout = $this->loadTestProfilesLayout($originalLayoutXml);
        // Two music embeds (one new) - not valid.
        $newLayoutJson = '{"iteration":"2",
            "col1":[
                {"xg_embed_instance_id":"14","xg_embed_key":"music"},
                {"xg_embed_instance_id":"13","xg_embed_key":"groups"},
                {"xg_embed_instance_id":"1","xg_embed_key":"photo"},
                {"xg_embed_instance_id":"4","xg_embed_key":"feed"},
                {"xg_embed_instance_id":"5","xg_embed_key":"html"},
                {"xg_embed_instance_id":"15","xg_embed_key":"activity"},
                {"xg_embed_instance_id":"6","xg_embed_key":"_friends"},
                {"xg_embed_instance_id":"20","xg_embed_key":"events"},
                {"xg_embed_instance_id":"3","xg_embed_key":"_profileqa"}],
            "col2":[
                {"xg_embed_instance_id":"2","xg_embed_key":"video"},
                {"xg_embed_instance_id":"11","xg_embed_key":"forum"},
                {"xg_embed_instance_id":"7","xg_embed_key":"_blogposts"},
                {"xg_embed_instance_id":"8","xg_embed_key":"_chatterwall"},
                {"xg_embed_instance_id":"0","xg_embed_key":"_badge"}]}';
        $this->assertEqual("6", $originalLayout->getLayout()->documentElement->getAttribute('version'));
        list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($originalLayout, $newLayoutJson);
        $this->assertTrue($embedsAdded > 0);
        $this->assertEqual("6", $newLayout->getLayout()->documentElement->getAttribute('version'));
    }

    public function testGetEmbedKey() {
        $this->assertEqual('_chatterwall', XG_LayoutEditHelper::getEmbedKey('profiles', 'profiles', 'embed2chatterwall'));
        $this->assertEqual('_badge', XG_LayoutEditHelper::getEmbedKey('profiles', 'profiles', 'embed1smallbadge'));
        $this->assertEqual('music', XG_LayoutEditHelper::getEmbedKey('profiles', 'music', 'embed1'));
        //TODO: test some homepage embeds too. [Thomas David Baker 2008-05-21]
    }

    public function testCheckEmbedDetails() {
        $exception = false;
        try {
            TestLayoutEditHelper::checkEmbedDetails(null, null, null, null);
        } catch (Exception $e) {
            $exception = true;
        }
        $this->assertTrue($exception);
        //TODO more here? [Thomas David Baker 2008-05-21]
    }

    public function testEmbedIsMovable() {
        $this->assertTrue(XG_LayoutEditHelper::embedIsMovable("profiles", "music", "embed1"));
        $this->assertTrue(XG_LayoutEditHelper::embedIsMovable("profiles", "music", "embed2"));
        $this->assertFalse(XG_LayoutEditHelper::embedIsMovable("profiles", "profiles", "embed1smallbadge"));
        $this->assertFalse(XG_LayoutEditHelper::embedIsMovable("profiles", "profiles", "embed3welcome"));
        $this->assertFalse(XG_LayoutEditHelper::embedIsMovable("profiles", "profiles", "embed3pagetitle"));
        //TODO: test homepage embeds also [Thomas David Baker 2008-05-21]
    }

    public function testCouldLoseData() {
        $layoutArr = array("iteration" => "-1", "col1" => array(), "col2" => array(), "col3" => array(), "sidebar" => array());
        $this->assertTrue(TestLayoutEditHelper::couldLoseData(XG_LayoutEditHelperTest::MAIN_PAGE_LAYOUT_XML, $layoutArr));
        //TODO: Create an added-things-but-not-removed-anything test of main page, too. [Thomas David Baker 2008-05-21]
        $layoutArr = array('iteration' => '-1',
            'col1' => array(
                array('xg_embed_instance_id' => "0", 'xg_embed_key' => "_badge"),
                array('xg_embed_instance_id' => "6", 'xg_embed_key' => "_friends"),
                array('xg_embed_instance_id' => "3", 'xg_embed_key' => "_profileqa"),
                array('xg_embed_instance_id' => "14", 'xg_embed_key' => "music"),
                array('xg_embed_instance_id' => "15", 'xg_embed_key' => "activity")
            ),
            'col2' => array(
                array('xg_embed_instance_id' => "2", 'xg_embed_key' => "video"),
                array('xg_embed_instance_id' => "7", 'xg_embed_key' => "_blogposts"),
                array('xg_embed_instance_id' => "8", 'xg_embed_key' => "_chatterwall"),
                array('xg_embed_instance_id' => "20", 'xg_embed_key' => "events")
            )
        );
        $this->assertTrue(TestLayoutEditHelper::couldLoseData(XG_LayoutEditHelperTest::PROFILE_PAGE_LAYOUT_XML2, $layoutArr));
        $layoutArr = array('iteration' => "2",
            'banner' => array(
                array('xg_embed_instance_id' => "10", "xg_embed_key" => "_pagetitle")
            ),
            'col1' => array(
                array('xg_embed_instance_id' => "12", 'xg_embed_key' => "_welcome"),
                array('xg_embed_instance_id' => "14", 'xg_embed_key' => "music"),
                array('xg_embed_instance_id' => "13", 'xg_embed_key' => "groups"),
                array('xg_embed_instance_id' => "1", 'xg_embed_key' => "photo"),
                array('xg_embed_instance_id' => "4", 'xg_embed_key' => "feed"),
                array('xg_embed_instance_id' => "5", 'xg_embed_key' => "html"),
                array('xg_embed_instance_id' => "15", 'xg_embed_key' => "activity"),
                array('xg_embed_instance_id' => "6", 'xg_embed_key' => "_friends"),
                array('xg_embed_instance_id' => "20", 'xg_embed_key' => "events"),
                array('xg_embed_instance_id' => "3", 'xg_embed_key' => "_profileqa")
            ),
            'col2' => array(
                array('xg_embed_instance_id' => "16", 'xg_embed_key' => '_userpagetitle'),
                array('xg_embed_instance_id' => "2", 'xg_embed_key' => "video"),
                array('xg_embed_instance_id' => "11", 'xg_embed_key' => "forum"),
                array('xg_embed_instance_id' => "7", 'xg_embed_key' => "_blogposts"),
                array('xg_embed_instance_id' => "8", 'xg_embed_key' => "_chatterwall"),
                array('xg_embed_instance_id' => "0", 'xg_embed_key' => "_badge")
            )
        );
        $this->assertFalse(TestLayoutEditHelper::couldLoseData(XG_LayoutEditHelperTest::PROFILE_PAGE_LAYOUT_XML2, $layoutArr));
    }

    public function testRequiredEmbedsPresent() {
        $layoutArr = array('iteration' => "2", 'col1' => array(), 'col2' => array(), 'col3' => array(), 'sidebar' => array());
        $this->assertFalse(TestLayoutEditHelper::requiredEmbedsPresent($layoutArr));
        $layoutArr['sidebar'] = array(array('xg_embed_key' => '_you'));
        $this->assertFalse(TestLayoutEditHelper::requiredEmbedsPresent($layoutArr));
        $layoutArr['sidebar'] = array(array('xg_embed_key' => '_you'), array('xg_embed_key' => '_ads'), array('xg_embed_key' => '_createdBy'));
        $this->assertTrue(TestLayoutEditHelper::requiredEmbedsPresent($layoutArr));
    }

    public function testEmbedLimitsExceeded() {
        $embedList = array('x' => array('embedLimit' => 1), 'y' => array('embedLimit' => 2));
        $layoutArr = array('col1' => array(array('xg_embed_key' => 'x'), array('xg_embed_key' => 'y')));
        $this->assertFalse(TestLayoutEditHelper::embedLimitsExceeded($embedList, $layoutArr));
        $layoutArr = array('col1' => array(array('xg_embed_key' => 'x'), array('xg_embed_key' => 'x')));
        $this->assertTrue(TestLayoutEditHelper::embedLimitsExceeded($embedList, $layoutArr));
        $layoutArr = array('col1' => array(array('xg_embed_key' => 'y'), array('xg_embed_key' => 'y')));
        $this->assertFalse(TestLayoutEditHelper::embedLimitsExceeded($embedList, $layoutArr));
        $layoutArr = array('col1' => array(array('xg_embed_key' => 'x'), array('xg_embed_key' => 'y'), array('xg_embed_key' => 'y')));
        $this->assertFalse(TestLayoutEditHelper::embedLimitsExceeded($embedList, $layoutArr));
        $layoutArr = array('col1' => array(array('xg_embed_key' => 'y'), array('xg_embed_key' => 'y'), array('xg_embed_key' => 'y')));
        // Here we test the changes made for BAZ-7993 (allowing text boxes and RSS feeds to be grandfathered above the limit).
        $this->assertFalse(TestLayoutEditHelper::embedLimitsExceeded($embedList, $layoutArr));
    }

    public function loadTestProfilesLayout($layoutXml) {
        $layout = new DOMDocument();
        $layout->loadXML(TestLayout::removeWhitespaceBetweenTags($layoutXml));
        $testLayout = XG_Layout::load('infidilibum', 'profiles');
        $testLayout->loadLayout($layout);
        return $testLayout;
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

    const MAIN_PAGE_LAYOUT_XML = '<?xml version="1.0"?>
        <layout iteration="9" nextEmbedInstanceId="117">
            <colgroup locked="1">
                <column width="3">
                    <colgroup>
                        <column width="1">
                            <module widgetName="main" action="embed1siteDescription" embedInstanceId="100" sitewide=""/>
                            <module widgetName="profiles" action="embed1activeMembers" embedInstanceId="101" sitewide="">
                                <displaySet>large</displaySet>
                                <rowsSet>4</rowsSet>
                                <sortSet>featured</sortSet>
                            </module>
                            <module widgetName="feed" action="embed1" embedInstanceId="102" sitewide="">
                                <title>RSS</title>
                                <feedUrl>http://bluebones.net/feed/</feedUrl>
                                <itemCount>2</itemCount>
                                <showDescriptions>1</showDescriptions>
                            </module>
                            <module widgetName="video" action="embed1" embedInstanceId="103" sitewide="">
                                <displayType>detail</displayType>
                                <videoSet>all</videoSet>
                                <videoNum>2</videoNum>
                            </module>
                        </column>
                        <column width="2">
                            <module widgetName="notes" action="embed2" embedInstanceId="104" sitewide="">
                                <display>details</display>
                                <title/>
                                <from>featured</from>
                                <count>3</count>
                            </module>
                            <module widgetName="events" action="embed2" embedInstanceId="105" sitewide=""/>
                            <module widgetName="feed" action="embed2" embedInstanceId="106" sitewide="">
                                <title>RSS</title>
                                <itemCount>5</itemCount>
                                <feedUrl>http://blog.ning.com/atom.xml</feedUrl>
                                <showDescriptions>0</showDescriptions>
                            </module>
                            <module widgetName="activity" action="embed2" embedInstanceId="108" sitewide="">
                                <activityNum>8</activityNum>
                                <itemNum>3</itemNum>
                                <activityItemsCount>7</activityItemsCount>
                            </module>
                            <module widgetName="photo" action="embed2" embedInstanceId="109" sitewide="">
                                <photoSet>all</photoSet>
                                <photoNum>4</photoNum>
                                <photoType>slideshow</photoType>
                                <albumSet>all</albumSet>
                            </module>
                            <module widgetName="profiles" action="embed2" embedInstanceId="110" sitewide="">
                                <displaySet>detail</displaySet>
                                <postsSet>5</postsSet>
                            </module>
                            <module widgetName="forum" action="embed2" embedInstanceId="111" sitewide="">
                                <topicSet>recentlyUpdated</topicSet>
                                <itemCount>3</itemCount>
                            </module>
                        </column>
                    </colgroup>
                </column>
                <column width="1" locked="1">
                    <module widgetName="main" action="embed1you" embedInstanceId="112" sitewide="1"/>
                    <module widgetName="main" action="embed1ads" embedInstanceId="113" sitewide="1"/>
                    <module widgetName="main" action="embed1createdBy" embedInstanceId="114" sitewide="1"/>
                    <module widgetName="profiles" action="embed1badge" embedInstanceId="116" sitewide=""/>
                </column>
            </colgroup>
        </layout>';

    const PROFILE_PAGE_LAYOUT_XML = '<?xml version="1.0"?>
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
                                    <itemCount>23</itemCount>
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
                                <module widgetName="events" action="embed1" embedInstanceId="20"/>
                            </column>
                            <column width="2">
                                <module widgetName="profiles" action="embed2pagetitle" embedInstanceId="16"/>
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

    const PROFILE_PAGE_LAYOUT_XML2 = '<?xml version="1.0"?>
        <layout nextEmbedInstanceId="100" version="6" iteration="2">
            <colgroup locked="1">
                <column width="3">
                    <module widgetName="profiles" action="embed3pagetitle" embedInstanceId="10">
                        <screenName>infidibilum</screenName>
                    </module>
                    <colgroup>
                        <column width="1">
                            <module widgetName="profiles" action="embed1smallbadge" embedInstanceId="0">
                                <screenName>infidibilum</screenName>
                            </module>
                            <module widgetName="profiles" action="embed1friends" embedInstanceId="6">
                                <screenName>infidibilum</screenName>
                            </module>
                            <module widgetName="music" action="embed1" embedInstanceId="14">
                                <autoplay/>
                                <shuffle/>
                                <playlistSet>userplaylist</playlistSet>
                                <playlistId/>
                                <playlistUrl/>
                                <showPlaylist>true</showPlaylist>
                            </module>
                            <module widgetName="groups" action="embed1" embedInstanceId="13"/>
                            <module widgetName="forum" action="embed1" embedInstanceId="11">
                                <topicSet>recent</topicSet>
                                <itemCount>8</itemCount>
                            </module>
                            <module widgetName="events" action="embed1" embedInstanceId="20"/>
                            <module widgetName="feed" action="embed1" embedInstanceId="4"/>
                        </column>
                        <column width="2">
                            <module widgetName="profiles" action="embed2pagetitle" embedInstanceId="16"/>
                            <module widgetName="profiles" action="embed3welcome" embedInstanceId="12">
                                <visible>0</visible>
                            </module>
                            <module widgetName="activity" action="embed2" embedInstanceId="15">
                                <screenName>infidibilum</screenName>
                                <activityNum>8</activityNum>
                                <activityItemsCount>7</activityItemsCount>
                            </module>
                            <module widgetName="profiles" action="embed2profileqa" embedInstanceId="3">
                                <screenName>infidibilum</screenName>
                            </module>
                            <module widgetName="html" action="embed2" embedInstanceId="5"/>
                            <module widgetName="photo" action="embed2" embedInstanceId="1">
                                <photoSet>for_contributor</photoSet>
                                <albumSet>all</albumSet>
                                <photoNum>4</photoNum>
                                <photoType>slideshow</photoType>
                            </module>
                            <module widgetName="video" action="embed2" embedInstanceId="2">
                                <displayType>detail</displayType>
                                <videoSet>for_contributor</videoSet>
                                <videoNum>3</videoNum>
                            </module>
                            <module widgetName="profiles" action="embed2blogposts" embedInstanceId="7">
                                <screenName>infidibilum</screenName>
                                <displaySet>detail</displaySet>
                                <postsSet>5</postsSet>
                            </module>
                            <module widgetName="profiles" action="embed2chatterwall" embedInstanceId="8">
                                <screenName>infidibilum</screenName>
                            </module>
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

class TestLayoutEditHelper extends XG_LayoutEditHelper {
    public static function couldLoseData($initialLayoutXml, $layoutArr) {
        return parent::couldLoseData($initialLayoutXml, $layoutArr);
    }
    public function checkEmbedDetails($status, $widgetName, $action, $path) {
        return parent::checkEmbedDetails($status, $widgetName, $action, $path);
    }
    public static function embedLimitsExceeded($embedList, $newLayoutArr) {
        return parent::embedLimitsExceeded($embedList, $newLayoutArr);
    }
    public static function requiredEmbedsPresent($newLayoutArr) {
        return parent::requiredEmbedsPresent($newLayoutArr);
    }
    public static function insertEmbed($layoutArr, $embed, $location) {
        return parent::insertEmbed($layoutArr, $embed, $location);
    }
    public static function addMissingEmbed($initialLayoutXml, $layoutArr, $key, $details) {
        return parent::addMissingEmbed($initialLayoutXml, $layoutArr, $key, $details);
    }
    public static function findEmbed($layoutArr, $key) {
        return parent::findEmbed($layoutArr, $key);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
