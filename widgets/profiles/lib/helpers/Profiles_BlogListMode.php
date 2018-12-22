<?php

/**
 * Logic for the different kinds of blog list pages: user, tag, promoted, all
 */
abstract class Profiles_BlogListMode {

    /** Facade for services used by the list action */
    protected $helper;

    /**
     * Creates the mode object.
     *
     * @param $helper Profiles_BlogListHelper  facade for services used by the list action
     */
    public function __construct($helper) {
        $this->helper = $helper;
    }

    /**
     * Returns tags to display in the Blog Topics By Tags box.
     *
     * @return array  a mapping of tag name to blog-post count.
     */
    public abstract function getTags();

    /**
     * Returns the User object for the current screenName.
     *
     * @return W_Content  the User object
     */
    public abstract function getUser();

    /**
     * Returns the XN_Profile for the current screenName.
     *
     * @return XN_Profile  the profile
     */
    public abstract function getProfile();

    /**
     * Returns filters for the BlogPost query
     *
     * @return array  filters for use with BlogPost::find()
     */
    public abstract function getFilters();

    /**
     * Returns HTML for the page title.
     *
     * @return string  HTML based on the mode and the identity of the viewer
     */
    public abstract function getTitleHtml();

    /**
     * Returns HTML for the page title for the given date..
     *
     * @param $monthName string  e.g., January
     * @param $year integer  e.g., 1979
     * @return string  HTML based on the mode and the identity of the viewer
     */
    public abstract function getTitleHtmlForDate($monthName, $year);

    /**
     * Returns whether to hide posts dated in the future.
     *
     * @return boolean  whether to hide posts that haven't been published yet.
     */
    public abstract function shouldHideFuturePosts();

    /**
     * Returns the message to display when there are no posts.
     *
     * @return string  HTML for the message
     */
    public abstract function getNoPostsMessage();

    /**
     * Returns whether the No Posts message is accompanied by an Add Blog Post link.
     *
     * @return boolean  whether to show an Add link
     */
    public abstract function doesNoPostsMessageHaveAddLink();

    /**
     * Returns the title for the Latest Blog Posts box.
     *
     * @return string  the plain-text title
     */
    public abstract function getLatestBlogPostsTitle();

    /**
     * Returns the title for the Most Popular Blog Posts box.
     *
     * @return string  the plain-text title
     */
    public abstract function getMostPopularBlogPostsTitle();

    /**
     * Returns the title for the Monthly Archives box.
     *
     * @return string  the plain-text title
     */
    public abstract function getMonthlyArchivesTitle();

    /**
     * Returns the text to use for the description <meta> tag.
     *
     * @return string  the meta text describing the page
     */
    public abstract function getMetaDescription();

    /**
     * Returns the blog-post counts to display in the Monthly Archives box.
     *
     * @return array Blog-post counts, keyed by year and month.
     */
    public abstract function getArchive();

    /**
     * Returns the name of the template to render.
     *
     * @param $numPosts  the total number of posts being viewed
     * @return string e.g., list
     */
    public abstract function getTemplateName($numPosts);

    /**
     * Returns the name of the sort field for the main BlogPost query.
     *
     * @param $monthSpecified boolean  whether we are showing posts for a specific month.
     * @return string  the name of the attribute to sort on
     */
    public abstract function getOrderAttribute($monthSpecified);

    /**
     * Returns the URL for the RSS feed.
     *
     * @return string  the feed URL
     */
    public abstract function getFeedUrl();

}
