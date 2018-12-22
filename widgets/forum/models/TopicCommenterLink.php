<?php
/**
 * An association between a Topic and a person who comments on it.
 * If Joe adds 3 comments to a Topic, and Sarah adds 4, there will be
 * two TopicCommenterLinks: one for Joe and one for Sarah.
 *
 * TopicCommenterLinks are used in the "Discussions Added To" query on the X's Discussions page.
 * It lists Topics that you've commented on.
 *
 * The commenter's username is given by the contributorName attribute.
 */
class TopicCommenterLink extends W_Model {

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
     */
    public $mozzle;

    /**
     * ID of the Topic object
     *
     * @var XN_Attribute::STRING
     */
    public $topicId;

    /**
     * Content ID of the Group to which this object belongs
     *
     * @var XN_Attribute::STRING optional
     */
    public $groupId;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here

    /**
     * Creates a TopicCommenterLink if one does not already exist
     * for the given topic and the current user.
     *
     * @param $topicId string  The ID of the Topic object
     * @param $testing boolean  Whether this function is currently being tested. Defaults to FALSE.
     */
    public static function createLinkIfNecessary($topicId, $testing = FALSE) {
        if (count(self::linksForCurrentUser($topicId))) { return; }
        $topicCommenterLink = W_Content::create('TopicCommenterLink');
        $widget = W_Cache::current('W_Widget');
        $topicCommenterLink->my->mozzle = $widget->dir;
        $topicCommenterLink->isPrivate = TRUE;
        $topicCommenterLink->my->topicId = (string) $topicId;
        $topicCommenterLink->save();
    }

    /**
     * Deletes a TopicCommenterLink if the current user does not currently
     * have any comments on the given Topic.
     *
     * @param $topicId string  The ID of the Topic object
     */
    public static function deleteLinkIfNecessary($topicId) {
        if (! $topicId) { throw new Exception('Assertion failed'); }
        $query = XN_Query::create('Content');
        $query->filter('type', '=', 'Comment');
        $query->filter('owner');
        XG_GroupHelper::addGroupFilter($query);
        $query->filter('contributorName', '=', XN_Profile::current()->screenName);
        $query->filter('my->attachedTo', '=', (string) $topicId);
        $query->end(1);
        if (count($query->execute())) { return; }
        foreach (self::linksForCurrentUser($topicId) as $topicCommenterLink) {
            XN_Content::delete($topicCommenterLink);
        }
    }

    /**
     * Returns the TopicCommenterLinks for the given topic and the current user.
     * Normally there should just be one.
     *
     * @param $topicId string  The ID of the Topic object
     * @return array  The TopicCommenterLink objects (normally just one)
     */
    public static function linksForCurrentUser($topicId) {
        if (! $topicId) { throw new Exception('Assertion failed'); }
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'TopicCommenterLink');
        XG_GroupHelper::addGroupFilter($query);
        $query->filter('my.topicId', '=', (string) $topicId);
        $query->filter('contributorName', '=', XN_Profile::current()->screenName);
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'TopicCommenterLink'));
        }
        return $query->execute();
    }

    /**
     * Returns the TopicCommenterLinks for the given topic and all people who have commented on it.
     *
     * @param $topicId string  The ID of the Topic object
     * @param $limit integer  The maximum number of TopicCommenterLinks to return; defaults to 100.
     * @return array  The TopicCommenterLink objects for the given topic
     */
    public static function links($topicId, $limit = 100) {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'TopicCommenterLink');
        XG_GroupHelper::addGroupFilter($query);
        $query->filter('my.topicId', '=', (string) $topicId);
        $query->end($limit);
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'TopicCommenterLink'));
        }
        return $query->execute();
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}
