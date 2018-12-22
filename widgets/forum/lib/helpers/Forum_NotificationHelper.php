<?php

/**
 * Useful functions for working with email notifications.
 */
class Forum_NotificationHelper {

    /** Label for all ProfileSets for subscribing to new topics */
    const NEW_TOPIC_LABEL = 'xg_new_topic_follow';

    /**
     * Returns the Profile Set ID for users subscribed to new topics.
     *
     * @return string  the Profile Set ID, which accounts for whether we are in a group context
     */
    public static function newTopicProfileSetId() {
        $newTopicProfileSetId = self::NEW_TOPIC_LABEL;
        if (XG_GroupHelper::inGroupContext()) { $newTopicProfileSetId .= '_' . strtr(XG_GroupHelper::currentGroupId(), ':', '_'); }
        return $newTopicProfileSetId;
    }

    /**
     * Returns whether the current user is subscribed to new topics.
     *
     * @return boolean  whether to notify the user about new topics
     */
    public static function currentUserIsFollowingNewTopics() {
        return XN_ProfileSet::setContainsUser(self::newTopicProfileSetId(), XN_Profile::current()->screenName);
    }

    /**
     * Subscribes the current user to new topics.
     */
    public static function startFollowingNewTopics() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        $labels = array(self::NEW_TOPIC_LABEL);
        if (XG_GroupHelper::inGroupContext()) { $labels[] = Index_NotificationHelper::groupLabel(XG_GroupHelper::currentGroupId()); }
        $profileSet = XN_ProfileSet::loadOrCreate(self::newTopicProfileSetId(), $labels);
        $profileSet->addMembers(XN_Profile::current()->screenName);
    }

    /**
     * Unsubscribes the current user from new topics.
     */
    public static function stopFollowingNewTopics() {
        $profileSet = XN_ProfileSet::loadOrCreate(self::newTopicProfileSetId());
        $profileSet->removeMember(XN_Profile::current()->screenName);
    }

    /**
     * Sends an email notification to users subscribed to new topics.
     *
     * @param $topic  XN_Content|W_Content  The Topic object
     */
    public static function notifyNewTopicFollowers($topic) {
		// executed via XG_Browser::execInEmailContext
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $notification = new XG_Message_New_Topic();
        $notification->send(self::newTopicProfileSetId() . '@lists', $topic,
                XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'topic', 'show', array('id' => $topic->id)),
                XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'index', 'index'));
    }

}
