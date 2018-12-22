<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');

class Forum_CommentHelper2Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testUpdateLastEntry() {
        $food = XN_Content::create('Food');
        $food->my->mozzle = 'forum';
        $food->save();
        list(, $comment1) = XG_TestHelper::createComment($food);
        usleep(250000);
        list(, $comment2) = XG_TestHelper::createComment($food);
        Forum_CommentHelper::updateLastEntry($food);
        $this->assertEqual($comment2->createdDate, $food->my->lastEntryDate);
    }

    public function testUpdateLastEntry2() {
        $food = XN_Content::create('Food');
        $food->my->mozzle = 'forum';
        $food->save();
        list(, $comment1) = XG_TestHelper::createComment($food);
        usleep(250000);
        list(, $comment2) = XG_TestHelper::createComment($food);
        Forum_CommentHelper::updateLastEntry($food, true);
        $this->assertEqual($comment1->createdDate, $food->my->lastEntryDate);
    }

    public function testUpdateLastEntry3() {
        $food = XN_Content::create('Food');
        $food->my->mozzle = 'forum';
        $food->save();
        list(, $comment1) = XG_TestHelper::createComment($food);
        Forum_CommentHelper::updateLastEntry($food);
        $this->assertEqual($comment1->createdDate, $food->my->lastEntryDate);
    }

    public function testUpdateLastEntry4() {
        $food = XN_Content::create('Food');
        $food->my->mozzle = 'forum';
        $food->save();
        list(, $comment1) = XG_TestHelper::createComment($food);
        Forum_CommentHelper::updateLastEntry($food, true);
        $this->assertEqual($food->createdDate, $food->my->lastEntryDate);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
