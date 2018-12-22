<?php

/**
 * Useful functions for working with XN_Cache.
 */
class XG_CacheHelper {

        /** Singleton instance of this helper. */
    protected static $instance = NULL;

    /**
     * Returns the singleton instance of this helper.
     *
     * @return XG_CacheHelper  the singleton
     */
    public static function instance() {
        if (is_null(self::$instance)) { self::$instance = new XG_CacheHelper(); }
        return self::$instance;
    }

    /**
     * Retrieves the cache entry with the given ID. If it doesn't exist,
     * or is older than the maxAge, rebuilds it using the buildCallback.
     *
     * @param $id string  ID for the cache entry
     * @param $labels string|array  a label, an array of labels, or null
     * @param $maxAge integer  maximum age for the cache entry in seconds
     * @param $buildCallback callback  public function to build the contents of the cache entry - typically an expensive operation
     * @param $buildCallbackArgs array  arguments to pass to the buildCallback
     * @return string  the contents of the cache entry
     */
    public function get($id, $labels, $maxAge, $buildCallback, $buildCallbackArgs = array()) {
        $payload = $this->getProper($id);
        if (is_null($payload)) {
            $payload = call_user_func_array($buildCallback, $buildCallbackArgs);
            $this->put($id, $labels, $maxAge, $payload);
        }
        return $payload;
    }

    /**
     * Retrieves the cache entry with the given ID. If it doesn't exist,
     * or is older than the maxAge, returns null.
     */
    public function getProper($id) {
        $data = XN_Cache::get($id);
        if ($data) { $data = unserialize($data); }
        if ($data && $data['expires'] < time()) { $data = null; }
        return $data['payload'];
    }

    /**
     * Sets the cache entry with the given ID to the given value.
     *
     * @param $id string  ID for the cache entry
     * @param $labels string|array  a label, an array of labels, or null
     * @param $maxAge integer  maximum age for the cache entry in seconds
     * @param $payload mixed  the contents of the cache entry
     * @see get
     */
    public function put($id, $labels, $maxAge, $payload) {
        $data = array('expires' => time() + $maxAge, 'payload' => $payload);
        XN_Cache::put($id, serialize($data), $labels);
    }

}
