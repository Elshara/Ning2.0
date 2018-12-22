<?php

// TODO: Rename this class to XG_CachedQuery (see Clearspace DOC-2081) [Jon Aquino 2008-09-11]

XG_App::includeFileOnce('/lib/XG_Cache.php');

/**
 * An variation of XN_Query that caches query results
 *
 *  When to use XG_Query vs. XN_Query:
 *
 *   - Is the query run infrequently? (e.g. adding/deleting content). Use XN_Query.
 *   - Does the query have a FRIENDS() filter? (e.g. addVisibilityFilter).
 * Use XG_Query with XG_QueryHelper::setMaxAgeForFriendsQuery()
 *   - Is one of the filters "order N"? That is, does the number of possibilities for the filter
*  scale with the amount of content or the number of members? (e.g. username, content ID,
*  various $_GET values). Use XG_Query *IF* XG_Cache::cacheOrderN() - this is a config setting.
 *   - Otherwise, use XG_Query.
 *
 *   - If in doubt, use XN_Query.
 */
class XG_Query {

    /** The actual XN_Query object is stored as a property of XG_Query
     * rather than having XG_Query extend XN_Query so that the XG_Query
     * constructor can accept an XN_Query object and cache its operations.
     */
    protected $_query = null;
    /** We also need to keep track of the query metadata so we can provide it after a cached query */
    protected $queryData = array('totalCount' => null, 'resultFrom' => null, 'resultTo' => null);

    protected $_maxAge = null;
    protected $_keys = array();

    /** Was the last execute/uniqueResult from the cache? */
    protected $_cached = null;


    /** The maximum estimated serialized size we'll pass to the cache (see BAZ-1909)
     * calculated on first use */
    protected static $_maxResultSerializedSize = null;
    protected static $_memMultipliers = array('k' => 1024, 'm' => 1048576, 'g' => 1073741824);
    protected static $_serializedSizeMemoryRatio = 0.9;

    /** Creation and construction works a bit differently so we can store the
     * XN_Query object internally
     */
    public static function create($subjectOrQuery) {
        return new XG_Query($subjectOrQuery);
    }

    private function __construct($subjectOrQuery) {
        if ($subjectOrQuery instanceof XN_Query) {
            $this->_query = $subjectOrQuery;
        } else {
            $this->_query = XN_Query::create($subjectOrQuery);
        }
    }

    /**
     * XN_Query methods that get special handling. They must be defined as
     * all lowercase in the array so that mixed-case invocation works.
     */
     protected static $methods = array(
        /** These methods get proxied over to the XN_Query instance or have saved results
          * returned if the results are cached */
        'proxyCached' => array('getresultfrom' => true, 'getresultto' => true, 'getresultsize' => true, 'gettotalcount' => true),
        /** These methods are proxied, but static */
        'proxyStatic' => array('friends' => true),
        /** These methods get proxied over but then we return $this instead of the method's return value
          * (which is the XN_Query instance) */
        'returnThis' => array('filter' => true,'order' => true,'rollup' => true,'begin' => true,'end' => true,'alwaysreturntotalcount' => true),
        /** These methods get proxied over, but wrapped with caching logic */
        'cache' => array('execute' => true, 'uniqueresult' => true)
        );
    public function __call($method, $args) {
        $lowercaseMethod = mb_strtolower($method);
        // This if/else chain is arranged in rough order of use frequency
        if (isset(self::$methods['returnThis'][$lowercaseMethod])) {
            $this->_query = call_user_func_array(array($this->_query, $method), $args);
            return $this;
        }
        else if (isset(self::$methods['cache'][$lowercaseMethod])) {
            return $this->cache($method, $args);
        }
        else if (isset(self::$methods['proxyCached'][$lowercaseMethod])) {
            if (is_array($this->_cached)) {
                $property = mb_substr($lowercaseMethod, 3);
                return $this->_cached[$property];
            } else {
                return call_user_func(array($this->_query, $method));
            }
        }
        else if (isset(self::$methods['proxyStatic'][$lowercaseMethod])) {
            return call_user_func_array(array('XN_Query', $method), $args);
        }
        throw new Exception("Unknown method: XG_Query::$method");
    }

    /**
     * Methods that are copies of those in XN_Query, but that we need to duplicate
     * here so that "$this" refers to the XG_Query object rather than the internal
     * XN_Query object stored in $this->_query */
    public function debugString() {
        $debugString = $this->_query->debugString();
        $cachedString = is_null($this->_cached) ? 'not executed' : (is_array($this->_cached) ? 'yes' : 'no');
        $debugString .= "\nXG_Query:\n" .
              "  maxAge [" . $this->_maxAge . "]\n".
              "  keys [" . implode(', ', $this->_keys) . "]\n" .
              "  cached? [$cachedString]";
        return $debugString;
    }
    public function debugHtml() {
        return '<pre>' . xnhtmlentities($this->debugString()) . '</pre>';
    }
    public function printDebugHtml() {
        print $this->debugHtml();
        return $this;
    }

