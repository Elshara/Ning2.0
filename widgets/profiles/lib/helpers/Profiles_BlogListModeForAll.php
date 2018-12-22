<?php

W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_BlogListMode.php');

/**
 * Logic for listing all blog posts.
 */
class Profiles_BlogListModeForAll extends Profiles_BlogListMode {

    /**
     * Returns tags to display in the Blog Topics By Tags box.
     *
     * @return array  a mapping of tag name to blog-post count.
     */
    public function getTags() {
        try {
            return $this->helper->createXnQuery('Tag_ValueCount')->filter('content->type','=','BlogPost')->end(10)->execute();
        } catch (Exception $e) {
            return array(); // Workaround for NING-7132 [Jon Aquino 2008-03-03]
        }
    }

    /**
     * Returns the User object for the current screenName.
     *
     * @return W_Content  the User object
     */
    public function getUser() {
        return null;
    }

    /**
     * Returns the XN_Profile for the current screenName.
     *
     * @return XN_Profile  the profile
     */
    public function getProfile() {
        return null;
    }

    /**
     * Returns filters for the BlogPost query
     *
     * @return array  filters for use with BlogPost::find()
     */
    public function getFilters() {
        return array('my->publishStatus' => 'publish');
    }

    /**
     * Returns HTML for the page heading.
     *
     * @return string  HTML based on the mode and the identity of the viewer
     */
    public function getTitleHtml() {
        return xg_html('ALL_BLOG_POSTS');
    }

    /**
     * Returns text for the page title
     *
     * @return string  text for the page title
     */
    public function getPageTitle() {
        return xg_text('BLOGS');
    }

    /**
     * Returns HTML for the page title for the given date..
     *
     * @param $monthName string  e.g., January
     * @param $year integer  e.g., 1979
     * @return string  HTML based on the mode and the identity of the viewer
     */
    public function getTitleHtmlForDate($monthName, $year) {
        return xg_html('M_Y_ARCHIVE', $monthName, $year);
    }

    /**
     * Returns whether to hide posts dated in the future.
     *
     * @return boolean  whether to hide posts that haven't been published yet.
     */
    public function shouldHideFuturePosts() {
        return true;
    }

    /**
     * Returns the message to display when there are no posts.
     *
     * @return string  HTML for the message
     */
    public function getNoPostsMessage() {
        return xg_text('NOBODY_HAS_WRITTEN_POSTS');
    }

    /**
     * Returns whether the No Posts message is accompanied by an Add Blog Post link.
     *
     * @return boolean  whether to show an Add link
     */
    public function doesNoPostsMessageHaveAddLink() {
        return true;
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
        return xg_text('BLOG') . ' '. $this->helper->getNetworkDescription();
    }

    /**
     * Returns the blog-post counts to display in the Monthly Archives box.
     *
     * @return array Blog-post counts, keyed by year and month.
     */
    public function getArchive() {
        return $this->helper->getArchiveForAllPosts();
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
        return Profiles_FeedHelper::feedUrl(W_Cache::getWidget('profiles')->buildUrl('blog','feed',array('xn_auth' => 'no')));
    }

}
