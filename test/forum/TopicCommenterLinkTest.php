<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');

class TopicCommenterLinkTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function test() {
        list(, $topic) = XG_TestHelper::createTopic();
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($topic->id)));
        list(, $comment1) = XG_TestHelper::createComment($topic);
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($topic->id)));
        list(, $comment2) = XG_TestHelper::createComment($topic);
        $this->assertEqual(1, count($links = TopicCommenterLink::linksForCurrentUser($topic->id)));
        $this->assertEqual($topic->id, $links[0]->my->topicId);
        $this->assertTrue($links[0]->isPrivate);
        XN_Content::delete(W_Content::unwrap($comment1));
        TopicCommenterLink::deleteLinkIfNecessary($topic->id);
        $this->assertEqual(1, count(TopicCommenterLink::linksForCurrentUser($topic->id)));
        XN_Content::delete(W_Content::unwrap($comment2));
        TopicCommenterLink::deleteLinkIfNecessary($topic->id);
        $this->assertEqual(0, count(TopicCommenterLink::linksForCurrentUser($topic->id)));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
