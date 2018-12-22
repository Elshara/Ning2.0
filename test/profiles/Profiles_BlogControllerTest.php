<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/controllers/BlogController.php');
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_BlogArchiveHelper.php');
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_BlogListHelper.php');
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_UserHelper.php');
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_FeedHelper.php');
XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
Mock::generate('Profiles_BlogListHelper');
Mock::generate('XN_Query');
Mock::generate('stdClass', 'MockXN_Content', array('save'));
Mock::generate('stdClass', 'MockXN_AttributeContainer', array('raw'));
Mock::generate('stdClass', 'MockXN_Profile', array('isLoggedIn'));

class Profiles_BlogControllerTest extends UnitTestCase {

    private $_originalGet = null;
    public function setUp() {
        parent::setUp();
        $this->_originalGet = $_GET;
        $_GET = array();
    }

    public function tearDown() {
        $_GET = $this->_originalGet;
        unset($this->_originalGet);
    }
    public function testParseBlogPostFormSubmission() {
        $data = TestBlogController::parseBlogPostFormSubmission(array(
            'post_add_comment_permission' => 'friends'
        ));
        $this->assertEqual('friends', $data['addCommentPermission']);
        $post = XN_Content::create('Test');
        BlogPost::update($post, $data);
        $this->assertEqual('friends', $post->my->addCommentPermission);
    }

    public function testFormDefaults() {
        $postOwner = XN_Content::create('Test');
        $postOwner->my->addCommentPermission = 'me';
        $post = XN_Content::create('Test');
        $post->my->publishStatus = 'publish';
        $post->my->publishWhen = 'later';
        $post->my->publishTime = '1977-02-15T15:19:21-08:00';
        $post->my->visibility = 'friends';
        $post->my->allowComments = 'Y';
        $post->title = 'Blue Sky Mining';
        $post->my->mood = 'Happy';
        $post->description = 'A <b>great</b> album.
Foo.';
        list($hour,$min) = explode(',',xg_date('G,i', strtotime($post->my->publishTime)));
        $defaults = TestBlogController::formDefaults($post, $postOwner);
        $this->assertEqual('later', $defaults['post_when']);
        $this->assertEqual(2, $defaults['post_month']);
        $this->assertEqual(15, $defaults['post_day']);
        $this->assertEqual(1977, $defaults['post_year']);
        $this->assertEqual('PM', $defaults['post_ampm']);
        $this->assertEqual($hour-12, $defaults['post_hour']);
        $this->assertEqual(0, $defaults['post_min']);
        $this->assertEqual('friends', $defaults['post_privacy']);
        $this->assertEqual('me', $defaults['post_add_comment_permission']);
        $this->assertEqual('Blue Sky Mining', $defaults['post_title']);
        $this->assertEqual('Happy', $defaults['post_mood']);
        $this->assertEqual('A <b>great</b> album. Foo.', $defaults['post_body']);
    }

    public function testFormDefaults2() {
        $postOwner = XN_Content::create('Test');
        $post = XN_Content::create('Test');

        $postOwner->my->addCommentPermission = 'me';
        $post->my->addCommentPermission = null;
        $post->my->allowComments = null;
        $defaults = TestBlogController::formDefaults($post, $postOwner);
        $this->assertEqual('me', $defaults['post_add_comment_permission']);

        $postOwner->my->addCommentPermission = 'all';
        $post->my->addCommentPermission = null;
        $post->my->allowComments = null;
        $defaults = TestBlogController::formDefaults($post, $postOwner);
        $this->assertEqual('all', $defaults['post_add_comment_permission']);

        $postOwner->my->addCommentPermission = 'me';
        $post->my->addCommentPermission = 'friends';
        $post->my->allowComments = null;
        $defaults = TestBlogController::formDefaults($post, $postOwner);
        $this->assertEqual('friends', $defaults['post_add_comment_permission']);

        $postOwner->my->addCommentPermission = 'me';
        $post->my->addCommentPermission = null;
        $post->my->allowComments = 'Y';
        $defaults = TestBlogController::formDefaults($post, $postOwner);
        $this->assertEqual('me', $defaults['post_add_comment_permission']);

        $postOwner->my->addCommentPermission = 'me';
        $post->my->addCommentPermission = 'friends';
        $post->my->allowComments = 'Y';
        $defaults = TestBlogController::formDefaults($post, $postOwner);
        $this->assertEqual('friends', $defaults['post_add_comment_permission']);

        $postOwner->my->addCommentPermission = 'me';
        $post->my->addCommentPermission = null;
        $post->my->allowComments = 'N';
        $defaults = TestBlogController::formDefaults($post, $postOwner);
        $this->assertEqual('me', $defaults['post_add_comment_permission']);

        $postOwner->my->addCommentPermission = 'me';
        $post->my->addCommentPermission = 'friends';
        $post->my->allowComments = 'N';
        $defaults = TestBlogController::formDefaults($post, $postOwner);
        $this->assertEqual('friends', $defaults['post_add_comment_permission']);

        $postOwner->my->addCommentPermission = 'all';
        $post->my->addCommentPermission = 'friends';
        $post->my->allowComments = 'N';
        $defaults = TestBlogController::formDefaults($post, $postOwner);
        $this->assertEqual('friends', $defaults['post_add_comment_permission']);
    }

