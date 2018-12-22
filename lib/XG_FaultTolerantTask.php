<?php

/**
 * A sequence of operations protected by various strategies to handle
 * exceptions, errors, fatal errors, and timeouts.
 *
 * Example:
 *     $task = new XG_FaultTolerantTask('myErrorCallback');
 *     $task->add(new XG_FaultTolerantOperation('sendInvitation'));
 *     $task->add(new XG_FaultTolerantOperation('sendFriendRequest'));
 *     $task->execute();
 *
 * @see XG_FaultTolerantTaskTest
 */
class XG_FaultTolerantTask {

    /** Indicator that the task has not been executed. */
    const CREATED = 'created';

    /** Indicator that the task is running. */
    const EXECUTING = 'executing';

    /** Indicator that the task has finished or has been aborted. */
    const STOPPED = 'stopped';

    /** PHP callback to call when an error occurs. */
    private $errorCallback;

    /** XG_FaultTolerantOperations to execute. */
    private $operations = array();

    /** XG_FaultTolerantOperations that were not executed or did not finish executing. */
    private $incompleteOperations = array();

    /** Index of the currently executing operation. */
    private $i;


    /**
     * Creates an XG_FaultTolerantTask. If an error occurs, the callback
     * will be called with an array of incomplete operations.
     *
     * @param $errorCallback mixed  a PHP callback, called when an error occurs.
     */
    public function __construct($errorCallback) {
        $this->status = self::CREATED;
        $this->errorCallback = $errorCallback;
        register_shutdown_function(array($this, 'onAbort'));
    }

    /**
     * Adds an operation to the queue.
     *
     * @param $callback mixed  a PHP callback
     * @param $args array  arguments to apply to the callback
     * @param $metadata array  information to attach to the operation
     */
    public function add($callback, $args = array(), $metadata = array()) {
        $this->operations[] = new XG_FaultTolerantOperation($callback, $args, $metadata);
        return $this;
    }

    /**
     * Runs each operation in sequence.
     *
     * @param $timeout integer  number of seconds before terminating the script, or null for no timeout.
     */
    public function execute($timeout) {
        if ($this->status != self::CREATED) { throw new Exception('XG_FaultTolerantTask already executed.'); }
        $start = time();
        $this->status = self::EXECUTING;
        set_error_handler(array($this, 'onError'), E_ERROR|E_PARSE|E_CORE_ERROR|E_COMPILE_ERROR|E_USER_ERROR|E_RECOVERABLE_ERROR);
        $this->i = 0;
        while ($this->i < count($this->operations)) {
            if ($timeout === 0 || $timeout < 0) {
                $this->onAbort();
                return;
            }
            if (isset($timeout) && (time() - $start > $timeout)) {
                $this->onAbort();
                return;
            }
            try {
                $this->operations[$this->i]->execute();
            } catch (Exception $e) {
                error_log($e->getMessage() . ' ' . $e->getTraceAsString());
                $this->incompleteOperations[] = $this->operations[$this->i];
            }
            $this->i++;
        }
        $this->status = self::STOPPED;
        if ($this->incompleteOperations) { $this->callErrorCallback(); }
        restore_error_handler();
    }

    /**
     * Calls the error callback, passing it the array of incomplete operations.
     */
    public function callErrorCallback() {
        call_user_func($this->errorCallback, $this->incompleteOperations);
    }

    /**
     * Called when an error occurs.
     */
    public function onError() {
        throw new Exception('onError');
    }

    /**
     * Called when the task ends normally or prematurely (due to a fatal error).
     */
    public function onAbort() {
        if ($this->status != self::EXECUTING) { return; }
        $this->status = self::STOPPED;
        restore_error_handler();
        $this->incompleteOperations = array_merge($this->incompleteOperations, array_slice($this->operations, $this->i));
        $this->callErrorCallback();
    }

    /**
     * Extracts the metadata for the given array of XG_FaultTolerantOperations.
     *
     * @param $operations array  an array of XG_FaultTolerantOperations
     * @return array  metadata for each operation
     */
    public static function extractMetadata($operations) {
        $metadata = array();
        foreach ($operations as $operation) {
            $metadata[] = $operation->metadata;
        }
        return $metadata;
    }

}

//#############################################################################
// XG_FaultTolerantOperation
//#############################################################################

/**
 * An atomic unit of work. An XG_FaultTolerantOperation has the following read-only properties:
 *
 *         callback -  a PHP callback
 *         args - arguments to apply to the callback
 */
class XG_FaultTolerantOperation {

    /** Read-only properties. */
    private $_data = array();

    /**
     * Creates an XG_FaultTolerantOperation.
     *
     * @param $callback mixed  a PHP callback
     * @param $args array  arguments to apply to the callback
     * @param $metadata mixed  information to attach to the operation
     */
    public function __construct($callback, $args = array(), $metadata = null) {
        $this->_data['callback'] = $callback;
        $this->_data['args'] = $args;
        $this->_data['metadata'] = $metadata;
    }

    /**
     * Provides read access to the property with the given name simulated as a
     * public instance variable accessed through the '->' operator.
     *
     * @param $name string  name of the property
     * @return mixed  value of the property
     */
    public function __get($name) {
        if (! array_key_exists($name, $this->_data)) { throw new XN_Exception("Invalid property name: '$name'"); }
        return $this->_data[$name];
    }

    /**
     * Runs the operation.
     */
    public function execute() {
        call_user_func_array($this->callback, $this->args);
    }

}
