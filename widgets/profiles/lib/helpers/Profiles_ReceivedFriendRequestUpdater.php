<?php

W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_AbstractFriendRequestUpdater.php');
W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');

/**
 * Processes all received friend requests using an async job.
 * Ensures that no more than one async job is operating on received friend requests.
 */
class Profiles_ReceivedFriendRequestUpdater extends Profiles_AbstractFriendRequestUpdater {

    /** The singleton instance of this class. */
    private static $instance;

    /**
     * Returns the singleton instance of this class.
     *
     * @return Profiles_ReceivedFriendRequestUpdater  the singleton
     */
    public static function instance() {
        if (! self::$instance) { self::$instance = new Profiles_ReceivedFriendRequestUpdater(); }
        return self::$instance;
    }

    /**
     * Friends the current user to all who have sent her friend requests
     */
    public function acceptAll() {
        $this->run(array('action' => 'acceptAll'));
    }

    /**
     * Deletes all friend requests received.
     */
    public function ignoreAll() {
        $this->run(array('action' => 'ignoreAll'));
    }

    /**
     * Returns the name of the User attribute containing the start time of the update.
     *
     * @return string  the attribute name
     */
    protected function getStartAttributeName() {
        return 'receivedFriendRequestUpdateStart';
    }

    /**
     * Relationship for the friend-request query.
     *
     * @return string  the contact relationship
     */
    protected function getRelationship() {
        return XN_Profile::GROUPIE;
    }

        /**
     * Processes a batch of friend requests.
     *
     * @param $screenNames array  the usernames for the friend requests
     * @param $args array  arguments passed to the async job
     */
    protected function update($screenNames, $args) {
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        if ($args['action'] === 'acceptAll') {
            Profiles_FriendHelper::instance()->acceptFriendRequests($screenNames, $args['batchNumber'] == 0, count(XG_LangHelper::arrayFlatten($args['friendRequestBatches'])));
        }
        if ($args['action'] === 'ignoreAll') {
            Profiles_FriendHelper::instance()->ignoreFriendRequests($screenNames);
        }
    }

}
