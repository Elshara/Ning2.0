<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_FileHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_UserHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_BulkHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');

class Forum_BulkHelper3Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testRemoveByUser() {
        list($pizzaTopicId, $pizzaTopic) = XG_TestHelper::createTopic();
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($pizzaTopicId)));
        list($pizzaAttachment1Id, $pizzaAttachment1) = XG_TestHelper::createAttachment($pizzaTopic);
        list($pizzaComment1Id, $pizzaComment1) = XG_TestHelper::createComment($pizzaTopic);
        list($pizzaAttachment2Id, $pizzaAttachment2) = XG_TestHelper::createAttachment($pizzaComment1);
        list($burgerTopicId, $burgerTopic) = XG_TestHelper::createTopic();
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($burgerTopicId)));
        list($burgerAttachment1Id, $burgerAttachment1) = XG_TestHelper::createAttachment($burgerTopic);
        list($burgerComment1Id, $burgerComment1) = XG_TestHelper::createComment($burgerTopic);
        list($burgerAttachment2Id, $burgerAttachment2) = XG_TestHelper::createAttachment($burgerComment1);
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($pizzaTopicId)));
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($burgerTopicId)));
        $pizzaIds = array($pizzaTopicId, $pizzaAttachment1Id, $pizzaComment1Id, $pizzaAttachment2Id);
        $burgerIds = array($burgerTopicId, $burgerAttachment1Id, $burgerComment1Id, $burgerAttachment2Id);
        $allIds = array_merge($pizzaIds, $burgerIds);
        $this->assertEqual(implode(',', $allIds), implode(',', XG_TestHelper::existingIds($allIds)));
        $this->assertEqual('0,0', implode(',', Forum_BulkHelper::removeByUser(100, 'FakeScreenName')));
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($pizzaTopicId)));
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($burgerTopicId)));
        $this->assertEqual(implode(',', $allIds), implode(',', XG_TestHelper::existingIds($allIds)));
        $this->assertEqual('6,0', implode(',', Forum_BulkHelper::removeByUser(100, XN_Profile::current()->screenName)));
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($pizzaTopicId)));
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($burgerTopicId)));
        $this->assertEqual('', implode(',', XG_TestHelper::existingIds($allIds)));
        foreach(XG_TestHelper::existingIds($allIds) as $id) { echo XN_Content::load($id)->debugHTML(); }
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
