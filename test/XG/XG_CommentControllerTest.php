<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_CommentController.php');

class XG_CommentControllerTest extends UnitTestCase {

    public function testShouldLogActivity() {
        $comment->my->approved = 'Y';
        $album->my->visibility = null;
        $this->assertTrue(TestCommentController::shouldLogActivity($comment, $album));
        $comment->my->approved = 'Y';
        $album->my->visibility = 'all';
        $this->assertTrue(TestCommentController::shouldLogActivity($comment, $album));
        $comment->my->approved = 'N';
        $album->my->visibility = 'all';
        $this->assertFalse(TestCommentController::shouldLogActivity($comment, $album));
        $comment->my->approved = 'Y';
        $album->my->visibility = 'friends';
        $this->assertFalse(TestCommentController::shouldLogActivity($comment, $album));
        $comment->my->approved = 'Y';
        $album->my->visibility = 'me';
        $this->assertFalse(TestCommentController::shouldLogActivity($comment, $album));
    }

}

abstract class TestCommentController extends XG_CommentController {
    public function shouldLogActivity($comment, $attachedTo) {
        return parent::shouldLogActivity($comment, $attachedTo);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


