<?php
/**
 * A category of discussions.
 */
class Category extends W_Model {

    /**
     * The title of the category. Be sure to escape this with xnhtmlentities() when displaying it.
     *
     * @var XN_Attribute::STRING
     * @rule length 1,200
     * @feature indexing text
     */
    public $title;
    const MAX_TITLE_LENGTH = 200;

    /**
     * The body of the category; scrubbed HTML.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 1,100000
     * @feature indexing text
     */
    public $description;
    const MAX_DESCRIPTION_LENGTH = 100000;

    /**
     * Is this category public or private?
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Content ID of the Group to which this object belongs
     *
     * @var XN_Attribute::STRING optional
     */
    public $groupId;

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     * @feature indexing phrase
     */
    public $mozzle;

    /**
     * Space-delimited string of IDs from deleted categories whose Topics
     * have been moved to this category; this category's ID is also included, for convenience.
     * The maximum number of IDs is 100, which is the limit on an "in" filter.
     * The ID string "null" is used for Topics with null categoryIds.
     *
     * @var XN_Attribute::STRING optional
     */
    public $alternativeIds;

    /**
     * Position of this category
     *
     * @var XN_Attribute::NUMBER
     */
    public $order;

    /**
     * Whether people other than the network creator can post to this category
     *
     * @var XN_Attribute::STRING
     * @rule choice 1
     */
    public $membersCanAddTopics;
    public $membersCanAddTopics_choices = array('Y','N');

    /**
     * Whether members can add replies to discussions in this category.
     *
     * @var XN_Attribute::STRING
     * @rule choice 1
     */
    public $membersCanReply;
    public $membersCanReply_choices = array('Y','N');


    /**
     * The number of discussion topics in this category
     *
     * @var XN_Attribute::NUMBER optional
     */
    public $discussionCount;


    /**
     * A comma separated list of timestamps and ids denoting discussion updates within this category; up to 10 ordered with the most recent first
     *
     * @var XN_Attribute::STRING optional
     */
    public $latestDiscussionActivity;


/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here

    /**
     * Constructs a new Category.
     *
     * @param $title string  The title of the category
     * @param $description string  The body of the category (HTML scrubbed of invalid tags)
     * @return W_Content  An unsaved Category
     */
    public function create($title = null, $description = null) {
        $category = W_Content::create('Category', $title, $description);
        $category->my->mozzle = W_Cache::current('W_Widget')->dir;
        $category->isPrivate = XG_App::appIsPrivate() || XG_GroupHelper::groupIsPrivate();
        return $category;
    }

    /**
     * Creates, updates, and deletes the app's Category objects, based on the given metadata.
     *
     * @param $metadata array  metadata for each Category object: id (optional), title, description, membersCanAddTopics, membersCanReply
     * @return array  The W_Content Category objects
     */
    public static function buildCategories($metadata) {
        $currentWidget = W_Cache::current('W_Widget')->dir;
        XG_Query::invalidateCache('category-ids-and-titles-' . $currentWidget);
        $oldCategories = self::idsToObjects(self::findAll());
        $categories = array();
        $i = 0;
        foreach ($metadata as $categoryMetadata) {
            $i++;
            $category = $categoryMetadata['id'] ? W_Content::load($categoryMetadata['id']): Category::create();
            if ($category->type != 'Category') { xg_echo_and_throw('Type of ' . $category->id . ' is not Category'); }
            $category->my->order = $i;
            $category->title = self::cleanTitle($categoryMetadata['title']);
            $category->description = self::cleanDescription($categoryMetadata['description']);
            $category->my->membersCanAddTopics = $categoryMetadata['membersCanAddTopics'] ? 'Y' : 'N';
            $category->my->membersCanReply = $categoryMetadata['membersCanReply'] ? 'Y' : 'N';
            $category->my->alternativeIds = $categoryMetadata['alternativeIds'];
            if ($category->my->membersCanAddTopics == 'Y') { $category->my->membersCanReply = 'Y'; }
            $errors = $category->validate();
            if (count($errors)) { xg_echo_and_throw('Invalid content: ' . var_export($errors, true)); }
            $categories[] = $category;
            unset($oldCategories[$category->id]);
        }
        // Only save if no errors occurred [Jon Aquino 2007-03-27]
        foreach ($categories as $category) {
            $category->save(); // save is required here
            if (! $category->my->alternativeIds || ! in_array($category->id, explode(' ', $category->my->alternativeIds))) {
                $category->my->alternativeIds = trim($category->my->alternativeIds . ' ' . $category->id);
                $category->save();
            }
            $category = self::updateDiscussionCountAndActivity($category, null, true);
        }
        foreach ($oldCategories as $id => $category) {
            // Delete the category rather than the ID; otherwise the query cache won't get invalidated [Jon Aquino 2007-03-27]
            XN_Content::delete($category);
        }
        self::$categories = array();
        self::invalidateRecentTopicsCacheForAllCategories();
        return $categories;
    }

