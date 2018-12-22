<?php

/**
 * Useful functions for working with XN_Jobs and XN_Tasks.
 */
class  XG_JobHelper {

    /** Name of the config parameter and URL parameter for the secret key used by sequenced jobs. */
    const KEY = 'asyncJobKey'; // Do not change this, as it is being used by existing async jobs [Jon Aquino 2008-04-15]

    /** Name of the config parameter for the date on which the 24-hour grace period begins. */
    const GRACE_PERIOD_KEY = 'asyncJobKeyGracePeriodStart';


    /**
     * Initiates a sequenced job. A sequenced job is an XN_Job that
     * will create a new XN_Job if it cannot complete its work. This results in a
     * sequence (or "chain") of jobs.
     *
     * To create a sequenced job, ensure that your widget has a controller named
     * SequencedjobController, which extends XG_SequencedjobController.
     * Add an action that does the work; it should call setContinueJob() to indicate
     * whether to create a new job or to stop.
     *
     * To run the sequenced job, call XG_JobHelper::start(widget, action, args).
     * The args will be passed to your action as instance variables. If you modify
     * or add new instance variables, the new values will be passed to the next job.
     *
     * @param $widgetName string  the name of the widget, e.g., profiles
     * @param $actionName string  the name of the action, e.g., buildBlogArchive
     * @param $args array  names and values to pass to the action as instance variables
     *
     * @see XG_JobHelper::create() for a more convenient way to create async jobs.
     */
    public static function start($widgetName, $actionName, $args = array()) {
        $args[self::KEY] = self::getSecretKey();
        self::run(W_Cache::getWidget($widgetName)->buildUrl('sequencedjob', $actionName), $args);
    }

    /**
     * Determines if an asynchronous job should be allowed to create another job.
     * Should be used before scheduling a potentially long loop of jobs so that they can be manually stopped in emergencies.
     *
     * @return  boolean true if chaining is allowed at this time.
     */
    public static function allowChaining() {
        if (file_exists('xn-app://socialnetworkmain/xn/XG_JobHelper.stopChaining.txt')) { return false; } // global check
        if (file_exists(NF_APP_BASE . '/xn_private/XG_JobHelper.stopChaining.txt')) { return false; } // local check
        return true;
    }

    /**
     * Creates and executes an asynchronous job.
     *
     * Example:
     *     XG_JobHelper::run('http://networkname.ning.com/foo', array('name' => 'Paul', 'city' => 'Victoria'));
     *
     * If $type is not set, it is chosen automatically as follows:
     *     - If $content is an array, the $type will be application/x-www-form-urlencoded.
     *     - If $content is valid XML, the $type will be application/xml.
     *     - Otherwise, the $type will be application/octet-stream.
     *
     * @param $executionCallback string  the URL to post to
     * @param $content array|string  the data for the POST body
     * @param $type string  the MIME type of the data, or null to choose one automatically.
     */
    private static function run($executionCallback, $content = array(), $type = null) {
        $job = XN_Job::create();
        $job->addTask(XN_Task::create(XG_SecurityHelper::addCsrfToken($executionCallback), $content, $type));
        $result = $job->save();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        if (Index_InvitationHelper::isErrorArray($result)) { throw new Exception(Index_InvitationHelper::errorMessage(key($result))); }
    }

    /**
     * Returns the secret key for sequenced jobs to use.
     *
     * @return string  a string used to authorize requests to XG_SequencedjobControllers
     */
    public static function getSecretKey() {
        return self::getSecretKeyProper(W_Cache::getWidget('main'));
    }

    /**
     * Returns the secret key for sequenced jobs to use.
     *
     * @param $mainWidget W_Widget  the main widget, or a mock object for testing
     * @param $lockId string  ID to use for the advisory lock
     * @return string  a string used to authorize requests to XG_SequencedjobControllers
     */
    protected static function getSecretKeyProper($mainWidget, $lockId = 'generate-async-job-key') {
        // Lock for at least a couple of minutes, because of NFS caching [Jon Aquino 2008-05-02]
        if (! $mainWidget->privateConfig[self::KEY] && XG_Cache::lock($lockId, 300)) {
            $mainWidget->privateConfig[self::KEY] = self::instance()->generateSecretKey();
            $mainWidget->saveConfig();
        }
        return $mainWidget->privateConfig[self::KEY];
    }

    /**
     * Returns whether the current request specifies the correct secret key as a POST variable.
     *
     * @return boolean  whether the security check passes
     */
    public static function checkSecretKey() {
        return self::checkSecretKeyProper(W_Cache::getWidget('main'), $_POST[self::KEY], time());
    }