   /** New public methods to support cache-related options */

   /**
    * Specify the max age of a cached result that the caller is willing to accept
    *
    * @param $maxAge integer How old can an otherwise matching cached result be and
    *                        still be considered valid?
    * @return XG_Query Returns $this for method chaining
    */
    public function maxAge($maxAge) {
        $this->_maxAge = $maxAge;
        return $this;
    }

    /**
     * Return the max age allowable for the current query
     *
     * @return integer
     */
     public function getMaxAge() {
         return $this->_maxAge;
     }

    /**
     * Specify one or more invalidation keys to go with the cached result
     *
     * @param $keys mixed One or more strings or arrays of strings of invalidation keys
     * @return XG_Query Returns $this for method chaining.
     */
     public function setCaching() {
         $keys = func_get_args();
         $this->_keys = array();
         return call_user_func_array(array($this,'addCaching'), $keys);
     }

     /**
      * Specify one or more invalidation keys to be added on to the list that
      * goes with the cached result
      *
      * @param $keys mixed One or more strings or arrays of strings of invalidation keys
      * @return XG_Query Returns $this for method chaining.
      */
      public function addCaching() {
         $keys = func_get_args();
         foreach ($keys as $key) {
             if (is_array($key)) {
                 foreach ($key as $subkey) {
                     $this->addCaching($subkey);
                 }
             } else {
                 $this->_keys[] = $key;
             }
         }
         return $this;
      }

     /**
      * Return the invalidation keys attached to the cached result
      *
      * @return array
      */
      public function getCaching() {
          return $this->_keys;
      }

     /** keys -- what happens if you specify keys a and b and there is a cached copy
     * that matches but has keys c and d (or a and c) -- do you get the cached result?
     * does a new cached result get saved?
     */

    /**
     * Invalidate thing(s) from the cache. The argument(s) can be XG_Query objects, XN_Query
     * objects, string invalidation keys, or the special constant XG_Cache::INVALIDATE_ALL, which
     * clears everything (not just queries) from the persistent cache
     *
     * @param mixed ... What to invalidate
     */
    public static function invalidateCache() {
        /* If query caching is off, there's nothing to invalidate */
        if (self::getCacheStorage() == 'none') {
            return;
        }
        $args = func_get_args();
        foreach ($args as $arg) {
            if (is_array($arg)) {
                call_user_func_array(array('XG_Query', 'invalidateCache'), $arg);
            }
            if ($arg instanceof XG_Query) {
                if (self::getCacheStorage() == 'file') {
                    XG_Cache::remove($arg->_toCacheId());
                }
                else {
                    XN_Cache::remove($arg->_toCacheId());
                }
            }
            else if ($arg instanceof XN_Query) {
                if (self::getCacheStorage() == 'file') {
                    XG_Cache::remove(XG_Query::create($arg)->_toCacheId());
                }
                else {
                    XN_Cache::remove(XG_Query::create($arg)->_toCacheId());
                }
            }
            else {
                if (self::getCacheStorage() == 'file') {
                    XG_Cache::invalidate($arg);
                }
                else {
                    if ($arg == XG_Cache::INVALIDATE_ALL) {
                        $arg = XN_Cache::ALL;
                        XN_Cache::remove($arg);
                    } else {
                        XN_Cache::invalidate($arg);
                    }
                }
            }
        }

    }

