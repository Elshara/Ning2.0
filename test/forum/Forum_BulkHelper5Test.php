<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_FileHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_UserHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_BulkHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');

class Forum_BulkHelper5Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testRemoveCommentAndSubComments1() {
        list($pizzaTopicId, $pizzaTopic) = XG_TestHelper::createTopic();
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($pizzaTopicId)));
        list($pizzaComment1Id, $pizzaComment1) = XG_TestHelper::createComment($pizzaTopic);
        usleep(250000);
        list($pizzaComment2Id, $pizzaComment2) = XG_TestHelper::createComment($pizzaTopic, $pizzaComment1);
        usleep(250000);
        list($pizzaComment3Id, $pizzaComment3) = XG_TestHelper::createComment($pizzaTopic, $pizzaComment2);
        usleep(250000);
        list($pizzaComment4Id, $pizzaComment4) = XG_TestHelper::createComment($pizzaTopic);
        list($burgerTopicId, $burgerTopic) = XG_TestHelper::createTopic();
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($burgerTopicId)));
        list($burgerComment1Id, $burgerComment1) = XG_TestHelper::createComment($burgerTopic);
        usleep(250000);
        list($burgerComment2Id, $burgerComment2) = XG_TestHelper::createComment($burgerTopic, $burgerComment1);
        usleep(250000);
        list($burgerComment3Id, $burgerComment3) = XG_TestHelper::createComment($burgerTopic);
        $pizzaTopic->save();
        $burgerTopic->save();
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($pizzaTopicId)));
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($burgerTopicId)));
        $allIds = array($pizzaTopicId, $pizzaComment1Id, $pizzaComment2Id, $pizzaComment3Id, $pizzaComment4Id, $burgerTopicId, $burgerComment1Id, $burgerComment2Id, $burgerComment3Id);
        $this->assertEqual(implode(',', $allIds), implode(',', XG_TestHelper::existingIds($allIds)));
        $counts = Comment::getCounts(XN_Content::load($pizzaTopic->id));
        $this->assertEqual(4, $counts['commentCount']);
        $counts = Comment::getCounts(XN_Content::load($burgerTopic->id));
        $this->assertEqual(3, $counts['commentCount']);
        $this->assertEqual('3,0', implode(',', Forum_BulkHelper::removeCommentAndSubComments($pizzaComment1, 100)));
        $this->assertEqual(implode(',', array($pizzaTopicId, $pizzaComment4Id, $burgerTopicId, $burgerComment1Id, $burgerComment2Id, $burgerComment3Id)), implode(',', XG_TestHelper::existingIds($allIds)));
        $counts = Comment::getCounts(XN_Content::load($pizzaTopic->id));
        $this->assertEqual(1, $counts['commentCount']);
        $counts = Comment::getCounts(XN_Content::load($burgerTopic->id));
        $this->assertEqual(3, $counts['commentCount']);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
