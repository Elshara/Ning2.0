<?php

/**
 * Processes all sent or received friend requests using an async job.
 * Ensures that no more than one async job is operating on sent friend requests
 * (and similarly for received friend requests)
 */
abstract class Profiles_AbstractFriendRequestUpdater {

    /** Max lifetime of an async job, in seconds. Jobs older than this will be ignored. */
    const MAX_AGE = 3600;

    /** Number of friend requests processed per async task. */
    const BATCH_SIZE = 50;

    /** W_Content object for the current user. */
    protected static $user = NULL;

    /**
     * Constructs a new Profiles_AbstractFriendRequestUpdater.
     */
    protected function __construct() { }

    /**
     * Returns the name of the User attribute containing the start time of the update.
     *
     * @return string  the attribute name
     */
    protected abstract function getStartAttributeName();

    /**
     * Relationship for the friend-request query.
     *
     * @return string  the contact relationship
     */
    protected abstract function getRelationship();

    /**
     * Processes a batch of friend requests.
     *
     * @param $screenNames array  the usernames for the friend requests
     * @param $args array  arguments passed to the async job
     */
    protected function update($screenNames, $args) {} // Should be abstract, but that causes an error in Profiles_AbstractFriendRequestUpdaterTest [Jon Aquino 2008-08-05]

    /**
     * Returns whether this updater is currently running.
     *
     * @return boolean  whether the async job is running
     */
    public function isRunning() {
        return ! is_null($this->getStart());
    }

    /**
     * Returns the time at which the updater was started.
     *
     * @return integer  the Unix timestamp, or NULL if the updater is not running
     */
    protected function getStart() {
        $start = self::currentUser()->my->raw($this->getStartAttributeName());
        if (time() - $start > self::MAX_AGE) { return NULL; }; // A problem occurred. [Jon Aquino 2008-08-05]
        return $start;
    }

    /**
     * Sets the time at which the updater was started.
     *
     * @param start integer  the Unix timestamp
     */
    protected function setStart($start) {
        self::currentUser()->my->set($this->getStartAttributeName(), $start);
        self::currentUser()->save();
    }

    /**
     * Returns the current user.
     *
     * @return W_Content  the user object for the person currently signed in
     */
    private static function currentUser() {
        if (is_null(self::$user)) { self::$user = User::load(XN_Profile::current()); }
        return self::$user;
    }

    /**
     * Starts running the updater.
     *
     * @param $extraArgs array  additional arguments to pass to the async job
     */
    protected function run($extraArgs = array()) {
        if ($this->isRunning()) { throw new Exception('Updater already running (1863285374)'); }
        $this->setStart(time());
        $friendRequests = $this->getAllFriendRequests();
        $friendRequestBatches = array_chunk($friendRequests, XG_App::constant('Profiles_AbstractFriendRequestUpdater::BATCH_SIZE'));
        // Process the friendRequestBatches in series rather than in parallel, because the
        // job-completion callback isn't currently working (NING-7326) and isn't yet supported by XG_JobHelper. [Jon Aquino 2008-08-09]
        $tasks = array(array(array(__CLASS__, 'task_run'), array_merge($extraArgs, array(
                'friendRequestBatches' => $friendRequestBatches,
                'batchNumber' => 0,
                'start' => $this->getStart(),
                'class' => get_class($this)))));
        if (count($friendRequestBatches) == 0) { return; }
        if (count($friendRequestBatches) == 1) {
            try {
                $this->runTask($tasks[0][1]);
            } catch (Exception $e) {
                error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                // If we're not using async jobs, an and error occurs, call finished(). Otherwise the
                // Friend Requests and Requests Sent tabs will be hidden for an hour (MAX_AGE) then will come back,
                // confusing the user. [Jon Aquino 2008-09-03]
                $this->finished();
                throw $e;
            }
            $this->finished();
            return;
        }
        $this->createJob($tasks);
    }

    /**
     * Returns the screen names of all friend requests.
     *
     * @return array  the usernames
     */
    protected function getAllFriendRequests() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_NetworkSpecificFriendRequestHelper.php');
        return User::screenNames(Profiles_NetworkSpecificFriendRequestHelper::instance()->getFriendRequestContacts(XN_Profile::current()->screenName, $this->getRelationship()));
    }

    /**
     * Callback for the async job.
     *
     * @param $friendRequestBatches array  array of arrays of screen names
     * @param $batchNumber integer  the index of the batch of screen names to process
     * @param $start integer  time at which the updater was started
     * @param $class string  name of the subclass of Profiles_AbstractFriendRequestUpdater
     */
    public static function task_run($args) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_SentFriendRequestUpdater.php');
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ReceivedFriendRequestUpdater.php');
        $updater = call_user_func(array($args['class'], 'instance'));
        $updater->runTask($args);
    }

    /**
     * Callback for the async job.
     *
     * @param $friendRequestBatches array  array of arrays of screen names
     * @param $batchNumber integer  the index of the batch of screen names to process
     * @param $start integer  time at which the updater was started
     * @param $class string  name of the subclass of Profiles_AbstractFriendRequestUpdater
     */
    protected function runTask($args) {
        if ($args['start'] != $this->getStart()) { return; } // Expired [Jon Aquino 2008-08-05]
        $this->update($args['friendRequestBatches'][$args['batchNumber']], $args);
        if ($args['batchNumber'] == count($args['friendRequestBatches']) - 1) {
            $this->finished();
            return;
        }
        ++$args['batchNumber'];
        $this->createJob(array(array(array(__CLASS__, 'task_run'), $args)));
    }

    /**
     * Creates a new async job and runs it.
     *
     * @see XG_JobHelper::create
     */
    protected function createJob($tasks) {
        if (count($tasks) != 1) { throw new Exception('Assertion failed (2058455390)'); }
        XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        XG_JobHelper::create($tasks, __FILE__);
    }

    /**
     * Called after the last friend requests have been updated.
     */
    protected function finished() {
        $this->setStart(NULL);
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_NetworkSpecificFriendRequestHelper.php');
        Profiles_NetworkSpecificFriendRequestHelper::instance()->invalidateFriendRequestsCache(XN_Profile::current()->screenName);
    }

}
