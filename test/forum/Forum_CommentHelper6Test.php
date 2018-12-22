<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');

class Forum_CommentHelper6Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        $this->food = XN_Content::create('Food');
        $this->food->my->mozzle = 'forum';
        $this->food->save();
        Forum_CommentHelper::$testForceNewestPostsFirst = TRUE;
    }

    public function testCleanDescription() {
        $this->assertEqual('<b>Hello</b> <a href="http://google.com">http://google.com</a> world', Forum_CommentHelper::cleanDescription('<b>Hello</b> http://google.com world<script>'));
    }

    public function testPositionOfNewCommentWithNewestPostsFirst() {
        // Post [Jon Aquino 2007-02-23]
        $this->assertEqual('refreshPage', Forum_CommentHelper::positionOfNewComment(FALSE, FALSE, FALSE, TRUE));
        $this->assertEqual('refreshPage', Forum_CommentHelper::positionOfNewComment(FALSE, FALSE, TRUE, TRUE));
        $this->assertEqual('topOfPage', Forum_CommentHelper::positionOfNewComment(FALSE, TRUE, FALSE, TRUE));
        $this->assertEqual('topOfPage', Forum_CommentHelper::positionOfNewComment(FALSE, TRUE, TRUE, TRUE));
        // Reply [Jon Aquino 2007-02-23]
        $this->assertEqual('firstChild', Forum_CommentHelper::positionOfNewComment(TRUE, FALSE, FALSE, TRUE));
        $this->assertEqual('firstChild', Forum_CommentHelper::positionOfNewComment(TRUE, FALSE, TRUE, TRUE));
        $this->assertEqual('firstChild', Forum_CommentHelper::positionOfNewComment(TRUE, TRUE, FALSE, TRUE));
        $this->assertEqual('firstChild', Forum_CommentHelper::positionOfNewComment(TRUE, TRUE, TRUE, TRUE));
    }

    public function testPositionOfNewCommentWithOldestPostsFirst() {
        // Post [Jon Aquino 2007-02-23]
        $this->assertEqual('refreshPage', Forum_CommentHelper::positionOfNewComment(FALSE, FALSE, FALSE, FALSE));
        $this->assertEqual('bottomOfPage', Forum_CommentHelper::positionOfNewComment(FALSE, FALSE, TRUE, FALSE));
        $this->assertEqual('refreshPage', Forum_CommentHelper::positionOfNewComment(FALSE, TRUE, FALSE, FALSE));
        $this->assertEqual('bottomOfPage', Forum_CommentHelper::positionOfNewComment(FALSE, TRUE, TRUE, FALSE));
        // Reply [Jon Aquino 2007-02-23]
        $this->assertEqual('lastChild', Forum_CommentHelper::positionOfNewComment(TRUE, FALSE, FALSE, FALSE));
        $this->assertEqual('lastChild', Forum_CommentHelper::positionOfNewComment(TRUE, FALSE, TRUE, FALSE));
        $this->assertEqual('lastChild', Forum_CommentHelper::positionOfNewComment(TRUE, TRUE, FALSE, FALSE));
        $this->assertEqual('lastChild', Forum_CommentHelper::positionOfNewComment(TRUE, TRUE, TRUE, FALSE));
    }

    public function testDelete() {
        list($pizzaComment1Id, $pizzaComment1) = XG_TestHelper::createComment($this->food);
        list($pizzaComment2Id, $pizzaComment2) = XG_TestHelper::createComment($this->food, $pizzaComment1);
        list($pizzaComment3Id, $pizzaComment3) = XG_TestHelper::createComment($this->food, $pizzaComment2);
        $this->food->save();
        $this->assertEqual(implode(',', array($pizzaComment1Id, $pizzaComment2Id, $pizzaComment3Id)), implode(',', XG_TestHelper::existingIds(array($pizzaComment1Id, $pizzaComment2Id, $pizzaComment3Id))));
        $this->assertNotNull($pizzaComment1->description);
        $this->assertNull($pizzaComment1->my->xg_forum_deleted);
        $this->assertNull($pizzaComment2->my->xg_forum_deleted);
        $this->assertNull($pizzaComment3->my->xg_forum_deleted);
        $result = Forum_CommentHelper::delete($pizzaComment1);
        $this->assertFalse($result);
        $this->assertEqual(implode(',', array($pizzaComment1Id, $pizzaComment2Id, $pizzaComment3Id)), implode(',', XG_TestHelper::existingIds(array($pizzaComment1Id, $pizzaComment2Id, $pizzaComment3Id))));
        $this->assertEqual(XN_Profile::current()->screenName, $this->food->my->lastCommentContributorName);
        $this->assertNull($pizzaComment1->description);
        $this->assertEqual('Y', $pizzaComment1->my->xg_forum_deleted);
        $this->assertNull($pizzaComment2->my->xg_forum_deleted);
        $this->assertNull($pizzaComment3->my->xg_forum_deleted);
        $result = Forum_CommentHelper::delete($pizzaComment3);
        $this->assertTrue($result);
        $this->assertEqual(implode(',', array($pizzaComment1Id, $pizzaComment2Id)), implode(',', XG_TestHelper::existingIds(array($pizzaComment1Id, $pizzaComment2Id, $pizzaComment3Id))));
        $this->assertEqual(XN_Profile::current()->screenName, $this->food->my->lastCommentContributorName);
        $this->assertEqual('Y', $pizzaComment1->my->xg_forum_deleted);
        $this->assertNull($pizzaComment2->my->xg_forum_deleted);
        $result = Forum_CommentHelper::delete($pizzaComment2);
        $this->assertTrue($result);
        $this->assertEqual('', implode(',', XG_TestHelper::existingIds(array($pizzaComment1Id, $pizzaComment2Id, $pizzaComment3Id))));
        $this->assertNull($this->food->my->lastCommentContributorName);
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
