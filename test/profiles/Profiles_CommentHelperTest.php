<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_CommentHelper.php');

class Profiles_CommentHelperTest extends UnitTestCase {

    public function testCanCurrentUserSeeAddCommentSection() {
        $blogPostOwner = XN_Content::create('Test');
        $blogPost = XN_Content::create('Test');

        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = 'friends';
        $this->assertEqual(true, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, 'me'));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = 'friends';
        $this->assertEqual(true, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, 'friend'));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = 'friends';
        $this->assertEqual(false, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, 'member'));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = 'friends';
        $this->assertEqual(false, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, null));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = null;
        $this->assertEqual(true, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, 'me'));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = null;
        $this->assertEqual(false, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, 'friend'));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = null;
        $this->assertEqual(false, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, 'member'));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = null;
        $this->assertEqual(false, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, null));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = 'all';
        $this->assertEqual(true, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, 'me'));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = 'all';
        $this->assertEqual(true, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, 'friend'));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = 'all';
        $this->assertEqual(true, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, 'member'));
        $blogPostOwner->my->addCommentPermission = 'me';
        $blogPost->my->addCommentPermission = 'all';
        $this->assertEqual(true, TestCommentHelper::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, null));
    }

    public function testUrlDoesNotContainBadAnchorId() {
        $comment = new StdClass();
        $comment->my = new StdClass();
        $comment->my->attachedTo = 'foobar';
        $comment->my->attachedToType = 'BlogPost';
        $comment->id = 'random_id:' . rand(1, 10);

        $url = TestCommentHelper::url($comment);
        $this->assertWantedPattern("/#comment-{$comment->id}$/", $url);
    }

}

class TestCommentHelper extends Profiles_CommentHelper {
    public static function canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, $relationship) {
        return parent::canCurrentUserSeeAddCommentSectionProper($blogPost, $blogPostOwner, $relationship);
    }

    /**
     * Stubbed out for tests against {@link Profiles_CommentHelper::url()}
     *
     * Could be set to return a value later if necessary
     */
    public static function page() {
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