    /**
     * Updates the discussion count and latestDiscussionActivity attribute for a category
     *
     * @param $category string|XN_Content|W_Content object or string The category object; will be loaded if an id is supplied
     * @param $count number  The number to update the discussions to.  If null, this is calculated.  defaults to null
     * @param $save boolean Whether to save the category in the function, defaults to false
     * @return W_Content  A Category object, either saved or not, depending on the value of $save
     * null may be returned in cases where a topic was created prior to the establishment of categories and $category is null
     * or if a given category can't be found
     */
    public function updateDiscussionCountAndActivity($category, $count = null, $save = false) {
        if (is_string($category)) {
            $category = self::findById($category);
        }
        if (is_null($category)) {
            return null;
        }
        if ($category->type != 'Category') {
            throw new Exception("Assertion failed (735450986)");
        }
        if ($count == null) {
            $query = XG_Query::create('Content')
                ->addCaching(XG_Cache::key('type', 'Topic'))
                ->filter('owner')
                ->filter('type', '=', 'Topic')
                ->filter('my->mozzle', '=', W_Cache::current('W_Widget')->dir)
                ->order('my->lastEntryDate','desc', XN_Attribute::DATE)
                ->end(1)
                ->alwaysReturnTotalCount(true);
                self::addCategoryFilter($query, $category);
            XG_GroupHelper::addGroupFilter($query);
            $results = $query->execute();
            $count =  $query->getTotalCount();
        }
        $category->my->set('discussionCount', $count, XN_Attribute::NUMBER);
        if ($count > 0) {
            $category = self::updateLatestDiscussionActivity($category, $results[0]);
        } else {
            $category->my->latestDiscussionActivity = '';
        }
        if ($save) {
            $category->save();
        }
        return $category;
    }

    /**
     * adds a discussion id and timestamp to a category's latestDiscussionActivity attribute
     *
     * @param $category string|XN_Content|W_Content string or object  The category object; will be loaded if an id is supplied
     * @param $topic object XN_Content|W_Content  The topic object; the id and updatedDate attributes will be used
     * @param $save boolean Whether to save the category in the function, defaults to false
     * @return W_Content  A Category object, either saved or not, depending on the value of $save
     * null may be returned in cases where a topic was created prior to the establishment of categories and $category is null
     * or if a given category can't be found
     */
    public function updateLatestDiscussionActivity($category, $topic, $save = false) {
        if ($topic->type != 'Topic') {
            throw new Exception("Assertion failed (994675765): Type is: " . $topic->type);
        }
        if (is_string($category)) {
            $category = self::findById($category);
        }
        if (is_null($category)) {
            return null;
        }
        if ($category->type != 'Category') {
            throw new Exception("Assertion failed (84231983): Type is: " . $category->type);
        }
        $attributeArray = explode(',', $category->my->latestDiscussionActivity);
        // don't do anything if the submitted timestamp is older than the current latest one
        if (count($attributeArray)) {
            $activeTopic = explode(' ', $attributeArray);
            if ($activeTopic[0] >= strtotime($topic->my->lastEntryDate)) {
                return $category;
            }
        }
        array_unshift($attributeArray, strtotime($topic->my->lastEntryDate) . ' ' . $topic->id);
        if (count($attributeArray) > 10) {
            $attributeArray = array_slice($attributeArray, 0, 5, true);
        }
        $category->my->latestDiscussionActivity = implode(',', $attributeArray);
        if ($save) {
            $category->save();
        }
        return $category;
    }


