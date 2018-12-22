<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *   Replacement for XG_Cache::lock()/XG_Cache::unlock().
 *   Features:
 *   	- more reliable locking (~0% race condition probability)
 *   	- obtained locks are automatically released upon script termination.
 *   	- blocking locks are supported (+ timeouts)
 *
 **/

class XG_LockHelper {
    /** Active locks, of the form lock-name => 1. */
    static protected    $locks          = array();

    /** Whether the _unlockAll shutdown handler has been registered. */
    static protected    $isReg          = false;

    /** Time between attempts to obtain a lock, in seconds. */
	static protected    $waitInterval    = 0.1;

	/** Default lock waiting timeout, in seconds. The number of attempts to get lock = waitTimeout / waitInterval */
	static protected    $waitTimeout     = 5;

    /** Maximum lifetime of a lock, in seconds. After this time has elapsed, obtained locks are treated as dead and are removed. */
    static protected    $expiredTimeout = 60;

    /** Name of the "repair" lock, obtained when removing an expired lock. */
    static protected    $expiredLockName= 'XG_LockHelper::internal-fg4bwe095buj459v';

	/** The XN_Cache used for obtaining locks. */
    static protected    $cache;

    /**
     * Attempts to obtain a lock with the given name.
     *
     * @param   $lock   		string  Name of the lock
	 * @param	$waitTimeout	float	Wait timeout in seconds. If NULL, default timeout is used. 0 - do not wait
     * @return  boolean         		Whether the lock was successfully obtained
     */
    public static function lock($lock, $waitTimeout = NULL) { # bool
        if (!self::$cache) {
            self::$cache = new XN_Cache;
        }
        if (isset(self::$locks[$lock])) {
			error_log("Attempt to obtain the already obtained lock `$lock'. URL is `$_SERVER[REQUEST_URI]'");
        	return true;
		}
		$ok = 0;
		$now = microtime(true);
		$retries = intval((NULL === $waitTimeout ? self::$waitTimeout : $waitTimeout) / self::$waitInterval);
        for ($i = 0; $i<=$retries; $i++) {
            if (self::$cache->insert($lock, $now)) {
                $ok = 1;
                break;
            }
            if (NULL === ($time = self::$cache->get($lock))) {  // Hmm.. Has the record just disappeared? Do not wait!
                continue;
            }
            if ($time < $now - self::$expiredTimeout) { // Is lock expired? Try to obtain the "repair" lock.
                if (!$repair = self::$cache->insert(self::$expiredLockName, $now)) {    // If we CANNOT obtain the repair lock
                    $rtime = self::$cache->get(self::$expiredLockName);                 // lets see when this lock was set.
                    if ($rtime && $rtime < $now - self::$expiredTimeout) {          // expired?
                        self::$cache->remove(self::$expiredLockName);
                        $rtime = 0;
                    }
                    if (!$rtime) {                                                  // disappeared or removed?
                        $repair = self::$cache->insert(self::$expiredLockName, $now);
                    }
                }
                if ($repair) {
                    if ($time === self::$cache->get($lock)) {
                        self::$cache->remove($lock);
                        self::$cache->remove(self::$expiredLockName);
                        continue;
                    }
                }
                // fallback to the waiting
            }
 			if ($i != $retries) { // do not wait on the last step
             	usleep(self::$waitInterval*1000000);
 			}
        }
        if (!$ok) {
            return false;
        }
        self::$locks[$lock] = 1;
        if (!self::$isReg) {
            self::$isReg = 1;
            register_shutdown_function(__CLASS__.'::_unlockAll');
        }
        return true;
    }

    /**
     * Releases the lock with the given name.
     *
     * @param   $lock   string  Name of the lock
     */
    public static function unlock($lock) { # void
        if (isset(self::$locks[$lock])) {
            self::$cache->remove($lock);
            unset(self::$locks[$lock]);
        }
    }

    /**
     * Releases all locks. Used to handle unexpected script terminations.
     */
    public static function _unlockAll() { # void
        foreach (self::$locks as $l=>$tmp) {
            self::$cache->remove($l);
        }
    }
}
?>
