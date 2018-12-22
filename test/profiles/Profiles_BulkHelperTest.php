<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/BulkHelperTestCase.php');
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_BulkHelper.php');

class Profiles_BulkHelperTest extends BulkHelperTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('profiles');
    }

    public function testSetPrivacy() {
        $friendlyBlogPostId = $this->createBlogPost(false, 'all');
        $semifriendlyBlogPostId = $this->createBlogPost(true, 'all');
        $unfriendlyBlogPostId = $this->createBlogPost(true, 'friends');

        Profiles_BulkHelper::setPrivacy(30, true);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $this->checkPrivacy(true, array($friendlyBlogPostId, $semifriendlyBlogPostId, $unfriendlyBlogPostId));

        Profiles_BulkHelper::setPrivacy(30, false);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $friendlyBlogPost = XN_Content::load($friendlyBlogPostId);
        $semifriendlyBlogPost = XN_Content::load($semifriendlyBlogPostId);
        $unfriendlyBlogPost = XN_Content::load($unfriendlyBlogPostId);
        $this->assertEqual(false, $friendlyBlogPost->isPrivate);
        $this->assertEqual(false, $semifriendlyBlogPost->isPrivate);
        $this->assertEqual(true, $unfriendlyBlogPost->isPrivate);
    }

    /**
     * Create a dummy blog post, save it to the content store and return its id.
     *
     * @param   $isPrivate boolean  Should the BlogPost be private (true) or public (false).
     * @param   $visibility  string Visibility for the BlogPost (should be 'all, 'friends' or 'me').
     * @return  string              ID of the BlogPost created.
     */
    private function createBlogPost($isPrivate, $visibility) {
        $post = W_Content::create('BlogPost');
        Comment::initializeCounts($post);
        $post->my->mozzle = W_Cache::current('W_Widget')->dir;
        /* Set defaults which may be overridden in update */
        $post->my->publishStatus = 'publish';
        $post->my->publishTime = gmdate('Y-m-d\TH:i:s\Z');
        $post->my->publishWhen = 'now';
        $post->my->visibility = $visibility;
        $post->my->allowComments = 'Y';
        $post->isPrivate = $isPrivate;
        $post->title = 'Test';
        $post->description = 'test';
        $post->my->searchText = strip_tags($post->title) . ' ' . strip_tags($post->description);
        // $post->mood = ;
        $post->save();
        return $post->id;
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
