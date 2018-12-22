<?php

/**
 * A widget app that caches the config XML and the list of filenames in the
 * lib and model directories.

 * @see BAZ-7521
 */
abstract class XG_ConfigCachingApp extends W_WidgetApp {

    /**
     * Whether to enable caching of the config XML and the list of filenames in the lib and model directories.
     * It is sometimes useful to turn this off during development.
     * Note: This is not a kill switch for XN_Cache calls; loadData() will still call XN_Cache::remove().
     */
    protected static $cachingEnabled = TRUE;

    /**
     * The config XML and the list of filenames in the lib and model directories:
     * <pre>
     * array(
     *     'instances' => array(
     *         'photo' => array(
     *             'public-config' => ...xml...,
     *             'private-config' => ...xml...),
     *         'video' => array(
     *             'public-config' => ...xml...,
     *             'private-config' => ...xml...)),
     *     'filenames' => array(
     *         '/apps/networkname/widgets/photo/models' => array('Photo.php', ...),
     *         '/apps/networkname/widgets/photo/lib' => array(),
     *         '/apps/networkname/widgets/video/models' => array('Video.php', ...),
     *         '/apps/networkname/widgets/video/lib' => array()))
     * </pre>
     */
    protected static $data = null;

    /** Key for the cached data. */
    const CACHE_ID = 'app-configuration';

    /**
     * Loads the app configuration from the cache, if it hasn't yet been loaded.
     */
    private static function initializeData() {
        if (is_null(self::$data) && self::$cachingEnabled) { self::loadData(); }
    }

    /**
     * Loads the app configuration from the cache.
     */
    protected static function loadData() {
        if (! self::$cachingEnabled) {
            // Delete the cache entry so that it gets rebuilt when $cachingEnabled is turned back on. [Jon Aquino 2008-05-16]
            return XN_Cache::remove(self::CACHE_ID);
        }
        self::log();
        $serializedData = XN_Cache::get(self::CACHE_ID);
        if (! is_null($serializedData)) {
            self::$data = unserialize($serializedData);
        } else {
            self::$data = self::buildData();
            XN_Cache::insert(self::CACHE_ID, serialize(self::$data));
            // If the cache entry is empty and multiple requests are trying to populate it,
            // the request that uses rebuildData will win over requests that use loadData,
            // because rebuildData uses put, whereas loadData uses insert. [Jon Aquino 2008-05-15]
        }
    }

    /**
     * Forces the cache entry to be rebuilt and re-cached.
     */
    public static function rebuildData() {
        if (! self::$cachingEnabled) { return; }
        self::log();
        self::$data = self::buildData();
        // Use put instead of insert, to force the data into the cache even if it already exists.
        // Better than delete, which may cause changes to be missed because of NFS caching. [Jon Aquino 2008-05-15]
        XN_Cache::put(self::CACHE_ID, serialize(self::$data));
        // If the cache entry is empty and multiple requests are trying to populate it,
        // the request that uses rebuildData will win over requests that use loadData,
        // because rebuildData uses put, whereas loadData uses insert. [Jon Aquino 2008-05-15]
    }

    /**
     * Builds an array containing the config XML and the list of filenames in the lib and model directories.
     *
     * @return array  the data to cache
     */
    private static function buildData() {
        self::log();
        $data = array();
        foreach (glob(NF_APP_BASE . '/instances/*') as $instancePath) {
            $contents = @file_get_contents($instancePath . '/widget-configuration.xml');
            // Make sure that $contents is not FALSE or empty [Jon Aquino 2008-05-27]
            if ($contents) { $data['instances'][basename($instancePath)]['public-config'] = $contents; }
        }
        foreach (glob(NF_APP_BASE . '/xn_private/*-private-configuration.xml') as $privateConfigPath) {
            preg_match('@([^/]*)-private-configuration.xml$@u', $privateConfigPath, $matches);
            $contents = @file_get_contents($privateConfigPath);
            if ($contents) { $data['instances'][$matches[1]]['private-config'] = $contents; ; }
        }
        foreach (glob(self::includePrefix() . '/widgets/*') as $widgetPath) {
            $data['filenames'][$widgetPath . '/models'] = array();
            foreach (glob($widgetPath . '/models/*.php') as $modelPath) {
                $data['filenames'][dirname($modelPath)][] = basename($modelPath);
            }
            $data['filenames'][$widgetPath . '/lib'] = array();
            foreach (glob($widgetPath . '/lib/*.php') as $libPath) {
                $data['filenames'][dirname($libPath)][] = basename($libPath);
            }
        }
        return $data;
    }

    /**
     * Return a list of widget instances to load. Each element
     * in the returned array will be passed to W_Widget::factory()
     *
     * @return array  photo, video, forum, etc.
     */
    public static function getInstances() {
        self::initializeData();
        if (! self::$cachingEnabled) { return parent::getInstances(); }
        self::log();
        return array_keys(self::$data['instances']);
    }