    /**
     * Returns a group of category objects, sliced and ordered according to the params.
     *
     * @param $begin number The starting position for the query. defaults to 0
     * @param $count number The number of objects to return. defaults to 20
     * @param $sort array An array representing the sort option in the form $sortBy, $sortOrder, $sortType
     * @return array  An array containing the Category XN_Content objects and the total count of objects
     */
    public static function find($begin = 0, $count = 20, $sort = array('my->latestDiscussionActivity','desc',XN_Attribute::STRING)) {
        $query = XN_Query::create('Content');
        if (XG_Cache::cacheOrderN() || (! XG_GroupHelper::inGroupContext())) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'Category'));
        }
        $query->filter('owner');
        $query->filter('type', '=', 'Category');
        XG_GroupHelper::addGroupFilter($query);
        $query->order($sort[0], $sort[1], $sort[2]);
        $query->begin($begin);
        $query->end($begin + $count);
        $widget = W_Cache::current('W_Widget');
        $query->filter('my.mozzle', '=', $widget->dir);
        $query->alwaysReturnTotalCount(true);
        return array('objects'=>$query->execute(),'totalCount'=>$query->getTotalCount());
    }

    /**
     * Returns the Category objects in their designated order.
     *
     * @param $includeOwnerOnlyCategories boolean  Whether to include categories to which only the owner can post
     * @return array  The Category XN_Content objects
     */
    public static function findAll($includeOwnerOnlyCategories = true) {
        if (is_null(self::$categories[$includeOwnerOnlyCategories]) || defined('UNIT_TESTING')) {
            $query = XN_Query::create('Content');
            if (XG_Cache::cacheOrderN() || (! XG_GroupHelper::inGroupContext())) {
                $query = XG_Query::create($query);
                $query->addCaching(XG_Cache::key('type', 'Category'));
            }
            $query->filter('owner');
            $query->filter('type', '=', 'Category');
            XG_GroupHelper::addGroupFilter($query);
            $query->order('my.order', 'asc', XN_Attribute::NUMBER);
            $widget = W_Cache::current('W_Widget');
            $query->filter('my.mozzle', '=', $widget->dir);
            if (! $includeOwnerOnlyCategories) { $query->filter('my.membersCanAddTopics', '=', 'Y'); }
            if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
            self::$categories[$includeOwnerOnlyCategories] = $query->execute();
        }
        return self::$categories[$includeOwnerOnlyCategories];
    }

    /**
     * Returns the Category object with the given ID.
     *
     * @param $categoryId string  the ID of the Category - null if the Topic has not been assigned a Category,
     *     in which case we look for a Category with null as one of its alternativeIds
     * @return W_Content  the matching Category object, or null if it no longer exists
     */
    public static function findById($categoryId) {
        if (! is_null($categoryId) && ! is_string($categoryId) && ! is_numeric($categoryId)) { xg_echo_and_throw('Not a string'); }
        foreach (self::findAll() as $category) {
            if (in_array($categoryId ? $categoryId : 'null', explode(' ', $category->my->alternativeIds))) { return $category; }
        }
        return null;
    }

    /** A two-element array containing cached categories that (a) do not include owner-only categories (b) do include owner-only categories */
    private static $categories = array();

    /**
     * Constructs a mapping of ids to objects.
     *
     * @param $objects array  XN_Content objects from which to extract the ids
     * @return array  An array of id => object
     */
    private static function idsToObjects($objects) {
        $idsToObjects = array();
        foreach ($objects as $object) {
            $idsToObjects[$object->id] = $object;
        }
        return $idsToObjects;
    }

    /**
     * Scrubs, linkifies, and truncates the given description.
     *
     * @param $description string  The Category description
     * @return string  The cleaned up Category description
     */
    public static function cleanDescription($description) {
        $description = trim($description ? $description : '');
        return mb_substr(xg_linkify(xg_scrub($description)), 0, self::MAX_DESCRIPTION_LENGTH);
    }

    /**
     * Truncates the given title
     *
     * @param $title string  The Category title
     * @return string  The cleaned up Category title
     */
    public static function cleanTitle($title) {
        $title = trim($title ? $title : '');
        return mb_substr($title ? $title : xg_text('UNTITLED_CATEGORY'), 0, self::MAX_TITLE_LENGTH);
    }

    /**
     * Returns recent topics for the specified category.
     *
     * @param $category XN_Content|W_Content  the Category
     * @return array  XN_Content Topic objects
     */
    public static function recentTopics($category, $end = 3) {
        $query = XN_Query::create('Content');
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->setCaching(self::recentTopicsInvalidationKey($category->id));
        }
        self::addCategoryFilter($query, $category);
        $query->end($end);
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Forum_Filter.php');
        return Forum_Filter::get('mostRecentlyUpdatedDiscussions')->execute($query);
    }

    /**
     * Filters the given topic query by category.
     *
     * @param $query XN_Query|XG_Query  the query to add the filter to
     * @param $category XN_Content|W_Content  the Category
     * @return XN_Query|XG_Query  the query
     */
    public static function addCategoryFilter($query, $category) {
        $filters = array();
        foreach (explode(' ', $category->my->alternativeIds) as $id) {
            $filters[] = XN_Filter('my.categoryId', '=', $id == 'null' ? null : $id);
        }
        return $query->filter(call_user_func_array(array('XN_Filter', 'any'), $filters));
    }

    /**
     * Filters the topic query to ensure that topics in deleted categories aren't included.
     * this will not scale to beyond 100 categories; this isn't a problem right now because > 100 categories isn't supported
     *
     * @param $query XN_Query|XG_Query  the query to add the filter to
     * @return XN_Query|XG_Query  the query
     */
    public static function addDeletedCategoryFilter($query) {
        $categories = self::findAll(true);
        $categoryIds = array_keys(self::titlesAndIds(true));
        if (count($categoryIds) > 100) {
            $categoryIds = array_slice($categoryIds, 0, 100, true);
            // if we have more than 100 items in the categoryIds array, then there may be some data loss for this query.
            // TODO: perhaps use this as an opportunity to launch a background job which updates the category and topic relationships
            // to make them more straightforward, updating topic objects which are attached to an alternativeId to point them
            // to the primary id, and deleting topics that belong to deleted categories. [pcm 2008-06-04]
        }
        if (count($categories)) {
            return $query->filter( XN_Filter::any(
                         XN_Filter('my->categoryId', 'in', $categoryIds),
                         XN_Filter('my->categoryId', '=', null)
                         ));
        } else {
            return $query;
        }
    }

    /**
     * Clears the cache of recent topics for the specified category.
     *
     * @param $category XN_Content|W_Content  the Category (or null, which will do nothing)
     */
    public static function invalidateRecentTopicsCache($category) {
        if (! $category) { return; }
        if ($category->type != 'Category') { xg_echo_and_throw('Not a Category: ' . $category->type); }
        XG_Query::invalidateCache(self::recentTopicsInvalidationKey($category->id));
    }

    /**
     * Clears the cache of recent topics for all categories.
     */
    public static function invalidateRecentTopicsCacheForAllCategories() {
        foreach (self::findAll() as $category) {
            self::invalidateRecentTopicsCache($category);
        }
    }

    /**
     * Returns the key used to invalidate the recent-topics query for the specified category.
     *
     * @param $categoryId string  the ID of the Category
     * @return string  the invalidation key
     * @see David Sklar, "Query Caching", internal wiki
     * @see XG_Query#setCaching
     */
    protected static function recentTopicsInvalidationKey($categoryId) {
        if (! is_string($categoryId) && ! is_numeric($categoryId)) { xg_echo_and_throw('Not a string'); }
        return 'recent-topics-' . $categoryId;
    }

    /**
     * Sets the topic's category
     *
     * @param $topic W_Content|XN_Content  The Topic
     * @param $categoryId string  The content ID of the Category
     */
    public static function setCategoryId($topic, $categoryId) {
        if (self::find($topic->my->categoryId) && $topic->my->categoryId != $categoryId) {
            self::$categoryIdsToInvalidateOnSave[] = $topic->my->categoryId;
        }
        $topic->my->categoryId = $categoryId;
    }

    /**
     * Returns a boolean indicating whether the network is using categories
     *
     * @return boolean
     */
    public static function usingCategories() {
        if (XG_GroupHelper::inGroupContext()) {
            return false;
        }
        $forumWidget = W_Cache::getWidget('forum');
        $usingCategories = $forumWidget->config['usingCategories'];
        if (is_null($usingCategories)) {
            $categories = self::findAll();
            if (count($categories) > 1) {
                $forumWidget->config['usingCategories'] = 1;
            } else {
                $forumWidget->config['usingCategories'] = 0;
            }
            $forumWidget->saveConfig();
        } else {
            return $usingCategories;
        }
    }

    /** IDs of categories for which to invalidate the recent topic caches the next time a Topic is saved. */
    private static $categoryIdsToInvalidateOnSave = array();

    /**
     * Called after a content object has been saved or before a content object has been deleted.
     *
     * @param $object mixed  The content object, an array, or possibly some other thing if the XN_Event API changes
     */
    public static function contentSavedOrDeleted($object) {
        if (is_array($object)) {
            foreach ($object as $o) { self::contentSavedOrDeleted($o); }
            return;
        }
        if (! ($object instanceof XN_Content || $object instanceof W_Content)) { return; }
        if ($object->type == 'Topic') {
            // Ensure W_Cache::current('W_Widget') returns the correct value [Jon Aquino 2007-03-29]
            W_Cache::push(W_Cache::getWidget($object->my->mozzle));
            self::invalidateRecentTopicsCache(self::findById($object->my->categoryId));
            foreach (self::$categoryIdsToInvalidateOnSave as $categoryId) {
                self::invalidateRecentTopicsCache(self::findById($categoryId));
            }
            self::$categoryIdsToInvalidateOnSave = array();
            W_Cache::pop(W_Cache::current('W_Widget'));
        }
    }


    /**
     * A heavily cached query to be used anywhere where we only present category titles and IDs.  It should not be
     * used anywhere where we need to present other category information, like the number of discussions in a category,
     * and $idsAsKeys = true should not be used anywhere where a category count is required, since it contains entires for alternative Ids
     * for deleted categories.
     * The query is only invalidated when an administrator updates the categories via the category manage page.
     *
     * @param $idsAsKeys boolean; returns the query as an array with the category ID as the key
     * @return XN_Query|XG_Query|array  the category objects
     */
    public static function titlesAndIds($idsAsKeys = false) {
        $results = self::categoriesForTitlesAndIds();
        if ($idsAsKeys) {
            $cats = array();
            foreach ($results as $category) {
                $alternatives = explode(' ', $category->my->alternativeIds);
                foreach ($alternatives as $alt) {
                    if ($alt == 'null') {
                        $cats[''] = $category;
                    } else {
                        $cats[$alt] = $category;
                    }
                }
            }
            return $cats;
        }
        return $results;
    }

    /**
     * Returns all Category objects, for use by the titlesAndIds function.
     * Heavily cached - the query is only invalidated when an administrator
     * updates the categories via the category manage page.
     *
     * @return array  the category objects
     */
     protected static function categoriesForTitlesAndIds() {
         if (is_null(self::$categoriesForTitlesAndIds)) {
             $currentWidget = W_Cache::current('W_Widget')->dir;
             self::$categoriesForTitlesAndIds = XG_Query::create('Content')
                     ->filter('owner')
                     ->filter('type','eic','Category')
                     ->order('my.order', 'asc', XN_Attribute::NUMBER)
                     ->filter('my.mozzle', '=', $currentWidget)
                     ->setCaching('category-ids-and-titles-' . $currentWidget)
                     ->execute();
         }
         return self::$categoriesForTitlesAndIds;
     }

    /** Category objects returned by the titlesAndIds function. */
    protected static $categoriesForTitlesAndIds = null;

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}

XN_Event::listen('xn/content/save/after', array('Category', 'contentSavedOrDeleted'));
XN_Event::listen('xn/content/delete/before', array('Category', 'contentSavedOrDeleted'));
