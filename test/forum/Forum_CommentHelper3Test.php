<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');

class Forum_CommentHelper3Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        $this->food = XN_Content::create('Food');
        $this->food->my->mozzle = 'forum';
        $this->food->save();
        Forum_CommentHelper::$testForceNewestPostsFirst = TRUE;
    }

    public function testLastCommentContributorName() {
        $firstComment = Forum_CommentHelper::createComment($this->food, 'abc', null);
        $firstCommentDate = date('c');
        sleep(2);
        $secondComment = Forum_CommentHelper::createComment($this->food, 'abc', null);
        $secondCommentDate = date('c');
        $this->food->save();
        $this->assertEqual(XN_Profile::current()->screenName, $this->food->my->lastCommentContributorName);

        Forum_CommentHelper::delete($secondComment);
        $this->food = XN_Content::load($this->food->id);
        $this->assertEqual(XN_Profile::current()->screenName, $this->food->my->lastCommentContributorName);
        $this->assertEqual($firstComment->createdDate, $this->food->my->lastCommentCreatedDate);

        Forum_CommentHelper::delete($firstComment);
        $this->food = XN_Content::load($this->food->id);
        $this->assertNull($this->food->my->lastCommentContributorName);
        $this->assertNull($this->food->my->lastCommentCreatedDate);
    }

    public function testCommentTimestamps() {
        $parentComment = $this->createComment($this->food, 'Burgers good', array(2, 7, 9));
        $childComment = $this->createComment($this->food, 'Indeed', array());
        $this->assertEqual('2,7,9,' . strtotime($childComment->createdDate), implode(',', Forum_CommentHelper::commentTimestamps($parentComment, $childComment)));
        $parentComment = NULL;
        $childComment = $this->createComment($this->food, 'Indeed', array());
        $this->assertEqual(strtotime($childComment->createdDate), implode(',', Forum_CommentHelper::commentTimestamps($parentComment, $childComment)));
    }

    public function testGetCommentTimestamps() {
        $comment = $this->createComment($this->food, 'Burgers good', array(2, 7, 9));
        $this->assertEqual('2,7,9', implode(',', Forum_CommentHelper::getCommentTimestamps($comment)));
        $this->assertEqual(2, Forum_CommentHelper::getAncestorCommentCount($comment));
        $comment = $this->createComment($this->food, 'Burgers good', array(6));
        $this->assertEqual('6', implode(',', Forum_CommentHelper::getCommentTimestamps($comment)));
        $this->assertEqual(0, Forum_CommentHelper::getAncestorCommentCount($comment));
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
