<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  SYNOPSIS:
 *
 *      function before_create($command, $event) {
 *          $command->addLock("event-$event->id");
 *          $command->myOwnVar = 1;
 *      }
 *      function on_create($command, $event) {
 *          if ($command->myOwnVar) {
 *              Events_EventHelper::update($event);
 *          }
 *      }
 *      function after_create($command, $event) {
 *          writeLog("Event#$event->id was successfully created");
 *      }
 *
 *      Events_EventCommand::register('event.save','before_create', 'on_create','after_create');
 *      Events_EventCommand::register('event.save','before_create2', 'on_create2','after_create2');
 *      Events_EventCommand::execute('event.save', $event);
 *
 *  DESCRIPTION:
 *
 *      Command object with locking support (a mini-transaction).
 *      The empty command (i.e. the command without handlers) does nothing. Interested objects must register their handlers for COMMAND-ID.
 *      Upon command execution the following operations will be performed:
 *          - all "before" handlers will be called
 *          - all locks will be obtained (if any lock cannot be obtained, the command fails)
 *          - all "on" handlers will be called
 *          - all obtained locks will be released
 *          - all "after" handlers will be called
 *
 **/
class Events_EventCommand {

    /** Handler callbacks, keyed by command ID and before|on|after. */
    static protected    $commands       = array();

    /**
     *  Adds a handler/listener for the command.
     *  Every handler is a function($command, ..args..).
     *      "$command"  is an Events_EventCommand instance
     *      "args"      are arguments passed to ::execute(). (Not an array.)
     *  Return value is ignored. Throw an Exception if you want to stop execution.
     *
     *  @param      $commandId  string      CommandID (app-specifc)
     *  @param      $before     callback    "Before" handler or NULL
     *  @param      $on         callback    "On" handler or NULL
     *  @param      $after      callback    "After" handler or NULL
     *  @return     void
     */
    public static function register($commandId, $before, $on, $after = NULL) {
        self::$commands[$commandId]['before'][] = $before;
        self::$commands[$commandId]['on'][]     = $on;
        self::$commands[$commandId]['after'][]  = $after;
    }

    /**
     *  Executes the given command.
     *
     *  @param      $commandId  string      CommandID (app-specific)
     *  @param      ..args..    mixed       Any arbitrary arguments. Will be passed to the handlers.
     *  @throw      Events_LockException    if some locks cannot be obtained
     *  @return     void
     */
    public static function execute($commandId /*..args..*/) {
        $args       = func_get_args();
        $cid        = $args[0];
        if (!isset(self::$commands[$cid])) {
            trigger_error("Attempt to execute unexisting command #$cid;",E_USER_WARNING);
            return;
        }
        $args[0]    = new Events_EventCommand($args[0]);
        $handlers   =& self::$commands[$cid];

        // Exec "before" handlers
        foreach ($handlers['before'] as &$h) {
            if ($h) call_user_func_array($h,$args);
        }

        // Try to obtain all locks
        sort($locks = array_keys($args[0]->_locks),SORT_STRING);
        for ($i = 0; $i<count($locks); $i++) {
            if (!XG_LockHelper::lock( $locks[$i] )) {
                for($j = $i-1; $j>=0; $j--) {
                    XG_LockHelper::unlock( $locks[$j] );
                }
                throw new Events_LockException("Cannot obtain lock #".$lock[$i].";");
            }
        }

        // Exec "on" handlers
        $ex = NULL;
        try { foreach ($handlers['on'] as &$h) { if ($h) call_user_func_array($h,$args); }
        } catch(Exception $e) { $ex = $e; }
        foreach ($locks as $l) {
			XG_LockHelper::unlock($l);
        }
        if ($ex) {
            throw $ex;
        }

        // Exec "after" handlers
        foreach ($handlers['after'] as &$h) {
            if ($h) call_user_func_array($h,$args);
        }

        return;
    }

    /**
     * Clears the internal state. For testing purposes only.
     */
    public static function _reset() { # void
        self::$commands     = array();
    }

//** Object methods
    /** Locks associated with this command, of the form lock-name => 1. */
    protected $_locks = array();

    /**
     *  Constructor
     */
    protected function  __construct($commandId) {
        $this->_command = $commandId;
    }

    /**
     *  Adds lock to the list of locks for this command.
     *
     *  @return     void
     */
    public function addLock($lockName) { # void
        $this->_locks[$lockName] = 1;
    }
}

/**
 * An Exception that is thrown when a lock cannot be obtained.
 */
class Events_LockException extends Exception {
}
?>
