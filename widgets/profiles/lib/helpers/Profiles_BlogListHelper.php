<?php

/**
 * Facade for services used by the blog/list action, making it easier to test.
 */
class Profiles_BlogListHelper {

    /**
     * Returns the User object for the given screenName.
     *
     * @param $screenName string  the username
     * @return W_Content  the User object
     */
    public function loadUser($screenName) {
        return User::load($screenName);
    }

    /**
     * Returns the XN_Profile for the given screenName.
     *
     * @param $screenName string  the username
     * @return XN_Profile  the profile
     */
    public function loadProfile($screenName) {
        return XG_Cache::profiles($screenName);
    }

    /**
     * Constructs a new XN_Query object for the given subject.
     *
     * @param $subject string specifies the subject of the query
     * @return XN_Query the XN_Query object
     */
    public function createXnQuery($subject) {
        return XN_Query::create($subject);
    }

    /**
     * Query for blog posts, incorporating the right visibility filters based
     * on the currently logged in user
     *
     * @param $filters array An array of filters keyed by attribute name k. Each array element is either:
     *              'v' to filter on k = v
     *              array('op','v') to filter on k op v
     *              array('op','v','type') to filter on k op v type
     * You can also specify the special filter 'ignoreVisibility => true' to get a query
     * that ignores visibility settings.
     * @param integer $begin optional result set start. Defaults to 0
     * @param integer $end   optional result set end.   Defaults to 10
     * @param string $order  optional field to order on. Defaults to my->publishTime
     * @param string $dir    optional ordering direction: asc or desc.
     * @return array A two element array: 'posts' => the requested posts
     *                                    'numPosts' => the total number of posts that match
     */
    public function findBlogPosts($filters, $begin = 0, $end = 10, $order = null, $dir = null) {
        return BlogPost::find($filters, $begin, $end, $order, $dir);
    }

    /**
     * Returns the user's full name
     *
     * @param $p XN_Profile  the user's profile
     * @return string  the network-specific full name
     */
    public function username($p) {
        return xg_username($p);
    }

    /**
     * Returns the name of the app.
     *
     * @return string  the name of the network
     */
    public function getNetworkName() {
        return XN_Application::load()->name;
    }

    /**
     * Get the description of the app
     *
     * @return string  the description of the network
     */
     public function getNetworkDescription() {
        return XG_MetatagHelper::appDescription();
     }

    /**
     * Get the archive list for a particular user
     *
     * @param User $user The user object that holds the archive
     * @return array The archive list
     */
    public function getArchiveForUser($user) {
        return Profiles_BlogArchiveHelper::getPostArchive($user);
    }

    /**
     * Returns the blog-post counts aggregated across all blogs, or an empty array if
     * the BlogArchive has not finished building.
     *
     * @return array Blog-post counts, keyed by year and month.
     */
    public function getArchiveForAllPosts() {
        return BlogArchive::getCounts();
    }

    /**
     * Returns the counts for featured blog posts, or an empty array if
     * the BlogArchive has not finished building.
     *
     * @return array Blog-post counts, keyed by year and month.
     */
    public function getArchiveForFeaturedPosts() {
        return BlogArchive::getFeaturedCounts();
    }

    /**
     * Determines the friend status for the given user list, which may contain
     * XN_Content objects, XN_Profiles, or usernames.
     *
     * @param string $screenName The screenName to test
     * @param object|array $users  A User XN_Content, User W_Content, XN_Profile, screen name, or array of the aforementioned
     * @return array  An array of screen name => status string (contact | friend | pending | requested |
     *         groupie | blocked | not-friend); or just the status if a user object (not an array) was passed in
     */
    public function getFriendStatusFor($screenName, $users) {
        return Profiles_UserHelper::getFriendStatusFor($screenName, $users);
    }

}
