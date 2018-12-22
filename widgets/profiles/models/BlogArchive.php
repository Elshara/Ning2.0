<?php
/**
 * A singleton content object that stores counts of blog posts across the network.
 */
class BlogArchive extends W_Model {

    /** Indicates that the build process has not begun. */
    const NOT_STARTED = 'not_started';

    /** Indicates that the build process is currently running. */
    const IN_PROGRESS = 'in_progress';

    /** Indicates that the build process has finished running. */
    const COMPLETE = 'complete';

    /**
     * Is this object public or private?
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     */
    public $mozzle;

    /**
     * Serialized array of blog-post counts, keyed by "all"/"featured", year, and month
     *
     * @var XN_Attribute::STRING
     */
    public $counts;

   /**
    * Status of the pre-population process.
    *
    * @var XN_Attribute::STRING
    * @rule choice 1,1
    */
    public $buildStatus;
    public $buildStatus_choices = array(self::NOT_STARTED, self::IN_PROGRESS, self::COMPLETE);

    /**
     * Time of the next closest posts scheduled for publishing in future.
	 * If there are no such posts, this field contains 0-date (1970-01-01T00:00:00Z).
     *
     * @var XN_Attribute::DATE optional
     */
	public $nextFuturePost;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here

    /**
     * Add the post to the network-wide archive list, if the post is published and visible to all.
     * If the post is featured, it will be removed from the featured archive list as well.
     *
     * @param $post BlogPost The post to add
     */
    public static function addPostIfEligible($post) {
        if (self::instance()->my->buildStatus == self::IN_PROGRESS) { return; }
        self::updateIfPostEligible($post, +1, true);
    }

    /**
     * Removes the post from the network-wide archive list, if the post is published and visible to all.
     * If the post is featured, it will be removed from the featured archive list as well.
     *
     * @param $post BlogPost The post to remove
     * @param $save boolean  Whether to save the BlogArchive
     */
    public static function removePostIfEligible($post, $save = true) {
        if (self::instance()->my->buildStatus == self::IN_PROGRESS) { return; }
        self::updateIfPostEligible($post, -1, $save);
    }

    /**
     * Add a post to the network-wide archive list, if the post is published and visible to all.
     * If the post is featured, the featured archive list will be updated as well.
     *
     * @param $post BlogPost The post to add or remove
     * @param $delta integer  The amount by which to change the count: +1 or -1
     * @param $save boolean  Whether to save the BlogArchive
     */
    private static function updateIfPostEligible($post, $delta, $save) {
        if ($post->my->publishStatus != 'publish') { return; }
        if ($post->my->visibility != 'all') { return; }
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_BlogArchiveHelper.php');
        list($y, $m) = Profiles_BlogArchiveHelper::timestampToMonthAndYear($post->my->publishTime);
        $counts = self::getCountsProper();
        $counts['all'][$y][$m] = max(0, $counts['all'][$y][$m] + $delta);
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if ($post->my->raw(XG_PromotionHelper::attributeName())) {
            $counts['featured'][$y][$m] = max(0, $counts['featured'][$y][$m] + $delta);
        }
        self::instance()->my->counts = serialize($counts);
        if ($save) {
            self::instance()->save();
        }
    }

    /**
     * Returns the network-wide archive list.
     *
     * @return array Blog-post counts, keyed by "all"/"featured", year, and month
     */
    private static function getCountsProper() {
        return unserialize(self::instance()->my->counts);
    }

    /**
     * Returns the blog-post counts aggregated across all blogs, or an empty array if
     * the BlogArchive has not finished building.
     *
     * @return array Blog-post counts, keyed by year and month.
     */
    public static function getCounts() {
        if (self::instance()->my->buildStatus == self::IN_PROGRESS) { return array(); }
        $counts = self::getCountsProper();
        return $counts['all'];
    }

    /**
     * Returns the counts for featured blog posts, or an empty array if
     * the BlogArchive has not finished building.
     *
     * @return array Blog-post counts, keyed by year and month.
     */
    public static function getFeaturedCounts() {
        if (self::instance()->my->buildStatus == self::IN_PROGRESS) { return array(); }
        $counts = self::getCountsProper();
        return $counts['featured'];
    }

    /**
     * Returns the content object that stores the network-wide archive data.
     *
     * @return XN_Content  the BlogArchive singleton
     */
    public static function instance() {
        static $instance = null;
        if ($instance) { return $instance; }
        $query = XG_Query::create('Content');
        $query->setCaching(XG_Cache::key('type', 'BlogArchive'));
        $query->filter('owner');
        $query->filter('type', '=', 'BlogArchive');
        $query->end(1);
        $results = $query->execute();
        if ($results) {
            return $instance = $results[0];
        }
        $instance = XN_Content::create('BlogArchive');
        $instance->my->counts = serialize(array('all' => array(), 'featured' => array()));
        $instance->my->mozzle = 'profiles';
        $instance->my->buildStatus = self::NOT_STARTED;
        $instance->isPrivate = XG_App::appIsPrivate();
        return $instance;
    }

    /**
     * Called when a content object becomes featured.
     * Its xg_status_promotedOn attribute will be set.
     */
    public static function contentFeatured($content) {
        if ($content->type == 'BlogPost') { self::addPostIfEligible($content); }
    }

    /**
     * Called when a content object becomes unfeatured.
     * Its xg_status_promotedOn attribute will be null.
     */
    public static function contentUnfeatured($content) {
        if ($content->type == 'BlogPost') { self::removePostIfEligible($content); }
    }

    /**
     * Create an XN_Job to populate the BlogArchive with existing blog posts.
     * Call this repeatedly until BlogArchive::instance()->my->buildStatus == BlogArchive::COMPLETE.
     *
     * @param $start integer  start index for the BlogPost query
     * @param $end integer  end index for the BlogPost query
     */
    public static function build($start, $end) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (self::instance()->my->buildStatus == self::COMPLETE) { return; }
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'BlogPost');
        $query->filter('my.visibility', '=', 'all');
        $query->filter('my.publishStatus', '=', 'publish');
        $query->begin($start ? $start : 0);
        $query->end($end);
        $query->order('createdDate', 'asc');
        $posts = $query->execute();
        foreach ($posts as $post) {
            self::updateIfPostEligible($post, +1, false);
        }
        self::instance()->my->buildStatus = $posts ? self::IN_PROGRESS : self::COMPLETE;
        self::instance()->save();
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}

XN_Event::listen('feature/after', array('BlogArchive', 'contentFeatured'));
XN_Event::listen('unfeature/before', array('BlogArchive', 'contentUnfeatured'));
