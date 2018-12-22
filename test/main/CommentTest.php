<?php
require $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/models/Comment.php');

class CommentTest extends UnitTestCase {

    public function testGetCounts() {
        call_user_func(array(W_Cache::getClass('app'),'loadWidgets'));
        $widget = W_Cache::getWidget('forum');
        W_Cache::push($widget);
        $food = XN_Content::create('Food');
        $food->my->mozzle = 'forum';
        $food->save();
        Comment::createAndAttachTo($food, 'Yum!')->save();
        Comment::createAndAttachTo($food, 'Tasty!')->save();
        $counts = Comment::getCounts($food);
        $this->assertEqual(2, $counts['commentCount']);
        // Topics are sorted by commentCount, which must therefore be an XN_Attribute::NUMBER  [Jon Aquino 2007-01-23]
        $this->assertEqual(XN_Attribute::NUMBER, $food->my->attribute(XG_App::widgetAttributeName($widget, 'commentCount'))->type);
    }

    public function testGetCounts2() {
        $food = XN_Content::create('Food');
        $food->my->mozzle = 'forum';
        $food->my->xg_forum_commentCount = 2;
        $food->my->xg_forum_commentToApproveCount = 3;
        $counts = Comment::getCounts($food);
        $this->assertEqual(2, $counts['commentCount']);
        $this->assertEqual(3, $counts['commentToApproveCount']);
        $this->assertEqual(0, $counts['approvedCommentCount']);
    }

    public function testGetCounts3() {
        $food = XN_Content::create('Food');
        $food->my->mozzle = 'forum';
        $food->my->xg_forum_commentCount = 5;
        $food->my->xg_forum_commentToApproveCount = 3;
        $counts = Comment::getCounts($food);
        $this->assertEqual(5, $counts['commentCount']);
        $this->assertEqual(3, $counts['commentToApproveCount']);
        $this->assertEqual(2, $counts['approvedCommentCount']);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
