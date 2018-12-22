<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_FileHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_UserHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_BulkHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');

class Forum_BulkHelperTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testRemove() {
        list($pizzaTopicId, $pizzaTopic) = XG_TestHelper::createTopic();
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($pizzaTopicId)));
        list($pizzaAttachment1Id, $pizzaAttachment1) = XG_TestHelper::createAttachment($pizzaTopic);
        list($pizzaAttachment2Id, $pizzaAttachment2) = XG_TestHelper::createAttachment($pizzaTopic);
        list($pizzaComment1Id, $pizzaComment1) = XG_TestHelper::createComment($pizzaTopic);
        list($pizzaAttachment3Id, $pizzaAttachment3) = XG_TestHelper::createAttachment($pizzaComment1);
        list($pizzaAttachment4Id, $pizzaAttachment4) = XG_TestHelper::createAttachment($pizzaComment1);
        list($pizzaComment2Id, $pizzaComment2) = XG_TestHelper::createComment($pizzaTopic, $pizzaComment1);
        list($pizzaAttachment5Id, $pizzaAttachment5) = XG_TestHelper::createAttachment($pizzaComment2);
        list($pizzaAttachment6Id, $pizzaAttachment6) = XG_TestHelper::createAttachment($pizzaComment2);
        list($pizzaComment3Id, $pizzaComment3) = XG_TestHelper::createComment($pizzaTopic);
        list($pizzaAttachment7Id, $pizzaAttachment7) = XG_TestHelper::createAttachment($pizzaComment3);
        list($pizzaAttachment8Id, $pizzaAttachment8) = XG_TestHelper::createAttachment($pizzaComment3);
        list($burgerTopicId, $burgerTopic) = XG_TestHelper::createTopic();
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($burgerTopicId)));
        list($burgerAttachment1Id, $burgerAttachment1) = XG_TestHelper::createAttachment($burgerTopic);
        list($burgerAttachment2Id, $burgerAttachment2) = XG_TestHelper::createAttachment($burgerTopic);
        list($burgerComment1Id, $burgerComment1) = XG_TestHelper::createComment($burgerTopic);
        list($burgerAttachment3Id, $burgerAttachment3) = XG_TestHelper::createAttachment($burgerComment1);
        list($burgerAttachment4Id, $burgerAttachment4) = XG_TestHelper::createAttachment($burgerComment1);
        list($burgerComment2Id, $burgerComment2) = XG_TestHelper::createComment($burgerTopic, $burgerComment1);
        list($burgerAttachment5Id, $burgerAttachment5) = XG_TestHelper::createAttachment($burgerComment2);
        list($burgerAttachment6Id, $burgerAttachment6) = XG_TestHelper::createAttachment($burgerComment2);
        list($burgerComment3Id, $burgerComment3) = XG_TestHelper::createComment($burgerTopic);
        list($burgerAttachment7Id, $burgerAttachment7) = XG_TestHelper::createAttachment($burgerComment3);
        list($burgerAttachment8Id, $burgerAttachment8) = XG_TestHelper::createAttachment($burgerComment3);
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($pizzaTopicId)));
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($burgerTopicId)));
        $pizzaIds = array($pizzaTopicId, $pizzaAttachment1Id, $pizzaAttachment2Id, $pizzaComment1Id, $pizzaAttachment3Id, $pizzaAttachment4Id, $pizzaComment2Id, $pizzaAttachment5Id, $pizzaAttachment6Id, $pizzaComment3Id, $pizzaAttachment7Id, $pizzaAttachment8Id);
        $burgerIds = array($burgerTopicId, $burgerAttachment1Id, $burgerAttachment2Id, $burgerComment1Id, $burgerAttachment3Id, $burgerAttachment4Id, $burgerComment2Id, $burgerAttachment5Id, $burgerAttachment6Id, $burgerComment3Id, $burgerAttachment7Id, $burgerAttachment8Id);
        $allIds = array_merge($pizzaIds, $burgerIds);
        $this->assertEqual(implode(',', $allIds), implode(',', XG_TestHelper::existingIds($allIds)));
        $this->assertEqual('5,0', implode(',', Forum_BulkHelper::remove($pizzaTopic, 100)));
        $this->assertEqual(implode(',', $burgerIds), implode(',', XG_TestHelper::existingIds($allIds)));
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($pizzaTopicId)));
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($burgerTopicId)));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
