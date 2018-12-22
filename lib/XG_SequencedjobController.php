<?php
/**
 * Dispatches requests for sequenced jobs. A sequenced job is an XN_Job that
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
 * @see XG_JobHelper::create() for a more convenient way to create async jobs.
 */
class XG_SequencedjobController extends W_Controller {

    /** Indicates that the variable has not been set. */
    const UNINITIALIZED = '[UNINITIALIZED]';

    /** Whether to create a new job after the action finishes. */
    protected $continueJob = self::UNINITIALIZED;

    /** Names of instance variables in existence before the action runs. */
    private $originalInstanceVariableNames = null;

    /**
     * Code that is run before each action.
     */
    protected function _before() {
        $this->header('HTTP/1.0 500 Internal Error');
        $json = new NF_JSON();
        error_log('XG_SequencedjobController: ' . XG_HttpHelper::currentUrl() . ' ' . $json->encode($_POST));
        $this->originalInstanceVariableNames = array_keys(get_object_vars($this));
        foreach ($this->getPostVariables() as $key => $value) {
            if (in_array($key, $this->originalInstanceVariableNames)) { throw new Exception($key . ' already exists (475667404)'); }
            $this->{$key} = $value;
        }
        if (! $this->allowChaining()) { throw new Exception('Chaining not allowed (2117210943)'); }
        XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        if (! XG_JobHelper::checkSecretKey()) { throw new Exception('Secret key check failed (1780502977)'); }
    }

    /**
     * Code that is run after each action.
     */
    protected function _after() {
        if ($this->continueJob === self::UNINITIALIZED) { throw new Exception('Sequenced-job action should call setContinueJob() (1228725479)'); }
        if ($this->continueJob) {
            $newInstanceVariableNames = array_diff(array_keys(get_object_vars($this)), $this->originalInstanceVariableNames);
            $newInstanceVariables = array();
            foreach ($newInstanceVariableNames as $name) { $newInstanceVariables[$name] = $this->{$name}; }
            $route = $this->getRequestedRoute();
            $this->start($route['widgetName'], $route['actionName'], $newInstanceVariables);
        }
        $this->header('HTTP/1.0 200 OK');
    }

    /**
     * Sets whether to create a new job. Every action should call this function.
     */
    protected function setContinueJob($continueJob) {
        $this->continueJob = $continueJob;
    }

    /**
     * Sends a raw HTTP header. Overridden by unit tests.
     *
     * @param $string string  the header string
     */
    protected function header($string) {
        header($string);
    }

    /**
     * Returns $_POST. Overridden by unit tests.
     *
     * @return array  the query-string variables
     */
    protected function getPostVariables() {
        return $_POST;
    }

    /**
     * Determines if an asynchronous job should be allowed to create another job.
     * Overridden by unit tests.
     *
     * @return  boolean true if chaining is allowed at this time.
     */
    protected function allowChaining() {
        XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        return XG_JobHelper::allowChaining();
    }

    /**
     * Returns the requested top-level route. Overridden by unit tests.
     *
     * @return array  the widgetName, controllerName, and actionName.
     */
    protected static function getRequestedRoute() {
        return XG_App::getRequestedRoute();
    }

    /**
     * Initiates a sequenced job. Overridden by unit tests.
     *
     * @param $widgetName string  the name of the widget, e.g., profiles
     * @param $actionName string  the name of the action, e.g., buildBlogArchive
     * @param $args array  names and values to pass to the action as instance variables
     */
    protected function start($widgetName, $actionName, $args) {
        XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        XG_JobHelper::start($widgetName, $actionName, $args);
    }

}
