<?php
/** $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  Extension of the NF_Controller caching
 *
 **/
class XG_LockingCacheController extends XG_BrowserAwareController {

    // Checks for the cached version, and if the data is about to expire, lets one person go in and update it
    protected function _getCachedData($cacheKey, $expire, $lockName) { # string | NULL
        try {
            $res = XN_Cache::get($cacheKey, intval($expire*1.1));
        } catch (Exception $e) {
            return NULL;
        }
        if ( !$res[$cacheKey] || !is_array($res = unserialize($res)) ) { // if no data or invalid data format, let everybody go through
            return NULL;
        }
        if ( (time() - $res[0]) < $expire * 0.9 ) { // if the data is fresh, just show it
            return $res[1];
        }
        // Data is about to expire, but we still have the staled data
        /*
        Disable "no lock name" case
        if ( !$lockName ) { // No guard protection is required
            return NULL;
        }
        */
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        if ( $locked = XG_LockHelper::lock($lockName, 0) ) {
            // lock will be automatically released at the end of request,
            // but we should call XG_LockHelper::unlock($lockName); if we can...
            return NULL;
        }
        return $res[1]; // display the staled data
    }

    /**
     * Sets the action caching.
     *
     * @param $method		string		Pass __METHOD__ and save us from calling debug_backtrace()
     * @param $cacheKey		string		Cache key
     * @param $expire		int			Cache expiration in seconds
     *
     *  $lock = $cacheKey:lock
     *  If there are staled data in the cache and the lock cannot be obtained, this staled
     *	data will be returned, to allow the single process to update the cache.
     *	If there are no stale data or the data were expired more than $expire/10 seconds ago,
     *	callback is called.
     *
     *	Every cache entry is a serialized array: (createTimestamp, data)
     */
    protected function setLockingCaching($method, $cacheKey, $expire = 86400) {
        if (isset($_GET['xn_debug'])) { //  If xn_debug is on we don't want to cache the output
            return;
        }
        $cacheKey = $method . '-' . $cacheKey;
        if (NULL !== ($text = $this->_getCachedData($cacheKey, $expire, "$cacheKey:lock"))) {
            echo $text;
            throw new NF_Exception_Cache_Hit();
        }
        list($class, $function) = explode('::', $method);
        $template = mb_substr($function, mb_strlen('action_'));
        $controller = $this->_controllerToUrl($class);
        $this->_disposition = array('_doRender', $template, $controller,
            array(self::CACHE_AT_LOCATION => array($cacheKey, NULL)));
    }

    protected function _doRender($template, $controller, $behaviors = null, $args = null) {
        $start = NF::outputTime('Entering NF_Controller::_doRender');
        $strategy = self::_getRenderingStrategy();
        $strategyFunction = array($this, '_doRender_' . $strategy);

        if (! is_callable($strategyFunction)) {
            $strategyFunction = array($this, '_doRender_html');
        }
        try {
            $start2 = NF::outputTime("In NF_Controller::_doRender(), calling " . $strategyFunction[1], $start);
            ob_start();
            $retval = call_user_func($strategyFunction, $template, $controller, $behaviors, $args);
            $output = ob_get_clean();
            NF::outputTime("In NF_Controller::_doRender(), completed " . $strategyFunction[1], $start2);
            /* $behaviors[self::CACHE_AT_LOCATION] is a two element array:
             * 0: the path or cache key to store the data in the cache at
             * 1: the invalidation conditions (if any)
             */
            if (isset($behaviors[self::CACHE_AT_LOCATION])) {
                $start3 = NF::outputTime('In NF_Controller::_doRender(), writing output to cache', $start);
                $cacheFullPath = $behaviors[self::CACHE_AT_LOCATION][0];
                try {
                    /* XN_Cache::put() gets the labels as the third argument. This matches with
                     * how the labels are passed to setCaching() (and hence set in $behaviors):
                     * null = no labels, single string  = 1 label, array = multiple labels
                     */
                    XN_Cache::put($cacheFullPath, serialize(array(time(),$output)), $behaviors[self::CACHE_AT_LOCATION][1]);
                } catch (Exception $e) {
                    error_log("Can't put to cache at $cacheFullPath: {$e->getMessage()}");
                }
                NF::outputTime('In NF_Controller::_doRender(), finished writing output to cache', $start3);
            }
            $start4 = NF::outputTime('In NF_Controller::_doRender(), writing output to stream', $start);
            echo $output;
            NF::outputTime('In NF_Controller::_doRender(), finished writing output to stream', $start4);
            if ($strategy == 'json' || $strategy == 'htmljson') {
                //  We intentionally delayed exiting after JSON output in case
                //   we needed to cache the output.  We need to exit now to
                //   prevent further processing or output.
                exit;
            }
            return $retval;
        } catch (Exception $e) {
            // The return value from error() must be returned so that if an
            // exception is thrown in a partial, renderPartial() can terminate
            // the rendering of the rest of the template
            NF::logException($e);
            return $this->error($e);
        }
        NF::outputTime('Leaving NF_Controller::_doRender()', $start);
    }
}
?>
