<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_FileHelper.php');
XG_App::includeFileOnce('/lib/XG_QueryHelper.php');

class Topic2Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testAddSearchFilter() {
        XN_Content::create('Fish', 'any of numerous cold-blooded strictly aquatic craniate vertebrates')->save();
        XN_Content::create('Fish', "Don't open the door")->save();
        $this->assertEqual(0, $this->searchFish('interesting'));
        $this->assertEqual(1, $this->searchFish('aquatic'));
        $this->assertEqual(1, $this->searchFish('numerous   aquatic'));
        $this->assertEqual(0, $this->searchFish('numerous   aquaticAAA'));
        $this->assertEqual(1, $this->searchFish("don't"));
        $this->assertEqual(0, $this->searchFish("open't"));
    }

    public function testEmptyAttachmentSlotCount() {
        $topic = Topic::create();
        $this->assertEqual(3, Topic::emptyAttachmentSlotCount($topic));
        Forum_FileHelper::addAttachmentProper('a', 'a.gif', 1, $topic);
        $this->assertEqual(2, Topic::emptyAttachmentSlotCount($topic));
        Forum_FileHelper::addAttachmentProper('a', 'a.gif', 1, $topic);
        $this->assertEqual(1, Topic::emptyAttachmentSlotCount($topic));
        Forum_FileHelper::addAttachmentProper('a', 'a.gif', 1, $topic);
        $this->assertEqual(0, Topic::emptyAttachmentSlotCount($topic));
        Forum_FileHelper::addAttachmentProper('a', 'a.gif', 1, $topic);
        $this->assertEqual(0, Topic::emptyAttachmentSlotCount($topic));
    }

    private function searchFish($keywords) {
        $q = XN_Query::create('Content');
        $q->filter('owner');
        $q->filter('type', '=', 'Fish');
        $q->filter('my->test', '=', 'Y');
        XG_QueryHelper::addSearchFilter($q, $keywords);
        return count($q->execute());
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
