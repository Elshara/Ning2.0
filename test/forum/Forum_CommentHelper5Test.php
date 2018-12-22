<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');

class Forum_CommentHelper5Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        $this->food = XN_Content::create('Food');
        $this->food->my->mozzle = 'forum';
        $this->food->save();
        Forum_CommentHelper::$testForceNewestPostsFirst = TRUE;
    }

    public function testCommentIdsWithChildComments() {
        list(, $comment1) = XG_TestHelper::createComment($this->food);
        sleep(1);
        list(, $comment2) = XG_TestHelper::createComment($this->food, $comment1);
        sleep(1);
        list(, $comment3) = XG_TestHelper::createComment($this->food, $comment2);
        sleep(1);
        list(, $comment4) = XG_TestHelper::createComment($this->food);
        $this->assertEqual(
                serialize(array($comment1->id => $comment1->id)),
                serialize(Forum_CommentHelper::commentIdsWithChildComments(1, array($comment1, $comment2, $comment3, $comment4))));
        $this->assertEqual(
                serialize(array($comment1->id => $comment1->id, $comment2->id => $comment2->id)),
                serialize(Forum_CommentHelper::commentIdsWithChildComments(2, array($comment1, $comment2, $comment3, $comment4))));
        $this->assertEqual(
                serialize(array($comment1->id => $comment1->id, $comment2->id => $comment2->id)),
                serialize(Forum_CommentHelper::commentIdsWithChildComments(3, array($comment1, $comment2, $comment3, $comment4))));
        $this->assertEqual(
                serialize(array($comment1->id => $comment1->id, $comment2->id => $comment2->id)),
                serialize(Forum_CommentHelper::commentIdsWithChildComments(4, array($comment1, $comment2, $comment3, $comment4))));
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
