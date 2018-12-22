<?php

W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_BlogListMode.php');

/**
 * Logic for listing blog posts for a given tag and user.
 */
class Profiles_BlogListModeForTagAndUser extends Profiles_BlogListMode {

    /** The User object for the specified screenName. */
    private $user;

    /** The XN_Profile for the specified screenName. */
    private $profile;

    /** Whether the current user is viewing her own blog posts. */
    private $myBlogPosts;

    /** XN_Profile for the current user */
    private $currentProfile;

    /**
     * Creates the mode object.
     *
     * @param $helper Profiles_BlogListHelper  facade for services used by the list action
     * @param $currentProfile XN_Profile  profile for the current user
     * @override
     */
    public function __construct($helper, $currentProfile) {
        parent::__construct($helper);
        $this->user = $helper->loadUser($_GET['user']);
        $this->profile = $helper->loadProfile($_GET['user']);
        $this->myBlogPosts = $this->profile->screenName == $currentProfile->screenName;
        $this->currentProfile = $currentProfile;
    }

    /**
     * Returns tags to display in the Blog Topics By Tags box.
     *
     * @return array  a mapping of tag name to blog-post count.
     */
    public function getTags() {
        return array();
    }

    /**
     * Returns the User object for the current screenName.
     *
     * @return W_Content  the User object
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Returns the XN_Profile for the current screenName.
     *
     * @return XN_Profile  the profile
     */
    public function getProfile() {
        return $this->profile;
    }

    /**
     * Returns filters for the BlogPost query
     *
     * @return array  filters for use with BlogPost::find()
     */
    public function getFilters() {
        return array('tag->value' => array('eic', $_GET['tag']), 'contributorName' => $_GET['user'], 'my->publishStatus' => $this->myBlogPosts ? array('<>', 'draft') : 'publish');
    }

    /**
     * Returns HTML for the page title.
     *
     * @return string  HTML based on the mode and the identity of the viewer
     */
    public function getTitleHtml() {
        if ($this->myBlogPosts) { return xg_html('MY_BLOG_POSTS_TAGGED_X', $_GET['tag']); }
        return xg_html('USERS_BLOG_POSTS_TAGGED_X', ucfirst($this->helper->username($this->profile)), $_GET['tag']);
    }

    /**
     * Returns text for the page title
     *
     * @return string  text for the page title
     */
    public function getPageTitle() {
        return $this->myBlogPosts ?
                    xg_text('MY_BLOG') :
                    xg_text('XS_BLOG', ucfirst($this->helper->username($this->profile)));
    }

    /**
     * Returns HTML for the page title for the given date..
     *
     * @param $monthName string  e.g., January
     * @param $year integer  e.g., 1979
     * @return string  HTML based on the mode and the identity of the viewer
     */
    public function getTitleHtmlForDate($monthName, $year) {
        if ($this->myBlogPosts) { return xg_html('MY_BLOG_POSTS_TAGGED_X_M_Y_ARCHIVE', $_GET['tag'], $monthName, $year); }
        return xg_html('USERS_BLOG_POSTS_TAGGED_X_M_Y_ARCHIVE', ucfirst($this->helper->username($this->profile)), $_GET['tag'], $monthName, $year);
    }

    /**
     * Returns whether to hide posts dated in the future.
     *
     * @return boolean  whether to hide posts that haven't been published yet.
     */
    public function shouldHideFuturePosts() {
        return ! $this->myBlogPosts;
    }

    /**
     * Returns the message to display when there are no posts.
     *
     * @return string  HTML for the message
     */
    public function getNoPostsMessage() {
        return $this->myBlogPosts ? xg_html('YOU_HAVE_NOT_WRITTEN_POSTS_TAGGED_X', xnhtmlentities($_GET['tag'])) : xg_html('USER_HAS_NOT_WRITTEN_POSTS_TAGGED_X', $this->helper->username($this->profile), xnhtmlentities($_GET['tag']));
    }

    /**
     * Returns whether the No Posts message is accompanied by an Add Blog Post link.
     *
     * @return boolean  whether to show an Add link
     */
    public function doesNoPostsMessageHaveAddLink() {
        return $this->myBlogPosts;
    }

    /**
     * Returns the title for the Latest Blog Posts box.
     *
     * @return string  the plain-text title
     */
    public function getLatestBlogPostsTitle() {
        return xg_text('LATEST_BLOG_POSTS');
    }

    /**
     * Returns the title for the Most Popular Blog Posts box.
     *
     * @return string  the plain-text title
     */
    public function getMostPopularBlogPostsTitle() {
        return xg_text('MOST_POPULAR_BLOG_POSTS');
    }

    /**
     * Returns the title for the Monthly Archives box.
     *
     * @return string  the plain-text title
     */
    public function getMonthlyArchivesTitle() {
        return xg_text('MONTHLY_ARCHIVES');
    }

    /**
     * Returns the text to use for the description <meta> tag.
     *
     * @return string  the meta text describing the page
     */
    public function getMetaDescription() {
        return xg_text('BLOG') . ' '. $this->helper->getNetworkDescription() . ' ' . $this->helper->username($this->profile) . ' ' . $_GET['tag'];
    }

    /**
     * Returns the blog-post counts to display in the Monthly Archives box.
     *
     * @return array Blog-post counts, keyed by year and month.
     */
    public function getArchive() {
        $archive = $this->helper->getArchiveForUser($this->user);
        if ($this->myBlogPosts) {
            $currentUserRelationship = 'me';
        } else {
            // If you're a friend of the current user, you get the friends archive, otherwise, you get the 'all' archive
            $isFriend = $this->currentProfile->isLoggedIn() && ($this->helper->getFriendStatusFor($_GET['user'], $this->currentProfile) == 'friend');
            $currentUserRelationship = $isFriend ? 'friends' : 'all';
        }
        return $archive[$currentUserRelationship];
    }

    /**
     * Returns the name of the template to render.
     *
     * @param $numPosts  the total number of posts being viewed
     * @return string e.g., list
     */
    public function getTemplateName($numPosts) {
        return 'list';
    }

    /**
     * Returns the name of the sort field for the main BlogPost query.
     *
     * @param $monthSpecified boolean  whether we are showing posts for a specific month.
     * @return string  the name of the attribute to sort on
     */
    public function getOrderAttribute($monthSpecified) {
        return 'my->publishTime';
    }

    /**
     * Returns the URL for the RSS feed.
     *
     * @return string  the feed URL, or null if no feed is available
     */
    public function getFeedUrl() {
        return null;
    }

}
