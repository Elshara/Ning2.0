<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');

class Forum_CommentHelperTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        $this->food = XN_Content::create('Food');
        $this->food->my->mozzle = 'forum';
        $this->food->save();
        Forum_CommentHelper::$testForceNewestPostsFirst = TRUE;
    }

    public function testIds() {
        list(, $comment1) = XG_TestHelper::createComment($this->food);
        list(, $comment2) = XG_TestHelper::createComment($this->food);
        $this->assertEqual(serialize(array($comment1->id, $comment2->id)), serialize(Forum_CommentHelper::ids(array($comment1, $comment2))));
    }

    public function testPage() {
        list(, $pizzaTopic) = XG_TestHelper::createTopic();
        list(, $pizzaComment1) = XG_TestHelper::createComment($pizzaTopic);
        sleep(1);
        list(, $pizzaComment2) = XG_TestHelper::createComment($pizzaTopic);
        sleep(1);
        list(, $pizzaComment3) = XG_TestHelper::createComment($pizzaTopic);
        sleep(1);
        list(, $pizzaComment4) = XG_TestHelper::createComment($pizzaTopic);
        $this->assertEqual(3, Forum_CommentHelper::page($pizzaComment2, 1));
        $this->assertEqual(2, Forum_CommentHelper::page($pizzaComment2, 2));
        $this->assertEqual(1, Forum_CommentHelper::page($pizzaComment2, 3));
        $this->assertEqual(1, Forum_CommentHelper::page($pizzaComment2, 4));
    }

    public function testMaxEmbedWidth() {
        $this->assertEqual(646, Forum_CommentHelper::maxEmbedWidth(0));
        $this->assertEqual(616, Forum_CommentHelper::maxEmbedWidth(1));
        $this->assertEqual(586, Forum_CommentHelper::maxEmbedWidth(2));
        $this->assertEqual(556, Forum_CommentHelper::maxEmbedWidth(3));
        $this->assertEqual(526, Forum_CommentHelper::maxEmbedWidth(4));
        $this->assertEqual(496, Forum_CommentHelper::maxEmbedWidth(5));
        $this->assertEqual(466, Forum_CommentHelper::maxEmbedWidth(6));
        $this->assertEqual(436, Forum_CommentHelper::maxEmbedWidth(7));
        $this->assertEqual(406, Forum_CommentHelper::maxEmbedWidth(8));
        $this->assertEqual(376, Forum_CommentHelper::maxEmbedWidth(9));
        $this->assertEqual(0, Forum_CommentHelper::maxEmbedWidth(1000));
    }

    private function createComment($object, $text, $commentTimestamps) {
        $comment = Comment::createAndAttachTo($object, $text);
        Forum_CommentHelper::setCommentTimestamps($comment, $commentTimestamps);
        $comment->save();
        return $comment;
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
