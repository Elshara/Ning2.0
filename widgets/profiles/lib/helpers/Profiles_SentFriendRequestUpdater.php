<?php

W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_AbstractFriendRequestUpdater.php');
W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');

/**
 * Processes all sent friend requests using an async job.
 * Ensures that no more than one async job is operating on sent friend requests.
 */
class Profiles_SentFriendRequestUpdater extends Profiles_AbstractFriendRequestUpdater {

    /** The singleton instance of this class. */
    private static $instance;

    /**
     * Returns the singleton instance of this class.
     *
     * @return Profiles_SentFriendRequestUpdater  the singleton
     */
    public static function instance() {
        if (! self::$instance) { self::$instance = new Profiles_SentFriendRequestUpdater(); }
        return self::$instance;
    }

    /**
     * Deletes all friend requests sent.
     */
    public function withdrawAll() {
        $this->run();
    }

    /**
     * Returns the name of the User attribute containing the start time of the update.
     *
     * @return string  the attribute name
     */
    protected function getStartAttributeName() {
        return 'sentFriendRequestUpdateStart';
    }

    /**
     * Relationship for the friend-request query.
     *
     * @return string  the contact relationship
     */
    protected function getRelationship() {
        return XN_Profile::FRIEND_PENDING;
    }

        /**
     * Processes a batch of friend requests.
     *
     * @param $screenNames array  the usernames for the friend requests
     * @param $args array  arguments passed to the async job
     */
    protected function update($screenNames, $args) {
        Profiles_FriendHelper::instance()->withdrawFriendRequests($screenNames);
    }

}