    /**
     * Given a widget instance name, returns identifier that can be
     * passed to W_BaseWidget::factory() to load the widget.
     *
     * @param $dir string  the instance directory, e.g., photo
     * @return $identifier string  the instance identifier
     */
    public static function getInstanceIdentifier($dir) {
        if (! self::$cachingEnabled) { return parent::getInstanceIdentifier($dir); }
        self::log();
        return $dir;
    }

    /**
     * Given an instance identifier from getInstanceIdentifer(), this
     * method returns the full path to the directory that contains the
     * widget code. Depending on how instances are identified and configuration
     * is stored, these two values may be different.
     *
     * @param $identifier string  the instance identifier
     * @return string  the path to the instance directory
     */
    public static function getInstanceDirectory($identifier) {
        if (! self::$cachingEnabled) { return parent::getInstanceDirectory($identifier); }
        self::log();
        return NF_APP_BASE . '/instances/' . $identifier . '/';
    }

    /**
     * Return a SimpleXML object containing the widget instance's
     * public configuration, or false if the instance can't be loaded.
     *
     * @param $widgetIndentifier string Unique identifier of the instance.
     * W_WidgetApp::getInstances() should return an array of these
     * @return SimpleXMLElement|false
     * @throws NF_Exception  if the config could not be loaded
     */
    public static function getWidgetPublicConfig($identifier) {
        self::initializeData();
        if (! self::$cachingEnabled) { return parent::getWidgetPublicConfig($identifier); }
        self::log();
        $xml = self::$data['instances'][$identifier]['public-config'];
        if (! $xml) {
            // The NF_Exception tells the caller "don't bother logging this"
            throw new NF_Exception("Can't load config for $identifier");
        }
        return @simplexml_load_string($xml);
    }

    /**
     * Returns the private configuration for an already-loaded widget
     * instance, or false if it's unavailable.
     *
     * @param $w W_BaseWidget The widget whose private config should be loaded
     * @return SimpleXMLElement|false
     */
    public static function getWidgetPrivateConfig(W_BaseWidget $w) {
        self::initializeData();
        if (! self::$cachingEnabled) { return parent::getWidgetPrivateConfig($w); }
        self::log();
        return @simplexml_load_string(self::$data['instances'][$w->dir]['private-config']);
    }

    /**
     * Save the public configuration for a given widget instance
     *
     * @param $widget W_BaseWidget The widget whose configuration is being saved
     * @param $xml string The xml config to save
     */
    public static function putWidgetPublicConfig(W_BaseWidget $w, $xml) {
        self::initializeData();
        parent::putWidgetPublicConfig($w, $xml);
        if (! self::$cachingEnabled) { return; }
        self::log();
        self::$data['instances'][$w->dir]['public-config'] = $xml;
        self::putWidgetConfig($w->dir);
    }

    /**
     * Save the private configuration for a given widget instance
     *
     * @param $widget W_BaseWidget The widget whose configuration is being saved
     * @param $xml string The xml config to save
     */
    public static function putWidgetPrivateConfig(W_BaseWidget $w, $xml) {
        self::initializeData();
        parent::putWidgetPrivateConfig($w, $xml);
        if (! self::$cachingEnabled) { return; }
        self::log();
        self::$data['instances'][$w->dir]['private-config'] = $xml;
        self::putWidgetConfig($w->dir);
    }

    /**
     * Put the configuration for the given widget instance into the cache.
     *
     * @param $dir string  the instance directory, e.g., photo
     */
    private static function putWidgetConfig($dir) {
        self::initializeData();
        // Minimize write contention on the config data by updating the data for just the one widget (BAZ-7521) [Jon Aquino 2008-05-17]
        $lockName = 'updating-app-configuration';
        self::includeFileOnce('/lib/XG_LockHelper.php');
        XG_LockHelper::lock($lockName); // If this returns false (shouldn't happen), just keep going. Better than throwing an exception. [Jon Aquino 2008-05-17]
        $widgetConfig = self::$data['instances'][$dir];
        self::loadData();
        self::$data['instances'][$dir] = $widgetConfig;
        XN_Cache::put(self::CACHE_ID, serialize(self::$data));
        XG_LockHelper::unlock($lockName);
    }

    /**
     * Discover all the PHP files in a given directory
     *
     * @param $dir Directory to look in
     * @return array List of filenames, including extension, of PHP files
     * in that directory
     */
    public static function findPhpInDirectory($dir) {
        self::initializeData();
        if (! self::$cachingEnabled || ! isset(self::$data['filenames'][$dir])) {
            return parent::findPhpInDirectory($dir);
        }
        self::log();
        return self::$data['filenames'][$dir];
    }

    /**
     * Logs the calling function, for QA. Does nothing if XG_LOG_CONFIG_CACHING is not defined.
     */
    private static function log() {
        if (! defined('XG_LOG_CONFIG_CACHING')) { return; }
        $backtrace = debug_backtrace();
        error_log('XG_LOG_CONFIG_CACHING: ' . $backtrace[1]['function'] . ' called');
    }

}


