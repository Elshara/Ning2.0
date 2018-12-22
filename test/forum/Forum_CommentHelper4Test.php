<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');

class Forum_CommentHelper4Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        $this->food = XN_Content::create('Food');
        $this->food->my->mozzle = 'forum';
        $this->food->save();
        Forum_CommentHelper::$testForceNewestPostsFirst = TRUE;
    }

    public function testCreateQuery() {
        $this->createComment($this->food, 'I like sushi', array(1));
        $this->createComment($this->food, 'Burgers are best', array(2));
        $this->createComment($this->food, 'Can\'t beat pizza', array(3));
        $this->createComment($this->food, 'Yeah, pizza is good', array(3, 4));
        $this->createComment($this->food, 'Pizza rules!', array(3, 4, 5));
        $this->createComment($this->food, 'Sushiya!', array(1, 6));
        $this->createComment($this->food, 'Burger King is OK', array(2, 7));
        $this->createComment($this->food, 'Sushi is great', array(1, 6, 8));
        $this->createComment($this->food, 'Burgers good', array(2, 7, 9));
        $comments = Forum_CommentHelper::createQuery($this->food->id, 0, 10)->execute();
        $commentString = '';
        foreach($comments as $comment) {
            $commentString .= $comment->description . ',';
        }
        $expectedResult =
                'Can\'t beat pizza,' .
                'Yeah, pizza is good,' .
                'Pizza rules!,' .
                'Burgers are best,' .
                'Burger King is OK,' .
                'Burgers good,' .
                'I like sushi,' .
                'Sushiya!,' .
                'Sushi is great,';
        $this->assertEqual($expectedResult, $commentString);
        if ($expectedResult != $commentString) {
            foreach ($comments as $comment) {
                echo $comment->debugHTML();
            }
        }
        $comments = Forum_CommentHelper::createQuery($this->food->id, 0, 10, 'mostRecent')->execute();
        $commentString = '';
        foreach($comments as $comment) {
            $commentString .= $comment->description . ',';
        }
        $expectedResult =
                'Burgers good,' .
                'Sushi is great,' .
                'Burger King is OK,' .
                'Sushiya!,' .
                'Pizza rules!,' .
                'Yeah, pizza is good,' .
                'Can\'t beat pizza,' .
                'Burgers are best,' .
                'I like sushi,';
        $this->assertEqual($expectedResult, $commentString);
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
