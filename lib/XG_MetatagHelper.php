<?php

/**
 * Methods that assist proper production of <meta/> tags for
 * description and keywords
 *
 * @ingroup XG
 */
class XG_MetatagHelper {

    /** The Cache ID for the retrieved app metadata */
    protected static $cacheId = 'app-metadata';

    /** How long the app metadata should be cached (in seconds) */
    protected static $cacheLifetime = 3600;

    /** The app metadata, so it's only loaded once per request */
    protected static $appMetadata = null;

    /**
     * Prepare a version of text suitable for the
     * <meta type="description"/> tag
     *
	 * RETURNED STRING IS ALREADY HTML-QUOTED(!). SINGLE QUOTE IS LEFT AS-IS.
     *
     * @param $text string
     * @return string
     */
    public static function forDescription($text) {
		$text = mb_substr(strip_tags($text), 0, 2000);
		$text = strtr($text, '<>"', '   '); // BAZ-4895 [Andrey 2008-05-13]
        $words = preg_split('/(\s+)|([\.!?]\s+)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
		$stopwords = array_flip(explode('|', str_replace(' ', '', xg_text('STOPWORDS'))));
		foreach($words as $k=>$v) {
			if (isset($stopwords[mb_strtolower($v)])) {
				unset($words[$k]);
			}
		}
		return htmlspecialchars(implode(' ', $words), ENT_COMPAT, 'UTF-8');
    }

    /**
	 *  Prepare a version of text suitable for the <meta type="keywords"/> tag
     *
     *  @param      $name   type    desc
     *  @return     void
     */
    public function forMetatags($text) {
        $text = strip_tags($text);
		$text = strtr($text, '<>"', '   ');
		return xg_excerpt(htmlspecialchars($text, ENT_COMPAT, 'UTF-8'), 4000);
    }


    /**
     * Get the app's description.
     *
     * @return string
     */
    public static function appDescription() {
        return W_Cache::getWidget('main')->config['description'];
    }

    /**
     * Get the app's tagline
     *
     * @return string
     */
    public static function appTagline() {
        return W_Cache::getWidget('main')->config['tagline'];
    }

    /**
     * Get the app's category. Retrieved from an in-app
     * cache until the /xn/atom/1.0/application endpoint contains
     * the appropriate information
     *
     * @return string
     */
    public static function appCategory() {
        self::loadAppMetadata();
        return self::$appMetadata['category'];
    }

    /**
     * Get the app's tags. Retrieved from an in-app
     * cache until the /xn/atom/1.0/application endpoing contains
     * the appropriate information
     *
     * @return string
     */
    public static function appTags() {
        self::loadAppMetadata();
        return self::$appMetadata['tags'];
    }

    /**
     * Flushes the in-app cache of app metadata. Should be called
     * from places such as index/admin/appProfile that potentially
     * change app metadata.
     */
    public static function flushCache() {
        XN_Cache::remove(self::$cacheId);
        self::$appMetadata = null;
    }

    /**
     * Loads the app metadata for the request duration if it's
     * not already loaded
     */
    protected static function loadAppMetadata() {
        if (is_null(self::$appMetadata)) {
            self::$appMetadata = self::appMetadata();
        }
    }

    /**
     * Retrieves the app metadata from the /xn/rest/1.0/application
     * endpoint and caches it locally.
     */
    protected static function appMetadata() {
        $hit = XN_Cache::get(self::$cacheId, self::$cacheLifetime);
        if (! is_null($hit)) {
            $appMetadata = $hit;
        }
        // Retrieve from endpoint, store in cache
        else {
            $appMetadata = array('description' => '', 'tags' => '', 'category' => '');
            try {
                // Retrieve
                $app = XN_Application::load()->relativeUrl;
                $url = 'http://' . XN_AtomHelper::HOST_APP($app) . '/xn/rest/1.0/application:' . rawurlencode($app) . '?xn_out=xml&xn_auth=no';
                $xml = XN_REST::get($url);
                $sxml = @simplexml_load_string($xml);
                if ($sxml) {
                    $description = (string) $sxml->description;
                    if (mb_strlen($description) && ($description != 'null')) {
                        $appMetadata['description'] = $description;
                    }
                    $category = (string) $sxml->category;
                    if (mb_strlen($category)) {
                        $appMetadata['category'] = $category;
                    }
                    $tags = (string) $sxml->tags;
                    if (mb_strlen($tags)) {
                        $appMetadata['tags'] = $tags;
                    }
                }
                // Save in the cache
                XN_Cache::put(self::$cacheId, $appMetadata);
            } catch (Exception $e) {
                error_log("Couldn't retrieve metadata for $app: {$e->getMessage()}");
            }
        }
        return $appMetadata;
    }
}