    protected function cache($method, $args) {
        $cacheId = $this->_toCacheId();
        // Is there matching data in the cache?
        if (self::getCacheStorage() == 'file') {
            $data = XG_Cache::load($cacheId, $this->_maxAge);
            $miss = ($data instanceof XG_Cache_Miss);
        }
        else if (self::getCacheStorage() == 'api') {
            $data = XN_Cache::get($cacheId, $this->_maxAge);
            $miss = is_null($data);
        }
        /* If query caching is off, it's always a miss */
        else {
            $miss = true;
        }
        // If not...
        if ($miss) {
            // Do the query
            $results = call_user_func_array(array($this->_query, $method), $args);
            $dataToStore = array('results' => $results,
                                 'resultfrom' => $this->_query->getResultFrom(),
                                 'resultto' => $this->_query->getResultTo(),
                                 'resultsize' => $this->_query->getResultSize(),
                                 'totalcount' => null);
            try {
                $dataToStore['totalcount'] = $this->_query->getTotalCount();
            } catch (Exception $e) {
                // Swallow exception if total count isn't provided
            }

            if (self::getCacheStorage() == 'file') {
                // Make sure we know the max serialized size we'll tolerate
                self::setMaxResultSerializedSize();
                $estimatedSerializedSize = self::estimateSerializedSize($results);
                if ($estimatedSerializedSize < self::$_maxResultSerializedSize) {
                    // Put the results in the cache
                    XG_Cache::save($cacheId, $dataToStore, array('keys' => $this->_keys));
                } else {
                    error_log("Did not cache query because estimated result set size ($estimatedSerializedSize) is bigger than limit (".self::$_maxResultSerializedSize."): " . $this->_query->debugString());
                }
            }
            else if (self::getCacheStorage() == 'api') {
                try {
                    XN_Cache::put($cacheId, $dataToStore, count($this->_keys) ? $this->_keys : null);
                } catch (Exception $e) {
                    error_log("XG_Query cache put failed: " . $e->getMessage());
                }
            }

            // Mark that the query had to be executed
            $this->_cached = false;

            // And return the results
            return $results;
        }
        // If there was matching data in the cache,
        else {
            // Mark that the query was found in the cache
            $this->_cached = $data;
            // Return it!
            return $this->_cached['results'];
        }
    }

    protected function _toCacheId() {
        if (strcasecmp($this->subject,'Contact') == 0) {
            $url = $this->_query->_toContactEndpoint();
        } else {
            $url = $this->_query->_toAtomEndpoint();
        }

        /* BAZ-5745: make sure cache ID doesn't grow too long */
        if (mb_strlen($url) > 2048) {
            error_log("Truncating long query cache ID: $url");
            $url = mb_substr($url, 0, 2016) . md5($url);
        }

        return $url;
    }

   /**
    * Since we can't catch out-of-memory errors, try to estimate the serialized size
    * of the result set and don't cache it if it would be too big
    */

    protected static function estimateSerializedSize($results) {
        $size = 100; // result set size, etc.
        if (! is_array($results)) {
            return $size;
        }
        foreach ($results as $result) {
            $size += self::estimateSerializedContentSize($result);
        }
        return $size;
    }

    protected static function estimateSerializedContentSize($content) {
        $size = 500; // Base XML, namespaces, etc.
        if ($content) {
            $overhead = 10;
            foreach ($content->attribute(null, false) as $attr) {
                $size += 2*mb_strlen($attr[0]->name) + mb_strlen($attr[0]->value) + mb_strlen($attr[0]->type);
                $size += $overhead;
            }
            if ($content->my) {
                $overhead = 23;
                foreach ($content->my->attribute(null, true) as $attrName => $as) {
                    foreach ($as as $attr) {
                        $size += 2*strlen($attr->name) + strlen($attr->value) + mb_strlen($attr->type);
                        $size += $overhead;
                    }
                }
            }
        }
        return $size;
    }

    protected static function setMaxResultSerializedSize($size = null) {
        if (is_null($size)) {
            if (is_null(self::$_maxResultSerializedSize) && mb_strlen($t = trim(ini_get('memory_limit')))) {
                if (isset(self::$_memMultipliers[$c=mb_strtolower(mb_substr($t,-1))])) { $t *= self::$_memMultipliers[$c]; }
                self::$_maxResultSerializedSize = intval($t * self::$_serializedSizeMemoryRatio);
            }
        } else {
            self::$_maxResultSerializedSize = $size;
        }
    }

    protected static function getMaxResultSerializedSize() { return self::$_maxResultSerializedSize; }
    protected static function getSerializedSizeMemoryRatio() { return self::$_serializedSizeMemoryRatio; }


    /**
     * Where to cache query results; configurable via widget config
     * @see BAZ-3577
     * @see BAZ-5747
     */
    protected static $_cacheStorage = null;

    /**
     * The default query caching setting to use if not specified in
     * the config:
     *   "api": use the XN_Cache API
     *   "file": use the filesystem
     *   "none": disable query caching
     */
    protected static $_defaultCacheStorage = 'api';

    /**
     * Where to cache query results (BAZ-3577)
     */
    public static function getCacheStorage() {
        if (is_null(self::$_cacheStorage)) {
            $adminWidget = W_Cache::getWidget('admin');
            if (mb_strlen($adminWidget->config['queryCacheStorage'])) {
                self::$_cacheStorage = $adminWidget->config['queryCacheStorage'];
            }
            else {
                /* Use the default */
                self::$_cacheStorage = self::$_defaultCacheStorage;
            }
        }
        return self::$_cacheStorage;
    }
}