    /**
     * Returns whether the current request specifies the correct secret key as a POST variable.
     *
     * @param $mainWidget W_Widget  the main widget, or a mock object for testing
     * @param $submittedKey  the key specified as a POST variable
     * @param $time  the current Unix timestamp
     * @return boolean  whether the security check passes
     */
    protected static function checkSecretKeyProper($mainWidget, $submittedKey, $time) {
        if ($submittedKey == self::getSecretKeyProper($mainWidget)) { return true; }
        $gracePeriod = 24 * 3600; // Allow existing async jobs to execute [Jon Aquino 2008-04-15]
        return $time - strtotime($mainWidget->privateConfig[self::GRACE_PERIOD_KEY]) < $gracePeriod;
    }

    /** Singleton instance of this class. */
    protected static $instance;

    /**
     *  Returns the singleton instance of this class.
     *
     *  @return Events_BulkHelper   the BulkHelper, or a mock object for testing
     */
    private static function instance() {
        if (! self::$instance) { self::$instance = new XG_JobHelper(); }
        return self::$instance;
    }

    /**
     * Generates a secret key for sequenced jobs to use.
     *
     * @return string  a string used to authorize requests to XG_SequencedjobControllers
     */
    protected function generateSecretKey() {
        return md5(uniqid(mt_rand(), true));
    }

    /** Async jobs data. For unit testing. */
    static protected $_jobs = array();

    /**
     * 	An alternative interface to XN_Job. Just write your method with name "task_SOMETHING" and run it through this function.
     *
     *  Creates and saves a new async job. Tasks will be executed in parallel.
     *  TASK_DEF is a list where the first item is a callback and the rest are positional parameters.
     *  Callbacks must have the format:
     *      array(CLASS,task_METHOD)    if method name does not start with "task_", an error is generated.
     *  task_METHOD signature:
     *      function( ..positional-params..) void
     *
     *  @param      $tasks   	list<TASK_DEF>    	List of tasks to run.
     *  @param		$fileToLoad string				Additional file to load before task is executed. Pass __FILE__ if your code is not in a file
     *  											that is loaded automatically.
     *  @return     void
     */
    public static function create(array $tasks, $fileToLoad = '') {
        if (!$tasks) {
            throw new Exception("Empty job! (2359672867)");
        }
        if (defined('UNIT_TESTING')) {
            self::$_jobs[] = array('tasks' => $tasks);
        } else {
            $asyncKey = self::getSecretKey();
            $url = XG_SecurityHelper::addCsrfToken(W_Cache::getWidget('main')->buildUrl('index','asyncJob'));
            $job = XN_Job::create();
            foreach ($tasks as $task) {
                $job->addTask( XN_Task::create($url, array(self::KEY => $asyncKey, 'extraFile' => $fileToLoad, 'task'=>$task) ) );
            }
            $job->save();
        }
    }

    /**
     *  Dispatches task. $args is typically $_REQUEST.
     *
     *  @param      $asyncKey	string                  Secret async key
     *  @param		$extraFile	string					Extra file to load (only files within XG_App::includePrefix() are allowed)
     *  @param      $task       list<method, ..args..>  TASK_DEF (see above)
     *  @return     void
     */
    public static function dispatch($args) {
        if ( !defined('UNIT_TESTING') ) {
            if ( !self::checkSecretKeyProper(W_Cache::getWidget('main'), $args[self::KEY], time()) ) {
                // BACKWARD COMPATIBILITY: As a last resort try to use the old events async key.
                // This check can be removed after 3.3 release.
                $asyncKey = '';
                try {
                    $asyncKey = W_Cache::getWidget('events')->privateConfig['asyncKey'];
                } catch(Exception $e) { }
                if (!$asyncKey || $args['asyncKey'] != $asyncKey) {
                    throw new Exception("asyncKey mismatch");
                }
            }
            // Try to load extraFile
            if ($args['extraFile']) {
                $args['extraFile'] = realpath($args['extraFile']);
                $prefix = XG_App::includePrefix();
                if ( strncmp($prefix, $args['extraFile'], mb_strlen($prefix)) == 0 ) {
                    XG_App::includeFileOnce($args['extraFile'], false);
                } else {
                    throw new Exception("File `$args[extraFile]' is located outside of the app root.");
                }
            }
        }
        $task = $args['task'];
        $callback = array_shift($task);
        if ( !is_array($callback) || count($callback)!=2 || !is_string($callback[0]) || !preg_match('/^task_\w+$/u',$callback[1]) ) {
            throw new Exception("Malformed task handler:".var_export($callback,TRUE));
        }
        // run task
        $start = microtime(true);
        call_user_func_array($callback, $task);
        error_log("AsyncJob $callback[0]::$callback[1](".join(", ",$task)."): done in ".sprintf('%.4f',microtime(true)-$start));
    }

    /**
     * Executes all scheduled async jobs. For unit testing only.
     */
    public static function _dispatchAll() { # void
        while (count(self::$_jobs)) { // cannot use foreach, because jobs can create other jobs
            $job = array_shift(self::$_jobs);
            foreach ($job['tasks'] as $task) {
                self::dispatch(array('task' => $task));
            }
        }
    }

}
