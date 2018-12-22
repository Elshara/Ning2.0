<?php
/**
 * A discussion topic.
 */
class Topic extends W_Model {

    // The name "Topic" is chosen for backwards compatibility with Ning Group [Jon Aquino 2007-01-17]

    /**
     * The title of the topic. Be sure to escape this with xnhtmlentities() when displaying it.
     *
     * @var XN_Attribute::STRING
     * @rule length 1,200
     * @feature indexing text
     */
    public $title;
    const MAX_TITLE_LENGTH = 200;

    /**
     * The body of the topic; scrubbed HTML
     *
     * @var XN_Attribute::STRING
     * @rule length 1,100000
     * @feature indexing text
     */
    public $description;
    const MAX_DESCRIPTION_LENGTH = 100000;

    /**
     * Is this topic public or private?
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     * @feature indexing phrase
     */
    public $mozzle;

    /**
     * The topic's most popular 10 tags
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $topTags;
    const TOP_TAGS_COUNT = 10;

    /**
     * Username of the person who left the last comment
     *
     * @var XN_Attribute::STRING optional
     */
    public $lastCommentContributorName;

    /**
     * ISO-8601 date of the last comment
     *
     * @var XN_Attribute::STRING optional
     */
    public $lastCommentCreatedDate;

    /**
     * ISO-8601 date of the last comment, or the creation date of the topic if no comments exist
     *
     * @var XN_Attribute::DATE optional
     */
    public $lastEntryDate;

    /**
     * The ID of the category to which this Topic belongs, if any
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing phrase
     */
    public $categoryId;

    /**
     * Content ID of the Group to which this object belongs
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing phrase
     */
    public $groupId;

    /**
     * "Y" indicates that this group should be excluded from Ningbar and widget
     * searches. This is true of topics in private groups.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $excludeFromPublicSearch;
    public $excludeFromPublicSearch_choices = array('Y', 'N');

    /**
     * "Y" indicates that comments are no longer allowed for this discussion thread.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $commentsClosed;
    public $commentsClosed_choices = array('Y', 'N');

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here

    /**
     * Construct a new Topic.
     *
     * @param $title string  The title of the topic
     * @param $description string  The body of the topic (HTML scrubbed of scripts and invalid tags)
     * @return W_Content  An unsaved Topic
     * @see Topic.php for the max title and description lengths
     * @see HTML_Scrubber::scrub()
     */
    public function create($title = null, $description = null) {
        $topic = W_Content::create('Topic');
        $topic->title = $title;
        $topic->description = $description;
        $topic->my->mozzle = W_Cache::current('W_Widget')->dir;
        $topic->isPrivate = XG_App::appIsPrivate() || XG_GroupHelper::groupIsPrivate();
        Comment::initializeCounts($topic);
        return $topic;
    }

    /**
     * Scrubs, linkifies, and truncates the given description.
     *
     * @param $description string  The Topic description
     * @return string  The cleaned up Topic description
     */
    public static function cleanDescription($description) {
        return mb_substr(xg_linkify(Forum_HtmlHelper::scrub($description ? $description : xg_text('NO_DESCRIPTION'))), 0, self::MAX_DESCRIPTION_LENGTH);
    }

    /**
     * Truncates the given title
     *
     * @param $title string  The Topic title
     * @return string  The cleaned up Topic title
     */
    public static function cleanTitle($title) {
        return mb_substr($title ? $title : xg_text('UNTITLED'), 0, Topic::MAX_TITLE_LENGTH);
    }

    /**
     * For the given array of Topic and Comment objects, returns a mapping of topic-ID => Topic.
     *
     * @param $topicsAndComments array  A mix of Topics and Comments
     * @return array  A mapping of topic IDs to Topic objects
     */
    public static function topics($topicsAndComments) {
        $topics = array();
        $topicIdsToQuery = array();
        foreach ($topicsAndComments as $topicOrComment) {
            if ($topicOrComment->type == 'Topic') {
                $topics[$topicOrComment->id] = $topicOrComment;
            } else {
                $topicIdsToQuery[] = $topicOrComment->my->attachedTo;
            }
        }
        if ($topicIdsToQuery) {
            $query = XN_Query::create('Content');
            $query->filter('owner');
            $query->filter('type', '=', 'Topic');
			XG_GroupHelper::addGroupFilter($query);
            $query->filter('id', 'in', $topicIdsToQuery);
            foreach ($query->execute() as $topic) {
                $topics[$topic->id] = $topic;
            }
        }
        return $topics;
    }

    /**
     * Returns the screen names of the people who contributed the last comment
     * in the given topics.
     *
     * @param $topics array  The XN_Content Topic objects
     * @return array  The usernames of last-comment contributors
     */
    public static function lastCommentContributorNames($topics) {
        $lastCommentContributorNames = array();
        foreach ($topics as $topic) {
            if (! $topic->my->lastCommentContributorName) { continue; }
            $lastCommentContributorNames[$topic->my->lastCommentContributorName] = $topic->my->lastCommentContributorName;
        }
        return $lastCommentContributorNames;
    }

    /**
     * Returns the number of files that can be attached to the given Topic.
     *
     * @param $topic W_Content  The Topic
     */
    public static function emptyAttachmentSlotCount(W_Content $topic) {
        return max(0, 3 - count(Forum_FileHelper::getFileAttachments($topic)));
    }

    /**
     * Returns the URL for the "Start a New Discussion" page.
     *
     * @param $categoryId string  the ID of the initial category, or null to choose one automatically
     * @return string  the URL
     */
    public static function newTopicUrl($categoryId = null) {
        $categoryId = $categoryId ? $categoryId : $_GET['categoryId'];
		$args = array('target' => XG_HttpHelper::currentUrl());
		if ($categoryId) {
			$args['categoryId'] = $categoryId;
		}
		return XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'topic', 'new', $args);
    }

    /**
     * Returns the n most recently featured Topics and the total featured count.
     *
     * @param $categoryID string the id of the category that you want to restrict items to; optional;
     *          query caching is not used if we filter by category
     * @return array  the Topic data, keyed by items and totalCount
     */
    public function getFeaturedTopics($categoryId = null, $start = 0, $count = 3) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_Filter.php');
        if (! XG_PromotionHelper::areQueriesEnabled()) { return array('items' => array(), 'totalCount' => 0); }
        if ($categoryId) {
            $category = Category::findById($categoryId);
            if ($category) {
                $featuredTopicsQuery = XN_Query::create('Content')->begin($start)->end($count)->filter('owner')->filter('type','=', 'Topic');
                $categoryIds = explode(' ' , $category->my->alternativeIds);
                $featuredTopicsQuery->filter('my->categoryId', 'in', $categoryIds);
            }
        } else {
            $featuredTopicsQuery = XG_Query::create('Content')->begin($start)->end($count);
            $featuredTopicsQuery->addCaching(XG_CacheExpiryHelper::promotedObjectsChangedCondition('Topic'));
            $featuredTopicsQuery = Category::addDeletedCategoryFilter($featuredTopicsQuery);
        }
        return array('items' => Forum_Filter::get('promoted')->execute($featuredTopicsQuery), 'totalCount' => $featuredTopicsQuery->getTotalCount());
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}
