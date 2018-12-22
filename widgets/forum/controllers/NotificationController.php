<?php

/**
 * Dispatches requests pertaining to email notifications.
 */
class Forum_NotificationController extends XG_GroupEnabledController {

    /**
     * Adds the current user to the list of people notified about new topics.
     *
     * Expected GET variables:
     *     xn_out - set this to "json"
     */
    public function action_startFollowingNewTopics() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        if (! Forum_SecurityHelper::currentUserCanFollowNewTopics()) { throw new Exception('Not allowed (1529927600)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (792331805)'); }
        $this->_widget->includeFileOnce('/lib/helpers/Forum_NotificationHelper.php');
        Forum_NotificationHelper::startFollowingNewTopics();
    }

    /**
     * Removes the current user from the list of people notified about new topics.
     *
     * Expected GET variables:
     *     xn_out - set this to "json"
     */
    public function action_stopFollowingNewTopics() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (1139545260)'); }
        $this->_widget->includeFileOnce('/lib/helpers/Forum_NotificationHelper.php');
        Forum_NotificationHelper::stopFollowingNewTopics();
    }

}
