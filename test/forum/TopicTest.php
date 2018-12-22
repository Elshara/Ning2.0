<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_FileHelper.php');
XG_App::includeFileOnce('/lib/XG_QueryHelper.php');

class TopicTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testCreatedDate() {
        // TopicController sets lastEntryDate to the createdDate immediately after saving the Topic object [Jon Aquino 2007-04-18]
        $topic = Topic::create('test', 'test');
        $this->assertNull($topic->createdDate);
        $topic->save();
        $this->assertNotNull($topic->createdDate);
    }

    public function testCleanDescription() {
        $this->assertEqual('<b>Hello</b> <a href="http://google.com">http://google.com</a> world', Topic::cleanDescription('<b>Hello</b> http://google.com world<script>'));
        $this->assertEqual('No Description', Topic::cleanDescription(''));
    }

    public function testCleanTitle() {
        $this->assertEqual('<b>Hello</b> http://google.com world<script>', Topic::cleanTitle('<b>Hello</b> http://google.com world<script>'));
        $this->assertEqual('Untitled', Topic::cleanTitle(''));
    }

    public function testCreate() {
        XG_TestHelper::setCurrentWidget('forum');
        $topic = Topic::create();
        $this->assertNull($topic->title);
        $this->assertNull($topic->description);
    }

    public function testLastCommentContributorNames() {
        $topics = array();
        list(, $topic1) = XG_TestHelper::createTopic();
        $topic1->my->lastCommentContributorName = 'Joe';
        list(, $topic2) = XG_TestHelper::createTopic();
        list(, $topic3) = XG_TestHelper::createTopic();
        $topic3->my->lastCommentContributorName = 'Sue';
        list(, $topic4) = XG_TestHelper::createTopic();
        $topic4->my->lastCommentContributorName = 'Sue';
        $this->assertEqual(serialize(array('Joe', 'Sue')), serialize(array_values(Topic::lastCommentContributorNames(array($topic1, $topic2, $topic3, $topic4)))));
    }

    public function testTopics() {
        list(, $pizzaTopic) = XG_TestHelper::createTopic();
        list(, $pizzaComment) = XG_TestHelper::createComment($pizzaTopic);
        list(, $burgerTopic) = XG_TestHelper::createTopic();
        list(, $burgerComment) = XG_TestHelper::createComment($burgerTopic);

        $topics = Topic::topics(array($pizzaTopic, $pizzaComment, $burgerComment));
        $this->assertEqual(2, count($topics));
        $this->assertIdentical($pizzaTopic->id, $topics[$pizzaTopic->id]->id);
        $this->assertIdentical($burgerTopic->id, $topics[$burgerTopic->id]->id);

        $topics = Topic::topics(array($pizzaTopic));
        $this->assertEqual(1, count($topics));
        $this->assertIdentical($pizzaTopic->id, $topics[$pizzaTopic->id]->id);

        $topics = Topic::topics(array());
        $this->assertEqual(0, count($topics));

        $topics = Topic::topics(array($pizzaComment));
        $this->assertEqual(1, count($topics));
        $this->assertIdentical($pizzaTopic->id, $topics[$pizzaTopic->id]->id);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