    public function testPrepareListAction1() {
        $this->doTestPrepareListAction(array(
                'getUser' => null,
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => null,
                'featuredPosts' => true,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for all posts]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Nobody has written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => NULL,
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'All Blog Posts',
                'expectedUser' => NULL,
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction2() {
        $this->doTestPrepareListAction(array(
                'getUser' => null,
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => 'jane',
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for all posts]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Nobody has written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => NULL,
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'All Blog Posts',
                'expectedUser' => NULL,
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction3() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => null,
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?user=jane&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction3b() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => null,
                'featuredPosts' => true,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?user=jane&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction4() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => 'don',
                'featuredPosts' => false,
                'friendStatus' => 'not-friend',
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?user=jane&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction5() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => 'don',
                'featuredPosts' => false,
                'friendStatus' => 'friend',
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for user: friends]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?user=jane&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction6() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => 'jane',
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft')),
                'expectedPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft')),

                'expectedArchive' => '[archive for user: me]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?user=jane&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'You haven\'t written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'My Blog',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => true,
        ));
    }

    public function testPrepareListAction7() {
        $this->doTestPrepareListAction(array(
                'getUser' => null,
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => null,
                'featuredPosts' => true,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for all posts]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?tag=cool&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Nobody has written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => NULL,
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'All Blog Posts Tagged <em>\'cool\'</em>',
                'expectedUser' => NULL,
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction7b() {
        $this->doTestPrepareListAction(array(
                'getUser' => null,
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => null,
                'featuredPosts' => true,
                'friendStatus' => null,
                'numPosts' => 0,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for all posts]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?tag=cool&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Nobody has written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => NULL,
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'listEmpty',
                'expectedTitleHtml' => 'All Blog Posts Tagged <em>\'cool\'</em>',
                'expectedUser' => NULL,
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction8() {
        $this->doTestPrepareListAction(array(
                'getUser' => null,
                'getTag' => null,
                'getPromoted' => 1,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => null,
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('promoted' => true, 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('promoted' => true, 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for featured posts]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?promoted=1&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Featured Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Featured',
                'expectedMonthlyArchivesTitle' => 'Featured Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Featured Blog Posts',
                'expectedNoPostsMessage' => 'No blog posts have been featured yet. ',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedOrder' => 'my->xg_main_promotedOn',
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => NULL,
                'expectedStart' => 0,
                'expectedTags' => NULL,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Featured Blog Posts',
                'expectedUser' => NULL,
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction9() {
        $this->doTestPrepareListAction(array(
                'getUser' => null,
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => 77,
                'getMonth' => 2,
                '_userScreenName' => null,
                'featuredPosts' => true,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for all posts]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Nobody has written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => NULL,
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'February 77 Blog Posts',
                'expectedUser' => NULL,
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction10() {
        $this->doTestPrepareListAction(array(
                'getUser' => null,
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => 'jane',
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for all posts]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Nobody has written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => NULL,
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'February 1977 Blog Posts',
                'expectedUser' => NULL,
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction11() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => null,
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?user=jane&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog – February 1977 Archive',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction12() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => 'don',
                'featuredPosts' => false,
                'friendStatus' => 'not-friend',
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?user=jane&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog – February 1977 Archive',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction13() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => 'don',
                'featuredPosts' => false,
                'friendStatus' => 'friend',
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for user: friends]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?user=jane&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog – February 1977 Archive',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction14() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => null,
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => 'jane',
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft')),
                'expectedPostsFilters' => array('contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft'), 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for user: me]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?user=jane&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'You haven\'t written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => '[tags]',
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'My Blog – February 1977 Archive',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => true,
        ));
    }

    public function testPrepareListAction15() {
        $this->doTestPrepareListAction(array(
                'getUser' => null,
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => null,
                'featuredPosts' => true,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for all posts]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?tag=cool&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Nobody has written any blog posts yet.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => NULL,
                'expectedStart' => 0,
                'expectedTags' => NULL,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'All Blog Posts Tagged <em>\'cool\'</em> – February 1977 Archive',
                'expectedUser' => NULL,
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction16() {
        $this->doTestPrepareListAction(array(
                'getUser' => null,
                'getTag' => null,
                'getPromoted' => 1,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => null,
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('promoted' => true, 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('promoted' => true, 'my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for featured posts]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedFeedUrl' => '/profiles/blog/feed?promoted=1&xn_auth=no',
                'expectedLatestBlogPostsTitle' => 'Latest Featured Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Featured',
                'expectedMonthlyArchivesTitle' => 'Featured Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Featured Blog Posts',
                'expectedNoPostsMessage' => 'No blog posts have been featured yet. ',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => NULL,
                'expectedStart' => 0,
                'expectedTags' => NULL,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Featured Blog Posts – February 1977 Archive',
                'expectedUser' => NULL,
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction17() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => null,
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog Posts Tagged <em>\'cool\'</em>',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction17b() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => null,
                'featuredPosts' => true,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog Posts Tagged <em>\'cool\'</em>',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction18() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => 'don',
                'featuredPosts' => false,
                'friendStatus' => 'not-friend',
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog Posts Tagged <em>\'cool\'</em>',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction19() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => 'don',
                'featuredPosts' => false,
                'friendStatus' => 'friend',
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array('<=', gmdate('Y-m-d\TH:i:s\Z'), 'date')),

                'expectedArchive' => '[archive for user: friends]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog Posts Tagged <em>\'cool\'</em>',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction20() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => null,
                'getMonth' => null,
                '_userScreenName' => 'jane',
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft')),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft')),

                'expectedArchive' => '[archive for user: me]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'You haven\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'My Blog Posts Tagged <em>\'cool\'</em>',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => true,
        ));
    }

    public function testPrepareListAction21() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => null,
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog Posts Tagged <em>\'cool\'</em> – February 1977 Archive',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction22() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => 'don',
                'featuredPosts' => false,
                'friendStatus' => 'not-friend',
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for user: all]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog Posts Tagged <em>\'cool\'</em> – February 1977 Archive',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction23() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => 'don',
                'featuredPosts' => false,
                'friendStatus' => 'friend',
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish'),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => 'publish', 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for user: friends]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'Jane Smith hasn\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => NULL,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'Jane Smith\'s Blog Posts Tagged <em>\'cool\'</em> – February 1977 Archive',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => false,
        ));
    }

    public function testPrepareListAction24() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => 'jane',
                'featuredPosts' => false,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft')),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft'), 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for user: me]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'You haven\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'My Blog Posts Tagged <em>\'cool\'</em> – February 1977 Archive',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => true,
        ));
    }

    public function testPrepareListAction25() {
        $this->doTestPrepareListAction(array(
                'getUser' => 'jane',
                'getTag' => 'cool',
                'getPromoted' => null,
                'getYear' => 1977,
                'getMonth' => 2,
                '_userScreenName' => 'jane',
                'featuredPosts' => true,
                'friendStatus' => null,
                'numPosts' => 10,

                'expectedRecentPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft')),
                'expectedPostsFilters' => array('tag->value' => array('eic', 'cool'), 'contributorName' => 'jane', 'my->publishStatus' => array('<>', 'draft'), 'my->publishTime' => array(array('>=', '1977-02-01T08:00:00Z', 'date'), array('<=', '1977-03-01T07:59:59Z', 'date'))),

                'expectedArchive' => '[archive for user: me]',
                'expectedEnd' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedLatestBlogPostsTitle' => 'Latest Blog Posts',
                'expectedMetaDescription' => 'Blog This is my test network Jane Smith cool',
                'expectedMonthlyArchivesTitle' => 'Monthly Archives',
                'expectedMostPopularBlogPostsTitle' => 'Most Popular Blog Posts',
                'expectedNoPostsMessage' => 'You haven\'t written any blog posts tagged \'cool\'.',
                'expectedNoPostsMessageHasAddLink' => true,
                'expectedPageSize' => Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE,
                'expectedProfileScreenName' => 'jane',
                'expectedStart' => 0,
                'expectedTags' => null,
                'expectedTemplateName' => 'list',
                'expectedTitleHtml' => 'My Blog Posts Tagged <em>\'cool\'</em> – February 1977 Archive',
                'expectedUser' => 'jane',
                'expectedUserIsOwner' => true,
        ));
    }

    // @todo we need to figure out how to shorten this - the length of the test can
    //       result in false positives as seconds tick by [Travis S. 2008-09-26]
    private function doTestPrepareListAction($args) {
        $_GET['user'] = $args['getUser'];
        $_GET['tag'] = $args['getTag'];
        $_GET['promoted'] = $args['getPromoted'];
        $_GET['year'] = $args['getYear'];
        $_GET['month'] = $args['getMonth'];
        $controller = new TestBlogController();
        $controller->_user = new MockXN_Profile();
        $controller->_user->screenName = $args['_userScreenName'];
        $controller->_user->setReturnValue('isLoggedIn', mb_strlen($args['_userScreenName']) > 0);
        $helper = new MockProfiles_BlogListHelper();
        $tagQuery = new MockXN_Query();
        $tagQuery->setReturnValue('filter', $tagQuery);
        $tagQuery->setReturnValue('end', $tagQuery);
        $tagQuery->setReturnValue('execute', '[tags]');
        if (mb_strlen($args['getUser']) && ! mb_strlen($args['getTag'])) {
            $tagQuery->expectCallCount('filter', 2);
            $tagQuery->expectAt(0, 'filter', array('content->type', '=', 'BlogPost'));
            $tagQuery->expectAt(1, 'filter', array('ownerName','=', $args['getUser']));
        } elseif ($args['expectedTags']) {
            $tagQuery->expectCallCount('filter', 1);
        } else {
            $tagQuery->expectCallCount('filter', 0);
        }
        $helper->expectAt(0, 'findBlogPosts', array($args['expectedRecentPostsFilters'], 0, 7));
        $helper->setReturnValueAt(0, 'findBlogPosts', '[recent posts]');
        $helper->expectAt(1, 'findBlogPosts', array(array_merge($args['expectedRecentPostsFilters'], array('my.popularityCount' => array('<>', null))), 0, 7, 'my.popularityCount', 'desc'));
        $helper->setReturnValueAt(1, 'findBlogPosts', '[popular posts]');
        $showFeaturedBlock = !isset($_GET['user']) && !isset($_GET['tag']) && !isset($_GET['q']) && !isset($_GET['promoted']);
        if ($showFeaturedBlock) {
            $helper->setReturnValueAt(2, 'findBlogPosts', $args['featuredPosts'] ? array('numPosts' => 1) : array('numPosts' => 0));
        }
        $i = $showFeaturedBlock ? 3 : 2;
        $expectedOrder = $args['expectedOrder'] ? $args['expectedOrder'] : 'my->publishTime';
        $helper->expectAt($i, 'findBlogPosts', array($args['expectedPostsFilters'], 0, Profiles_BlogController::DEFAULT_BLOG_PAGE_SIZE, $expectedOrder, 'desc'));
        $helper->setReturnValueAt($i, 'findBlogPosts', array('numPosts' => $args['numPosts']));
        $helper->setReturnValue('createXnQuery', $tagQuery);
        $user = new MockXN_Content();
        $user->title = $_GET['user'];
        $user->my = new MockXN_AttributeContainer();
        $helper->setReturnValue('loadUser', $user);
        $profile = new MockXN_Profile();
        $profile->screenName = $args['getUser'];
        $profile->setReturnValue('isLoggedIn', mb_strlen($args['getUser']) > 0);
        $helper->setReturnValue('loadProfile', $profile);
        $helper->setReturnValue('username', 'Jane Smith');
        $helper->setReturnValue('getNetworkName', 'My Test Network');
        $helper->setReturnValue('getNetworkDescription', 'This is my test network');
        $helper->setReturnValue('getArchiveForUser', array('me' => '[archive for user: me]', 'friends' => '[archive for user: friends]', 'all' => '[archive for user: all]'));
        $helper->setReturnValue('getArchiveForAllPosts', '[archive for all posts]');
        $helper->setReturnValue('getArchiveForFeaturedPosts', '[archive for featured posts]');
        $helper->setReturnValue('getFriendStatusFor', $args['friendStatus']);
        $controller->prepareListAction($helper);
        if ($args['getUser']) { $this->assertEqual($args['getUser'], $controller->user->title); }
        $this->assertEqual('[recent posts]', $controller->recentPosts);
        $this->assertEqual('[popular posts]', $controller->popularPosts);
        $this->assertEqual(array('numPosts' => $args['numPosts']), $controller->posts);

        $this->assertEqual($args['expectedArchive'], $controller->archive);
        $this->assertEqual($args['expectedEnd'], $controller->end);
        $this->assertEqual($args['expectedFeedUrl'], preg_replace('@.*php@u', '', $controller->feedUrl));
        $this->assertEqual($args['expectedLatestBlogPostsTitle'], $controller->latestBlogPostsTitle);
        $this->assertEqual($args['expectedMetaDescription'], $controller->metaDescription);
        $this->assertEqual($args['expectedMonthlyArchivesTitle'], $controller->monthlyArchivesTitle);
        $this->assertEqual($args['expectedMostPopularBlogPostsTitle'], $controller->mostPopularBlogPostsTitle);
        $this->assertEqual($args['expectedNoPostsMessage'], $controller->noPostsMessage);
        $this->assertEqual($args['expectedNoPostsMessageHasAddLink'], $controller->noPostsMessageHasAddLink);
        $this->assertEqual($args['expectedPageSize'], $controller->pageSize);
        $this->assertEqual($args['expectedProfileScreenName'], $controller->profile->screenName);
        $this->assertEqual($args['expectedStart'], $controller->start);
        $this->assertEqual($args['expectedTags'], $controller->tags);
        $this->assertEqual($args['expectedTemplateName'], $controller->templateName);
        $this->assertEqual($args['expectedTitleHtml'], $controller->titleHtml);
        $this->assertEqual($args['expectedUser'], $controller->user->title);
        $this->assertEqual($args['expectedUserIsOwner'], $controller->userIsOwner);
        $debugOutput = '<pre>' . xnhtmlentities(var_export(array(
                'expectedArchive' => $controller->archive,
                'expectedEnd' => $controller->end,
                'expectedLatestBlogPostsTitle' => $controller->latestBlogPostsTitle,
                'expectedMetaDescription' => $controller->metaDescription,
                'expectedMonthlyArchivesTitle' => $controller->monthlyArchivesTitle,
                'expectedMostPopularBlogPostsTitle' => $controller->mostPopularBlogPostsTitle,
                'expectedNoPostsMessage' => $controller->noPostsMessage,
                'expectedNoPostsMessageHasAddLink' => $controller->noPostsMessageHasAddLink,
                'expectedPageSize' => $controller->pageSize,
                'expectedProfileScreenName' => $controller->profile->screenName,
                'expectedStart' => $controller->start,
                'expectedTags' => $controller->tags,
                'expectedTemplateName' => $controller->templateName,
                'expectedTitleHtml' => $controller->titleHtml,
                'expectedUser' => $controller->user->title,
                'expectedUserIsOwner' => $controller->userIsOwner), true)) . '</pre>';
        // echo $debugOutput;
    }

    public function testBaz6619() {
        $contents = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/widgets/profiles/templates/blog/new.php');
        preg_match_all('@\$this->form->[a-z]+\(\'([^\']+)@ui', $contents, $matches);
        foreach(array_diff($matches[1], TestBlogController::getFormVariables()) as $formVariable) {
            $this->fail('Missing from BlogController::$formVariables: \'' . $formVariable . '\'');
        }
    }

    public function testHelpersCompile() {
        XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_BlogListModeForTagAndUser.php');
        XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_BlogListModeForUser.php');
        XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_BlogListModeForTag.php');
        XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_BlogListModeForFeatured.php');
        XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_BlogListModeForAll.php');
    }

}

class TestBlogController extends Profiles_BlogController {
    public function __construct() {
        return parent::__construct(W_Cache::getWidget('profiles'));
    }
    public static function formDefaults($post, $postOwner) {
        return parent::formDefaults($post, $postOwner);
    }
    public static function parseBlogPostFormSubmission($submittedData) {
        return parent::parseBlogPostFormSubmission($submittedData);
    }
    public function prepareListAction($helper) {
        return parent::prepareListAction($helper);
    }
    public function getFormVariables() {
        return parent::$formVariables;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

