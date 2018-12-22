<?php

/**
 * Contains utility functions for dealing with Google Maps.
 */
class XG_MapHelper {

    /**
     * Outputs the JavaScript element for loading the Google Maps library,
     * and ensures GUnload() is called when the page is unloaded.
     */
    public static function outputScriptTag() { ?>
        <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<%= self::googleMapsApiKey() %>"></script>
		<script>xg.addOnRequire(function(){dojo.addOnUnload(function() { if (GBrowserIsCompatible()) { GUnload(); } });})</script>
    <?php
    }

    public static function googleMapsApiKey() {
        self::generateGoogleMapsApiKeyIfNecessary();
        $domainToKeyMap = unserialize(W_Cache::getWidget('main')->privateConfig['googleMapsApiKeys']);
        return $domainToKeyMap[$_SERVER['HTTP_HOST']];
    }

    private static function generateGoogleMapsApiKey() {
        return file_get_contents('http://snazzyapps' . XN_AtomHelper::$DOMAIN_SUFFIX .
                                 '/getAPIKey.php?type=gmaps&domain=' . urlencode($_SERVER['HTTP_HOST']));
    }

    private static function generateGoogleMapsApiKeyIfNecessary() {
        $mainWidget = W_Cache::getWidget('main');
        if (self::generateGoogleMapsApiKeyIfNecessaryProper($mainWidget->privateConfig)) {
            $mainWidget->saveConfig();
        }
    }

    /*
     * Sets the privateConfig's googleMapsApiKeys value.
     *
     * @param $privateConfig array  The main widget's privateConfig field
     * @param $testKey boolean  Simulated generateGoogleMapsApiKey return value, for testing
     * @return boolean  Whether $privateConfig was modified and needs saving
     */
    public static function generateGoogleMapsApiKeyIfNecessaryProper(&$privateConfig, $testKey = NULL) {
        $domainToKeyMap = unserialize($privateConfig['googleMapsApiKeys']);
        $domainToKeyMap = $domainToKeyMap ? $domainToKeyMap : array();
        $key = $domainToKeyMap[$_SERVER['HTTP_HOST']];
        $minute = 60;
        if (! self::keyValid($key) || (preg_match('@tried (\d+)@u', $key, $matches) && $matches[1] < time() - 60 * $minute)) {
            $testKey = $_GET['test_key'] && XG_SecurityHelper::userIsAdmin() ? $_GET['test_key'] : $testKey;
            $key = $testKey ? $testKey : self::generateGoogleMapsApiKey();
            if (! self::keyValid($key)) { $key = 'tried ' . time(); }
            $domainToKeyMap[$_SERVER['HTTP_HOST']] = $key;
            $privateConfig['googleMapsApiKeys'] = serialize($domainToKeyMap);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Checks that the Google Maps API key does not look bogus,
     * for example, because of an error at Ning, at Google, or on the network.
     *
     * @return whether the Google Maps API key is valid
     */
    private static function keyValid($key) {
        if ($key && mb_strpos($key, 'tried ') === 0) { return TRUE; }
        return $key && mb_strlen($key) > 5 && mb_strpos($key, "\n") === FALSE && mb_strpos($key, '<') === FALSE;
    }

}