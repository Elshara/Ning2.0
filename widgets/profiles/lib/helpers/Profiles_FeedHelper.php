<?php

class Profiles_FeedHelper {
    
    protected static $keyParam = 'key';
    
    /**
     * Return the correct URL for a particular feed
     * including the private-site key if necessary
     *
     * @param $url string Base url for feed
     * @param $widget optional Widget that generates the feed. Defaults to current
     * @return string Modified (if necessary) url for feed
     */
    public static function feedUrl($url, $widget = null) {
        /* Public apps with unrestricted visibility don't need modified feeds */
        $mainWidget = W_Cache::getWidget('main');
        if ((! XG_App::appIsPrivate()) && ($mainWidget->config['nonregVisibility'] == 'everything')) {
            return $url;
        }
        // @todo: look for key param already in URL and replace if so?
        $url .= (mb_strpos($url,'?') === false) ? '?' : '&';
        $url .= rawurlencode(self::$keyParam) . '=' . rawurlencode(self::feedKey($widget));
        return $url;
    }
    
    /**
     * Return the feed key for the site. Only useful for private sites.
     *
     * @param $widget optional widget that's serving up the feed. Defaults
     * to the current widget
     * @return string
     */
     public static function feedKey($widget = null) {
        if (is_null($widget)) {
            $widget = W_Cache::current('W_Widget');
        }
        if (mb_strlen($widget->privateConfig['feedKey']) == 0) {
            $widget->privateConfig['feedKey'] = md5(uniqid());
            $widget->saveConfig();
        }
        return $widget->privateConfig['feedKey'];
     }
     
    /**
     * Determine whether the URL contains a valid key
     *
     * @param $url optional URL to parse; Defaults to checking $_GET. 
     * @param $widget optional widget that's serving up the feed. Defaults
     * to the current widget
     * @return boolean
     */
     public static function validKeyProvided($url = null, $widget = null) {
         // No need for keys in a public app
         if (! XG_App::appIsPrivate()) { return true; }
         if (is_null($url)) {
             $params = $_GET;
         } else {
             $parts = parse_url($url);
             if (isset($parts['query'])) {
                 parse_str($parts['query'], $params);
             } else {
                 $params = array();
             }
         }
         return (isset($params[self::$keyParam]) && ($params[self::$keyParam] == self::feedKey($widget)));
     }
}

